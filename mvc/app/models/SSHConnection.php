<?php

class SSHConnection
{
    private $host,$port;
    private $connection;
    private $sudo_user,$sudo_pass;
    public function configure($host,$port){
        $this->host=$host;
        $this->port=$port;
    }
    public function create_user($user,$pass,$newuser_script_path,$quota_limit){

        $stream=ssh2_exec($this->connection, 'cd ' . $newuser_script_path  . ';' . 'sudo ./CreateUser.sh ' . $pass . ' '. $user . ' ' . $quota_limit);
    }
    public function connect($user,$pass){
        if (!($this->connection = ssh2_connect($this->host, $this->port))) {
            throw new Exception('Could not establish SSH connection!');
        }
        if (!ssh2_auth_password($this->connection, $user, $pass)){
            unset($this->connection);
            return false;
        }
        return true;
    }
    public function close(){
        ssh2_disconnect($this->connection);
        //unset($this->connection);
    }
    public function execute($command,$timeout_seconds){
        ssh2_exec($this->connection, 'echo "' .  $command . '" > command.sh'); /*preventing command injection (using ';') with "" */
        ssh2_exec($this->connection, 'chmod +x command.sh');
        $stream = ssh2_exec($this->connection, 'timeout ' . $timeout_seconds .  ' ./command.sh');/*hardcoded + adaugat comentariu*/ 
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $out_msg=stream_get_contents($stream_out);
        if(empty($out_msg)==true){
            $stream_err = ssh2_fetch_stream($stream,SSH2_STREAM_STDERR);
            $err_msg=stream_get_contents($stream_err);    
            throw new Exception($err_msg);
        }
        return $out_msg;
    }
}