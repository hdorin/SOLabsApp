<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapters</title>

    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/questions.css" type="text/css" />
    </head>
<body>

<?php
    include "header.php"
?>
    <div class="questionsBox">
        <?php 
            for($i=0;$i<$data['questions_nr'];$i++){
                echo $data['questions'][$i];
            }
        ?>
    </div>
</body>
</html>