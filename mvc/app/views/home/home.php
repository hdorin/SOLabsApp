<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Homepage</title>

    <link rel="stylesheet" href="resources/stylesheets/header.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/home.css" type="text/css" />
    </head>
<body>

<?php
    include "header.php"
?>
    <?php 
        if($_SESSION["is_admin"]==true){ echo '
            <form class="addNews" action="home/add_news" method="POST">
                <h3>Add news</h3>
                <textarea type="text" name="text_field" required ></textarea>
                <input type="submit" value="Submit">
            </form>';
        }
    ?>
    <div class="newsBox">
        <?php 
            for($i=$data['news_nr'] -1 ;$i>=0;$i--){
                echo $data['news'][$i];
            }
        ?>
    </div>
</body>
</html>