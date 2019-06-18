<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Questions</title>

    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/view_questions.css" type="text/css" />
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
                    <input class="userField" name="user_field" type="text" value="' . (!empty($_SESSION['raw_criteria_user']) ? $_SESSION['raw_criteria_user'] : '') . '">
                    <h3>Status</h3>
                    <select name="status_field">';
                        if(!empty($_SESSION["raw_criteria_posted"]) &&strstr($_SESSION["raw_criteria_posted"],"posted")){
                            echo '<option value="posted" selected="selected">Posted</option>';
                        }else{
                            echo '<option value="posted">Posted</option>';
                        }
                        if(!empty($_SESSION["raw_criteria_posted"]) &&strstr($_SESSION["criteria_posted"],"deleted")){
                            echo '<option value="deleted" selected="selected">Deleted</option>';
                        }else{
                            echo '<option value="deleted">Deleted</option>';
                        }
            echo   '</select>
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
        if(!empty($_SESSION['is_admin']) && $_SESSION['is_admin']==true){
            echo '
                    </select>
                    <h3>Validation</h3>
                    <select name="validation_field">
                        <option value="All">All</option>';
                if(!empty($_SESSION["raw_criteria_validation"]) &&strstr($_SESSION["raw_criteria_validation"],"None")){    
                    echo '<option value="None" selected="selected">None</option>';
                }else{
                    echo '<option value="None">None</option>';
                }
                if(!empty($_SESSION["raw_criteria_validation"]) &&strstr($_SESSION["raw_criteria_validation"],"Valid")){    
                        echo '<option value="Valid" selected="selected">Valid</option>';
                }else{
                        echo '<option value="Valid">Valid</option>';
                }
                if(!empty($_SESSION["raw_criteria_validation"]) &&strstr($_SESSION["raw_criteria_validation"],"Invalid")){    
                        echo '<option value="Invalid" selected="selected">Invalid</option>';
                }else{
                        echo '<option value="Invalid">Invalid</option>';
                }
            echo    '</select>
                    <h3>Sort by</h3>
                    <select name="sort_field">
                        <option value="none">None</option>';
                    if(!empty($_SESSION["raw_criteria_sort"]) && strstr($_SESSION["raw_criteria_sort"],"reports_asc")){    
                        echo '<option value="reports_asc" selected="selected">Reports Ascendent</option>';
                    }else{
                        echo '<option value="reports_asc">Reports Ascendent</option>';
                    }
                    if(!empty($_SESSION["raw_criteria_sort"]) &&strstr($_SESSION["raw_criteria_sort"],"reports_desc")){    
                        echo '<option value="reports_desc" selected="selected">Reports Descendent</option>';
                    }else{
                        echo '<option value="reports_desc">Reports Descendent</option>';
                    }
                    if(!empty($_SESSION["raw_criteria_sort"]) &&strstr($_SESSION["raw_criteria_sort"],"date_asc")){    
                        echo '<option value="date_asc" selected="selected">Date Ascendent</option>';
                    }else{
                        echo '<option value="date_asc">Date Ascendent</option>';
                    }
                    if(!empty($_SESSION["raw_criteria_sort"]) &&strstr($_SESSION["raw_criteria_sort"],"date_desc")){    
                        echo '<option value="date_desc" selected="selected">Date Descendent</option>';
                    }else{
                        echo '<option value="date_desc">Date Descendent</option>';
                    }
            echo    '</select>
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
    <?php echo $data['page_controls'];
    ?>
</body>
</html>