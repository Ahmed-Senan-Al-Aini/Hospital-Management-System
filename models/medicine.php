<?php


class Medicine extends Model
{
    protected $table = 'medicines';

    /**
     * جلب جميع الأدوية مع الكمية الحالية
     */
    public function getAllWithStock()
    {
        $stmt = $this->pdo->query("SELECT 
    m.*,
    COALESCE(SUM(CASE WHEN it.type = 'add' THEN it.quantity ELSE 0 END), 0) -
    COALESCE(SUM(CASE WHEN it.type = 'remove' THEN it.quantity ELSE 0 END), 0) as current_stock
FROM medicines m
LEFT JOIN inventory_transactions it ON m.id = it.medicine_id
GROUP BY m.id
        ");
        //  SELECT m.*,
        //                    COALESCE(SUM(CASE WHEN it.type = 'add' THEN it.quantity ELSE 0 END), 0) -
        //                    COALESCE(SUM(CASE WHEN it.type = 'remove' THEN it.quantity ELSE 0 END), 0) as current_stock
        //             FROM medicines m
        //             LEFT JOIN inventory_transactions it ON m.id = it.medicine_id
        //             GROUP BY m.id
        //             ORDER BY m.name

        return $stmt->fetchAll();
    }

    /**
     * حساب الكمية الحالية لدواء
     */
    public function getCurrentStock($medicineId)
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(CASE WHEN type = 'add' THEN quantity ELSE 0 END), 0) -
                   COALESCE(SUM(CASE WHEN type = 'remove' THEN quantity ELSE 0 END), 0) as current_stock
            FROM inventory_transactions
            WHERE medicine_id = :medicine_id
        ");
        $stmt->execute(['medicine_id' => $medicineId]);
        $result = $stmt->fetch();
        return $result->current_stock ?? 0;
    }

    /**
     * منخفضة المخزون
     */
    public function getLowStock()
    {
        $stmt = $this->pdo->query("
            SELECT m.*,
                   COALESCE(SUM(CASE WHEN it.type = 'add' THEN it.quantity ELSE 0 END), 0) -
                   COALESCE(SUM(CASE WHEN it.type = 'remove' THEN it.quantity ELSE 0 END), 0) as current_stock
            FROM medicines m
            LEFT JOIN inventory_transactions it ON m.id = it.medicine_id
            GROUP BY m.id
            HAVING current_stock <= m.min_quantity
            ORDER BY current_stock ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * الأدوية في المخزون الحرج
     */
    public function getCriticalStock()
    {
        $stmt = $this->pdo->query("
            SELECT m.*,
                   COALESCE(SUM(CASE WHEN it.type = 'add' THEN it.quantity ELSE 0 END), 0) -
                   COALESCE(SUM(CASE WHEN it.type = 'remove' THEN it.quantity ELSE 0 END), 0) as current_stock
            FROM medicines m
            LEFT JOIN inventory_transactions it ON m.id = it.medicine_id
            GROUP BY m.id
            HAVING current_stock <= m.critical_quantity
            ORDER BY current_stock ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * الأدوية قريبة الانتهاء
     */
    public function getNearExpiry($days = 30)
    {
        $stmt = $this->pdo->prepare("
            SELECT m.name, it.expiry_date, it.quantity
            FROM inventory_transactions it
            JOIN medicines m ON it.medicine_id = m.id
            WHERE it.expiry_date IS NOT NULL
              AND it.expiry_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL :days DAY)
              AND it.quantity > 0
            ORDER BY it.expiry_date ASC
        ");
        $stmt->execute(['days' => $days]);
        return $stmt->fetchAll();
    }

    /**
     * إضافة مخزون (استلام)
     */
    public function addStock($medicineId, $quantity, $expiryDate, $userId)
    {
        // error_log("stock-dd");
        $stmt = $this->pdo->prepare("
            INSERT INTO inventory_transactions 
            (medicine_id, user_id, type, quantity, expiry_date, created_at) 
            VALUES (:medicine_id, :user_id, 'add', :quantity, :expiry_date, NOW())
        ");

        return $stmt->execute([
            'medicine_id' => $medicineId,
            'user_id' => $userId,
            'quantity' => $quantity,
            'expiry_date' => $expiryDate
        ]);
    }

    /**
     * خصم مخزون (صرف)
     */
    public function deductStock($medicineId, $quantity, $referenceId, $referenceType, $userId)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO inventory_transactions 
            (medicine_id, user_id, type, quantity, reference_id, reference_type, created_at) 
            VALUES (:medicine_id, :user_id, 'remove', :quantity, :reference_id, :reference_type, NOW())
        ");

        return $stmt->execute([
            'medicine_id' => $medicineId,
            'user_id' => $userId,
            'quantity' => $quantity,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType
        ]);
    }

    /**
     * البحث عن الأدوية
     */
    public function search($keyword)
    {
        $keyword = "%$keyword%";
        $stmt = $this->pdo->prepare("
            SELECT m.*,
                   COALESCE(SUM(CASE WHEN it.type = 'add' THEN it.quantity ELSE 0 END), 0) -
                   COALESCE(SUM(CASE WHEN it.type = 'remove' THEN it.quantity ELSE 0 END), 0) as current_stock
            FROM medicines m
            LEFT JOIN inventory_transactions it ON m.id = it.medicine_id
            WHERE m.name LIKE :keyword OR m.description LIKE :keyword
            GROUP BY m.id
            LIMIT 20
        ");
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll();
    }
}
