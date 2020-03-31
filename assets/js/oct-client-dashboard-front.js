
jQuery(document).ready(function(){
	jQuery('#client-dashboard-table').DataTable( {
		dom: 'lfrtip'
    } );
});



jQuery(document).ready(function(){
	/** Delete service & location popover **/	
	jQuery('.oct-cancel-book-popover').popover({ 
			html : true,
			content: function() {
				var currpopid = jQuery(this).data('poid');
				jQuery('.popover').each(function(){
					var popid = jQuery(this).attr('id');
					if(popid!=currpopid){
						jQuery(this).fadeOut();
					}
					
				});
			  return jQuery('#'+currpopid).html();
			}
	});
	
	 /* Hide Cancel Appointment Popover */
	jQuery(document).on('click', '#oct-close-cancel-appointment-cd-popup', function(){		
		jQuery('.popover').fadeOut();
	});	 

});
/* on click current client booking cancel */
jQuery(document).on('click','.oct_client_save_cancel_reason',function(){
	var ajaxurl = objs_booking_list.plugin_path;
	var curr_client_bookingid = jQuery(this).attr('data-curr_client_bookingid');
	var cancel_reason=jQuery('#cancel_reason_txt'+curr_client_bookingid).val();

	if(cancel_reason==''){
	jQuery('.cancel_reason_txt').css("color","red");
	}else{
		jQuery('#oct .loader').show();	
	  var postdata =  { 
			booking_id:curr_client_bookingid,
			method:'CC',
			general_ajax_action:'c_r_cs_cc_appointment',
			action_content:cancel_reason
			}				
	
	jQuery.ajax({
					url  : ajaxurl+"/assets/lib/admin_general_ajax.php",
					type : 'POST',
					data : postdata,
					dataType : 'html',
					success  : function(response) {
						jQuery('#oct .loader').hide();	
						window.location.reload();						            
					}                
				});
			}
		});

jQuery(document).on('click','.oct_client_cancel',function(){
	var ajaxurl = objs_booking_list.plugin_path;
	var curr_client_bookingid = jQuery(this).attr('data-curr_client_bookingid');
	jQuery('#cancel_reason'+curr_client_bookingid).show('slow');
	jQuery('#cancel_reason'+curr_client_bookingid).css("text-align","center");	
});   

		
jQuery(document).on('click','.oct_client_login', function(e) {  

	var ajaxurl = objs_booking_list.plugin_path;

	var username = jQuery('input[name="oct_client_username"]').val();
	var password = jQuery('input[name="oct_client_password"]').val();
	jQuery('#client_login_username-error').hide();
	jQuery('#client_login_password-error').hide();
	jQuery('#client_login-error').hide();
	if(username=='' && password=='') {
		jQuery('#client_login_username-error').show();
		jQuery('#client_login_password-error').show();
		jQuery('#client_login_username-error').html('Please enter username');
		jQuery('#client_login_password-error').html('Please enter password');
	}else if(username=='') {
		jQuery('#client_login_username-error').show();
		jQuery('#client_login_username-error').html('Please enter username');
	}else if(password=='') {
		jQuery('#client_login_password-error').show();
		jQuery('#client_login_password-error').html('Please enter password');
	}else{
		
		jQuery('#oct .loader').show();		
		
		var postdata =  {username:username,password:password,general_ajax_action:'client_dashboard_login'}
		
		jQuery.ajax({
			url  : ajaxurl+"/assets/lib/admin_general_ajax.php",
			type : 'POST',
			dataType : 'html',
			data : postdata,
			success  : function(response) {
				if(jQuery.trim(response)=='1'){
					window.location.reload();						
				} else {
					jQuery('#client_login-error').show();
					jQuery('#client_login-error').html(response);
				}
			}
		});
	}
});		
jQuery(document).on('click','.oct_close_cancel_rsn', function(e) { 
	jQuery('.oct_cancel_reason').css("display","none");

	
});

		
		