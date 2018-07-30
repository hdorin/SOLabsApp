<?php

class SSHConnection
{
    private $host;
    private $port;
    private $connection;
    public function configure($host,$port){
        $this->host=$host;
        $this->port=$port;
        
    }
    public function create_user($user,$pass){
        if (!($connection = ssh2_connect($this->host, $this->port))) {
            throw new Exception('Could not establish SSH connection!');
        } 
        if (!ssh2_auth_password($connection, 'dorin', 'halogenuri')) {
            throw new Exception('Could not access administrator account!');
        }
        ssh2_exec($connection, '/var/www/html/AplicatieSO');
        ssh2_exec($connection, 'sudo ./CreateUser.sh ' . $pass . ' '. $user);
        unset($connection);
    }
    public function check_user($user,$pass){
        
        if (!($connection = ssh2_connect($this->host, $this->port))) {
            throw new Exception('Could not establish SSH connection!');
        }
        if (ssh2_auth_password($connection, $user, $pass)) {
            unset($connection);
            return true;
        }else{
            unset($connection);
            return false;
        }
    }
    public function connect($user,$pass){
        if (!($this->connection = ssh2_connect($this->host, $this->port))) {
            throw new Exception('Could not establish SSH connection!');
        }
        if (!ssh2_auth_password($this->connection, $user, $pass)){
            unset($this->connection);
            throw new Exception('Could not acces Linux machine!');
        }
    }
    public function execute($command){
        $stream = ssh2_exec($this->connection, $command);
    }
}