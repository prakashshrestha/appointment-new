

/* Front page Form Steps show/hide */
jQuery(document).ready(function () { 
	
	jQuery('#btn-more-bookings').on( "click", function() {
		jQuery('#oct_first_step').addClass('show-data');
		jQuery('#oct_first_step').removeClass('hide-data');        
		jQuery('#oct_second_step').addClass('hide-data');
		jQuery('#oct_second_step').removeClass('show-data');
		jQuery('#oct_third_step').addClass('hide-data');
		jQuery('#oct_third_step').removeClass('show-data');
	});
	jQuery('.oct-cart-items-count').on( "click", function() {
		jQuery('#oct_first_step').addClass('hide-data');
		jQuery('#oct_first_step').removeClass('show-data'); 
		oct_activate_nav('oct_second_step');       
		jQuery('#oct_second_step').addClass('show-data');
		jQuery('#oct_second_step').removeClass('hide-data');		
	});

	oct_first_step_initiate();
	
});	
/* scroll to top when on second step */
jQuery(document).ready(function(){
	jQuery('#btn-second-step, #btn-more-bookings, .oct-cart-items-count, #btn-third-step').on('click',function(){
		jQuery('html, body').stop().animate({
			'scrollTop': jQuery('#oct-main').offset().top - 80
		}, 800, 'swing', function () {});
	});
});


function oct_first_step_initiate()
{
	jQuery('.oct_booking_form_field').hide();
	if(octmain_obj.multilication_status!='E'){
		jQuery('.oct_frt').hide();
		oct_activate_nav('oct_likepvdr');
		jQuery('#oct_likepvdr').show();
	}
	else{
	 jQuery('.oct_frt').show();
	 oct_activate_nav('oct_chosltn');
	 jQuery('#oct_chosltn').show();
   }
}


jQuery(document).ready(function () { 
	if (jQuery("#oct").width() >= 380 && jQuery("#oct").width() < 600){
		jQuery( ".oct-main-left").addClass( "active-left-xs12" );
		jQuery( ".oct-main-right").addClass( "active-right-xs12" );
	}	
	if (jQuery("#oct").width() >= 601 && jQuery("#oct").width() < 850){
		jQuery( ".oct-main-left").addClass( "active-left-res75" );
		jQuery( ".oct-main-right").addClass( "active-right-res57" );
	}	
	
	if (!jQuery('.oct_remove_left_sidebar_class').hasClass("no-sidebar-right")) {
		jQuery('.oct_remove_left_sidebar_class').addClass('oct-asr');
	}
	if (!jQuery('.oct_remove_right_sidebar_class').hasClass("no-cart-item-sidebar")) {
		jQuery('.oct_remove_right_sidebar_class').addClass('oct-cis');
	}
});   



/* Booking summary delete extra service NS */
jQuery(document).ready(function () { 
	jQuery(document).on("click",".oct-delete-icon",function() {
		if(jQuery('.oct-es').hasClass('delete-toggle')){
			jQuery(".oct-es").removeClass('delete-toggle'); 
		}
		jQuery(this).parent(".oct-es").addClass('delete-toggle');
	});
	jQuery(document).on("click",".oct-delete-confirm",function() {
		jQuery(this).parent(".oct-es").slideUp();
	});
	
	/* Booking summary delete booking full list */
	jQuery(document).on("click",".oct-delete-booking",function() {
		if(jQuery('.booking-list').hasClass('delete-list')){
			jQuery(".booking-list").removeClass('delete-list'); 
		}
		jQuery(this).parent(".booking-list").addClass('delete-list');
	});
	jQuery(document).on("click",".oct-delete-booking-box",function() {
		jQuery(this).parent(".booking-list").slideUp();
	});
	
	/* Remove delete booking button on ESC key */
	jQuery( document ).on( 'keydown', function ( e ) {
		if ( e.keyCode === 27 )  {
			jQuery(".booking-list").removeClass('delete-list'); 
			jQuery(".oct-es").removeClass('delete-toggle'); 
		}
	});

	/* var elem = jQuery( '.sidebar-box' );
	jQuery( document ).on( 'click', function ( e ) {
		if (jQuery( e.target ).closest( elem ).length === 0 ) {
			jQuery(".booking-list").removeClass('delete-list'); 
			jQuery(".oct-es").removeClass('delete-toggle'); 
		}
	});  */
	
});
jQuery(document).ready(function() {
	jQuery('.oct-slots-count').tooltipster({
		animation: 'grow',
		delay: 10,
		side: 'top',
		theme: 'tooltipster-shadow',
		trigger: 'hover'
	});
});



/* custom dropdown show hide list */

jQuery(document).ready(function () { 
	
	
	
	/* Location */
	jQuery(document).on("click",".select-location",function() {
		jQuery(".service-selection").removeClass('clicked');
		jQuery(".service-dropdown").removeClass('bounceInUp');	
		jQuery(".staff-selection").removeClass('clicked');
		jQuery(".staff-dropdown").removeClass('bounceInUp');
		
		jQuery(".cus-location").addClass('focus');
		jQuery(".location-selection").toggleClass('clicked');
		jQuery(".location-dropdown").toggleClass('bounceInUp');	
		
	});
	jQuery(document).on("click",".select_location",function() {
		jQuery('#selected_location').html(jQuery(this).html());
		jQuery(".location-selection").removeClass('clicked');
		jQuery(".location-dropdown").removeClass('bounceInUp');		
	});
	/* select staff */
	jQuery(document).on("click",".select-staff",function() {
		jQuery(".service-selection").removeClass('clicked');
		jQuery(".service-dropdown").removeClass('bounceInUp');
		jQuery(".location-selection").removeClass('clicked');
		jQuery(".location-dropdown").removeClass('bounceInUp');	
		
		jQuery(".cus-select-staff").addClass('focus');
		jQuery(".staff-selection").toggleClass('clicked');
		jQuery(".staff-dropdown").toggleClass('bounceInUp');
	});
	jQuery(document).on("click",".select_staff",function() {
		jQuery(".staff-selection").removeClass('clicked');
		jQuery(".staff-dropdown").removeClass('bounceInUp');		
	});
	/* Service */
	jQuery(document).on("click",".select-custom",function() {
		jQuery(".staff-selection").removeClass('clicked');
		jQuery(".staff-dropdown").removeClass('bounceInUp');
		jQuery(".location-selection").removeClass('clicked');
		jQuery(".location-dropdown").removeClass('bounceInUp');
		
		jQuery(".cus-select").addClass('focus');	
		jQuery(".service-selection").toggleClass('clicked');
		jQuery(".service-dropdown").toggleClass('bounceInUp');
	});
	jQuery(document).on("click",".select_custom",function() {
		jQuery(".service-selection").removeClass('clicked');
		jQuery(".service-dropdown").removeClass('bounceInUp');
		jQuery(".select_custom").removeClass("selected_services");
		jQuery(this).addClass("selected_services");
	});
	jQuery(document).on('click','.oct-addon-ser',function(){
		var addonid = jQuery(this).data('addonid');
		jQuery('.oct-addon-count'+addonid).toggle();
		var value = jQuery(this).prop('checked');
	});
	/* Addon service counting */
	jQuery(function () {
		jQuery('#add').on('click',function(){
			var $qty=jQuery(this).closest('.oct-btn-group').find('.addon_qty');
			var currentVal = parseInt($qty.val());
			if (!isNaN(currentVal)) {
				$qty.val(currentVal + 1);
			}
		});
		jQuery('#minus').on('click',function(){
			var $qty=jQuery(this).closest('.oct-btn-group').find('.addon_qty');
			var currentVal = parseInt($qty.val());
			if (!isNaN(currentVal) && currentVal > 0) {
				$qty.val(currentVal - 1);
			}
		});
	});
});
/* Calendar click date to show slots */
jQuery(document).ready(function () { 
	/* user new and existing radio show hide fields */
	/* jQuery(document).on('click', '#oct-existing-user', function(){			
		jQuery('.existing-user-login').show( "blind", {direction: "vertical"}, 1000 );
		jQuery('.oct-new-user-area').hide( "blind", {direction: "vertical"}, 500 );
		
	});
	jQuery(document).on('click', '#oct-new-user', function(){			
		jQuery('.new-user-area').show( "blind", {direction: "vertical"}, 1000 );
		jQuery('.existing-user-login').hide( "blind", {direction: "vertical"}, 500 );
		
	});  */
	jQuery(document).on('click', '#oct-existing-user', function(){
		jQuery('.existing-user-login').show( "blind", {direction: "vertical"}, 700 );
		jQuery('.new-user-area').hide( "blind", {direction: "vertical"}, 300 );
		jQuery('.new-user-personal-detail-area').hide( "blind", {direction: "vertical"}, 300 );
	});
	jQuery(document).on('click', '#oct-new-user', function(){
		jQuery('.new-user-area').show( "blind", {direction: "vertical"}, 700 );
		jQuery('.existing-user-login').hide( "blind", {direction: "vertical"}, 300 );
		jQuery('.hide_new_user_login_details').show( "blind", {direction: "vertical"}, 300 );
		jQuery('.new-user-personal-detail-area').show( "blind", {direction: "vertical"}, 700 );
	}); 
	jQuery(document).on('click', '#oct-guest-user', function(){
		jQuery('.existing-user-login').hide( "blind", {direction: "vertical"}, 300 );
		jQuery('.hide_new_user_login_details').hide();
		jQuery('.new-user-personal-detail-area').show( "blind", {direction: "vertical"}, 700 );
	}); 
	jQuery(document).on('ready ajaxComplete', function(){
		jQuery("#oct-front-phone").intlTelInput({
		 /*   allowDropdown: false,
		   autoHideDialCode: false,
		   autoPlaceholder: false,
		   dropdownContainer: "body",
		   excludeCountries: ["us"],
		   geoIpLookup: function(callback) {
		     $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
		       var countryCode = (resp && resp.country) ? resp.country : "";
		       callback(countryCode);
		     });
		   },
		   initialCountry: "auto",
		   nationalMode: false,
		   numberType: "MOBILE",
		   onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
		   preferredCountries: ['cn', 'jp'], */
		   separateDialCode: true,
		  utilsScript: "utils.js"
		});
	});
	/* payment methods */
	jQuery(document).on('click','.payment_checkbox',function() {
		if(jQuery('#stripe-payments').is(':checked')) { jQuery('#stripe-payment-main').fadeIn("slow"); } else {
			 jQuery('#stripe-payment-main').fadeOut("slow");
		}		

	});
});

