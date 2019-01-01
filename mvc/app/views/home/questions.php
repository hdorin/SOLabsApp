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
    <?php 
        if($_SESSION['is_admin']==true){
            echo '
                <form class="questionsCriteria" action="view_questions/refresh_criteria" method="POST">
                    <h3>User</h3>
                    <input class="userField" name="user_field" type="text">
                    <h3>Status</h3>
                    <select name="status_field">
                        <option value="posted">Posted</option>
                        <option value="deleted">Deleted</option>
                    </select>
                    <h3>Chapter</h3>
                    <select name="chapter_field">';
        }
    ?>
            <?php 
                if($_SESSION['is_admin']==true){
                    for($i=0;$i<$data['chapters_nr'];$i++){
                        echo $data['chapters'][$i];
                    }
                }
            ?>
    <?php 
        if($_SESSION['is_admin']==true){
            echo '
                    </select>
                    <h3>Validation</h3>
                    <select name="validation_field">
                        <option value="All">All</option>
                        <option value="None">None</option>
                        <option value="Valid">Valid</option>
                        <option value="Invalid">Invalid</option>
                    </select>
                    <h3>Sort by</h3>
                    <select name="sort_field">
                        <option value="none">None</option>
                        <option value="reports_asc">Reports Ascendent</option>
                        <option value="reports_desc">Reports Descendent</option>
                        <option value="date_asc">Date Ascendent</option>
                        <option value="date_desc">Date Descendent</option>
                    </select>
                    <input class="refreshButton" type="submit" value="Refresh" />
                </form>';
        }
    ?>
    <div class="questionsBox">
        <?php 
            for($i=0;$i<$data['questions_nr'];$i++){
                echo $data['questions'][$i];
            }
        ?>
        
    </div>
    <div class="pageNumber">
        <form class="pageNumberPrevious" action="view_questions/0" method="POST">
            <input type="submit" value="Previous Page"/>
        </form>
        <form class="pageNumberValue" action="view_questions/1" method="POST">
            <input type="number"/>
            <input type="submit" value="Jump"/>
        </form>
        <form class="pageNumberNext" action="view_questions/2" method="POST">
            <input type="submit" value="Next Page"/>
        </form>
    </div>
</body>
</html>