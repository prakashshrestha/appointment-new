<?php 
session_start();
$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
			if (file_exists($root.'/wp-load.php')) {
			require_once($root.'/wp-load.php');
}
if ( ! defined( 'ABSPATH' ) ) exit;  /* direct access prohibited  */

	$category = new octabook_category();
	$location = new octabook_location();
	$service = new octabook_service();
	$general = new octabook_general();
	$staff = new octabook_staff();
	$plugin_url_for_ajax = plugins_url('',dirname(dirname(__FILE__)));
	
	$oct_currency_symbol = get_option('octabook_currency_symbol');
/* insert addons */	
if(!empty($_POST['action']) && $_POST['action']=='addons_inst_data'){

	$service->addon_service_id = $_POST['addon_service_id'];
	$service->addon_title = $_POST['addon_title'];
	$service->addon_price = $_POST['addon_price'];
	$service->addon_location_id = $_SESSION['oct_location'];
	$service->image = $_POST['addon_img_service'];
	$service->addon_maxqty_service = $_POST['addon_maxqty_service'];
	$service->addon_multipleqty_status = $_POST['addon_multipleqty_status'];
	$service->insert_addons();  
}
/* update addons */	

if(!empty($_POST['action']) && $_POST['action']=='addons_update'){	

	 $service->addon_update_id = $_POST['addons_update_id'];
	 $service->addon_title = $_POST['addon_title'];
	 $service->addon_price = $_POST['addon_price'];
	 $service->addon_service_id = $_POST['addon_service_id'];
	 $service->addon_location_id = $_SESSION['oct_location'];
	 $service->image = $_POST['addon_image'];
	 $service->addon_maxqty_service = $_POST['addon_maxqty_service'];
	 $service->addon_multipleqty_status = $_POST['addon_multipleqty_status'];
	 $service->addon_update(); 
}
/* Add Qty Addon*/
if(!empty($_POST['action']) && $_POST['action']=='add_new_qty'){
	$service->addon_service_id = $_POST['addon_service_id'];
	$service->unit = $_POST['unit'];
	$service->rules = $_POST['rules'];
	$service->rate = $_POST['rate'];
	$service->insert_addonprice();
}
/* Delete Qty */
if(isset($_POST['action'],$_POST['addon_service_id']) && $_POST['action']=='delete_addons_qty' && $_POST['addon_service_id']!=''){
		$service->id= $_POST['addon_service_id'];
		$service->addon_qty_delete(); 
}	
if(!empty($_POST['action']) && $_POST['action']=='addon_update_qty'){	

	 $service->addon_service_id = $_POST['addon_service_id'];
	 $service->addon_update_id = $_POST['addon_update_id'];
	 $service->unit = $_POST['unit'];
	 $service->rules = $_POST['rules'];
	 $service->rate = $_POST['rate'];
	 $service->addon_qty_update(); 
}


/* Delete Addon Permanently */		
if(isset($_POST['action'],$_POST['addon_service_id']) && $_POST['action']=='delete_addons' && $_POST['addon_service_id']!=''){
		$service->id= $_POST['addon_service_id'];
		$service->addon_delete(); 
}	

/* Remove Location Image */
if(isset($_POST['action'],$_POST['mediaid'],$_POST['mediapath']) && $_POST['action']=='delete_image'){
		$service->id= $_POST['mediaid'];
		$service->image=''; 
		$service->remove_service_image(); 	
		unlink($root.'/wp-content/uploads'.$_POST['mediapath']);
}	
/* Link/Unlink Provider With Service */	
if(isset($_POST['staff_id'],$_POST['service_id'],$_POST['service_action']) && ($_POST['service_action']=='link_staff' || $_POST['service_action']=='unlink_staff')){
		$staff->location_id = $_SESSION['oct_location'];
		$oct_all_staff = $staff->readAll_with_disables();
		if($_POST['staff_id']=='all'){
			foreach($oct_all_staff as $oct_staff){
				$service->provider_id = $oct_staff['id'];
				$service->id = $_POST['service_id'];
				if($_POST['service_action']=='link_staff'){
				echo $service->unlink_service_providers();
				echo $service->link_service_providers();
				}else{
				echo $service->unlink_service_providers();
				}
			}
		}else{
			$service->provider_id = $_POST['staff_id'];
			$service->id = $_POST['service_id'];
			if($_POST['service_action']=='link_staff'){
			echo $service->link_service_providers();
			}else{
			echo $service->unlink_service_providers();
			}
	}

}
/*Sort Location Position*/
if(isset($_POST['position'],$_POST['service_action']) && $_POST['position']!='' && $_POST['service_action']=='sort_service_position'){
		parse_str($_POST['position'], $output);
		$order_counter=0;
		foreach ($output as $order_no){
			foreach($order_no as $order_value){			 
			  $service->position = $order_counter;
			  $service->id = $order_value;
			  $service->sort_service_position();
			$order_counter++;
			}
		}
}	
	