/* see more instructions in service popup */
jQuery(document).ready(function() {
    jQuery(".show-more-toggler").click(function() {
		jQuery(".bullet-more").toggle( "blind", {direction: "vertical"}, 500);
        jQuery(".show-more-toggler").toggleClass('rotate');
    });
});


/*********************************************************************************************/
/********************************** OCT Front JS Function ********************************** / 
/*********************************************************************************************/

/* Get Location by Zip Code/Postal Code If Multisite is Enabled */
jQuery(document).on('keyup','#oct_zip_code',function(event){
	var ajaxurl = octmain_obj.plugin_path;
	var location_err_msg = octmain_obj.location_err_msg;
	var location_search_msg = octmain_obj.location_search_msg;
	var Choose_service_msg = octmain_obj.Choose_service;
	
	var zipcode = jQuery('#oct_zip_code').val();	
	jQuery('#oct_selected_service').val(0);
	jQuery('#oct_selected_staff').val(0);
	jQuery('#oct_selected_location').val('X');
	jQuery('#oct_service_addons').html('');
	jQuery('#oct_service_addon_st').val('D');
	jQuery('#oct_selected_datetime').val('');
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').addClass('oct-hide');
	
	if(zipcode!=''){
		jQuery('.oct-loader-first-step').hide();	
		jQuery('#oct_location_success').hide();			
		jQuery('#close_service_details').trigger('click');	
		jQuery('#selected_custom .oct-value').html(Choose_service_msg);
		
		jQuery('#oct_location_error').html(location_search_msg);
		var postdata = {zipcode:zipcode,action:'oct_get_location'};		
		jQuery.ajax({
				type:"POST",
				async:false,
				url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
				dataType : 'html',			
				data:postdata,
				success:function(response){	
					 jQuery('.oct-loader-first-step').hide();
					if(jQuery.trim(response)=='notfound'){
						jQuery('#oct_location_success').hide();	
						jQuery('#oct_location_error').show();
						jQuery('#oct_location_error').html(location_err_msg);
					}else{	
						jQuery('#oct_selected_location').val(0);
						jQuery('#oct_location_success').show();						
						jQuery('#oct_location_error').hide();
						/* Get Services By Found Location */
						var location_id = 0;
						var servicedata = {location_id:location_id,action:'oct_get_location_services'};	
						jQuery.ajax({
							type:"POST",
							url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
							dataType : 'html',			
							data:servicedata,
							success:function(response){					
								jQuery('#oct_services').html(response);	
								jQuery('.oct_booking_form_field').hide();
								oct_activate_nav('oct_srvdr');
								jQuery('#oct_srvdr').show();

							}
						});
					}				
				}
		});
	}
});

function oct_activate_nav(actice_id)
{
	jQuery('.oct_service_li_tilte').remove();
	jQuery('.oct_stand').removeClass('active');
	if(actice_id =='oct_chosltn'){
        jQuery('.oct_frt').addClass('active');
    }
    if(actice_id =='oct_likepvdr'){
    	jQuery('.oct_frt').addClass('active');
    	var oct_frt = jQuery('.oct_frt').html();
    	jQuery('.oct_frt').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frt);
        jQuery('.oct_snd').addClass('active');
    }
    if(actice_id =='oct_srvdr'){
    	jQuery('.oct_frt').addClass('active');
    	var oct_frt = jQuery('.oct_frt').html();
    	jQuery('.oct_frt').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frt);
    	jQuery('.oct_snd').addClass('active');
    	var oct_snd = jQuery('.oct_snd').html();
    	jQuery('.oct_snd').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_snd);
        jQuery('.oct_frth').addClass('active');
    }
    if(actice_id =='oct_clndr'){
    	jQuery('.oct_frt').addClass('active');
    	var oct_frt = jQuery('.oct_frt').html();
    	jQuery('.oct_frt').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frt);
    	jQuery('.oct_snd').addClass('active');
    	var oct_snd = jQuery('.oct_snd').html();
    	jQuery('.oct_snd').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_snd);
    	jQuery('.oct_frth').addClass('active');
    	var oct_frth = jQuery('.oct_frth').html();
    	jQuery('.oct_frth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frth);
        jQuery('.oct_fth').addClass('active');
    }
    if(actice_id =='oct_second_step'){
    	jQuery('.oct_frt').addClass('active');
    	var oct_frt = jQuery('.oct_frt').html();
    	jQuery('.oct_frt').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frt);
    	jQuery('.oct_snd').addClass('active');
    	var oct_snd = jQuery('.oct_snd').html();
    	jQuery('.oct_snd').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_snd);
    	jQuery('.oct_frth').addClass('active');
    	var oct_frth = jQuery('.oct_frth').html();
    	jQuery('.oct_frth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frth);
    	jQuery('.oct_fth').addClass('active');
    	var oct_fth = jQuery('.oct_fth').html();
    	jQuery('.oct_fth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_fth);
        jQuery('.oct_sth').addClass('active');
    }
    if(actice_id =='oct_third_step'){
    	jQuery('.oct_frt').addClass('active');
    	var oct_frt = jQuery('.oct_frt').html();
    	jQuery('.oct_frt').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frt);
    	jQuery('.oct_snd').addClass('active');
    	var oct_snd = jQuery('.oct_snd').html();
    	jQuery('.oct_snd').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_snd);
    	jQuery('.oct_frth').addClass('active');
    	var oct_frth = jQuery('.oct_frth').html();
    	jQuery('.oct_frth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_frth);
    	jQuery('.oct_fth').addClass('active');
    	var oct_fth = jQuery('.oct_fth').html();
    	jQuery('.oct_fth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_fth);
    	jQuery('.oct_sth').addClass('active');
    	var oct_sth = jQuery('.oct_sth').html();
    	jQuery('.oct_sth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_sth);
        jQuery('.oct_svth').addClass('active');
        var oct_svth = jQuery('.oct_svth').html();
    	jQuery('.oct_svth').html('<img class="oct_service_li_tilte" src="'+octmain_obj.plugin_url+'/octabook/assets/images/ok.png">'+oct_svth);
    }
}

jQuery(document).on('click','.select_location',function(event){
	var ajaxurl = octmain_obj.plugin_path;
	var location_err_msg = octmain_obj.location_err_msg;
	var location_search_msg = octmain_obj.location_search_msg;
	var Choose_service_msg = octmain_obj.Choose_service;
	jQuery('#oct_location_error').hide();	
	jQuery('#close_service_details').trigger('click');	
	jQuery('#selected_custom .oct-value').html(Choose_service_msg);	
	
	jQuery('#oct_selected_service').val(0);
	jQuery('#oct_selected_staff').val(0);
	jQuery('#oct_selected_location').val('X');
	jQuery('#oct_service_addons').html('');
	jQuery('#oct_service_addon_st').val('D');
	jQuery('#oct_selected_datetime').val('');
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').addClass('oct-hide');
	
	jQuery('.oct-loader-first-step').show();
	
	/* Get Services By Found Location */
	var location_id = jQuery(this).attr('value');
	jQuery('#oct_selected_location').val(location_id);	
	var servicedata = {location_id:location_id,action:'oct_get_location_services'};	
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:servicedata,
			success:function(response){		
				oct_activate_nav('oct_likepvdr');			
				jQuery('#oct_services').html(response);	
				jQuery('.oct-loader-first-step').hide();	
				jQuery('.oct_booking_form_field').hide();
				jQuery('#oct_likepvdr').toggle("slide", {direction:'right'});
			}
		});	
});


