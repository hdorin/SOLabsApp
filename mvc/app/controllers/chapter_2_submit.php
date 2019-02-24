<?php
//Chapter Commands
class Chapter_2_Submit extends Controller
{
    const CHAPTER_ID=2;
    const TEXT_MAX_LEN=1500;
    const CODE_MAX_LEN=150;
    public function index()
    {
        $chapter_id=self::CHAPTER_ID;
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        if($this->can_submit_quesion($chapter_id)==false){
            die("You cannot access this!");
        }
        $error_msg=$this->session_extract("error_msg",true);
        $exec_msg=$this->session_extract("exec_msg",true);
        $code_field=$this->session_extract("code_field");
        $text_field=$this->session_extract("text_field");
        $this->view('home/chapter_' . (string)$chapter_id . '_submit',['code_field' => $code_field, 'text_field' => $text_field,'error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_" . (string)self::CHAPTER_ID. "_submit";
        header('Location: '.$new_url);
        $this->my_sem_release();
        die;
    }
    private function execute($command){
        $config=$this->model('JSONConfig');
        $ssh_host=$config->get('ssh','host');
        $ssh_port=$config->get('ssh','port');
        $ssh_timeout_seconds=$config->get('ssh','timeout_seconds');
        $ssh_user=$this->session_user;
        $ssh_pass=$this->session_pass;
        $ssh_connection=$this->model('SSHConnection');
        $ssh_connection->configure($ssh_host,$ssh_port);
        try{
            if(!$ssh_connection->connect($ssh_user,$ssh_pass)){
                $ssh_connection->close();
                $this->reload("Could not access Linux machine!");
            }
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        try{    
            $_SESSION["exec_msg"]=$ssh_connection->execute($command,$ssh_timeout_seconds);
        }catch(Exception $e){
            if(empty($e->getMessage())==true){
                $this->reload("Output cannot be empty!");
            }
            $this->reload($e->getMessage());
        }
        
        $ssh_connection->close();execexec
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
        $sql=$link->prepare("SELECT right_answers,deleted_questions FROM " . $chapter_name_aux . " WHERE `user_id`=?");
        $sql->bind_param('i',$this->session_user_id);
        $sql->execute();
        $sql->bind_result($right_answersexecexec,$deleted_questions);
        $sql->fetch();
        $sql->close();

        $sql=$link->prepare("SELECT COUNT(id) FROM questions WHERE `user_id`=? AND chapter_id=?");
        $sql->bind_param('ii',$this->session_user_id,$chapter_id);
        $sql->execute();
        $sql->bind_result($posted_questions);
        $sql->fetch();
        $sql->close();

        /*formula to calculate questions to answer left until can submit question for a chapter*/
        $formulas=$this->model('Formulas');
        $auxx=$formulas->can_submit_question($posted_questions,$right_answers,$deleted_questions);
        $answers_left=$formulas->get_answers_left();        

        if($answers_left>=0){
            $this->answers_left=$answers_left;
            return true;
        }else{
            $this->answers_left=(-1)*$answers_left;
            return false;
        }
        
    
    }
    private function submit($text,$command){
        $this->execute($command);
        $aux_output=$_SESSION["exec_msg"];
        $this->execute($command);
        if(strcmp($aux_output,$_SESSION["exec_msg"])!=0){
            $exec_msg=$this->session_extract("exec_msg",true);
            $this->reload("Code is not deterministic!");
        }
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('INSERT INTO questions (`user_id`,chapter_id,`status`,date_created) VALUES (?,?,?,now())');
        $chapter_id=self::CHAPTER_ID;
        $status="pending";
        $sql->bind_param('iis', $this->session_user_id,$chapter_id,$status);
        $sql->execute();
        $sql->close();
        $sql=$link->prepare('SELECT id FROM questions WHERE `user_id`=? AND chapter_id=? AND `status`=?');
        $sql->bind_param('iis', $this->session_user_id,$chapter_id,$status);
        $sql->execute();
        $sql->bind_result($question_id);
        $sql->fetch();exec
        $sql->close();
        $sql=$link->prepare('UPDATE questions SET `status`=? WHERE id=?');        
        $status="posted";
        $sql->bind_param('si', $status,$question_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        
        $code_file=fopen('/var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.code');
        fwrite($code_file,$command);
        fclose($code_file);
        $text_file=fopen('/var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text');
        fwrite($text_file,$text);
        fclose($text_file);
        
    }
    public function process(){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        if($this->can_submit_quesion($chapter_id)==false){
            die("You cannot access this!");
        }
        $this->my_sem_acquire($this->session_user_id);
        if(strlen($_POST["text_field"])>self::TEXT_MAX_LEN || strlen($_POST["code_field"])>self::CODE_MAX_LEN){
            $this->reload("Characters limit exceeded!");
        }
        if(empty($text=$_POST["text_field"])==true){
            $this->reload("You did not enter the question text!");
        }
        if(empty($command=$_POST["code_field"])==true){
            $this->reload("You did not enter a command!");
        }
        $_SESSION["code_field"]=$_POST["code_field"];
        $_SESSION["text_field"]=$_POST["text_field"];
        if($_POST["action"]=="Execute"){
            $this->execute($command);
            header('Location: ../chapter_' . (string)self::CHAPTER_ID . '_submit');
        }else{

            $this->submit($text,$command);
            $this->session_extract("code_field",true);
            $this->session_extract("text_field",true);
            header('Location: ../submit_question');
        }
        $this->my_sem_release();
    }
}