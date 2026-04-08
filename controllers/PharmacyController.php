<?php
// controllers/PharmacyController.php

class PharmacyController extends Controller
{
    private $prescriptionModel;
    private $medicineModel;

    public function __construct()
    {
        $this->prescriptionModel = new Prescription();
        $this->medicineModel = new Medicine();
    }

    public function middleware()
    {
        return [
            'queue' => [ROLE_PHARMACIST, ROLE_ADMIN],
            'dispense' => [ROLE_PHARMACIST, ROLE_ADMIN],
            'inventory' => [ROLE_PHARMACIST, ROLE_ADMIN]
        ];
    }

    public function queue()
    {
        $pending = $this->prescriptionModel->getPending();
        $data = [
            'prescriptions' => $pending,
            'todayPrescriptions' => $this->prescriptionModel->getTodayCount(),
            'pendingPrescriptions' => $this->prescriptionModel->getPendingCount()
        ];

        // ✅ تصحيح المسار: يجب أن يكون 'pharmacy.queue' وليس 'pharmacys.queue'
        $this->view('pharmacys.queue', $data);
    }

    public function dispense($id)
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

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $csrfToken = $headers['X-CSRF-TOKEN'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        error_log(" disp -in ");
        if (!CSRF::validate($csrfToken)) {
            $this->json(['error' => 'رمز الأمان غير صالح'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'طريقة طلب غير صحيحة'], 405);
            return;
        }


        $prescription = $this->prescriptionModel->find($id);
        if (!$prescription) {
            $this->json(['error' => 'الوصفة غير موجودة'], 404);
            return;
        }


        if ($prescription->status !== STATUS_PENDING) {
            $this->json(['error' => 'الوصفة تم صرفها مسبقاً'], 400);
            return;
        }

        // ✅ التحقق من توفر الكميات
        $items = $this->prescriptionModel->getItems($id);

        if (empty($items)) {
            $this->json(['error' => 'الوصفة لا تحتوي على أدوية'], 400);
            return;
        }

        $errors = [];

        foreach ($items as $item) {
            $currentStock = $this->medicineModel->getCurrentStock($item->medicine_id);
            if ($currentStock < $item->quantity) {
                $medicine = $this->medicineModel->find($item->medicine_id);
                $medicineName = $medicine ? $medicine->name : 'غير معروف';
                $errors[] = "الدواء {$medicineName} غير متوفر بالكمية المطلوبة (المطلوب: {$item->quantity}, المتوفر: $currentStock)";
            }
        }
    

        if (!empty($errors)) {
            $this->json(['error' => implode('\n', $errors)], 400);
            return;
        }

        // ✅ بدء المعاملة (Transaction)
        $pdo = Database::getInstance();
        try {
            $pdo->beginTransaction();

            // ✅ خصم المخزون لكل دواء
            foreach ($items as $item) {
                $deducted = $this->medicineModel->deductStock(
                    $item->medicine_id,
                    $item->quantity,
                    $id,
                    'prescription',
                    Auth::id()
                );

                if (!$deducted) {
                    throw new Exception("فشل خصم المخزون للدواء ID: {$item->medicine_id}");
                }
            }
            

            // ✅ تحديث حالة الوصفة
            $updated = $this->prescriptionModel->update($id, [
                'status' => STATUS_DISPENSED,
                'dispensed_by' => Auth::id(),
                'dispensed_at' => date('Y-m-d H:i:s')
            ]);

            if (!$updated) {
                throw new Exception("فشل تحديث حالة الوصفة");
            }

            $pdo->commit();

            // ✅ إرجاع استجابة نجاح
            $this->json([
                'success' => true,
                'message' => 'تم صرف الوصفة بنجاح'
            ]);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error dispensing prescription: " . $e->getMessage());
            $this->json(['error' => 'حدث خطأ أثناء الصرف: ' . $e->getMessage()], 500);
        }
    }
}
