<?php
class Chapter_Commands extends Controller
{
    private $question_text;
    private $get_question_input;
    public function index()
    {
        $this->check_login();
        $this->get_question();
        
        $error_msg=$this->session_extract("error_msg",true);
        $exec_msg=$this->session_extract("exec_msg",true);
        $code_field=$this->session_extract("code_field");
        $this->view('home/chapter_commands',['question_text' => $this->question_text, 'code_field' =>$code_field,'error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_commands";
        header('Location: '.$new_url);
        die;
    }
    public function get_question(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT last_question_id FROM chapter_1 WHERE `user_id`=?');
        $sql->bind_param('i', $_SESSION['user_id']);
        $sql->execute();
        $sql->bind_result($last_question_id);
        $status=$sql->fetch();
        $sql->close();
        
        if(!$status){/*insert user into chapter_1 table*/
            $sql=$link->prepare('SELECT id FROM questions WHERE chapter_id=1');
            $sql->execute();
            $sql->bind_result($last_question_id);
            $sql->fetch();
            $sql->close();
            $sql=$link->prepare('INSERT INTO chapter_1 (`user_id`,right_answers,last_question_id) VALUES (?,?,?)');
            $right_answers=0;
            $sql->bind_param('sii', $_SESSION['user_id'],$right_answers,$last_question_id);
            $sql->execute();
            $sql->close();
        }/*increment right_answers for user*/
        $db_connection->close();
        exec("cat /var/www/html/AplicatieSO/mvc/app/questions/" . (string)$last_question_id . ".text",$question_text_aux);
        $this->question_text=$question_text_aux[0];
        
    }
    private function correct_answer(){ /*add question_id*/
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT right_answers FROM chapter_1 WHERE `user_id`=?');
        $sql->bind_param('i', $_SESSION['user_id']);
        $sql->execute();
        $sql->bind_result($right_answers);
        $sql->fetch();
        $sql->close();
        /*increment right_answers for user*/
        $sql=$link->prepare('UPDATE chapter_1 SET right_answers=? WHERE `user_id`=?');        
        $right_answers=$right_answers+1;
        $sql->bind_param('ii',$right_answers,$_SESSION['user_id']);
        $sql->execute();
        $sql->close();
        $db_connection->close();
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
    public function process(){
        if(strlen($_POST["code_field"])>150){
            $this->reload("Characters limit exceeded!");
        }
        if(empty($command=$_POST["code_field"])==true){
            $this->reload("You did not enter a command!");
        }
        $_SESSION["code_field"]=$_POST["code_field"];
        if($_POST["action"]=="Execute"){
            $this->execute($command);
        }else{
            $this->submit($text,$command);
            $this->session_extract("code_field",true);
            $this->session_extract("text_field",true);       
        }
        header('Location: ../chapter_commands');
    }
}