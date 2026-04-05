<?php
// controllers/PatientController.php

class PatientController extends Controller
{
    private $patientModel;

    public function __construct()
    {
        $this->patientModel = new Patient();
    }

    /**
     * جلب جميع المرضى من قاعدة البيانات
     */
    public function all()
    {
        return $this->patientModel->all();
    }

    /**
     * جلب إحصائيات حقيقية عن المرضى
     */
    private function getPatientStats()
    {
        $pdo = Database::getInstance();

        // إجمالي عدد المرضى
        $total = $pdo->query("SELECT COUNT(*) as count FROM patients")->fetch()->count;

        // عدد الذكور
        $males = $pdo->query("SELECT COUNT(*) as count FROM patients WHERE gender = 'male'")->fetch()->count;

        // عدد الإناث
        $females = $pdo->query("SELECT COUNT(*) as count FROM patients WHERE gender = 'female'")->fetch()->count;

        // عدد المرضى الجدد اليوم
        $today = $pdo->query("
            SELECT COUNT(*) as count 
            FROM patients 
            WHERE DATE(created_at) = CURDATE()
        ")->fetch()->count;

        // عدد المرضى الأطفال (أقل من 18 سنة)
        $children = $pdo->query("
            SELECT COUNT(*) as count 
            FROM patients 
            WHERE birth_date IS NOT NULL 
            AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18
        ")->fetch()->count;

        // عدد كبار السن (أكثر من 60 سنة)
        $elderly = $pdo->query("
            SELECT COUNT(*) as count 
            FROM patients 
            WHERE birth_date IS NOT NULL 
            AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 60
        ")->fetch()->count;

        // عدد المرضى المكررين (الذين لديهم أكثر من زيارة)
        $repeatStmt = $pdo->query("
            SELECT patient_id, COUNT(*) as visit_count
            FROM visits
            GROUP BY patient_id
            HAVING visit_count > 1
        ");
        $repeat = $repeatStmt->rowCount();

        return [
            'total' => $total,
            'males' => $males,
            'females' => $females,
            'today' => $today,
            'children' => $children,
            'elderly' => $elderly,
            'repeat' => $repeat
        ];
    }

    /**
     * جلب توزيع الأعمار
     */
    private function getAgeDistribution()
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query("
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 17 THEN 'طفل'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30 THEN 'شاب'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 50 THEN 'بالغ'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 70 THEN 'كهل'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 70 THEN 'مسن'
                    ELSE 'غير محدد'
                END as age_group,
                COUNT(*) as count
            FROM patients
            WHERE birth_date IS NOT NULL
            GROUP BY age_group
        ");

        $distribution = [];
        while ($row = $stmt->fetch()) {
            $distribution[$row->age_group] = $row->count;
        }

        return $distribution;
    }

    public function middleware()
    {
        return [
            'list' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_SECRETARY],
            'create' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_SECRETARY],
            'store' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_SECRETARY],
            'view' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_SECRETARY],   // ✅ تغيير من views إلى view
            'edit' => [ROLE_ADMIN, ROLE_DOCTOR],
            'update' => [ROLE_ADMIN, ROLE_DOCTOR]
        ];
    }

    /**
     * عرض قائمة المرضى
     */
    public function list()
    {
        // جلب قائمة المرضى
        $patients = $this->all();

        // جلب الإحصائيات الحقيقية
        $stats = $this->getPatientStats();
        $ageDistribution = $this->getAgeDistribution();

        $this->view('patients.list', [
            'patients' => $patients,
            'stats' => $stats,
            'ageDistribution' => $ageDistribution
        ]);
    }

    /**
     * عرض صفحة إضافة مريض
     */
    public function create()
    {
        $this->view('patients.create');
    }

