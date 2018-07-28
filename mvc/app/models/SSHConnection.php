<?php

class SSHConnection
{
    private $user,$pass;
    public function create_new_user(){
        
        if (!($connection = ssh2_connect('127.0.0.1', '22'))) {
            throw new Exception('Could not establish SSH connection!');
        } 
        
        if (!ssh2_auth_password($connection, 'dorin', 'halogenuri')) {
            throw new Exception('Could not access administrator account!');
        }
        
        ssh2_exec($connection, 'sudo ./CreateUser.sh ' . $this->pass . ' '. $this->user);

        unset($connection);
        
    }
    public function connect($user,$pass,$first_login){
        $this->user=$user;
        $this->pass=$pass;
        if($first_login==true){
            if (!($external_cconnection = ssh2_connect('students.info.uaic.ro', '22'))){
                throw new Exception('Could not establish external SSH connection!');
            }
                if (!ssh2_auth_password($external_cconnection, $this->user, $this->pass)) {
                    return false;
                }
            unset($external_cconnection);  
            $this->create_new_user();
            //sleep(2);
        }
        if (!($connection = ssh2_connect('127.0.0.1', '22'))) {
            throw new Exception('Could not establish SSH connection!');
        } 
    
        if (ssh2_auth_password($connection, $this->user, $this->pass)) {
            return true;
        }else{
            return false;
        }
    }
}