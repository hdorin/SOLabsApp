<?php
class View_Questions extends Controller
{
    private $questions,$questions_nr;
    private $chapters,$chapters_nr;
    private $page_controls;
    private const QUESTIONS_PER_PAGE=6;
    private const TEXT_MAX_LEN=500;
    public function index($page='test')
    {        
        $this->check_login();
        if(strcmp($page,'test')!=0){
            $_SESSION["questions_page"]=intval($page);
            if($_SESSION["questions_page"]<1){
                $_SESSION["questions_page"]=1;
            }
            $this->reload();
        }
        //echo "Page number=". $_SESSION["questions_page"];
        $this->session_extract("exec_msg",true);
        $this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);

        $this->generate_page_controls();
        $this->get_questions();
        $this->get_chapters();
        $this->view('home/view_questions',['questions' => $this->questions,'questions_nr' => $this->questions_nr,'chapters' => $this->chapters,
                                           'chapters_nr'=>$this->chapters_nr, 'page_controls' =>$this->page_controls]);
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
            $_SESSION["raw_criteria_posted"]='posted';
        }else{
            $_SESSION["criteria_posted"]="AND q.`status`='deleted'";
            $_SESSION["raw_criteria_posted"]='deleted';
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
            $_SESSION["raw_criteria_user"]=$_POST["user_field"];
        }else{
            $_SESSION["criteria_user"]=" ";
            unset($_SESSION["raw_criteria_user"]);
        }
        if(empty($_POST["chapter_field"]) || strcmp($_POST["chapter_field"],"all")==0){
            $_SESSION["criteria_chapter"]=" ";
            unset($_SESSION["raw_criteria_chapter"]);
        }else{
            $_SESSION["criteria_chapter"]="AND q.chapter_id=" . (string)intval($_POST["chapter_field"]);/*prevents SQL injection*/
            $_SESSION["raw_criteria_chapter"]=intval($_POST["chapter_field"]);
        }
        if(empty($_POST["validation_field"]) || strcmp($_POST["validation_field"],"All")==0){
            $_SESSION["criteria_validation"]=" ";
            unset($_SESSION["raw_criteria_validation"]);
        }else if(strcmp($_POST["validation_field"],"None")==0){
            $_SESSION["criteria_validation"]="AND q.validation='None'";
            $_SESSION["raw_criteria_validation"]='None';
        }else if(strcmp($_POST["validation_field"],"Valid")==0){
            $_SESSION["criteria_validation"]="AND q.validation='Valid'";
            $_SESSION["raw_criteria_validation"]='Valid';
        }else if(strcmp($_POST["validation_field"],"Invalid")==0){
            $_SESSION["criteria_validation"]="AND q.validation='Invalid'";
            $_SESSION["raw_criteria_validation"]='Invalid';
        }else{
            $_SESSION["criteria_validation"]=" ";
            unset($_SESSION["raw_criteria_user"]);
        }
        if(empty($_POST["sort_field"]) || strcmp($_POST["sort_field"],"none")==0){
            $_SESSION["criteria_sort"]=" ";
            unset($_SESSION["raw_criteria_sort"]);
        }else if(strcmp($_POST["sort_field"],"reports_asc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.reports_nr ASC";
            $_SESSION["raw_criteria_sort"]="reports_asc";
        }else if(strcmp($_POST["sort_field"],"reports_desc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.reports_nr DESC";
            $_SESSION["raw_criteria_sort"]="reports_desc";
        }else if(strcmp($_POST["sort_field"],"date_asc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.date_created ASC";
            $_SESSION["raw_criteria_sort"]="date_asc";
        }else if(strcmp($_POST["sort_field"],"date_desc")==0){
            $_SESSION["criteria_sort"]="ORDER BY q.date_created DESC";
            $_SESSION["raw_criteria_sort"]="date_desc";
        }
        $db_connection->close();
        $_SESSION["questions_page"]=1;
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
        $chapter_posted=" ";
        
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if($this->session_is_admin==false){
            $chapter_posted="AND c.status='posted'";
            $search_user="AND q.`user_id`=" . (string)$this->session_user_id;
            $search_chapter=" ";
        }
        $qurery="SELECT q.id,q.`chapter_id`,q.all_answers,q.right_answers,q.`validation`,c.name,u.user_name,q.date_created,q.reports_nr FROM questions q JOIN chapters c ON q.chapter_id=c.id JOIN users u ON q.user_id=u.id WHERE 1=1 " . $chapter_posted . " " . $search_user .  " " . $question_posted . " " . $search_chapter . " " . $search_validation . " " . $sort_criterion;
        //echo $qurery;
        $sql=$link->prepare($qurery);
        $sql->execute();
        $sql->bind_result($question_id,$chapter_id,$all_answers,$right_answers,$validation,$chapter_name,$user_name,$date_submitted,$reports_nr);
        
        for($i=0 ; $i < ($_SESSION["questions_page"]-1)*self::QUESTIONS_PER_PAGE ; $i++){
            $sql->fetch();
        }
        $this->questions_nr=0;
        $config=$this->model('JSONConfig');
        $app_local_path=$config->get('app','local_path');
        while($sql->fetch() && $this->questions_nr<self::QUESTIONS_PER_PAGE){
            $question_text=$question_text_aux=null;
            $line=0;
            $text_file=fopen($app_local_path . '/mvc/app/questions/' . $question_id . '.text','r');
            $question_text=fread($text_file,self::TEXT_MAX_LEN);
            fclose($text_file);$question_text=$this->replace_html_special_characters($question_text);
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
                                                                    <p class='details_admin'> User: " . $user_name . "</p>
                                                                    <p class='details_admin'> Date Submitted: " . $date_submitted . "</p>
                                                                    <p class='details_admin'> Reports: " . $reports_nr . "</p>
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
        if($this->session_is_admin==true){/*admins can see unposted chapters*/
            $query="SELECT id,`name` FROM chapters";
        }else{
            $query="SELECT id,`name` FROM chapters WHERE `status`='posted'";
        }
        $sql=$link->prepare($query);
        $sql->execute();
        $sql->bind_result($chapter_id,$chapter_name);
        $this->chapters_nr=1;
        $this->chapters[0]='<option value="all">All</option>';
        while($sql->fetch()){
                if(!empty($_SESSION['raw_criteria_chapter']) && $chapter_id==$_SESSION['raw_criteria_chapter']){
                    $this->chapters[$this->chapters_nr]='<option value="' . (string)$chapter_id . '" selected="selected">' . $chapter_name . '</option>';
                }else{
                    $this->chapters[$this->chapters_nr]='<option value="' . (string)$chapter_id . '">' . $chapter_name . '</option>';
                }
            $this->chapters_nr=$this->chapters_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
    public function jump_to_page(){
        $this->check_login();
        if(empty($_POST["number_field"]) || $_POST["number_field"]<1){
            $number=1;
        }else{
            $number=$_POST["number_field"];
        }
        $new_url="../view_questions/" . (string)$number;
        header('Location: '.$new_url);
        die;
    }
    private function generate_page_controls(){
        /*count all questions that can be displayed*/
        $question_posted=$this->session_extract("criteria_posted");
        $search_user=$this->session_extract("criteria_user");
        $search_chapter=$this->session_extract("criteria_chapter");
        $search_validation=$this->session_extract("criteria_validation");
        $chapter_posted=" ";

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        if($this->session_is_admin==false){
            $chapter_posted="AND c.`status`='posted'";
            $search_user="AND q.`user_id`=" . (string)$this->session_user_id;
            $search_chapter=" ";
        }
        $qurery="SELECT COUNT(q.id) FROM questions q JOIN chapters c ON q.chapter_id=c.id JOIN users u ON q.user_id=u.id WHERE 1=1 ". $chapter_posted. " " . $search_user .  " " . $question_posted . " " . $search_chapter . " " . $search_validation;
        $sql=$link->prepare($qurery);
        $sql->execute();
        $sql->bind_result($questions_total);
        $sql->fetch();
        $sql->close();
        $db_connection->close();
        if(ceil($questions_total/self::QUESTIONS_PER_PAGE)<$_SESSION["questions_page"]){
            $_SESSION["questions_page"]=ceil($questions_total/self::QUESTIONS_PER_PAGE);
        }
        
        $this->page_controls='<div class="pageNumber">
            <div class="controls">
                <form class="pageNumberPrevious" action="view_questions/' . (string)($_SESSION["questions_page"]-1) . '" method="POST">';
        if($_SESSION["questions_page"]>1){
            $this->page_controls=$this->page_controls . '<input type="submit" value="Previous Page"/>';
        }else{
            $this->page_controls=$this->page_controls . '<input type="submit" value="Previous Page" disabled/>';
        }
        $this->page_controls=$this->page_controls . '</form>
                <form class="pageNumberValue" action="view_questions/jump_to_page" method="POST">
                    <input type="number" min="1" max="' . (string)ceil(($questions_total/self::QUESTIONS_PER_PAGE)) . '"name="number_field" value="' . (string)$_SESSION["questions_page"] . '" required/>
                    <input type="submit" value="Jump"/>
                </form>
                <form class="pageNumberNext" action="view_questions/' . (string)($_SESSION["questions_page"]+1) . '" method="POST">';
        if(ceil($questions_total/self::QUESTIONS_PER_PAGE) - $_SESSION["questions_page"]>0){
            $this->page_controls=$this->page_controls . '<input type="submit" value="Next Page"/>';
        }else{
            $this->page_controls=$this->page_controls . '<input type="submit" value="Next Page" disabled/>';
        }
        $this->page_controls=$this->page_controls .'</form>
            </div>
        </div>';
    }
}