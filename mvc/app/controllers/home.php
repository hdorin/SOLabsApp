<?php
class Home extends Controller
{
    public function index()
    {
        if(isset($_SESSION['user'])){
            $this->view('home/home');
        }else{
            $this->view('home/login');
        }
        
    }
}