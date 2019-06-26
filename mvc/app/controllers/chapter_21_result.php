<?php
//Chapter Scripts
class Chapter_21_Result extends Controller
{
    const CHAPTER_ID=21;
    const REPORT_MAX_LEN=100;
    private $question_id;
    private $answers_left;
    public function index()
    {
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->question_id=$this->session_extract('question_id');
        if(empty($this->question_id)){
            die("You can't access this!");
        }
        $question_text=$this->session_extract('question_text');
        $question_text=$this->replace_html_special_characters($question_text);
        $question_args=$this->session_extract('question_args');
        $question_input=$this->session_extract('question_input');
        $question_keybd=$this->session_extract('question_keybd');
        $user_code=$this->session_extract('user_code');
        $user_code=$this->replace_html_special_characters($user_code);
        $user_output=$this->session_extract('user_output');
        $author_code=$this->session_extract('author_code');
        $author_code=$this->replace_html_special_characters($author_code);
        $author_output=$this->session_extract('author_output');
        $result_correct=$this->session_extract('result_correct');
        $result_incorrect=$this->session_extract('result_incorrect');
        $error_msg=$this->session_extract("error_msg",true);
        $chapter_name=$this->get_chapter_name(self::CHAPTER_ID);
        $reveal="";
        if(empty($result_correct)) {
            $reveal=$this->build_reveal();
            $author_code="";
        }
        $this->view('home/chapter_' . (string)self::CHAPTER_ID . '_result',['chapter_id' => (string)self::CHAPTER_ID,'chapter_name'=>$chapter_name,'error_msg' => $error_msg,'reveal' => $reveal,
                                                                            'result_correct' => $result_correct,'result_incorrect' => $result_incorrect,'question_text' => $question_text, 'question_args' => $question_args,'question_input' => $question_input,
                                                                            'question_keybd' => $question_keybd,'user_code' => $user_code,'user_output' => $user_output, 'author_code' => $author_code, 'author_output' => $author_output]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapter_" . (string)self::CHAPTER_ID . "_result";
        header('Location: '.$new_url);
        $this->my_sem_release();
        die;
    }
    private function report($message){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        /*Only one report per question is allowed*/
        $sql=$link->prepare('SELECT id FROM reports WHERE `user_id`=? AND question_id=?');
        $sql->bind_param('ii', $this->session_user_id,$this->question_id);
        $sql->execute();
        $sql->bind_result($aux);
        if($sql->fetch()){
            $sql->close();
            $db_connection->close(); 
            $this->reload("You already reported this question!");
        }
        $sql->close();
        $sql=$link->prepare('INSERT INTO reports (`user_id`,question_id,`text`,date_created) VALUES (?,?,?,now())');
        $sql->bind_param('iis', $this->session_user_id,$this->question_id,$message);
        $sql->execute();
        /*Increment reports_nr for question*/
        $sql=$link->prepare('SELECT reports_nr FROM questions WHERE id=?');
        $sql->bind_param('i', $this->question_id);
        $sql->execute();
        $sql->bind_result($reports_nr);
        $sql->fetch();
        $sql->close();
        $reports_nr=$reports_nr+1;
        $sql=$link->prepare("UPDATE questions SET reports_nr=? WHERE id=?");        
        $sql->bind_param('ii',$reports_nr,$this->question_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
    }
    private function can_reveal_code(){
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
        $formulas->can_reveal_author_code($right_answers,$posted_questions,$deleted_questions,$code_reveals);
        $answers_left=$formulas->get_answers_left();        
        if($answers_left>=0){
            $this->answers_left=$answers_left;
            return true;
        }else{
            $this->answers_left=(-1)*$answers_left;
            return false;
        }
    }
    private function build_reveal(){
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
        $sql=$link->prepare("SELECT right_answers FROM " . $chapter_name_aux . " WHERE `user_id`=?");
        $sql->bind_param('i',$this->session_user_id);
        $sql->execute();
        $sql->bind_result($right_answers);
        $sql->fetch();
        $sql->close();
        if($this->can_reveal_code()==true){
            $reveal = "<form class='revealForm' action='chapter_" . (string)$chapter_id . "_result/reveal_author_code' method='POST'>
                            <input class='btnReveal' name='action' type='submit' value='Reveal'/>
                            <br>Right answers after reveal: " . (string)$this->answers_left . "
                            <br>Cost: 3 right answers
                         </form>";
        }else{
            $reveal = "<form class='revealForm' action='chapter_" . (string)$chapter_id . "_result/reveal_author_code' method='POST'>
                                <input class='btnRevealGray' name='action' type='submit' value='Reveal' disabled/>
                                <br>Additional right answers required: " . $this->answers_left . "
                         </form>";
        }
        return $reveal;
        }
    public function reveal_author_code(){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $result_correct=$this->session_extract('result_correct');
        if($this->can_reveal_code()==false || !empty($result_correct)){
            die("You cannot do that!");
        }
        $_SESSION["result_correct"]=" ";
        $this->my_sem_acquire($this->session_user_id);
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        /*mark question as deleted*/
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        /*decrement deleted_questions*/
        $chapter_id=self::CHAPTER_ID;
        $sql=$link->prepare('SELECT code_reveals FROM chapter_' . (string)$chapter_id . ' WHERE `user_id`=?');
        $sql->bind_param('i', $this->session_user_id);
        $sql->execute();
        $sql->bind_result($code_reveals);
        $sql->fetch();
        $sql->close();
        $code_reveals=$code_reveals+1;
        $sql=$link->prepare("UPDATE chapter_" . (string)$chapter_id . " SET code_reveals=? WHERE `user_id`=?");        
        $sql->bind_param('ii',$code_reveals,$this->session_user_id);
        $sql->execute();
        $sql->close();
        //die("AICI!" . $deleted_questions . $chapter_id);
        $db_connection->close();
        $this->my_sem_release();
        $this->reload();
    }
    public function process(){
        $this->check_login();
        $this->check_chapter_posted(self::CHAPTER_ID);
        $this->my_sem_acquire($this->session_user_id);
        if(strlen($_POST["text_field"])>self::REPORT_MAX_LEN){
            $this->reload("Characters limit exceeded!");
        }
        if($_POST["action"]=="Report"){
            if(strcmp($_POST["text_field"],"")==0){
                $message="";
            }else{
                $message=$_POST["text_field"];
            }
            $this->question_id=$this->session_extract('question_id');
            if(empty($this->question_id)){
                $this->my_sem_release();
                die("You can't submit report!");
            }
            if(strcmp($message,'Enter report message')==0){
                $message="";
            }
            $this->report($message);
        }
        /*Clear output message*/
        $this->session_extract('question_id',true);
        $this->session_extract('question_text',true);
        $this->session_extract('user_code',true);
        $this->session_extract('user_output',true);
        $this->session_extract('author_code',true);
        $this->session_extract('author_output',true);
        $this->session_extract('result_correct',true);
        $this->session_extract('result_incorrect',true);
        header('Location: ../chapter_' . (string)self::CHAPTER_ID . '_solve');
        $this->my_sem_release();
    }
}