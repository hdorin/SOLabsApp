<?php
//Chapter Commands
class Chapter_1_View_Question extends Controller
{
    const CHAPTER_ID=1;
    private $answers_left=0;
    private $validation="";
    private $question_text="",$question_code="";
    private $right_answers=0,$all_answers=0;
    private $date_submitted="";
    private $reports,$reports_nr=0;
    public function index($data='test')
    {   
        if(strcmp($data,'test')!=0){
            $_SESSION["question_id"]=intval($data);
            $this->reload();
        }
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $question_id=$this->session_extract("question_id");
        if($this->can_view_quesion($question_id)==false){
            die("You cannot access this!");
        }
        $this->get_question($question_id);
        $this->get_reports($question_id);
        $error_msg=$this->session_extract("error_msg",true);
        $exec_msg=$this->session_extract("exec_msg",true);
        $code_fieold=$this->session_extract("code_field");
        $text_field=$this->session_extract("text_field");
        $can_delete=$this->check_can_delete_question($question_id);
        
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_view_question',['question_id'=>$question_id, 'can_delete' =>$can_delete,'answers_left'=>$this->answers_left,
                                                                                  'all_answers' =>$this->all_answers, 'right_answers'=>$this->right_answers,
                                                                                  'validation' =>$this->validation, 'question_text' => $this->question_text,
                                                                                  'question_code' => $this->question_code,'date_submitted'=>$this->date_submitted,
                                                                                  'reports' => $this->reports,'reports_nr' => $this->reports_nr]);
    }
    private function reload(){
        $new_url="../chapter_" . (string)self::CHAPTER_ID . "_view_question";
        header('Location: '.$new_url);
        die;
    }
    private function check_can_delete_question($question_id){
        if($this->session_is_admin==true){
            return true;
        }
        $chapter_id=self::CHAPTER_ID;
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $chapter_name_aux="chapter_".(string)self::CHAPTER_ID;
        $sql=$link->prepare("SELECT right_answers,deleted_questions FROM " . $chapter_name_aux . " WHERE `user_id`=?");
        $sql->bind_param('i',$this->session_user_id);
        $sql->execute();
        $sql->bind_result($right_answers,$deleted_questions);
        $sql->fetch();
        $sql->close();

        $sql=$link->prepare("SELECT COUNT(id) FROM questions WHERE `user_id`=? AND chapter_id=?");
        $sql->bind_param('ii',$this->session_user_id,$chapter_id);
        $sql->execute();
        $sql->bind_result($posted_questions);
        $sql->fetch();
        $sql->close();
        $db_connection->close();

        $formulas=$this->model('Formulas');
        $formulas->can_delete_question($posted_questions,$right_answers,$deleted_questions);
        $answers_left=$formulas->get_answers_left();        
        if($answers_left>=0){
            $this->answers_left=$answers_left;
            return true;
        }else{
            $this->answers_left=(-1)*$answers_left;
            return false;
        }
    }
    private function can_view_quesion($question_id){ 
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT `user_id`,chapter_id,`status` FROM questions WHERE `id`=?");
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->bind_result($user_id,$chapter_id,$status);
        if(!$sql->fetch()){//Could not find question
            $sql->close();
            $db_connection->close();
            return false;
        }
        $sql->close();
        $db_connection->close();
        if($chapter_id!=self::CHAPTER_ID){
            return false;
        }
        if($this->session_is_admin==true){
            return true;
        }
        if($this->session_user_id!=$user_id || strcmp($status,'posted')!=0){
            return false;
        }        
        return true;
    }
    private function get_question($question_id){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT all_answers, right_answers,`validation`,date_created  FROM questions WHERE `id`=?");
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->bind_result($this->all_answers,$this->right_answers,$this->validation,$this->date_submitted);
        if(!$sql->fetch()){//Could not find question
            $sql->close();
            $db_connection->close();
            die("Error getting question details!");
        }
        $sql->close();
        $db_connection->close();
        exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.code',$question_code_aux);
        $this->question_code=$question_code_aux[0];
        exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text',$question_text_aux);
        $this->question_text=$question_text_aux[0];
        return true;
    }
    private function get_reports($question_id){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT `text`,date_created FROM reports WHERE `question_id`=?');
        $sql->bind_param('i', $question_id);
        $sql->execute();
        $sql->bind_result($report_text,$date_submitted);
        $this->reports_nr=0;
        while($sql->fetch()){
            $this->reports[$this->reports_nr]=   "<div class='report'>
                                                                <p class='text'>" . $report_text . "</p>
                                                                <p class='details'> Date submitted: " . $date_submitted . "</p>
                                                        </div>";
            $this->reports_nr=$this->reports_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
    public function delete_question($question_id){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->my_sem_acquire($this->session_user_id);
        if($this->can_view_quesion($question_id)==false || $this->check_can_delete_question($question_id)==false){
            die("You cannot do that!");
        }

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        /*mark question as deleted*/
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        /*$sql=$link->prepare('SELECT `status` FROM questions WHERE `id`=?');
        $sql->bind_param('i', $question_id);
        $sql->execute();
        $sql->bind_result($question_status);
        $sql->fetch();
        $sql->close();
        if(strcmp($question_status,'deleted')==0){
            die("Question has already been deleted!");
        }
        $sql=$link->prepare("UPDATE questions SET `status`='deleted' WHERE `id`=?");        
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->close();*/
        /*increment deleted_questions*/
        $chapter_id=self::CHAPTER_ID;
        $sql=$link->prepare('SELECT deleted_questions FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($deleted_questions);
        $sql->fetch();
        $sql->close();
        $deleted_questions=$deleted_questions+1;
        $sql=$link->prepare("UPDATE chapter_" . (string)$chapter_id . " SET deleted_questions=? WHERE `user_id`=?");        
        $sql->bind_param('ii',$deleted_questions,$this->session_user_id);
        $sql->execute();
        $sql->close();
        die("AICI!" . $deleted_questions . $chapter_id);
        $db_connection->close();
        $this->my_sem_release();
    } 
}