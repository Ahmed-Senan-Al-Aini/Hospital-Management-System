<?php

class Middleware
{
    /**
     * 
     * @return void
     */
    public static function requir_auth()
    {
        if (!Auth::check()) {
            if (self::isAjax()) {
                self::jsonError('Required login first ', 401);
            }
            header('Location:' . BASE_URL . 'auth/login');
            exit;
        }
    }


    /**
     * @return void
     */
    public static function requir_guest()
    {
        if (Auth::check()) {
            header('Location:' . BASE_URL . 'dashboard');
            exit;
        }
    }



    public static function requir_role($role)
    {
        self::requir_auth();

        if (!Auth::has_role($role)) {
            self::forbidden('ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }
    }




    public static function requir_Any_role($roles)
    {
        if (!is_array($roles) || empty($roles)) {
            error_log("Middleware error: invald roles array");
            self::forbidden('خطاء في الصلاحيات');
            return;
        }

        self::requir_auth();
        if (!Auth::hasAnyRole($roles)) {
            self::forbidden('ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }
    }



    public static function requir_all_role($roles)
    {
        error_log(" hkkjl ahmed -in ");
        self::requir_auth();
        if (!Auth::hasAllRole($roles)) {
            self::forbidden('ليس لديك الصلاحيات الكافية');
        }
    }

    public static function requirePermission($permission)
    {
        self::requir_auth();

        if (!Auth::can($permission)) {
            error_log(" hkkjl ahmed -in ");
            self::forbidden('ليس لديك الصلاحية لتنفيذ هذا الإجراء');
        }
    }

    public static function requirePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::jsonError('طريقة طلب غير صحيحة', 405);
            error_log(" hkkjl ahmed -in ");
        }
    }


    public static function requireCsrfToken()
    {
        $methods = ['POST', 'PUT', 'DELETE', 'PATCH'];

        if (in_array($_SERVER['REQUEST_METHOD'], $methods)) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (!CSRF::validate($token)) {
                if (self::isAjax()) {
                    self::jsonError('رمز CSRF غير صالح', 403);
                }

                error_log("CSRF validation failed for IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

                header('HTTP/1.0 403 Forbidden');
                die('خطأ في التحقق من الأمان (CSRF). يرجى تحديث الصفحة والمحاولة مرة أخرى.');
            }
        }
    }

    /**
     * صفحة ممنوع الوصول
     */
    private static function forbidden($message = 'غير مصرح بالوصول')
    {
        if (self::isAjax()) {
            self::jsonError($message, 403);
        }

        header('HTTP/1.0 403 Forbidden');

        if (file_exists(VIEWS_PATH . 'errors/403.php')) {
            require VIEWS_PATH . 'errors/403.php';
        } else {
            echo "<!DOCTYPE html>
            <html dir='rtl'>
            <head>
                <title>403 - غير مصرح بالوصول</title>
                <style>
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background: #f8f9fa;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .error-container {
                        text-align: center;
                        background: white;
                        padding: 50px;
                        border-radius: 15px;
                        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                    }
                    .error-code {
                        font-size: 72px;
                        font-weight: 800;
                        color: #dc3545;
                        margin: 0;
                    }
                    .error-message {
                        font-size: 24px;
                        color: #1e2b37;
                        margin: 20px 0;
                    }
                    .error-description {
                        color: #6c757d;
                        margin-bottom: 30px;
                    }
                    .btn-home {
                        background: #0066cc;
                        color: white;
                        padding: 12px 30px;
                        text-decoration: none;
                        border-radius: 8px;
                        display: inline-block;
                    }
                    .btn-home:hover {
                        background: #0052a3;
                    }
                </style>
            </head>
            <body>
                <div class='error-container'>
                    <h1 class='error-code'>403</h1>
                    <h2 class='error-message'>غير مصرح بالوصول</h2>
                    <p class='error-description'>$message</p>
                    <a href='" . BASE_URL . "dashboard' class='btn-home'>العودة للرئيسية</a>
                </div>
            </body>
            </html>";
        }
        exit;
    }

    /**
     * التحقق من طلب AJAX
     */
    private static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * إرجاع خطأ JSON
     */
    private static function jsonError($massage, $code)
    {

        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => "hhh", 'massage' => $massage]);
        exit;
    }

}
