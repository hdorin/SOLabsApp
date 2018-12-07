<?php
//Chapter Commands
class Chapter_1_View_Question extends Controller
{
    const CHAPTER_ID=1;
    private $answers_left=0;
    public function index($data='test')
    {   
        if(strcmp($data,'test')!=0){
            $_SESSION["question_id"]=intval($data);
            $this->reload();
        }
        $this->check_login();
        $question_id=$this->session_extract("question_id");
        if($this->can_view_quesion($question_id)==false){
            die("You cannot access this!");
        }
        $error_msg=$this->session_extract("error_msg",true);
        $exec_msg=$this->session_extract("exec_msg",true);
        $code_field=$this->session_extract("code_field");
        $text_field=$this->session_extract("text_field");
        $this->get_question($question_id);
        $can_delete=$this->check_can_delete_question($question_id);
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_view_question',['can_delete' =>$can_delete,'answers_left'=>$this->answers_left,'question_id'=>(string)$question_id]);
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
        $sql=$link->prepare("SELECT `user_id`,chapter_id FROM questions WHERE `id`=? AND `status`='posted'");
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->bind_result($user_id,$chapter_id);
        if(!$sql->fetch()){//The question doesn't belong to the curent user
            $sql->close();
            $db_connection->close();
            return false;
        }
        $sql->close();
        $db_connection->close();
        
        if($chapter_id!=self::CHAPTER_ID){
            return false;
        }
        if($this->session_is_admin==false && $this->session_user_id!=$user_id){
            return false;
        }
        return true;
    }
    private function get_question($question_id){
       

    }
    public function delete_question($question_id){
        $this->check_login();
        $this->my_sem_acquire($this->session_user_id);
        if($this->can_view_quesion($question_id)==false){
            die("You cannot do that!");
        }
        //check if can delete
        die("AJUNS" . (string)$question_id);
        //scazut din nr total de intrebari rezolvate
        $this->my_sem_release();
    } 
}