<?php
class Command extends Controller
{
    public function index()
    {
        if(isset($_SESSION["error_msg"])==false){
            $error_msg="";
        }else{
            $error_msg=$_SESSION["error_msg"];
        }
        unset($_SESSION["error_msg"]);
        if(isset($_SESSION['user_name'])==false){
            die('You are not logged in!');
        }
        $this->view('home/command',['error_msg' => $error_msg]);
    }
    public function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../command";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
        if(empty($command=$_POST["command_field"])==1){
            $this->reload("You did not enter a command!");
        } 
        $ssh_connection=$this->model('SSHConnection');
        $ssh_connection->configure('127.0.0.1','22');
        
            try{
                $ssh_connection->connect('dorin.haloca','C0demasters');
            }catch(Exception $e){
                $this->reload($e->getMessage());
            }
            
        $ssh_connection->execute($command);
    }
}