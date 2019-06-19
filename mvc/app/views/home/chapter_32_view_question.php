<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapter: <?=$data['chapter_name']?></title>
    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapter_<?=$data['chapter_id']?>_view_question.css" type="text/css" />
</head>
<body>
    <?php
        include "header.php"
    ?>
    <div class='questionBox' >
        <p class='questionText'> <?php echo  $data["question_text"];?></p>
        <?php
            if($_SESSION['is_admin']==true){
                echo "<form class='restoreQuestion' method='POST' action='chapter_" . $data['chapter_id'] . "_view_question/restore_question/" . $data['question_id'] . "'>";
                if($data['can_delete']==true){
                    echo "<input class='btnRestoreGray' type='submit' value='Restore' disabled/>";        
                }else{
                    echo "<input class='btnRestore' type='submit' value='Restore'/>";        
                }
                echo "</form>";
            }
        ?>
        <form class='deleteQuestion' method="POST" action="chapter_<?=$data['chapter_id']?>_view_question/delete_question/<?php echo $data['question_id'] ?>">
            <?php 
                if($data['can_delete']==false){
                    echo "<input class='btnDeleteGray' type='submit' value='Delete' disabled/><br><p class='cannotDeleteMessage'>Answer correctly " . $data['answers_left'] . " more questions to delete!<p>";        
                }else{
                    if($_SESSION['is_admin']==true){
                        echo "<input class='btnDelete' type='submit' value='Delete'/>";
                    }else{
                        echo "<input class='btnDelete' type='submit' value='Delete'/><br><p class='cannotDeleteMessage'>Extra right answers " . $data['answers_left'] . "<p>";
                    }
                }
            ?>
        </form>   
        <h3>Arguments: </h3>
        <p class='questionArgs'><?php echo  $data["question_args"];?></p>
        <h3>Input keyboard: </h3>
        <p class='questionKeybd'><?php echo  $data["question_keybd"];?></p>
        <h3>Input file: </h3>
        <p class='questionInput'><?php echo  $data["question_input"];?></p>
        <h3>Code: </h3>
        <p class='questionCode'><?php echo  $data["question_code"]; ?></p>
        <div class='questionDetailsBox'>
            <p class='questionDetails'><?php echo "<b>Times answered: </b>" . $data["right_answers"] . " / " . $data["all_answers"] ;?> </p>
            <p class='questionDetails'><?php echo "<b>Validation: </b>" . $data["validation"]; ?> </p>
            <?php if($_SESSION['is_admin']==true){
                    echo '
                        <form class="validateQuestion" method="POST" action="chapter_' . $data['chapter_id'] . '_view_question/validate_question/' . $data["question_id"] . '">
                            <h3>Mark as</h3>    
                            <select name="validation_field">
                                    <option value="Unvalidated">Unvalidated</option>
                                    <option value="Valid">Valid</option>
                                    <option value="Invalid">Invalid</option>
                            </select>
                            <input class="markButton" type="submit" value="Mark" />
                        </form>';
                }
            ?>
            <p class='questionDetails'><?php echo "<b>Date submitted: </b>" . $data["date_submitted"]; ?> </p>
        </div>
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

