<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: Commands</title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_1.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class="questionBox">
        <div class="questionText">
            <p><?php echo $data['question_text']?></p>
        </div>
        <form class="questionCode" action="chapter_1/process" method="POST">
        <div class="textarea">
            <textarea class="codeField" name="code_field" type="text" rows="4" cols="50" required maxlength="150"><?php echo $data['code_field']; ?></textarea>
        </div>
            
            
            <input class="btnSubmit" name="action" type="submit" value="Execute" />
            <input class="btnSubmit" name="action" type="submit" value="Submit" />
            <input class="btnSubmit" name="action" type="submit" value="Skip"  formnovalidate/>
        </form> 
    </div>
    <div class="resultBox">
        <p class="errorMsg"><?=$data['error_msg']?></p>
        <p class="execMsg"><?=$data['exec_msg']?></p>
    </div>
</body>
</html>

