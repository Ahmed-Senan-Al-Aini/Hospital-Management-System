<!-- views/reports/inventory.php -->
<div class="report-page-container">
    <!-- رأس الصفحة -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-boxes"></i> تقرير المخزون</h1>
            <p class="subtitle">عرض حالة المخزون الحالية لجميع الأدوية مع تنبيهات النقص والانتهاء</p>
        </div>

        <div class="header-actions">
            <div class="header-date">
                <i class="fas fa-calendar-alt"></i>
                <span><?php echo date('Y-m-d'); ?></span>
            </div>
            <a href="<?php echo BASE_URL; ?>report" class="btn-back">
                <i class="fas fa-arrow-right"></i> عودة
            </a>
        </div>
    </div>

    <!-- بطاقات ملخص المخزون -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-details">
                <h3><?php echo $goodStockCount ?? 0; ?></h3>
                <p>مخزون جيد</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-details">
                <h3><?php echo $lowStockCount ?? 0; ?></h3>
                <p>منخفض المخزون</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-skull-crossbones"></i></div>
            <div class="stat-details">
                <h3><?php echo $criticalStockCount ?? 0; ?></h3>
                <p>مخزون حرج</p>
            </div>
        </div>
    </div>

    <!-- جدول المخزون -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> قائمة المخزون الحالي</h3>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInventory" placeholder="بحث عن دواء...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>الدواء</th>
                            <th>الكمية الحالية</th>
                            <th>الحد الأدنى</th>
                            <th>الحد الحرج</th>
                            <th>الحالة</th>
                            <th>الإجراء المطلوب</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicines as $medicine): ?>
                            <?php
                            $currentStock = $medicine->current_stock ?? 0;
                            if ($currentStock <= $medicine->critical_quantity):
                                $status = 'critical';
                                $action = 'طلب عاجل';
                                $actionClass = 'urgent';
                            elseif ($currentStock <= $medicine->min_quantity):
                                $status = 'low';
                                $action = 'إعادة تزويد';
                                $actionClass = 'warning';
                            else:
                                $status = 'good';
                                $action = 'مستقر';
                                $actionClass = 'stable';
                            endif;
                            ?>
                            <tr class="stock-row <?php echo $status; ?>">
                                <td>
                                    <div class="medicine-name">
                                        <i class="fas fa-capsules"></i>
                                        <?php echo htmlspecialchars($medicine->name); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="stock-badge <?php echo $status; ?>">
                                        <?php echo $currentStock; ?>
                                    </span>
                                </td>
                                <td><?php echo $medicine->min_quantity; ?></td>
                                <td><?php echo $medicine->critical_quantity; ?></td>
                                <td>
                                    <?php if ($status == 'critical'): ?>
                                        <span class="status-badge critical"><i class="fas fa-skull-crossbones"></i> حرج</span>
                                    <?php elseif ($status == 'low'): ?>
                                        <span class="status-badge low"><i class="fas fa-exclamation-triangle"></i> منخفض</span>
                                    <?php else: ?>
                                        <span class="status-badge good"><i class="fas fa-check-circle"></i> جيد</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="action-badge <?php echo $actionClass; ?>">
                                        <?php echo $action; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInventory').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll('.inventory-table tbody tr');

        rows.forEach(row => {
            let name = row.querySelector('.medicine-name')?.textContent.toLowerCase() || '';
            if (name.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>