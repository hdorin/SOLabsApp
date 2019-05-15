<?php
class Logout extends Controller
{
    public function index()
    {
        $this->check_login();
        $this->my_sem_acquire($this->session_user_id);
        $this->session_extract("user",true);
        $this->session_extract("user_id",true);
        $this->session_extract("is_admin",true);
        header('Location: ../public');/*redict to home controller after login*/
        $this->my_sem_release();
    }
    
}