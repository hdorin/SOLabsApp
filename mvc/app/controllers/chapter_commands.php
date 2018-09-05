<?php
class Chapter_Commands extends Controller
{
    public function index()
    {
        $this->check_login();
        if(isset($_SESSION["input_field"])){
            unset($_SESSION["input_field"]);
        }
        if(isset($_SESSION["text_field"])){
            unset($_SESSION["text_field"]);
        }
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
        $this->view('home/chapter_commands',['error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_commands";
        header('Location: '.$new_url);
        die;
    }
    public function correct_answer(){ /*add question_id*/
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT right_answers FROM chapter_1 WHERE `user_id`=?');
        $sql->bind_param('i', $_SESSION['user_id']);
        $sql->execute();
        $sql->bind_result($right_answers);
        $status=$sql->fetch();
        $db_connection->close();

        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if(!$status){/*insert user into chapter_1 table*/
            $sql=$link->prepare('INSERT INTO chapter_1 (`user_id`,right_answers) VALUES (?,?)');
            $right_answers=1;
            $sql->bind_param('si', $_SESSION['user_id'],$right_answers);
            $sql->execute();
        }else{/*increment right_answers for user*/
            $sql=$link->prepare('UPDATE chapter_1 SET right_answers=? WHERE `user_id`=?');        
            $right_answers=$right_answers+1;
            $sql->bind_param('ii',$right_answers,$_SESSION['user_id']);
            $sql->execute();
        }
        $db_connection->close();
    }
    public function process(){
        if(strlen($_POST["input_field"])>150 ){
            $this->reload("Characters limit exceeded!");
        }
        if(empty($command=$_POST["input_field"])==true){
            $this->reload("You did not enter a command!");
        }
        if($_POST["action"]=="Execute"){
            $_SESSION["input_field"]=$_POST["input_field"];
        }else{
            if(isset($_SESSION["input_field"])){
                unset($_SESSION["input_field"]);
            }
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
        try{    
            $_SESSION["exec_msg"]=$ssh_connection->execute($command,$ssh_timeout_seconds);
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        
        $ssh_connection->close();
        header('Location: ../chapter_commands');
    }
}