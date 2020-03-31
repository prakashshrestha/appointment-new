<?php 
session_start();
$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
			if (file_exists($root.'/wp-load.php')) {
			require_once($root.'/wp-load.php');
}
if ( ! defined( 'ABSPATH' ) ) exit;  /* direct access prohibited  */

	$settings = new octabook_location();
	$emailtemplates = new octabook_email_template();
	$smstemplates = new octabook_sms_template();
	$coupons = new octabook_coupons();
	$plugin_url_for_ajax = plugins_url('',dirname(dirname(__FILE__)));
	$octabook_currencysymbol_array =  array('ALL'=>'Lek', 'AFN'=>'؋', 'ARS'=>'$', 'AWG'=>'ƒ', 'AUD'=>'$', 'AZN'=>'ман', 'AED'=>'د.إ', 'ANG'=>'NAƒ',	'BSD'=>'$', 'BBD'=>'$', 'BYR'=>'p.', 'BZD'=>'BZ$', 'BMD'=>'$', 'BOB'=>'$b', 'BAM'=>'KM', 'BWP'=>'P', 'BGN'=>'лв', 'BRL'=>'R$', 'BND'=>'$', 'BDT'=>'Tk', 'BIF'=>'FBu',	 'KHR'=>'៛', 'CAD'=>'$', 'KYD'=>'$', 'CLP'=>'$', 'CNY'=>'¥', 'CYN'=>'¥', 'COP'=>'$', 'CRC'=>'₡', 'HRK'=>'kn', 'CUP'=>'₱', 'CZK'=>'Kč', 'CVE'=>'Esc', 'CHF'=>'CHF',	 'DKK'=>'kr', 'DOP'=>'RD$', 'DJF'=>'Fdj', 'DZD'=>'دج',	 'XCD'=>'$', 'EGP'=>'£', 'SVC'=>'$', 'EEK'=>'kr', 'EUR'=>'€', 'ETB'=>'Br', 'FKP'=>'£',	 'FJD'=>'$', 'GHC'=>'¢', 'GIP'=>'£', 'GTQ'=>'Q', 'GGP'=>'£', 'GYD'=>'$', 'GMD'=>'D', 'GNF'=>'FG', 'HNL'=>'L', 'HKD'=>'$', 'HUF'=>'Ft', 'HRK'=>'kn', 'HTG'=>'G',	 'ISK'=>'kr', 'INR'=>'Rs.', 'IDR'=>'Rp', 'IRR'=>'﷼', 'IMP'=>'£', 'ILS'=>'₪',	 'JMD'=>'J$', 'JPY'=>'¥', 'JEP'=>'£',	 'KZT'=>'лв', 'KPW'=>'₩', 'KRW'=>'₩', 'KGS'=>'лв', 'KES'=>'KSh', 'KMF'=>'KMF',	 'LAK'=>'₭', 'LVL'=>'Ls', 'LBP'=>'£', 'LRD'=>'$', 'LTL'=>'Lt',	 'MKD'=>'ден', 'MYR'=>'RM', 'MUR'=>'₨', 'MXN'=>'$', 'MNT'=>'₮', 'MZN'=>'MT', 'MDL'=>'MDL', 'MOP'=>'$', 'MRO'=>'UM', 'MVR'=>'Rf', 'MWK'=>'MK', 'MAD'=>'د.م.',	 'NAD'=>'$', 'NPR'=>'₨', 'ANG'=>'ƒ', 'NZD'=>'$', 'NIO'=>'C$', 'NGN'=>'₦', 'NOK'=>'kr', 'OMR'=>'﷼', 'PKR'=>'₨', 'PAB'=>'B/.', 'PYG'=>'Gs', 'PEN'=>'S/.', 'PHP'=>'₱', 'PLN'=>'zł', 'PGK'=>'K',	 'QAR'=>'﷼',	 'RON'=>'lei', 'RUB'=>'руб',	 'SHP'=>'£', 'SAR'=>'﷼', 'RSD'=>'Дин.', 'SCR'=>'₨', 'SGD'=>'$', 'SBD'=>'$', 'SOS'=>'S', 'ZAR'=>'R', 'LKR'=>'₨', 'SEK'=>'kr', 'CHF'=>'CHF', 'SRD'=>'$', 'SYP'=>'£', 'SLL'=>'Le', 'STD'=>'Db', 'TWD'=>'NT', 'THB'=>'฿', 'TTD'=>'TTD', 'TRY'=>'₤', 'TVD'=>'$', 'TOP'=>'T$', 'TZS'=>'x',	 'UAH'=>'₴', 'GBP'=>'£', 'USD'=>'$', 'UYU'=>'$U', 'UZS'=>'лв', 'UGX'=>'USh', 'VEF'=>'Bs', 'VND'=>'₫', 'VUV'=>'Vt',	 'WST'=>'WS$',	 'XAF'=>'BEAC', 'XOF'=>'BCEAO', 'XPF'=>'F',	 'YER'=>'﷼', 'ZWD'=>'Z$', 'ZAR'=>'R');
	
