<?php 
	include(dirname(__FILE__).'/header.php');
	$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));
	
	/* Create Location */
	$location = new octabook_location();
	$category = new octabook_category();
	$service = new octabook_service();
	$general = new octabook_general();
	$staff = new octabook_staff();
	$schedule = new octabook_schedule();
	$breaks = new octabook_schedule_breaks();
	$schedule_offdays = new octabook_schedule_offdays();
	$ssp = new octabook_service_schedule_price();
	$oct_image_upload= new octabook_image_upload();

/* Get All Services */
$service->location_id = $_SESSION['oct_location'];
$oct_services = $service->readAll();
	
/* Get All WP Users */
$staff->location_id = $_SESSION['oct_location'];
$all_existing_users = $staff->readAll_existing_users();
// print_r($all_existing_users);
$oct_all_staff = $staff->readAll_with_disables();
$location_all_staff = $staff->countAll();
/* Get All Locations */
$location_sortby = get_option('octabook_location_sortby');
$oct_locations = $location->readAll('','','');
$temp_locatio_name = array();
$interval = get_option('octabook_booking_time_interval');
$currstaff_key = 0;
$oct_currency_symbol = get_option('octabook_currency_symbol');
$user_sp='';
$user_sp_manager='';
if(current_user_can('oct_staff') && !current_user_can('manage_options')) {
	$user_sp = 'Y';
}if(current_user_can('oct_manager') && !current_user_can('manage_options')) {
	$user_sp_manager = 'Y';
}
?>
<div id="oct-staff-panel" class="panel tab-content">
	
		<div class="oct-staff-list col-md-3 col-sm-3 col-xs-12 col-lg-3">
			<div class="oct-staff-container">
				<h3><?php echo __("Staff Members","oct");?><span>(<?php echo $location_all_staff;?>)</span>
					<?php if($user_sp!='Y' && $user_sp_manager!='Y'){ ?>
					<button id="oct-add-new-staff" class="pull-right btn btn-circle btn-info" rel="popover" data-placement='bottom' title="<?php echo __("Add New Staff Member","oct");?>"> <i class="fa fa-user-plus custom-icon-space"></i><?php echo __("Add","oct");?></button>
					<?php } ?>
					
					
					<div id="popover-content-wrapper" style="display: none;">
						<div class="arrow"></div>
					 <form id="oct_create_staff" method="post">
					  <table class="form-horizontal" cellspacing="0">
						<tbody>
						<tr>
							<td width="110px">
							<div class="pull-right oct-custom-radio">
								<ul class="custom-staff-width oct-radio-list ">
									<li>
										<input type="radio" id="oct-new-user" class="oct-radio oct-new-usercl" name="staff-new-exist-user" value="N" />
										<label for="oct-new-user"><span></span><?php echo __("New User","oct");?></label>
									</li>
								</ul>
							</div>
							</td>
							<td>
								<div class="pull-left oct-custom-radio">
									<ul class="oct-radio-list">
										<li>
											<input type="radio" checked="checked" id="oct-existing-user" class="oct-radio oct-existing-usercl" name="staff-new-exist-user" value="E" />
										<label for="oct-existing-user"><span></span><?php echo __("Existing User","oct");?></label>
										</li>
									</ul>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="oct-select-user" class="oct-existing-user-data" ><?php echo __("User","oct");?></label>
							</td>
							<td><div class="oct-existing-user-data">	
								<select class="form-control" name="oct_selected_wpuser" id="oct-selected-wp-user">
									<option value=""><?php echo __("Select from WP users","oct");?></option>
									<?php foreach($all_existing_users as $single_existing_user){
                                     ?> 

									<option value="<?php echo $single_existing_user->ID; ?>" ><?php echo $single_existing_user->display_name; ?></option> <?php } ?>
								</select>
								</div>
							</td>
						</tr>
						<tr class="form-field form-required">
							<td><label for=""  class="oct-new-user-data hide-div"><?php echo __("Username","oct");?></label></td>
							<td><div class="oct-new-user-data hide-div">	<input type="text" class="form-control" id="oct-staff-username" name="oct_newuser_username"  /></div></td>
						</tr>
						<tr class="form-field form-required" >
							<td><label for="oct-staff-password"  class="oct-new-user-data hide-div"><?php echo __("Password","oct");?></label></td>
							<td><div class="oct-new-user-data hide-div">	<input type="password" class="form-control" id="oct-staff-password" value=""  name="oct_newuser_password"  /></div></td>
						</tr>
						<tr class="form-field form-required">
							<td><label class="oct-new-user-data hide-div" for="ab-newstaff-fullname"><?php echo __("Full name","oct");?></label></td>
							<td><input type="text" class="form-control oct-new-user-data hide-div" id="oct-staff-fullname" name="oct_newuser_fullname" value=""  /></td>
						</tr>
						<tr class="form-field form-required">
							<td><label class="oct-new-user-data hide-div" for="ab-newstaff-fullname"><?php echo __("Email","oct");?></label></td>
							<td><input type="email" class="form-control oct-new-user-data hide-div" id="oct-staff-email" name="oct_newuser_email" value=""   /></td>
						</tr>
						<tr>
							<td></td>
							<td>
								<a id="oct_create_staff_btn" value="Create Staff" class="btn btn-info" href="javascript:void(0);"><?php echo __("Create","oct");?></a>
								<a id="oct-close-popover-new-staff" class="btn btn-default" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
							</td>
						</tr>
						</tbody>
					</table>
					</form>
					</div>
					
				</h3><!-- end popover -->
				
				<ul class="nav nav-tab nav-stacked oct-left-staff" id="oct-staff-sortable">
					<?php foreach($oct_all_staff  as $oct_staffkey => $oct_staff){ 
					
					
					if($user_sp_manager=='' && $user_sp=='Y' && $oct_staff['id']!=$current_user->ID){continue;}
					if($user_sp_manager=='' && $user_sp=='Y'){
						$currstaff_key = $oct_staffkey;
					}
			
					?>
					
					
					<li class="staff-list br-2" data-staff_id="<?php echo $oct_staff['id']; ?>" id="staff_detail_<?php echo $oct_staff['id']; ?>">
						<a href="javascript:void(0)" data-toggle="pill">
						<!-- <span class="oct-staff-clone"><button class="btn btn-circle btn-success pull-right oct-clone-staff" data-pid="<?php //echo $oct_staff['id']; ?>" title="<?php //echo __("Reset","oct");?>Clone Staff Member"><i class="fa fa-clone"></i></button></span> -->
						<span class="oct-staff-image"><img class="oct-staf-img-small" src="<?php if($oct_staff['image']==''){ echo $plugin_url_for_ajax.'/assets/images/staff.png';}else{
						echo site_url()."/wp-content/uploads".$oct_staff['image'];}?>" /></span>
						<span class="oct-staff-name f-letter-capitalize"><?php echo $oct_staff['staff_name']; ?></span>
						</a>
						<?php if(current_user_can('manage_options')){?>
						<span class="oct-manager-star">
							<input <?php if(isset($oct_staff['caps']['oct_manager'])){ echo "checked='checked'"; } ?> type="checkbox" data-staff_id="<?php echo $oct_staff['id']; ?>" id="oct_staff_manager<?php echo $oct_staff['id']; ?>" class="oct-checkbox oct_staff_manager" />
							<label for="oct_staff_manager<?php echo $oct_staff['id']; ?>" title="<?php echo __("Manager","oct");?>"><span><i class="fa fa-star"></i><br/ ><span class="oct-text"></span></span></label>
						</span><?php }?>
					</li>
					<?php } ?>
				</ul>
			</div>	
		</div>
	
	<div class="panel-body">
		<div class="oct-staff-details tab-content col-md-9 col-sm-9 col-lg-9 col-xs-12">
			<!-- right side common menu for staff -->
			<!--  <?php if(isset($oct_all_staff[$currstaff_key])){
					$service->provider_id = $oct_all_staff[$currstaff_key]['id'];
					if($oct_all_staff[$currstaff_key]['schedule_type']=='M'){$wl_end=5;}else{$wl_end=1;}
										
					$schedule->provider_id = $oct_all_staff[$currstaff_key]['id'];
					$ins_update_status = $schedule->check_sechedule_exist_for_provider();
		
			?>  -->


			<div class="oct-staff-top-header">
				<span class="oct-staff-member-name pull-left f-letter-capitalize" data-staff_id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>">
					<?php echo $oct_all_staff[$currstaff_key]['staff_name'];?></span>
				
				<?php if($user_sp!='Y' && $user_sp_manager!='Y'){ ?>
				<button id="oct-delete-staff-member" class="pull-right btn btn-circle btn-danger" rel="popover" data-placement='bottom' title="<?php echo __("Delete Member?","oct");?>"> <i class="fa fa-trash"></i></button><?php } ?>
				
				
				<div id="popover-delete-member" style="display: none;">
					<div class="arrow"></div>
					<?php if($service->total_staff_services()>0){?>
						<span><?php echo __("Unable to delete staff,having linked services","oct");?></span>
					<?php }else{?>
					<table class="form-horizontal" cellspacing="0">
						<tbody>
							<tr>
								<td>
									<button data-staff_id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" id="delete_staff" value="Delete" class="btn btn-danger" type="submit"><?php echo __("Yes","oct");?></button>
									<button id="oct-close-popover-delete-staff" class="btn btn-default" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
								</td>
							</tr>
						</tbody>
					</table>
					<?php }?>
				</div>
						
			</div>
			<hr id="hr" />
			<ul class="nav nav-tabs nav-justified oct-staff-right-menu">
				<li class="active"><a href="#member-details" data-toggle="tab"><?php echo __("Details","oct");?></a></li>
				<li><a href="#member-services" data-toggle="tab"><?php echo __("Services","oct");?></a></li>
				<li><a href="#member-availabilty" data-toggle="tab"><?php echo __("Availabilty","oct");?></a></li>
				<li><a href="#member-addbreaks" data-toggle="tab"><?php echo __("Add Breaks","oct");?></a></li>
				<li><a href="#member-offtime" data-toggle="tab"><?php echo __("Off Time","oct");?></a></li>
				<li><a href="#member-offdays" data-toggle="tab"><?php echo __("Off Days","oct");?></a></li>
			</ul>
			
			
			<div class="tab-pane active" id="demo-andrew"><!-- first staff nmember -->
			
				<div class="container-fluid tab-content oct-staff-right-details">
					
					<div class="tab-pane active col-lg-12 col-md-12 col-sm-12 col-xs-12 member-details" id="member-details">
						
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
							<div class="oct-member-image-uploader">
						
								<img id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>locimage" src="<?php if($oct_all_staff[$currstaff_key]['image']==''){ echo $plugin_url_for_ajax.'/assets/images/staff.png';}else{
								echo site_url()."/wp-content/uploads".$oct_all_staff[$currstaff_key]['image'];
								}?>" class="oct-staff-image br-100" height="100" width="100">
									<label for="oct-upload-imagebdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" <?php if($oct_all_staff[$currstaff_key]['image']==''){ echo "style='display:block'"; }else{ echo "style='display:none'"; } ?> class="oct-staff-img-icon-label show_image_icon_add<?php echo $oct_all_staff[$currstaff_key]['id']; ?>">
										<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
										<i class="pull-left fa fa-plus-circle fa-2x"></i>
									</label>
									<input data-us="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"  />
									
									
									<a id="oct-remove-staff-imagebdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" <?php if($oct_all_staff[$currstaff_key]['image']==''){ echo "style='display:none;'";}  ?> class="pull-left br-100 btn-danger oct-remove-staff-img btn-xs oct_remove_image" rel="popover" data-placement='bottom' title="<?php echo __("Remove Image?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Remove Staff Image","oct");?>"></i></a>
									<div style="display: none;" class="oct-popover br-5" id="popover-oct-remove-staff-imagebdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>">
									<span class="oct-popover-title"><?php echo __("Delete Image","oct");?></span>
										<span class="oct-popover-content">
											<div class="oct-arrow"></div>
											<a href="javascript:void(0)" value="Delete" data-mediaid="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" data-mediasection='staff' data-mediapath="<?php echo $oct_all_staff[$currstaff_key]['image'];?>" data-imgfieldid="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>uploadedimg"	
											class="btn btn-danger btn-sm oct_delete_image"><?php echo __("Yes","oct");?></a>
											<a href="javascript:void(0)" id="popover-oct-remove-staff-imagebdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" class="btn btn-default btn-sm close_delete_popup" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
										</span>
									</div><!-- end pop up -->
							</div>	
							<div id="oct-image-upload-popupbdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
								<div class="vertical-alignment-helper">
									<div class="modal-dialog modal-md vertical-align-center">
										<div class="modal-content">
											<div class="modal-header">
												<div class="col-md-12 col-xs-12">
													<a data-us="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" ><?php echo __("Crop & Save","oct");?></a>
													<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
												</div>	
											</div>
											<div class="modal-body">
												<img id="oct-preview-imgbdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" />
											</div>
											<div class="modal-footer">
												<div class="col-md-12 np">
													<div class="col-md-4 col-xs-12">
														<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>filesize" name="filesize" />
													</div>	
													<div class="col-md-4 col-xs-12">	
														<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>h" name="h" /> 
													</div>
													<div class="col-md-4 col-xs-12">	
														<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>w" name="w" />
													</div>
													<input type="hidden" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>x1" name="x1" />
													 <input type="hidden" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>y1" name="y1" />
													<input type="hidden" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>x2" name="x2" />
													<input type="hidden" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>y2" name="y2" />
													<input id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>bdimagetype" type="hidden" name="bdimagetype"/>
													<input type="hidden" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id']; ?>bdimagename" name="bdimagename" value="" />
													</div>
											</div>							
										</div>		
									</div>			
								</div>			
							</div>

							<input name="image" id="bdsdu<?php echo $oct_all_staff[$currstaff_key]['id'];?>uploadedimg" type="hidden" value="<?php echo $oct_all_staff[$currstaff_key]['image'];?>" />
						</div>
					
						<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
							<form action="post" id="staff_personal_detail<?php echo $oct_all_staff[$currstaff_key]['id'];?>" class="staff_personal_detail">
							<table class="oct-staff-common-table">
								<tbody>
									<tr>
										<td><label for="oct-member-name"><?php echo __("User Name","oct");?></label></td>
										<td><input type="text" readonly value="<?php echo $oct_all_staff[$currstaff_key]['username']; ?>" class="form-control" id="oct-member-name" /></td>
									</tr>
									<tr>
										<td><label for="staff_name_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"><?php echo __("Full Name","oct");?></label></td>
										<td><input type="text" class="form-control" id="staff_name_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" value="<?php echo $oct_all_staff[$currstaff_key]['staff_name']; ?>"/></td>
									</tr>
									
									<tr>
										<td><label for="staff_description_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"><?php echo __("Desc","oct");?></label></td>
										<td><textarea class="form-control" id="staff_description_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"><?php echo $oct_all_staff[$currstaff_key]['description']; ?></textarea></td>
									</tr>
									<tr>
										<td><label for="phone-number"><?php echo __("Phone","oct");?></label></td>
										<td><input type="tel" class="form-control staff_phone_number" id="staff_phone_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" value="<?php echo $oct_all_staff[$currstaff_key]['phone']; ?>" name="staff_phone" maxlength="10"/></td>
									</tr>
									
									<tr>
										<td><label for="staff_timezone_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"><?php echo __("Time Zone","oct"); ?></label></td>
										<td>
											<select class="selectpicker" id="staff_timezone_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" data-size="10" style="display: none;">
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '1'){ echo "selected"; } ?> timeZoneId="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" value="-12">(GMT-12:00) International Date Line West</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '2'){ echo "selected"; } ?> timeZoneId="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" value="-11">(GMT-11:00) Midway Island, Samoa</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '3'){ echo "selected"; } ?> timeZoneId="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" value="-10">(GMT-10:00) Hawaii</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '4'){ echo "selected"; } ?> timeZoneId="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" value="-9">(GMT-09:00) Alaska</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '5'){ echo "selected"; } ?> timeZoneId="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-8">(GMT-08:00) Pacific Time (US & Canada)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '6'){ echo "selected"; } ?> timeZoneId="6" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-8">(GMT-08:00) Tijuana, Baja California</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '7'){ echo "selected"; } ?> timeZoneId="7" gmtAdjustment="GMT-07:00" useDaylightTime="0" value="-7">(GMT-07:00) Arizona</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '8'){ echo "selected"; } ?> timeZoneId="8" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-7">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '9'){ echo "selected"; } ?> timeZoneId="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-7">(GMT-07:00) Mountain Time (US & Canada)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '10'){ echo "selected"; } ?> timeZoneId="10" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-6">(GMT-06:00) Central America</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '11'){ echo "selected"; } ?> timeZoneId="11" gmtAdjustment="GMT-06:00" useDaylightTime="1" value="-6">(GMT-06:00) Central Time (US & Canada)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '12'){ echo "selected"; } ?> timeZoneId="12" gmtAdjustment="GMT-06:00" useDaylightTime="1" value="-6">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '13'){ echo "selected"; } ?> timeZoneId="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-6">(GMT-06:00) Saskatchewan</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '14'){ echo "selected"; } ?> timeZoneId="14" gmtAdjustment="GMT-05:00" useDaylightTime="0" value="-5">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '15'){ echo "selected"; } ?> timeZoneId="15" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-5">(GMT-05:00) Eastern Time (US & Canada)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '16'){ echo "selected"; } ?> timeZoneId="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-5">(GMT-05:00) Indiana (East)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '17'){ echo "selected"; } ?> timeZoneId="17" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-4">(GMT-04:00) Atlantic Time (Canada)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '18'){ echo "selected"; } ?> timeZoneId="18" gmtAdjustment="GMT-04:00" useDaylightTime="0" value="-4">(GMT-04:00) Caracas, La Paz</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '19'){ echo "selected"; } ?> timeZoneId="19" gmtAdjustment="GMT-04:00" useDaylightTime="0" value="-4">(GMT-04:00) Manaus</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '20'){ echo "selected"; } ?> timeZoneId="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-4">(GMT-04:00) Santiago</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '21'){ echo "selected"; } ?> timeZoneId="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" value="-3.5">(GMT-03:30) Newfoundland</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '22'){ echo "selected"; } ?> timeZoneId="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Brasilia</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '23'){ echo "selected"; } ?> timeZoneId="23" gmtAdjustment="GMT-03:00" useDaylightTime="0" value="-3">(GMT-03:00) Buenos Aires, Georgetown</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '24'){ echo "selected"; } ?> timeZoneId="24" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Greenland</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '25'){ echo "selected"; } ?> timeZoneId="25" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Montevideo</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '26'){ echo "selected"; } ?> timeZoneId="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" value="-2">(GMT-02:00) Mid-Atlantic</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '27'){ echo "selected"; } ?> timeZoneId="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" value="-1">(GMT-01:00) Cape Verde Is.</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '28'){ echo "selected"; } ?> timeZoneId="28" gmtAdjustment="GMT-01:00" useDaylightTime="1" value="-1">(GMT-01:00) Azores</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '29'){ echo "selected"; } ?> timeZoneId="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" value="0">(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '30'){ echo "selected"; } ?> timeZoneId="30" gmtAdjustment="GMT+00:00" useDaylightTime="1" value="0">(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '31'){ echo "selected"; } ?> timeZoneId="31" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '32'){ echo "selected"; } ?> timeZoneId="32" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '33'){ echo "selected"; } ?> timeZoneId="33" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '34'){ echo "selected"; } ?> timeZoneId="34" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '35'){ echo "selected"; } ?> timeZoneId="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) West Central Africa</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '36'){ echo "selected"; } ?> timeZoneId="36" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Amman</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '37'){ echo "selected"; } ?> timeZoneId="37" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Athens, Bucharest, Istanbul</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '38'){ echo "selected"; } ?> timeZoneId="38" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Beirut</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '39'){ echo "selected"; } ?> timeZoneId="39" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Cairo</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '40'){ echo "selected"; } ?> timeZoneId="40" gmtAdjustment="GMT+02:00" useDaylightTime="0" value="2">(GMT+02:00) Harare, Pretoria</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '41'){ echo "selected"; } ?> timeZoneId="41" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '42'){ echo "selected"; } ?> timeZoneId="42" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Jerusalem</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '43'){ echo "selected"; } ?> timeZoneId="43" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Minsk</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '44'){ echo "selected"; } ?> timeZoneId="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Windhoek</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '45'){ echo "selected"; } ?> timeZoneId="45" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '46'){ echo "selected"; } ?> timeZoneId="46" gmtAdjustment="GMT+03:00" useDaylightTime="1" value="3">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '47'){ echo "selected"; } ?> timeZoneId="47" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Nairobi</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '48'){ echo "selected"; } ?> timeZoneId="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Tbilisi</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '49'){ echo "selected"; } ?> timeZoneId="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" value="3.5">(GMT+03:30) Tehran</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '50'){ echo "selected"; } ?> timeZoneId="50" gmtAdjustment="GMT+04:00" useDaylightTime="0" value="4">(GMT+04:00) Abu Dhabi, Muscat</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '51'){ echo "selected"; } ?> timeZoneId="51" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="4">(GMT+04:00) Baku</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '52'){ echo "selected"; } ?> timeZoneId="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="4">(GMT+04:00) Yerevan</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '53'){ echo "selected"; } ?> timeZoneId="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" value="4.5">(GMT+04:30) Kabul</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '54'){ echo "selected"; } ?> timeZoneId="54" gmtAdjustment="GMT+05:00" useDaylightTime="1" value="5">(GMT+05:00) Yekaterinburg</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '55'){ echo "selected"; } ?> timeZoneId="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" value="5">(GMT+05:00) Islamabad, Karachi, Tashkent</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '56'){ echo "selected"; } ?> timeZoneId="56" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="5.5">(GMT+05:30) Sri Jayawardenapura</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '57'){ echo "selected"; } ?> timeZoneId="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="5.5">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '58'){ echo "selected"; } ?> timeZoneId="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" value="5.75">(GMT+05:45) Kathmandu</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '59'){ echo "selected"; } ?> timeZoneId="59" gmtAdjustment="GMT+06:00" useDaylightTime="1" value="6">(GMT+06:00) Almaty, Novosibirsk</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '60'){ echo "selected"; } ?> timeZoneId="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" value="6">(GMT+06:00) Astana, Dhaka</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '61'){ echo "selected"; } ?> timeZoneId="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" value="6.5">(GMT+06:30) Yangon (Rangoon)</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '62'){ echo "selected"; } ?> timeZoneId="62" gmtAdjustment="GMT+07:00" useDaylightTime="0" value="7">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '63'){ echo "selected"; } ?> timeZoneId="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" value="7">(GMT+07:00) Krasnoyarsk</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '64'){ echo "selected"; } ?> timeZoneId="64" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '65'){ echo "selected"; } ?> timeZoneId="65" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Kuala Lumpur, Singapore</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '66'){ echo "selected"; } ?> timeZoneId="66" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '67'){ echo "selected"; } ?> timeZoneId="67" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Perth</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '68'){ echo "selected"; } ?> timeZoneId="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Taipei</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '69'){ echo "selected"; } ?> timeZoneId="69" gmtAdjustment="GMT+09:00" useDaylightTime="0" value="9">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '70'){ echo "selected"; } ?> timeZoneId="70" gmtAdjustment="GMT+09:00" useDaylightTime="0" value="9">(GMT+09:00) Seoul</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '71'){ echo "selected"; } ?> timeZoneId="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" value="9">(GMT+09:00) Yakutsk</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '72'){ echo "selected"; } ?> timeZoneId="72" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="9.5">(GMT+09:30) Adelaide</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '73'){ echo "selected"; } ?> timeZoneId="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="9.5">(GMT+09:30) Darwin</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '74'){ echo "selected"; } ?> timeZoneId="74" gmtAdjustment="GMT+10:00" useDaylightTime="0" value="10">(GMT+10:00) Brisbane</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '75'){ echo "selected"; } ?> timeZoneId="75" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Canberra, Melbourne, Sydney</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '76'){ echo "selected"; } ?> timeZoneId="76" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Hobart</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '77'){ echo "selected"; } ?> timeZoneId="77" gmtAdjustment="GMT+10:00" useDaylightTime="0" value="10">(GMT+10:00) Guam, Port Moresby</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '78'){ echo "selected"; } ?> timeZoneId="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Vladivostok</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '79'){ echo "selected"; } ?> timeZoneId="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" value="11">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '80'){ echo "selected"; } ?> timeZoneId="80" gmtAdjustment="GMT+12:00" useDaylightTime="1" value="12">(GMT+12:00) Auckland, Wellington</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '81'){ echo "selected"; } ?> timeZoneId="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" value="12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
												<option <?php if($oct_all_staff[$currstaff_key]['timezoneID'] == '82'){ echo "selected"; } ?> timeZoneId="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" value="13">(GMT+13:00) Nuku'alofa</option>
											</select>
										</td>
										
									</tr>
									
									<tr>
										<td><label for="phone-number"><?php echo __("Schedule Type","oct");?></label></td>
										<td>
											<label for="staff_schedule_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>">
												<input type="checkbox" <?php if($oct_all_staff[$currstaff_key]['schedule_type']=='M'){ echo "checked";} ?> id="staff_schedule_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"  data-toggle="toggle" data-size="small" data-on="<?php echo __("Monthly","oct");?>" data-off="<?php echo __("Weekly","oct");?>" data-onstyle="info" data-offstyle="warning" >
											</label>
											<input type="hidden" id="curr_staff_schedule_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" value="<?php echo $oct_all_staff[$currstaff_key]['schedule_type']; ?>" />
										</td>
									</tr>
									<tr>
										<td><label><?php echo __("Enable Booking","oct");?></label></td>
										<td>
											<label for="staff_status_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>">
												<input type="checkbox" <?php if($oct_all_staff[$currstaff_key]['status']=='E'){ echo "checked";} ?> id="staff_status_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" >
											</label>
										</td>
									</tr>
									<tr>
										<td></td>
										<td><a data-staff_id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" href="javascript:void(0)" class="btn btn-success oct-btn-width update_staff_detail"><?php echo __("Save","oct");?></a>
									</tr>
								</tbody>
							</table>
							</form>	
						</div>
					</div>
					<?php
					/* Get Staff Services */
					$service->provider_id = $oct_all_staff[$currstaff_key]['id'];
					$oct_staff_services = $service->readall_services_of_provider();
					$staffservces = array();
					foreach($oct_staff_services as $staffservice){$staffservces[]=$staffservice->service_id;}
					?>
					<div class="tab-pane oct-services-list col-lg-12 col-md-12 col-sm-12 col-xs-12 member-services" id="member-services">
						<div class="tab-content">
							<div class="panel panel-default">
								<h4 class="oct-right-header"><?php echo __("Services provided by","oct");?> <strong><?php echo $oct_all_staff[$currstaff_key]['staff_name']; ?> (<span data-total_service="<?php echo sizeof((array)$oct_services);?>" class="staff_servicecount_<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"><?php echo sizeof((array)$staffservces);?></span>)</strong></h4>
									<div id="accordion" class="panel-group" role="tablist" >
										<div class="panel panel-default oct-staff-service-panel">
											<div class="panel-heading" role="tab" >
												<h4 class="panel-title">
													<label for="all-services">
														<input type="checkbox" data-staff_id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" <?php if(sizeof((array)$staffservces)==sizeof((array)$oct_services)){ echo"checked";}?> class="link_service linkallservices" value="all" id="all-services" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" >
													</label>
													<span><?php echo __("All Services","oct");?></span>
												</h4>
											</div>
										</div>
									</div>	
									<?php 
									
									foreach($oct_services as $oct_service){ ?>
									<div id="accordion" class="panel-group" role="tablist">
										<div class="panel panel-default oct-staff-service-panel">
											<div class="panel-heading">
												<h4 class="panel-title">
													<label for="staff-service<?php echo $oct_service->id;?><?php echo $oct_all_staff[$currstaff_key]['id']; ?>">
														<input type="checkbox" class="link_service oct_all_service<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" <?php if(in_array($oct_service->id,$staffservces)){ echo "checked";}?> value="<?php echo $oct_service->id; ?>" data-staff_id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>"  id="staff-service<?php echo $oct_service->id;?><?php echo $oct_all_staff[$currstaff_key]['id']; ?>" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" >
													</label>
													<span><?php echo $oct_service->service_title; ?></span>
													
													<span class="pull-right">
														<span class="oct-service-time-member"><?php  $durationformat = $general->convertToHoursMins($oct_service->duration,'%02dh %02dm');
														if(is_numeric(strpos($durationformat,'00h'))){
														echo str_replace('00h','',$durationformat).'in';}
														elseif(is_numeric(strpos($durationformat,'00m'))){ echo str_replace('00m','',$durationformat);}else{
														echo $durationformat;
														} ?></span>
														<span class="oct-service-price-member"><?php echo $oct_currency_symbol; ?><?php if($oct_service->offered_price != ""){
															echo $oct_service->offered_price;
														}else{ echo $oct_service->amount; }?></span>
														
														<div class="oct-show-hide">
															<input type="checkbox" name="oct-show-hide" class="oct-show-hide-checkbox ssp-show-hide-checkbox" id="ssp<?php echo $oct_service->id;?>" >
															<label class="oct-show-hide-label" for="ssp<?php echo $oct_service->id;?>"></label>
														</div>
														
													</span>	
												</h4>
											</div>
											<div  class="ssp_detail panel-collapse collapse detail-id_ssp<?php echo $oct_service->id;?>">
												<div class="panel-body">
													<form id="" method="post" type="" class="slide-toggle" >
														<div id="staff-service1" class="panel-collapse collapse in">
															<div class="panel-body">
																<div class="oct-provider-custom-price-menu">
																	<ul class="nav nav-pills">
																		<?php
																		if($oct_all_staff[$currstaff_key]['schedule_type']=='M'){
																			$week_name=array(__('First Week','oct'),__('Second Week','oct'),__('Third Week ','oct'),__('Fourth Week ','oct'),__('Fifth Week ','oct'));
																		}else{
																			$week_name=array(__('Week ','oct'));
																		}
																		for($tab=1;$tab<=$wl_end;$tab++) { ?>
																		<li <?php if($tab==1) { echo " class='active' "; } else { }  ?>><a href="#staffdayprice<?php echo $tab; ?><?php echo $oct_service->id;?>" data-toggle="tab"><?php echo $week_name[$tab-1]; echo __("Price","oct");?></a></li>
																		<?php } ?>	
																	</ul>
																</div>	
																<div class="oct-staff-price-rules">
																	<div class="tab-content">	
																	<?php 	for($w=1;$w<=$wl_end;$w++) { ?>
																		<div class="tab-pane <?php if($w==1){ echo "active";} ?>" id="staffdayprice<?php echo $w; ?><?php echo $oct_service->id;?>"><!-- first week price scheduling -->
																		<div class="panel panel-default">
																			<div class="panel-body">
																			<h4 class="oct-right-header"><?php echo $week_name[$w-1].__(" time scheduling of","oct");?> <strong><?php echo $oct_all_staff[$currstaff_key]['staff_name']; ?></strong></h4>
																				<ul class="list-unstyled" id="oct-staff-price">
																					<?php 	$day_name=array(__('Monday','oct'),__('Tuesday','oct'),__('Wednesday','oct'),__('Thursday','oct'),__('Friday','oct'),__('Saturday','oct'),__('Sunday','oct'));
																					for($i=1;$i<=7;$i++) {
																						$ssp->provider_id = $oct_all_staff[$currstaff_key]['id'];
																						$ssp->service_id = $oct_service->id;
																						$ssp->weekid = $w;
																						$ssp->weekdayid = $i;
																						$oct_ssp_info = $ssp->readOne_ssp();
																					?>
																					<li class="active">
																						<div class="col-sm-12 col-md-4 col-lg-4 col-xs-12 np top5">
																							<span class="col-sm-6 col-md-7 col-lg-7 col-xs-6 oct-day-name"><?php echo $day_name[$i-1];?></span>
																							<span class="col-sm-6 col-md-5 col-lg-5 col-xs-6">
																								<a class="btn btn-small btn-success oct-small-br-btn oct_add_ssp" data-serviceamout="<?php echo $oct_service->amount; ?>"  data-weekid="<?php echo $w;?>" data-dayid="<?php echo $i;?>" data-serviceid="<?php echo $oct_service->id; ?>" data-staffid="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" data-mainid="<?php echo $w;?>_<?php echo $i;?>"><?php echo __("Add price","oct");?></a>
																							</span>	
																						</div>	
																						<div class="col-sm-12 col-md-8 col-lg-8 col-xs-12">
																							<ul class="oct-price-row pull-left list-unstyled" id="oct_ssp_<?php echo $oct_service->id;?>_<?php echo $w;?>_<?php echo $i;?>">
																								<?php foreach($oct_ssp_info as $oct_ssp){?>
																								<li class="fullwidth bb1f0" id="oct_ssp_detail_<?php echo $oct_ssp->id; ?>">
																										<span class="col-sm-5 col-md-12 col-lg-5 col-xs-12 oct-staff-price-schedule np">
																											<ul class="list-unstyled">
																												<li>
																													<select id="ssp_starttime_<?php echo $oct_ssp->id; ?>" name="ssp_starttime_<?php echo $oct_ssp->id; ?>" class="selectpicker ssp_starttime" data-sspid="<?php echo $oct_ssp->id; ?>" data-size="10"  style="display: none;">
																														<?php $min =0;
																														while($min < 1440)
																														{																	
																														if($min==1440) {		
																														$timeValue = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015)); 						
																														} else {				
																														$timeValue = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015)); 								}								
																														$timetoprint = date_i18n('G:i:s',mktime(0,$min,0,1,1,2014)); ?>
																														
																														<option  value="<?php echo $timeValue; ?>" <?php if ( $timetoprint==date_i18n('G:i:s',strtotime($oct_ssp->ssp_starttime))){ echo "selected";}?> ><?php echo date_i18n(get_option('time_format'),strtotime($timetoprint)); ?></option>
																														<?php 								  
																														  $min = $min+$interval;
																														} ?>
																													</select>
																												  
																													<span class="oct-price-hours-to"><?php echo __("to","oct");?></span>
																													<select id="ssp_endtime_<?php echo $oct_ssp->id; ?>" name="ssp_endtime_<?php echo $oct_ssp->id; ?>" class="selectpicker" data-sspid="" data-size="10" style="display: none;">
																														<?php $min =0;
																														while($min <= 1440)
																														{								if($min==1440) {		
																														$timeValue = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015)); 
																														$timetoprint = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015));
																														}else{				
																														$timeValue = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015)); 
																														$timetoprint = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015));						
																														}
																														?>																														
																														<option  value="<?php echo $timeValue; ?>"  <?php if ( $timetoprint==date_i18n('G:i:s',strtotime($oct_ssp->ssp_endtime))){ echo "selected";}?> ><?php echo date_i18n(get_option('time_format'),strtotime($timetoprint)); ?></option>
																														<?php 								  
																														  $min = $min+$interval;
																														} ?>
																													</select>
																												</li>
																											</ul>	
																										</span>
																										<span class="col-sm-7 col-md-12 col-lg-7 col-xs-12 npr">
																											<table  class="oct-staff-common-table">
																												<tbody>
																													<tr class="col-lg-7 col-sm-6 col-xs-6 npr">
																													<td class="col-xs-4"><?php echo __("Price","oct");?></td>
																														<td class="col-xs-8"><div class="input-group"><span class="input-group-addon"><?php echo $oct_currency_symbol; ?></span><input type="text" id="ssp_price_<?php echo $oct_ssp->id; ?>" class="form-control" value="<?php echo $oct_ssp->price; ?>" placeholder="<?php echo __("$10","oct");?>" /></div></td>
																													</tr>
																													<tr class="col-lg-5 col-sm-6 col-xs-6 npr">
																														<td class="col-xs-6"><a href="javascript:void(0)" id="oct-delete-staff-price<?php echo $oct_ssp->id; ?>" data-sspid="<?php echo $oct_ssp->id; ?>" class="pull-right btn btn-circle btn-default delete_ssp_popover" rel="popover" data-placement='bottom' title="<?php echo __("Are You Sure?","oct");?>"> <i class="fa fa-trash"></i></a>
																															<div id="popover-delete-price<?php echo $oct_ssp->id; ?>" style="display: none;">
																																<div class="arrow"></div>
																																<table class="form-horizontal" cellspacing="0">
																																	<tbody>
																																		<tr>
																																			<td>
																																				<a id="<?php echo $oct_ssp->id; ?>" value="Delete" class="btn btn-danger delete_ssp" ><?php echo __("Yes","oct");?></a>
																																				<a id="oct-close-popover-delete-price<?php echo $oct_ssp->id; ?>" class="btn btn-default cancel_ssp_delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																																			</td>
																																		</tr>
																																	</tbody>
																																</table>
																															</div>
																														</td>
																														<td class="col-xs-6"><a href="javascript:void(0)" data-sspid="<?php echo $oct_ssp->id; ?>" class="pull-right btn btn-circle btn-success update_ssp_detail" title="Save"> <i class="fa fa-save"></i></a></td>																								</tr>
																													
																												</tbody>
																											</table>	
																										</span>
																									</li>																			
																								<?php }?>																					
																							</ul>	
																						</div>	
																					</li>
																					<?php } ?>
																					
																				</ul>
																			
																			</div>
																		</div>
																		</div><!-- first week price end -->
																		<?php } ?>
																		
																		
																	</div>
																</div>
															</div>
														</div>
														
													</form>
												</div>
											</div>
										</div>
										
									</div>
									<?php } ?>	
									
							</div>
						</div>
						
					</div>
					
					<div class="tab-pane member-availabilty" id="member-availabilty">
					<form id="" method="POST" >
						<div class="panel panel-default">
							
							<div class="col-sm-3 col-md-3 col-lg-3 col-xs-12 oct-weeks-schedule-menu">
								<ul class="nav nav-pills nav-stacked">
								<?php
								if($oct_all_staff[$currstaff_key]['schedule_type']=='M'){
									$week_name=array(__('First Week','oct'),__('Second Week','oct'),__('Third Week','oct'),__('Fourth Week','oct'),__('Fifth Week','oct'));
								}else{
									$week_name=array(__('This Week','oct'));
								}
								for($tab=1;$tab<=$wl_end;$tab++) { ?>
								<li <?php if($tab==1) { echo " class='active' "; } else { }  ?>><a href="#tab<?php echo $tab; ?>" data-toggle="tab"><?php echo $week_name[$tab-1];?></a></li>
								<?php } ?>
								</ul>
							</div>	
							
							<div class="col-sm-9 col-md-9 col-lg-9 col-xs-12">
							<hr id="vr" />
								<div class="tab-content">	
									<?php 	for($w=1;$w<=$wl_end;$w++) { ?>
									<div class="tab-pane <?php if($w==1){ echo "active";} ?>" id="tab<?php echo $w; ?>">							
										<div class="panel panel-default">
											<div class="panel-body">
											<h4 class="oct-right-header"><?php echo $week_name[$w-1].__(" time scheduling of","oct");?> <strong><?php echo $oct_all_staff[$currstaff_key]['staff_name']; ?></strong></h4>
												<ul class="list-unstyled" id="oct-staff-timing">
												    <?php 	$day_name=array(__('Monday','oct'),__('Tuesday','oct'),__('Wednesday','oct'),__('Thursday','oct'),__('Friday','oct'),__('Saturday','oct'),__('Sunday','oct'));
													for($i=1;$i<=7;$i++) {
													/* Get selected Provider Time Schedule */
													$schedule->week_id = $w; 
													$schedule->provider_id = $oct_all_staff[$currstaff_key]['id']; 
													$schedule->weekday_id = $i; 
													$schedule->readOne_new(); ?>	
													<li class="active">
														<span class="col-sm-3 col-md-3 col-lg-3 col-xs-6 oct-day-name"><?php echo $day_name[$i-1];?></span>
														<span class="col-sm-2 col-md-2 col-lg-2 col-xs-6">
															<label class="oct-col2" for="off_day_<?php echo $w;?>_<?php echo $i;?>">
																<input type="checkbox" class="staff_dayoff"  <?php if(!$schedule->get_offdays_new()){ echo " checked "; }?> name="off_day_[<?php echo $w;?>][<?php echo $i;?>]" id="off_day_<?php echo $w;?>_<?php echo $i;?>" data-mainid="<?php echo $w;?>_<?php echo $i;?>" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" />
															</label>
														</span>	
														<span <?php if($schedule->get_offdays_new()){ echo " style='display:none;' "; }?> class="col-sm-7 col-md-7 col-lg-7 col-xs-12 oct-staff-time-schedule" id="staff_st_et_<?php echo $w;?>_<?php echo $i;?>">
															<div class="pull-right">
																<select name="start_time[<?php echo $w;?>][<?php echo $i;?>]" id="start_time_<?php echo $w;?>_<?php echo $i;?>" data-mainid="<?php echo $w;?>_<?php echo $i;?>" class="selectpicker schedule_day_start_time" data-size="10"  style="display: none;">
																	<?php $min =0;
																			while($min < 1440)
																			{																	
																			if($min==1440) {		
																			$timeValue = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015)); 						
																			} else {				
																			$timeValue = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015)); 								}								
																			$timetoprint = date_i18n('G:i:s',mktime(0,$min,0,1,1,2014));
																			
																			$timetocmp = date_i18n('H:i:s',mktime(0,$min,0,1,1,2014)); 						
																			
																			?>
																			
																			<option  value="<?php echo $timeValue; ?>"  <?php if('Y'!=$schedule->get_offdays()  && strtotime($schedule->daystart_time)==strtotime($timetocmp)){ echo "selected='selected'"; } ?> ><?php echo date_i18n(get_option('time_format'),strtotime($timetoprint)); ?></option>
																			<?php 								  
																			  $min = $min+$interval;
																			} ?>
																</select>
															  
																<span class="oct-staff-hours-to"> <?php echo __("to","oct");?> </span>
																<select name="end_time[<?php echo $w;?>][<?php echo $i;?>]"  id="end_time_<?php echo $w;?>_<?php echo $i;?>" class="selectpicker" data-size="10"  style="display: none;">
																	<?php $min =0;
																		$counter=0;
																		while($min <= 1440)
																		{				
																		if($min==1440) {						
																			$timeValue = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015));
																			$timetoprint = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015));
																			$timetocmp = date_i18n('H:i:s',mktime(0,$min-1,0,1,1,2014));
																		} else {				
																			$timeValue = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015));
																			$timetoprint = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015));
																			$timetocmp = date_i18n('H:i:s',mktime(0,$min,0,1,1,2014));
																		}
																		
																		?>
																		   <option  value="<?php echo $timeValue; ?>" <?php if(strtotime($schedule->dayend_time)==strtotime($timetocmp)){ echo "selected"; }?>><?php echo date_i18n(get_option('time_format'),strtotime($timetoprint)); ?></option>
																		  <?php 
																		  $timinglisting[$counter]['value'] = $timetoprint;
																		  $timinglisting[$counter]['text'] =  date_i18n('h:i A',strtotime($timetoprint));
																		  $counter++;
																		  $min = $min+$interval; 
																		} ?>
																</select>
															</div> 
														</span>
													</li>
													<?php } ?>												
												</ul>
											</div>
										</div>
									</div>
									<?php } ?>
								</div>	
							</div>	
							
						</div>
						<table class="oct-staff-common-table">
							<tbody>
								<tr>
									<td></td>
									<td>
										<a href="javascript:void(0)" data-st="<?php echo $oct_all_staff[$currstaff_key]['schedule_type']; ?>" id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" value="" name="update_staff_schedule" class="btn btn-success oct-btn-width col-xs-offset-3 update_staff_schedule"><?php echo __("Save Setting","oct");?></a>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					</div>
					
					<div class="tab-pane member-addbreaks" id="member-addbreaks">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="col-sm-3 col-md-3 col-lg-3 col-xs-12 oct-weeks-breaks-menu">
								<ul class="nav nav-pills nav-stacked">
									<?php
								if($oct_all_staff[$currstaff_key]['schedule_type']=='M'){
									$week_name=array(__('First Week Breaks','oct'),__('Second Week Breaks','oct'),__('Third Week Breaks','oct'),__('Fourth Week Breaks','oct'),__('Fifth Week Breaks','oct'));
								}else{
									$week_name=array(__('This Week Breaks','oct'));
								}
								for($tab=1;$tab<=$wl_end;$tab++) { ?>
								<li <?php if($tab==1) { echo " class='active' "; } else { }  ?>><a href="#tabbreak<?php echo $tab; ?>" data-toggle="tab"><?php echo $week_name[$tab-1];?></a></li>
								<?php } ?>
								</ul>
								</div>	
							
								<div class="col-sm-9 col-md-9 col-lg-9 col-xs-12 oct-weeks-breaks-details">
									<div class="tab-content">
										<?php 	for($w=1;$w<=$wl_end;$w++) { ?>
									<div class="tab-pane <?php if($w==1){ echo "active";} ?>" id="tabbreak<?php echo $w; ?>">
											<div class="panel panel-default">
												<div class="panel-body">
												<h4 class="oct-right-header"><?php echo $week_name[$w-1].__(" of","oct");?> <strong><?php echo $oct_all_staff[$currstaff_key]['staff_name']; ?></strong></h4>
													<ul class="list-unstyled" id="oct-staff-breaks">
														 <?php 	$day_name=array(__('Monday','oct'),__('Tuesday','oct'),__('Wednesday','oct'),__('Thursday','oct'),__('Friday','oct'),__('Saturday','oct'),__('Sunday','oct'));
															for($i=1;$i<=7;$i++) {
															/* Get selected Provider Time Schedule */
															$breaks->week_id = $w; 
															$breaks->provider_id = $oct_all_staff[$currstaff_key]['id']; 
															$breaks->weekday_id = $i; 
															$all_day_breaks = $breaks->read_day_breaks(); ?>
														<li class="active">
															<span class="col-sm-5 col-md-3 col-lg-3 col-xs-6 oct-day-name"><?php echo $day_name[$i-1];?></span>
															<span class="col-sm-5 col-md-2 col-lg-2 col-xs-6">
																<a id="oct-add-staff-breaks" data-staff_id="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" data-weekid="<?php echo $w;?>" data-dayid="<?php echo $i;?>" class="btn btn-small btn-success oct-small-br-btn staff_add_break"><?php echo __("Add Break","oct");?></a>
															</span>	
															<span class="col-sm-12 col-md-7 col-lg-7 col-xs-12 oct-staff-breaks-schedule">
																<ul class="list-unstyled" id="oct_staff_breaks_<?php echo $w;?>_<?php echo $i;?>">
																<?php if(sizeof((array)$all_day_breaks)>0){
																	foreach($all_day_breaks as $day_break){ ?>
																			<li id="staff_break_<?php echo $day_break['break_id']; ?>">
																				<select id="staff_breakstart_<?php echo $day_break['break_id']; ?>" data-bid="<?php echo $day_break['break_id']; ?>" data-bv="start" class="selectpicker staff_schedule_break" data-size="10" style="display: none;">
																					<?php $min =0;
																					while($min < 1440)
																					{																	
																					if($min==1440) {		
																					$timeValue = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015)); 						
																					} else {				
																					$timeValue = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015)); 								}								
																					$timetoprint = date_i18n('G:i:s',mktime(0,$min,0,1,1,2014)); ?>
																					
																					<option  value="<?php echo $timeValue; ?>"  <?php if ( $timetoprint==date_i18n('G:i:s',strtotime($day_break['break_start']))){ echo "selected";}?> ><?php echo date_i18n(get_option('time_format'),strtotime($timetoprint)); ?></option>
																					<?php 								  
																					  $min = $min+$interval;
																					} ?>
																					
																			</select>
																			<span class="oct-staff-hours-to"> <?php echo __("to","oct");?> </span>
																				<select id="staff_breakend_<?php echo $day_break['break_id']; ?>" name="staff_breakend_<?php echo $day_break['break_id']; ?>" data-bid="<?php echo $day_break['break_id']; ?>" data-bv="end" class="selectpicker staff_schedule_break" data-size="10" style="display: none;">
																					<?php $min =0;
																					while($min < 1440)
																					{																	
																					if($min==1440) {		
																					$timeValue = date_i18n('G:i:s',mktime(0,$min-1,0,1,1,2015)); 						
																					} else {				
																					$timeValue = date_i18n('G:i:s',mktime(0,$min,0,1,1,2015)); 								}								
																					$timetoprint = date_i18n('G:i:s',mktime(0,$min,0,1,1,2014)); ?>
																					
																					<option  value="<?php echo $timeValue; ?>"  <?php if ( $timetoprint==date_i18n('G:i:s',strtotime($day_break['break_end']))){ echo "selected";}?> ><?php echo date_i18n(get_option('time_format'),strtotime($timetoprint)); ?></option>
																					<?php 								  
																					  $min = $min+$interval;
																					} ?>
																				</select>
																			<!-- <input type="hidden" id="staff_breakend_<?php echo $day_break['break_id']; ?>" value=""/>-->			
																			<button id="oct-delete-staff-break<?php echo $day_break['break_id']; ?>"  data-bid="<?php echo $day_break['break_id']; ?>"class="pull-right btn btn-circle btn-danger staff_delete_break" rel="popover" data-placement='bottom' title="<?php echo __("Are You Sure?","oct");?>"> <i class="fa fa-trash"></i></button>
																			<div id="popover-delete-breaks<?php echo $day_break['break_id']; ?>" style="display: none;">
																				<div class="arrow"></div>
																				<table class="form-horizontal" cellspacing="0">
																					<tbody>
																						<tr>
																							<td>
																								<a href="javascript:void(0)" id="<?php echo $day_break['break_id']; ?>" value="Delete" class="btn btn-danger delete_staff_break" ><?php echo __("Yes","oct");?></a>
																								<button data-bid="<?php echo $day_break['break_id']; ?>" id="oct-close-popover-delete-breaks<?php echo $day_break['break_id']; ?>" class="btn btn-default close_break_del_popover" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</div>
																		</li>
																	<?php }
																}?>	
																</ul>	
															</span>
														</li>
													<?php } ?>	
													</ul>
												</div>
											</div>
										</div>
										<?php } ?>						
													
									</div><!-- end tab content main right -->
								</div>
							</div>
						</div>
					</div>
					
					
					<?php 
					$breaks->provider_id = $oct_all_staff[$currstaff_key]['id'];
					$oct_offtimes = $breaks->read_offtime();
					?>
					<div class="tab-pane member-offtime" id="member-offtime">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="oct-member-offtime-inner">
								<h3><?php echo __("Off Times for","oct");?> <b><?php echo $oct_all_staff[$currstaff_key]['staff_name']; ?></b></h3>
									<div class="col-md-6 col-sm-7 col-xs-12 col-lg-6 mb-10">
										<label><?php echo __("Add new off time","oct");?></label>
										<div id="offtime-daterange" class="form-control"  >
											<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
											<span></span> <i class="fas fa-caret-down"></i>
										</div>
									</div>
									<div class="col-md-2 col-sm-4 col-xs-12 col-lg-2">
										<a href="javascript:void(0)" class="form-group btn btn-info mt-20 add_staff_offtime" data-sid="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" name=""> <?php echo __("Add Break","oct");?></a>
									</div>
									
								</div>
								<div class="oct-staff-member-offtime-list-main">
									<div class="table-responsive"> 
										<table id="oct-staff-member-offtime-list" class="oct-staff-member-offtime-list table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
											 <thead>
												<tr>
													<th><?php echo __("Start Date","oct");?></th>
													<th><?php echo __("Start Time","oct");?></th>
													<th><?php echo __("End Date","oct");?></th>
													<th><?php echo __("End Time","oct");?></th>
													<th><?php echo __("Action","oct");?></th>
												</tr>
											</thead>
											 <tbody id="staff_offtimes">
												<?php foreach($oct_offtimes as $offtime) { ?>
												<tr id="offtime_detail_<?php echo $offtime->id; ?>">
													<td> <?php echo date_i18n(get_option('date_format'),strtotime($offtime->offtime_start)); ?></td>
													<td><?php echo date_i18n(get_option('time_format'),strtotime($offtime->offtime_start)); ?></td>
													<td><?php echo date_i18n(get_option('date_format'),strtotime($offtime->offtime_end)); ?></td>
													<td><?php echo date_i18n(get_option('time_format'),strtotime($offtime->offtime_end)); ?></td>
													<td><a href="javascript:void(0)" data-staffid="<?php echo $oct_all_staff[$currstaff_key]['id'];?>"  data-otid="<?php echo $offtime->id; ?>" class='btn btn-danger left-margin delete_staff_offtime'><span class='glyphicon glyphicon-remove'></span></a></td>
												</tr>
											<?php } ?>	
											</tbody>
											
											
										</table>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
					<div class="tab-pane member-offdays" id="member-offdays">
						<div class="panel panel-default">
							<div class="panel-body">
							<input type="hidden" value="<?php echo $oct_all_staff[$currstaff_key]['id']; ?>" id="staff_offdays_id" />
							<?php
							/* Get Offdays Information */
							$schedule_offdays->provider_id = $oct_all_staff[$currstaff_key]['id'];
							$all_off_days = $schedule_offdays->read_all_offs_by_provider();
						  
							if(sizeof((array)$all_off_days)!=0) {
							  foreach($all_off_days as $trun){
								$arr_all_off_day [] = $trun->off_date;
							  }
							}
							
							
							$year_arr = array(date('Y'),date('Y')+1);
							$month_num=date('n');



							if(isset($_GET['y']) && in_array($_GET['y'],$year_arr)) {
							 $year = $_GET['y'];
							} else {
							 $year=date('Y');
							}

							$nextYear = date('Y')+1;
							$date=date('d');
							
							$month=array(__('January','oct'),__('February','oct'),__('March','oct'),__('April','oct'),__('May','oct'),__('June','oct'),__('July','oct'),__('August','oct'),__('September','oct'),__('October','oct'),__('November','oct'),__('December','oct'));


							echo '<table class="offdaystable">';
							echo '<th colspan=4 align=center><div style="margin-top:10px;">'.__('Provider Name','oct').': <b>'.$oct_all_staff[$currstaff_key]['staff_name'].'</b><span style="float:right;">'.date('Y').'</span></div></th>';

							for ($reihe=1; $reihe<=4; $reihe++) {
								echo '<tr>';
								for ($spalte=1; $spalte<=3; $spalte++) {
									$this_month=($reihe-1)*3+$spalte;
									$erster=date('w',mktime(0,0,0,$this_month,1,$year));
									$insgesamt=date('t',mktime(0,0,0,$this_month,1,$year));
									if($erster==0) $erster=7;
									echo '<td class="col-md-4 col-sm-4 col-lg-4 col-xs-12">';
									echo '<table align="center" class="table table-bordered table-striped monthtable">';?>
									<th colspan="7" align="center"><?php echo $month[$this_month-1];?>
									
									
									<div class="pull-right">
										<div class="oct-custom-checkbox">
											<ul class="oct-checkbox-list">
												<li>
													<input type="checkbox" class="fullmonthoff" id="<?php echo $year.'-'.$this_month;?>" <?php  $schedule_offdays->off_year_month=$year.'-'.$this_month;	if($schedule_offdays->check_full_month_off()==true) { echo " checked "; }  ?> />
													<label for="<?php echo $year.'-'.$this_month;?>"><?php echo __("Full Month","oct");?><span class="ml5r0"></span></label>
												</li>
											</ul>
										</div>
									</div>
									
									</th>
									<?php 
									echo '<tr><td><b>M</b></td><td><b>T</b></td>';
									echo '<td><b>W</b></td><td><b>T</b></td>';
									echo '<td><b>F</b></td><td class="sat"><b>S</b></td>';
									echo '<td class="sun"><b>S</b></td></tr>';
									echo '<tr class="dateline selmonth_'.$year.'-'.$this_month.'"><br>';
									$i=1;
									while ($i<$erster) {
										echo '<td> </td>';
										$i++;
									}
									$i=1;
									while ($i<=$insgesamt) {
										$rest=($i+$erster-1)%7;
										
										$cal_cur_date =  $year."-".sprintf('%02d', $this_month)."-".sprintf('%02d', $i);
										 
								
										
										if (($i==$date) && ($this_month==$month_num)) {
											
											if(isset($arr_all_off_day)  && in_array($cal_cur_date, $arr_all_off_day)) { 
											  echo '<td  id="'.$year.'-'.$this_month.'-'.$i.'"  class="selectedDate RR"  align=center>';
											} else {
											  echo '<td  id="'.$year.'-'.$this_month.'-'.$i.'"  class="date_single RR"  align=center>';
											}
										
										} else {
											if(isset($arr_all_off_day)  &&  in_array($cal_cur_date, $arr_all_off_day)) { 
											  echo '<td  id="'.$year.'-'.$this_month.'-'.$i.'"  class="selectedDate RR"  align=center>';
											} else {
											   echo '<td  id="'.$year.'-'.$this_month.'-'.$i.'" class="date_single RR"  align=center>';
											}
										}
										
										
										
										if (($i==$date) && ($this_month==$month_num)) {
											echo '<span style="color:#3d3d3d;">'.$i.'</span>';
										}	else if ($rest==6) {
											echo '<span   style="color:#0000cc;">'.$i.'</span>';
										} else if ($rest==0) {
											echo '<span  style="color:#cc0000;">'.$i.'</span>';
										} else {
											echo $i;
										}
										echo "</td>\n";
										if ($rest==0) echo "</tr>\n<tr class='dateline selmonth_".$year."-".$this_month."'>\n";
										$i++;
									}
									echo '</tr>';
									echo '</table>';
									echo '</td>';
								}
								echo '</tr>';
							}

							echo '</table>';
							?>
							</div>
						</div>
					</div>
				
				</div><!-- end first -->
			</div>
			<?php }else{
				echo __("No Staff Found","oct");
			} ?>

		</div>
		
	</div>
</div>
<?php 
	include(dirname(__FILE__).'/footer.php');
?>
<script>
	var staffObj={"plugin_path":"<?php echo $plugin_url_for_ajax;?>"}
</script>