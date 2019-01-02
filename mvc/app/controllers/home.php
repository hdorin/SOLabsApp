<?php
class Home extends Controller
{
    private $news,$new_nr;
    public function index()
    {
        if(isset($_SESSION['user'])){
            $this->get_news();
            $this->view('home/home', ['news' => $this->news,'news_nr' => $this->news_nr]);
        }else{
            $this->view('home/login');
        }
        
    }
    public function delete_news($news_id){
        $this->check_login();
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }
        $this->my_sem_acquire($this->session_user_id);
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('DELETE FROM news WHERE id=?');
        $sql->bind_param('i',intval($news_id));
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();
        header('Location: ../../home');
    }
    public function add_news(){
        $this->check_login();
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }
        $this->my_sem_acquire($this->session_user_id);
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('INSERT INTO news (`user_id`,`text`,date_created) VALUES (?,?,now())');
        $sql->bind_param('is', $this->session_user_id,$_POST["text_field"]);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();
        header('Location: ../home');
    }
    public function get_news(){
        $this->check_login();
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT id,`text`,date_created FROM news");
        $sql->execute();
        $sql->bind_result($news_id,$news_text,$news_date);
        $this->news_nr=0;
        
        while($sql->fetch()){
            if($this->session_is_admin==true){
                $this->news[$this->news_nr]=   "<div class='news'>
                                                    <p>" . $news_date . ":  " . $news_text . "</p>
                                                    <a href='home/delete_news/" . (string)$news_id . "'>Delete</a>
                                                </div>";
            }else{
                $this->news[$this->news_nr]=   "<div class='news'>
                                                    <p>" . $news_date . ":  " . $news_text . "</p>
                                                </div>";
            }
            $this->news_nr=$this->news_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
}