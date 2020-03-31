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
	$plugin_url_for_ajax = plugins_url('',dirname(dirname(__FILE__)));

	
/*Sort Category Position*/
if(isset($_POST['position'],$_POST['category_action']) && $_POST['position']!='' && $_POST['category_action']=='sort_category_position'){
		parse_str($_POST['position'], $output);
		$order_counter=0;
		foreach ($output as $order_no){
			foreach($order_no as $order_value){			 
			  $category->position = $order_counter;
			  $category->id = $order_value;
			  $category->sort_category_position();
			$order_counter++;
			}
		}
}	
	
/* Delete Category Permanently */		
if(isset($_POST['category_action'],$_POST['category_id']) && $_POST['category_action']=='delete_category' && $_POST['category_id']!=''){
		$category->id = $_POST['category_id'];
		$category->delete(); 
}	

/* Get Category Title  */		
if(isset($_POST['category_action'],$_POST['category_id']) && $_POST['category_action']=='get_category_title' && $_POST['category_id']!=''){
		$category->id= $_POST['category_id'];
		$category->readName(); 
		echo $category->category_title;die();
}	
		
/* Delete Blank Category */	
if(isset($_POST['category_action'],$_POST['category_id']) && $_POST['category_action']=='delete_blank_category' && $_POST['category_id']!=''){
	$category->id= $_POST['category_id'];
	$category->delete_cate();
}
		
/* Create New Category */	
if(isset($_POST['category_action'],$_POST['category_title']) && $_POST['category_action']=='create_category' && $_POST['category_title']!=''){
	
		$category->category_title= $_POST['category_title'];
		$category->location_id= $_SESSION['oct_location'];
		$category->create();
}	
/* Get Category Listing */	
if(isset($_POST['category_action']) && ($_POST['category_action']=='create_category' || $_POST['category_action']=='delete_category' || $_POST['category_action']=='get_category_lsiting')){	
		$category->location_id = $_SESSION['oct_location'];	
		$all_categories = $category->readAll();
		$location_sortby = get_option('octabook_location_sortby');
		$oct_locations = $location->readAll('','','');
		$temp_locatio_name = array();
		$service->location_id = $_SESSION['oct_location'];	
		$all_services = $service->countAll();
?>				
<h3><?php echo __("All Categories","oct");?> <span>(<?php echo sizeof((array)$all_categories);?>)</span>
					<button id="oct-add-new-category" class="pull-right btn btn-circle btn-info" rel="popover" data-placement='bottom' title="<?php echo __("Add New Category","oct");?>"> <i class="fa fa-th-large  icon-space"></i>Category</button>
					
					
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
					<li class=" oct-left-service-menu-li br-2 oct_category_services oct_category_all_service " data-cid="all" >
					<span class="oct-service-sort-icon"><i class="fa fa-th"></i></span>
						<a href="javascript:void(0);" data-toggle="pill">
							<span class="oct-service-name"><?php echo __("All Services","oct");?> (<?php echo $all_services; ?>)</span>
						</a>
					</li>
				</ul>	
				<ul class="nav nav-tab nav-stacked oct-left-service" id="sortable-category-list">
					<?php foreach($all_categories as $oct_category){
								$service->service_category = $oct_category->id;
							$cat_services = $service->readAll_category_services(); ?>
					<li data-cid="<?php echo $oct_category->id;?>" class="oct-left-service-menu-li br-2 oct_category_services f-letter-capitalize" id="category_detail_<?php echo $oct_category->id;?>">
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
<?php } 
if(isset($_POST['category_action']) && $_POST['category_action']=='read_category_dd_options'){	
		$category->location_id = $_SESSION['oct_location'];	
		$all_categories = $category->readAll();
		foreach($all_categories as $oct_category){	?>
			<option value="<?php echo $oct_category->id;?>" ><?php echo $oct_category->category_title;?></option>
			<?php }
}	