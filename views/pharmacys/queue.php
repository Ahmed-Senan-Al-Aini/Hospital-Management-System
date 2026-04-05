<!-- views/pharmacy/queue.php -->
<div class="pharmacy-container">
    <!-- رأس الصفحة -->
    <div class="page-header">
        <div class="header-content">
            <h1>طلبات الصرف</h1>
            <p class="subtitle">إدارة ومتابعة عمليات الصرف والتسليم</p>
        </div>
        <div class="header-actions">
            <button class="btn-refresh" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="stats-wrapper">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $pendingPrescriptions ?? 0; ?></span>
                <span class="stat-label">طلبات الانتظار</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $stats['high_priority'] ?? 0; ?></span>
                <span class="stat-label">أولوية عالية</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $todayPrescriptions ?? 0; ?></span>
                <span class="stat-label">صرف اليوم</span>
            </div>
        </div>
    </div>

    <!-- فلاتر البحث -->
    <div class="filters-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="بحث عن مريض أو طبيب...">
        </div>
        <div class="filter-tabs">
            <button class="filter-btn active" data-filter="all">الكل</button>
            <button class="filter-btn" data-filter="high">أولوية عالية</button>
            <button class="filter-btn" data-filter="normal">عادية</button>
        </div>
    </div>

    <!-- قائمة طلبات الصرف -->
    <div class="requests-grid">
        <?php if (empty($prescriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x"></i>
                <p>لا توجد طلبات صرف حالياً</p>
            </div>
        <?php else: ?>
            <?php foreach ($prescriptions as $index => $p): ?>
                <div class="request-card <?php echo isset($p->priority) && $p->priority == 'high' ? 'high-priority' : ''; ?>"
                    id="request-<?php echo $p->id; ?>">
                    <div class="card-header">
                        <div class="request-badge">
                            <span class="badge-id">#<?php echo str_pad($p->id, 4, '0', STR_PAD_LEFT); ?></span>
                            <?php if (isset($p->priority) && $p->priority == 'high'): ?>
                                <span class="badge-priority">أولوية عالية</span>
                            <?php endif; ?>
                        </div>
                        <span class="request-time">
                            <i class="far fa-clock"></i> <?php echo time_elapsed_string($p->created_at); ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="patient-info">
                            <i class="fas fa-user-circle fa-2x"></i>
                            <div class="info-details">
                                <h4><?php echo htmlspecialchars($p->patient_name); ?></h4>
                                <p>د. <?php echo htmlspecialchars($p->doctor_name); ?></p>
                            </div>
                        </div>

                        <div class="request-details">
                            <div class="detail-item">
                                <span class="detail-label">القسم</span>
                                <span class="detail-value">الطوارئ</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">التصنيف</span>
                                <span class="detail-value">عام</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">الكمية</span>
                                <span class="detail-value"><?php echo $p->items_count ?? 1; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="action-buttons">
                            <a href="<?php echo BASE_URL; ?>prescription/views/<?php echo $p->id; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <button onclick="dispensePrescription(<?php echo $p->id; ?>)" class="btn-dispense">
                                <i class="fas fa-check"></i> صرف
                            </button>
                        </div>
                        <div class="quick-actions">
                            <button class="quick-btn" title="تعديل الإجراءات" onclick="quickAction('edit_actions', <?php echo $p->id; ?>)">
                                <i class="fas fa-tasks"></i>
                            </button>
                            <button class="quick-btn" title="تعديل الإنجاز" onclick="quickAction('edit_achievement', <?php echo $p->id; ?>)">
                                <i class="fas fa-check-double"></i>
                            </button>
                            <button class="quick-btn" title="تعديل الإنتاج" onclick="quickAction('edit_production', <?php echo $p->id; ?>)">
                                <i class="fas fa-chart-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- أسئلة الرقابة -->
    <div class="control-section">
        <h3><i class="fas fa-sliders-h"></i> أسئلة الرقابة</h3>
        <div class="control-grid">
            <a href="<?php echo BASE_URL; ?>control/updateEditor" class="control-item">
                <i class="fas fa-sync-alt"></i> تحديث المحرر
            </a>
            <a href="<?php echo BASE_URL; ?>control/editEditor" class="control-item">
                <i class="fas fa-edit"></i> تعديل المحرر
            </a>
            <a href="<?php echo BASE_URL; ?>control/changeActivity" class="control-item">
                <i class="fas fa-toggle-on"></i> تغيير النشاط
            </a>
            <a href="<?php echo BASE_URL; ?>control/editSignature" class="control-item">
                <i class="fas fa-signature"></i> تعديل التوقيع
            </a>
            <a href="<?php echo BASE_URL; ?>control/editActions" class="control-item">
                <i class="fas fa-tasks"></i> تعديل الإجراءات
            </a>
            <a href="<?php echo BASE_URL; ?>control/editAchievement" class="control-item">
                <i class="fas fa-check-double"></i> تعديل الإنجاز
            </a>
            <a href="<?php echo BASE_URL; ?>control/editProduction" class="control-item">
                <i class="fas fa-chart-line"></i> تعديل الإنتاج
            </a>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // تحديث دالة الصرف
    function dispensePrescription(id) {
        if (confirm('هل أنت متأكد من صرف هذه الوصفة؟')) {
            showLoading();

            fetch('<?php echo BASE_URL; ?>pharmacy/dispense/' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?php echo csrf_token() ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showNotification('تم الصرف بنجاح', 'success');
                        document.getElementById('request-' + id).remove();
                    } else {
                        showNotification('خطأ: ' + data.error, 'error');
                    }
                });
        }
    }

    // دوال التحكم السريع
    function quickAction(action, id) {
        showLoading();

        fetch('<?php echo BASE_URL; ?>control/api', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification('حدث خطأ', 'error');
                }
            });
    }

    // فلترة الطلبات
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            const cards = document.querySelectorAll('.request-card');

            cards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else if (filter === 'high') {
                    card.style.display = card.classList.contains('high-priority') ? 'block' : 'none';
                } else if (filter === 'normal') {
                    card.style.display = !card.classList.contains('high-priority') ? 'block' : 'none';
                }
            });
        });
    });

    // البحث المباشر
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const cards = document.querySelectorAll('.request-card');

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? 'block' : 'none';
        });
    });
