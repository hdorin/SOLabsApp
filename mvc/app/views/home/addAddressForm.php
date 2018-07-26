<?php
    
    include 'header.php';


?>

<!--<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100' rel='stylesheet' type='text/css'>-->
<link rel="stylesheet" href="resources/stylesheets/add_forms.css" type="text/css" />

<p id="responseMsg"><?=$data['resp']?></p>

<form action="addAddressForm/process" method="POST" class="credit-card">
  <div class="form-header">
    <h4 class="title">Add a new address</h4>
  </div>
 
  <div class="form-body">
  	<!-- Name -->
  	<input name="name" type="text" class="card-number" placeholder="Name" required>

    <!-- Phone Number -->
    <input name="phoneno" type="text" class="card-number" placeholder="Phone Number" required>
 
    <!-- Address -->
    <input name="address" type="address" class="card-number" placeholder="Address" required>

    <!-- City -->
    <input name="city" type="text" class="card-number" placeholder="City" required>
 
    <!-- County -->
    <input name="county" type="text" class="card-number" placeholder="County" required>
 	  
    <!-- Country -->
    <input name="country" type="text" class="card-number" placeholder="Country" required>

    <!-- Buttons -->
    <input value="Proceed" type="submit" class="proceed-btn" />
  </div>
</form>

 <?php

include 'footer.php';

?>