<?php
class View_Questions extends Controller
{
    private $questions;
    private $questions_nr;
    public function index()
    {
        $this->check_login();
        $this->session_extract("exec_msg",true);
        $this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);

        if($_SESSION['is_admin']==true){
            $this->get_questions_admin();
        }else{
            $this->get_questions_user();
        }
        $this->view('home/questions',['questions' => $this->questions,'questions_nr' => $this->questions_nr]);
    }
    private function get_questions_user(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT id,`chapter_id`,all_answers,right_answers,`validation` FROM questions WHERE `user_id`=? AND `status`='posted'");
        $sql->bind_param('i',$_SESSION['user_id']);
        $sql->execute();
        $sql->bind_result($question_id,$chapter_id,$all_answers,$right_answers,$validation);
        $this->questions_nr=0;
        
        while($sql->fetch()){
            exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text',$question_text_aux);
            $question_text=$question_text_aux[$this->questions_nr];
            $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                <p >" . $question_text . "</p>
                                                        </a>";
            $this->questions_nr=$this->questions_nr+1;
        }
        $sql->close();
    }
    private function get_questions_admin(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $ceva="SELECT id,`chapter_id`,all_answers,right_answers,`validation` FROM questions";
        $sql=$link->prepare($ceva);
        $sql->execute();
        $sql->bind_result($question_id,$chapter_id,$all_answers,$right_answers,$validation);
        $this->questions_nr=0;
        
        while($sql->fetch()){
            exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text',$question_text_aux);
            $question_text=$question_text_aux[$this->questions_nr];
            $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                <p class='text'>" . $question_text . "</p>
                                                                <p class='details'> Validation:" . $validation . "</p>
                                                        </a>";
            $this->questions_nr=$this->questions_nr+1;
        }
        $sql->close();
    }
}