/* Update Location Detail */		
if(isset($_POST['service_action'],$_POST['service_id']) && $_POST['service_action']=='update_service' && $_POST['service_id']!=''){
		$service->id= $_POST['service_id'];
		$service->readOne();
		if($service->image!='' && $service->image!=$_POST['image']){
			unlink($root.'/wp-content/uploads'.$service->image);
		}		
		$service->color_tag= $_POST['color_tag'];
		$service->service_title= filter_var($_POST['service_title'], FILTER_SANITIZE_STRING);
		$service->image= $_POST['image'];
		$service->service_description= filter_var($_POST['service_description'], FILTER_SANITIZE_STRING);
		$service->service_category= $_POST['service_category'];
		$service->duration= ($_POST['service_duration_hrs']*60)+$_POST['service_duration_mins'];
		$service->amount= filter_var($_POST['service_amount'], FILTER_SANITIZE_STRING);
		$service->offered_price= filter_var($_POST['service_offeredprice'], FILTER_SANITIZE_STRING);
		$category->id = $_POST['service_category'];
		$category->readOne();
		$service->location_id = $category->location_id;
		$service->update(); 
}
	
/* Delete Location Permanently */		
if(isset($_POST['service_action'],$_POST['service_id']) && $_POST['service_action']=='delete_service' && $_POST['service_id']!=''){
		$service->id= $_POST['service_id'];
		$service->delete(); 
}	
/* Update Service Status Enable/Disable */		
if(isset($_POST['service_action'],$_POST['service_id'],$_POST['service_status']) && $_POST['service_action']=='updateservicestatus' && $_POST['service_id']!=''){
		$service->id= $_POST['service_id'];
		$service->service_status= $_POST['service_status'];
		$service->update_service_status(); 
}

