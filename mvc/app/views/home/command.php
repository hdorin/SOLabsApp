<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Command Page</title>
    <link rel="stylesheet" href="resources/stylesheets/command.css" type="text/css" />
</head>
<body id="bodyMain">
    
    <form id="commandPanel" action="command/process" method="POST">
        <p id="errorMsg"><?=$data['error_msg']?></p>
        <p id="execMsg"><?=$data['exec_msg']?></p>
        <input id="commandField" name="command_field" type="text" required/>
        <input id="submitButton" type="submit" value="Submit" />
    </form> 


