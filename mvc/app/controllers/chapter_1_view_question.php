<?php
//Chapter Commands
class Chapter_1_View_Question extends Controller
{
    const CHAPTER_ID=1;
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
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_view_question',[]);
    }
    private function reload(){
        $new_url="../chapter_" . (string)self::CHAPTER_ID . "_view_question";
        header('Location: '.$new_url);
        die;
    }
    private function can_view_quesion($question_id){
        
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT `user_id`,chapter_id FROM questions WHERE `id`=? AND `status`='posted'");
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->bind_result($user_id,$chapter_id);
        $sql->fetch();
        $sql->close();
        
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
}