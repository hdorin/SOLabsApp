<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login Page</title>
    <link rel="stylesheet" href="resources/stylesheets/login.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/app.css" type="text/css" />
    <link rel="apple-touch-icon" sizes="180x180" href="resources/images/favicon/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="resources/images/favicon/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="resources/images/favicon/favicon-16x16.png" />
</head>
<body id="bodyMain">
    <div id="siteLogo">
        <a href="index">
            <img src="resources/images/logo.jpg" alt="Site logo" />
        </a>
    </div>
    <form id="lgnPanel" action="login/process" method="POST">

        <h1>Login</h1>
        <p id="errorMsg"><?=$data['error']?></p>
        <h2>Email</h2>
        <input id="emailField" name="emailField" type="email" required/>
        <h2>Password</h2>
        <input id="passField" name="passField" type="password" required/>
        
        <div id="rememberMe" >
            <input type="checkbox" name="rememberMe"/>
            Remember Me<br>
        </div>
        <input id="lgnButton" type="submit" value="Login" />
        <div id="frgtPass">
            <a href="frgtpass">I forgot my password</a>
        </div>
    </form>

<?php

    include 'footer.php';

?>


