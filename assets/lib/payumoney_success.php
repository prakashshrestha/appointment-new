<?php
	if(!session_id()) { @session_start(); }
    $root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));	
	if (file_exists($root.'/wp-load.php')) {
		require_once($root.'/wp-load.php');	
	}		
	
	$plugin_url = plugins_url('',  dirname(__FILE__));
	$base =   dirname(dirname(dirname(__FILE__)));
	/* $booking_Info = $_SESSION['ct_details']; */
	$partialdeposite_status = get_option('octabook_partial_deposit_status');
	/* if($partialdeposite_status=='E'){
		$amt = number_format($_SESSION['oct_partialdeposit'],2,".",',');
	}else{
		$amt = number_format($_SESSION['oct_nettotal'],2,".",',');
	} */
	
$status=$_POST["status"];
$firstname=$_POST['firstname'];
$amount = $_POST['amount'];
$txnid=$_POST["txnid"];
$_SESSION['payu_transaction_id'] = $_POST["txnid"];
$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$salt=get_option('octabook_payumoney_saltkey');

If (isset($_POST["additionalCharges"])) {
	$additionalCharges=$_POST["additionalCharges"];
	$retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}else {	  
	$retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$hash = hash("sha512", $retHashSeq);
if ($hash != $posted_hash) {
	echo "Invalid Transaction. Please try again";
}else{
	$plugin_url = plugins_url('',  dirname(__FILE__));
	?>
	<script>window.location.href = '<?php echo $plugin_url; ?>/lib/oct_front_booking_complete.php'; </script>
	<?php
}
?>