<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>AuctioX</title>

    <link rel="stylesheet" href="resources/stylesheets/app.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/searchbar.css" type="text/css" />
    <link rel="stylesheet" href="resources/stylesheets/abt.css" type="text/css" />


    <link rel="apple-touch-icon" sizes="180x180" href="resources/images/favicon/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="resources/images/favicon/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="resources/images/favicon/favicon-16x16.png" />
    <link rel="manifest" href="resources/images/favicon/site.webmanifest" />
    <link rel="mask-icon" href="resources/images/favicon/safari-pinned-tab.svg" color="#5bbad5" />
    <meta name="msapplication-TileColor" content="#da532c" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        function showProducts(str) {
            if (str === "") {
                document.getElementById("txtHint").innerHTML = "";
                return;
            }
            let xmlhttp;
            if (window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
            else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "user_items?q=" + str, true);
            xmlhttp.send();
        }
        function showFeed(str) {
            if (str.length === 0) {
                document.getElementById("rssOutput").innerHTML = "";
                return;
            }
            let xmlhttp;
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {  // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById("rssOutput").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "generate_feed?q=" + str, true);
            xmlhttp.send();
        }
        </script>
</head>

<body>
    <header>
        <div class="topnav">
            <div class="firstrow">
                <a href="index"><img id="logo" src="resources/images/logo.jpg" alt="Site Logo" /></a>

                <div class="searchbarUnit" >
                    <form class="form-wrapper" action="advs">
                        <input type="text" placeholder="What are you looking for?" name="src" id="src" required>
                        <button  type="submit" onclick="showProducts()"  >Search</button>
                    </form>
                </div>
                
                <a id="advsearch" href="advs">Advanced search</a>
                <?php
                    if(isset($_SESSION['userId'])==false){
                        echo '<a id="btnLogin" href="login">Login</a>
                        <a id="btnReg" href="register">Register</a>';
                    }else{
                        echo '<p id="welcome">'.' Welcome, '. $_SESSION["userName"] . '!</p>
                        <a href="logout" id="btnLogout">Logout</a>';
                    }
                ?>
                
                
            </div> <!--FIRST ROW-->

            <div id="menu_tab">
                <div class="left_menu_corner"></div>
                <ul class="menu">
                    <li><a href="index" class="nav1"> Home</a></li>
                    <li class="divider"></li>
                    <!--Drop down menue-hover for products START-->
                    <!--All of the .css stuff is in the abt.css folder-->

                    <li class="dropdown1">
                        <Button class="dropbtn1">Products</Button>
                        <div class="dropdown-content1">
                            <a href="products">See feed</a>
                            <a href="export/xml">Export XML</a>
                            <a href="products">Link 3</a>
                        </div>
                    </li>
                    <!--Drop down menue-hover for products END-->
                    <li class="divider"></li>
                    <li><a href="#" class="nav3">Specials</a></li>
                    <li class="divider"></li>
                    <li><a href="#" class="nav4">Last Minute!</a></li>
                    <li class="divider"></li>
                    <li><a href="myAccount" class="nav5">My account</a></li>
                    <li class="divider"></li>
                    <li><a href="myitems" class="nav6">My Items</a></li>
                    <li class="divider"></li>
                    <!-- <li><a href="#" class="nav7">Info</a></li>-->
                    <!--Drop down menue-hover for products START-->
                    <!--All of the .css stuff is in the abt.css folder-->

                    <li class="dropdown2">
                        <Button class="dropbtn2">  Info  </Button>
                        <div class="dropdown-content2">
                            <a href="contact">Contact Us</a>
                            <a href="about">About Us</a>
                            <a href="shipping">Shipping</a>

                        </div>
                    </li>
                    <!--Drop down menue-hover for products END-->
                    <li class="divider"></li>
                    <li class="currencies">
                        Currencies
                        <select>
                            <option>US Dollar</option>
                            <option>Euro</option>
                        </select>
                    </li>
                </ul>
                <div class="right_menu_corner"></div>
            </div><!--MENU_TAB-->

        </div><!--TOPNAV-->


    </header>