/** Get Left Side of Service Section On Several Events **/
if(isset($_POST['service_action']) && $_POST['service_action']=='update_service'){
			/* Get All Services */
			$service->location_id = $_SESSION['oct_location'];
			$all_services = $service->countAll();		
			/* Get All Categories */
			$category->location_id = $_SESSION['oct_location'];
			$all_categories = $category->readAll();
?>

<h3><?php echo __("All Categories","oct");?> <span>(<?php echo sizeof((array)$all_categories);?>)</span>
	<button id="oct-add-new-category" class="pull-right btn btn-circle btn-info" rel="popover" data-placement='bottom' title="Add New Category"> <i class="fa fa-th-large icon-space"></i></button>
	
	
	<div id="popover-content-wrapper" style="display: none">
	<div class="arrow"></div>
	<form id="oct_create_category" action="" method="post">
	<table class="form-horizontal" cellspacing="0">
		<tbody>
			<tr class="form-field form-required">
				<td><input type="text" class="form-control" id="oct_category_title" name="oct_category_title"  value=""/></td>
			</tr>
			<tr>
				<td>
					<a id="" class="btn btn-info oct_create_category" href="javascript:void(0)"><?php echo __("Create","oct");?></a>
					<button id="oct-close-popover-new-service-category" class="btn btn-default" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
	</div>
	
</h3><!-- end popover -->

<ul class="nav nav-tab nav-stacked oct-left-services">
	<li class="oct-left-service-menu-li br-2 oct_category_services oct_category_all_service f-letter-capitalize" data-cid="all">
	<span class="oct-service-sort-icon"><i class="fa fa-th"></i></span>
		<a href="javascript:void(0);" data-toggle="pill">
			<span class="oct-service-name"><?php echo __("All Services","oct");?> (<?php echo $all_services; ?>)</span>
		</a>
	</li>
</ul>	

<ul class="nav nav-tab nav-stacked oct-left-service" id="sortable-category-list">
	<?php foreach($all_categories as $oct_category){ 
			$service->service_category = $oct_category->id;
			$cat_services = $service->readAll_category_services();
	?>
	<li data-cid="<?php echo $oct_category->id;?>" data-cs="<?php echo sizeof((array)$cat_services);?>" class="oct-left-service-menu-li br-2 oct_category_services f-letter-capitalize" id="category_detail_<?php echo $oct_category->id;?>">
	<span class="oct-service-sort-icon"><i class="fa fa-th-list"></i></span>
		<a href="javascript:void(0)" data-toggle="pill">
			<span class="oct-service-name"><?php echo $oct_category->category_title;?> (<?php echo sizeof((array)$cat_services);?>)</span>
		</a>
		
	</li>
	<?php
	/* if(sizeof((array)$cat_services) == 0){
		?>
		<span class="oct-delete-null-category pull-right" style="margin-top: -33px; cursor: pointer;" data-cid="<?php echo $oct_category->id;?>"><i class="fa fa-trash" style="font-size:20px; margin-top: -33px;" aria-hidden="true"></i></span>
		<?php
	} */
	?>
	<?php } ?>
</ul>	


<?php
}	
/* Get Category Serivces */
if(isset($_POST['service_action'],$_POST['category_id']) && $_POST['service_action']=='get_category_services' && $_POST['category_id']!=''){	
		/* Get All Locations */
		$location_sortby = get_option('octabook_location_sortby');
		$oct_locations = $location->readAll('','','');
		$temp_locatio_name = array();
		/* Get All Categories */
		$category->location_id = $_SESSION['oct_location'];
		$all_categories = $category->readAll();
		
		/* Get Services By Category */		
		if($_POST['category_id']=='all'){
		$service->location_id = $_SESSION['oct_location'];
		$oct_services = $service->readAll();
		}else{
		$service->service_category = $_POST['category_id'];
		$oct_services = $service->readAll_category_services();
		}
		/* Get All Enable Staff Members */
		$staff->location_id = $_SESSION['oct_location'];
		$oct_all_staff = $staff->readAll_with_disables();
	
if(sizeof((array)$oct_services)==0){
	echo __("No Service Found","oct");
}	
?>
<?php
foreach($oct_services as $oct_service){ 	
$service->id = $oct_service->id; ?>		
	
<li id="service_detail_<?php echo $oct_service->id; ?>" class="panel panel-default oct-services-panel" >
	<div class="panel-heading">
		<h4 class="panel-title">
			<div class="col-lg-5 col-sm-12 col-xs-12 np">
				<div class="pull-left">
					<i class="fa fa-th-list"></i><span class="badge" style="background-color:<?php echo $oct_service->color_tag; ?>" title="Service color badge"></span>
				</div>	
				<span class="oct-service-title-name f-letter-capitalize"><?php echo $oct_service->service_title; ?></span>
				
			</div>
			<div class="col-lg-7 col-sm-12 col-xs-12 np">
				<div class="col-lg-3 col-sm-3 col-xs-6 np">
					<span class="oct-service-time-main"><i class="fa fa-clock-o"></i><?php if(floor($oct_service->duration/60)!=0){ echo floor($oct_service->duration/60); echo __(" Hrs","oct"); } ?>  <?php  if($oct_service->duration%60 !=0){ echo $oct_service->duration%60; echo __(" Mins","oct");} ?></span>
				</div>
				<div class="col-lg-2 col-sm-2 col-xs-6 np">
					<span class="oct-service-price-main"><span><?php echo $oct_currency_symbol;?></span><?php if($oct_service->offered_price != ""){
						echo $oct_service->offered_price;
					}else{ echo $oct_service->amount; } ?></span>
				</div>	
				<div class="col-lg-2 col-sm-2 col-xs-4 np">
					<label class="pull-left mr-10 toggle-large" for="sevice-endis-<?php echo $oct_service->id; ?>">
					<input type="checkbox" data-id="<?php echo $oct_service->id; ?>" class="update_service_status" id="sevice-endis-<?php echo $oct_service->id; ?>" <?php if($oct_service->service_status=='Y'){echo 'checked'; } ?> data-toggle="toggle" data-size="small" data-on="<?php echo __("Enable","oct");?>" data-off="<?php echo __("Disable","oct");?>" data-onstyle="success" data-offstyle="danger" />
					
					</label>
				</div>
				<div class="col-lg-2 col-sm-2 col-xs-4 npr rnp">
				<!--addons btn -->
				<a href="?page=service_addons&sid=<?php echo $oct_service->id; ?>" class="btn btn-info btn-sm manage-addons-btn"><i class="fa fa-puzzle-piece icon-space" aria-hidden="true"></i><?php echo __("Addons","oct");?></a>
				</div>
				<div class="pull-right">
					<div class="col-lg-2 col-sm-1 col-xs-4 np">
					<a data-poid="oct-popover-delete-service<?php echo $oct_service->id; ?>" id="oct-delete-service<?php echo $oct_service->id; ?>" class="pull-right btn-circle btn-danger btn-sm oct-delete-popover" rel="popover" data-placement='bottom' title="Delete this service?"><i class="fa fa-trash" title="Delete Service"></i></a>
						<div class="oct-popover" id="oct-popover-delete-service<?php echo $oct_service->id; ?>" style="display: none;">
							<div class="arrow"></div>
							<table class="form-horizontal" cellspacing="0">
								<tbody>
									<tr>
										<td>
											<?php if($service->total_service_bookings()>0){?>
											<span class="oct-popover-title"><?php echo __("Unable to delete service,having bookings","oct");?></span>
											<?php }else{?>		
											<button data-id="<?php echo $oct_service->id; ?>" value="Delete" class="btn btn-danger btn-sm mr-10 delete_service" type="submit"><?php echo __("Yes","oct");?></button>
											<button data-poid="oct-popover-service<?php echo $oct_service->id; ?>" class="btn btn-default btn-sm oct-close-popover-delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button><?php } ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="oct-show-hide pull-right">
						<input type="checkbox" name="oct-show-hide" class="oct-show-hide-checkbox" id="<?php echo $oct_service->id; ?>" ><!--Added Serivce Id-->
						<label class="oct-show-hide-label" for="<?php echo $oct_service->id; ?>"></label>
					</div>
				</div>
			</div>
			
		</h4>
	</div>
	<div id="" class="service_detail panel-collapse collapse detail-id_<?php echo $oct_service->id; ?>">
		<div class="panel-body">
			<div class="oct-service-collapse-div col-sm-7 col-md-7 col-lg-7 col-xs-12">
				<form data-sid="<?php echo $oct_service->id; ?>" id="oct_update_service_<?php echo $oct_service->id; ?>" method="post" type="" class="slide-toggle oct_update_service" >
					<table class="oct-create-service-table">
						<tbody>
							<tr>
								<td><label for="oct-service-color-tag<?php echo $oct_service->id; ?>"><?php echo __("Color Tag","oct");?></label></td>
								<td><input type="text" id="oct-service-color-tag<?php echo $oct_service->id; ?>" class="form-control demo" data-control="saturation" value="<?php echo $oct_service->color_tag; ?>"></td>
							</tr>
							<tr>
								<td><label for="oct-service-title<?php echo $oct_service->id; ?>"><?php echo __("Service Title","oct");?></label></td>
								<td><input type="text" name="u_service_title" class="form-control" id="oct-service-title<?php echo $oct_service->id; ?>" value="<?php echo $oct_service->service_title; ?>" /></td>
							</tr>
							
							<tr>
								<td><label for="oct-service-desc<?php echo $oct_service->id; ?>"><?php echo __("Service Description","oct");?></label></td>
								<td><textarea  name="u_service_desc" id="oct-service-desc<?php echo $oct_service->id; ?>" class="form-control"><?php echo $oct_service->service_description; ?></textarea></td>
							</tr>
							<tr>
								<td><label for="oct-service-desc"><?php echo __("Service Image","oct");?></label></td>
								<td>
									<div class="oct-service-image-uploader">
										<img id="bdls<?php echo $oct_service->id; ?>locimage" src="<?php if($oct_service->image==''){ echo $plugin_url_for_ajax.'/assets/images/service.png';}else{
										echo site_url()."/wp-content/uploads".$oct_service->image;
										}?>" class="oct-service-image br-100" height="100" width="100">
										<label for="oct-upload-imagebdls<?php echo $oct_service->id; ?>" <?php if($oct_service->image==''){ echo "style='display:block'"; }else{ echo "style='display:none'"; } ?> class="oct-service-img-icon-label show_image_icon_add<?php echo $oct_service->id; ?> ">
											<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
											<i class="pull-left fa fa-plus-circle fa-2x"></i>
										</label>
										<input data-us="bdls<?php echo $oct_service->id; ?>" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdls<?php echo $oct_service->id; ?>"  />
										
										<a id="oct-remove-service-imagebdls<?php echo $oct_service->id; ?>" <?php if($oct_service->image!=''){ echo "style='display:block;'";}  ?> class="pull-left br-100 btn-danger oct-remove-service-img btn-xs oct_remove_image" rel="popover" data-placement='bottom' title="<?php echo __("Remove Image?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Remove Service Image","oct");?>"></i></a>
										
										
										<div style="display: none;" class="oct-popover" id="popover-oct-remove-service-imagebdls<?php echo $oct_service->id; ?>">
										<div class="arrow"></div>
										<table class="form-horizontal" cellspacing="0">
											<tbody>
												<tr>
													<td>
														<a href="javascript:void(0)" value="Delete" data-mediaid="<?php echo $oct_service->id; ?>" data-mediasection='service' data-mediapath="<?php echo $oct_service->image;?>" data-imgfieldid="bdls<?php echo $oct_service->id;?>uploadedimg"	
														class="btn btn-danger btn-sm oct_delete_image"><?php echo __("Yes","oct");?></a>
														<a href="javascript:void(0)" id="popover-oct-remove-service-imagebdls<?php echo $oct_service->id; ?>" class="btn btn-default btn-sm close_delete_popup" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>	
				<div id="oct-image-upload-popupbdls<?php echo $oct_service->id; ?>" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
				<div class="vertical-alignment-helper">
					<div class="modal-dialog modal-md vertical-align-center">
						<div class="modal-content">
							<div class="modal-header">
								<div class="col-md-12 col-xs-12">
									<a data-us="bdls<?php echo $oct_service->id; ?>" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdls<?php echo $oct_service->id; ?>" ><?php echo __("Crop & Save","oct");?></a>
									<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
								</div>	
							</div>
							<div class="modal-body">
								<img id="oct-preview-imgbdls<?php echo $oct_service->id; ?>" />
							</div>
							<div class="modal-footer">
								<div class="col-md-12 np">
									<div class="col-md-4 col-xs-12">
										<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdls<?php echo $oct_service->id; ?>filesize" name="filesize" />
									</div>	
									<div class="col-md-4 col-xs-12">	
										<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdls<?php echo $oct_service->id; ?>h" name="h" /> 
									</div>
									<div class="col-md-4 col-xs-12">	
										<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdls<?php echo $oct_service->id; ?>w" name="w" />
									</div>
									<input type="hidden" id="bdls<?php echo $oct_service->id; ?>x1" name="x1" />
									 <input type="hidden" id="bdls<?php echo $oct_service->id; ?>y1" name="y1" />
									<input type="hidden" id="bdls<?php echo $oct_service->id; ?>x2" name="x2" />
									<input type="hidden" id="bdls<?php echo $oct_service->id; ?>y2" name="y2" />
									<input id="bdls<?php echo $oct_service->id; ?>bdimagetype" type="hidden" name="bdimagetype"/>
									<input type="hidden" id="bdls<?php echo $oct_service->id; ?>bdimagename" name="bdimagename" value="" />
									</div>
							</div>							
						</div>		
					</div>			
				</div>			
			</div>
				</td>
			<input name="image" id="bdls<?php echo $oct_service->id;?>uploadedimg" type="hidden" value="<?php echo $oct_service->image;?>" />
							</tr>
							
							<tr>
							
								<td><label for="oct-service-category<?php echo $oct_service->id; ?>"><?php echo __("Select Category","oct");?></label></td>
								<td>
									<div class="form-group">
									  <select id="oct-service-category<?php echo $oct_service->id; ?>"  name="service_category" class="selectpicker form-control" data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true"  >
											<?php foreach($all_categories as $oct_category){ ?>
											<option <?php if($oct_service->category_id==$oct_category->id){ echo "selected";}?> value="<?php echo $oct_category->id;?>"><?php echo $oct_category->category_title;?></option>
										<?php } ?>
									</select>
									</div>
								</td>
							</tr>										
							
							<tr>
								<td><label for="oct-service-duration<?php echo $oct_service->id; ?>"><?php echo __("Duration","oct");?></label></td>
								<td>	
									<div class="form-inline">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
											<input class="form-control" placeholder="00" size="2" maxlength="2" id="oct-duration-hrs<?php echo $oct_service->id; ?>" name="u_duration_hrs" value="<?php echo floor($oct_service->duration/60);?>" type="text">
											<span class="input-group-addon"><?php echo __("Hours","oct");?></span>
										</div>
										<div class="input-group">

											<input class="form-control" placeholder="05" size="2" maxlength="2" id="oct-duration-mins<?php echo $oct_service->id; ?>" name="u_duration_mins" value="<?php echo $oct_service->duration%60;?>" type="text">
											<span class="input-group-addon"><?php echo __("Minutes","oct");?></span>
										</div>
										<label id="oct-duration-hrs<?php echo $oct_service->id; ?>-error" class="error" for="oct-duration-hrs<?php echo $oct_service->id; ?>" style="display:none;"></label>
										<label id="oct-duration-mins<?php echo $oct_service->id; ?>-error" class="error" for="oct-duration-mins<?php echo $oct_service->id; ?>" style="display:none;"></label>
									</div>									
								</td>									
							</tr>
							<tr>
								<td><label for="oct-service-price<?php echo $oct_service->id; ?>"><?php echo __("Default Price","oct");?></label></td>
								<td>
									<div class="input-group">
										<span class="input-group-addon"><?php echo $oct_currency_symbol;?></span>
										<input name="u_service_price" id="oct-service-price<?php echo $oct_service->id; ?>" type="text" class="form-control" placeholder="<?php echo __("US Dollar","oct");?>" value="<?php echo $oct_service->amount; ?>">
									</div>	
									<label id="oct-service-price<?php echo $oct_service->id; ?>-error" class="error" for="oct-service-price<?php echo $oct_service->id; ?>" style="display:none;"></label>
								</td>
							</tr>
							<tr>
								<td><label for="oct-service-price<?php echo $oct_service->id; ?>"><?php echo __("Offered Price","oct");?></label></td>
								<td>
									<div class="input-group">
										<span class="input-group-addon"><?php echo $oct_currency_symbol;?></span>
										<input name="u_service_offeredprice" id="oct-service-offered-price<?php echo $oct_service->id; ?>" type="text" class="form-control" placeholder="<?php echo __("US Dollar","oct");?>" value="<?php echo $oct_service->offered_price; ?>">
									</div>	
									<label id="oct-service-offered-price<?php echo $oct_service->id; ?>-error" class="error" for="oct-service-offered-price<?php echo $oct_service->id; ?>" style="display:none;"></label>
								</td>
							</tr>
							
							
						</tbody>
					</table>
				
			</div>
			<?php if(sizeof((array)$oct_all_staff)>0){ 
				  $service->id = $oct_service->id; ?>
			<div class="col-sm-5 col-md-5 col-lg-5 col-xs-12">
				<h6 class="oct-right-header"><?php echo __("Who provide these Services","oct");?></h6>
				<ul class="list-unstyled" id="oct-select-staff-member">
					<li class="active">
						<div class="oct-col12">
							
							<label class="pull-left mr-10 toggle-large" for="all-staff-member<?php echo 'all'.$oct_service->id; ?>">
								<input data-service_id="<?php echo $oct_service->id; ?>" <?php if($service->get_total_linked_staff_of_service()==sizeof((array)$oct_all_staff)){ echo "checked='checked'";} ?> class="link_staff linkallstaff" type="checkbox" id="all-staff-member<?php echo 'all'.$oct_service->id; ?>" value="all" data-toggle="toggle" data-size="small" data-on="<?php echo __("Enable","oct");?>" data-off="<?php echo __("Disable","oct");?>" data-onstyle="success" data-offstyle="danger" />
								
							</label>
							<span class="oct-service-provider-list"><?php echo __("All Staff Members","oct");?></span>
						</div>
					</li>
				</ul>
				<ul class="list-unstyled" id="oct-select-staff-member">		
				<?php foreach($oct_all_staff as $oct_staff){
						$service->id = $oct_service->id;
						$service->provider_id = $oct_staff['id'];
					?>	
					<li class="active">
						<div class="oct-col12">
							
							<label class="pull-left mr-10" for="staff-member<?php echo $oct_staff['id'].$oct_service->id; ?>">
								<input data-service_id="<?php echo $oct_service->id; ?>" class="oct-toggle-input link_staff oct_all_staff<?php echo $oct_service->id; ?>" <?php if($service->service_provider_link_status()=="Y"){ echo "checked";} ?> type="checkbox" value="<?php echo $oct_staff['id']; ?>" id="staff-member<?php echo $oct_staff['id'].$oct_service->id; ?>" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" />
							</label>
							<span class="oct-service-provider-list"><?php echo $oct_staff['staff_name']; ?></span>
						</div>
					</li>
					<?php } ?>
				</ul>		

			</div>	
		<?php } ?>	
			<table class="col-sm-7 col-md-7 col-lg-7 col-xs-12 mt-20 mb-20">
				<tbody>
					<tr>
						<td></td>
						<td>
							<a href="javascript:void(0);" data-service_id="<?php echo $oct_service->id; ?>" name="" class="btn btn-success oct-btn-width col-md-offset-4 update_service" type="submit"><?php echo __("Save","oct");?></a>
							<button type="reset" class="btn btn-default oct-btn-width ml-30"><?php echo __("Reset","oct");?></button>
						</td>
					</tr>
				</tbody>
			</table>
			
			</form>
		</div>
	</div>

	</li>
	<?php } ?>
	<!-- add new service pop up -->
	<li>
	<div class="panel panel-default oct-services-panel oct-add-new-service">
	<div class="panel-heading">
		<h4 class="panel-title">
			<div class="oct-col6">
				<span class="oct-service-title-name"><?php echo __("Add New Service","oct");?></span>		
			</div>
			<div class="pull-right oct-col6">					
				<div class="pull-right">
						<div class="oct-show-hide pull-right">
						<input type="checkbox" name="oct-show-hide" checked="checked" class="oct-show-hide-checkbox" id="addservice" ><!--Added Serivce Id-->
						<label class="oct-show-hide-label" for="addservice"></label>
					</div>
				</div>
			</div>										
		</h4>
	</div>
	<div id="" class="service_detail panel-collapse collapse in detail-id_addservice">
		<div class="panel-body">
		<form id="oct_create_service" method="post" type="" class="slide-toggle" >
			<div class="oct-service-collapse-div col-sm-7 col-md-7 col-lg-7 col-xs-12">
				
					<table class="oct-create-service-table">
						<tbody>
							<tr>
								<td><label for="oct-service-color-tag"><?php echo __("Color Tag","oct");?></label></td>
								<td><input type="text" id="oct-service-color-tag" class="form-control demo" name="color_tag" data-control="saturation" value="#35add2"></td>
							</tr>
							<tr>
								<td><label for="oct-service-title"><?php echo __("Service Title","oct");?></label></td>
								<td><input type="text" name="service_title" class="form-control" id="oct-service-title" /></td>
							</tr>
							
							<tr>
								<td><label for="oct-service-desc"><?php echo __("Service Description","oct");?></label></td>
								<td><textarea id="oct-service-desc" name="service_description" class="form-control"></textarea></td>
							</tr>
							<tr>
								<td><label for="oct-service-desc"><?php echo __("Service Image","oct");?></label></td>
								<td>
									<div class="oct-service-image-uploader">
										<img id="bdcslocimage" src="<?php echo $plugin_url_for_ajax; ?>/assets/images/service.png" class="oct-service-image br-100"  height="100" width="100">
										<label for="oct-upload-imagebdcs" class="oct-service-img-icon-label">
											<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
											<i class="pull-left fa fa-plus-circle fa-2x"></i>
										</label>
										<input data-us="bdcs" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdcs"  />
										
										<a style="display: none;" id="oct-remove-service-imagebdcs" class="pull-left br-100 btn-danger oct-remove-service-img btn-xs" rel="popover" data-placement='bottom' title="<?php echo __("Remove Image?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Remove service Image","oct");?>"></i></a>
										<div id="popover-oct-remove-service-imagebdcs" style="display: none;">
											<div class="arrow"></div>
											<table class="form-horizontal" cellspacing="0">
												<tbody>
													<tr>
														<td>
															<a href="javascript:void(0)" id="" value="Delete" class="btn btn-danger btn-sm" type="submit"><?php echo __("Yes","oct");?></a>
															<a href="javascript:void(0)" id="oct-close-popover-service-imagebdcs" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
														</td>
													</tr>
												</tbody>
											</table>
										</div><!-- end pop up -->
									</div>
			<div id="oct-image-upload-popupbdcs" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
				<div class="vertical-alignment-helper">
					<div class="modal-dialog modal-md vertical-align-center">
						<div class="modal-content">
							<div class="modal-header">
								<div class="col-md-12 col-xs-12">
									<a data-us="bdcs" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdcs"><?php echo __("Crop & Save","oct");?></a>
									<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
								</div>	
							</div>
							<div class="modal-body">
								<img id="oct-preview-imgbdcs" />
							</div>
							<div class="modal-footer">
								<div class="col-md-12 np">
									<div class="col-md-4 col-xs-12">
										<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdcsfilesize" name="filesize" />
									</div>	
									<div class="col-md-4 col-xs-12">	
										<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdcsh" name="h" /> 
									</div>
									<div class="col-md-4 col-xs-12">	
										<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdcsw" name="w" />
									</div>
									<input type="hidden" id="bdcsx1" name="x1" />
									 <input type="hidden" id="bdcsy1" name="y1" />
									<input type="hidden" id="bdcsx2" name="x2" />
									<input type="hidden" id="bdcsy2" name="y2" />
									<input id="bdcsbdimagetype" type="hidden" name="bdimagetype"/>
									<input type="hidden" id="bdcsbdimagename" name="bdimagename" value="" />
								</div>
							</div>							
						</div>		
					</div>			
				</div>			
			</div>
			<input name="service_image" id="bdcsuploadedimg" type="hidden" value="" />						
								</td>
							</tr>
							<tr>
								<td><label for="oct-service-desc"><?php echo __("Select Category","oct");?></label></td>
								<td>
									<div class="form-group">
									  <select class="selectpicker form-control" id="oct_service_categories" name="service_category" data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true"  >
									  <?php foreach($all_categories as $oct_category){ ?>
											<option <?php if($_POST['category_id']==$oct_category->id){ echo "selected";}?> value="<?php echo $oct_category->id;?>"><?php echo $oct_category->category_title;?></option>
										<?php } ?>	
									</select>
									</div>
								</td>
							</tr>
					
							<tr>
								<td><label for="oct-service-duration"><?php echo __("Duration","oct");?></label></td>
								<td>	
									<div class="form-inline">
										<div class="input-group">
											<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
											<input class="form-control" placeholder="00" size="2" maxlength="2" id="service_duration_hrs" name="service_duration_hrs" type="text">
											<span class="input-group-addon"><?php echo __("Hours","oct");?></span>
										</div>
										<div class="input-group">
											<input class="form-control" placeholder="05" size="2" maxlength="2" id="service_duration_mins" name="service_duration_mins" type="text">
											<span class="input-group-addon"><?php echo __("Minutes","oct");?></span>
										</div>
										<label id="service_duration_hrs-error" class="error" for="service_duration_hrs" style="display:none"></label>
										<label id="service_duration_mins-error" class="error" for="service_duration_mins" style="display:none"></label>
									
									</div>									
								</td>
							</tr>
							<tr>
								<td><label for="oct-service-price"><?php echo __("Default Price","oct");?></label></td>
								<td>
									<div class="input-group">
										<span class="input-group-addon"><?php echo $oct_currency_symbol;?></span>
										<input type="text" name="service_price" class="form-control" placeholder="<?php echo __("US Dollar","oct");?>">
									</div>	
									<label id="oct-service-price-error" class="error" for="oct-service-price" style="display:none"></label>
								</td>
							</tr>
							<tr>
								<td><label for="oct-service-price"><?php echo __("Offered Price","oct");?></label></td>
								<td>
									<div class="input-group">
										<span class="input-group-addon"><?php echo $oct_currency_symbol;?></span>
										<input type="text" name="offered_price" class="form-control" placeholder="<?php echo __("US Dollar","oct");?>">
									</div>
									<label id="oct-service-price-error" class="error" for="oct-service-price" style="display:none"></label>									
								</td>
							</tr>
							
						</tbody>
					</table>
				
			</div>
			<?php if(sizeof((array)$oct_all_staff)>0){?>
			<div class="col-sm-5 col-md-5 col-lg-5 col-xs-12">
				<h6 class="oct-right-header"><?php echo __("Who provide these Services","oct");?></h6>
				<ul class="list-unstyled" id="oct-select-staff-member">
					<li class="active">
						<div class="oct-col12">
							
							<label class="toggle-large" for="all-staff-member-c">
								<input data-service_id='' name="service_staff_c_all"  class="oct-toggle-input linkallstaff" type="checkbox" id="all-staff-member-c" type="checkbox"  data-toggle="toggle" data-size="small" data-on="<?php echo __("Enable","oct");?>" data-off="<?php echo __("Disable","oct");?>" data-onstyle="success" data-offstyle="danger" />
							</label>
							<span><?php echo __("All Staff Members","oct");?></span>
						</div>
					</li>
				</ul>
				<ul class="list-unstyled" id="oct-select-staff-member">		
					<?php foreach($oct_all_staff as $oct_staff){ ?>	
					<li class="active">
						<div class="oct-col12">
							
							<label for="staff-member-c<?php echo $oct_staff['id']; ?>">
								<input type="checkbox" value="<?php echo $oct_staff['id']; ?>" id="staff-member-c<?php echo $oct_staff['id']; ?>" name="service_staff_c_<?php echo $oct_staff['id']; ?>" class="oct_all_staff" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" />
							</label>
							<span class="oct-service-provider-list"><?php echo $oct_staff['staff_name']; ?></span>
						</div>
					</li>
					<?php } ?>
				</ul>		

			</div>
			<?php } ?>
			<table class="col-sm-7 col-md-7 col-lg-7 col-xs-12 mt-20 mb-20">
				<tbody>
					<tr>
						<td></td>
						<td>
							<button id="oct_create_service_btn" name="oct_create_service" class="btn btn-success oct-btn-width col-md-offset-4" type="submit"><?php echo __("Save","oct");?></button>
							<button type="reset" class="btn btn-default oct-btn-width ml-30"><?php echo __("Reset","oct");?></button>
						</td>
					</tr>
				</tbody>
			</table>
			
			
			</form>									
		</div>
	</div>
	</div>
	</li>
<?php }

