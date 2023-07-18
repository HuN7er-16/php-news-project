<?php

namespace Auth;

use database\DataBase;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Auth {

    protected function redirect($url)
    {

        header('location: ' . trim(CURRENT_DOMAIN, '/ ') . '/' . trim($url, '/ '));
        exit;
    }

    protected function redirectBack()
    {

        header('location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    private function hash($password)
    {

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashPassword;
    }

    private function random(){
        
        return bin2hex(openssl_random_pseudo_bytes(32));

    }

    public function activationMessage($username, $verifyToken){

        $message = '
        <h1>فعال سازی حساب کاربری</h1>
        <p>'. $username .' عزیز برای فعال سازی حساب کاربری خود روی لینک زیر کلیک کنید</p>
        <div><a href="'. url('activation/' . $verifyToken) .'">فعال سازی حساب</a></div>
        ';
        return $message;

    }

    private function sendMail($emailAddress, $subject, $body)
    {

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug  = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->CharSet    = "UTF-8";                   
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = SMTP_AUTH;                                   //Enable SMTP authentication
            $mail->Username   = MAIL_USERNAME;                     //SMTP username
            $mail->Password   = MAIL_PASSWORD;                               //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port       = MAIL_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(SENDER_MAIL, SENDER_NAME);
            $mail->addAddress($emailAddress);     //Add a recipient


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    public function register(){
        
        require_once (BASE_PATH . '/template/auth/register.php');

    }

    public function registerStore($request){

        if(empty($request['email']) or empty($request['username']) or empty($request['password'])){

            flash('register_error', 'تمامی فیلد ها اجباری میباشند');
            $this->redirectBack();

        }elseif(strlen($request['password']) < 8){

            flash('register_error', 'رمز عبور باید حداقل 8 کارکتر باشد');
            $this->redirectBack();

        }elseif(!filter_var($request['email'], FILTER_VALIDATE_EMAIL)){

            flash('register_error', 'ایمیل معتبری وارد نشده است');
            $this->redirectBack();

        }else{

            $db = new DataBase();
            $user = $db->select('SELECT * FROM `users` WHERE `email` = ?', [$request['email']])->fetch();
            if($user != null){

                flash('register_error', 'کاربر در سیستم وجود دارد');
                $this->redirectBack();

            }else{

                $randomToken = $this->random();
                $activationMessage = $this->activationMessage($request['username'], $randomToken);
                $result = $this->sendMail($request['email'], 'فعال سازی حساب کاربری', $activationMessage);
                if($result){

                    $request['verify_token'] = $randomToken;
                    $request['password'] = $this->hash($request['password']);
                    $db->insert('users', array_keys($request), $request);
                    $this->redirect('login');

                }else{

                    flash('register_error', 'ارسال ایمیل با خطا مواجه شد');
                    $this->redirectBack();

                }

            }

        }

    }

    public function activation($verifyToken){

        $db = new DataBase();
        $user = $db->select("SELECT * FROM users WHERE verify_token = ? AND is_active = 1;", [$verifyToken])->fetch();
        if($user == null){

            $this->redirect('login');

        }else{

            $result = $db->update('users', $user['id'], ['is_active'], [2]);
            $this->redirect('login');

        }

    }

    public function login(){

        require_once (BASE_PATH . '/template/auth/login.php');

    }

    public function checkLogin($request){
        
        if(empty($request['email']) or empty($request['password'])){

            flash('login_error', 'تمامی فیلد ها الزامی است');
            $this->redirectBack();

        }else{

            $db = new  DataBase();
            $user = $db->select("SELECT * FROM users WHERE email = ?", [$request['email']])->fetch();
            if($user != null){

                if($request['password'] == $user['password'] && $user['is_active'] == 2){

                    $_SESSION['user'] = $user['id'];
                    $this->redirect('admin');

                }else{
                    
                    flash('login_error', 'ایمیل یا رمز عبور اشتباه است');
                    $this->redirectBack();  

                }

            }else{

                flash('login_error', 'کاربری با این مشخصات یافت نشد');
                $this->redirectBack(); 

            }

        }

    }

    public function checkAdmin(){

        if(isset($_SESSION['user'])){

            $db = new DataBase();
            $user = $db->select("SELECT * FROM users WHERE id = ?;", [$_SESSION['user']])->fetch();
            if($user != null){

                if($user['permission'] != 'admin'){

                    $this->redirect('home');

                }

            }else{

                $this->redirect('home');

            }

        }else{

            $this->redirect('home');

        }

    }

    public function logout(){
        
        if(isset($_SESSION['user'])){

            unset($_SESSION['user']);
            session_destroy();

        } 
        $this->redirect('home');  

    }
}
