<?php 
	include(dirname(__FILE__).'/header.php');
	$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));
	
	/* Create Location */
	$location = new octabook_location();
	$category = new octabook_category();
	$staff = new octabook_staff();
	$service = new octabook_service();
	$general = new octabook_general();
	$oct_image_upload= new octabook_image_upload();
	$oct_currency_symbol = get_option('octabook_currency_symbol');
	
	if(isset($_POST['oct_create_service'])){
		$service->color_tag = $_POST['color_tag'];
		$service->service_title = filter_var($_POST['service_title'], FILTER_SANITIZE_STRING);
		$service->service_description = filter_var($_POST['service_description'], FILTER_SANITIZE_STRING);
		$service->image = $_POST['service_image'];
		$service->service_category = $_POST['service_category'];
		$service->duration = ($_POST['service_duration_hrs']*60) + $_POST['service_duration_mins'];
		$service->amount = $_POST['service_price'];
		$service->offered_price = $_POST['offered_price'];
		$service->location_id = $_SESSION['oct_location'];
		$serice_id = $servicecreate = $service->create();
	}
	/* Get All Services */
	$service->location_id = $_SESSION['oct_location'];
	
	$service->id = $_GET['sid'];
	//echo $_GET['sid'];exit;
	$oct_service_addons = $service->readAll_addons();
	
	
	$service->id = $_GET['sid'];
	$service->readOne();
	$service_title = $service->service_title;
	
	
	 if(!empty($_POST['service_title']))
	{
		/*$service->addons_id = $_GET['sid'];
		$service->addons_location_id = $_SESSION['oct_location']; */
		/* $insert_addons = $service->insert_addons(); */
	}
	if(!empty($_POST['u_service_title']))
	{
		/*$service->addons_id = $_GET['sid'];
		$service->addons_location_id = $_SESSION['oct_location']; */
		$update_addons = $service->addon_update();
	}
