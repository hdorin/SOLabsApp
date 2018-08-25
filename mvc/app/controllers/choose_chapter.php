<?php
class Choose_Chapter extends Controller
{
    public function index()
    {
        $this->check_login();
        if(isset($_SESSION["error_msg"])==false){
            $error_msg="";
        }else{
            $error_msg=$_SESSION["error_msg"];
        }
        unset($_SESSION['error_msg']);
        unset($_SESSION['exec_msg']);
        $this->view('home/chapters',['error_msg' => $error_msg]);
    }
    public function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapters";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
       
    }
}