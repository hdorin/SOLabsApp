<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: C Linux</title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_<?=$data['chapter_id']?>_submit.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class="questionBox">
        <div class="questionTextTitle">
            <h1>Enter question text</h1>
        </div>
        <form class="submitQuestion" action="chapter_<?=$data['chapter_id']?>_submit/process" method="POST">
        <div class="questionText">
            <textarea name="text_field" rows="4" cols="50" required maxlength="<?php echo (string)$data['text_field_max_len']; ?>"><?php echo $data['text_field']; ?></textarea>
        </div>
        <div class="questionCodeTitle">
            <h1>Enter question code</h1>
        </div>
        <div class="questionCode">
            <textarea class="codeField" name="code_field" rows="4" cols="50" required maxlength="<?php echo (string)$data['code_field_max_len']; ?>"><?php echo $data['code_field']; ?></textarea>
        </div>
            <input class="btnExecute" name="action" type="submit" value="Execute" />
            <input class="btnSubmit" name="action" type="submit" value="Submit" />
        </form> 
    </div>
    <div class="resultBox">
        <p class="errorMsg"><?=$data['error_msg']?></p>
        <p class="execMsg"><?=$data['exec_msg']?></p>
    </div>
</body>
</html>

