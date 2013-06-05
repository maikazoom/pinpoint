<style>
	.mainInfo {
	background: #000; 
	color: #fff;
	border-radius: 10px;
	padding: 10px;
	width:400px;
	}
</style>
<img src="../img/logo.jpg" /><br><Br>
<div class='mainInfo'>

	<div class="pageTitle">Login</div>
    <div class="pageTitleBorder"></div>
	<p>Please login with your email and password below.</p>
	
	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("auth/login");?>
    	
      <p>
      	<label for="identity">Email:</label>
      	<?php echo form_input($identity);?>
      </p>
      
      <p>
      	<label for="password">Password:</label>
      	<?php echo form_input($password);?>
      </p>
      
      <p>
	      <label for="remember">Remember Me:</label>
	      <?php echo form_checkbox('remember', '1', FALSE);?>
	  </p>
      
      
      <p><?php echo form_submit('submit', 'Login');?></p>

      
    <?php echo form_close();?>
</div>
	<?php if (!$this->config->item('allow_registration', 'tank_auth')) echo anchor('register/', 'Register'); ?>
