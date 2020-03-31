<?php 
	include(dirname(__FILE__).'/header.php');
	$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));
	
	 /* Intialization of Class Object */
	$category = new octabook_category();
	$location = new octabook_location();
	$service = new octabook_service();
	$general = new octabook_general();
	$payments= new octabook_payments();
	$staff = new octabook_staff();
	$order_info = new octabook_order();
	$oct_bookings = new octabook_booking();
	$provider = new octabook_staff();
	$clients = new octabook_clients();
	$oct_bookings->location_id = $_SESSION['oct_location'];
	$todayAnalyatics = $oct_bookings->get_today_booking_and_earning();
	$weekstartdate = date_i18n('Y-m-d',strtotime('monday this week'));
	$weekenddatedate = date_i18n('Y-m-d',strtotime('sunday this week'));
	$first_day_this_month = date_i18n('Y-m-01');
	$last_day_this_month  = date_i18n('Y-m-t');
	$weekAnalyatics = $oct_bookings->get_week_booking_and_earning($weekstartdate,$weekenddatedate);
	$monthAnalyatics = $oct_bookings->get_month_booking_and_earning($first_day_this_month,$last_day_this_month);
	$yearAnalyatics = $oct_bookings->get_year_booking_and_earning();	
	$upcommingbookings = $oct_bookings->today_upcomming_appointments();

	$latestactivitybookings = $oct_bookings->get_booking_by_latest_activity();
	$pastquickactionbookings = $oct_bookings->get_past_pending_quickaction_bookings();	
	$oct_currency_symbol = get_option('octabook_currency_symbol');
	
?>

