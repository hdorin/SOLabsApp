<?php
//Chapter Commands
class Chapter_1_Result extends Controller
{
    const CHAPTER_ID=1;
    const REPORT_MAX_LEN=100;
    private $question_id;
    public function index()
    {
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->question_id=$this->session_extract('question_id');
        if(empty($this->question_id)){
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
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_result',['chapter_id' => (string)self::CHAPTER_ID,'error_msg' => $error_msg,'result_correct' => $result_correct,'result_incorrect' => $result_incorrect,'question_text' => $question_text, 'user_command' => $user_command,'user_output' => $user_output, 'author_command' => $author_command, 'author_output' => $author_output]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_" . (string)self::CHAPTER_ID . "_result";
        header('Location: '.$new_url);
        $this->my_sem_release();
        die;
    }
    private function report($message){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        /*Only one report per question is allowed*/
        $sql=$link->prepare('SELECT id FROM reports WHERE `user_id`=? AND question_id=?');
        $sql->bind_param('ii', $this->session_user_id,$this->question_id);
        $sql->execute();
        $sql->bind_result($aux);
        if($sql->fetch()){
            $sql->close();
            $db_connection->close(); 
            $this->reload("You already reported this question!");
        }
        $sql->close();
        $sql=$link->prepare('INSERT INTO reports (`user_id`,question_id,`text`,date_created) VALUES (?,?,?,now())');
        $sql->bind_param('iis', $this->session_user_id,$this->question_id,$message);
        $sql->execute();
        /*Increment reports_nr for question*/
        $sql=$link->prepare('SELECT reports_nr FROM questions WHERE id=?');
        $sql->bind_param('i', $this->question_id);
        $sql->execute();
        $sql->bind_result($reports_nr);
        $sql->fetch();
        $sql->close();
        $reports_nr=$reports_nr+1;
        $sql=$link->prepare("UPDATE questions SET reports_nr=? WHERE id=?");        
        $sql->bind_param('ii',$reports_nr,$this->question_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
    }
    public function process(){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->my_sem_acquire($this->session_user_id);
        if(strlen($_POST["text_field"])>self::REPORT_MAX_LEN){
            $this->reload("Characters limit exceeded!");
        }
        if($_POST["action"]=="Report"){
            if(strcmp($_POST["text_field"],"")==0){
                $message="";
            }else{
                $message=$_POST["text_field"];
            }
            $this->question_id=$this->session_extract('question_id');
            if(empty($this->question_id)){
                $this->my_sem_release();
                die("You can't submit report!");
            }
            if(strcmp($message,'Enter report message')==0){
                $message="";
            }
            $this->report($message);
        }
        /*Clear output message*/
        $this->session_extract('question_id',true);
        $this->session_extract('question_text',true);
        $this->session_extract('user_command',true);
        $this->session_extract('user_output',true);
        $this->session_extract('author_command',true);
        $this->session_extract('author_output',true);
        $this->session_extract('result_correct',true);
        $this->session_extract('result_incorrect',true);
        header('Location: ../chapter_' . (string)self::CHAPTER_ID . '_solve');
        $this->my_sem_release();
    }
}