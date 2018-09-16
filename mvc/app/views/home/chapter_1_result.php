<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: Commands</title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_1_result.css" type="text/css" />
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
        <div class="userbox">
            <div class="questionCodeTitle">
                <h1>Your command</h1>
            </div>
            <div class="questionCode">
                <p><?php echo $data['user_command']; ?></p>
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
                <h1>Author's command</h1>
            </div>
            <div class="questionCode">
                <p><?php if(empty($data['result_correct'])) {
                            echo "Hidden";
                        }else{
                            echo $data['author_command'];
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
        <form class="resultActions" action="chapter_1_result/process" method="POST">
            <input class="btnContinue" name="action" type="submit" value="Continue" />
            <input class="btnReport" name="action" type="submit" value="Report" />
        </form> 
    </div>
</body>
</html>

