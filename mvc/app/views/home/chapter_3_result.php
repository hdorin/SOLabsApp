<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: C Linux</title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_<?=$data['chapter_id']?>_result.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class="questionBox">
        <div class="questionResultCorrect">
            <h1><?php echo $data['result_correct']; ?></h1>
        </div>
        <div class="questionResultIncorrect">
            <h1><?php echo $data['result_incorrect']; ?></h1>
        </div>
        
        <div class="questionText">
            <p><?php echo $data['question_text']; ?></p>
        </div>
        <div class="outputs">
            <div class="userbox">
                <div class="questionCodeTitle">
                    <h1>Your code</h1>
                </div>
                <div class="questionCode">
                    <p><?php echo $data['user_code']; ?></p>
                </div>
                <div class="questionOutputTitle">
                    <h1>Output</h1>
                </div>
                <div class="questionCode">
                    <p><?php echo $data['user_output']; ?></p>
                </div>
            </div>
            <div class="authorBox">
                <div class="questionCodeTitle">
                    <h1>Author's code</h1>
                </div>
                <div class="questionCode">
                    <p><?php if(empty($data['result_correct'])) {
                                echo "Hidden";
                            }else{
                                echo $data['author_code'];
                            }
                    ?></p>
                </div>
                <div class="questionOutputTitle">
                    <h1>Output</h1>
                </div>
                    <div class="questionOutput">
                    <p><?php echo $data['author_output']; ?></p>
                </div>
            </div>
        </div >
        <form class="resultActions" action="chapter_<?=$data['chapter_id']?>_result/process" method="POST">
            <input class="reportText" name="text_field" type="text" maxlength="100" onfocus="this.value=''"  value="Enter report message"/>    
            <input class="btnReport" name="action" type="submit" value="Report" />
            <input class="btnContinue" name="action" type="submit" value="Continue" formnovalidate/>
        </form> 
    </div>
    <div class="resultBox">
        <p class="errorMsg"><?=$data['error_msg']?></p>
        
    </div>
</body>
</html>

