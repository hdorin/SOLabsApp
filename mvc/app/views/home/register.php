<!DOCTYPE html>


<head>
    <meta charset="utf-8" />
    <title>Register Page</title>
    <link rel="stylesheet" href="resources/stylesheets/register.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/app.css" type="text/css" />
    <link rel="apple-touch-icon" sizes="180x180" href="resources/images/favicon/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="resources/images/favicon/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="resources/images/favicon/favicon-16x16.png" />
</head>
<body id="bodyMain">
    <div id="siteLogo">
        <a  href="index">
            <img src="resources/images/logo.jpg" alt="Site logo" />
        </a>
    </div>
    <form id="regPanel" action="register/process" method="POST" >
        
        <h1>Register</h1>
        <p id="errorMsg"><?=$data['error']?></p>
        <h2>First Name</h2>
        <input id="nameField" name="nameField" type="text" required/>
        <h2>Last Name</h2>
        <input id="nameField" name="lnameField" type="text" required/>
        <h2>Date of Birth</h2>
        <input id="nameField" name="dobField" type="Date" required/>
        <h2>Email</h2>
        <input id="emailField" name="emailField" type="email" required/>
        <h2>Password</h2>
        <input id="passField1" name="passField1" type="password" required />
        <h2>Confirm Password</h2>
        <input id="passField2" name="passField2" type="password" required />
        <div id="acceptEULA" >
            <input type="checkbox" name="acceptEULA" required/>
            I have read and accept the <a href="resources/documents/EULA.pdf">EULA</a>
        </div>
        <input id="regButton" type="submit" value="Register" />
    </form>
<?php

include 'footer.php';

?>