?>
<input type="hidden" name="addon_service_id" id="addon_service_id" value="<?php echo $_GET['sid'];?>"></input>
<div id="oct-service-addon-panel" class="panel tab-content table-fixed">
	<div class="panel-body">
		<div class="oct-service-details tab-content col-md-12 col-sm-12 col-lg-12 col-xs-12">
			<ul class="breadcrumb">
                <li><a style="cursor:pointer"  href="?page=services_submenu" class=""><?php echo $service_title; ?></a></li>
                <li><?php echo __("Addons","oct"); ?></li>
            </ul>
			<div class="oct-service-top-header">
				<a href="?page=services_submenu" class="btn btn-success"><i class="fa fa-angle-left icon-space "></i><?php echo __("Back To Services","oct");?></a>
				<div class="pull-right">
					<table>
						<tbody>
							<tr>
								<td>
									<button id="oct-add-new-service-addons" class="btn btn-success" value="add new service"><i class="fa fa-plus icon-space "></i><?php echo __("Create Addon Service","oct");?></button>
								</td>
							</tr>							
						</tbody>
					</table>
					</form>
				</div>
			</div>
			<div id="hr"></div>
			<div class="tab-pane active" id="">
				<div class="tab-content oct-services-right-details">
					<div class="tab-pane active col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="accordion" class="panel-group">
						<ul class="nav nav-tab nav-stacked" id="sortable-services" > <!-- sortable-services -->
							<?php foreach($oct_service_addons as $oct_addon){ 
								$service->id = $oct_addon->id;	?>
							<li id="service_detail_<?php echo $oct_addon->id; ?>" class="panel panel-default oct-services-panel" >
							
								<div class="panel-heading">
									<h4 class="panel-title">
										<div class="col-lg-6 col-sm-7 col-xs-12 np">
											<div class="pull-left">
												<i class="fa fa-th-list"></i>
											</div>	
											<span class="oct-service-title-name f-letter-capitalize"><?php echo $oct_addon->addon_service_name; ?></span>
											
										</div>
										<div class="col-lg-6 col-sm-5 col-xs-12 np">
											<div class="col-lg-2 col-sm-2 col-xs-4 np">
												<span class="oct-service-price-main"><span><?php echo $oct_currency_symbol;?></span><?php echo $oct_addon->base_price; ?></span>
											</div>	
											<div class="col-lg-2 col-sm-2 col-xs-4 np">
												<label for="sevice-endis-<?php echo $oct_addon->id; ?>">
													<input data-id="<?php echo $oct_addon->id; ?>" type="checkbox" class="update_service_addon_status" id="sevice-endis-<?php echo $oct_addon->id; ?>" <?php if($oct_addon->status=='E'){echo 'checked'; } ?> data-toggle="toggle" data-size="small" data-on="<?php echo __("Enable","oct");?>" data-off="<?php echo __("Disable","oct");?>" data-onstyle="success" data-offstyle="danger" >
												</label>
											</div>
											<div class="pull-right">
												<div class="col-lg-2 col-sm-2 col-xs-4 np">
												<a data-poid="oct-popover-delete-service<?php echo $oct_addon->id; ?>" id="oct-delete-service<?php echo $oct_addon->id; ?>" class="pull-right btn-circle btn-danger btn-sm oct-delete-popover" rel="popover" data-placement='bottom' title="<?php echo __("Delete this Addon?","oct");?>"><i class="fa fa-trash" title="<?php echo __("Delete Addon","oct");?>"></i></a>
													<div class="oct-popover" id="oct-popover-delete-service<?php echo $oct_addon->id; ?>" style="display: none;">
														<div class="arrow"></div>
														<table class="form-horizontal" cellspacing="0">
															<tbody>
																<tr>
																	<td>
																		<?php if($service->total_service_bookings()>0){?>
																		<span class="oct-popover-title"><?php echo __("Unable to delete service,having bookings","oct");?></span>
																		<?php }else{?>		
																		<button data-id="<?php echo $oct_addon->id; ?>" value="Delete" class="btn btn-danger btn-sm mr-10 delete_addon" type="submit"><?php echo __("Yes","oct");?></button>
																		<button data-poid="oct-popover-addon<?php echo $oct_addon->id; ?>" class="btn btn-default btn-sm oct-close-popover-delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button><?php } ?>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>											
												<div class="oct-show-hide pull-right">
													<input type="checkbox" name="oct-show-hide" class="oct-show-hide-checkbox " id="<?php echo $oct_addon->id; ?>" ><!--Added Serivce Id-->
													<label class="oct-show-hide-label" for="<?php echo $oct_addon->id; ?>"></label>
												</div>
											</div>
										</div>
										
									</h4>
								</div>
								<div id="" class="service_detail panel-collapse collapse detail-id_<?php echo $oct_addon->id; ?>">
									<div class="panel-body">
										<div class="oct-service-collapse-div col-sm-5 col-md-5 col-lg-5 col-xs-12">
											<form data-sid="<?php echo $oct_addon->id; ?>" id="oct_update_service_addon_<?php echo $oct_addon->id; ?>" method="post" type="" class="slide-toggle oct_update_service_addon" >
												<table class="oct-create-service-table">
													<tbody>
														<tr>
															<td><label for="oct-service-title<?php echo $oct_addon->id; ?>"><?php echo __("Addon Title","oct");?></label></td>
															<td><input type="text" name="u_service_title" class="form-control" id="oct-service-title<?php echo $oct_addon->id; ?>" value="<?php echo $oct_addon->addon_service_name; ?>" /></td>
														</tr>
														
														<tr>
															<td><label for="oct-service-desc"><?php echo __("Addon Image","oct");?></label></td>
															<td>
																<div class="oct-service-image-uploader">
																	<img id="bdscad<?php echo $oct_addon->id; ?>addimage" src="<?php if($oct_addon->image==''){ echo $plugin_url_for_ajax.'/assets/images/addon.png';}else{
																	echo site_url()."/wp-content/uploads".$oct_addon->image;
																	}?>" class="oct-service-image br-100" height="100" width="100">
																	
																	<label <?php if($oct_addon->image==''){ echo "style='display:block'"; }else{ echo "style='display:none'"; } ?> for="oct-upload-imagebdscad<?php echo $oct_addon->id; ?>" class="oct-service-img-icon-label show_image_icon_add<?php echo $oct_addon->id; ?>">
																		<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
																		<i class="pull-left fa fa-plus-circle fa-2x"></i>
																	</label>
																	<input data-us="bdscad<?php echo $oct_addon->id; ?>" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdscad<?php echo $oct_addon->id; ?>"  />
																	<a  id="oct-remove-service-imagebdscad<?php echo $oct_addon->id; ?>" <?php if($oct_addon->image==''){ echo "style='display:none;'";}else{ echo "style='display:block'"; }  ?> class="pull-left br-100 btn-danger oct-remove-service-img btn-xs oct_remove_image" rel="popover" data-placement='bottom' title="<?php echo __("Remove Image?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Remove Service Image","oct");?>"></i></a>
																	
																	
																	
																	<div id="popover-oct-remove-service-imagebdscad<?php echo $oct_addon->id; ?>" style="display: none;">
																		<div class="arrow"></div>
																		<table class="form-horizontal" cellspacing="0">
																			<tbody>
																				<tr>
																					<td>
																						<a href="javascript:void(0)" value="Delete" data-mediaid="<?php echo $oct_addon->id; ?>" data-mediasection='service' data-mediapath="<?php echo $oct_addon->image;?>" data-imgfieldid="bdscad<?php echo $oct_addon->id;?>uploadedimg" class="btn btn-danger btn-sm oct_delete_image"><?php echo __("Yes","oct");?></a>
																						<a href="javascript:void(0)" id="popover-oct-remove-service-imagebdscad<?php echo $oct_addon->id; ?>" class="btn btn-default btn-sm close_delete_popup" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</div>	
											<div id="oct-image-upload-popupbdscad<?php echo $oct_addon->id; ?>" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
												<div class="vertical-alignment-helper">
													<div class="modal-dialog modal-md vertical-align-center">
														<div class="modal-content">
															<div class="modal-header">
																<div class="col-md-12 col-xs-12">
																	<a data-us="bdscad<?php echo $oct_addon->id; ?>" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdscad<?php echo $oct_addon->id; ?>" ><?php echo __("Crop & Save","oct");?></a>
																	<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
																</div>	
															</div>
															<div class="modal-body">
																<img id="oct-preview-imgbdscad<?php echo $oct_addon->id; ?>" />
															</div>
															<div class="modal-footer">
																<div class="col-md-12 np">
																	<div class="col-md-4 col-xs-12">
																		<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdscad<?php echo $oct_addon->id; ?>filesize" name="filesize" />
																	</div>	
																	<div class="col-md-4 col-xs-12">	
																		<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdscad<?php echo $oct_addon->id; ?>h" name="h" /> 
																	</div>
																	<div class="col-md-4 col-xs-12">	
																		<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdscad<?php echo $oct_addon->id; ?>w" name="w" />
																	</div>
																	<input type="hidden" id="bdscad<?php echo $oct_addon->id; ?>x1" name="x1" />
																	 <input type="hidden" id="bdscad<?php echo $oct_addon->id; ?>y1" name="y1" />
																	<input type="hidden" id="bdscad<?php echo $oct_addon->id; ?>x2" name="x2" />
																	<input type="hidden" id="bdscad<?php echo $oct_addon->id; ?>y2" name="y2" />
																	<input id="bdscad<?php echo $oct_addon->id; ?>bdimagetype" type="hidden" name="bdimagetype"/>
																	<input type="hidden" id="bdscad<?php echo $oct_addon->id; ?>bdimagename" name="bdimagename" value="" />
																	</div>
															</div>							
														</div>		
													</div>			
												</div>			
											</div>
											</td>
										<input name="image" id="bdscad<?php echo $oct_addon->id;?>uploadedimg" type="hidden" value="<?php echo $oct_addon->image;?>" />
														</tr>
														
														<tr>
															<td><label for="oct-service-price<?php echo $oct_addon->id; ?>"><?php echo __("Price","oct");?></label></td>
															<td>
																<div class="input-group">
																	<span class="input-group-addon"><?php echo $oct_currency_symbol;?></span>
																	<input name="u_service_price" id="oct-service-price<?php echo $oct_addon->id; ?>" type="text" class="form-control" placeholder="<?php /*echo __("Cancel","oct");*/?>US Dollar" value="<?php echo $oct_addon->base_price; ?>">
																</div>	
																<label id="oct-service-price<?php echo $oct_addon->id; ?>-error" class="error" for="oct-service-price<?php echo $oct_addon->id;?>" style="display:none"></label>
															</td>
														</tr>
														<tr>
														
															<td><label for="oct-service-maxqty"><?php echo __("Max Qty","oct");?></label></td>
															<td><input type="text" name="service_maxqty" class="form-control maxqty<?php echo $oct_addon->id; ?>" id="oct-service-addons-maxqty<?php echo $oct_addon->id; ?>" value="<?php echo $oct_addon->maxqty; ?>" /></td>
														</tr>
													<tr>
														<td><label><?php echo __("Multiple Qty","oct");?></label></td>
													<td>
														<div class="form-group">
														<label for="service_addons_multiple_qty">
															<input type="checkbox" class="addon_multipleqty<?php echo $oct_addon->id;?>" <?php if ($oct_addon->multipleqty == 'Y') { echo"checked"; } ?> id="service_addons_multiple_qty" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" value="<?php echo $oct_addon->multipleqty; ?>"/>
													</label>
													</div>
												</td>
												</tr>
												<tr>
												<td></td>
													<td>
														<button data-service_id="<?php echo $oct_addon->id; ?>" name="" class="btn btn-success oct-btn-width update_service_addon" type="button"><?php echo __("Save","oct");?></button>
														<button type="reset" class="btn btn-default oct-btn-width ml-30"><?php echo __("Reset","oct");?></button>
													</td>
												</tr>
													
													</tbody>
												</table>
											</form>
											
										</div>
										<div class="oct-service-collapse-div col-sm-7 col-md-7 col-lg-7 col-xs-12 mt-20">
										<h6 class="oct-right-header"><?php echo __("Service Addons price rules","oct");?></h6>
										<ul>
										<li>
										<label class="col-xs-12 col-sm-2" for="service_addons_price"><?php echo __("Price","oct");?></label>
											<div class="col-xs-4 col-sm-2 npl">
                                                <input class="form-control" placeholder="1" value="1" id="" type="text" readonly="readonly" /></div>
                                            <div class="col-xs-4 col-sm-2 npl" >
                                                <select class="form-control Addons_price_rules_select" id="">
                                                    <option selected="" readonly value="=">= </option>
                                                </select>
                                            </div>
											<div class="col-xs-4 col-sm-2 npl">
                                                <input class="pull-left form-control" readonly value="<?php echo $oct_addon->base_price; ?>" placeholder="<?php echo __("Price","oct");?> type="text" />
                                            </div>
										</li>
										</ul>
										<ul class="myaddonspricebyqty<?php echo $oct_addon->id; ?>">
										<?php
										$service->addon_service_id = $oct_addon->id;
                                        $idss = $_GET['sid'];
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
											<div class="col-xs-12 col-sm-3  pull-left np" >
                                                <a data-id="<?php echo $r->id; ?>" class="btn btn-success btn-circle mr-15 pull-left myloadedbtnsave_addons update-addon-rule" data-addon_service_id="<?php echo $oct_addon->id;?>"><i class="fa fa-save" title="<?php echo __("Update","oct");?>"></i></a>
											
                                                <a href="javascript:void(0);" data-id="<?php echo $r->id; ?>" class="btn btn-danger btn-circle pull-left delete-addon-rule myloadedbtndelete_addons">
												<i class="fa fa-trash"></i>
												</a>
											</div>
											</form>
										</li>
										<?php
                                        }}
                                        ?>
										<li class="form-group">
										<form class="add_addon_pricing" id="mynewaddedform_addonunits<?php echo $oct_addon->id; ?>">
										<label class="col-xs-12 col-sm-2" for="service_addons_price"><?php echo __("Qty","oct");?></label>
											<div class="col-xs-4 col-sm-2 npl">
                                                <input name="mynewssqtyaddon" id="mynewaddedqty_addon<?php echo $oct_addon->id; ?>" class="form-control mynewqty_addons<?php echo $oct_addon->id; ?>" type="text" />
											</div>
                                            <div class="col-xs-4 col-sm-2 npl">
                                                <select class="form-control Addons_price_rules_select mynewrules_addons<?php echo $oct_addon->id; ?>">
                                                        <option selected value="E">=</option>
                                                        <option value="G"> &gt; </option>
                                                </select>
                                            </div>
											<div class="col-xs-4 col-sm-2 npl">
                                                <input name="mynewsspriceaddon" id="mynewaddedprice_addon<?php echo $oct_addon->id; ?>" class="pull-left form-control mynewprice_addons<?php echo $oct_addon->id; ?>"  placeholder="<?php echo __("Price","oct");?>" type="text" />
                                            </div>
											<div class="col-xs-12 col-sm-3 pull-left npl" >
                                                <a href="javascript:void(0);" data-id="<?php echo $oct_addon->id; ?>" class="btn btn-success btn-circle pull-left mybtnaddnewqty_addon add-addon-price-rule form-group new-manage-price-list"><?php echo __("Add New","oct");?></a>
											</div>
											</form>
										</li>
										
										</ul>
										</div>
									</div>
								</div>
							</li>
							<?php } ?>
							<!-- add new service pop up -->
							<li>
							<div class="panel panel-default oct-services-panel oct-add-new-service-addons">
								<div class="panel-heading">
									<h4 class="panel-title">
										<div class="oct-col6">
											<span class="oct-service-title-name"><?php echo __("Add New Addon Service","oct");?></span>		
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
										<div class="oct-service-collapse-div col-sm-6 col-md-6 col-lg-6 col-xs-12">
											<form id="oct_create_service_addon" method="post" type="" class="slide-toggle" >
												<table class="oct-create-service-table">
													<tbody>
														<tr>
															<td><label for="oct-service-title"><?php echo __("Addon Title","oct");?></label></td>
															<td><input type="text" name="service_title" class="form-control" id="oct-service-addons-title" /></td>
														</tr>
														
														<tr>
															<td><label for="oct-service-desc"><?php echo __("Service Image","oct");?></label></td>
															<td>
																<div class="oct-service-image-uploader">
																	<img id="bdscadlocimage" src="<?php echo $plugin_url_for_ajax; ?>/assets/images/service.png" class="oct-service-image br-100" height="100" width="100">
																	<label for="oct-upload-imagebdscad" class="oct-service-img-icon-label">
																		<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
																		<i class="pull-left fa fa-plus-circle fa-2x"></i>
																	</label>
																	<input data-us="bdscad" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdscad"  />
																	
																	<a style="display: none;" id="oct-remove-service-imagebdscad" class="pull-left br-100 btn-danger oct-remove-service-img btn-xs" rel="popover" data-placement='bottom' title="<?php echo __("Remove Image?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Remove service Image","oct");?>"></i></a>
																	<div id="popover-oct-remove-service-imagebdscad" style="display: none;">
																		<div class="arrow"></div>
																		<table class="form-horizontal" cellspacing="0">
																			<tbody>
																				<tr>
																					<td>
																						<a href="javascript:void(0)" id="" value="Delete" class="btn btn-danger btn-sm" type="submit"><?php echo __("Yes","oct");?></a>
																						<a href="javascript:void(0)" id="oct-close-popover-service-imagebdscad" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div><!-- end pop up -->
																</div>
										<div id="oct-image-upload-popupbdscad" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
											<div class="vertical-alignment-helper">
												<div class="modal-dialog modal-md vertical-align-center">
													<div class="modal-content">
														<div class="modal-header">
															<div class="col-md-12 col-xs-12">
																<a data-us="bdscad" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdscad"><?php echo __("Crop & Save","oct");?></a>
																<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
															</div>	
														</div>
														<div class="modal-body">
															<img id="oct-preview-imgbdscad" />
														</div>
														<div class="modal-footer">
															<div class="col-md-12 np">
																<div class="col-md-4 col-xs-12">
																	<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdscadfilesize" name="filesize" />
																</div>	
																<div class="col-md-4 col-xs-12">	
																	<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdscadh" name="h" /> 
																</div>
																<div class="col-md-4 col-xs-12">	
																	<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdscadw" name="w" />
																</div>
																<input type="hidden" id="bdscadx1" name="x1" />
																 <input type="hidden" id="bdscady1" name="y1" />
																<input type="hidden" id="bdscadx2" name="x2" />
																<input type="hidden" id="bdscady2" name="y2" />
																<input id="bdscadbdimagetype" type="hidden" name="bdimagetype"/>
																<input type="hidden" id="bdscadbdimagename" name="bdimagename" value="" />
															</div>
														</div>							
													</div>		
												</div>			
											</div>			
										</div>
										<input name="service_image" id="bdscaduploadedimg" type="hidden" value="" />						
															</td>
														</tr>
														<tr>
															<td><label for="oct-service-price"><?php echo __("Price","oct");?></label></td>
															<td>
																<div class="input-group">
																	<span class="input-group-addon"><?php echo $oct_currency_symbol;?></span>
																	<input type="text" name="service_addons_price" class="form-control" id="service_addons_price" placeholder="<?php echo __("US Dollar","oct");?>">
																</div>	
																<label id="service_addons_price-error" class="error" for="service_addons_price" style="display:none;"></label>
															</td>
														</tr>
														<tr>
															<td><label for="oct-service-maxqty"><?php echo __("Max Qty","oct");?></label></td>
															<td><input type="text" name="service_maxqty" class="form-control maxqty" id="oct-service-addons-maxqty" /></td>
														</tr>
													<tr>
														<td><label><?php echo __("Multiple Qty","oct");?></label></td>
													<td>
														<div class="form-group">
														<label for="service_addons_multiple_qty">
															<input type="checkbox" class="addon_multipleqty" id="service_addons_multiple_qty" data-toggle="toggle" data-size="small" data-on="<?php echo __("On","oct");?>" data-off="<?php echo __("Off","oct");?>" data-onstyle="success" data-offstyle="default" />
													</label>
													</div>
												</td>
												</tr>
													</tbody>
												</table>
											
										</div>
										<table class="col-sm-7 col-md-7 col-lg-7 col-xs-12 mt-20 mb-20">
											<tbody>
												<tr>
													<td></td>
													<td>
														<button id="oct_create_service_addons" name="oct_create_service_addons" class="btn btn-success oct-btn-width col-md-offset-4" type="button" ><?php echo __("Save","oct");?></button>
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
							
							</ul>
						</div>	
					</div>
				</div>
			</div>
			
		</div>
			
	</div>
</div>
<?php 
	include(dirname(__FILE__).'/footer.php');
?>
<script>
	var serviceObj={"plugin_path":"<?php echo $plugin_url_for_ajax;?>"}
</script>