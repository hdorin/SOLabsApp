<?php

class SSHConnection
{
    private $host,$port;
    private $connection;
    private $sudo_user,$sudo_pass;
    private $execution_user;
    public function configure($host,$port){
        $this->host=$host;
        $this->port=$port;
    }
    public function create_user($user,$pass,$quota_limit,$procs_limit){
        $stream = ssh2_exec($this->connection, 'cat ./CreateUser.sh ');/*check for script to create new user*/ 
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $out_msg=stream_get_contents($stream_out);
        if(empty($out_msg)==true){
            $stream_err = ssh2_fetch_stream($stream,SSH2_STREAM_STDERR);
            $err_msg=stream_get_contents($stream_err);    
            throw new Exception($err_msg);
        }else{
            $stream=ssh2_exec($this->connection, 'sudo ./CreateUser.sh ' . $pass . ' '. $user . ' ' . $quota_limit);
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $out_msg=stream_get_contents($stream_out);
            if(empty($out_msg)==true){
                throw new Exception("Could not execute CreateUser.sh!");
            }
        }
    }
    public function connect($user,$pass){
        $this->execution_user=$user;
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
        //ssh2_disconnect($this->connection);
        unset($this->connection);
    }
    public function execute($command,$timeout_seconds){
        $stream = ssh2_exec($this->connection, "sleep " . $timeout_seconds . "; pkill --signal SIGKILL -u " . $this->execution_user);/*kill all processes after timeout_seconds*/
        $stream = ssh2_exec($this->connection, $command); 
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
    public function write_code_file($local_file,$extension){
        if(!ssh2_scp_send($this->connection,$local_file,"code." . $extension)){
            throw new Exception("Could not send file for exection!");
        }
    }
}