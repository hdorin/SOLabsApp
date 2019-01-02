<?php
class Admin extends Controller
{
    private $chapters,$chapters_nr;
    private $admin,$admins_nr;
    public function index()
    {
        $this->check_login();
        if($this->session_is_admin==false){
            die("You cannot access this!");
        }
        $this->get_chapters();
        $this->get_admins();
        $this->view('home/admin',['chapters'=>$this->chapters, 'chapters_nr'=>$this->chapters_nr,'admins'=>$this->admins,'admins_nr'=>$this->admins_nr]);
    }
    private function get_chapters(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT id,`name`,`status` FROM chapters");
        $sql->execute();
        $sql->bind_result($chapter_id,$chapter_name,$chapter_status);
        $this->chapters_nr=0;
        
        while($sql->fetch()){
            
            $this->chapters[$this->chapters_nr]=   "<div class='chapter'>
                                                        <p>" . $chapter_name . " :  " . $chapter_status . "</p>
                                                        <a href='admin/post_chapter/" . (string)$chapter_id . "'>Post</a>
                                                        <a href='admin/unpost_chapter/" . (string)$chapter_id . "'>Unpost</a>
                                                    </div>";
            $this->chapters_nr=$this->chapters_nr+1;
        }
        $sql->close();
        $db_connection->close();
    }
    public function post_chapter($chapter_id){
        $this->check_login();
        $this->my_sem_acquire($this->session_user_id);
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("UPDATE chapters SET `status`='posted' WHERE `id`=?");        
        $sql->bind_param('i',$chapter_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();

        $new_url="../../admin";
        header('Location: '.$new_url);
    }
    public function unpost_chapter($chapter_id){
        $this->check_login();
        $this->my_sem_acquire($this->session_user_id);
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("UPDATE chapters SET `status`='unposted' WHERE `id`=?");        
        $sql->bind_param('i',$chapter_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();

        $new_url="../../admin";
        header('Location: '.$new_url);
    }
    private function get_admins(){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT id,`user_name` FROM users WHERE is_admin=1");
        $sql->execute();
        $sql->bind_result($admin_id,$admin_name);
        $this->admins_nr=0;
        while($sql->fetch()){
            
            $this->admins[$this->admins_nr]=   "<div class='admin'>
                                                        <p>" . $admin_name . "</p>
                                                        <a href='admin/remove_admin/" . (string)$admin_id . "'>Remove</a>
                                                </div>";
            $this->admins_nr=$this->admins_nr+1;
        }

        $sql->close();
        $db_connection->close();
    }
    public function remove_admin($admin_id){
        $this->check_login();
        $this->my_sem_acquire($this->session_user_id);
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("UPDATE users SET is_admin=0 WHERE `id`=?");        
        $sql->bind_param('i',$admin_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();

        $new_url="../../admin";
        header('Location: '.$new_url);
    }
    public function add_admin(){
        $this->check_login();
        $this->my_sem_acquire($this->session_user_id);
        if($this->session_is_admin==false){
            die("You cannot do that!");
        }

        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT id FROM users WHERE `user_name`=?');
        $sql->bind_param('s', $_POST["user_field"]);
        $sql->execute();
        $sql->bind_result($aux_user_id);
        if(!$sql->fetch()){
            die("User not found!");
        }
        $sql->close();
        $sql=$link->prepare("UPDATE users SET is_admin=1 WHERE `id`=?");        
        $sql->bind_param('i',$aux_user_id);
        $sql->execute();
        $sql->close();
        $db_connection->close();
        $this->my_sem_release();

        $new_url="../admin";
        header('Location: '.$new_url);
    }
}