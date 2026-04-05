<?php
// controllers/PrescriptionController.php

class PrescriptionController extends Controller
{
    private $prescriptionModel;
    private $medicineModel;
    private $patientModel;

    public function __construct()
    {
        $this->prescriptionModel = new Prescription();
        $this->medicineModel = new Medicine();
        $this->patientModel = new Patient();
    }

    public function middleware()
    {
        return [
            'list' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_PHARMACIST],
            'create' => [ROLE_DOCTOR],
            'store' => [ROLE_DOCTOR],
            'view' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_PHARMACIST],
            'print' => [ROLE_ADMIN, ROLE_DOCTOR, ROLE_PHARMACIST]
        ];
    }

    public function list()
    {
        $prescriptions = $this->prescriptionModel->getAll();
        $this->view('prescriptions.list', ['prescriptions' => $prescriptions]);
    }

    public function create()
    {
        $patients = $this->patientModel->all();
        $medicines = $this->medicineModel->getAllWithStock();
        $this->view('prescriptions.create', [
            'patients' => $patients,
            'medicines' => $medicines
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('prescriptions/list');
        }

        $patientId = $_POST['patient_id'] ?? 0;
        $items = $_POST['items'] ?? [];

        if (empty($patientId) || empty($items)) {
            Session::flash('error', 'يجب اختيار مريض وإضافة أدوية');
            $this->redirect('prescriptions/create');
        }

        // إنشاء وصفة جديدة
        $prescriptionData = [
            'patient_id' => $patientId,
            'doctor_id' => Auth::id(),
            'status' => STATUS_PENDING,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $prescriptionId = $this->prescriptionModel->create($prescriptionData);
        if (!$prescriptionId) {
            Session::flash('error', 'حدث خطأ أثناء إنشاء الوصفة');
            $this->redirect('prescriptions/create');
        }

        // إضافة العناصر
        foreach ($items as $item) {
            $this->prescriptionModel->addItem([
                'prescription_id' => $prescriptionId,
                'medicine_id' => $item['medicine_id'],
                'dosage' => $item['dosage'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'instructions' => $item['instructions'] ?? null
            ]);
        }

        Session::flash('success', 'تم إنشاء الوصفة بنجاح');
        $this->redirect('prescription/views/' . $prescriptionId);
    }

    public function views($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;

        $prescription = $this->prescriptionModel->getWithDetails($id);
        if (!$prescription) {
            $this->redirect('prescriptions/list');
        }
        $this->view('prescriptions.view', ['prescription' => $prescription]);
    }

    public function print($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            session::flash('error', 'معرف غير صالح');
            $this->redirect('patient/list');
        }

        $id = (int)$id;

        $prescription = $this->prescriptionModel->getWithDetails($id);
        if (!$prescription) {
            $this->redirect('prescriptions/list');
        }
        $this->view('prescriptions.print', ['prescription' => $prescription]);
    }
}