<div class="panel oct-panel-default" id="oct-dashboard">
	<div class="panel-body">
		<ul class="nav nav-tab oct-top-menus-stats">
			<li class="col-lg-3 col-md-4 col-sm-4 col-xs-12 mb-10" style="">
				<a href="#oct-this-week-stats" data-toggle="pill" class="oct-today-title bg-radius">
					<h4 class="oct-dash-header pull-right"><?php echo __('Today',"oct"); ?></h4>
					<div class="oct-title-amount-stats">
						<div class="oct-icon-booking-stats pull-left">
							<div class="oct-dash-icon this-week">
								<img src="<?php echo $plugin_url_for_ajax; ?>/assets/images/cal-year.png" />
							</div>
							<div class="oct-dash-details">
								<span class="oct-stats-title"><?php echo __('Bookings',"oct"); ?></span>
								<span class="oct-stats-counting"><?php echo $todayAnalyatics[0]->bookings;?></span>
							</div>
						</div>
					</div>
					<div class="oct-stats-total pull-right">
						<span class="oct-currency-stats"><?php echo $oct_currency_symbol;?></span><?php echo number_format($todayAnalyatics[0]->earning,get_option('octabook_price_format_decimal_places'),".",',');?>
					</div>
				</a>
			</li>
			<li class="col-lg-3 col-md-4 col-sm-4 col-xs-12 mb-10">
				<a href="#oct-this-week-stats" data-toggle="pill" class="oct-thisweek-title bg-radius">
					<h4 class="oct-dash-header pull-right"><?php echo __('This Week',"oct"); ?></h4>
					<div class="oct-title-amount-stats">
						<div class="oct-icon-booking-stats pull-left">
							<div class="oct-dash-icon this-week">
								<img src="<?php echo $plugin_url_for_ajax; ?>/assets/images/cal-year.png" />
							</div>
							<div class="oct-dash-details">
								<span class="oct-stats-title"><?php echo __('Bookings',"oct"); ?></span>
								<span class="oct-stats-counting"><?php echo $weekAnalyatics[0]->bookings;?></span>
							</div>
						</div>
					</div>
					<div class="oct-stats-total pull-right">
						<span class="oct-currency-stats"><?php echo $oct_currency_symbol;?></span><?php echo number_format($weekAnalyatics[0]->earning,get_option('octabook_price_format_decimal_places'),".",',');?>
					</div>
						
				</a>
			</li>
			<li class="col-lg-3 col-md-4 col-sm-4 col-xs-12 mb-10">
				<a href="#oct-this-week-stats" data-toggle="pill" class="oct-thisweek-title bg-radius">
					<h4 class="oct-dash-header pull-right"><?php echo __('This Month',"oct"); ?></h4>
					<div class="oct-title-amount-stats">
						<div class="oct-icon-booking-stats pull-left">
							<div class="oct-dash-icon this-week">
								<img src="<?php echo $plugin_url_for_ajax; ?>/assets/images/cal-year.png" />
							</div>
							<div class="oct-dash-details">
								<span class="oct-stats-title"><?php echo __('Bookings',"oct"); ?></span>
								<span class="oct-stats-counting"><?php echo $monthAnalyatics[0]->bookings;?></span>
							</div>
						</div>
					</div>
					<div class="oct-stats-total pull-right">
						<span class="oct-currency-stats"><?php echo $oct_currency_symbol;?></span><?php echo number_format($monthAnalyatics[0]->earning,get_option('octabook_price_format_decimal_places'),".",',');?>
					</div>
						
				</a>
			</li>
			<li class="col-lg-3 col-md-4 col-sm-4 col-xs-12 mb-10">
				<a href="#oct-this-week-stats" data-toggle="pill" class="oct-thisweek-title bg-radius">
					<h4 class="oct-dash-header pull-right"><?php echo __('Total',"oct"); ?></h4>
					<div class="oct-title-amount-stats">
						<div class="oct-icon-booking-stats pull-left">
							<div class="oct-dash-icon this-week">
								<img src="<?php echo $plugin_url_for_ajax; ?>/assets/images/cal-year.png" />
							</div>
							<div class="oct-dash-details">
								<span class="oct-stats-title"><?php echo __('Bookings',"oct"); ?></span>
								<span class="oct-stats-counting"><?php echo $yearAnalyatics[0]->bookings;?></span>
							</div>
						</div>
					</div>
					<div class="oct-stats-total pull-right">
						<span class="oct-currency-stats"><?php echo $oct_currency_symbol;?></span><?php echo number_format($yearAnalyatics[0]->earning,get_option('octabook_price_format_decimal_places'),".",',');?>
					</div>
						
				</a>
			</li>
		</ul>
		<div class="panel-body p-0 mt-20">	
			<div class="col-md-6 col-lg-7 rnp">
				<div class="tab-content  b-shadow of-h main_panel_height">
					<div class="tab-pane active" id="oct-today-stats">	
						<div class="oct-left-menu-stats">
							<ul class="nav nav-tabs">
								<li class="w-20 com-respo-width">
									<div class="dropdown">
										<button class="btn btn_dropdown_select btn-primary dropdown-toggle btn-dropdown" type="button" data-toggle="dropdown"><?php echo __('Filter',"oct"); ?>
										<span class="caret"></span></button>
										<ul class="dropdown-menu">
											<li><a href="#" class="Analytics_data" data-method = "today" id='Analytics_data_today'><?php echo __('Today',"oct"); ?></a></li>
											<li><a href="#" class="Analytics_data" data-method = "week" id='Analytics_data_week'><?php echo __('This Week',"oct"); ?></a></li>
											<li><a href="#" class="Analytics_data" data-method = "month" id='Analytics_data_month'><?php echo __('This Month',"oct"); ?></a></li>
										</ul>
									</div>
								</li>
								<li class="active oct-col4 oct_view_chart_analytics oct_service_chart" data-method="service"><a href="#service-view-tab" data-toggle="pill" ><?php echo __('Services View',"oct"); ?></a></li>
								<li class="oct-col4 oct_view_chart_analytics" data-method="provider"><a href="#provider-view-tab" data-toggle="pill"><?php echo __('Provider View',"oct"); ?></a></li>
							</ul>
						</div>
						
						<div id="service-view-tab" class="tab-pane fade in active ta-c col-md-12 col-sm-12 col-lg-12 col-xs-12 mt-20 mb-20 chart_view_content" style="margin-bottom: 430px !important;">
							<div id="chartContainer_service" style="height: 100%; width: 100%;"></div>
							<div id="chartContainer_service_today" style="height: 100%; width: 100%;"></div>
							<div id="chartContainer_service_week" style="height: 100%; width: 100%;"></div>
							<div id="chartContainer_service_month" style="height: 100%; width: 100%;"></div>
							<div class="oct_nodata_service oct-no-booking-chart">
								<i class="fa fa-cogs fa-3x"></i>
								<h3><?php echo __('No service data available',"oct"); ?> </h3>		
							</div>
						</div>
						<div id="provider-view-tab" class="tab-pane fade ta-c col-md-12 col-sm-12 col-lg-12 col-xs-12 mt-20 mb-20 chart_view_content" style="margin-bottom: 430px !important;">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="line">
										<div id="chartContainer_provider" style="height: 100%; width: 100%;"></div>
										<div id="chartContainer_provider_today" data-method = "today" style="height: 100%; width: 100%;"></div>
										<div id="chartContainer_provider_week" data-method = "week" style="height: 100%; width: 100%;"></div>
										<div id="chartContainer_provider_month" data-method = "month" style="height: 100%; width: 100%;"></div>
								</div>
							</div>
							<div class="oct_nodata_provider oct-no-booking-chart">
								<i class="fa fa-user fa-3x"></i>
								<h3><?php echo __('No provider data available',"oct"); ?> </h3>		
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end chart -->
		<div class="oct-today-bookings col-md-6 col-lg-5 rnp">
		<div class="panel panel-default b-shadow panel_height">
			<div class="panel-heading bg-success"><?php echo __("Upcomming Booking","oct"); ?></div>
			<div class="panel-body">
			<?php if(sizeof((array)$upcommingbookings)!=0){
							foreach($upcommingbookings as $upcommingbooking){
							
								$service->id= $upcommingbooking->service_id;
								$service->readone();
								$service_title=stripslashes_deep($service->service_title);
								$servicedurationstrinng = '';
								if(floor($service->duration/60)!=0){ $servicedurationstrinng .= floor($service->duration/60); $servicedurationstrinng .= __(" Hrs","oct"); } 
								if($service->duration%60 !=0){  $servicedurationstrinng .= $service->duration%60; $servicedurationstrinng .= __(" Mins","oct"); }
								$booking_datetime = $upcommingbooking->booking_datetime;
								$dayname = date('l', strtotime($booking_datetime));
								$staff->id=$upcommingbooking->provider_id;
								$staff_info = $staff->readOne();   
								$provider_name = ucfirst($staff_info[0]['staff_name']);				
								$clients->order_id=$upcommingbooking->order_id;
								$client_info = $clients->get_client_info_by_order_id();
								$clientname= $client_info[0]->client_name;
								$yrdata= strtotime($booking_datetime);
								$date_format =  date('d-M-Y', $yrdata);
									?>
								<div class="oct-no-today-booking-message" data-bookingid = '<?php echo $upcommingbooking->id;?>'>
									<div class="oct-client_name">
										<span><?php echo $service_title ;?> with <b><?php echo $provider_name;?></b> On <?php echo $dayname;?></span>
										<!-- <span>On <?php echo $dayname;?></span> -->
									</div>
									<div class="oct-client_info">
										<span>
										
										<i class="fa fa-calendar mr-10" aria-hidden="true"></i><?php echo $date_format;?> at <?php echo date_i18n(get_option('time_format'),strtotime($upcommingbooking->booking_datetime)); ?></span>
										<div class="oct-time-name">
											<span><i class="far fa-clock mr-11" aria-hidden="true"></i><?php echo $servicedurationstrinng; ?></span>
											<span class="mr"><i class="fa fa-user mr-11" aria-hidden="true"></i><?php echo $clientname;?></span>
										</div>
									</div>
									<button type="button" class="btn btn-primary btn-active oct-today-list" data-bookingid = '<?php echo $upcommingbooking->id;?>' data-toggle="modal" data-target="#booking-details"><i class="fa fa-bell mr-10" aria-hidden="true"></i>Active</button>

								</div>
							<?php 
							} 
						}else{ ?>
										<div class="oct-no-today-booking-message">
												<i class="fa fa-clock-o fa-3x"></i>
												<h3 class="f18"><?php echo __('No Appointments Today',"oct"); ?></h3>		
										</div>
					 <?php } ?>
			</div>
		</div>
	</div>
<?php 
	include('footer.php');	
?>