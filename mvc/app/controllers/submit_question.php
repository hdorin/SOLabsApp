<?php
class Submit_Question extends Controller
{
    private $chapters;
    private $chapters_nr;
    private $answers_left;
    public function index()
    {
        $this->check_login();
        $this->session_extract("exec_msg",true);
        $error_msg=$this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);

        $this->get_chapters();
        $this->view('home/chapters',['error_msg' => $error_msg,'chapters' => $this->chapters,'chapters_nr' => $this->chapters_nr]);
    }
    private function can_submit_quesion($chapter_id){
        if($this->session_is_admin==true){
            return true;
        }
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $chapter_name_aux="chapter_".(string)$chapter_id;
        $sql=$link->prepare("SELECT right_answers FROM " . $chapter_name_aux . " WHERE `user_id`=?");
        $sql->bind_param('i',$this->session_user_id);
        $sql->execute();
        
        $sql->bind_result($right_answers);
        $sql->fetch();
        $sql->close();

        $sql=$link->prepare("SELECT COUNT(id) FROM questions WHERE `user_id`=? AND chapter_id=?");
        $sql->bind_param('ii',$this->session_user_id,$chapter_id);
        $sql->execute();
        $sql->bind_result($all_questions);
        $sql->fetch();
        $sql->close();
        
        $formulas=$this->model('Formulas');
        $auxx=$formulas->can_submit_question_formula($all_questions,$right_answers);
        $answers_left=$formulas->get_answers_left();        

        if($answers_left>=0){
            $this->answers_left=$answers_left;
            return true;
        }else{
            $this->answers_left=(-1)*$answers_left;
            return false;
        }
    }
    private function get_chapters(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT id,`name` FROM chapters WHERE `status`='posted'");
        $sql->execute();
        $sql->bind_result($chapter_id,$chapter_name);
        $this->chapters_nr=0;
        
        while($sql->fetch()){
            if($this->session_is_admin==true){
                $this->chapters[$this->chapters_nr]=   "<div class='chapter'>
                                                        <a href='chapter_" . (string)$chapter_id . "_submit'>" . $chapter_name . "</a>
                                                        <p>No need to answer questions</p>
                                                        </div>";
            }else if($this->can_submit_quesion($chapter_id)){  
                $this->chapters[$this->chapters_nr]=   "<div class='chapter'>
                                                            <a href='chapter_" . (string)$chapter_id . "_submit'>" . $chapter_name . "</a>
                                                            <p>Answers extra: " . $this->answers_left . "</p>
                                                        </div>";
            }else{
                $this->chapters[$this->chapters_nr]=   "<div class='chapter'>
                                                            <a>" . $chapter_name . "</a>
                                                            <p>Answers left: " . $this->answers_left . "</p>
                                                        </div>";
            }
            $this->chapters_nr=$this->chapters_nr+1;
        }
        $sql->close();
    }
}