<!-- views/medicine/list.php -->
<?php Middleware::requir_Any_role([ROLE_ADMIN, ROLE_PHARMACIST]); ?>

<div class="medicines-container">
    <!-- رأس الصفحة -->
    <div class="page-header">
        <div>
            <h1>قائمة الأدوية</h1>
            <p class="subtitle">إدارة وعرض جميع الأدوية والمستلزمات الطبية</p>
        </div>

        <div class="header-actions">
            <div class="header-date">
                <i class="fas fa-calendar-alt"></i>
                <span><?php echo date('Y-m-d'); ?></span>
            </div>

            <?php if (Auth::hasAnyRole([ROLE_ADMIN, ROLE_PHARMACIST])): ?>
                <a href="<?php echo BASE_URL; ?>medicine/create" class="btn-add">
                    <i class="fas fa-plus"></i> إضافة دواء
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php $goodStockCount = count($medicines) - count($alerts['low_stock']) - count($alerts['critical_stock']); ?>

    <!-- بطاقات الإحصائيات -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-pills"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo count($medicines); ?></h3>
                <p>إجمالي الأدوية</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $goodStockCount ?? 0; ?></h3>
                <p>مخزون جيد</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo count($alerts['low_stock']); ?></h3>
                <p>منخفض المخزون</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-skull-crossbones"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo count($alerts['critical_stock']) ?? 0; ?></h3>
                <p>مخزون حرج</p>
            </div>
        </div>
    </div>

    <!-- شريط البحث -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-search"></i> البحث</h3>
            <div class="card-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchMedicine" placeholder="ابحث باسم الدواء...">
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الأدوية -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> قائمة المخزون الحالية</h3>
            <span class="items-count"><?php echo count($medicines); ?> صنف</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="medicines-table" id="medicinesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الدواء</th>
                            <th>الوصف</th>
                            <th>الكمية الحالية</th>
                            <th>الحد الأدنى</th>
                            <th>الحد الحرج</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                    </thead>
                    <tbody>
                        <?php foreach ($medicines as $index => $medicine): ?>
                            <?php
                            $currentStock = $medicine->current_stock ?? 0;
                            $stockStatus = '';
                            $statusClass = '';
                            if ($currentStock <= $medicine->critical_quantity):
                                $stockStatus = 'حرج';
                                $statusClass = 'critical';
                            elseif ($currentStock <= $medicine->min_quantity):
                                $stockStatus = 'منخفض';
                                $statusClass = 'low';
                            else:
                                $stockStatus = 'جيد';
                                $statusClass = 'good';
                            endif;
                            ?>
                            <tr class="medicine-row <?php echo $statusClass; ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="medicine-name">
                                        <i class="fas fa-capsules"></i>
                                        <?php echo htmlspecialchars($medicine->name); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($medicine->description ?? '-'); ?></td>
                                <td>
                                    <span class="stock-badge <?php echo $statusClass; ?>">
                                        <?php echo $currentStock; ?>
                                    </span>
                                </td>
                                <td><?php echo $medicine->min_quantity; ?></td>
                                <td><?php echo $medicine->critical_quantity; ?></td>
                                <td>
                                    <?php if ($currentStock <= $medicine->critical_quantity): ?>
                                        <span class="badge badge-critical">
                                            <i class="fas fa-skull-crosswalk"></i> حرج
                                        </span>
                                    <?php elseif ($currentStock <= $medicine->min_quantity): ?>
                                        <span class="badge badge-low">
                                            <i class="fas fa-exclamation-triangle"></i> منخفض
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-good">
                                            <i class="fas fa-check-circle"></i> جيد
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if (Auth::hasAnyRole([ROLE_ADMIN, ROLE_PHARMACIST])): ?>
                                            <button onclick="showAddStock(<?php echo $medicine->id; ?>)" class="btn-icon add-stock" title="إضافة مخزون">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
                                            <a href="<?php echo BASE_URL; ?>medicine/edit/<?php echo $medicine->id; ?>" class="btn-icon edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- معلومات إضافية -->
    <div class="info-card">
        <div class="info-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-content">
            <h4>تنبيهات المخزون</h4>
            <p>• المخزون الجيد: الكمية أعلى من الحد الأدنى<br>
                • المخزون المنخفض: الكمية أقل من الحد الأدنى<br>
                • المخزون الحرج: الكمية أقل من الحد الحرج - يجب إعادة التزويد فوراً</p>
        </div>
    </div>
</div>