/* Hide Service Desciption On Click of Close */
jQuery(document).on("click","#close_service_details",function() {
		jQuery(".service-details").removeClass('oct-show');
		jQuery(".service-details").addClass('oct-hide');
		
});

/* Get Service Detail On Select Of Service */
jQuery(document).on('click','#oct_services .select_custom',function(event){
	var ajaxurl = octmain_obj.plugin_path;
	var sid = jQuery(this).data('sid');			
	var multiloction_status = octmain_obj.multilocation_status;
	var zipwise_status = octmain_obj.zipwise_status;
	var selected_location = jQuery('#oct_selected_location').val();
	jQuery('#oct_service_addon_st').val('D');				
	jQuery('#oct_service_addons').html('');	
	jQuery('#oct_selected_datetime').val('');
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').addClass('oct-hide');
	
	jQuery('#oct_service_error').hide();
	if(multiloction_status=='E' && selected_location=='X'){		
		jQuery('#oct_location_error').show();
		jQuery(".common-selection-main").removeClass('clicked');
		jQuery('html, body').stop().animate({
			'scrollTop': jQuery('#oct_location_error').offset().top - 80
		}, 800, 'swing', function () {});
		return false;
	}
	if(zipwise_status=='E' && selected_location=='X'){
		var Choose_zipcode_msg = octmain_obj.Choose_zipcode;		
		jQuery('#oct_location_success').hide();
		jQuery('#oct_location_error').show();
		jQuery('#oct_location_error').html(Choose_zipcode_msg);
		jQuery(".common-selection-main").removeClass('clicked');
		
		jQuery('html, body').stop().animate({
			'scrollTop': jQuery('#oct_location_error').offset().top - 80
		}, 800, 'swing', function () {});
		return false;
	}	
	jQuery('.oct-loader-first-step').show();
	jQuery('#selected_custom').html(jQuery(this).html());	
		
	var servicedata = {sid:sid,action:'oct_get_service_detail'};
	jQuery('#oct_selected_service').val(sid);
	/* Get Services By Found Location */	
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:servicedata,
			success:function(response){	
				var service_details = jQuery.parseJSON(response);
				if(service_details.description!=''){
					jQuery('#oct_service_detail').html(service_details.description);				
					jQuery(".common-selection-main").removeClass('clicked');
					jQuery(".custom-dropdown").slideUp();
					// jQuery(".service-details").removeClass('oct-hide');
					// jQuery(".service-details").addClass('oct-show');
					if (jQuery("#oct").width() >= 600 && jQuery("#oct").width() < 800){
						jQuery( ".service-duration, .service-price" ).addClass( "active-xs-12" );
					}
				}
				if(service_details.addonsinfo!=''){
					jQuery('#oct_service_addon_st').val('E');	
					jQuery('#oct_service_addons').hide();
					jQuery('#oct_service_addons').html(service_details.addonsinfo);				
					jQuery(".common-selection-main").removeClass('clicked');
					jQuery(".custom-dropdown").slideUp();
					jQuery("#oct_service_addons").removeClass('oct-hide');
				}


				/* Get Provider By Service Provider */	
				var servicestaffdata = {sid:sid,action:'oct_get_service_providers'};
					jQuery.ajax({
						type:"POST",
						url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
						dataType : 'html',			
						data:servicestaffdata,
						success:function(response){	
							jQuery('.oct_booking_form_field').hide();
							if(service_details.addonsinfo!=''){
								jQuery('#oct_srvdr').hide();
								jQuery('#oct_service_addons').toggle("slide",{direction:'right'});
							}
							else
							{
								oct_activate_nav('oct_srvdr');
								jQuery('#oct_srvdr').toggle("slide", {direction:'right'});
							}
							jQuery('.oct-loader-first-step').hide();
							jQuery('#oct_staff_info').html(response);
							if (jQuery("#oct").width() >= 600 && jQuery("#oct").width() < 800){
								jQuery( ".oct-staff-box" ).addClass( "active-sm-6" );
							}
						}
					});
			}
		});	
	
});

jQuery(document).on('click','#oct-continue-addon-service', function(){
	oct_activate_nav('oct_srvdr');
	jQuery('#oct_service_addons').hide();
	jQuery('#oct_srvdr').toggle("slide", {direction:'right'});
});


/* Select Staff */
jQuery(document).on('click','.oct-staff-box,#cus-select-staff .select_staff',function(event){
	
	jQuery('#oct_service_error').hide();
	jQuery('#oct_staff_error').hide();
	jQuery('#oct_staff_error').addClass('oct-hide');
	
	jQuery('#oct_selected_datetime').val('');
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').addClass('oct-hide');
	
	
	jQuery(".service-selection").removeClass('clicked');
	jQuery(".service-dropdown").removeClass('bounceInUp');
	jQuery(".location-selection").removeClass('clicked');
	jQuery(".location-dropdown").removeClass('bounceInUp');	
	
	
	var selserviceid = jQuery('#oct_selected_service').val();	
	if(selserviceid==0){
		var Choose_service_msg = octmain_obj.Choose_service;
		jQuery('#oct_service_error').html(Choose_service_msg);
		jQuery('#oct_service_error').show();
		jQuery('#oct_service_error').removeClass('oct-hide');
		jQuery('html, body').stop().animate({
			'scrollTop': jQuery('#oct_service_error').offset().top - 80
		}, 800, 'swing', function () {});
		return false;
	}
	
	var staffid = jQuery(this).data('staffid');
	jQuery('#oct_selected_staff').val(staffid);
	jQuery('#selected_custom_staff').html(jQuery(this).html());
	
});

function goToStep(el_id)
{
	jQuery('.oct_booking_form_field').hide();
	oct_activate_nav(el_id);
	jQuery('#'+el_id).toggle("slide", {direction:'left'});
}

