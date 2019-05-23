<?php

class SSHConnection
{
    private $host,$port;
    private $connection;
    private $execution_user;
    public function configure($host,$port){
        $this->host=$host;
        $this->port=$port;
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
    public function execute($current_user,$command,$timeout_seconds,$use_strace=false){
        //$stream = ssh2_exec($this->connection, "sleep " . $timeout_seconds . "; docker kill -name " . $this->execution_user);/*kill all processes after timeout_seconds*/
        $stream = ssh2_exec($this->connection, '/snap/bin/docker run ubuntu ' . $command); 
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $out_msg=stream_get_contents($stream_out);
        if($use_strace==true){
            $stream_err = ssh2_fetch_stream($stream,SSH2_STREAM_STDERR);
            $err_msg=stream_get_contents($stream_err);    
            return $err_msg;
        }
        if(empty($out_msg)==true){
            $stream_err = ssh2_fetch_stream($stream,SSH2_STREAM_STDERR);
            $err_msg=stream_get_contents($stream_err);    
            throw new Exception($err_msg);
        }
        return $out_msg;
    }
    public function send_code_file($current_user,$local_file,$new_extension){
        if(!ssh2_scp_send($this->connection,$local_file, $current_user . $new_extension)){
            throw new Exception("Could not send file for exection!");
        }
    }
}