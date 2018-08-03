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
        $config=$this->model('JSONConfig');
        $ssh_host=$config->get('ssh','host');
        $ssh_port=$config->get('ssh','port');
        $ssh_timeout_seconds=$config->get('ssh','timeout_seconds');
        $ssh_user=$_SESSION['user'];
        $ssh_pass=$_SESSION['pass'];
        $ssh_connection=$this->model('SSHConnection');
        $ssh_connection->configure($ssh_host,$ssh_port);
        try{
            if(!$ssh_connection->connect($ssh_user,$ssh_pass)){
                $ssh_connection->close();
                $this->reload("Could not access Linux machine!");
            }
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        $_SESSION["exec_msg"]=$ssh_connection->execute($command,$ssh_timeout_seconds);
        $ssh_connection->close();
        header('Location: ../command');
    }
}