/* Addon Quantity Increment/Decrement */
jQuery(document).on('click','.oct_addonqty', function() {
	var ajaxurl = octmain_obj.plugin_path;
	var addon_id = jQuery(this).data('addonid');
	var addon_qty_action = jQuery(this).data('qtyaction');
	var addon_maxqty = jQuery(this).data('addonmax');	
	var currentqtyvalue = jQuery('#addonqty_'+addon_id).val();
	if(addon_qty_action=='minus'){
		if(parseInt(currentqtyvalue)>1){
			jQuery('#addonqty_'+addon_id).val(parseInt(currentqtyvalue)-1);
		}
	}else{
		if(parseInt(currentqtyvalue)<parseInt(addon_maxqty)){
			jQuery('#addonqty_'+addon_id).val(parseInt(currentqtyvalue)+1);
		}
	}
});
/* Show Provider Time Slot*/
jQuery(document).on('click','.oct-week,.by_default_today_selected', function() {
	if(jQuery(this).hasClass('inactive')){
		return false;
	}
	
	var ajaxurl = octmain_obj.plugin_path;
	
	var selstaffid = jQuery('#oct_selected_staff').val();	
	if(selstaffid==0){
		jQuery('#oct_staff_error').show();
		jQuery('#oct_staff_error').removeClass('oct-hide');
		jQuery('html, body').stop().animate({
			'scrollTop': jQuery('#oct_staff_error').offset().top - 80
		}, 800, 'swing', function () {});
		return false;
	}else{
		jQuery('.oct-loader-first-step').show();
		var calrowid = jQuery(this).data('calrowid');
		var seldate = jQuery(this).data('seldate');
		var service_id = jQuery(".selected_services").data('sid');
		var calenderdata = {selstaffid:selstaffid,service_id:service_id,seldate:seldate,action:'oct_get_provider_slots'};
		
		jQuery('.oct-week').each(function(){
			jQuery(this).removeClass('active');				
			
		});
		jQuery('.oct-show-time').each(function(){	
			jQuery(this).removeClass('shown');			
			jQuery(this).removeAttr('style');			
			
		});
		jQuery(this).addClass('active');		
		
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:calenderdata,
			success:function(response){	

				jQuery('.oct-loader-first-step').hide();


				jQuery('.curr_selected_row'+calrowid).addClass('shown');
				jQuery('.curr_selected_row'+calrowid).css('display','block');
				jQuery('.curr_selected_row'+calrowid+' .oct_day_slots').html(response);
				jQuery('#oct_tmtslt_ul').html(response);
				jQuery('.oct_booking_form_field').hide();
				jQuery('#oct_tmtslt').toggle("slide", {direction:'right'});
				
			}
		});	
	}	
});

/* Select Time Slot*/
jQuery(document).on('click','.oct_select_slot', function() {

	var ajaxurl = octmain_obj.plugin_path;
	var slotdate = jQuery(this).data('slot_db_date');
	var slottime = jQuery(this).data('slot_db_time');
	var displaydate = jQuery(this).data('displaydate');
	var displaytime = jQuery(this).data('displaytime');
     
	 // jQuery('#oct_nxt').show();
	 
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').removeClass('oct-hide');
	jQuery('.time-slot').each(function(){
			jQuery(this).removeClass('oct-slot-selected');				
			
	});
	jQuery(this).addClass('oct-slot-selected');
	jQuery('.oct-selected-date-view').removeClass('oct-hide');
	jQuery('#oct_selected_datetime').val(slotdate+' '+slottime);
	jQuery('.oct-date-selected').html(displaydate);
	jQuery('.oct-time-selected').html(displaytime);
	jQuery('.time_slotss').show();
	jQuery('.confirm-slot-final').remove();
	jQuery(this).hide();
	jQuery(this).after('<li style="display:none;" class="time-slot br-5 time_slotss confirm-slot-final"><span class="confirm-slot-final-txt">Confirm</span>'+jQuery(this).html()+'</li>');
	jQuery('.confirm-slot-final').toggle("slide", {direction:'right'});
	// jQuery('.oct-show-time').hide();

});

jQuery(document).on('click','.confirm-slot-final', function(){
	jQuery('#btn-second-step').click();
});

jQuery(document).on('click','.staff-radio', function(){
	jQuery('#oct_selected_datetime').val('');
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').addClass('oct-hide');
	
	var selected_location = jQuery('#oct_selected_location').val();
	var selected_service = jQuery('#oct_selected_service').val();
	var selected_staff = jQuery('#oct_selected_staff').val();
	
	var ajaxurl = octmain_obj.plugin_path;
	
	var d = new Date(),

    n = d.getMonth()+1;

    y = d.getFullYear();
		if(n == 13){
			n = 1;
			y++;
		}
		
	var calmonth = n;
	var calyear =  y;
	var calenderdata = {selected_location:selected_location,selected_service:selected_service,selected_staff:selected_staff,calmonth:calmonth,calyear:calyear,action:'oct_cal_next_prev'};
	
	jQuery('.oct-loader-first-step').show();
	
	jQuery.ajax({
		type:"POST",
		url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
		dataType : 'html',			
		data:calenderdata,
		success:function(response){	
			oct_activate_nav('oct_clndr');
			jQuery('#oct_service_addons').hide();
		    jQuery('#oct_service_addons').removeClass('oct-show');
		    jQuery('.oct_booking_form_field').hide();
		    jQuery('#oct_clndr').toggle("slide", {direction:'right'});
			jQuery('.calendar-wrapper').html(response);
			jQuery('.oct-loader-first-step').hide();
		}
	});

});
/* Goto Today */
jQuery(document).on('click','.today_btttn', function(){
	
	var calmonth = jQuery('.previous-date').data('curmonth');
	var calyear = jQuery('.previous-date').data('curyear');
	
	var selmonth = jQuery(this).data('smonth');
	var selyear = jQuery(this).data('syear');
	
	if(selmonth==calmonth && calyear==selyear){
		jQuery('.by_default_today_selected').trigger('click');	
	}else{
		jQuery('.oct-loader-first-step').show();
		var ajaxurl = octmain_obj.plugin_path;
		var calenderdata = {calmonth:calmonth,calyear:calyear,action:'oct_cal_next_prev'};
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:calenderdata,
			success:function(response){	
				jQuery('.oct-loader-first-step').hide();
				jQuery('.calendar-wrapper').html(response);
				oct_activate_nav('oct_clndr');
				jQuery('.by_default_today_selected').trigger('click');
			}
		});		
	}	
});

/* Get Calender Next Previous Month */
jQuery(document).on('click','.oct_month_change', function() {
	
	jQuery('#oct_selected_datetime').val('');
	jQuery('#oct_datetime_error').hide();
	jQuery('.oct-selected-date-view').addClass('oct-hide');
	var selected_location = jQuery('#oct_selected_location').val();
	var selected_service = jQuery('#oct_selected_service').val();
	var selected_staff = jQuery('#oct_selected_staff').val();
	
	var ajaxurl = octmain_obj.plugin_path;
	var calmonth = jQuery(this).data('calmonth');
	var calyear = jQuery(this).data('calyear');
	var calenderdata = {selected_location:selected_location,selected_service:selected_service,selected_staff:selected_staff,calmonth:calmonth,calyear:calyear,action:'oct_cal_next_prev'};
	jQuery('.oct-loader-first-step').show();
	
	jQuery.ajax({
		type:"POST",
		url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
		dataType : 'html',			
		data:calenderdata,
		success:function(response){				
			jQuery('.calendar-wrapper').html(response);
			jQuery('.oct-loader-first-step').hide();

		}
	});
});

