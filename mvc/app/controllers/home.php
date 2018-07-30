<?php
class Home extends Controller
{
    public function index()
    {
        if(isset($_SESSION['user_name'])){
            die("._.");
        }else{
            $this->view('home/login');
        }
        
    }
}