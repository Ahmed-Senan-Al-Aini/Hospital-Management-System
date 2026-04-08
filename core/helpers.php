<?php
// core/helpers.php

function time_elapsed_string($datetime, $full = false)
{
    if (empty($datetime)) {
        return 'الآن';
    }

    try {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        if (!$diff) {
            return 'الآن';
        }

        $units = [
            'y' => 'سنة',
            'm' => 'شهر',
            'd' => 'يوم',
            'h' => 'ساعة',
            'i' => 'دقيقة',
            's' => 'ثانية',
        ];

        $parts = [];

        foreach ($units as $key => $label) {
            if (isset($diff->$key) && $diff->$key > 0) {
                $parts[] = $diff->$key . ' ' . $label . ($diff->$key > 1 ? '' : '');
            }
        }

        if (empty($parts)) {
            return 'الآن';
        }

        if (!$full) {
            return 'منذ ' . $parts[0];
        }

        return 'منذ ' . implode('، ', $parts);
    } catch (Exception $e) {
        error_log("Error in time_elapsed_string: " . $e->getMessage());
        return 'الآن';
    }
}


function set_flash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}


function get_flash($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}


function has_flash($key)
{
    return isset($_SESSION['flash'][$key]);
}



// دالة مخصصة للأرقام العشوائية
function safe_random($min, $max)
{
    // استخدام الطابع الزمني لتوليد رقم شبه عشوائي
    $range = $max - $min;
    $rand = (int)(microtime(true) * 1000) % ($range + 1);
    return $min + $rand;
}


function csrf_field()
{
    return CSRF::field();
}


function csrf_check()
{
    return CSRF::checkPost();
}


function csrf_token()
{
    return CSRF::getToken();
}

function validateId($id, $redirectUrl = null)
{
    if (!is_numeric($id) || $id <= 0) {
        if ($redirectUrl) {
            Session::flash('error', 'معرف غير صالح');
            header('Location: ' . BASE_URL . $redirectUrl);
            exit;
        }
        return false;
    }
    return (int)$id;
}
