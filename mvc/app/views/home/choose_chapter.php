<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Chapters</title>

    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/choose_chapter.css" type="text/css" />
    </head>
<body>

<?php
    include "header.php"
?>
    
    <div class="chaptersBox">
        <h1><?php echo $data['title_message'] ?></h1>
        <?php 
            for($i=0;$i<=$data['chapters_nr'];$i++){
                echo $data['chapters'][$i];
            }
        ?>
    </div>
</body>
</html>