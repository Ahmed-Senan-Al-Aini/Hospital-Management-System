<?php

class MedicineController extends Controller
{
    private $medicineModel;

    public function __construct()
    {
        $this->medicineModel = new Medicine();
    }

    public function middleware()
    {
        return [
            'list' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_PHARMACIST],
            'create' => [ROLE_ADMIN, ROLE_PHARMACIST],
            'store' => [ROLE_ADMIN, ROLE_PHARMACIST],
            'addStock' => [ROLE_ADMIN, ROLE_PHARMACIST],
            'alerts' => [ROLE_ADMIN, ROLE_PHARMACIST],
            'edit' => [ROLE_ADMIN, ROLE_PHARMACIST],
            'update' => [ROLE_ADMIN, ROLE_PHARMACIST]
        ];
    }

    public function list()
    {
        $medicines = $this->medicineModel->getAllWithStock();
        $alerts = [
            'low_stock' => $this->medicineModel->getLowStock(),
            'critical_stock' => $this->medicineModel->getCriticalStock(),
        ];

        $this->view('medicines.list', ['medicines' => $medicines, 'alerts' => $alerts]);
    }

    public function create()
    {
        $alerts = [
            'low_stock' => $this->medicineModel->getLowStock(),
            'critical_stock' => $this->medicineModel->getCriticalStock()
        ];
        $this->view('medicines.create', $alerts);
    }

    public function store()
    {
        error_log("stock");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('medicine/list');
        }

        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            //'quantity' => $_POST['quantity'] ?? null,
            'min_quantity' => $_POST['min_quantity'] ?? 10,
            'critical_quantity' => $_POST['critical_quantity'] ?? 5,
            'organization_id' => $_SESSION['organization_id']
        ];

        if ($this->medicineModel->create($data)) {
            Session::flash('success', 'تم إضافة الدواء بنجاح');
        } else {
            Session::flash('error', 'حدث خطأ أثناء الإضافة');
        }

        $this->redirect('medicine/list');
    }

    public function addStock($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;

        // error_log("stock -ddd");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('medicine/list');
        }


        $medicine = $this->medicineModel->find($id);

        if (!$medicine) {
            Session::flash('error', 'الدواء غير موجود');
            $this->redirect('medicine/list');
            return;
        }


        $quantity = $_POST['quantity'] ?? 0;
        $expiryDate = $_POST['expiry_date'] ?? null;

        if ($quantity <= 0) {
            Session::flash('error', 'الكمية يجب أن تكون أكبر من صفر');
            $this->redirect('medicine/list');
        }

        if ($this->medicineModel->addStock($id, $quantity, $expiryDate, Auth::id())) {
            Session::flash('success', 'تم إضافة المخزون بنجاح');
        } else {
            Session::flash('error', 'حدث خطأ أثناء إضافة المخزون');
        }

        $this->redirect('medicine/list');
    }

    public function alerts()
    {
        $alerts = [
            'sum' => $this->medicineModel->getAllWithStock(),
            'low_stock' => $this->medicineModel->getLowStock(),
            'critical_stock' => $this->medicineModel->getCriticalStock(),
            'near_expiry' => $this->medicineModel->getNearExpiry()
        ];
        $this->view('medicines.alerts', ['alerts' => $alerts]);
    }

    public function edit($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;


        $medicine = $this->medicineModel->find($id);

        if (!$medicine) {
            Session::flash('error', 'الدواء غير موجود');
            $this->redirect('medicine/list');
            return;
        }


        $medicine->current_stock = $this->medicineModel->getCurrentStock($id);

        if (!$medicine) {
            $this->redirect('medicines/list');
        }
        $this->view('medicines.edit', ['medicine' => $medicine]);
    }

    public function update($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('medicines/list');
        }


        $medicine = $this->medicineModel->find($id);

        if (!$medicine) {
            Session::flash('error', 'الدواء غير موجود');
            $this->redirect('medicine/list');
            return;
        }

        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'min_quantity' => $_POST['min_quantity'],
            'critical_quantity' => $_POST['critical_quantity']
        ];

        if ($this->medicineModel->update($id, $data)) {
            Session::flash('success', 'تم تحديث الدواء');
        } else {
            Session::flash('error', 'حدث خطأ أثناء التحديث');
        }

        $this->redirect('medicine/list');
    }
}
