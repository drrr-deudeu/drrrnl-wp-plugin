<div id="drrrnl_form_p">
<form action=<?php echo (get_permalink().'#drrrnl_form_p'); ?> id="drrnl_form" method="post">
	<div class="drrrnl_flexbox">
		<div class="tr_drrrnl_form">
			<span class="ln">
				<label for="drrrnl_LastName">Nom&nbsp&nbsp&nbsp&nbsp:</label>
			</span>
			<input type="text" name="drrrnl_LastName" value="<?php echo get_option('drrrnl_value_lastname') ?>" id="drrrnl_LastName" class="requiredfield" />
			<div class="drrrnl_error"><?php echo get_option('drrrnl_error_lastname') ?></div>
		</div>
		<div class="tr_drrrnl_form">
			<span class="fn">
				<label for="drrrnl_FirstName">Pr&eacute;nom:</label>
			</span>
			<input type="text" name="drrrnl_FirstName" value="<?php echo get_option('drrrnl_value_firstname') ?>" id="drrrnl_FirstName" class="requiredfield" />
			<div class="drrrnl_error"><?php echo get_option('drrrnl_error_firstname') ?></div>
		</div>
		<div class="tr_drrrnl_form">
			<span class="ml">
				<label class="" for="drrrnl_Email">Email&nbsp&nbsp&nbsp:</label>
			</span>
			<input type="text" name="drrrnl_Email" value="<?php echo get_option('drrrnl_value_email') ?>" id="drrrnl_Email" class="requiredfield" />
			<div class="drrrnl_error"><?php echo get_option('drrrnl_error_email') ?></div>
		</div>
		<div class="ck_drrrnl_form">
			<label class="label_ck" for="drrrnl_Pro">Vous &ecirctes un Professionnel :</label>
			<input type="checkbox" name="drrrnl_Pro" id="drrrnl_Pro" />
		</div>
	</div>
	<div class="buttons">
		<!-- input type="hidden" name="submitted" id="submitted"/-->
		<button type="submit" name="drrrnl_submit" id="drrrnl_submit">Envoyer</button>
	</div>
</form> 
</div>
