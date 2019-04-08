<?php
//Chapter Commands
class Chapter_1_Solve extends Controller
{
    private $question_text;
    private $get_question_i0nput;
    const CHAPTER_ID=1;
    const CODE_MAX_LEN=150;
    public function index()
    {   
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->get_question();
        $error_msg=$this->session_extract("error_msg",true);
        $exec_msg=$this->session_extract("exec_msg",true);
        $code_field=$this->session_extract("code_field");
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_solve',['chapter_id' => (string)self::CHAPTER_ID, 'question_text' => $this->question_text, 'code_field' =>$code_field, 'code_field_max_len' =>self::CODE_MAX_LEN,'error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_" . (string)self::CHAPTER_ID . "_solve";
        header('Location: '.$new_url);
        $this->my_sem_release();
        die;
    }
    private function next_question(){
        $chapter_id=self::CHAPTER_ID;
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT last_question_id FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($last_question_id);
        $status=$sql->fetch();
        $sql->close();
        $sql=$link->prepare('SELECT COUNT(id) FROM questions WHERE chapter_id=? AND `status`="posted" AND `validation`!="invalid" AND id != ? AND `user_id`!=?');
        $sql->bind_param('iii',$chapter_id,$last_question_id,$this->session_user_id);
        $sql->execute();
        $sql->bind_result($questions_nr);
        $sql->fetch();
        $sql->close();

        if($questions_nr<1){
            die("Could not find a suitable question!");
        }
        
        $sql=$link->prepare('SELECT id FROM questions WHERE chapter_id=? AND `status`="posted" AND `validation`!="invalid" AND id != ? AND `user_id`!=?');
        $sql->bind_param('iii',$chapter_id,$last_question_id,$this->session_user_id);
        $sql->execute();
        $sql->bind_result($question_id);    
        for($i=1;$i<=rand(1,$questions_nr);$i++){
            $sql->fetch();
        }
        $sql->close();
        
        $sql=$link->prepare('UPDATE chapter_' . (string)$chapter_id . ' SET last_question_id=? WHERE `user_id`=?');        
        $sql->bind_param('ii',$question_id,$this->session_user_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
    }
  
    private function get_question(){
        $chapter_id=self::CHAPTER_ID;
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT last_question_id FROM chapter_'. (string)$chapter_id .' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($last_question_id);
        $status=$sql->fetch();
        $sql->close();
        
        if(!$status){/*insert user into chapter_1 table*/
            $sql=$link->prepare('SELECT id FROM questions WHERE chapter_id=? AND `status`="posted" AND `validation`!="invalid"');
            $sql->bind_param('i',$chapter_id);
            $sql->execute();
            $sql->bind_result($last_question_id);
            $sql->fetch();
            $sql->close();
            $sql=$link->prepare('INSERT INTO chapter_' . (string)$chapter_id . ' (`user_id`,right_answers,last_question_id) VALUES (?,?,?)');
            $right_answers=0;
            $sql->bind_param('sii', $this->session_user_id,$right_answers,$last_question_id);
            $sql->execute();
            $sql->close();
        }/*increment right_answers for user*/
        
        /*check if question is still available*/
        $sql=$link->prepare('SELECT user_id FROM questions WHERE chapter_id=? AND `status`="posted" AND id=? AND `validation`!="invalid"');
        $sql->bind_param('ii',$chapter_id, $last_question_id);
        $sql->execute();
        $sql->bind_result($aux_res);
        if(!$sql->fetch()){/*in case the question is not available*/
            $this->next_question();
            $sql_1=$link->prepare('SELECT id FROM questions WHERE chapter_id=? AND `status`="posted" AND `validation`!="invalid"');
            $sql->bind_param('i',$chapter_id);
            $sql_1->execute();
            $sql_1->bind_result($last_question_id);
            $sql_1->fetch();
            $sql_1->close();
        }
        $sql->close();
        $db_connection->close();
        $config=$this->model('JSONConfig');
        $app_local_path=$config->get('app','local_path');
        exec("cat " . $app_local_path . "/mvc/app/questions/" . (string)$last_question_id . ".text",$question_text_aux);
        $this->question_text=$question_text_aux[0];
    }
    private function correct_answer(){ /*add question_id*/
        $chapter_id=self::CHAPTER_ID;
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT right_answers FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($right_answers);
        $sql->fetch();
        $sql->close();
        /*increment right_answers for user*/
        $sql=$link->prepare('UPDATE chapter_' . (string)$chapter_id . ' SET right_answers=? WHERE `user_id`=?');        
        $right_answers=$right_answers+1;
        $sql->bind_param('ii',$right_answers,$this->session_user_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
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
        $ssh_connection->close();
    }
    private function submit($command,$skip=false){
        $chapter_id=self::CHAPTER_ID;
        if($skip==false){
            $this->execute($command);
        }
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        /*check if user is in the chapter_1 users list*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT last_question_id FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($last_question_id);
        $status=$sql->fetch();
        $sql->close();
        $sql=$link->prepare('SELECT all_answers,right_answers FROM questions WHERE `id`=?');
        $sql->bind_param('i', $last_question_id);
        $sql->execute();
        $sql->bind_result($all_answers,$right_answers);
        $sql->fetch();
        $sql->close();
        if($skip==false){
            $config=$this->model('JSONConfig');
            $app_local_path=$config->get('app','local_path');
            exec('cat ' . $app_local_path . '/mvc/app/questions/' . (string)$last_question_id . '.code',$question_code_aux);
            $question_code=$question_code_aux[0];
            $this->execute($question_code);
            $aux_output=$_SESSION["exec_msg"];
            $this->execute($command);
            if(strcmp($aux_output,$_SESSION["exec_msg"])==0 || strcmp($question_code,$command)==0){
                $this->correct_answer();
                $right_answers=$right_answers+1;
                $_SESSION['result_correct']="You answerd correctly!";
            }else{
                $_SESSION['result_incorrect']="You answerd incorrectly!";
            }
        }
        /*increment answers for question*/
        $sql=$link->prepare('UPDATE questions SET all_answers=?,right_answers=? WHERE `id`=?');        
        $all_answers=$all_answers+1;
        $sql->bind_param('iii',$all_answers,$right_answers,$last_question_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        
        if($skip==false){/*prepare info for result*/
            $this->get_question();
            $_SESSION['question_id']=$last_question_id;
            $_SESSION['question_text']=$this->question_text;
            $_SESSION['user_command']=$command;
            $_SESSION['user_output']=$_SESSION["exec_msg"];
            $_SESSION['author_command']=$question_code;
            $_SESSION['author_output']=$aux_output;
        }
        
        $this->next_question();  
    }
    public function process(){
        $chapter_id=self::CHAPTER_ID;
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->my_sem_acquire($this->session_user_id);
        if(strlen($_POST["code_field"])>self::CODE_MAX_LEN){
            $this->reload("Characters limit exceeded!");
        }
        if($_POST["action"]!="Skip" && empty($command=$_POST["code_field"])==true){
            $this->reload("You did not enter a command!");
        }
        $_SESSION["code_field"]=$_POST["code_field"];
        if($_POST["action"]=="Execute"){
            $this->execute($command);
            header('Location: ../chapter_' . (string)$chapter_id . '_solve'); 
        }else if($_POST["action"]=="Submit"){
            $this->submit($command);
            $this->session_extract("code_field",true);
            $this->session_extract("text_field",true);
            $this->session_extract("error_msg",true);
            $this->session_extract("exec_msg",true);
            header('Location: ../chapter_' . (string)$chapter_id . '_result');       
        }else{
            $this->submit("",true);
            $this->session_extract("code_field",true);
            $this->session_extract("text_field",true);
            $this->session_extract("error_msg",true);
            $this->session_extract("exec_msg",true);  
            header('Location: ../chapter_' . (string)$chapter_id . '_solve');  
        }
        $this->my_sem_release();
        
    }
}