<?php

    include 'header.php';
?>
	
    <link rel="stylesheet" href="resources/stylesheets/myitems.css" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <script>
    	$(document).on("click", 'div button', function(){
    		$('div button').removeClass('active');
    		$(this).addClass('active');
		});
    </script>


        <div class="products_wrapper">
                <button class="tab" onclick="showProducts(this.value)" value="my_items">My Won Items</button>
                <button class="tab" onclick="showProducts(this.value)" value="my_uploaded_items">My Uploaded Items</button>
        </div>


    <p id="responseMsg"><?=$data['resp']?></p>

    <div id="txtHint">

    </div>

    

<?php

    include 'footer.php';

?>