/* Add Booking Into cart */
jQuery(document).on('click','#btn-second-step', function() {
	var selected_datetime = jQuery('#oct_selected_datetime').val();
	if(selected_datetime==0){
		jQuery('#oct_datetime_error').show();
		jQuery('html, body').stop().animate({
			'scrollTop': jQuery('#oct_datetime_error').offset().top - 80
		}, 800, 'swing', function () {});
		return false;
	}else{
		var ajaxurl = octmain_obj.plugin_path;
		var selected_location = jQuery('#oct_selected_location').val();
		var selected_service = jQuery('#oct_selected_service').val();
		var selected_staff = jQuery('#oct_selected_staff').val();
		var service_addon_st = jQuery('#oct_service_addon_st').val();
		var selected_datetime = jQuery('#oct_selected_datetime').val();
		var serviceaddons = [];
		if(service_addon_st=='E'){
			jQuery('.addon-service-list li .addon-checkbox').each(function(){
				if(jQuery(this).is(':checked')){
					var maxqtyst = jQuery(this).data('saddonmaxqty');
					var saddonid = jQuery(this).data('saddonid');
					var maxqty = '0';
					if(maxqtyst=='Y'){
						var maxqty = jQuery('#addonqty_'+saddonid).val();	
					}
					serviceaddons.push({'addonid':saddonid,'maxqty':maxqty}); 
				}
			});
		}
		jQuery('.oct-loader-first-step').show();
		var cartitemdata = {selected_location:selected_location,selected_service:selected_service,selected_staff:selected_staff,service_addon_st:service_addon_st,selected_datetime:selected_datetime,serviceaddons:serviceaddons,action:'add_item_into_cart'};
	
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:cartitemdata,
			success:function(response){	
				jQuery('#oct_booking_sidebar').html(response);
				jQuery('.oct-loader-first-step').hide();
				jQuery('#oct_selected_location').val('X');
				jQuery('#oct_selected_service').val('0');
				jQuery('#oct_selected_staff').val('0');
				jQuery('#oct_service_addon_st').val('d');
				jQuery('#oct_selected_datetime').val('0');
				jQuery('#oct_first_step').removeClass('show-data');
				jQuery('#oct_first_step').addClass('hide-data');  
				oct_activate_nav('oct_second_step');      
				jQuery('#oct_second_step').removeClass('hide-data');
				jQuery('#oct_second_step').addClass('show-data');
				jQuery('.oct_remove_left_sidebar_class').removeClass('no-sidebar-right');
				jQuery('.oct_remove_left_sidebar_class').addClass('oct-asr');
				jQuery('.oct_remove_right_sidebar_class').removeClass('no-cart-item-sidebar');
				jQuery('.oct_remove_right_sidebar_class').addClass('cart-item-sidebar');
				jQuery('.oct_remove_right_sidebar_class').addClass('oct-cis');
			}
		});		
	}
});

/* Remove Item From Cart */
jQuery(document).on('click','.oct_remove_item', function() {
	var ajaxurl = octmain_obj.plugin_path;
	var cartitemid = jQuery(this).data('cartitemid');
	
	var deletecartitemdata = {cartitemid:cartitemid,action:'oct_delete_cart_item'};
	jQuery('.oct-loader-first-step').show();
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:deletecartitemdata,
			success:function(response){	
				jQuery.ajax({
					type:"POST",
					url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
					dataType : 'html',			
					data:{ action:'refresh_sidebar'},
					success:function(response){
						jQuery('#oct_booking_sidebar').html(response);
						jQuery('.oct-loader-first-step').hide();
						if(jQuery('#oct_booking_summary').hasClass('oct_cart_item_not_exist')){
							jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
							jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
							jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
							jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
							
							jQuery('#oct_second_step').removeClass('show-data');
							jQuery('#oct_second_step').addClass('hide-data');        
							jQuery('#oct_first_step').removeClass('hide-data');
							jQuery('#oct_first_step').addClass('show-data');
							// jQuery('.select_location').trigger('click');
							// jQuery('#oct_zip_code').trigger('keyup');
							oct_first_step_initiate();
							jQuery('.oct-show-time').each(function(){ jQuery(this).hide(); });
							jQuery('html, body').stop().animate({
								'scrollTop': jQuery('#oct-main').offset().top - 80
							}, 800, 'swing', function () {});
						}
					}
				});	
			}
		});
	
	
});

/* Remove Service Addon From Cart Item */
jQuery(document).on('click','.oct_remove_addon', function() {
	var ajaxurl = octmain_obj.plugin_path;
	var cartitemid = jQuery(this).data('cartitemid');
	var addonid = jQuery(this).data('addonid');
	
	var deletecartaddondata = {addonid:addonid,cartitemid:cartitemid,action:'oct_delete_addon'};
	jQuery('.oct-loader-first-step').show();
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:deletecartaddondata,
			success:function(response){	
				 jQuery.ajax({
					type:"POST",
					url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
					dataType : 'html',			
					data:{ action:'refresh_sidebar'},
					success:function(response){	
						jQuery('#oct_booking_sidebar').html(response);
						jQuery('.oct-loader-first-step').hide();
						if(jQuery('#oct_booking_summary').hasClass('oct_cart_item_not_exist')){
							jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
							jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
							jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
							jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
							jQuery('.oct_remove_right_sidebar_class').removeClass('oct-cis');

							jQuery('#oct_second_step').removeClass('show-data');
							jQuery('#oct_second_step').addClass('hide-data');        
							jQuery('#oct_first_step').removeClass('hide-data');
							jQuery('#oct_first_step').addClass('show-data');
							jQuery('.select_location').trigger('click');
							jQuery('#oct_zip_code').trigger('keyup');
							jQuery('.oct-show-time').each(function(){ jQuery(this).hide(); });
							jQuery('html, body').stop().animate({
								'scrollTop': jQuery('#oct-main').offset().top - 80
							}, 800, 'swing', function () {});
						}

					}
				});	 
			}
		});
	
	
});
/* Apply/Reverse Coupon */
jQuery(document).on('click','#remove_applied_coupon,#oct_apply_coupon', function() {
		var ajaxurl = octmain_obj.plugin_path;	
		var couponaction = jQuery(this).data('action');	
		var selected_location = jQuery('#oct_selected_location').val();
		jQuery('.oct_promocode_error').hide();	
		if(selected_location=='X'){
			var selected_location = 0;
		}
		
		
		if(couponaction=='apply'){
			var coupon_code = jQuery('#oct-coupon').val();
			if(coupon_code==''){
				jQuery('.oct_promocode_error').show();	
			}
			var coupondata = {selected_location:selected_location,coupon_code:coupon_code,couponaction:'apply',action:'oct_coupon_ar'};
		}else{
			var coupondata = {selected_location:selected_location,couponaction:'reverse',action:'oct_coupon_ar'};
		}
		jQuery('.oct-loader-first-step').show();
	
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',			
			data:coupondata,
			success:function(response){
				jQuery('.oct-loader-first-step').hide();	
				/* If coupon applied */
				if(couponaction=='apply'){
					if(response=='ok'){
						jQuery.ajax({
							type:"POST",
							url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
							dataType : 'html',			
							data:{ action:'refresh_sidebar'},
							success:function(response){	
								jQuery('#oct_booking_sidebar').html(response);
								if(jQuery('#oct_booking_summary').hasClass('oct_cart_item_not_exist')){
									jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
									jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
									jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
									jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
									jQuery('.oct_remove_right_sidebar_class').removeClass('oct-cis');
								}

							}
						});	
					}else{
						jQuery('.oct_promocode_error').show();	
					}					
				/* If coupon reversed */
				}else{
					jQuery.ajax({
							type:"POST",
							url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
							dataType : 'html',			
							data:{ action:'refresh_sidebar'},
							success:function(response){	
								jQuery('#oct_booking_sidebar').html(response);
								if(jQuery('#oct_booking_summary').hasClass('oct_cart_item_not_exist')){
									jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
									jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
									jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
									jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
									jQuery('.oct_remove_right_sidebar_class').removeClass('oct-cis');
								}
							}
						});
				}								
			}
		});	
});
jQuery(document).on('click','#btn-more-bookings', function() {
	jQuery('#oct_second_step').removeClass('show-data');
	jQuery('#oct_second_step').addClass('hide-data');        
	jQuery('#oct_first_step').removeClass('hide-data');
	jQuery('#oct_first_step').addClass('show-data');
	/* jQuery('.select_location').trigger('click');
	jQuery('#oct_zip_code').trigger('keyup'); */
	jQuery('.oct-show-time').each(function(){ jQuery(this).hide(); });
		
	jQuery('.oct-selected-date-view').removeClass('oct-show');	
	jQuery('.oct-selected-date-view').addClass('oct-hide');	
	
	var Choose_location = octmain_obj.Choose_location;
	jQuery('#selected_location .oct-value').html(Choose_location);
	jQuery('#oct_service_addons').html('');
	jQuery('#close_service_details').trigger('click');
	
	var Choose_provider = octmain_obj.Choose_provider;
	jQuery('#selected_custom_staff .oct-value').html(Choose_provider);
	jQuery('.staff-radio').each(function(){
		jQuery(this).attr('checked',false);
	});
	
	
	var Choose_service = octmain_obj.Choose_service;
	jQuery('#selected_custom .oct-value').html(Choose_service);
		
	jQuery('#oct_selected_location').val('X');
	jQuery('#oct_selected_service').val('0');
	jQuery('#oct_selected_staff').val('0');
	jQuery('#oct_service_addon_st').val('d');
	jQuery('#oct_selected_datetime').val('0');
				
});



