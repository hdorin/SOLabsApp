<?php
class Submit_Question extends Controller
{
    private $chapters;
    private $chapters_nr;
    private $answers_left;
    private const TITLE_MESSAGE="Choose a chapter to submit a question";
    public function index()
    {
        $this->check_login();
        $this->session_extract("exec_msg",true);
        $error_msg=$this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);
        $this->session_extract("args_field",true);
        $this->session_extract("input_field",true);
        $this->get_chapters();
        $this->view('home/choose_chapter',['title_message'=>self::TITLE_MESSAGE,'error_msg' => $error_msg,'chapters' => $this->chapters,'chapters_nr' => $this->chapters_nr]);
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
        $sql=$link->prepare("SELECT right_answers,posted_questions,deleted_questions,code_reveals FROM " . $chapter_name_aux . " WHERE `user_id`=?");
        $sql->bind_param('i',$this->session_user_id);
        $sql->execute();
        $sql->bind_result($right_answers,$posted_questions,$deleted_questions,$code_reveals);
        $sql->fetch();
        $sql->close();
        /*formula to calculate questions to answer left until can submit question for a chapter*/
        $formulas=$this->model('Formulas');
        $auxx=$formulas->can_submit_question($right_answers,$posted_questions,$deleted_questions,$code_reveals);
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
        if($this->session_is_admin==true){/*admins can see unposted chapters*/
            $query="SELECT id,`name` FROM chapters ORDER BY id";
        }else{
            $query="SELECT id,`name` FROM chapters WHERE `status`='posted' ORDER BY id";
        }
        $sql=$link->prepare($query);
        $sql->execute();
        $sql->bind_result($chapter_id,$chapter_name);
        $this->chapters_nr=0;
        $chapter_id_aux=0;
        while($sql->fetch()){
            if(floor($chapter_id/10)>floor($chapter_id_aux/10)){
                if($chapter_id_aux==0){
                    $this->chapters[$this->chapters_nr] = "<div class='chapter'>
                                                           ";         
                }else{
                    $this->chapters[$this->chapters_nr] = "</div>
                                                        <div class='chapter'>
                                                        ";         
                }
                $chapter_id_aux=$chapter_id;
            }else{
                $this->chapters[$this->chapters_nr] =  "<br>                                  
                                                        ";
            }
            if($this->session_is_admin==true){
                $this->chapters[$this->chapters_nr] = $this->chapters[$this->chapters_nr] .   "
                                                        <a href='chapter_" . (string)$chapter_id . "_submit'>" . $chapter_name . "</a>
                                                        <p>No need to answer questions</p>
                                                        ";
            }else if($this->can_submit_quesion($chapter_id)){                         
                $this->chapters[$this->chapters_nr]=  $this->chapters[$this->chapters_nr] . "<a href='chapter_" . (string)$chapter_id . "_submit'>" . $chapter_name . "</a>
                                                        <p>Extra right answers: " . $this->answers_left . "</p>
                                                        ";
            }else{
                $this->chapters[$this->chapters_nr]=  $this->chapters[$this->chapters_nr] ."<a>" . $chapter_name . "</a>
                                                        <p>Additional right answers required: " . $this->answers_left . "</p>
                                                        ";
            }
            $this->chapters_nr=$this->chapters_nr+1;
        }
        $this->chapters[$this->chapters_nr] = "</div>";
        $sql->close();
        $db_connection->close();        
    }
}