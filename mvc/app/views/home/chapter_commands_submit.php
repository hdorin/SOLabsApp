<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: Commands</title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_commands_submit.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class="questionBox">
        <div class="questionTextTitle">
            <h1>Enter question text</h1>
        </div>
        <form class="submitQuestion" action="chapter_commands_submit/process" method="POST">
        <div class="questionText">
            <textarea name="text_field" type="text" rows="4" cols="50" required maxlength="500"><?php echo $data['text_field']; ?></textarea>
        </div>
        <div class="questionInputitle">
            <h1>Enter question input</h1>
        </div>
        <div class="questionInput">
            <textarea class="inputField" name="input_field" type="text" rows="4" cols="50" required maxlength="150"><?php echo $data['input_field']; ?></textarea>
        </div>
            
            
            <input class="btnSubmit" name="action" type="submit" value="Execute" />
            <input class="btnSubmit" name="action" type="submit" value="Submit" />
        </form> 
    </div>
    <div class="resultBox">
        <p class="errorMsg"><?=$data['error_msg']?></p>
        <p class="execMsg"><?=$data['exec_msg']?></p>
    </div>
</body>
</html>

