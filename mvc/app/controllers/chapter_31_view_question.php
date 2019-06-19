<?php
//Chapter C Linux
class Chapter_31_View_Question extends Controller
{
    const CHAPTER_ID=31;
    const TEXT_MAX_LEN=500;
    const CODE_MAX_LEN=1500;
    const ARGS_MAX_LEN=100;
    const KEYBD_MAX_LEN=500;
    const INPUT_MAX_LEN=500;
    private $answers_left=0;
    private $status="",$validation="";
    private $question_text="",$question_code="",$question_args="",$question_keybd="",$question_input="";
    private $right_answers=0,$all_answers=0;
    private $date_submitted="";
    private $reports,$reports_nr=0;
    public function index($data='test')
    {   
        $this->check_login();
        if(strcmp($data,'test')!=0){
            $_SESSION["question_id"]=intval($data);
            $this->reload();
        }
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
        if($this->status=='deleted'){
            $can_delete=false;
        }else{
            $can_delete=$this->check_can_delete_question($question_id);
        }
        $chapter_name=$this->get_chapter_name(self::CHAPTER_ID);
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_view_question',['chapter_id' => (string)self::CHAPTER_ID,'chapter_name'=>$chapter_name,'question_id'=>$question_id, 'can_delete' =>$can_delete,'answers_left'=>$this->answers_left,
                                                                                  'all_answers' =>$this->all_answers, 'right_answers'=>$this->right_answers,'validation' =>$this->validation, 'question_text' => $this->question_text,'question_code' => $this->question_code,
                                                                                  'question_args' => $this->question_args,'question_keybd' => $this->question_keybd,'question_input' => $this->question_input,'date_submitted'=>$this->date_submitted,
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
        $sql=$link->prepare("SELECT right_answers,posted_questions,deleted_questions,code_reveals FROM " . $chapter_name_aux . " WHERE `user_id`=?");
        $sql->bind_param('i',$this->session_user_id);
        $sql->execute();
        $sql->bind_result($right_answers,$posted_questions,$deleted_questions,$code_reveals);
        $sql->fetch();
        $sql->close();
        $formulas=$this->model('Formulas');
        $formulas->can_delete_question($right_answers,$posted_questions,$deleted_questions,$code_reveals);
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
        $sql=$link->prepare("SELECT all_answers, right_answers,`status`,`validation`,date_created  FROM questions WHERE `id`=?");
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->bind_result($this->all_answers,$this->right_answers,$this->status,$this->validation,$this->date_submitted);
        if(!$sql->fetch()){//Could not find question
            $sql->close();
            $db_connection->close();
            die("Error getting question details!");
        }
        $sql->close();
        $db_connection->close();

        $config=$this->model('JSONConfig');
        $app_local_path=$config->get('app','local_path');
        $code_file=fopen($app_local_path . '/mvc/app/questions/' .  (string)$question_id . '.code','r');
        $this->question_code=fread($code_file,self::CODE_MAX_LEN);
        fclose($code_file);
        $text_file=fopen($app_local_path . '/mvc/app/questions/' .  (string)$question_id . '.text','r');
        $this->question_text=fread($text_file,self::TEXT_MAX_LEN);
        fclose($text_file);
        $args_file=fopen($app_local_path . '/mvc/app/questions/' .  (string)$question_id . '.args','r');
        $this->question_args=fread($args_file,self::ARGS_MAX_LEN);
        fclose($args_file);
        $keybd_file=fopen($app_local_path . '/mvc/app/questions/' .  (string)$question_id . '.keybd','r');
        $this->question_keybd=fread($keybd_file,self::KEYBD_MAX_LEN);
        fclose($keybd_file);
        $input_file=fopen($app_local_path . '/mvc/app/questions/' .  (string)$question_id . '.input','r');
        $this->question_input=fread($input_file,self::INPUT_MAX_LEN);
        fclose($input_file);
        
        $this->question_text=$this->replace_html_special_characters($this->question_text);
        $this->question_code=$this->replace_html_special_characters($this->question_code);
        $this->question_input=$this->replace_html_special_characters($this->question_input);
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
        $sql=$link->prepare('SELECT u.user_name,r.`text`,r.date_created FROM reports r JOIN users u ON r.`user_id`=u.id WHERE `question_id`=?');
        $sql->bind_param('i', $question_id);
        $sql->execute();
        $sql->bind_result($user_name,$report_text,$date_submitted);
        $this->reports_nr=0;
        while($sql->fetch()){
            if($this->session_is_admin==true){
                $this->reports[$this->reports_nr]=   "<div class='report'>
                                                                    <p class='text'>" . $report_text . "</p>
                                                                    <p class='details'> Date submitted: " . $date_submitted . "</p>
                                                                    <p class='details'> User name: " . $user_name . "</p>
                                                            </div>";
            }else{
                $this->reports[$this->reports_nr]=   "<div class='report'>
                                                                    <p class='text'>" . $report_text . "</p>
                                                                    <p class='details'> Date submitted: " . $date_submitted . "</p>
                                                                  
                                                            </div>";
            }
            $this->reports_nr=$this->reports_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
    public function restore_question($question_id){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        if($this->can_view_quesion($question_id)==false){
            die("You cannot do that!");
        }
        $this->my_sem_acquire($this->session_user_id);
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        /*mark question as deleted*/
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT `user_id`,`status` FROM questions WHERE `id`=?');
        $sql->bind_param('i', $question_id);
        $sql->execute();
        $sql->bind_result($user_id,$question_status);
        $sql->fetch();
        $sql->close();
        if(strcmp($question_status,'deleted')!=0){
            die("Question is not deleted!");
        }
        $sql=$link->prepare("UPDATE questions SET `status`='posted' WHERE `id`=?");        
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->close();
        /*decrement deleted_questions*/
        $chapter_id=self::CHAPTER_ID;
        $sql=$link->prepare('SELECT posted_questions,deleted_questions FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $user_id);
        $sql->execute();
        $sql->bind_result($posted_questions,$deleted_questions);
        $sql->fetch();
        $sql->close();
        $posted_questions=$posted_questions+1;
        $deleted_questions=$deleted_questions-1;
        $sql=$link->prepare("UPDATE chapter_" . (string)$chapter_id . " SET posted_questions=?,deleted_questions=? WHERE `user_id`=?");        
        $sql->bind_param('iii',$posted_questions,$deleted_questions,$user_id);
        $sql->execute();
        $sql->close();
        //die("AICI!" . $deleted_questions . $chapter_id);
        $db_connection->close();
        $this->my_sem_release();
        $new_url="../" . $question_id;
        header('Location: '.$new_url);
        die;
    }
    public function delete_question($question_id){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        if($this->can_view_quesion($question_id)==false){
            die("You cannot do that!");
        }
        $this->my_sem_acquire($this->session_user_id);
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        /*mark question as deleted*/
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT `user_id`,`status` FROM questions WHERE `id`=?');
        $sql->bind_param('i', $question_id);
        $sql->execute();
        $sql->bind_result($user_id,$question_status);
        $sql->fetch();
        $sql->close();
        if(strcmp($question_status,'deleted')==0){
            die("Question had already been deleted deleted!");
        }
        $sql=$link->prepare("UPDATE questions SET `status`='deleted' WHERE `id`=?");        
        $sql->bind_param('i',$question_id);
        $sql->execute();
        $sql->close();
        /*decrement deleted_questions*/
        $chapter_id=self::CHAPTER_ID;
        $sql=$link->prepare('SELECT posted_questions,deleted_questions FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $user_id);
        $sql->execute();
        $sql->bind_result($posted_questions,$deleted_questions);
        $sql->fetch();
        $sql->close();
        $posted_questions=$posted_questions-1;
        $deleted_questions=$deleted_questions+1;
        $sql=$link->prepare("UPDATE chapter_" . (string)$chapter_id . " SET posted_questions=?,deleted_questions=? WHERE `user_id`=?");        
        $sql->bind_param('iii',$posted_questions,$deleted_questions,$user_id);
        $sql->execute();
        $sql->close();
        //die("AICI!" . $deleted_questions . $chapter_id);
        $db_connection->close();
        $this->my_sem_release();
        $new_url="../../view_questions";
        header('Location: '.$new_url);
        die;        /*redirect user to view questions page*/
    }
    public function validate_question($question_id){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }
        $this->my_sem_acquire($this->session_user_id);
        if(strcmp($_POST["validation_field"],"Unvalidated")==0){
            $validation="Unvalidated";
        }else if(strcmp($_POST["validation_field"],"Valid")==0){
            $validation="Valid";
        }else if(strcmp($_POST["validation_field"],"Invalid")==0){
            $validation="Invalid";
        }else{
            die("Invalid validation specified!");
        }

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("UPDATE questions SET `validation`=? WHERE `id`=?");        
        $sql->bind_param('si',$validation,$question_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();
        /*redirect user to view questions page*/
        $new_url="../" . $question_id;
        header('Location: '.$new_url);
        die;
    }
}