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
        //if(isset($_SESSION['user_id'])==false){
            $this->view('home/login',['error_msg' => $error_msg]);
       // }else{
        //    echo "You must first logout!";
        //}
        unset($_SESSION["error_msg"]);
    }
    public function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../login";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
        if(empty($user=$_POST["user_field"])==1){
            $this->reload("You did not enter a username!");
        }
        if(empty($pass=$_POST["pass_field"])==1){
            $this->reload("You did not enter a password!");
        }
        $connection=$this->model('SSHConnection');
        try{
            if(!$connection->connect($user,$pass,true)){
                $this->reload("Invalid username/password!");
            }
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        die("LOGAT!");
    }
}