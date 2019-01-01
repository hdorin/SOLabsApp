<?php
class View_Questions extends Controller
{
    private $questions,$questions_nr;
    private $chapters,$chapters_nr;
    public function index()
    {
        $this->check_login();
        $this->session_extract("exec_msg",true);
        $this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);

        
        $this->get_questions();
        $this->get_chapters();
        $this->view('home/questions',['questions' => $this->questions,'questions_nr' => $this->questions_nr,'chapters' => $this->chapters,'chapters_nr'=>$this->chapters_nr]);
    }
    public function refresh_criteria(){
        $this->check_login();
        if($this->session_is_admin==false){
            die("You cannot access this!");
        }
        
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        
        if(strcmp($_POST["status_field"],"posted")==0){
            $_SESSION["criteria_posted"]="AND q.`status`='posted'";
        }else{
            $_SESSION["criteria_posted"]="AND q.`status`='deleted'";
        }

        if(!empty($_POST["user_field"])){
           
            $sql=$link->prepare('SELECT id FROM users WHERE `user_name`=?');
            $sql->bind_param('s', $_POST["user_field"]);
            $sql->execute();
            $sql->bind_result($aux_user_id);
            if(!$sql->fetch()){
                $aux_user_id="-1";
            }
            $sql->close();
            $_SESSION["criteria_user"]="AND q.`user_id`=" . $aux_user_id;
        }else{
            $_SESSION["criteria_user"]=" ";
        }

        if(empty($_POST["chapter_field"]) || strcmp($_POST["chapter_field"],"all")==0 || strlen($_POST["chapter_field"])>2){/*last condition prevents SQL injection*/
            $_SESSION["criteria_chapter"]=" ";
        }else{
            $_SESSION["criteria_chapter"]="AND q.chapter_id=" . $_POST["chapter_field"];
        }

        if(empty($_POST["validation_field"]) || strcmp($_POST["validation_field"],"All")==0){
            $_SESSION["criteria_validation"]=" ";
        }else if(strcmp($_POST["validation_field"],"None")==0){
            $_SESSION["criteria_validation"]="AND q.validation='None'";
        }else if(strcmp($_POST["validation_field"],"Valid")==0){
            $_SESSION["criteria_validation"]="AND q.validation='Valid'";
        }else if(strcmp($_POST["validation_field"],"Invalid")==0){
            $_SESSION["criteria_validation"]="AND q.validation='Invalid'";
        }else{
            $_SESSION["criteria_validation"]=" ";
        }


        if(empty($_POST["sort_field"]) || strcmp($_POST["sort_field"],"none")==0){
            $_SESSION["criteria_sort"]=" ";
        }else if(strcmp($_POST["sort_field"],"reports_asc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.reports_nr ASC";
        }else if(strcmp($_POST["sort_field"],"reports_desc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.reports_nr DESC";
        }else if(strcmp($_POST["sort_field"],"date_asc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.date_created ASC";
        }else if(strcmp($_POST["sort_field"],"date_desc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.date_created DESC";
        }
        $db_connection->close();
        $this->reload();
    }
    private function reload(){
        $new_url="../view_questions";
        header('Location: '.$new_url);
        die;
    }
    private function get_questions(){
        $question_posted=$this->session_extract("criteria_posted");
        $search_user=$this->session_extract("criteria_user");
        $search_chapter=$this->session_extract("criteria_chapter");
        $search_validation=$this->session_extract("criteria_validation");
        $sort_criterion=$this->session_extract("criteria_sort");
        

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if($this->session_is_admin==false){
            $is_posted="AND q.`status`='posted'";
            $search_user="AND q.`user_id`=" . (string)$this->session_user_id;
            $search_chapter=" ";
        }
            
        $qurery="SELECT q.id,q.`chapter_id`,q.all_answers,q.right_answers,q.`validation`,c.name,u.user_name,q.date_created,q.reports_nr FROM questions q JOIN chapters c ON q.chapter_id=c.id JOIN users u ON q.user_id=u.id WHERE c.status='posted' " . $search_user .  " " . $question_posted . " " . $search_chapter . " " . $search_validation . " " . $sort_criterion;
        $sql=$link->prepare($qurery);
        $sql->execute();
        $sql->bind_result($question_id,$chapter_id,$all_answers,$right_answers,$validation,$chapter_name,$user_name,$date_submitted,$reports_nr);
        $this->questions_nr=0;
        
        while($sql->fetch()){
            exec('cat /var/www/html/AplicatieSO/mvc/app/questions/' . (string)$question_id . '.text',$question_text_aux);
            $question_text=$question_text_aux[$this->questions_nr];
            if($this->session_is_admin==false){
                $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                    <p class='text'>" . $question_text . "</p>
                                                                    <p class='details'> Times Answered: " . $right_answers . " / " .  $all_answers . "</p>
                                                                    <p class='details'> Validation: " . $validation . "</p>
                                                                    <p class='details'> Chapter: " . $chapter_name . "</p>
                                                            </a>";
                
            }else{
                $this->questions[$this->questions_nr]=   "<a class='question' href='chapter_" . (string)$chapter_id . "_view_question/" . $question_id . "'>
                                                                    <p class='text'>" . $question_text . "</p>
                                                                    <p class='details'> Times Answered: " . $right_answers . " / " .  $all_answers . "</p>
                                                                    <p class='details'> Validation: " . $validation . "</p>
                                                                    <p class='details'> Chapter: " . $chapter_name . "</p>
                                                                    <p class='details'> User: " . $user_name . "</p>
                                                                    <p class='details'> Date Submitted: " . $date_submitted . "</p>
                                                                    <p class='details'> Reports: " . $reports_nr . "</p>
                                                            </a>";
            }
            $this->questions_nr=$this->questions_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
    private function get_chapters(){
        if($this->session_is_admin==false){
            $this->chapters="";
            $this->chapters_nr=0;
            return 0;
        }
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
        $this->chapters_nr=1;
        $this->chapters[0]='<option value="all">All</option>';
        while($sql->fetch()){
                $this->chapters[$this->chapters_nr]='<option value="' . (string)$chapter_id . '">' . $chapter_name . '</option>';
            $this->chapters_nr=$this->chapters_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
}