/* Update Settings */
if(isset($_POST['setting_action']) && $_POST['setting_action']=='update_settings'){
	foreach($_POST as $option_name => $option_value){
		
		if($option_name=='octabook_allow_terms_and_conditions_url' || $option_name=='octabook_allow_privacy_policy_url'){
			$option_value = urlencode($option_value);
		}
		update_option($option_name,filter_var($option_value, FILTER_SANITIZE_STRING));
		
		if($option_name=='octabook_currency'){
			if(isset($octabook_currencysymbol_array[$option_value])){			
			$currencysymbol = $octabook_currencysymbol_array[$option_value];
			}else{
			$currencysymbol ='$';
			}
			update_option('octabook_currency_symbol',$currencysymbol);
		}
	}
}
/* Update Settings */
if(isset($_POST['setting_action']) && $_POST['setting_action']=='delete_company_image'){
		update_option('octabook_company_logo','');
		unlink($root.'/wp-content/uploads'.$_POST['mediapath']);
}
/* Update Email Templates */
if(isset($_POST['setting_action'],$_POST['template_id']) && $_POST['setting_action']=='update_emailtemplate' && $_POST['template_id']!=''){
		$emailtemplates->id = $_POST['template_id'];
		$emailtemplates->email_subject = $_POST['email_subject'];
		$emailtemplates->email_message = $_POST['email_message'];
		$emailtemplates->update_template_subject_message();

}
/* Update Email Template Status */
if(isset($_POST['setting_action'],$_POST['template_id']) && $_POST['setting_action']=='update_emailtemplate_status' && $_POST['template_id']!=''){
		$emailtemplates->id = $_POST['template_id'];
		$emailtemplates->template_status = $_POST['email_status'];
		$emailtemplates->update_template_status();

}
/* Update SMS Templates */
if(isset($_POST['setting_action'],$_POST['template_id']) && $_POST['setting_action']=='update_smstemplate' && $_POST['template_id']!=''){
		$smstemplates->id = $_POST['template_id'];
		$smstemplates->sms_message = $_POST['sms_message'];
		$smstemplates->update_template_subject_message();

}
/* Update SMS Template Status */
if(isset($_POST['setting_action'],$_POST['template_id']) && $_POST['setting_action']=='update_smstemplate_status' && $_POST['template_id']!=''){
		$smstemplates->id = $_POST['template_id'];
		$smstemplates->template_status = $_POST['sms_status'];
		$smstemplates->update_template_status();

}
/* Create Coupon */
if(isset($_POST['setting_action']) && $_POST['setting_action']=='create_coupon'){
		$coupons->location_id = $_SESSION['oct_location'];
		$coupons->coupon_code = filter_var($_POST['coupon_code'], FILTER_SANITIZE_STRING);
		$coupons->coupon_type = $_POST['coupon_type'];
		$coupons->coupon_value = filter_var($_POST['coupon_value'], FILTER_SANITIZE_STRING);
		$coupons->coupon_limit = filter_var($_POST['coupon_limit'], FILTER_SANITIZE_STRING);
		$coupons->coupon_expirydate = date_i18n('Y-m-d',strtotime($_POST['coupon_expiry']));
		$coupon_id = $coupons->create();
		$coupon_status = 'E';
}
if(isset($_POST['setting_action']) && $_POST['setting_action']=='update_coupon'){
		$coupons->id = $_POST['coupon_id'];
		$coupons->coupon_code = filter_var($_POST['coupon_code'], FILTER_SANITIZE_STRING);
		$coupons->coupon_type = $_POST['coupon_type'];
		$coupons->coupon_value = filter_var($_POST['coupon_value'], FILTER_SANITIZE_STRING);
		$coupons->coupon_limit = filter_var($_POST['coupon_limit'], FILTER_SANITIZE_STRING);
		$coupons->coupon_expirydate = date_i18n('Y-m-d',strtotime($_POST['coupon_expiry']));
		$coupons->update();
		$coupon_id = $_POST['coupon_id'];
		$coupon_status = 'E';
		if($_POST['coupon_status']=='D'){
			$coupon_status = 'D';
		}
		
}
if(isset($_POST['setting_action']) && ($_POST['setting_action']=='create_coupon' || $_POST['setting_action']=='update_coupon') ){	
		?>
		<tr id="coupon_detail<?php echo $coupon_id;?>">	
		<td><?php echo $_POST['coupon_code'];?></td>
		<td><?php echo date_i18n(get_option('date_format'),strtotime($_POST['coupon_expiry']));?></td>
		<td><?php echo $_POST['coupon_value'];?></td>
		<td><?php echo $_POST['coupon_limit'];?></td>
		<td>0</td>
		<td>
			
		<label class="toggle-large oct-toggle-medium" for="promocode_status<?php echo $coupon_id;?>">
			<input <?php if($coupon_status=='E'){ echo "checked='checked'";} ?> data-cid="<?php echo $coupon_id;?>" class="oct-toggle-medium-input oct_update_couponstatus" type="checkbox" id="promocode_status<?php echo $coupon_id;?>" data-toggle="toggle" data-size="small" data-on="<?php echo __("Enable","oct");?>" data-off="<?php echo __("Disable","oct");?>" data-onstyle="success" data-offstyle="danger"/>
			
		</label>
		
		
			
		</td>
		<td>
			<a data-cid="<?php echo $coupon_id;?>" href="javascript:void(0)" id="update_promocode<?php echo $coupon_id;?>" class="btn-circle btn-info btn-xs oct_update_promocode" title="Edit coupon code"><i class="fas fa-pencil-alt"></i></a>
			
			<a data-poid="oct-popover-coupon<?php echo $coupon_id; ?>" id="oct-delete-coupon<?php echo $coupon_id; ?>" class="pull-right btn-circle btn-danger btn-sm oct-delete-popover" rel="popover" data-placement='bottom' title="<?php echo __("Delete this coupon?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Delete coupon","oct");?>"></i></a>
			<div class="oct-popover" id="oct-popover-coupon<?php echo $coupon_id; ?>" style="display: none;">
				<div class="arrow"></div>
					<table class="form-horizontal" cellspacing="0">
						<tbody>
							<tr>
								<td>
									<a data-id="<?php echo $coupon_id; ?>" value="Delete" class="btn btn-danger btn-sm mr-10 oct_delete_coupon" type="submit"><?php echo __("Yes","oct");?></a>
									<a data-poid="oct-popover-coupon<?php echo $coupon_id; ?>" class="btn btn-default btn-sm oct-close-popover-delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
		</td>	
	</tr>
	<?php		
}
/* Update Coupon Status */
if(isset($_POST['setting_action'],$_POST['coupon_id']) && $_POST['setting_action']=='update_coupon_status' && $_POST['coupon_id']!=''){
		$coupons->id = $_POST['coupon_id'];
		$coupons->coupon_status = $_POST['coupon_status'];
		$coupons->Update_coupon_status_by_coupon_id();
}
/* Delete Coupon */
if(isset($_POST['setting_action'],$_POST['coupon_id']) && $_POST['setting_action']=='delete_coupon' && $_POST['coupon_id']!=''){
		$coupons->id = $_POST['coupon_id'];
		$coupons->delete();
}

/* Save GC Settings Start */

if(isset($_POST['GC_settings']) && $_POST['GC_settings'] == '1') {
	$gc_enable_disable = $_POST['gc_enable_disable'];
	$appointup_gc_twowaysync = $_POST['appointup_gc_twowaysync'];
	$appointup_gc_id = $_POST['appointup_gc_id'];
	$oct_gc_client_id = $_POST['oct_gc_client_id'];
	$oct_gc_client_secret = $_POST['oct_gc_client_secret'];
	$oct_gc_frontend_url = $_POST['oct_gc_frontend_url'];
	$oct_gc_admin_url = $_POST['oct_gc_admin_url'];
	
	update_option('oct_gc_status',$gc_enable_disable);
	update_option('oct_gc_two_way_sync_status',$appointup_gc_twowaysync);
	update_option('oct_gc_id',$appointup_gc_id);
	update_option('oct_gc_client_id',$oct_gc_client_id);
	update_option('oct_gc_client_secret',$oct_gc_client_secret);
	update_option('oct_gc_frontend_url',$oct_gc_frontend_url);
	update_option('oct_gc_admin_url',$oct_gc_admin_url);
}


/* Save GC Settings End */