</script>

<style>
    /* ===== التصميم المحسن ===== */
    .pharmacy-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 25px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }

    /* رأس الصفحة */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-content h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1e2b37;
        margin: 0 0 5px 0;
    }

    .header-content .subtitle {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .btn-refresh {
        background: white;
        border: 1px solid #dee2e6;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-refresh:hover {
        background: #f8f9fa;
        border-color: #0066cc;
        color: #0066cc;
    }

    /* بطاقات الإحصائيات */
    .stats-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        transition: transform 0.3s;
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
        color: #1976d2;
    }

    .stat-icon.orange {
        background: #fff3e0;
        color: #f57c00;
    }

    .stat-icon.green {
        background: #e8f5e9;
        color: #388e3c;
    }

    .stat-icon i {
        font-size: 24px;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1e2b37;
        line-height: 1.2;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
    }

    /* قسم الفلاتر */
    .filters-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 300px;
    }

    .search-box i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    .search-box input {
        width: 100%;
        padding: 10px 35px 10px 12px;
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

    .filter-tabs {
        display: flex;
        gap: 10px;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }

    .filter-btn.active {
        background: #0066cc;
        color: white;
        border-color: #0066cc;
    }

    .filter-btn:hover:not(.active) {
        background: #f8f9fa;
    }

    /* شبكة الطلبات */
    .requests-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .request-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        border-right: 4px solid transparent;
    }

    .request-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .request-card.high-priority {
        border-right-color: #dc3545;
    }

    .card-header {
        padding: 15px;
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
    }

    .request-badge {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-id {
        font-weight: 600;
        color: #1e2b37;
    }

    .badge-priority {
        background: #dc3545;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .request-time {
        color: #666;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .card-body {
        padding: 15px;
    }

    .patient-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .patient-info i {
        color: #0066cc;
    }

    .info-details h4 {
        margin: 0 0 3px 0;
        font-size: 16px;
        color: #1e2b37;
    }

    .info-details p {
        margin: 0;
        font-size: 13px;
        color: #666;
    }

    .request-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
    }

    .detail-item {
        text-align: center;
    }

    .detail-label {
        display: block;
        font-size: 11px;
        color: #666;
        margin-bottom: 3px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e2b37;
    }

    .card-footer {
        padding: 15px;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .btn-view,
    .btn-dispense {
        flex: 1;
        padding: 8px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.3s;
        text-decoration: none;
    }

    .btn-view {
        background: #f8f9fa;
        color: #0066cc;
        border: 1px solid #dee2e6;
    }

    .btn-view:hover {
        background: #e9ecef;
    }

    .btn-dispense {
        background: #0066cc;
        color: white;
        border: 1px solid #0066cc;
    }

    .btn-dispense:hover {
        background: #0052a3;
    }

    .quick-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .quick-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        color: #666;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quick-btn:hover {
        background: #0066cc;
        color: white;
    }

    /* قسم التحكم */
    .control-section {
        margin-top: 40px;
    }

    .control-section h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 20px;
        color: #1e2b37;
        margin-bottom: 20px;
    }

    .control-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .control-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        color: #1e2b37;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }

    .control-item:hover {
        background: #0066cc;
        color: white;
        border-color: #0066cc;
        transform: translateY(-2px);
    }

    .control-item i {
        font-size: 16px;
    }

    /* حالة فارغة */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        grid-column: 1 / -1;
    }

    .empty-state i {
        color: #dee2e6;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: #666;
        font-size: 16px;
    }

    /* صنع مع Emergent */
    .made-with {
        text-align: center;
        color: #999;
        font-size: 12px;
        margin-top: 50px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .pharmacy-container {
            padding: 15px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filters-section {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            max-width: 100%;
        }

        .filter-tabs {
            justify-content: center;
        }

        .requests-grid {
            grid-template-columns: 1fr;
        }

        .control-grid {
            flex-direction: column;
        }

        .control-item {
            width: 100%;
            justify-content: center;
        }
    }
</style>