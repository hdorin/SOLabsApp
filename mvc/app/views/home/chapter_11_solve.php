<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: <?=$data['chapter_name']?></title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_<?=$data['chapter_id']?>_solve.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class="questionBox">
        <div class="questionText">
            <p><?php echo $data['question_text']?></p>
        </div>
        <form class="questionCode" action="chapter_<?=$data['chapter_id']?>_solve/process" method="POST">
        <div class="textareaInput">
            <h3>Input <br> file:</h3>
            <textarea class="inputField" name="input_field" rows="2" cols="50" maxlength="<?php echo (string)$data['input_field_max_len']; ?>"><?php echo $data['input_field']; ?></textarea>
        </div>
        <div class="textareaCode">
            <textarea id="codeField" class="codeField" name="code_field" rows="1" cols="50" required maxlength="<?php echo (string)$data['code_field_max_len']; ?>"><?php echo $data['code_field']; ?></textarea>
        </div>
        <input class="btnExecute" name="action" type="submit" value="Execute" onclick="buttonFunction('Executing ...')"/>
            <input class="btnSubmit" name="action" type="submit" value="Submit" onclick="buttonFunction('Submitting ...')"/>
            <input class="btnSkip" name="action" type="submit" value="Skip" onclick="buttonFunction('Skipping ...')" formnovalidate/>
            <script>
                function buttonFunction(msg) {
                    if(document.getElementById("codeField").value == '' && msg!="Skipping ..."){
                        document.getElementById("execMsg").innerHTML = "";
                        document.getElementById("errorMsg").innerHTML = "Fill in mandatory fields!";
                    }else{
                        document.getElementById("execMsg").innerHTML = msg;
                        document.getElementById("errorMsg").innerHTML = "";
                    }
                }
            </script>
        </form> 
    </div>
    <div class="resultBox">
        <p class="errorMsg" id="errorMsg"><?=$data['error_msg']?></p>
        <p class="execMsg" id="execMsg"><?=$data['exec_msg']?></p>
    </div>
</body>
</html>

