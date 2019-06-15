<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: <?=$data['chapter_name']?></title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_<?=$data['chapter_id']?>_submit.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class="questionBox">
        <form class="submitQuestion" action="chapter_<?=$data['chapter_id']?>_submit/process" method="POST">
            <div class="questionTextTitle">
                <h1>Enter question text*</h1>
            </div>
            <div class="questionText">
                <textarea name="text_field" rows="4" cols="50" required maxlength="<?php echo (string)$data['text_field_max_len']; ?>"><?php echo $data['text_field']; ?></textarea>
            </div>
            <div class="questionArgsTitle">
                <h1>Enter question arguments</h1>
            </div>
            <div class="questionArgs">
                <textarea class="argsField" name="args_field" rows="1" cols="50" maxlength="<?php echo (string)$data['args_field_max_len']; ?>"><?php echo $data['args_field']; ?></textarea>
            </div>
            <div class="questionInputTitle">
                <h1>Enter question input</h1>
            </div>
            <div class="questionInput">
                <textarea class="inputField" name="input_field" rows="2" cols="50" maxlength="<?php echo (string)$data['input_field_max_len']; ?>"><?php echo $data['input_field']; ?></textarea>
            </div>
            <div class="questionCodeTitle">
                <h1>Enter question code*</h1>
            </div>
            <div class="questionCode">
                <textarea class="codeField" name="code_field" rows="4" cols="50" required maxlength="<?php echo (string)$data['code_field_max_len']; ?>"><?php echo $data['code_field']; ?></textarea>
            </div>
            <input class="btnExecute" name="action" type="submit" value="Execute" onclick="executeFunction()"/>
            <input class="btnSubmit" name="action" type="submit" value="Submit" onclick="submitFunction()"/>
            <script>
                function executeFunction() {
                    document.getElementById("execMsg").innerHTML = "Executing ...";
                    document.getElementById("errorMsg").innerHTML = " ";
                }
                function submitFunction() {
                    document.getElementById("execMsg").innerHTML = "Submitting ...";
                    document.getElementById("errorMsg").innerHTML = " ";
                }
            </script>
        </form> 
    </div>
    <div class="resultBox">
        <p class="errorMsg"  id="errorMsg"><?=$data['error_msg']?></p>
        <p class="execMsg"  id="execMsg"><?=$data['exec_msg']?></p>
    </div>
</body>
</html>

