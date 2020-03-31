<?php
if(!session_id()) { @session_start(); }
    $root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));	
	if (file_exists($root.'/wp-load.php')) {
		require_once($root.'/wp-load.php');	
	}		
	
	$plugin_url = plugins_url('',  dirname(__FILE__));
	$base =   dirname(dirname(dirname(__FILE__)));

if (isset($_POST["STATUS"]) && $_POST["STATUS"] == "TXN_SUCCESS") {
	$transaction_id = $_REQUEST['TXNID'];
	$_SESSION['oct_detail']['paytm_transaction_id'] = $transaction_id;
	header('location:'.$plugin_url.'/lib/oct_front_booking_complete.php');
	exit(0);
}
else {
	echo "<h4>Transaction status is failure. You may try making the payment by clicking the link below.</h4><p><a href='".site_url()."/octabook;'> Try Again</a></p>";
	
}