/********Code For Register booking complete and login and logout***************/
jQuery(document).ready(function(){
	var errObj = octmain_error_obj;
	jQuery('#oct_login_form_check_validate').validate({
		rules:{
			'oct_existing_login_username_input':{required:true,email:true},
			'oct_existing_login_password_input':{required:true,minlength:8,maxlength:30},
		},
		messages:{
			'oct_existing_login_username_input':{required : errObj.Please_Enter_Email, email : errObj.Please_Enter_Valid_Email},
			'oct_existing_login_password_input':{required : errObj.Please_Enter_Password,minlength:errObj.Please_enter_minimum_8_Characters, maxlength:errObj.Please_enter_maximum_30_Characters},
			
		}
	});
});
jQuery(document).on('click','#oct_existing_login_btn', function() {
	if(jQuery('#oct_login_form_check_validate').valid()){
		jQuery('.oct-loader-first-step').show();
		var ajaxurl = octmain_obj.plugin_path;
		var uname = jQuery('#oct_existing_login_username').val();
		var pwd = jQuery('#oct_existing_login_password').val();
		var dataString = { 'uname':uname, 'pwd':pwd, 'action':'get_existing_user_data' };
		jQuery.ajax({
			type:"POST",
			url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
			dataType : 'html',
			data:dataString,
			success:function(response){
				jQuery('.oct-loader-first-step').hide();
				if(jQuery.trim(response) != "Invalid Username or Password"){
					var getdata = jQuery.parseJSON(response);
					jQuery('.user-login-main').hide();
					jQuery('.existing-user-login').hide();
					jQuery('.existing-user-success-login-message').show();
					jQuery('.new-user-personal-detail-area').show();
					jQuery("#invalid_un_pwd").css("display","none");
					jQuery('.hide_new_user_login_details').hide();
					jQuery('#logged_in_user_name').html(getdata.first_name+" "+getdata.last_name);
					
					jQuery('#new_user_firstname').addClass("focus");
					jQuery('#new_user_lastname').addClass("focus");
					jQuery('#oct-front-phone').addClass("focus");
					jQuery('#new_user_street_address').addClass("focus");
					jQuery('#new_user_city').addClass("focus");
					jQuery('#new_user_state').addClass("focus");
					jQuery('#new_user_notes').addClass("focus");
					
					jQuery('#new_user_preferred_password').val(getdata.password);
					jQuery('#new_user_preferred_username').val(getdata.user_email);
					jQuery('#new_user_firstname').val(getdata.first_name);
					jQuery('#new_user_lastname').val(getdata.last_name);
					/* jQuery('#oct-front-phone').val(getdata.phone); */
					jQuery('#oct-front-phone').intlTelInput("setNumber", getdata.phone);
					
					jQuery('#oct-front-phone').attr('data-ccode',getdata.ccode);
					jQuery('#new_user_street_address').val(getdata.address);
					jQuery('#new_user_city').val(getdata.city);
					jQuery('#new_user_state').val(getdata.state);
					jQuery('#new_user_notes').val(getdata.notes);
					
					if(getdata.gender == 'M'){
						jQuery('#oct-male').prop('checked',true);
					}else{
						jQuery('#oct-female').prop('checked',true);
					}
					
					jQuery('.error').each(function(){
						jQuery(this).hide();
					});
				}else{
					jQuery("#invalid_un_pwd").css("display","block");
				}
			}
		});
	}
});


/* Validate Card Fields */
jQuery(document).ready(function() {
	jQuery('input.cc-number').payment('formatCardNumber');
	jQuery('input.cc-cvc').payment('formatCardCVC');
	jQuery('input.cc-exp-month').payment('restrictNumeric');
	jQuery('input.cc-exp-year').payment('restrictNumeric');

});

jQuery(document).on( "click",'.oct-termcondition-area',function() {
		jQuery('.oct_terms_and_condition_error').hide();
});