if(isset($_POST['action']) && $_POST['action'] == "check_service_title"){
	$service_title = $_POST['title'];
	global $wpdb;
	$query = "select count(id) as count_title from ".$wpdb->prefix."oct_services where service_title='".$service_title."' and location_id='".$_SESSION['oct_location']."'";
	$get_resss = $wpdb->get_var($query);
	if($get_resss == 0){
		echo "true";
	}else{
		echo "false";
	}
}

if(isset($_POST['action']) && $_POST['action'] == "check_location_title"){
	$location_title = $_POST['title'];
	global $wpdb;
	$query = "select count(id) as count_title from ".$wpdb->prefix."oct_locations where location_title='".$location_title."'";
	$get_resss = $wpdb->get_var($query);
	if($get_resss == 0){
		echo "true";
	}else{
		echo "false";
	}
}

if(isset($_POST['action']) && $_POST['action'] == "check_category_title"){
	$category_title = $_POST['title'];
	global $wpdb;
	$query = "select count(id) as count_title from ".$wpdb->prefix."oct_categories where category_title='".$category_title."' and location_id='".$_SESSION['oct_location']."'";
	$get_resss = $wpdb->get_var($query);
	if($get_resss == 0){
		echo "true";
	}else{
		echo "false";
	}
}

/* update addons status */
if(isset($_POST['action']) && $_POST['action'] == "addons_update_status"){
	$addons_id = $_POST['addons_update_id'];
	$addon_title = $_POST['addon_title'];
	$addon_price = $_POST['addon_price'];
	$status = $_POST['status'];
	$addon_service_id = $_POST['addon_service_id'];
	$service_status= $_POST['service_status'];
	
	global $wpdb;
	$query = "UPDATE ".$wpdb->prefix."oct_services_addon SET status ='$status' WHERE	id =$addons_id";
	$get_resss = $wpdb->get_var($query);
	if($get_resss == 0){
		echo "true";
	}else{
		echo "false";
	}
	
}
/* Get Addon Pricing Rules */
if(isset($_POST['action'],$_POST['addon_id']) && $_POST['action'] == "get_addon_pricing_rules" && $_POST['addon_id']!=''){
		$addonid = $_POST['addon_id'];
		$service->addon_service_id = $addonid;
		$result = $service->readall_qty_addon();		
		$count_addon = count((array)$result);
		if($count_addon > 0){
		foreach($result as $r) {
			
			?>
		<li class="form-group myaddon-qty_price_row<?php echo $r->id; ?>">
		<form class="service_addon_pricing" id="myedtform_addonunits<?php echo $r->id; ?>">
		<label class="col-xs-12 col-sm-2" for="service_addons_price"><?php echo __("Qty","oct");?></label>
			<div class="col-xs-4 col-sm-2 npl">
				<input id="myedtqty_addon<?php echo $r->id; ?>" name="txtedtqtyaddons" class="form-control myloadedqty_addons<?php echo $r->id; ?>" placeholder="1" value="<?php echo $r->unit; ?>" type="text" />
			</div>
			<div class="col-xs-4 col-sm-2 npl">
				<select class="form-control Addons_price_rules_select myloadedrules_addons<?php echo $r->id; ?>">
					<option <?php if ($r->rules == 'E'){ ?>selected<?php } ?>
									value="E">=
					</option>
					<option <?php if ($r->rules == 'G'){ ?>selected<?php } ?>
									value="G"> &gt; </option>
				</select>
			</div>
			<div class="col-xs-4 col-sm-2 npl">
				<input name="myedtpriceaddon" id="myedtprice_addon<?php echo $r->id; ?>" class="pull-left form-control myloadedprice_addons<?php echo $r->id; ?>" value="<?php echo $r->rate; ?>"  placeholder="<?php echo __("Price","oct");?>" type="text" />
			</div>
			<div class="col-xs-12 col-sm-3  pull-left npr" >
				<a data-id="<?php echo $r->id; ?>" class="btn btn-success btn-circle mr-15 pull-left myloadedbtnsave_addons update-addon-rule" data-addon_service_id="<?php echo $addonid;?>"><i class="fa fa-save" title="<?php echo __("Update","oct");?>"></i></a>
			
				<a href="javascript:void(0);" data-id="<?php echo $r->id; ?>" class="btn btn-danger btn-circle pull-left delete-addon-rule myloadedbtndelete_addons">
				<i class="fa fa-trash"></i>
				</a>
			</div>
			</form>
		</li>
		<?php
		}
	}
	?>
	<li class="form-group">
	<form class="add_addon_pricing" id="mynewaddedform_addonunits<?php echo $addonid; ?>">
	<label class="col-xs-12 col-sm-2" for="service_addons_price"><?php echo __("Qty","oct");?></label>
		<div class="col-xs-4 col-sm-2 npl">
			<input name="mynewssqtyaddon" id="mynewaddedqty_addon<?php echo $addonid; ?>" class="form-control mynewqty_addons<?php echo $addonid; ?>" type="text" />
		</div>
		<div class="col-xs-4 col-sm-2 npl">
			<select class="form-control Addons_price_rules_select mynewrules_addons<?php echo $addonid; ?>">
					<option selected value="E">=</option>
					<option value="G"> &gt; </option>
			</select>
		</div>
		<div class="col-xs-4 col-sm-2 npl">
			<input name="mynewsspriceaddon" id="mynewaddedprice_addon<?php echo $addonid; ?>" class="pull-left form-control mynewprice_addons<?php echo $addonid; ?>"  placeholder="<?php echo __("Price","oct");?>" type="text" />
		</div>
		<div class="col-xs-12 col-sm-3 pull-left npl" >
			<a href="javascript:void(0);" data-id="<?php echo $addonid; ?>" class="btn btn-success btn-circle pull-left mybtnaddnewqty_addon add-addon-price-rule form-group new-manage-price-list"><?php echo __("Add New","oct");?></a>
		</div>
		</form>
	</li>
	<?php
}

 ?>			

