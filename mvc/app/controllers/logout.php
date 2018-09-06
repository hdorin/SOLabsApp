<?php
class Logout extends Controller
{
    public function index()
    {
        $this->session_extract("user",true);
        $this->session_extract("pass",true);
        header('Location: ../public');/*redict to home controller after login*/
    }
    
}