
function responsiveMobileMenu() {	
		jQuery('.octabook_nav_new').each(function() {
			
			
			
			jQuery(this).children('ul').addClass('octabook_nav_new-main-list');	// mark main menu list
			
			
			var jQuerystyle = jQuery(this).attr('data-menu-style');	// get menu style
				if ( typeof jQuerystyle == 'undefined' ||  jQuerystyle == false )
					{
						jQuery(this).addClass('graphite'); // set graphite style if style is not defined
					}
				else {
						jQuery(this).addClass(jQuerystyle);
					}
					
					
			/* 	width of menu list (non-toggled) */
			
			var jQuerywidth = 0;
				jQuery(this).find('ul li').each(function() {
					jQuerywidth += jQuery(this).outerWidth();
				});
				
			// if modern browser
			
			if (jQuery.support.leadingWhitespace) {
				jQuery(this).css('max-width' , jQuerywidth*1.08+'px');
			}
			// 
			else {
				jQuery(this).css('width' , jQuerywidth*1.05+'px');
			}
		
	 	});
}
function getMobileMenu() {

	/* 	build toggled dropdown menu list */
	
	jQuery('.octabook_nav_new').each(function() {	
				var menutitle = jQuery(this).attr("data-menu-title");
				if ( menutitle == "" ) {
					menutitle = "Menu";
				}
				else if ( menutitle == undefined ) {
					menutitle = "Menu";
				}
				var jQuerymenulist = jQuery(this).children('.octabook_nav_new-main-list').html();
				var jQuerymenucontrols ="<div class='octabook_nav_new-toggled-controls'><div class='octabook_nav_new-toggled-title'>" + menutitle + "</div><div class='octabook_nav_new-button'><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div></div>";
				jQuery(this).prepend("<div class='octabook_nav_new-toggled octabook_nav_new-closed'>"+jQuerymenucontrols+"<ul>"+jQuerymenulist+"</ul></div>");

		});
}

function adaptMenu() {
	
	/* 	toggle menu on resize */
	
	jQuery('.octabook_nav_new').each(function() {
			var jQuerywidth = jQuery(this).css('max-width');
			jQuerywidth = jQuerywidth.replace('px', ''); 
			if ( jQuery(this).parent().width() < jQuerywidth*1 ) {
				jQuery(this).children('.octabook_nav_new-main-list').hide(0);
				jQuery(this).children('.octabook_nav_new-toggled').show(0);
			}
			else {
				jQuery(this).children('.octabook_nav_new-main-list').show(0);
				jQuery(this).children('.octabook_nav_new-toggled').hide(0);
			}
		});
		
}

jQuery(function() {

	 responsiveMobileMenu();
	 getMobileMenu();
	 adaptMenu();
	 
	 /* slide down mobile menu on click */
	 
	 jQuery('.octabook_nav_new-toggled, .octabook_nav_new-toggled .octabook_nav_new-button').click(function(){
	 	if ( jQuery(this).is(".octabook_nav_new-closed")) {
		 	 jQuery(this).find('ul').stop().show(300);
		 	 jQuery(this).removeClass("octabook_nav_new-closed");
	 	}
	 	else {
		 	jQuery(this).find('ul').stop().hide(300);
		 	 jQuery(this).addClass("octabook_nav_new-closed");
	 	}
		
	});	

});
	/* 	hide mobile menu on resize */
jQuery(window).resize(function() {
 	adaptMenu();
});