jQuery(document).on( "click",'#btn-third-step',function() {
	jQuery('.oct_terms_and_condition_error').hide();
	var errObj = octmain_error_obj;
	var ajaxurl = octmain_obj.plugin_path;
	var thankyou_url = octmain_obj.thankyou_url;
	var oct_payment_gateways_st = octmain_obj.oct_payment_gateways_st;
	var oct_home_page_link = octmain_obj.oct_home_page_link;
	var octabook_thankyou_page_rdtime = octmain_obj.octabook_thankyou_page_rdtime;
	var oct_terms_and_condition_status = octmain_obj.oct_terms_and_condition_status;
	var currstep = jQuery('.oct-booking-step').data('current');
	var terms_condition = jQuery("#oct-accept-conditions").prop("checked");
    
	if(!jQuery('#oct_second_step').hasClass('show-data')){
		jQuery('#oct_first_step').removeClass('show-data');
		jQuery('#oct_first_step').addClass('hide-data');   
		oct_activate_nav('oct_second_step');     
		jQuery('#oct_second_step').removeClass('hide-data');
		jQuery('#oct_second_step').addClass('show-data');	
		return false;
	}	
	
	if(oct_terms_and_condition_status=='E' && terms_condition !== true){  
		jQuery('.oct_terms_and_condition_error').show();
		jQuery('html, body').stop().animate({
								'scrollTop': jQuery('.oct_terms_and_condition_error').offset().top - 80
						}, 800, 'swing', function () {});		
		
		return false; 
	}
	
	jQuery.validator.addMethod("pattern_phone", function(value, element) {
        return this.optional(element) || /^[0-9+]*$/.test(value);
    }, "Enter Only Numerics");
	
	jQuery('#oct_newuser_form_validate').validate({
		rules:{
			'new_user_preferred_username':{required:true, email:true, remote: {
								url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
								type: "POST",
								async: false,
								data: {
									email: function(){ return jQuery("#new_user_preferred_username").val(); },
									action:"check_existing_username"
								}
							}},
			'new_user_preferred_password':{required:true,minlength:8,maxlength:30},
			'new_user_firstname':{required:true},
			'new_user_lastname':{required:true},
			'oct-phone':{required:true,pattern_phone:true,minlength:10,maxlength:14},
			'new_user_street_address':{required:true},
			'new_user_city':{required:true},
			'new_user_state':{required:true},
			'new_user_notes':{required:true},
		},
		messages:{
			'new_user_preferred_username':{required : errObj.Please_Enter_Email, email : errObj.Please_Enter_Valid_Email, remote : errObj.Email_already_exist},
			'new_user_preferred_password':{required : errObj.Please_Enter_Password,minlength:errObj.Please_enter_minimum_8_Characters, maxlength:errObj.Please_enter_maximum_30_Characters},
			'new_user_firstname':{required : errObj.Please_Enter_First_Name},
			'new_user_lastname':{required : errObj.Please_Enter_Last_Name},
			'oct-phone':{required : errObj.Please_Enter_Phone_Number,pattern_phone : errObj.Please_Enter_Valid_Phone_Number,minlength:errObj.Please_enter_minimum_10_Characters, maxlength:errObj.Please_enter_maximum_14_Characters},
			'new_user_street_address':{required : errObj.Please_Enter_Address},
			'new_user_city':{required : errObj.Please_Enter_City},
			'new_user_state':{required : errObj.Please_Enter_State},
			'new_user_notes':{required : errObj.Please_Enter_Notes},
		}
	});
	
	jQuery('.get_custom_field').each(function(){
		var name_field = jQuery(this).attr('name');
		var required_field = jQuery(this).data('required');
		var fieldlabel = jQuery(this).data('fieldlabel')
		if(required_field == "Y"){
			jQuery(this).rules("add",{ required : true, messages : { required : errObj.Please_Enter+" "+fieldlabel+""}});
		}
	});
	
	if(jQuery('#oct_newuser_form_validate').valid()){
		jQuery('.oct-loader-first-step').show();
		jQuery('.oct_terms_and_condition_error').hide();
		var username = jQuery('#new_user_preferred_username').val();
		if(oct_payment_gateways_st=='E'){			
			var payment_method = jQuery('.oct_payment_method:checked').val();
		}else{
			var payment_method = 'pay_locally';	
		}
		var pwd = jQuery('#new_user_preferred_password').val();
		var fname = jQuery('#new_user_firstname').val();
		var lname = jQuery('#new_user_lastname').val();
		var phone = jQuery('#oct-front-phone').val();
		var address = jQuery('#new_user_street_address').val();
		var city = jQuery('#new_user_city').val();
		var state = jQuery('#new_user_state').val();
		var notes = jQuery('#new_user_notes').val();
		var check_status = jQuery('.new_and_existing_user_radio_btn').prop('checked');
		var check_statuss = jQuery('.new_and_existing_user_radio_btn:checked').val();
		var check_gender = jQuery('.new_user_gender').prop('checked');
		var ccode = jQuery('#oct-front-phone').data('ccode');
		
		if(check_statuss == 'Guest User'){
			var oct_user_type = 'guest';
		}else if(check_statuss == 'Existing User'){
			var oct_user_type = 'existing';
			if(jQuery('#oct_existing_login_btn').is(':visible')){
				jQuery('#invalid_un_pwd').show();
				jQuery('html, body').stop().animate({
						'scrollTop': jQuery('#invalid_un_pwd').offset().top - 80
				}, 800, 'swing', function () {});
				jQuery('.oct-loader-first-step').hide();
				return false;
			}			
		}else{
			var oct_user_type = 'new';
		}
		
		if(check_gender){
			var gender = 'M';
		}else{
			var gender = 'F';
		}
		
		var dynamic_field_add = {};
		jQuery('.get_custom_field').each(function(){
			if(jQuery(this).data('fieldname') == "radio_group"){
				dynamic_field_add[jQuery(this).data('fieldlabel')] = jQuery('.get_custom_field:checked').val();
			}else{
				dynamic_field_add[jQuery(this).data('fieldlabel')] = jQuery(this).val();
			}
		});
		
		var dataString = { 'username':username, 'pwd':pwd, 'fname':fname, 'lname':lname, 'phone':phone, 'address':address, 'city':city, 'state':state, 'notes':notes, 'oct_user_type':oct_user_type, 'payment_method':payment_method, 'gender':gender, 'dynamic_field_add':dynamic_field_add, 'ccode':ccode, 'action':'oct_booking_complete' };
		
		if(payment_method == 'stripe'){
			var stripe_pubkey = oct_stripeObj.pubkey;
			Stripe.setPublishableKey(stripe_pubkey);
			var stripeResponseHandler = function(status, response) {							
				if (response.error) {
					/* Show the errors on the form*/
					jQuery('.oct-loader-first-step').hide();
					jQuery('.show_card_payment_error').show();
					jQuery('.show_card_payment_error').text(response.error.message);
					jQuery('html, body').stop().animate({
						'scrollTop': jQuery('.show_card_payment_error').offset().top - 80
					}, 800, 'swing', function () {});
					
				} else {
					/* token contains id, last4, and card type*/
					var token = response.id;					
					function waitForElement(){ 
						if(typeof token !== "undefined" && token != ''){
							var st_token = token;									
							dataString['st_token'] = st_token;
							jQuery.ajax({
								type:"POST",
								url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
								dataType : 'html',
								data:dataString,
								success:function(response){						
									jQuery.ajax({
										type:"POST",
										url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
										dataType : 'html',			
										data:{ action:'refresh_sidebar'},
										success:function(response){
											jQuery('.oct-loader-first-step').hide();	
											jQuery('#oct_booking_sidebar').html(response);
											if(thankyou_url!=''){
												window.location.href = thankyou_url;
											}
											jQuery('#oct_first_step').removeClass('show-data');
											jQuery('#oct_first_step').addClass('hide-data');
											jQuery('#oct_second_step').removeClass('show-data');
											jQuery('#oct_second_step').addClass('hide-data');
											oct_activate_nav('oct_third_step');
											jQuery('#oct_third_step').removeClass('hide-data');
											jQuery('#oct_third_step').addClass('show-data');
											jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
											jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
											jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
											jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
											jQuery('.oct_remove_right_sidebar_class').removeClass('oct-cis');
											setTimeout(function() {	window.location.href = oct_home_page_link;  }, octabook_thankyou_page_rdtime);
										}
									});
								}
							});
						} else{ 
							setTimeout(function(){ waitForElement(); },2000); 
						} 
					}
					waitForElement();
				}
			};
			/*Disable the submit button to prevent repeated clicks*/
			Stripe.card.createToken({
				number: jQuery('#card-number').val(),
				cvc: jQuery('#cvc-code').val(),
				exp_month: jQuery('#card-expiry').val(),
				exp_year: jQuery('.cc-exp-year').val()
			}, stripeResponseHandler); 
		} else if(payment_method == "payumoney"){
			jQuery.ajax({
				type:"POST",
				url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
				dataType : 'html',
				data:dataString,
				success:function(response){
					jQuery.ajax({
						type:"POST",
						url  : ajaxurl+"/assets/lib/payumoney_payment_process.php",
						data:dataString,
						success:function(response){
							var pm = jQuery.parseJSON(response);
							jQuery("#payu_key").val(pm.merchant_key);
							jQuery("#payu_hash").val(pm.hash);
							jQuery("#payu_txnid").val(pm.txnid);
							jQuery("#payu_amount").val(pm.amt);
							jQuery("#payu_fname").val(pm.fname);
							jQuery("#payu_email").val(pm.email);
							jQuery("#payu_phone").val(pm.phone);
							jQuery("#payu_productinfo").val(pm.productinfo);
							jQuery("#payu_surl").val(pm.payu_surl);
							jQuery("#payu_furl").val(pm.payu_furl); 
							jQuery("#payu_service_provider").val(pm.service_provider);
							jQuery("#payuForm").submit();
						}
					});
				}
			});
			} else if(payment_method == 'paytm'){
				jQuery.ajax({
					type:"POST",
					url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
					dataType : 'html',
					data:dataString,
					success:function(response){
						jQuery('.oct-loader-first-step').show();
						jQuery.ajax({
							type:"POST",
							url  : ajaxurl+"/assets/lib/paytm_payment_process.php",
							data:dataString,
							success:function(response){
								var response_detail = jQuery.parseJSON(response);
								jQuery('#oct_paytm_form').attr('action',response_detail.PAYTM_TXN_URL);
								jQuery('#oct_CHECKSUMHASH').val(response_detail.CHECKSUMHASH);
								jQuery('#oct_paytm_form').append(response_detail.Extra_form_fields);
								jQuery("#oct_paytm_form").submit();
							}
						});
					}
				});
			}else if(payment_method == 'paypal'){
			jQuery.ajax({
				type:"POST",
				url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
				dataType : 'html',
				data:dataString,
				success:function(response){
					var response_detail = jQuery.parseJSON(response);
					if(response_detail.status=='error'){
						jQuery('.payment_error_msg').show();
						jQuery('.payment_error_msg').text(response_detail.value);
					}else{
						window.location = response_detail.value;
					}
					if(response=='OK'){
						jQuery.ajax({
							type:"POST",
							url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
							dataType : 'html',
							data:{ action:'refresh_sidebar'},
							success:function(response){
								jQuery('.oct-loader-first-step').hide();	
								jQuery('#oct_booking_sidebar').html(response);
								jQuery('#oct_first_step').removeClass('show-data');
								jQuery('#oct_first_step').addClass('hide-data');
								jQuery('#oct_second_step').removeClass('show-data');
								jQuery('#oct_second_step').addClass('hide-data');
								oct_activate_nav('oct_third_step');
								jQuery('#oct_third_step').removeClass('hide-data');
								jQuery('#oct_third_step').addClass('show-data');
								jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
								jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
								jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
								jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
								jQuery('.oct_remove_right_sidebar_class').removeClass('oct-cis');
								setTimeout(function() {	window.location.href = oct_home_page_link;  }, octabook_thankyou_page_rdtime);
							}
						});
					}
				}
			});
		}else{
			jQuery.ajax({
				type:"POST",
				url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
				dataType : 'html',
				data:dataString,
				success:function(response){	
					jQuery.ajax({
						type:"POST",
						url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
						dataType : 'html',			
						data:{ action:'refresh_sidebar'},
						success:function(response){
							jQuery('.oct-loader-first-step').hide();	
							if(thankyou_url!=''){
								window.location.href = thankyou_url;
							}
							jQuery('#oct_booking_sidebar').html(response);
							jQuery('#oct_first_step').removeClass('show-data');
							jQuery('#oct_first_step').addClass('hide-data');
							jQuery('#oct_second_step').removeClass('show-data');
							jQuery('#oct_second_step').addClass('hide-data');
							oct_activate_nav('oct_third_step');
							jQuery('#oct_third_step').removeClass('hide-data');
							jQuery('#oct_third_step').addClass('show-data');
							jQuery('.oct_remove_left_sidebar_class').addClass('no-sidebar-right');
							jQuery('.oct_remove_left_sidebar_class').removeClass('oct-asr');
							jQuery('.oct_remove_right_sidebar_class').addClass('no-cart-item-sidebar');
							jQuery('.oct_remove_right_sidebar_class').removeClass('cart-item-sidebar');
							jQuery('.oct_remove_right_sidebar_class').removeClass('oct-cis');
							setTimeout(function() {	window.location.href = oct_home_page_link;  }, 15000);
						}
					});
				}
			});
		}
	}
});

