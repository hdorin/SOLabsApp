<?php
//Chapter C Linux
class Chapter_31_Submit extends Controller
{
    const CHAPTER_ID=31;
    const TEXT_MAX_LEN=500;
    const CODE_MAX_LEN=1500;
    const ARGS_MAX_LEN=100;
    const INPUT_MAX_LEN=500;
    const KEYBD_MAX_LEN=500;
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
        $args_field=$this->session_extract("args_field");
        $keybd_field=$this->session_extract("keybd_field");
        $input_field=$this->session_extract("input_field");
        $chapter_name=$this->get_chapter_name(self::CHAPTER_ID);
        $this->view('home/chapter_' . (string)$chapter_id . '_submit',['chapter_id' => (string)self::CHAPTER_ID,'chapter_name'=>$chapter_name,'code_field' => $code_field, 'code_field_max_len'=>self::CODE_MAX_LEN,
                                                                       'args_field' => $args_field,'args_field_max_len'=>self::ARGS_MAX_LEN,'keybd_field' => $keybd_field,'keybd_field_max_len'=>self::KEYBD_MAX_LEN,'input_field' => $input_field,'input_field_max_len'=>self::INPUT_MAX_LEN,
                                                                       'text_field' => $text_field, 'text_field_max_len'=>self::TEXT_MAX_LEN,'error_msg' => $error_msg, 'exec_msg' => $exec_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_" . (string)self::CHAPTER_ID. "_submit";
        header('Location: '.$new_url);
        $this->my_sem_release();
        die;
    }
    private function execute($code,$args="",$keybd="",$input="",$combine_outputs=false){//the $combine_outputs argument adds output file contents in the exec_msg
        $config=$this->model('JSONConfig');
        $ssh_host=$config->get('ssh','host');
        $ssh_port=$config->get('ssh','port');
        $ssh_user=$config->get('ssh','user');
        $ssh_pass=$config->get('ssh','pass');
        $ssh_timeout_seconds=$config->get('ssh','timeout_seconds');
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
        $app_local_path=$config->get('app','local_path');
        $code_file=fopen($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.code','w');
        fwrite($code_file,$code);
        fclose($code_file);
        $run_file=fopen($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.run','w');
        fwrite($run_file,"gcc code.c -o code.out && ./code.out " . $args . " < code.keybd");
        fclose($run_file); 
        $keybd_file=fopen($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.keybd','w');
        fwrite($keybd_file,$keybd);
        fclose($keybd_file);
        $input_file=fopen($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.input','w');
        fwrite($input_file,$input);
        fclose($input_file);
        try{
            $ssh_connection->send_code_file($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.code', $this->session_user . '.c');
            $ssh_connection->send_code_file($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.run', $this->session_user . '.run');
            $ssh_connection->send_code_file($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.keybd', $this->session_user . '.keybd');
            $ssh_connection->send_code_file($app_local_path . '/mvc/app/scp_cache/' . $this->session_user . '.input', $this->session_user . '.input');
            $docker_command="docker run --name " . $this->session_user . " -v $(pwd)/" . $this->session_user . ".c:/code.c -v $(pwd)/" . 
                                                   $this->session_user . ".keybd:/code.keybd:ro -v $(pwd)/" . $this->session_user . ".input:/code.input -v $(pwd)/" . 
                                                   $this->session_user . ".output:/code.output -v $(pwd)/" . $this->session_user . ".run:/code.run:ro --rm gcc bash ./code.run";
            /*creating the output file which will be mounted in the container*/
            $ssh_connection->execute("echo>" . $this->session_user . ".output",true);
            $_SESSION["output_file"]=0;
            $_SESSION["exec_msg"]=$ssh_connection->execute("timeout --signal=SIGKILL " . $ssh_timeout_seconds . " " . $docker_command);
        }catch(Exception $e){
            $ssh_connection->close();
            $this->reload($e->getMessage());
        }
        if(empty($_SESSION["exec_msg"])==true){
            $_SESSION["output_file"]=$ssh_connection->read_file($this->session_user . ".output");//only if the standard output is empty should we read the output file
            if(empty($_SESSION["output_file"])==true || ord($_SESSION["exec_msg"][0])==10){
                $ssh_connection->close();
                $this->reload("Output cannot be empty!");
            }else{
                if($combine_outputs==true){
                    $_SESSION["exec_msg"]=$_SESSION["output_file"];
                }
            }
        }
        $_SESSION["output_file"]=$ssh_connection->read_file($this->session_user . ".output");//only if the standard output is empty should we read the output file
        $ssh_connection->close();
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
    private function submit($text,$code,$args="",$keybd="",$input=""){
        $this->execute($code,$args,$keybd,$input,true);
        $aux_output=$_SESSION["exec_msg"];
        $this->execute($code,$args,$keybd,$input,true);
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
        $sql->fetch();
        $sql->close();
        $sql=$link->prepare('UPDATE questions SET `status`=? WHERE id=?');        
        $status="posted";
        $sql->bind_param('si', $status,$question_id);
        $sql->execute();
        $sql->close();
        /*increment posted questions count for user*/
        $chapter_name_aux="chapter_".(string)$chapter_id;
        $sql=$link->prepare('SELECT posted_questions FROM ' . $chapter_name_aux . ' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($posted_questions);
        $sql->fetch();
        $sql->close();
        $sql=$link->prepare('UPDATE ' . $chapter_name_aux . ' SET `posted_questions`=? WHERE `user_id`=?');        
        $status="posted";
        $posted_questions=$posted_questions+1;
        $sql->bind_param('ii',$posted_questions,$this->session_user_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $config=$this->model('JSONConfig');
        $app_local_path=$config->get('app','local_path');
        $code_file=fopen($app_local_path . '/mvc/app/questions/' . (string)$question_id . '.code','w');
        fwrite($code_file,$code);
        fclose($code_file);
        $text_file=fopen($app_local_path . '/mvc/app/questions/' . (string)$question_id . '.text','w');
        fwrite($text_file,$text);
        fclose($text_file);
        $args_file=fopen($app_local_path . '/mvc/app/questions/' . (string)$question_id . '.args','w');
        fwrite($args_file,$args);
        fclose($args_file);
        $keybd_file=fopen($app_local_path . '/mvc/app/questions/' . (string)$question_id . '.keybd','w');
        fwrite($keybd_file,$keybd);
        fclose($keybd_file);
        $input_file=fopen($app_local_path . '/mvc/app/questions/' . (string)$question_id . '.input','w');
        fwrite($input_file,$input);
        fclose($input_file);
    }
    public function process(){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        if($this->can_submit_quesion(self::CHAPTER_ID)==false){
            die("You cannot access this!");
        }
        $this->my_sem_acquire($this->session_user_id);
        if(strlen($_POST["text_field"])>self::TEXT_MAX_LEN){
            $this->reload("Characters limit exceeded for text!");
        }
        if(strlen($_POST["code_field"])>self::CODE_MAX_LEN){
            $this->reload("Characters limit exceeded for code!");
        }
        if(strlen($_POST["args_field"])>self::ARGS_MAX_LEN){
            $this->reload("Characters limit exceeded for arguments!");
        }
        if(strstr($_POST["args_field"],"\n")==true){
            $this->reload("New line not permitted for arguments!");
        }
        if(strlen($_POST["keybd_field"])>self::KEYBD_MAX_LEN){
            $this->reload("Characters limit exceeded for input keyboard!");
        }
        if(strlen($_POST["input_field"])>self::INPUT_MAX_LEN){
            $this->reload("Characters limit exceeded for input file!");
        }
        if(empty($text=$_POST["text_field"])==true){
            $this->reload("You did not enter the question text!");
        }
        if(empty($code=$_POST["code_field"])==true){
            $this->reload("You did not enter the question code!");
        }
        $code=$_SESSION["code_field"]=$_POST["code_field"];
        $text=$_SESSION["text_field"]=$_POST["text_field"];
        $args=$_SESSION["args_field"]=$_POST["args_field"];
        $keybd=$_SESSION["keybd_field"]=$_POST["keybd_field"];
        $input=$_SESSION["input_field"]=$_POST["input_field"];
        $code=str_replace("\r","",$code);//Converting DOS line end to Linux version
        $text=str_replace("\r","",$text);//Converting DOS line end to Linux version
        $args=str_replace("\r","",$args);//Converting DOS line end to Linux version
        $keybd=str_replace("\r","",$keybd);//Converting DOS line end to Linux version
        $input=str_replace("\r","",$input);//Converting DOS line end to Linux version
        if($_POST["action"]=="Execute"){
            $this->execute($code,$args,$keybd,$input,true);
            header('Location: ../chapter_' . (string)self::CHAPTER_ID . '_submit');
        }else{
            $this->submit($text,$code,$args,$keybd,$input);
            $this->session_extract("code_field",true);
            $this->session_extract("text_field",true);
            $this->session_extract("args_field",true);
            $this->session_extract("keybd_field",true);
            $this->session_extract("input_field",true);
            header('Location: ../submit_question');
        }
        $this->my_sem_release();
    }
}