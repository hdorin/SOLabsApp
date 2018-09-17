<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapters</title>

    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/chapters.css" type="text/css" />
    </head>
<body>

<?php
    include "header.php"
?>
    <div class="chaptersBox">
        <?php 
            for($i=0;$i<$data['chapters_nr'];$i++){
                echo $data['chapters'][$i];
            }
        ?>
    </div>
</body>
</html>