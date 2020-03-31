<?php
session_start();
$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
			if (file_exists($root.'/wp-load.php')) {
			require_once($root.'/wp-load.php');
}
include('header.php');
include('footer.php'); 
require_once dirname(dirname(__FILE__)).'/assets/GoogleCalendar/google-api-php-client/src/Google_Client.php';
$staff_id= get_current_user_id();
$staff = new octabook_staff();
$staff->staff_id=$staff_id;
$_SESSION['staff_id']=$staff_id;
$oct_gc_staff = $staff->readOne_gc_staff_setting();
/* $oct_get_option_staff = $staff->get_staff_option("gc_status",$_SESSION['staff_id']); */

?>
<html>
<head>
</head>
<body>
<?php
	$GcclientID = $oct_gc_staff[0]->gc_client_id;
	$GcclientSecret = $oct_gc_staff[0]->gc_client_secret;
	$GcEDvalue = $oct_gc_staff[0]->gc_status;
?>
	<form id="oct" method="post" type="" class="oct-google-calendar" >
					<div class="panel panel-default">
			<!-- 			<div class="panel-heading oct-top-right">
							<h1 class="panel-title"><?php //echo __("Google Calendar","oct");?></h1>
						</div> -->
						<div class="panel-body">
									<table class="form-inline oct-common-table mt-50">
								<tbody>
									<tr>
										<td><?php echo __("Add Appointments To Google Calendar","oct");?></td><td><input class="staff_gc_status" data-on='<?php echo __("Yes","oct");?>' data-off='<?php echo __("No","oct");?>' data-onstyle="primary" data-offstyle="default" data-toggle="toggle" type="checkbox" name="gc_status" <?php if($oct_gc_staff[0]->gc_status=='Y'){ echo "checked";} ?> /></td>
									</tr>
									<tr>
										<td><?php echo __("Google Calender ID","oct");?></td>	
										<td>
											<div class="form-group">
												<input type="text" class="form-control" size="35" id="staff_gc_id" name="gc_id"  placeholder="<?php echo __("Your Calender ID","oct");?>" value="<?php echo $oct_gc_staff[0]->gc_id;?>" />
											</div>
										</td>
									</tr>
									<tr>
										<td><?php echo __("Google Calender Client ID","oct");?></td>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" size="35" id="staff_gc_client_id" name="gc_client_id"   placeholder="<?php echo __("Your Client ID","oct");?>" value="<?php echo $oct_gc_staff[0]->gc_client_id;?> "/>
											</div>
										</td>
									</tr>
									<tr>
										<td><?php echo __("Google Calender Client Secret","oct");?></td>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" size="35" id="staff_gc_client_secret"  name="gc_client_secret"   placeholder="<?php echo __("Your Client Secret ID","oct");?>" value="<?php echo $oct_gc_staff[0]->gc_client_secret;?>" />
											</div>
										</td>
									</tr>
									<tr>
										<td><?php echo __("Google Calender Frontend URL","oct");?></td>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" size="35" id="staff_gc_frontend_url" name="gc_frontend_url"  placeholder="<?php echo __("Your Frontend URL","oct");?>" value="<?php echo $oct_gc_staff[0]->gc_frontend_url;?>" />
											</div>
										</td>
									</tr>
									<tr>
										<td><?php echo __("Google Calender Admin URL","oct");?></td>
										<td>
											<div class="form-group">
												<input type="text" class="form-control" size="35" id="staff_gc_admin_url"  name="gc_admin_url"  placeholder="<?php echo __("Your Admin URL","oct");?>" value="<?php echo $oct_gc_staff[0]->gc_admin_url;?>" />
											</div>
										</td>
									</tr>
									<tr>
										<td><?php echo __("Two Way Sync","oct");?></td><td><input class="appointup_gc_twowaysync" data-on='<?php echo __("Yes","oct");?>' data-off='<?php echo __("No","oct");?>' data-onstyle="primary" data-offstyle="default"  data-toggle="toggle" type="checkbox" name="gc_twowaysync" <?php if($oct_gc_staff[0]->gc_status_sync_configure=='Y'){ echo "checked";} ?>/></td>
									</tr>
									<?php
									 if($GcclientID!='' &&	$GcclientSecret!='' &&	$GcEDvalue=='Y'){
							
										 $client = new Google_Client();
										 $client->setApplicationName('OctaBook Google Calender');
										 $client->setClientId($GcclientID);
										 $client->setClientSecret($GcclientSecret);
										 $client->setRedirectUri($oct_gc_staff[0]->gc_admin_url);
										 $client->setDeveloperKey($GcclientID);
										 $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/calendar','https://www.google.com/calendar/feeds/'));
										 $client->setAccessType('offline');
										 $client->setApprovalPrompt( 'force' );
										 
										 if(isset($_GET['GC_action']) && $_GET['GC_action']=='gcd'){
											$revokeaccesstoken = $oct_gc_staff[0]->gc_token;
											$client->revokeToken($revokeaccesstoken);
											$staff->update_staff_option('gc_token', '', $_SESSION['staff_id']);
											$staff->update_staff_option('gc_status_configure', "N", $_SESSION['staff_id']);
											header('Location:'.site_url().'/wp-admin/admin.php?page=google_calander_submenu');
										 }
										 if(isset($_GET['code']) && $_GET['code']!=''){
											$access_token =  $client->authenticate($_GET['code']);
											$staff->update_staff_option("gc_token",$access_token,$_SESSION['staff_id']);
											$staff->update_staff_option("gc_status_configure","Y",$_SESSION['staff_id']);
											header('Location:'.site_url().'/wp-admin/admin.php?page=google_calander_submenu');
										 }
										  $curlcalenders = curl_init();
						 				  curl_setopt_array($curlcalenders, array(
										  CURLOPT_RETURNTRANSFER => 1,
										  CURLOPT_URL => site_url().'/wp-content/plugins/octabook/assets/GoogleCalendar/callist_provider.php?pid='.$staff_id,
										  CURLOPT_FRESH_CONNECT =>true,
										  CURLOPT_USERAGENT => 'OctaBook'
										  ));					 
										  $response = curl_exec($curlcalenders);	
										
										 curl_close($curlcalenders);							 
										 if(isset($response)){
										  $calenders = json_decode($response);
										 }else{
										  $calenders = array();
										 }
									 }
									 if(count((array)$calenders)==0){
									?>
									<tr>
										<td></td>
										<td>
											<?php
												if($client != ''){
											 $authUrl = $client->createAuthUrl();
												print"<a class='verify_gc_staff_account' style='color:#1E8CBE' href='javascript:void(0)' data-staff_hreflink='$authUrl'>Verify Account</a>"; }?>
										</td>
									</tr>
									<?php  }else{ ?>
									<tr>
										<td><?php echo __("Select Calendar","oct");?></td>
										<td><select name="staff_appointup_gc_id" class="staff_appointup_gc_id"><?php	
										for($i=0;$i<count((array)$calenders);$i++){
											foreach($calenders[$i] as $calinfo){
												$calenderInfo = explode('##==##',$calinfo);
												$selected='';
												if($oct_gc_staff[0]->gc_id==$calenderInfo[1]){ $selected="selected";}
												echo "<option ".$selected." value='".$calenderInfo[1]."'>".$calenderInfo[0]."</option>";
											}
										}
										?></select> <a style="text-decoration:underline;color:#1E8CBE;" href="<?php echo site_url();?>/wp-admin/admin.php?page=google_calander_submenu&GC_action=gcd"><?php echo __("Disconnect","oct");?></a></td>
									</tr><?php
									}   ?>									
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td>
										<?php if(sizeof((array)$oct_gc_staff[0])>0){
										?>
										<a id="update_save_gc_settings" data-staff_id="<?php echo $staff_id;?>" name="" class="btn btn-success"><?php echo __("Save Setting","oct");?></a>
										<?php
										}else{?>
											<a id="save_gc_settings" data-staff_id="<?php echo $staff_id;?>" name="" class="btn btn-success"><?php echo __("Save Setting","oct");?></a>
										<?php } ?>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
	</form>
			
</body>
</html>