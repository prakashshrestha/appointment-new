<ul class="nav nav-pills nav-stacked">
  <li role="presentation" <?php if($_GET['page']=='settings_submenu'){ ?>class="active" <?php } ?>><a href="?page=settings_submenu"><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;<?php echo __("General","oct");?></a></li>
  <li role="presentation" <?php if($_GET['page']=='appearance_submenu'){ ?>class="active" <?php } ?> ><a href="?page=appearance_submenu"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;<?php echo __("Appearance ","oct");?></a></li>
  <li role="presentation" <?php if($_GET['page']=='payment_settings_submenu'){ ?>class="active" <?php } ?>><a href="?page=payment_settings_submenu"><span class="glyphicon glyphicon-usd"></span>&nbsp;&nbsp;<?php echo __("Payment ","oct");?></a></li>
   <li role="presentation" <?php if($_GET['page']=='email_settings_submenu'){ ?>class="active" <?php } ?>><a href="?page=email_settings_submenu"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;<?php echo __("Email ","oct");?></a></li>
  <li role="presentation" <?php if($_GET['page']=='email_template_submenu' || $_GET['page']=='email_template_settings_submenu' ){ ?>class="active" <?php } ?>><a href="?page=email_template_submenu"><span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;<?php echo __("Email Templates","oct");?></a></li>
  <li role="presentation" <?php if($_GET['page']=='reminder_sms_submenu'){ ?>class="active" <?php } ?> ><a href="?page=reminder_sms_submenu"><span class="glyphicon glyphicon-phone"></span>&nbsp;&nbsp;<?php echo __("Reminder SMS","oct");?></a></li>
  <li role="presentation" <?php if($_GET['page']=='list_coupons_submenu' || $_GET['page']=='coupons_submenu' ){ ?>class="active" <?php } ?>><a href="?page=list_coupons_submenu"><span class="glyphicon glyphicon-tag"></span>&nbsp;&nbsp;<?php echo __("Discount Coupons","oct");?></a></li>
  
  <li role="presentation" <?php if($_GET['page']=='manage_form_submenu'){ ?>class="active" <?php } ?> ><a href="?page=manage_form_submenu"><span class="glyphicon glyphicon-align-left"></span>&nbsp;&nbsp;<?php echo __("Form Fields","oct");?></a></li>
</ul>