<!-- Modal for adding stock -->
<div id="addStockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> إضافة مخزون</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="addStockForm" method="POST" action="">
                <?php echo CSRF::field(); ?>
                <div class="form-group">
                    <label for="quantity">
                        <i class="fas fa-boxes"></i>
                        الكمية
                    </label>
                    <input type="number" id="quantity" name="quantity" min="1" required placeholder="أدخل الكمية">
                </div>
                <div class="form-group">
                    <label for="expiry_date">
                        <i class="fas fa-calendar-alt"></i>
                        تاريخ انتهاء الصلاحية
                    </label>
                    <input type="date" id="expiry_date" name="expiry_date" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> إضافة
                    </button>
                    <button type="button" class="btn-cancel-modal" onclick="closeModal()">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // البحث المباشر
    document.getElementById('searchMedicine').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll('#medicinesTable tbody tr');

        rows.forEach(row => {
            let name = row.querySelector('.medicine-name')?.textContent.toLowerCase() || '';
            if (name.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function showAddStock(medicineId) {
        const modal = document.getElementById('addStockModal');
        const form = document.getElementById('addStockForm');
        form.action = '<?php echo BASE_URL; ?>medicine/addStock/' + medicineId;
        modal.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('addStockModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('addStockModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>

<style>
    /* ===== التصميم العام ===== */
    .medicines-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 25px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }

    /* ===== رأس الصفحة ===== */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 800;
        color: #1e2b37;
        margin: 0 0 5px 0;
    }

    .page-header .subtitle {
        color: #6c757d;
        font-size: 14px;
        margin: 0;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .header-date {
        background: white;
        padding: 10px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .header-date i {
        color: #0066cc;
    }

    .btn-add {
        background: #0066cc;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.2);
    }

    .btn-add:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    /* ===== بطاقات الإحصائيات ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon.blue {
        background: #e3f2fd;
        color: #0066cc;
    }

    .stat-icon.green {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .stat-icon.orange {
        background: #fff3e0;
        color: #f57c00;
    }

    .stat-icon.red {
        background: #fee9ed;
        color: #dc3545;
    }

    .stat-icon i {
        font-size: 24px;
    }

    .stat-details h3 {
        font-size: 28px;
        margin: 0;
        color: #1e2b37;
        font-weight: 700;
    }

    .stat-details p {
        margin: 5px 0 0;
        color: #6c757d;
        font-size: 14px;
    }

    /* ===== البطاقة الرئيسية ===== */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .card-header {
        padding: 18px 25px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        background: white;
    }

    .card-header h3 {
        margin: 0;
        color: #1e2b37;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header h3 i {
        color: #0066cc;
    }

    .items-count {
        background: #f8f9fa;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        color: #6c757d;
    }

    .card-body {
        padding: 25px;
    }

    /* ===== صندوق البحث ===== */
    .search-box {
        position: relative;
        min-width: 250px;
    }

    .search-box i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .search-box input {
        width: 100%;
        padding: 8px 35px 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .search-box input:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    /* ===== الجدول ===== */
    .table-responsive {
        overflow-x: auto;
    }

    .medicines-table {
        width: 100%;
        border-collapse: collapse;
    }

    .medicines-table th {
        background: #f8f9fa;
        padding: 15px 12px;
        text-align: right;
        color: #1e2b37;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }

    .medicines-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #e9ecef;
        color: #1e2b37;
        vertical-align: middle;
    }

    .medicines-table tbody tr:hover {
        background: #f8f9fa;
    }

    .medicine-row.critical {
        background: #fee9ed;
    }

    .medicine-row.low {
        background: #fff3e0;
    }

    .medicine-name {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .medicine-name i {
        color: #0066cc;
    }

    .stock-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .stock-badge.good {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .stock-badge.low {
        background: #fff3e0;
        color: #f57c00;
    }

    .stock-badge.critical {
        background: #fee9ed;
        color: #dc3545;
    }

    /* ===== الشارات ===== */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-good {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .badge-low {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-critical {
        background: #fee9ed;
        color: #dc3545;
    }

    /* ===== أزرار الإجراءات ===== */
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-icon {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f8f9fa;
        color: #6c757d;
        text-decoration: none;
        transition: all 0.3s;
        cursor: pointer;
        border: none;
    }

    .btn-icon:hover {
        transform: translateY(-2px);
    }

    .btn-icon.add-stock:hover {
        background: #28a745;
        color: white;
    }

    .btn-icon.edit:hover {
        background: #ffc107;
        color: #1e2b37;
    }

    /* ===== المودال ===== */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .modal-header {
        padding: 20px 25px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #1e2b37;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-header h3 i {
        color: #0066cc;
    }

    .modal-header .close {
        font-size: 28px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s;
    }

    .modal-header .close:hover {
        color: #dc3545;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-body .form-group {
        margin-bottom: 20px;
    }

    .modal-body label {
        display: block;
        margin-bottom: 8px;
        color: #1e2b37;
        font-weight: 500;
        font-size: 14px;
    }

    .modal-body label i {
        color: #0066cc;
        margin-left: 5px;
    }

    .modal-body input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 14px;
    }

    .modal-body input:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .btn-submit {
        background: #0066cc;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        flex: 1;
        justify-content: center;
    }

    .btn-submit:hover {
        background: #0052a3;
        transform: translateY(-2px);
    }

    .btn-cancel-modal {
        background: #f8f9fa;
        color: #6c757d;
        padding: 10px 20px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        flex: 1;
        justify-content: center;
    }

    .btn-cancel-modal:hover {
        background: #e9ecef;
    }

    /* ===== بطاقة معلومات ===== */
    .info-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdef5 100%);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-icon i {
        font-size: 24px;
        color: #0066cc;
    }

    .info-content h4 {
        margin: 0 0 5px 0;
        color: #1e2b37;
        font-size: 16px;
    }

    .info-content p {
        margin: 0;
        color: #0066cc;
        font-size: 14px;
        line-height: 1.6;
    }

    /* ===== تصميم متجاوب ===== */
    @media (max-width: 768px) {
        .medicines-container {
            padding: 15px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .header-date,
        .btn-add {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-box {
            width: 100%;
        }

        .action-buttons {
            justify-content: center;
        }

        .info-card {
            flex-direction: column;
            text-align: center;
        }

        .medicines-table {
            min-width: 600px;
        }
    }
</style>