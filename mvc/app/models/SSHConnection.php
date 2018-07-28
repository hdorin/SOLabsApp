<?php

class SSHConnection
{

    public function connect($user,$pass){
        if (!($connection = ssh2_connect('127.0.0.1', '22'))) {
            throw new Exception('Cannot connect to server!');
        } 
    
        if (ssh2_auth_password($connection, $user, $pass)) {
            return 0;
          } else {
            return 1;
          }
    }
}