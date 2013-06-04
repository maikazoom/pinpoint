<div style = 'clear: none; float: right; width: 655px; font-size:12px; margin-left: 10px;'>
<?php 

$attributes = array ('id'=>'register', 'name'=>'register', 'class'=>"wufoo page", 'autocomplete'=>'off');
echo form_open ('login_user/', $attributes); 

?>
<span style = 'color: red; font-weight: bold;'><?php if(isset($success)) echo $success; ?></span>

<ul>


<li  class="notranslate      ">
<label class="desc" for="email">
Email:
<span id="req_1" class="req">*</span>
</label>
<div>
<input id="email" name="email" type="email" spellcheck="false" class="field text medium validate[custom[email]]" value="" maxlength="255" tabindex="4" /> 
</div>
</li>


<li  class="notranslate      ">
<label class="desc" for="password">
Password:
<span id="req_4" class="req">*</span>
</label>
<div>
<input id="password" name="password" type="password" spellcheck="false" class="field text medium validate[required]" value="" maxlength="255" tabindex="7" /> 
</div>
</li>

<li class="buttons ">
<div>

<input id="saveForm" name="saveForm" class="btTxt submit" type="submit" value=" Login "
 /></div>
</li>

</ul>
<p>Don't have an account with us? <a href = 'register'>Register Here</a></p>
</form> 
	               
	<div style = 'clear: both; padding-top:20px;'></div>
	</div>
	