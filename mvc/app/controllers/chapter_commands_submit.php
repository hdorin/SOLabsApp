<?php
class Chapter_Commands_Submit extends Controller
{
    public function index()
    {
        $this->check_login();
        $error_msg=$this->session_extract("error_msg");
        $exec_msg=$this->session_extract("exec_msg");
        $code_field=$this->session_extract("code_field");
        $text_field=$this->session_extract("text_field");
        $this->view('home/chapter_commands_submit',['code_field' => $code_field, 'text_field' => $text_field,'error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
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
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('INSERT INTO questions (`user_id`,chapter,`status`,date_created) VALUES (?,?,?,now())');
        $chapter="commands";
        $status="pending";
        $sql->bind_param('iss', $_SESSION['user_id'],$chapter,$status);
        $sql->execute();

        $sql=$link->prepare('SELECT id FROM questions WHERE `user_id`=? AND chapter=? AND `status`=?');
        $sql->bind_param('iss', $_SESSION['user_id'],$chapter,$status);
        $sql->execute();
        $sql->bind_result($question_id);
        $sql->fetch();
        $db_connection->close();
        
        /*for some reason, the third prepare statement doesn't work*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('UPDATE questions SET `status`=? WHERE id=?');        
        $status="posted";
        $sql->bind_param('si', $status,$question_id);
        $sql->execute();
        $db_connection->close();

    }
    public function process(){
        if(strlen($_POST["text_field"])>500 || strlen($_POST["code_field"])>150){
            $this->reload("Characters limit exceeded!");
        }
        if(empty($text=$_POST["text_field"])==true){
            $this->reload("You did not enter the question text!");
        }
        if(empty($command=$_POST["code_field"])==true){
            $this->reload("You did not enter a command!");
        }
        $_SESSION["code_field"]=$_POST["code_field"];
        $_SESSION["text_field"]=$_POST["text_field"];
        if($_POST["action"]=="Execute"){
            $this->execute($command);
        }else{
            $this->submit($text,$command);
            if(isset($_SESSION["code_field"])){
                unset($_SESSION["code_field"]);
            }
            if(isset($_SESSION["text_field"])){
                unset($_SESSION["text_field"]);
            }
            
        }
       
        header('Location: ../submit_question');
    }
}