    /**
     * حفظ مريض جديد
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('patient/list');
        }

        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'رمز الأمان غير صالح');
            $this->redirect('patient/create');
            return;
        }

        // التحقق من البيانات
        $validation = new Validation($_POST);
        $validation->required('name')->required('phone');

        if (!$validation->passes()) {
            Session::flash('error', $validation->errors()[0][0] ?? 'تحقق من المدخلات');
            $this->redirect('patient/create');
        }

        $data = [
            'national_id' => $_POST['national_id'] ?? null,
            'name' => $_POST['name'],
            'birth_date' => $_POST['birth_date'] ?? null,
            'phone' => $_POST['phone'],
            'gender' => $_POST['gender'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'blood_type' => $_POST['blood_type'] ?? null,
            'address' => $_POST['address'] ?? null
        ];


        if (empty($data['name'])) {
            Session::flash('error', 'الاسم مطلوب');
            $this->redirect('patient/create');
            return;
        }

        if (empty($data['phone'])) {
            Session::flash('error', 'رقم الهاتف مطلوب');
            $this->redirect('patient/create');
            return;
        }



        if ($this->patientModel->create($data)) {
            Session::flash('success', 'تم إضافة المريض بنجاح');
        } else {
            Session::flash('error', 'حدث خطأ أثناء الإضافة');
        }

        $this->redirect('patient/list');
    }

    /**
     * عرض صفحة مريض - ✅ تم التصحيح (كانت views والآن view)
     */

    public function views($id)  // ✅ تغيير اسم الدالة من views إلى view
    {
        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }
        
        $id = (int)$id;
        
        $patient = $this->patientModel->getWithVisits($id);

        if (!$patient) {
            Session::flash('error', 'المريض غير موجود');
            $this->redirect('patient/list');
            return;
        }
        

        // جلب الزيارات بشكل إضافي إذا لم تكن موجودة في getWithVisits
        if (!isset($patient->visits)) {
            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("
                    SELECT v.*, u.name as doctor_name
                    FROM visits v
                    LEFT JOIN users u ON v.doctor_id = u.id
                    WHERE v.patient_id = ?
                    ORDER BY v.visit_date DESC
                ");
                $stmt->execute([$id]);
                $patient->visits = $stmt->fetchAll();
            } catch (Exception $e) {
                error_log("Error fetching visits: " . $e->getMessage());
                $patient->visits = [];
            }
        }

        // جلب الوصفات السابقة للمريض
        try {
            $prescriptionModel = new Prescription();
            $patient->prescriptions = $prescriptionModel->getByPatient($id, 5);
        } catch (Exception $e) {
            error_log("Error fetching prescriptions: " . $e->getMessage());
            $patient->prescriptions = [];
        }

        $this->view('patients.view', ['patient' => $patient]);
    }

    /**
     * عرض صفحة تعديل مريض
     */
    public function edit($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;

        $patient = $this->patientModel->find($id);
        if (!$patient) {
            Session::flash('error', 'المريض غير موجود');
            $this->redirect('patient/list');
        }
        $this->view('patients.edit', ['patient' => $patient]);
    }

    /**
     * تحديث بيانات مريض
     */
    public function update($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('patient/list');
        }

        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'رمز الأمان غير صالح');
            $this->redirect('patient/edit/' . $id);
            return;
        }

        error_log("Updating patient ID: " . $id);

        $data = [
            'national_id' => $_POST['national_id'] ?? null,
            'name' => $_POST['name'],
            'birth_date' => $_POST['birth_date'] ?? null,
            'phone' => $_POST['phone'],
            'gender' => $_POST['gender'] ?? null,
            'blood_type' => $_POST['blood_type'] ?? null,
            'address' => $_POST['address'] ?? null
        ];

        if (empty($data['name'])) {
            Session::flash('error', 'الاسم مطلوب');
            $this->redirect('patient/edit/' . $id);
            return;
        }

        if (empty($data['phone'])) {
            Session::flash('error', 'رقم الهاتف مطلوب');
            $this->redirect('patient/edit/' . $id);
            return;
        }

        if ($this->patientModel->update($id, $data)) {
            Session::flash('success', 'تم تحديث بيانات المريض');
        } else {
            Session::flash('error', 'حدث خطأ أثناء التحديث');
        }

        // ✅ تصحيح الرابط من patient/views إلى patient/view
        $this->redirect('patient/views/' . $id);
    }
}
