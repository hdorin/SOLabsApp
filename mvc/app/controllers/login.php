<?php
class Login extends Controller
{
    public function index()
    {
        if(isset($_SESSION["error"])==false){
            $error="";
        }else{
            $error=$_SESSION["error"];
        }
        if(isset($_SESSION['userId'])==false){
            $this->view('home/login',['error' => $error]);
        }else{
            echo "You must first logout!";
        }
        unset($_SESSION["error"]);
    }
    public function reload($data=''){
        $_SESSION["error"]=$data;
        $newURL="../login";
        header('Location: '.$newURL);
        die;
    }
    public function remember_user($userId){
        $link = $this->auctiox_db_connect();
        $sql = $link->prepare('INSERT INTO cookies (user_id,value,date_created) VALUES (?, ?,now())');
        do{
            $cookie_value=(string)random_int(0,1000000000);
            $cookie_value=$cookie_value . (string)random_int(0,1000000000);
            $cookie_value=$cookie_value . (string)random_int(0,1000000000);
            setcookie("AuthenticationId", $cookie_value, time()+3600, "/");/* expire in 1 hour */
            $cookie_value=md5($cookie_value);
            $sql->bind_param('ss', $userId,$cookie_value); 
        }while($sql->execute() == false);//in case cookie value already exists
        mysqli_close($link);
    }
    public function process(){
        if(empty($_POST["emailField"])==1){
            $this->reload("You did not enter an email!");
        }
        $email=$_POST["emailField"];
        if(empty($_POST["passField"])==1){
            $this->reload("You did not enter a password!");
        }
        $pass=$_POST["passField"];
        $pass=md5($pass);
        //echo $email ;
         //echo $pass;
        $this->check_data($email,$pass);
    }
    public function check_data($email,$pass){
        $link = $this->auctiox_db_connect();
 
        $sql = $link->prepare('SELECT id,name FROM users WHERE email=? AND passw=?');
        $sql->bind_param('ss', $email,$pass); 
        $sql->execute();
        $sql->bind_result($userId,$userName);
        $sql->fetch();
        mysqli_close($link);
        
        if(empty($userId)){
            $this->reload("Invalid email or password!");
        }else{
            
            $_SESSION["userId"]=$userId;
            $_SESSION["userName"]=$userName;
            //die ($_SESSION["userName"]);
            if(isset($_POST["rememberMe"])){
                $this->remember_user($userId);
            }
            $newURL="../";
            header('Location: '.$newURL);
        }
    }
}