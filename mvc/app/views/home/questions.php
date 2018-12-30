<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Questions</title>

    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/questions.css" type="text/css" />
    </head>
<body>

<?php
    include "header.php"
?>
    <!--Restrict this form only to admins-->
    <form class="questionsCriteria" action="questions/get_questions" method="POST">
        <h3>User</h3>
        <input class="userField" name="user_field" type="text">
        <select>
            <option value="posted">Posted</option>
            <option value="deleted">Deleted</option>
        </select>
        <input class="refreshButton" type="submit" value="Refresh" />
    </form>
    <div class="questionsBox">
        <?php 
            for($i=0;$i<$data['questions_nr'];$i++){
                echo $data['questions'][$i];
            }
        ?>
    </div>
</body>
</html>