jQuery(document).on( "click", '#oct_log_out_user', function() {
	var ajaxurl = octmain_obj.plugin_path;
	var dataString = { 'action':'oct_logout_user' };
	jQuery('.oct-loader-first-step').show();
	jQuery.ajax({
		type:"POST",
		url  : ajaxurl+"/assets/lib/oct_front_ajax.php",
		dataType : 'html',
		data:dataString,
		success:function(response){
			jQuery('.oct-loader-first-step').hide();
			jQuery('.user-login-main').show();
			jQuery('.user-login-main').show();
			jQuery('.existing-user-success-login-message').hide();
			jQuery('#oct-new-user').trigger('click');
			
			jQuery(".oct-main-left label.custom").removeClass('focus'); 
			jQuery(".oct-main-left .custom-input").removeClass('focus'); 
			jQuery('#new_user_preferred_password').val('');
			jQuery('#new_user_preferred_username').val('');
			jQuery('#new_user_firstname').val('');
			jQuery('#new_user_lastname').val('');
			jQuery('#oct-front-phone').val('');
			jQuery('#new_user_street_address').val('');
			jQuery('#new_user_city').val('');
			jQuery('#new_user_state').val('');
			jQuery('#new_user_notes').val('');
			
			jQuery('#oct-male').prop('checked',true);
			
		}
	});
});

/* Display Country Code on click flag on phone*/
jQuery(window).load(function(){
	if(jQuery("#oct-front-phone").data('ccode') != ''){
		jQuery('.country').removeClass('active');
		jQuery('.country').each(function(){
			if('+'+jQuery(this).data("dial-code") == jQuery("#oct-front-phone").data('ccode')){
				jQuery(this).addClass('active');
				var get_phoneno = jQuery(this).val();
				jQuery('#oct-front-phone').intlTelInput("setNumber", '+'+jQuery(this).data("dial-code")+''+get_phoneno);
			}
		});	
	}else{
		if(jQuery('.input_flg').val() != ''){
		   var country_code=jQuery('.input_flg').val();
		   var get_phoneno = jQuery('#oct-front-phone').val();
		   if(get_phoneno == ''){
			jQuery('#oct-front-phone').intlTelInput("setNumber",country_code);
		   }
		   jQuery("#oct-front-phone").attr('data-ccode',country_code);
		  }else{
				var country_code=jQuery('.country.active').data("dial-code");
				if(country_code === undefined){
					country_code = '1';
				}
				var get_phoneno = jQuery('#oct-front-phone').val();
				if(get_phoneno == ''){
					jQuery('#oct-front-phone').intlTelInput("setNumber", '+'+country_code);
				}
				jQuery("#oct-front-phone").attr('data-ccode','+'+country_code);
		  }
	}
});
jQuery(document).on('click','.country',function() {
	var country_code=jQuery(this).data("dial-code");
	var get_phoneno = jQuery('#oct-front-phone').val();
	jQuery('#oct-front-phone').intlTelInput("setNumber", '+'+country_code);
	jQuery("#oct-front-phone").attr('data-ccode','+'+country_code);
});

/* On focus transform label */
jQuery(document).ready(function () { 
	function checkForInput(element) {
	  /* element is passed to the function ^ */
		if(jQuery(element).hasClass('oct-phone-input')){
			var $label = jQuery('.oct-phone-label'); 
		}else{
			var $label = jQuery(element).siblings('label'); 
		}
				
		if (jQuery(element).val().length > 0) {
			$label.addClass('focus');
			jQuery(this).addClass( "focus" );
		} else {
			$label.removeClass('focus');
			jQuery(this).removeClass( "focus" );
		}
		/* user login then show the label at top */
		if (jQuery('.custom-input').val().length > 0) {
			jQuery(".oct-main-left label.custom").addClass('focus'); 
		}else{
			jQuery('.label.custom').removeClass('focus');
		}
		/* user login then show the label at top */
		if (jQuery('#oct-front-phone').val().length > 0) {
			jQuery("label.oct-phone-label").addClass('focus'); 
		}else{
			jQuery('#oct-front-phone').removeClass('focus');
		}
		
	}	
	
	/* The lines below are executed on page load */
	jQuery('.custom-input').each(function() {
		checkForInput(this);	
		if (jQuery(this).val().length > 0) {
			jQuery(this).addClass('focus'); 
		}else{
			jQuery(this).removeClass('focus'); 
		}
		
	});

	 /* The lines below (inside) are executed on change & keyup */
	jQuery('.custom-input').on('change keyup', function() {
		checkForInput(this);  
		jQuery(this).addClass( "focus" );	
	});
});

jQuery(document).on("click",".get_current_location",function() {

 var geocoder;

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
	} 
//Get the latitude and the longitude;
	function successFunction(position) {
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			codeLatLng(lat, lng)
	}

	function errorFunction(err){
			console.warn(`ERROR(${err.code}): ${err.message}`);
	}

  function initialize() {
    geocoder = new google.maps.Geocoder();
  }

  function codeLatLng(lat, lng) {
	var geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    var add= results[0].formatted_address ;
                    var  value=add.split(",");
                    count=value.length;
										current_loc = value[count-6];
										current_loc1 = value[count-5];
										current_loc2 = value[count-4];
                    country=value[count-1];
                    state=value[count-2];
                    city=value[count-3];
									  jQuery("#new_user_street_address").val(current_loc+''+current_loc1+''+current_loc2);	
									  jQuery("#new_user_city").val(city);	
									  jQuery("#new_user_state").val(state);	
                }
                else  {
                    alert("address not found");
                }
        }
         else {
            alert("Geocoder failed due to: " + status);
        }
      });
  }
});