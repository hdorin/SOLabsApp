<?php
class Choose_Chapter extends Controller
{
    public function index()
    {
        $this->check_login();
        $this->session_extract("exec_msg",true);
        $error_msg=$this->session_extract("error_msg",true);
        $this->session_extract("text_field",true);
        $this->session_extract("code_field",true);
        $this->view('home/chapters',['error_msg' => $error_msg]);
    }
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../chapters";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
       
    }
}