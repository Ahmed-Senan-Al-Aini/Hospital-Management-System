// تأكيد الإجراءات
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

// تنسيق التاريخ
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("ar-SA");
}

// إظهار مؤشر التحميل
function showLoading() {
  const loader = document.createElement("div");
  loader.className = "loader";
  loader.id = "loader";
  loader.innerHTML = '<div class="spinner"></div>';
  document.body.appendChild(loader);
}

// إخفاء مؤشر التحميل
function hideLoading() {
  const loader = document.getElementById("loader");
  if (loader) {
    loader.remove();
  }
}

// إظهار إشعار
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 3000);
}

// إغلاق التنبيهات تلقائياً
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.display = "none";
    }, 5000);
  });
});


// إضافة loader styles
const style = document.createElement("style");
style.textContent = `
    .loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .notification {
        position: fixed;
        top: 20px;
        left: 20px;
        padding: 15px 20px;
        background: white;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        border-right: 4px solid;
    }
    .notification-info {
        border-color: #28a745;
    }
    .notification-success {
        border-color: #28a745;
    }
    .notification-warning {
        border-color: #ffc107;
    }
    .notification-error {
        border-color: #dc3545;
    }
`;
document.head.appendChild(style);


document.addEventListener('click', function (e) {
  if (e.target.classList.contains('quick-control')) {
    e.preventDefault();

    const action = e.target.dataset.action;
    const id = e.target.dataset.id;

    showLoading();

    fetch(BASE_URL + 'control/api', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: action,
        id: id,
        data: {}
      })
    })
      .then(response => response.json())
      .then(data => {
        hideLoading();
        if (data.success) {
          showNotification(data.message, 'success');
        } else {
          showNotification('حدث خطأ: ' + data.error, 'error');
        }
      });
  }
});