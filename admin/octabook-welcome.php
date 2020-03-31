<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

include(dirname(__FILE__).'/header.php');
$plugin_url_for_ajax = plugins_url('',  dirname(__FILE__));
?>
	<div class="container"> 
		<div id="oct-octabook-welcome">
			<div class="oct-welcome-main col-md-12 col-sm-12">
				<h1> Welcome to Octabook 1.0</h1>
				<!-- <div class="oct-into-text">
					Thank you for choosing Octabook ! If this is your first time using Octabook , you will find some helpful "Getting Started" links below. If you just updated the plugin, you can find out what's new in the "What's New" section below. 
				</div> -->
				<div class="oct-into-text">
					Thank you for choosing Octabook ! If this is your first time using Octabook.You can find new features and improvements in "Octabook Change Log" section below. 
				</div>
				<div class="oct-octabook-badge">
					<img src="<?php echo site_url(); ?>/wp-content/plugins/octabook/assets/images/logo-octabook.png" />
					
				</div>
			</div>
			<div class="oct-welcome-inner br-2">
				<div class=""></div>
				<!-- <div class="oct-cleato-articles col-md-6 col-lg-6 ">
					<div class="panel panel-default h-450 br-2">
						<div class="panel-heading bg-info">Getting Started</div>
						<div class="panel-body">
							<ul class="oct-articles-ul">
								
								<li><a href="https://skymoonlabs.ticksy.com/article/11710/" target="_BLANK"> How To Install Plugin ? <i class="fa fa-external-link"></i></li>
								<li><a href="https://skymoonlabs.ticksy.com/article/11718/" target="_BLANK"> Manage Settings <i class="fa fa-external-link"></i></li>
								<li><a href="https://skymoonlabs.ticksy.com/article/11713/" target="_BLANK"> Manage Staff <i class="fa fa-external-link"></i></li>
								<li><a href="https://skymoonlabs.ticksy.com/article/11714/" target="_BLANK"> How To Use Shortcode in Website ?<i class="fa fa-external-link"></i></li>
								<li><a href="https://skymoonlabs.ticksy.com/article/11712/" target="_BLANK"> Manage Services<i class="fa fa-external-link"></i></li>
								<li><a href="https://skymoonlabs.ticksy.com/article/11715/" target="_BLANK"> Manage Appointments Using Calendar<i class="fa fa-external-link"></i></li>
								<li><a href="https://skymoonlabs.ticksy.com/article/11711/" target="_BLANK"> Manage Locations<i class="fa fa-external-link"></i></li>
								<a href="https://skymoonlabs.ticksy.com/articles/100010837/" class="btn-primary btn btn-circle">Read all articles <i class="fa fa-external-link"></i></a>
								
							</ul>
						</div>
					</div>
				</div> -->
				<!--
				<div class="oct-cleato-help col-md-6 col-lg-6 ">
					<div class="panel panel-default h-450 br-2">
						<div class="panel-heading bg-success">Help</div>
						<div class="panel-body">
							<iframe width="100%" height="315" src="" frameborder="0" allowfullscreen></iframe>
						</div>
					</div>
				</div>-->
				<div class="oct-cleato-changelog col-md-12 col-lg-12 ">
					<div class="panel panel-default br-2">
						<div class="panel-heading bg-primary">Octabook Change Log</div>
						<div class="panel-body">
							<div class="oct-changelog-menu col-md-3 col-sm-4 col-xs-12 col-lg-3 np">
								<ul class="nav nav-tab nav-stacked">								
									<li class="active"><a href="#version1_0" data-toggle="pill">1.0 </a></li>
								</ul>
							</div>
							<div class="panel-body">
								<div class="oct-changelog-details tab-content col-md-9 col-sm-8 col-lg-9 col-xs-12 container-fluid">									
									<div class="changelog-details tab-pane active" id="version1_0">
										<h4 class="nm">What's new in 1.0?</h4>
										<ul class="oct-changelog-ul">
											<li><span class="oct-added bg-success br-3 b-shadow">Release</span>Initial release</li>
										</ul>
									</div>
								</div>
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