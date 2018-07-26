<?php
    
    include 'header.php';


?>

<!--<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,100' rel='stylesheet' type='text/css'>-->
<link rel="stylesheet" href="resources/stylesheets/add_forms.css" type="text/css" />

<p id="responseMsg"><?=$data['resp']?></p>

<form action="modifyCreditCard/process" method="POST" class="credit-card">
  <div class="form-header">
    <h4 class="title">Modify your credit card</h4>
  </div>
 
  <div class="form-body">
  	<!-- Name -->
  	<input name="ccname" type="text" class="card-number" placeholder="Name">

    <!-- Card Number -->
    <input name="ccnumber" type="text" class="card-number" value="<?php if (isset($_POST['ccno']))echo $_POST['ccno']; else echo 'Card Number'; ?>" 
    readonly >
 
    <!-- Date Field -->
    <div class="date-field">
      <div class="month">
        <select name="ccmonth">
          <option value="january">January</option>
          <option value="february">February</option>
          <option value="march">March</option>
          <option value="april">April</option>
          <option value="may">May</option>
          <option value="june">June</option>
          <option value="july">July</option>
          <option value="august">August</option>
          <option value="september">September</option>
          <option value="october">October</option>
          <option value="november">November</option>
          <option value="december">December</option>
        </select>
      </div>
      <div class="year">
        <select name="ccyear">
          <option value="2019">2019</option>
          <option value="2020">2020</option>
          <option value="2021">2021</option>
          <option value="2022">2022</option>
          <option value="2023">2023</option>
          <option value="2024">2024</option>
          <option value="2025">2025</option>
          <option value="2026">2026</option>
          <option value="2027">2027</option>
          <option value="2028">2028</option>
          <option value="2029">2029</option>
          <option value="2030">2030</option>
        </select>
      </div>
    </div>
 
    <!-- Card Verification Field -->
    <div class="card-verification">
      <div class="cvv-input">
        <input name="ccver" type="text" placeholder="CVV">
      </div>
      <div class="cvv-details">
      </div>
    </div>
 	
    <!-- Buttons -->
    <input value="Proceed" type="submit" class="proceed-btn" />
  </div>
</form>

 <?php

include 'footer.php';

?>