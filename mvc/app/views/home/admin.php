<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Admin Page</title>
    <link rel="stylesheet" href="resources/stylesheets/admin.css" type="text/css" />
</head>
<body>
    <div class="chaptersBox">
        <h2>Chapters</h2>
        <?php 
            for($i=0;$i<$data['chapters_nr'];$i++){
                echo $data['chapters'][$i];
            }
        ?>
    </div> 
    <div class="adminsBox">
        <h2>Admins</h2>
        <?php 
            for($i=0;$i<$data['admins_nr'];$i++){
                echo $data['admins'][$i];
            }
        ?>
        <form class="addAdmin" action="admin/add_admin" method="POST">
                <h3>Add admin by user_name</h3>
                <input type="text" name="user_field" required >
                <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>

