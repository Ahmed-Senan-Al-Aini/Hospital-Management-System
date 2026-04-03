
<?php

define('APP_NAME', 'Hospital System');
define('APP_VERSION', '1.0');
define('APP_DEBUG', true);


//مسارات المجلدات
define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
define('CORE_PATH', ROOT_PATH . 'core' . DS);
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers' . DS);
define('MODELS_PATH', ROOT_PATH . 'models' . DS);
define('VIEWS_PATH', ROOT_PATH . 'views' . DS);
define('PUBLIC_PATH', ROOT_PATH . 'public' . DS);
define('STORAGE_PATH', ROOT_PATH . 'storage' . DS);


// مسارات أساسية
define('PDF_PATH', STORAGE_PATH . 'pdf/');
define('LOG_PATH', STORAGE_PATH . 'logs/');
define('BASE_URL', 'http://localhost/project/Hospital-Management-System/');
// define('BASE_URL', 'http://localhost/project/pharmacy/');
define('PUBLIC_URL', BASE_URL . 'public/');
define('CSS_URL', PUBLIC_URL . 'css/');
define('JS_URL', PUBLIC_URL . 'js/');
define('IMAGES_URL', PUBLIC_URL . 'images/');



date_default_timezone_set('Asia/Riyadh');
