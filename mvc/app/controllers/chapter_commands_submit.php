<?php
class Chapter_Commands_Submit extends Controller
{
    public function index()
    {
        $this->check_login();
        $error_msg=$this->session_extract("error_msg");
        $exec_msg=$this->session_extract("exec_msg");
        $input_field=$this->session_extract("input_field");
        $text_field=$this->session_extract("text_field");
        $this->view('home/chapter_commands_submit',['input_field' => $input_field, 'text_field' => $text_field,'error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_commands_submit";
        header('Location: '.$new_url);
        die;
    }
    private function execute($command){
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
        try{    
            $_SESSION["exec_msg"]=$ssh_connection->execute($command,$ssh_timeout_seconds);
        }catch(Exception $e){
            if(empty($e->getMessage())==true){
                $this->reload("Output cannot be empty!");
            }
            $this->reload($e->getMessage());
        }
        
        $ssh_connection->close();
    }
    private function submit($text,$command){
        $this->execute($command);
        
    }
    public function process(){
        if(strlen($_POST["text_field"])>500 || strlen($_POST["input_field"])>150){
            $this->reload("Characters limit exceeded!");
        }
        if(empty($text=$_POST["text_field"])==true){
            $this->reload("You did not enter the question text!");
        }
        if(empty($command=$_POST["input_field"])==true){
            $this->reload("You did not enter a command!");
        }
        $_SESSION["input_field"]=$_POST["input_field"];
        $_SESSION["text_field"]=$_POST["text_field"];
        if($_POST["action"]=="Execute"){
            $this->execute($command);
        }else{
            $this->submit($text,$command);
            if(isset($_SESSION["input_field"])){
                unset($_SESSION["input_field"]);
            }
            if(isset($_SESSION["text_field"])){
                unset($_SESSION["text_field"]);
            }
            die("BUN");
        }
       
        header('Location: ../chapter_commands_submit');
    }
}