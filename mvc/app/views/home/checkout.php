<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Checkout</title>
    <link rel="stylesheet" href="../resources/stylesheets/checkout.css" type="text/css" />
    <link rel="apple-touch-icon" sizes="180x180" href="../resources/images/favicon/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../resources/images/favicon/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="../resources/images/favicon/favicon-16x16.png" />
</head>
<body id="bodyMain">

    <div id="checkoutBox">
        <div id="siteLogoContainer">
            <img id="siteLogo" src="../resources/images/logo.jpg" alt="Site logo" />
        </div>
        <div id="heading1">
            <h1>Checkout</h1>
        </div>
        <div id="infoBox">
            <div id="productImage">
                <img src="<?=$data['productImage']?>" alt="Product Image" />
            </div>
            <div id="detailsList">
                <ul>
                    <li><mark>Product title:</mark> <?=$data['productTitle']?></li>
                    <li><mark>Description:</mark> <?=$data['productDesc']?></li>
                    <li><mark>Condition:</mark> <?=$data['productCondition']?></li>
                    <li><mark>Total Price:</mark> <?=$data['productPrice']?></li>
                </ul>
            </div>


        </div>
        <div id="paypalBox">
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="item_name" value='<?=$data['productTitle']?>'> <!--Name of the product-->
            <input type="hidden" name="item_number" value=<?=$data['productId']?>> <!--ID of the product-->
            <input type="hidden" name="hosted_button_id" value="HRNYJXXCDTPR6">
            <table>
            <tr><td><input type="hidden" name="on0" value="ShippingAddressId">Shipping Address</td></tr><tr><td><select name="os0">
            <?php
                $link = $this->auctiox_db_connect();
                        $sql = $link->prepare('SELECT id,address,city,county,country FROM addresses WHERE user_id = ?');
                        $sql->bind_param('s', $data['winnerId']);
                        $sql->execute();

                        $addressId = null; $addressName = null; $addressCity=null; $addressCounty=null; $addressCounty=null;
                        $sql->bind_result($addressId,$addressName,$addressCity,$addressCounty,$addressCountry);

                        while ($sql->fetch()) {
                            echo '<option value=' . $addressId . '>' . $addressName . ' (' . $addressCity . ',' . $addressCounty . ',' . $addressCountry . ')' .'</option>';
                        }
            ?>
            </select> </td></tr>
            <tr><td><input type="hidden" name="on1" value="WinnerID"></td></tr><tr><td><input type="hidden" name="os1" maxlength="200" value=<?=$data['winnerId']?>></td></tr>
            <tr><td><input type="hidden" name="on2" value="ProductId"></td></tr><tr><td><input type="hidden" name="os2" maxlength="200" value=<?=$data['productId']?>></td></tr>
            </table>
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>

        </div>

    </div>
</body>
</html>