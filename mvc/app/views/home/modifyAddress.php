<?php
    
    include 'header.php';


?>

<!--<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100' rel='stylesheet' type='text/css'>-->
<link rel="stylesheet" href="resources/stylesheets/add_forms.css" type="text/css" />

<p id="responseMsg"><?=$data['resp']?></p>

<form action="modifyAddress/process" method="POST" class="credit-card">
  <div class="form-header">
    <h4 class="title">Modify your address</h4>
  </div>
 
  <div class="form-body">
    <input type="hidden" name="id" value="<?php if (isset($_POST['id']))echo $_POST['id']; ?>"/>
 
    <!-- Name -->
    <input name="name" type="text" class="card-number" 
    value="<?php if (isset($_POST['name']))echo $_POST['name']; else echo 'Name'; ?>">

    <!-- Phone Number -->
    <input name="phoneno" type="text" class="card-number" 
    value="<?php if (isset($_POST['phone_no']))echo $_POST['phone_no']; else echo 'Phone Number'; ?>">
 
    <!-- Address -->
    <input name="address" type="address" class="card-number" 
    value="<?php if (isset($_POST['address']))echo $_POST['address']; else echo 'Address'; ?>">

    <!-- City -->
    <input name="city" type="text" class="card-number" 
    value="<?php if (isset($_POST['city']))echo $_POST['city']; else echo 'City'; ?>">
 
    <!-- County -->
    <input name="county" type="text" class="card-number" 
    value="<?php if (isset($_POST['county']))echo $_POST['county']; else echo 'County'; ?>">
    
    <!-- Country -->
    <input name="country" type="text" class="card-number" 
    value="<?php if (isset($_POST['country']))echo $_POST['country']; else echo 'Country'; ?>">
 	
    <!-- Buttons -->
    <input value="Proceed" type="submit" class="proceed-btn" />
  </div>
</form>

 <?php

include 'footer.php';

?>