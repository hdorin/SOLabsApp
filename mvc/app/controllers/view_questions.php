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
        $is_posted=" ";
        $search_user=" ";
        
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if($this->session_is_admin==false){
            $is_posted="AND q.`status`='posted'";
            $search_user="AND q.`user_id`=" . (string)$this->session_user_id;
        }
            $qurery="SELECT q.id,q.`chapter_id`,q.all_answers,q.right_answers,q.`validation`,c.name,q.date_created FROM questions q JOIN chapters c ON q.chapter_id=c.id WHERE 1=1 " . $search_user .  " " . $is_posted . " ";    
        
        $sql=$link->prepare($qurery);
        /*if($this->session_is_admin==false){
            $sql->bind_param('i', $this->session_user_id);
        }*/
        $sql->execute();
        $sql->bind_result($question_id,$chapter_id,$all_answers,$right_answers,$validation,$chapter_name, $date_submitted);
        $this->questions_nr=0;
        
        while($sql->fetch()){
            exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text',$question_text_aux);
            $question_text=$question_text_aux[$this->questions_nr];
            if($this->session_is_admin==false){
                $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                    <p class='text'>" . $question_text . "</p>
                                                                    <p class='details'> Times Answered: " . $right_answers . " / " .  $all_answers . "</p>
                                                                    <p class='details'> Validation: " . $validation . "</p>
                                                                    <p class='details'> Chapter: " . $chapter_name . "</p>
                                                            </a>";
                
            }else{
                $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                    <p class='text'>" . $question_text . "</p>
                                                                    <p class='details'> Times Answered: " . $right_answers . " / " .  $all_answers . "</p>
                                                                    <p class='details'> Validation: " . $validation . "</p>
                                                                    <p class='details'> Chapter: " . $chapter_name . "</p>
                                                                    <p class='details'> User: " . $this->session_user . "</p>
                                                                    <p class='details'> Date Submitted: " . $date_submitted . "</p>
                                                            </a>";
            }
            $this->questions_nr=$this->questions_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
}