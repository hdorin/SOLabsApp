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

        
        $this->get_questions();
        $this->view('home/questions',['questions' => $this->questions,'questions_nr' => $this->questions_nr]);
    }
    
    private function get_questions(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if($this->session_is_admin==true){
            $qurery="SELECT id,`chapter_id`,all_answers,right_answers,`validation`,date_created FROM questions";    
        }else{
            $qurery="SELECT id,`chapter_id`,all_answers,right_answers,`validation`,date_created FROM questions WHERE `user_id`=?";    
        }
        $sql=$link->prepare($qurery);
        if($this->session_is_admin==false){
            $sql->bind_param('i', $this->session_user_id);
        }
        $sql->execute();
        $sql->bind_result($question_id,$chapter_id,$all_answers,$right_answers,$validation,$date_created);
        $this->questions_nr=0;
        
        while($sql->fetch()){
            exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text',$question_text_aux);
            $question_text=$question_text_aux[$this->questions_nr];
            $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                <p class='text'>" . $question_text . "</p>
                                                                <p class='details'> Times Answered: " . $right_answers . " / " .  $all_answers . "</p>
                                                                <p class='details'> Validation: " . $validation . "</p>
                                                                <p class='details'> Date submitted: " . $date_created . "</p>
                                                        </a>";
            $this->questions_nr=$this->questions_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
}