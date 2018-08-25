<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login Page</title>
    <link rel="stylesheet" href="resources/stylesheets/login.css" type="text/css" />
</head>
<body>
    <form class="lgnPanel" action="login/process" method="POST">
        <h1>Login</h1>
        <p class="errorMsg"><?=$data['error_msg']?></p>
        <h2>Username</h2>
        <input class="userField" name="user_field" type="text" required/>
        <h2>Password</h2>
        <input class="passField" name="pass_field" type="password" required/>
        <input class="lgnButton" type="submit" value="Login" />
    </form> 
</body>
</html>

