<?php
class Login extends Controller
{
    public function index()
    {
        if(isset($_SESSION["error_msg"])==false){
            $error_msg="";
        }else{
            $error_msg=$_SESSION["error_msg"];
        }
        if(isset($_SESSION['user_id'])==false){
            $this->view('home/login',['error_msg' => $error_msg]);
        }else{
            echo "You must first logout!";
        }
        unset($_SESSION["error"]);
    }
    public function reload($data=''){
        $_SESSION["error"]=$data;
        $new_url="../login";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
        if(empty($user=$_POST["user_field"])==1){
            $this->reload("You did not enter an email!");
        }
        if(empty($pass=$_POST["pass_field"])==1){
            $this->reload("You did not enter a password!");
        }
        echo $user ;
        echo $pass;
        $this->check_data($user,$pass);
    }
    public function check_data($user,$pass){
        $ssh=$this->model('SSHConnection');
        if (!($ssh->connect('127.0.0.1','2222',$user,$pass))) {
            die('Cannot connect to server with SSH');
        }
        
        die;

        
    }
}