<?php
class MyAccount extends Controller
{
    public function index()
    {
        if(isset($_SESSION["response"])==false){
            $resp="";
        }else{
            $resp=$_SESSION["response"];
        }

    	if (isset($_SESSION['userId'])==false)
    		die('You must first login to access this page!');
        else 
        	$this->view('home/myaccount',['resp' => $resp]);

        unset($_SESSION["response"]);
    }

    public function reload($data=''){

        die($data);
    }

    public function emailChange()
    {
    	if(empty($_POST["oldEmail"])==1){
            $this->reload("You did not enter an old email!");
        }
        $oe=$_POST["oldEmail"];

        if(empty($_POST["newEmail"])==1){
            $this->reload("You did not enter a new email!");
        }
        $ne=$_POST["newEmail"];

        if ($oe == $ne){
        	$this->reload("Emails must not match!");
        }

        $this->checkEmails($oe, $ne);
    }

    public function pwdChange(){

    	if(empty($_POST["oldPwd"])==1){
            $this->reload("You did not enter an old password!");
        }
        $op=$_POST["oldPwd"];

        if(empty($_POST["newPwd"])==1){
            $this->reload("You did not enter a new password!");
        }
        $np=$_POST["newPwd"];

        if ($op == $np){
        	$this->reload("Passwords must not match!");
        }

        $op=md5($op);
        $np=md5($np);
        $this->checkPwds($op, $np);
    }

    public function checkPwds($oldPass, $newPass){
        $link = $this->auctiox_db_connect();

        $_sql = $link->prepare('SELECT passw FROM users WHERE id = ?');
        if ($_sql === false) {
  			die(E_USER_ERROR);
  			return;
		}

		$_sql->bind_param('s', $_SESSION['userId']); 
        $_sql->execute();
        $_sql->bind_result($retPass);
        $_sql->fetch();
        $_sql->close();

        if ($retPass != $oldPass){
        	die('Old password is wrong!');
        }
 
        $sql = $link->prepare('UPDATE users SET passw = ? where id = ?');
        if ($sql === false) {
  			$this->reload("SQL query failed!");
  			return;
		}

        $sql->bind_param('ss', $newPass, $_SESSION['userId']); 
        $status = $sql->execute();
        mysqli_close($link);

        if ($status === false) {
  			$this->reload("Unexpected error...");
		}
		else
		{
			$this->reload("Password changed successfully!");
            $newURL="../myAccount";
            header('Location: '.$newURL);
		}
    }

    public function checkEmails($oldEm, $newEm){
        $link = $this->auctiox_db_connect();

        $_sql = $link->prepare('SELECT email FROM users WHERE id = ?');
        if ($_sql === false) {
  			die(E_USER_ERROR);
  			return;
		}

		$_sql->bind_param('s', $_SESSION['userId']); 
        $_sql->execute();
        $_sql->bind_result($retEm);
        $_sql->fetch();
        $_sql->close();

        if ($retEm != $oldEm){
        	die('Old email is wrong!');
        }
 
        $sql = $link->prepare('UPDATE users SET email = ? where id = ?');
        if ($sql === false) {
  			$this->reload("SQL query failed!");
  			return;
		}

        $sql->bind_param('ss', $newEm, $_SESSION['userId']); 
        $status = $sql->execute();
        mysqli_close($link);

        if ($status === false) {
  			$this->reload("Unexpected error...");
		}
		else
		{
			$this->reload("Email changed successfully!");
            $newURL="../myAccount";
            header('Location: '.$newURL);
		}
    }

    public function deleteCC()
    {
        $ccno = $_POST['ccno'];

        $link = $this->auctiox_db_connect();
        $sql = $link->prepare('DELETE FROM creditcards WHERE number = ?');

        $sql->bind_param('s', $ccno); 
        $sql->execute();
        mysqli_close($link);

        $_SESSION["response"]="Credit card deleted successfully...";
        $newURL="../myAccount";
        header('Location: '.$newURL);
        die;

    }

    public function deleteAddress()
    {
        $id = $_POST['id'];

        $link = $this->auctiox_db_connect();
        $sql = $link->prepare('DELETE FROM addresses WHERE id = ?');

        $sql->bind_param('s', $id); 
        $sql->execute();
        mysqli_close($link);

        $_SESSION["response"]="Address deleted successfully...";
        $newURL="../myAccount";
        header('Location: '.$newURL);
        die;

    }

}