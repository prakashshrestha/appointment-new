<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
include_once "header.php";
global $wpdb;
$page_title = "Reviews";
$plugin_url_for_ajax = plugins_url('',  dirname(__FILE__));
$reviews = new octabook_reviews();
$clients = new octabook_clients();
$oct_bookings = new octabook_booking();
$service = new octabook_service();
$order_info = new octabook_order();

$reviews->location_id = $_SESSION['oct_location'];
$all_reviews_info = $reviews->readAll();


?>
<div id="oct-reviews" class="panel tab-content">
	<div class="panel panel-default">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#published_reviews"><?php echo __("Published Reviews","oct");?></a></li>
			<li><a data-toggle="tab" href="#pending_reviews"><?php echo __("Pending Reviews","oct");?></a></li>
			<li><a data-toggle="tab" href="#hidden_reviews"><?php echo __("Hidden Reviews","oct");?></a></li>
			
		</ul>
		<div class="tab-content">
			<div id="published_reviews" class="tab-pane fade in active">
				<h3><?php echo __("Published Reviews","oct");?></h3>  <div class=""></div>
					<div id="accordion" class="panel-group">
						<div class="oct-customer-reviews cb">
							<form class="form-horizontal">
								<div class="table-responsive">
									<table id="oct-published-reviews-table" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><?php echo __("#","oct");?></th>
												<th><?php echo __("Date","oct");?></th>
												<th><?php echo __("Service","oct");?></th>
												<th><?php echo __("Customer","oct");?></th>
												<th><?php echo __("Provider","oct");?></th>									
												<th width="63px;"><?php echo __("Rating","oct");?></th>
												<th width="250px;"><?php echo __("Review","oct");?></th>
												<th width="52px;"><?php echo __("Actions","oct");?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($all_reviews_info as $review_info){ 
													if($review_info->status!='P'){continue;}													
													$staffinfo = get_user_by( 'id',$review_info->provider_id );	
													$oct_bookings->booking_id =$review_info->booking_id; 
													$oct_bookings->readOne_by_booking_id();
													/* Service Info */						
													$service->id=$oct_bookings->service_id;
													$service->readOne(); 
													/* Client Info */	
													$clients->order_id=$oct_bookings->order_id;
													$client_info = $clients->get_client_info_by_order_id();
												
											?>
											<tr>
												
												<td><?php echo $review_info->id;?></td>
												<td><?php echo date_i18n(get_option('date_format'),strtotime($review_info->id));?></td>
												<td><?php echo stripslashes_deep($service->service_title);?></td>
												<td><?php echo stripslashes_deep($client_info[0]->client_name);?></td>
												<td><?php echo $staffinfo->data->display_name;?></td>
												
													<!-- <fieldset class="rating">
														  <input <?php if($review_info->rating=='5'){ echo 'checked="checked"';} ?> type="radio" id="star5<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="5" /><label class="full" for="star5<?php echo $review_info->id;?>" title="<?php echo __("Awesome - 5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='4.5'){ echo 'checked="checked"';} ?> type="radio" id="star4half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="4.5" /><label class="half" for="star4half<?php echo $review_info->id;?>" title="<?php echo __("Pretty good - 4.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='4'){ echo 'checked="checked"';} ?> type="radio" id="star4<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="4" /><label class="full" for="star4<?php echo $review_info->id;?>" title="<?php echo __("Pretty good - 4 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='3.5'){ echo 'checked="checked"';} ?> type="radio" id="star3half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="3.5" /><label class="half" for="star3half<?php echo $review_info->id;?>" title="<?php echo __("Meh - 3.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='3'){ echo 'checked="checked"';} ?> type="radio" id="star3<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="3" /><label class="full" for="star3<?php echo $review_info->id;?>" title="<?php echo __("Meh - 3 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='2.5'){ echo 'checked="checked"';} ?> type="radio" id="star2half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="2.5" /><label class="half" for="star2half<?php echo $review_info->id;?>" title="<?php echo __("Kinda bad - 2.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='2'){ echo 'checked="checked"';} ?> type="radio" id="star2<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="2" /><label class="full" for="star2<?php echo $review_info->id;?>" title="<?php echo __("Kinda bad - 2 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='1.5'){ echo 'checked="checked"';} ?> type="radio" id="star1half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="1.5" /><label class="half" for="star1half<?php echo $review_info->id;?>" title="<?php echo __("Meh - 1.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='1'){ echo 'checked="checked"';} ?> type="radio" id="star1<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="1" /><label class="full" for="star1<?php echo $review_info->id;?>" title="<?php echo __("Sucks big time - 1 star","oct");?>"></label>
														  <input <?php if($review_info->rating=='0.5'){ echo 'checked="checked"';} ?> type="radio" id="starhalf<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="0.5" /><label class="half" for="starhalf<?php echo $review_info->id;?>" title="<?php echo __("Sucks big time - 0.5 stars","oct");?>"></label>
														 
														</fieldset>	-->		
												<td><?php 
												if(isset($review_info->rating)){
												
												
												$starNumber=$review_info->rating;;
												for($x=1;$x<=$starNumber;$x++) {
												?>
												<i class="fa fa-star" style="color:#f0ad4e;">
												<?php 
												}
												if (strpos($starNumber,'.5')) {
												?>
												<i class="fa fa-star-half" style="color:#f0ad4e;">
												<?php $x++;
												}
													while ($x<=5) {
													echo '';
													$x++;
												}
												
												
												}else{echo '-';}?></td>
												<td class="review-text"><?php echo $review_info->description;?></td>
												<td class="review-action">							
													<a data-method='H' data-review_id='<?php echo $review_info->id;?>' href="javascript:void(0)" class="btn btn-info oct_review_phd" title="<?php echo __("Hide Review","oct");?>"><i class="fa fa-eye"></i></a>
													<a href="javascript:void(0)" id="oct-delete-review<?php echo $review_info->id;?>" class="pull-right btn btn-circle btn-danger oct-delete-review" rel="popover" data-placement='bottom' title="<?php echo __("Delete Review?","oct");?>"> <i class="fas fa-trash"></i></a>
								
													<div id="popover-oct-delete-review<?php echo $review_info->id;?>" style="display: none;">
														<div class="arrow"></div>
														<table class="form-horizontal" cellspacing="0">
															<tbody>
																
																<tr>
																	<td>
																		<a data-method='delete' data-review_id='<?php echo $review_info->id;?>' value="Delete" class="btn btn-danger btn-sm oct_review_phd" href="javascript:void(0)"><?php echo __("Yes","oct");?></a>
																		<a id="oct-close-popover-oct-delete-review<?php echo $review_info->id;?>" class="btn btn-default btn-sm oct-close-popover-oct-delete-review" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												
												</td>
											</tr>
											<?php } ?>											
										</tbody>
									</table>
								</div>	
							</form>
						</div>					
						
					</div>
			</div>
			<div id="pending_reviews" class="tab-pane fade in">
				<h3><?php echo __("Pending Reviews","oct");?></h3>  <div class=""></div>
					<div id="accordion" class="panel-group">
						<div class="oct-customer-reviews cb">
							<form class="form-horizontal">
								<div class="table-responsive">
									<table id="oct-pending-reviews-table" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><?php echo __("#","oct");?></th>
												<th><?php echo __("Date","oct");?></th>
												<th><?php echo __("Service","oct");?></th>
												<th><?php echo __("Customer","oct");?></th>
												<th><?php echo __("Provider","oct");?></th>			
												<th width="63px;"><?php echo __("Rating","oct");?></th>
												<th width="250px;"><?php echo __("Review","oct");?></th>
												<th width="78px;"><?php echo __("Actions","oct");?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($all_reviews_info as $review_info){ 
													if($review_info->status!='A'){continue;}													
													$staffinfo = get_user_by( 'id',$review_info->provider_id );	
													$oct_bookings->booking_id =$review_info->booking_id; 
													$oct_bookings->readOne_by_booking_id();
													/* Service Info */						
													$service->id=$oct_bookings->service_id;
													$service->readOne(); 
													/* Client Info */	
													$clients->order_id=$oct_bookings->order_id;
													$client_info = $clients->get_client_info_by_order_id();
												
											?>
											<tr>
												
												<td><?php echo $review_info->id;?></td>
												<td><?php echo date_i18n(get_option('date_format'),strtotime($review_info->id));?></td>
												<td><?php echo stripslashes_deep($service->service_title);?></td>
												<td><?php echo stripslashes_deep($client_info[0]->client_name);?></td>
												<td><?php echo $staffinfo->data->display_name;?></td>
												
													<!-- <fieldset class="rating">
														  <input <?php if($review_info->rating=='5'){ echo 'checked="checked"';} ?> type="radio" id="star5<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="5" /><label class="full" for="star5<?php echo $review_info->id;?>" title="<?php echo __("Awesome - 5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='4.5'){ echo 'checked="checked"';} ?> type="radio" id="star4half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="4.5" /><label class="half" for="star4half<?php echo $review_info->id;?>" title="<?php echo __("Pretty good - 4.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='4'){ echo 'checked="checked"';} ?> type="radio" id="star4<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="4" /><label class="full" for="star4<?php echo $review_info->id;?>" title="<?php echo __("Pretty good - 4 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='3.5'){ echo 'checked="checked"';} ?> type="radio" id="star3half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="3.5" /><label class="half" for="star3half<?php echo $review_info->id;?>" title="<?php echo __("Meh - 3.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='3'){ echo 'checked="checked"';} ?> type="radio" id="star3<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="3" /><label class="full" for="star3<?php echo $review_info->id;?>" title="<?php echo __("Meh - 3 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='2.5'){ echo 'checked="checked"';} ?> type="radio" id="star2half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="2.5" /><label class="half" for="star2half<?php echo $review_info->id;?>" title="<?php echo __("Kinda bad - 2.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='2'){ echo 'checked="checked"';} ?> type="radio" id="star2<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="2" /><label class="full" for="star2<?php echo $review_info->id;?>" title="<?php echo __("Kinda bad - 2 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='1.5'){ echo 'checked="checked"';} ?> type="radio" id="star1half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="1.5" /><label class="half" for="star1half<?php echo $review_info->id;?>" title="<?php echo __("Meh - 1.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='1'){ echo 'checked="checked"';} ?> type="radio" id="star1<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="1" /><label class="full" for="star1<?php echo $review_info->id;?>" title="<?php echo __("Sucks big time - 1 star","oct");?>"></label>
														  <input <?php if($review_info->rating=='0.5'){ echo 'checked="checked"';} ?> type="radio" id="starhalf<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="0.5" /><label class="half" for="starhalf<?php echo $review_info->id;?>" title="<?php echo __("Sucks big time - 0.5 stars","oct");?>"></label>
														 
														</fieldset>	-->		
												<td><?php 
												if(isset($review_info->rating)){
												
												
												$starNumber=$review_info->rating;;
												for($x=1;$x<=$starNumber;$x++) {
												?>
												<i class="fa fa-star" style="color:#f0ad4e;">
												<?php 
												}
												if (strpos($starNumber,'.5')) {
												?>
												<i class="fa fa-star-half" style="color:#f0ad4e;">
												<?php $x++;
												}
													while ($x<=5) {
													echo '';
													$x++;
												}
												
												
												}else{echo '-';}?></td>
												
												<td class="review-text"><?php echo $review_info->description;?></td>
												<td class="review-action">
													<a data-method='P' data-review_id='<?php echo $review_info->id;?>' href="javascript:void(0)" class="btn btn-success oct_review_phd" title="<?php echo __("Approve & Publish","oct");?>"><i class="fas fa-check"></i></a>
													<a data-method='H' data-review_id='<?php echo $review_info->id;?>' href="javascript:void(0)" class="btn btn-info oct_review_phd" title="<?php echo __("Hide Review","oct");?>"><i class="fa fa-eye"></i></a>
													<a href="javascript:void(0)" id="oct-delete-review<?php echo $review_info->id;?>" class="pull-right btn btn-circle btn-danger oct-delete-review" rel="popover" data-placement='bottom' title="<?php echo __("Delete Review?","oct");?>"> <i class="fas fa-trash"></i></a>
								
													<div id="popover-oct-delete-review<?php echo $review_info->id;?>" style="display: none;">
														<div class="arrow"></div>
														<table class="form-horizontal" cellspacing="0">
															<tbody>
																
																<tr>
																	<td>
																		<a data-method='delete' data-review_id='<?php echo $review_info->id;?>' value="Delete" class="btn btn-danger btn-sm oct_review_phd" href="javascript:void(0)"><?php echo __("Yes","oct");?></a>
																		<a id="oct-close-popover-oct-delete-review<?php echo $review_info->id;?>" class="btn btn-default btn-sm oct-close-popover-oct-delete-review" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>	
							</form>
						</div>					
						
					</div>
			</div>
			<div id="hidden_reviews" class="tab-pane fade in">
				<h3><?php echo __("Hidden Reviews","oct");?></h3>  <div class=""></div>
					<div id="accordion" class="panel-group">
						<div class="oct-customer-reviews cb">
							<form class="form-horizontal">
								<div class="table-responsive">
									<table id="oct-hidden-reviews-table" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><?php echo __("#","oct");?></th>
												<th><?php echo __("Date","oct");?></th>
												<th><?php echo __("Service","oct");?></th>
												<th><?php echo __("Customer","oct");?></th>
												<th><?php echo __("Provider","oct");?></th>			
												<th width="63px;"><?php echo __("Rating","oct");?></th>
												<th width="250px;"><?php echo __("Review","oct");?></th>
												<th width="52px;"><?php echo __("Actions","oct");?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($all_reviews_info as $review_info){ 
													if($review_info->status!='H'){continue;}													
													$staffinfo = get_user_by( 'id',$review_info->provider_id );	
													$oct_bookings->booking_id =$review_info->booking_id; 
													$oct_bookings->readOne_by_booking_id();
													/* Service Info */						
													$service->id=$oct_bookings->service_id;
													$service->readOne(); 
													/* Client Info */	
													$clients->order_id=$oct_bookings->order_id;
													$client_info = $clients->get_client_info_by_order_id();
												
											?>
											<tr>
												
												<td><?php echo $review_info->id;?></td>
												<td><?php echo date_i18n(get_option('date_format'),strtotime($review_info->id));?></td>
												<td><?php echo stripslashes_deep($service->service_title);?></td>
												<td><?php echo stripslashes_deep($client_info[0]->client_name);?></td>
												<td><?php echo $staffinfo->data->display_name;?></td>
												<td>
													<fieldset class="rating">
														  <input <?php if($review_info->rating=='5'){ echo 'checked="checked"';} ?> type="radio" id="star5<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="5" /><label class="full" for="star5<?php echo $review_info->id;?>" title="<?php echo __("Awesome - 5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='4.5'){ echo 'checked="checked"';} ?> type="radio" id="star4half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="4.5" /><label class="half" for="star4half<?php echo $review_info->id;?>" title="<?php echo __("Pretty good - 4.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='4'){ echo 'checked="checked"';} ?> type="radio" id="star4<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="4" /><label class="full" for="star4<?php echo $review_info->id;?>" title="<?php echo __("Pretty good - 4 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='3.5'){ echo 'checked="checked"';} ?> type="radio" id="star3half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="3.5" /><label class="half" for="star3half<?php echo $review_info->id;?>" title="<?php echo __("Meh - 3.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='3'){ echo 'checked="checked"';} ?> type="radio" id="star3<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="3" /><label class="full" for="star3<?php echo $review_info->id;?>" title="<?php echo __("Meh - 3 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='2.5'){ echo 'checked="checked"';} ?> type="radio" id="star2half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="2.5" /><label class="half" for="star2half<?php echo $review_info->id;?>" title="<?php echo __("Kinda bad - 2.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='2'){ echo 'checked="checked"';} ?> type="radio" id="star2<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="2" /><label class="full" for="star2<?php echo $review_info->id;?>" title="<?php echo __("Kinda bad - 2 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='1.5'){ echo 'checked="checked"';} ?> type="radio" id="star1half<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="1.5" /><label class="half" for="star1half<?php echo $review_info->id;?>" title="<?php echo __("Meh - 1.5 stars","oct");?>"></label>
														  <input <?php if($review_info->rating=='1'){ echo 'checked="checked"';} ?> type="radio" id="star1<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="1" /><label class="full" for="star1<?php echo $review_info->id;?>" title="<?php echo __("Sucks big time - 1 star","oct");?>"></label>
														  <input <?php if($review_info->rating=='0.5'){ echo 'checked="checked"';} ?> type="radio" id="starhalf<?php echo $review_info->id;?>" name="octabook_rating<?php echo $review_info->id;?>" value="0.5" /><label class="half" for="starhalf<?php echo $review_info->id;?>" title="<?php echo __("Sucks big time - 0.5 stars","oct");?>"></label>
														 
														</fieldset>			
												</td>
												<td class="review-text"><?php echo $review_info->description;?></td>
												<td class="review-action">
													<a data-method='P' data-review_id='<?php echo $review_info->id;?>' href="javascript:void(0)" class="btn btn-success oct_review_phd" title="<?php echo __("Approve & Publish","oct");?>"><i class="fas fa-check"></i></a>
													
																							
													<a href="javascript:void(0)" id="oct-delete-review<?php echo $review_info->id;?>" class="pull-right btn btn-circle btn-danger oct-delete-review" rel="popover" data-placement='bottom' title="<?php echo __("Delete Review?","oct");?>"> <i class="fas fa-trash"></i></a>
								
													<div id="popover-oct-delete-review<?php echo $review_info->id;?>" style="display: none;">
														<div class="arrow"></div>
														<table class="form-horizontal" cellspacing="0">
															<tbody>
																
																<tr>
																	<td>
																		<a data-method='delete' data-review_id='<?php echo $review_info->id;?>' value="Delete" class="btn btn-danger btn-sm oct_review_phd" href="javascript:void(0)"><?php echo __("Yes","oct");?></a>
																		<a id="oct-close-popover-oct-delete-review<?php echo $review_info->id;?>" class="btn btn-default btn-sm oct-close-popover-oct-delete-review" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
													
												
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>	
							</form>
						</div>					
						
					</div>
			</div>
			
			
		</div>
	</div>
</div>
<?php 
	include_once "footer.php";
?>
