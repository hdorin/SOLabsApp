
<?php
    
    include 'header.php';
?>

<link rel="stylesheet" href="resources/stylesheets/products.css" type="text/css"/>

<main>
    <div class="wrapper">
        <section class="index-links">

            <?php echo 'here comes products soon' ?>
            <!--<?php var_dump($data['products']); ?>-->
        </section>
    </div>
</main>


<div class="product" >
    <a href="#">
    	<h2>Speakers [Good]</h2>
        <img src="resources/images/speakers.jpg" alt="alternative" />
    </a>

    <div class="rightSide">
	    <div id="desc" class="desc" onclick="coll()">
	    	<p><strong>DESCRIPTION</strong>:</p>
	        This is some long text that will not fit in the box. This is some long text that will not fit in the box.
	        This is some long text that will not fit in the box. This is some long text that will not fit in the box.
	        This is some long text that will not fit in the box. This is some long text that will not fit in the box.
	        This is some long text that will not fit in the box. This is some long text that will not fit in the box.
	        This is some long text that will not fit in the box. This is some long text that will not fit in the box.
	    </div>
	    <div class="inner">
	    	<p><strong>Brand</strong>: Shure</p>
	    	<p><strong>Country of provenance</strong>: United States</p>
	        <p><strong>Keywords</strong>: audio device</p>
	        <p><strong>Expiration Date</strong>: 2018-06-17</p>
	    </div>
	    <div class="prices">
		    <p>Current Price - 200$</p>
		    <p>Next Price - 220$</p>
		 </div>
	    <button class="button" style="vertical-align:middle"><span>Bid </span></button>
	</div>
	<br style="clear:both;"/>
</div>


<?php

    include 'footer.php';

?>