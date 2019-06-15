<?php
class Choose_Chapter extends Controller
{
    private $chapters;
    private $chapters_nr;
    private const TITLE_MESSAGE="Choose a chapter to begin solving questions:";
    public function index()
    {
        $this->check_login();
        /*clearing $_SESSION[]*/
        $this->session_extract("exec_msg",true);
        $error_msg=$this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);
        $this->session_extract("args_field",true);
        $this->session_extract("input_field",true);

        $this->session_extract('question_id',true);
        $this->session_extract('question_text',true);
        $this->session_extract('user_command',true);
        $this->session_extract('user_output',true);
        $this->session_extract('author_command',true);
        $this->session_extract('author_output',true);
        $this->session_extract('result_correct',true);
        $this->session_extract('result_incorrect',true);

        $this->get_chapters();
        
        $this->view('home/choose_chapter',['title_message'=>self::TITLE_MESSAGE,'error_msg' => $error_msg,'chapters' => $this->chapters,'chapters_nr' => $this->chapters_nr]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapters";
        header('Location: '.$new_url);
        die;
    }
    private function get_chapters(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if($this->session_is_admin==true){/*admins can see unposted chapters*/
            $query="SELECT id,`name`,`description` FROM chapters";
        }else{
            $query="SELECT id,`name`,`description` FROM chapters WHERE `status`='posted'";
        }
        $sql=$link->prepare($query);
        $sql->execute();
        $sql->bind_result($chapter_id,$chapter_name,$chapter_description);
        $this->chapters_nr=0;
        
        while($sql->fetch()){
                $this->chapters[$this->chapters_nr]=   "<div class='chapter'>
                                                            <a href='chapter_" . (string)$chapter_id . "_solve'>" . $chapter_name . "</a>
                                                            <p>" . $chapter_description . "</p>
                                                        </div>";
            $this->chapters_nr=$this->chapters_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
}