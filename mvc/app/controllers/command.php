<?php
class Command extends Controller
{
    public function index()
    {
        $this->check_login();
        if(isset($_SESSION["error_msg"])==false){
            $error_msg="";
        }else{
            $error_msg=$_SESSION["error_msg"];
        }
        if(isset($_SESSION["exec_msg"])==false){
            $exec_msg="";
        }else{
            $exec_msg=$_SESSION["exec_msg"];
        }
        unset($_SESSION['error_msg']);
        unset($_SESSION['exec_msg']);
        $this->view('home/command',['error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    public function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../command";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
        if(empty($command=$_POST["command_field"])==true){
            $this->reload("You did not enter a command!");
        } 
        $ssh_connection=$this->model('SSHConnection');
        $ssh_connection->configure('127.0.0.1','22');/*hadcoded*/
        
            try{
                $ssh_connection->connect('dorin.haloca','C0demasters');/*hadcoded*/
            }catch(Exception $e){
                $this->reload($e->getMessage());
            }
        $_SESSION["exec_msg"]=$ssh_connection->execute($command);/*hadcoded*/
        header('Location: ../command');
        die;
    }
}