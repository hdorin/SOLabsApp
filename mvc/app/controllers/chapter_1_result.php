<?php
//Chapter Commands
class Chapter_1_Result extends Controller
{
    public function index()
    {
        $this->check_login();
        $question_id=$this->session_extract('question_id');
        if(empty($question_id)){
            die("You can't access this!");
        }
        $question_text=$this->session_extract('question_text');
        $user_command=$this->session_extract('user_command');
        $user_output=$this->session_extract('user_output');
        $author_command=$this->session_extract('author_command');
        $author_output=$this->session_extract('author_output');
        $result_correct=$this->session_extract('result_correct');
        $result_incorrect=$this->session_extract('result_incorrect');
        $error_msg=$this->session_extract("error_msg",true);
        $this->view('home/chapter_1_result',['error_msg' => $error_msg,'result_correct' => $result_correct,'result_incorrect' => $result_incorrect,'question_text' => $question_text, 'user_command' => $user_command,'user_output' => $user_output, 'author_command' => $author_command, 'author_output' => $author_output]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_1_result";
        header('Location: '.$new_url);
        die;
    }
    private function report($message,$question_id){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('INSERT INTO reports (`user_id`,question_id,`text`,date_created) VALUES (?,?,?,now())');
        $sql->bind_param('iis', $this->session_user_id,$question_id,$message);
        $sql->execute();
        $sql->close();
    }
    public function process(){
        $this->check_login();
        if(strlen($_POST["text_field"])>50){
            $this->reload("Characters limit exceeded!");
        }
        $question_id=$this->session_extract('question_id',true);
        $this->session_extract('question_text',true);
        $this->session_extract('user_command',true);
        $this->session_extract('user_output',true);
        $this->session_extract('author_command',true);
        $this->session_extract('author_output',true);
        $this->session_extract('result_correct',true);
        $this->session_extract('result_incorrect',true);
        if($_POST["action"]=="Report"){
            if(strcmp($_POST["text_field"],"")==0){
                $message="";
            }else{
                $message=$_POST["text_field"];
            }
            $this->report($message,$question_id);
        }
        header('Location: ../chapter_1_solve');
    }
}