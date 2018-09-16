<?php
//Chapter Commands
class Chapter_1_Result extends Controller
{
    private $question_id;
    public function index()
    {
        $this->check_login();
        $this->question_id=$this->session_extract('question_id');
        $question_text=$this->session_extract('question_text');
        $user_command=$this->session_extract('user_command');
        $user_output=$this->session_extract('user_output');
        $author_command=$this->session_extract('author_command');
        $author_output=$this->session_extract('author_output');
        $result_correct=$this->session_extract('result_correct');
        $result_incorrect=$this->session_extract('result_incorrect');
        $this->view('home/chapter_1_result',['result_correct' => $result_correct,'result_incorrect' => $result_incorrect,'question_text' => $question_text, 'user_command' => $user_command,'user_output' => $user_output, 'author_command' => $author_command, 'author_output' => $author_output]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_1_submit";
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
    private function get_question(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $last_question_id=$_SESSION['last_question_id'];
        exec("cat /var/www/html/AplicatieSO/mvc/app/questions/" . (string)$last_question_id . ".text",$question_text_aux);
        $this->question_text=$question_text_aux[0];
        exec("cat /var/www/html/AplicatieSO/mvc/app/questions/" . (string)$last_question_id . ".code",$question_code_aux);
        $question_code=$question_code_aux[0];
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
            header('Location: ../chapter_1_submit');
        }else{
            $this->submit($text,$command);
            $this->session_extract("code_field",true);
            $this->session_extract("text_field",true);
            header('Location: ../submit_question');
        }
    }
}