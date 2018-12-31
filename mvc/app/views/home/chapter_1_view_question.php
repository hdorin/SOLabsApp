<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: Commands</title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_1_view_question.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class='questionBox' >
        <p class='questionText'><?php echo  $data["question_text"] ;?> </p>
        <p class='questionCode'><?php echo  $data["question_code"]; ?> </p>
        <div class='questionDetailsBox'>
            <p class='questionDetails'><?php echo "Times answered: " . $data["right_answers"] . " / " . $data["all_answers"] ;?> </p>
            <p class='questionDetails'><?php echo "Validation: " . $data["validation"]; ?> </p>
            <?php if($_SESSION['is_admin']==true){
                    echo '
                        <form class="validateQuestion" method="POST" action="chapter_1_view_question/validate_question/' . $data["question_id"] . '">
                            <h3>Mark as</h3>    
                            <select name="validation_field">
                                    <option value="None">None</option>
                                    <option value="Valid">Valid</option>
                                    <option value="Invalid">Invalid</option>
                            </select>
                            <input class="markButton" type="submit" value="Mark" />
                        </form>';
                }
            ?>
            <p class='questionDetails'><?php echo "Date submitted: " . $data["date_submitted"]; ?> </p>
        </div>
        <form class='deleteQuestion' method="POST" action="chapter_1_view_question/delete_question/<?php echo $data['question_id'] ?>">
            <?php 
                if($data['can_delete']==false){
                    echo "<input class='btnDeleteGray' type='submit' value='Delete' disabled/><br><p class='cannotDeleteMessage'>Answer " . $data['answers_left'] . " more questions!<p>";        
                }else{
                    echo "<input class='btnDelete' type='submit' value='Delete'/>";        
                }
            ?>
           
        </form>
        <h2>Reports</h2>
        <div class='reportsBox'>
            <?php 
                for($i=0;$i<$data['reports_nr'];$i++){
                    echo $data['reports'][$i];
                }
            ?>
        </div>
    </div>
</body>
</html>

