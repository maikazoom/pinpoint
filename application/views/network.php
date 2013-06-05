 <div class="container">
		        <div id="myCarousel" class="carousel slide access">
		          <div class="carousel-inner">
        
		    		<div class="item active access">
		              <img src="img/background_access_thenetwork.jpg" alt="">
		              <div class="container">
		                <div class="carousel-caption"><!--<h1 class="fontface" data-animate="rollIn">ACCESS high quality <br/>consumers</h1>-->
		                  <p class="lead span5">Our exclusive network of top Tanzania websites and blogs delivers 25 Million ad impressions to over <span class="label label-warning">1.3 Million</span> different people each month.
		    </p></div>
	    </div>
		       </div></div></div>  </div>  
		       
		        <div class="clearfix"></div>    
			    
			    
		<!-- /.row fluid for ad and format gallery -->
			    
    <div class="row-fluid span9 offset3">
	   <div class="container gapped padded"> 
	   	<?php if(!isset($details)): ?>
		   <div class=" span7"><h2>The Network </h2><p class="style5">If they are online in Tanzania, we can reach them for you.  Our exclusive network of 27 top Tanzanian digital publishers provides unprecedented access to the most desirable market segments.  Mouse over the logos below for brief description about each site.
  			</p><a href="contact"><button class="btn-small btn-success">Contact Us</button></a>
		   </div>
		<?php else: ?>
			<?php if($sites->num_rows() > 0): ?>
			<?php $title = $sites->row()->name; ?>
			<?php $bundle_description = $sites->row()->bundle_description; ?>
			 <div class=" span7"><h2><?php echo $title; ?></h2><p class="style5"><?php strip_tags($bundle_description); ?>
  			</p><a href="contact"><button class="btn-small btn-success">Contact Us</button></a>
		   </div>
			<?php else: ?>
			<?php $title = $bundle->name; ?>
			<?php $bundle_description = $bundle->description; ?>
			 <div class=" span7"><h2><?php echo $title; ?></h2><p class="style5"><?php strip_tags($bundle_description); ?>
  			</p><a href="contact"><button class="btn-small btn-success">Contact Us</button></a>
		   </div>
			<?php endif; ?>

		<?php endif; ?>
	   </div>
		   <div class="clearfix"></div>
		 
		<?php 
			$j = $i = 0; 

			$all = $sites->num_rows();
		?>
		<?php if($all > 0): ?>
		<?php foreach($sites->result() as $site): ?>
		<?php if($i==0): ?>
			<div class="container gapped padded">
		<?php endif; ?>   
				<div class="thumbnail span2">
					<a href="<?php echo current_url() . "#" . str_replace("-","",url_title($site->title)); ?>" id="<?php echo str_replace("-","",url_title($site->title)); ?>" data-placement="top" rel="popover">
<!-- 						<?php if($site->logo) $logo = $site->logo; else $logo="no_logos.jpg" ?>
						<img src="img/<?php echo $logo ?>"></a> -->
					<?php if($site->logo): ?>
						<img src="img/<?php echo $site->logo ?>">
					<?php else: ?>
						<?php echo $site->title; ?>
					<?php endif; ?>

					<p>
						<br/>
						<a href="<?php echo $site->url ?>" target = "_blank" class="exception"><?php echo str_replace('http://','',$site->url); ?></a>
					</p>
		
				</div>
   	    <?php $i++; ?>
   	    <?php $j++; ?>


		<?php if($i==4 or $j==$all): ?>
		<?php $i=0 ?>
   	     	</div>
   	     <?php endif; ?>  
   	    <?php endforeach; ?>
   		<?php else: ?>
   			<p>No Sites in This Bundle</p>
   	    <?php endif; ?>  
   	    

    </div>
   <!-- end of row fluid ad gallery -->
   
    <!-- Placed at the end of the document so the pages load faster -->
	<script type='text/javascript' src='js/jquery.js'></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
   
    <!--script for tooltip popover-->
    <script>

    <?php foreach($sites->result() as $site): ?>
    	 
	    $(function ()
	    { $("#<?php echo str_replace("-","",url_title($site->title)); ?>").popover(
	    	{
	    		title: "<?php echo $site->title ?>", 
	    		content:"<?php  if($site->text) echo trim(str_replace('&nbsp;',' ',strip_tags($site->text))); else echo trim(str_replace('&nbsp;',' ',strip_tags($site->main_site_description)));  ?>"
	    	});
	    });
    <?php endforeach; ?>
    
    </script>

    <?php  ?>