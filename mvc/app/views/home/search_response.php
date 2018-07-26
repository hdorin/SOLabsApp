<!DOCTYPE html>

<html lang="en">
<head>
    <link rel="stylesheet" href="resources/stylesheets/app.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/searchbar.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/abt.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/products.css" type="text/css" />
</head>
<body>

<?php
    $srv = $_SERVER['QUERY_STRING'];
    if (strpos ($_SERVER['QUERY_STRING'], "&src")) {
        $q = explode ("&", $srv);
        $Bad_cond = $_GET["Q1"];
        $Good_cond = $_GET["Q2"];
        $New_cond = $_GET["Q3"];
        $Min_value=$_GET["MN"];
        $Max_value=$_GET["MA"];
        $country=$_GET["CTCHO"];
        $TEST_COND=$Bad_cond.$Good_cond.$New_cond;
        if($TEST_COND == ''){
            $Bad_cond="Bad";
            $Good_cond="Good";
            $New_cond="Like New";
        }else {
            if($Bad_cond!="Bad"&&$Bad_cond!="")$Bad_cond="Bad";
            if($Good_cond!="Good"&&$Good_cond!="")$Good_cond="Good";
            if($New_cond!="Like New"&&$New_cond!="")$New_cond="Like New";
        }

        if($Max_value==''){
            $Max_value=999999;
        }
        if($Min_value==''){
            $Min_value=0;
        }
        if($Max_value<$Min_value){
            $ss=$Max_value;
            $Max_value=$Max_value;
            $Min_value=$ss;
        }
//        echo $Good_cond;
        $conn = new mysqli("localhost", "root", "", "auctiox_db");
        if ($conn->connect_error) {
            die ("Connection Failed");
        } else {


            $t = '%' . $_GET['src'] . '%';
            $t = str_replace (" ", "%", $t);
            if ($country=="NONE") {
                $stmt = $conn->prepare ('SELECT * FROM products WHERE (keywords LIKE ?  OR title LIKE ?)AND (`condition`=? OR `condition`=? OR `condition`=?) AND `curr_price`>=? AND `curr_price`<=?');
                $stmt->bind_param ("sssssii", $t,$t,$Good_cond,$Bad_cond,$New_cond,$Min_value,$Max_value);
            }
            else{

                $stmt = $conn->prepare ('SELECT * FROM products WHERE (keywords LIKE ?  OR title LIKE ?)AND (`condition`=? OR `condition`=? OR `condition`=?) AND `curr_price`>=? AND `curr_price`<=? AND `country`=?');
                $stmt->bind_param ("sssssiis", $t,$t,$Good_cond,$Bad_cond,$New_cond,$Min_value,$Max_value,$country);
            }
            $stmt->execute ();
            $results = $stmt->get_result ();
            if ($results->num_rows > 0) {
                echo '<div class="resultz">';
                foreach ($results as $row) {
                    echo'<div class="product" >';
                    echo '<a href="#">';
                    echo '<h2>'.$row["title"]."[".$row["condition"]."]</h2>";
                    echo '<img src="resources/images/'.$row["image"].'"'.' alt="alternative" /></a>';
                    echo '<div class="rightSide">';
                    echo '<div id="desc" class="desc" onclick="coll()">';
                    echo '<p><strong>DESCRIPTION</strong>:</p>';
                    echo $row['description'];
                    echo '</div>';
                    echo '<div class="inner">';
                    echo '<p><strong>Brand</strong>:'.$row['brand'].'</p>';
                    echo '<p><strong>Country of provenance</strong>:'.$row['country'].'</p>';
                    echo '<p><strong>Keywords</strong>:'.$row['keywords'].'</p>';
                    echo '<p><strong>Expiration Date</strong>:'.$row['expires_on'].'</p>';
                    echo '</div>';
                    echo '<div class="prices">';
                    echo '<p>Current Price - '.$row['curr_price' ].'$</p>';
                    echo '<p>Next Price - '.$row["next_price"].'$</p>';
                    echo '</div>';
                    echo '<button class="button" style="vertical-align:middle"><span>Bid </span></button>';
                    echo '</div>';
                    echo '<br style="clear:both;"/>';
                    echo '</div>';
                }
                echo '</div>';
            }
            else echo "No Items Found with such criteria";

        }
    }
    else echo "something went wrong";
?>

</body>
</html>
