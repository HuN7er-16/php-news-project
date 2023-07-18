<?php


//session start

use Auth\Auth;

session_start();


//config
define('BASE_PATH', __DIR__);
define('CURRENT_DOMAIN', currentDomain() . '/php-news-project/');
define('DISPLAY_ERROR', true);
define('DB_HOST', 'localhost');
define('DB_NAME', 'news_project');
define('DB_USERNAME', 'Amirali_Hosseini');
define('DB_PASSWORD', '44266007');

//mail
define('MAIL_HOST', 'smtp.gmail.com');
define('SMTP_AUTH', true);
define('MAIL_USERNAME', 'aliamirhosseini216@gmail.com');
define('MAIL_PASSWORD', 'amirali1379');
define('MAIL_PORT', 587);
define('SENDER_MAIL', 'aliamirhosseini216@gmail.com');
define('SENDER_NAME', 'وبسایت خبری اولیه');



require_once 'database/DataBase.php';
require_once 'database/CreateDB.php';
require_once 'activities/Admin/Admin.php';
require_once 'activities/Admin/Category.php';
require_once 'activities/Admin/Post.php';
require_once 'activities/Admin/Banner.php';
require_once 'activities/Admin/User.php';
require_once 'activities/Admin/Comment.php';
require_once 'activities/Admin/Menu.php';
require_once 'activities/Admin/Setting.php';
require_once 'activities/Auth/Auth.php';



//helpers
spl_autoload_register(function($className){

    $path = BASE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
    include $path . $className . '.php';

});


// $auth = new Auth();
// $auth->sendMail('aliamirhosseini216@gmail.com', 'test', '<p>test</p>');

function jalaliDate($date){

    return \Parsidev\Jalali\jDate::forge($date)->format('datetime');

}

function uri($reservedUrl, $class, $method, $requestMethod = 'GET'){

    //current url array
    $currentUrl = explode('?',currentUrl())[0];
    $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
    $currentUrl = trim($currentUrl, '/');
    $currentUrlArray = explode('/', $currentUrl);
    $currentUrlArray = array_filter($currentUrlArray);


    //reserver Url array
    $reservedUrl = trim($reservedUrl, '/');
    $reservedUrlArray = explode('/', $reservedUrl);
    $reservedUrlArray = array_filter($reservedUrlArray);


    if(sizeof($reservedUrlArray) != sizeof($currentUrlArray) or methodField() != $requestMethod){

        return false;

    }

    $parameters = [];
    for($key = 0; $key < sizeof($reservedUrlArray); $key++){

        if($reservedUrlArray[$key][0] =="{" and $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] =="}"){

            array_push($parameters, $currentUrlArray[$key]);

        }elseif($currentUrlArray[$key] !== $reservedUrlArray[$key]){

            return false;

        }

    }

    if(methodField() == 'POST'){

        $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $parameters = array_merge([$request], $parameters);

    }


    $object = new $class;
    call_user_func_array(array($object, $method), $parameters);
    exit();

}

function protocol(){

    return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

}

function currentDomain(){

    return protocol() . $_SERVER['HTTP_HOST'];

}

function asset($src){

    $domain = trim(CURRENT_DOMAIN, '/ ');
    $src = $domain . '/' . trim($src, '/ ');
    return $src;

}

function url($url){

    $domain = trim(CURRENT_DOMAIN, '/ ');
    $url = $domain . '/' . trim($url, '/ ');
    return $url;

}

function currentUrl(){

    return currentDomain() . $_SERVER['REQUEST_URI'];

}

function methodField(){

    return $_SERVER['REQUEST_METHOD'];

}

function displayError($displayError){

    if($displayError){

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

    }else{

        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);

    }

}

global $flashMessage;

if(isset($_SESSION['flash_message'])){

    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

}

function flash($name, $value = null){

    if($value === null){

        global $flashMessage;
        $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
        return $message;

    }else{

        $_SESSION['flash_message'][$name] = $value;

    }

}

function dd($var){
    echo '<pre>';
    var_dump($var);
    exit;
}


//category
uri('admin/category', 'Admin\Category', 'index');
uri('admin/category/create', 'Admin\Category', 'create');
uri('admin/category/store', 'Admin\Category', 'store', 'POST');
uri('admin/category/edit/{id}', 'Admin\Category', 'edit');
uri('admin/category/update/{id}', 'Admin\Category', 'update', 'POST');
uri('admin/category/delete/{id}', 'Admin\Category', 'delete');


//posts
uri('admin/post', 'Admin\Post', 'index');
uri('admin/post/create', 'Admin\Post', 'create');
uri('admin/post/store', 'Admin\Post', 'store', 'POST');
uri('admin/post/edit/{id}', 'Admin\Post', 'edit');
uri('admin/post/update/{id}', 'Admin\Post', 'update', 'POST');
uri('admin/post/delete/{id}', 'Admin\Post', 'delete');
uri('admin/post/selected/{id}', 'Admin\Post', 'selected');
uri('admin/post/breaking-news/{id}', 'Admin\Post', 'breakingNews');

//banners
uri('admin/banner', 'Admin\Banner', 'index');
uri('admin/banner/create', 'Admin\Banner', 'create');
uri('admin/banner/store', 'Admin\Banner', 'store', 'POST');
uri('admin/banner/edit/{id}', 'Admin\Banner', 'edit');
uri('admin/banner/update/{id}', 'Admin\Banner', 'update', 'POST');
uri('admin/banner/delete/{id}', 'Admin\Banner', 'delete');

//users
uri('admin/user', 'Admin\User', 'index');
uri('admin/user/edit/{id}', 'Admin\User', 'edit');
uri('admin/user/update/{id}', 'Admin\User', 'update', 'POST');
uri('admin/user/delete/{id}', 'Admin\User', 'delete');
uri('admin/user/permission/{id}', 'Admin\User', 'permission');

//comments
uri('admin/comment', 'Admin\Comment', 'index');
uri('admin/comment/change-status/{id}', 'Admin\Comment', 'changeStatus');

//menu
uri('admin/menu', 'Admin\Menu', 'index');
uri('admin/menu/create', 'Admin\Menu', 'create');
uri('admin/menu/store', 'Admin\Menu', 'store', 'POST');
uri('admin/menu/edit/{id}', 'Admin\Menu', 'edit');
uri('admin/menu/update/{id}', 'Admin\Menu', 'update', 'POST');
uri('admin/menu/delete/{id}', 'Admin\Menu', 'delete');

//setting
uri('admin/setting', 'Admin\Setting', 'index');
uri('admin/setting/edit', 'Admin\Setting', 'edit');
uri('admin/setting/update', 'Admin\Setting', 'update', 'POST');

//Auth
uri('register', 'Auth\Auth', 'register');
uri('register/store', 'Auth\Auth', 'registerStore', 'POST');
uri('activation/{verify_token}', 'Auth\Auth', 'activation');

echo '404- page not found';


