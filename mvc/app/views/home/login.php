<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login Page</title>
    <link rel="stylesheet" href="resources/stylesheets/login.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/app.css" type="text/css" />
</head>
<body id="bodyMain">
    <form id="lgnPanel" action="login/process" method="POST">

        <h1>Login</h1>
        <p id="errorMsg"><?=$data['error_msg']?></p>
        <h2>Username</h2>
        <input id="userField" name="user_field" type="text" required/>
        <h2>Password</h2>
        <input id="passField" name="pass_field" type="password" required/>
        
        <div id="rememberMe" >
            <input type="checkbox" name="rememberMe"/>
            Remember Me<br>
        </div>
        <input id="lgnButton" type="submit" value="Login" />
    </form> 


