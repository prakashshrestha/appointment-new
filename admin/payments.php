<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$page_title = "Payments";
include_once "header.php";
$plugin_url_for_ajax = plugins_url('',  dirname(__FILE__));
$general = new octabook_general();
$payments= new octabook_payments();
$order_info = new octabook_order();
if(isset($_SESSION['oct_all_loc_payments']) && $_SESSION['oct_all_loc_payments']=='Y'){
$payments->location_id = 'All';
}else{
$payments->location_id = $_SESSION['oct_location'];
}
$all_payments=$payments->readAll();
?>
<div id="oct-payments" class="panel tab-content">
	<div class="panel panel-default">
		<div class="panel-body">
			<div id="" class="tab-pane fade in active">
				<form id="" name="" class="" method="post">
					
					<div class="col-md-4 col-sm-6 col-xs-12 col-lg-4 ">
						<label class="f-letter-capitalize custom-width custom-width-2"><?php echo __("Select payment option export details","oct");?></label>
							<div id="oct_reportrange" class="form-control custom-width custom-width-2 " >
								<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
								<span></span> <i class="fas fa-caret-down"></i>
							</div>
	
					</div>
					<div class="col-md-2 col-sm-2 col-xs-12 col-lg-2">
						<br />
						<button type="button" class="btn btn-info oct_payments_byrange" name="oct_payments_byrange"><?php echo __("Submit","oct");?></button>
					</div>
					<?php if(current_user_can('manage_options')){?>
					<div class="col-md-4 col-sm-4 col-xs-12 col-lg-4 pull-right">
					<br />
						<div class="oct-custom-checkbox pull-right">
							<ul class="oct-checkbox-list">
								<li>
									<input <?php if(isset($_SESSION['oct_all_loc_payments']) && $_SESSION['oct_all_loc_payments']=='Y'){ echo "checked='checked'"; } ?> type="checkbox" id="oct_all_locations_payments" />
									<label for="oct_all_locations_payments"><?php echo __("All Locations Payments","oct");?><span></span></label>
								</li>
							</ul>
						</div>
					</div>
					<?php } ?>
						
					<div class="mb-5" id="hr"></div>
					<div class="col-md-12 col-lg-12 col-sm-12">
					<div class="table-responsive"> 
						<table id="payments-details" class="display table table-striped table-bordered" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php echo __("Client","oct");?></th>
									<th><?php echo __("Payment method","oct");?></th>
									<th><?php echo __("Total amount","oct");?></th>
									<th><?php echo __("Discount","oct");?></th>
									<th><?php echo __("Tax","oct");?></th>
									<th><?php echo __("Partial Amount","oct");?></th>
									<th><?php echo __("Net Total","oct");?></th>
								</tr>
							</thead>
							<tbody id="oct_payment_details">
						
							
								<?php foreach($all_payments as $payment){ 
									$order_info->order_id = $payment->order_id;
									$order_info->readOne_by_order_id();
								
								 ?>
								<tr>
									<td><?php echo $order_info->client_name;?></td>	
									<?php if($payment->payment_method == 'paypal') { ?>
										<td><?php echo __("Paypal","oct");?></td>
									<?php }
									else if($payment->payment_method == 'pay_locally') { ?>
										<td><?php echo __("Pay Locally","oct");?></td>
									<?php }
									else if($payment->payment_method == 'Free') { ?>
										<td><?php echo __("Free","oct");?></td>
									<?php }
									else if($payment->payment_method == 'stripe') { ?>
										<td><?php echo __("Stripe","oct");?></td>
									<?php }
									else if($payment->payment_method == 'payumoney') { ?>
										<td><?php echo __("PayUmoney","oct");?></td>
									<?php }
									else if($payment->payment_method == 'paytm') { ?>
										<td><?php echo __("Paytm","oct");?></td>
									<?php }
									else{ 
										echo '<td>&nbsp;</td>';
									}	 ?>		
									<td><?php echo $general->oct_price_format($payment->amount);?></td>
									<td><?php echo $general->oct_price_format($payment->discount);?></td>
									<td><?php echo $general->oct_price_format($payment->taxes);?></td>
									<td><?php echo $general->oct_price_format($payment->partial);?></td>
									<td><?php echo $general->oct_price_format($payment->net_total);?></td>
								</tr>	
								<?php }?>		
							</tbody>
						</table>	
					</div>	
					</div>	
				</form>	
			</div>
		</div>
	</div>		
</div>		
<?php 
	include_once "footer.php";
?>