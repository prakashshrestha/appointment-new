<?php 
	include(dirname(__FILE__).'/header.php');
	$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));
	
	/* Create Location */
	$location = new octabook_location();
	$oct_image_upload= new octabook_image_upload();
	$staff = new octabook_staff();
	
	/*********************Sample Date**************************/
	if(get_option('octabook_sample_status')=='Y'){
	$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			if (file_exists($root.'/wp-load.php')) {
			require_once($root.'/wp-load.php');
		}
	global $wpdb;
	
		$locationsinfo = array(array('location_title'=>'California','description'=>'California','email'=>'California@California.com','phone'=>'7739477310','address'=>'1625 E 75th St','city'=>'California','state'=>'Los Angles','zip'=>'60649','country'=>'USA'),array('location_title'=>'Singapore ','description'=>'Singapore','email'=>'Singapore@Singapore.com','phone'=>'8884081113','address'=>'514 S. MAGNOLIA ST.','city'=>'Rome','state'=>'Rome','zip'=>'32806','country'=>'Italy'));
		
		$staffsinfo = 	array(array('staff_name'=>'John','username'=>'john'.rand(10,1000),'email'=>'john@demo.com','description'=>'John staff description'),array('staff_name'=>'Johndoe','username'=>'johndoe'.rand(10,1000),'email'=>'Johndoe@demo.com','description'=>'Johndoe staff description'));
		
		$staffsinfo2 = 	array(array('staff_name'=>'Divina Lapine','username'=>'divinalapine'.rand(10,1000),'email'=>'divinalapine@demo.com','description'=>'Divina Lapine staff description'),array('staff_name'=>'Alia Gile','username'=>'aliagile'.rand(10,1000),'email'=>'aliagile@demo.com','description'=>'Alia Gile staff description'));
		
		$staffsinfo3 = 	array(array('staff_name'=>'Sam Kelly','username'=>'samkelly'.rand(10,1000),'email'=>'samkelly@demo.com','description'=>'Sam Kelly staff description'),array('staff_name'=>'Dorothy	Blake','username'=>'dorothyblake'.rand(10,1000),'email'=>'dorothyblake@demo.com','description'=>'Dorothy	Blake staff description'));
		
		$servicesinfo = array(array('service_title'=>'Cosmetic Dentistry','description'=>'Cosmetic dentistry is generally used to refer to any dental work that improves the appearance (though not necessarily the functionality) of teeth, gums and/or bite. It primarily focuses on improvement dental aesthetics in color, position, shape, size, alignment and overall smile appearance.'),array('service_title'=>'Routine Tooth Extractions','description'=>'Routine Extractions. There are instances when a tooth cannot be restored. Extensive decay as a result of chronic neglect or trauma that results in the inadvertent fracture of teeth are two leading causes for a tooth to be deemed non-salvageable.'));
		
		$servicesinfo2 = array(array('service_title'=>'Composite Bonding','description'=>'Composite bonding refers to the repair of decayed, damaged or discolored teeth using material that resembles the color of tooth enamel. Your dentist drills out the tooth decay and applies the composite onto the tooths surface, then culpts it into the right shape before curing it with a high-intensity light.'),array('service_title'=>'Dental Veneers','description'=>'Typically manufactured from medical-grade ceramic, dental veneers are made individually for each patient to resemble ones natural teeth.'));
		
		$servicesinfo3 = array(array('service_title'=>'Teeth Whitening','description'=>'One of the most basic cosmetic dentistry procedures, teeth whitening or teeth bleaching can be performed at your dentists office. Whitening should occur after plaque, tartar and other debris are cleaned from the surface of each tooth, restoring their natural appearance.'),array('service_title'=>'Implants','description'=>'Dental implants are used to replace teeth after tooth loss. The dentist inserts a small titanium screw into the jaw at the site of the missing tooth, which serves as the support for a crown.'));
		
		$addonsinfo = array(array('addon_title'=>'Teeth Whitening','price'=>'20','max_qty'=>5),array('addon_title'=>'Surgical tooth extractions','price'=>'100','max_qty'=>10));
		
		$addonsinfo2 = array(array('addon_title'=>'Composite Bonding','price'=>'20','max_qty'=>5),array('addon_title'=>'Dental Veneers','price'=>'100','max_qty'=>10));
		
		$addonsinfo3 = array(array('addon_title'=>'Teeth Whitening','price'=>'20','max_qty'=>5),array('addon_title'=>'Implants','price'=>'100','max_qty'=>10));
		
		$categoriesinfo = array(array('category_title'=>' Cosmetic Dentistry'),array('category_title'=>'Routine Tooth Extractions'));
		
		$categoriesinfo2 = array(array('category_title'=>'Composite Bonding'),array('category_title'=>'Dental Veneers'));
		
		$categoriesinfo3 = array(array('category_title'=>'Teeth Whitening'),array('category_title'=>'Implants'));
		
		$oct_clientinfo = array(array('client_name'=>'John Deo','client_email'=>'johndeo@example.com','client_phone'=>'+17567436945'),array('client_name'=>'John Martin','client_email'=>'johnmartin@example.com','client_phone'=>'+17567436949'));
	
		$oct_clientinfo2 = array(array('client_name'=>'Olivia	Terry','client_email'=>'oliviaterry@example.com','client_phone'=>'+17567436945'),array('client_name'=>'Leonard	North','client_email'=>'leonardnorth@example.com','client_phone'=>'+17567436949'));
		
		$oct_clientinfo3 = array(array('client_name'=>'Jessica Walker','client_email'=>'jessicawalker@example.com','client_phone'=>'+17567436945'),array('client_name'=>'James McGrath','client_email'=>'jamesmcgrath@example.com','client_phone'=>'+17567436949'));
		
		$locationsids = array();	
		$servicesids = array();	
		$categoriesids = array();	
		$staffsids = array();
		$bdclientids = array();
		$bookingsids = array();
		$paymentsids = array();
		$orderids = array();
		/*Adding Locations */
		foreach($locationsinfo as $locationinfo){
			if(get_option('octabook_multi_location')=='E'){	
				$wpdb->query("insert into ".$wpdb->prefix."oct_locations set location_title='".$locationinfo['location_title']."',description='".$locationinfo['description']."',email='".$locationinfo['email']."',phone='".$locationinfo['phone']."',address='".$locationinfo['address']."',city='".$locationinfo['city']."',state='".$locationinfo['state']."',zip='".$locationinfo['zip']."',country='".$locationinfo['country']."',status='E'");
				$locationsids[] = $wpdb->insert_id;
			}else{
				$locationsids[] = 0;
			}
		}	
		
		/* Adding Categories */
		$catecounter = 0;
		foreach($categoriesinfo as $categoryinfo){
						
			$wpdb->query("insert into ".$wpdb->prefix."oct_categories set location_id='".$locationsids[$catecounter]."',category_title='".$categoryinfo['category_title']."'");
			$categoriesids[] =  $wpdb->insert_id;
			$catecounter++;
		}
		
		/* Adding Categories 2 */
		
		$catecounter = 0;
		foreach($categoriesinfo2 as $categoryinfo){
						
			$wpdb->query("insert into ".$wpdb->prefix."oct_categories set location_id='".$locationsids[$catecounter]."',category_title='".$categoryinfo['category_title']."'");
			$categoriesids2[] =  $wpdb->insert_id;
			$catecounter++;
		}
		
		/* Adding Categories 3 */
		
		$catecounter = 0;
		foreach($categoriesinfo3 as $categoryinfo){
						
			$wpdb->query("insert into ".$wpdb->prefix."oct_categories set location_id='".$locationsids[$catecounter]."',category_title='".$categoryinfo['category_title']."'");
			$categoriesids3[] =  $wpdb->insert_id;
			$catecounter++;
		}
		
		
		/* Add Staff Members1 */
		$staffcounter =0;
		foreach($staffsinfo as $staffinfo){
		$userdata = array('user_login'=>$staffinfo['username'],'user_email'=>$staffinfo['email'],'user_pass'=>$staffinfo['staff_name'],'first_name'=>$staffinfo['staff_name'],		'last_name'=>'','nickname'=>'','role'=>'subscriber');					
		$user_id = wp_insert_user($userdata);
		$staffsids[] = $user_id;	
		$user = new WP_User($user_id);
		$user->add_cap('oct_staff');
		add_user_meta($user_id, 'staff_location',$locationsids[$staffcounter]);
		add_user_meta($user_id, 'staff_phone','');
		add_user_meta($user_id, 'staff_description',$staffinfo['description']);
		add_user_meta($user_id, 'schedule_type','W');
		add_user_meta($user_id, 'staff_image','');
		add_user_meta($user_id, 'staff_status','E');
		add_user_meta($user_id, 'staff_timezone','');
		add_user_meta($user_id, 'staff_timezoneID','');
			/*Adding Provider Schedule */
			for($dayid=1;$dayid<=7;$dayid++){
				$wpdb->query("insert into ".$wpdb->prefix."oct_schedule set provider_id='".$user_id."',weekday_id='".$dayid."',daystart_time='08:00:00',dayend_time='17:00:00',week_id='1'");
			}
			$staffcounter++;		
		}
		
		/* Add Staff Members2 */
		$staffcounter =0;
		foreach($staffsinfo2 as $staffinfo){
		$userdata = array('user_login'=>$staffinfo['username'],'user_email'=>$staffinfo['email'],'user_pass'=>$staffinfo['staff_name'],'first_name'=>$staffinfo['staff_name'],		'last_name'=>'','nickname'=>'','role'=>'subscriber');					
		$user_id = wp_insert_user($userdata);
		$staffsids2[] = $user_id;	
		$user = new WP_User($user_id);
		$user->add_cap('oct_staff');
		add_user_meta($user_id, 'staff_location',$locationsids[$staffcounter]);
		add_user_meta($user_id, 'staff_phone','');
		add_user_meta($user_id, 'staff_description',$staffinfo['description']);
		add_user_meta($user_id, 'schedule_type','W');
		add_user_meta($user_id, 'staff_image','');
		add_user_meta($user_id, 'staff_status','E');
		add_user_meta($user_id, 'staff_timezone','');
		add_user_meta($user_id, 'staff_timezoneID','');
		
			/*Adding Provider Schedule */
			for($dayid=1;$dayid<=7;$dayid++){
				$wpdb->query("insert into ".$wpdb->prefix."oct_schedule set provider_id='".$user_id."',weekday_id='".$dayid."',daystart_time='08:00:00',dayend_time='17:00:00',week_id='1'");
			}
			$staffcounter++;		
		}
		
		/* Add Staff Members3 */
		$staffcounter =0;
		foreach($staffsinfo3 as $staffinfo){
		$userdata = array('user_login'=>$staffinfo['username'],'user_email'=>$staffinfo['email'],'user_pass'=>$staffinfo['staff_name'],'first_name'=>$staffinfo['staff_name'],		'last_name'=>'','nickname'=>'','role'=>'subscriber');					
		$user_id = wp_insert_user($userdata);
		$staffsids3[] = $user_id;	
		$user = new WP_User($user_id);
		$user->add_cap('oct_staff');
		add_user_meta($user_id, 'staff_location',$locationsids[$staffcounter]);
		add_user_meta($user_id, 'staff_phone','');
		add_user_meta($user_id, 'staff_description',$staffinfo['description']);
		add_user_meta($user_id, 'schedule_type','W');
		add_user_meta($user_id, 'staff_image','');
		add_user_meta($user_id, 'staff_status','E');
		add_user_meta($user_id, 'staff_timezone','');
		add_user_meta($user_id, 'staff_timezoneID','');
		
			/*Adding Provider Schedule */
			for($dayid=1;$dayid<=7;$dayid++){
				$wpdb->query("insert into ".$wpdb->prefix."oct_schedule set provider_id='".$user_id."',weekday_id='".$dayid."',daystart_time='08:00:00',dayend_time='17:00:00',week_id='1'");
			}
			$staffcounter++;		
		}
		
		

		/* Adding Services */
		$servcounter = 0;
		foreach($servicesinfo as $serviceinfo){
			$wpdb->query("insert into ".$wpdb->prefix."oct_services set location_id='".$locationsids[$servcounter]."',color_tag='#".rand(100000,999999)."',service_title='".$serviceinfo['service_title']."',category_id='".$categoriesids[$servcounter]."',duration='30',amount='50',service_description='".$serviceinfo['description']."',service_status='Y'");
			$servicesids[] =  $wpdb->insert_id;			
			
			/*Link Service With Staff Member*/
			$wpdb->query("insert into ".$wpdb->prefix."oct_providers_services set provider_id='".$staffsids[$servcounter]."',service_id='".$servicesids[$servcounter]."'");
			
			/* Service Addons */
			$wpdb->query("INSERT INTO ".$wpdb->prefix."oct_services_addon (id,service_id,addon_service_name,base_price,maxqty,image,multipleqty,status,position,predefine_image,predefine_image_title,location_id)values('','".$servicesids[$servcounter]."','".$addonsinfo[$servcounter]['addon_title']."','".$addonsinfo[$servcounter]['price']."','".$addonsinfo[$servcounter]['max_qty']."','','Y','E','','','','".$locationsids[$servcounter]."')");
			
			$servcounter++;
		}
		
		/* Adding Services2 */
		$servcounter = 0;
		$category_count = 2;
		foreach($servicesinfo2 as $serviceinfo){
		
			$wpdb->query("insert into ".$wpdb->prefix."oct_services set location_id='".$locationsids[$servcounter]."',color_tag='#".rand(100000,999999)."',service_title='".$serviceinfo['service_title']."',category_id='".$categoriesids2[$servcounter]."',duration='30',amount='50',service_description='".$serviceinfo['description']."',service_status='Y'");
			$servicesids[] =  $wpdb->insert_id;			
			
			/*Link Service With Staff Member*/
			$wpdb->query("insert into ".$wpdb->prefix."oct_providers_services set provider_id='".$staffsids2[$servcounter]."',service_id='".$servicesids[$category_count]."'");
			
			/* Service Addons */
			$wpdb->query("INSERT INTO ".$wpdb->prefix."oct_services_addon (id,service_id,addon_service_name,base_price,maxqty,image,multipleqty,status,position,predefine_image,predefine_image_title,location_id)values('','".$servicesids2[$category_count]."','".$addonsinfo2[$servcounter]['addon_title']."','".$addonsinfo2[$servcounter]['price']."','".$addonsinfo2[$servcounter]['max_qty']."','','Y','E','','','','".$locationsids[$servcounter]."')");
			
			$servcounter++;
			$category_count++;
		}
		
		/* Adding Services2 */
		$servcounter = 0;
		$category_count = 4;
		foreach($servicesinfo3 as $serviceinfo){
			$wpdb->query("insert into ".$wpdb->prefix."oct_services set location_id='".$locationsids[$servcounter]."',color_tag='#".rand(100000,999999)."',service_title='".$serviceinfo['service_title']."',category_id='".$categoriesids3[$servcounter]."',duration='30',amount='50',service_description='".$serviceinfo['description']."',service_status='Y'");
			$servicesids[] =  $wpdb->insert_id;			
			
			/*Link Service With Staff Member*/
			$wpdb->query("insert into ".$wpdb->prefix."oct_providers_services set provider_id='".$staffsids3[$servcounter]."',service_id='".$servicesids[$category_count]."'");
			
			/* Service Addons */
			$wpdb->query("INSERT INTO ".$wpdb->prefix."oct_services_addon (id,service_id,addon_service_name,base_price,maxqty,image,multipleqty,status,position,predefine_image,predefine_image_title,location_id)values('','".$servicesids3[$category_count]."','".$addonsinfo3[$servcounter]['addon_title']."','".$addonsinfo3[$servcounter]['price']."','".$addonsinfo3[$servcounter]['max_qty']."','','Y','E','','','','".$locationsids[$servcounter]."')");
			
			$servcounter++;
			$category_count++;
		}
		
		
	/* Adding Clients */
		$clientcounter = 0;
		
		foreach($oct_clientinfo as $oct_clientsinfo){
			
			if($oct_clientsinfo['client_name'] == 'John Deo'){	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='California@California.com'";
				$res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Cosmetic Dentistry'";
				$res_service = $wpdb->get_results($query);
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1s = date_i18n('Y-m-d H:i:s');
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s)))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}else{	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='Singapore@Singapore.com'";
	      $res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Routine Tooth Extractions'";
				$res_service = $wpdb->get_results($query);
				
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 month", strtotime(date_i18n('Y-m-d',strtotime($todaydate)).' '.date_i18n('H:i:s',$todaydate))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}
			
			/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			
			$orderids[] =$order_id;
			$oct_user_info = array(
					'user_login'    =>   $oct_clientsinfo['client_name'],
					'user_email'    =>   $oct_clientsinfo['client_email'],
					'user_pass'     =>   '12345678',
					'first_name'    =>   $oct_clientsinfo['client_name'],
					'last_name'     =>   '',
					'nickname'      =>  '',
					'role' => 'subscriber'
					);	
			$new_oct_user = wp_insert_user( $oct_user_info );
			$bdclientids[] =  $new_oct_user;
			$user = new WP_User($new_oct_user);
			$user->add_cap('read');
			$user->add_cap('oct_client'); 
			$user->add_role('oct_users');
			$user_id = $new_oct_user;
			$user_login = $preff_username;
			add_user_meta( $new_oct_user, 'oct_client_locations','#'.$res[0]->id.'#');
			
			$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
			$add = $wpdb->query($query1);
			if($add){
				echo "addedd client";
			}else{
				echo "not client";
			}
			
			for($i=0;$i<=3;$i++){
				/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			 	if($i < 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				 } elseif($i <= 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+2 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}else{
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+3 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				} 
				
				$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
				$add = $wpdb->query($query1);
				if($add){
					echo "addedd client";
				}else{
					echo "not client";
				}
				foreach($res_provider as $re_provider){
				$query2 = "INSERT INTO ".$wpdb->prefix."oct_bookings (`id`, `location_id`, `order_id`, `client_id`, `service_id`, `provider_id`, `booking_price`, `booking_datetime`, `booking_endtime`, `booking_status`, `reject_reason`, `cancel_reason`, `confirm_note`, `reschedule_note`, `reminder`, `notification`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$order_id."', '".$user_id."', '".$res_service[0]->id."', '".$re_provider->provider_id."', '50', '".$bookdate1."', '".$bookend."', 'C', '', '', '', '', '0', '0', NOW());";
				$add1 = $wpdb->query($query2);
				$bookingsids[] = $wpdb->insert_id;
				}
				$query3 = "INSERT INTO ".$wpdb->prefix."oct_payments (`id`, `location_id`, `client_id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `discount`, `taxes`, `partial`, `net_total`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$user_id."', '".$order_id."', 'pay_locally', '', '50', '0', '0', '0', '50', '')";
				$add2 = $wpdb->query($query3);
				$paymentsids[] = $wpdb->insert_id;
				
			}
			
			$clientcounter++;
		}
		
		foreach($oct_clientinfo2 as $oct_clientsinfo){
			
			if($oct_clientsinfo['client_name'] == 'Olivia	Terry'){	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='California@California.com'";
				$res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Composite Bonding'";
				$res_service = $wpdb->get_results($query);
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1s = date_i18n('Y-m-d H:i:s');
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s)))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}else{	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='Singapore@Singapore.com'";
	      $res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Dental Nenners'";
				$res_service = $wpdb->get_results($query);
				
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 month", strtotime(date_i18n('Y-m-d',strtotime($todaydate)).' '.date_i18n('H:i:s',$todaydate))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}
			
			/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			
			$orderids2[] =$order_id;
			$oct_user_info = array(
					'user_login'    =>   $oct_clientsinfo['client_name'],
					'user_email'    =>   $oct_clientsinfo['client_email'],
					'user_pass'     =>   '12345678',
					'first_name'    =>   $oct_clientsinfo['client_name'],
					'last_name'     =>   '',
					'nickname'      =>  '',
					'role' => 'subscriber'
					);	
			$new_oct_user = wp_insert_user( $oct_user_info );
			$bdclientids[] =  $new_oct_user;
			$user = new WP_User($new_oct_user);
			$user->add_cap('read');
			$user->add_cap('oct_client'); 
			$user->add_role('oct_users');
			$user_id = $new_oct_user;
			$user_login = $preff_username;
			add_user_meta( $new_oct_user, 'oct_client_locations','#'.$res[0]->id.'#');
			
			$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
			$add = $wpdb->query($query1);
			if($add){
				echo "addedd client";
			}else{
				echo "not client";
			}
			
			for($i=0;$i<=3;$i++){
				/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			if($i < 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				 } elseif($i <= 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+2 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}else{
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+3 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}
				
				$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
				$add = $wpdb->query($query1);
				if($add){
					echo "addedd client";
				}else{
					echo "not client";
				}
				foreach($res_provider as $re_provider){
					print_r($re_provider);
				$query2 = "INSERT INTO ".$wpdb->prefix."oct_bookings (`id`, `location_id`, `order_id`, `client_id`, `service_id`, `provider_id`, `booking_price`, `booking_datetime`, `booking_endtime`, `booking_status`, `reject_reason`, `cancel_reason`, `confirm_note`, `reschedule_note`, `reminder`, `notification`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$order_id."', '".$user_id."', '".$res_service[0]->id."', '".$re_provider->provider_id."', '50', '".$bookdate1."', '".$bookend."', 'C', '', '', '', '', '0', '0', NOW());";
				$add1 = $wpdb->query($query2);
				$bookingsids[] = $wpdb->insert_id;
				}
				$query3 = "INSERT INTO ".$wpdb->prefix."oct_payments (`id`, `location_id`, `client_id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `discount`, `taxes`, `partial`, `net_total`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$user_id."', '".$order_id."', 'pay_locally', '', '50', '0', '0', '0', '50', '')";
				$add2 = $wpdb->query($query3);
				$paymentsids[] = $wpdb->insert_id;
				
			}
			
			$clientcounter++;
		}
		foreach($oct_clientinfo3 as $oct_clientsinfo){
			
			if($oct_clientsinfo['client_name'] == 'Jessica Walker'){	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='California@California.com'";
				$res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Teeth Whitening'";
				$res_service = $wpdb->get_results($query);
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1s = date_i18n('Y-m-d H:i:s');
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s)))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}else{	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='Singapore@Singapore.com'";
	      $res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Implants'";
				$res_service = $wpdb->get_results($query);
				
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 month", strtotime(date_i18n('Y-m-d',strtotime($todaydate)).' '.date_i18n('H:i:s',$todaydate))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}
			
			/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			
			$orderids3[] =$order_id;
			$oct_user_info = array(
					'user_login'    =>   $oct_clientsinfo['client_name'],
					'user_email'    =>   $oct_clientsinfo['client_email'],
					'user_pass'     =>   '12345678',
					'first_name'    =>   $oct_clientsinfo['client_name'],
					'last_name'     =>   '',
					'nickname'      =>  '',
					'role' => 'subscriber'
					);	
			$new_oct_user = wp_insert_user( $oct_user_info );
			$bdclientids[] =  $new_oct_user;
			$user = new WP_User($new_oct_user);
			$user->add_cap('read');
			$user->add_cap('oct_client'); 
			$user->add_role('oct_users');
			$user_id = $new_oct_user;
			$user_login = $preff_username;
			add_user_meta( $new_oct_user, 'oct_client_locations','#'.$res[0]->id.'#');
			
			$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
			$add = $wpdb->query($query1);
			if($add){
				echo "addedd client";
			}else{
				echo "not client";
			}
			
			for($i=0;$i<2;$i++){
				/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			if($i < 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				 } elseif($i <= 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+2 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}else{
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+3 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}
				
				$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
				$add = $wpdb->query($query1);
				if($add){
					echo "addedd client";
				}else{
					echo "not client";
				}
				foreach($res_provider as $re_provider){
					print_r($re_provider);
				$query2 = "INSERT INTO ".$wpdb->prefix."oct_bookings (`id`, `location_id`, `order_id`, `client_id`, `service_id`, `provider_id`, `booking_price`, `booking_datetime`, `booking_endtime`, `booking_status`, `reject_reason`, `cancel_reason`, `confirm_note`, `reschedule_note`, `reminder`, `notification`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$order_id."', '".$user_id."', '".$res_service[0]->id."', '".$re_provider->provider_id."', '50', '".$bookdate1."', '".$bookend."', 'C', '', '', '', '', '0', '0', NOW());";
				$add1 = $wpdb->query($query2);
				$bookingsids[] = $wpdb->insert_id;
				}
				$query3 = "INSERT INTO ".$wpdb->prefix."oct_payments (`id`, `location_id`, `client_id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `discount`, `taxes`, `partial`, `net_total`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$user_id."', '".$order_id."', 'pay_locally', '', '50', '0', '0', '0', '50', '')";
				$add2 = $wpdb->query($query3);
				$paymentsids[] = $wpdb->insert_id;
				
			}
			
			$clientcounter++;
		}
		
			$sampledataids = array('locationsids'=>implode(',',$locationsids),'servicesids'=>implode(',',$servicesids),'categoriesids'=>implode(',',$categoriesids),'categoriesids2'=>implode(',',$categoriesids2),'categoriesids3'=>implode(',',$categoriesids3),'staffsids'=>implode(',',$staffsids),'staffsids2'=>implode(',',$staffsids2),'staffsids3'=>implode(',',$staffsids3),'bdclientids'=>implode(',',$bdclientids),'bookingsids'=>implode(',',$bookingsids),'paymentsids'=>implode(',',$paymentsids),'orderids'=>implode(',',$orderids),'orderids2'=>implode(',',$orderids2),'orderids3'=>implode(',',$orderids3));
			
			add_option('octabook_sample_dataids',serialize($sampledataids));	
			update_option('octabook_sample_status','N');
			$_SESSION['oct_location'] =0;
			header("Refresh:0");
	}
	/***********************************************/
	
	
	
if(isset($_POST['location_title'])){		
	$location->location_title = filter_var($_POST['location_title'], FILTER_SANITIZE_STRING);
	$location->description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$location->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$location->phone = $_POST['phone'];
	$location->address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
	$location->city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
	$location->state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
	$location->image = $_POST['locationimage'];
	$location->zip = filter_var($_POST['zip'], FILTER_SANITIZE_STRING);
	$location->country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
	$servicecreate = $location->create();
}	

/* Get All Locations */
$location_sortby = get_option('octabook_location_sortby');
$oct_locations = $location->readAll('','','');
$all_locations = $location->countAll();
$temp_locatio_name = array();
$all_city_state_array = array();
foreach($oct_locations as $oct_location){ 
	if($location_sortby=='city'){$locationsort = $oct_location->city;}
	else{$locationsort = $oct_location->state;}
	$all_city_state_array[]=$locationsort;					 
}
?>
<div id="oct-locations-panel" class="panel tab-content table-fixed">
	<div class="oct-locations-list table-cell col-md-3 col-sm-3 col-xs-12 col-lg-3">
		<div class="oct-locations-container">
			<ul class="nav nav-tab nav-stacked oct-left-locations">
				<li class="active oct-left-location-menu-li br-2 getsorted_locations" data-location_sortby="all">
				<span class="oct-location-sort-icon"><i class="fa fa-th"></i></span>
					<a href="javascript:void(0);" data-toggle="pill">
						<span class="oct-location-name"><?php echo __("All States/City","oct");?> (<?php echo $all_locations; ?>)</span>
					</a>
				</li>
			</ul>	
			<ul class="nav nav-tab nav-stacked oct-left-location" id="sortable-city-state">
				<?php foreach($oct_locations as $oct_location){ 
					if($location_sortby=='city'){ $locationsort = $oct_location->city;}else{ $locationsort = $oct_location->state;}
					
					if(!in_array($locationsort,$temp_locatio_name)){
						$temp_locatio_name[]=$locationsort;
						
						 $city_state_locations = array_count_values($all_city_state_array);
				
					?>
				<li class="active oct-left-location-menu-li br-2 getsorted_locations" data-location_sortby="<?php if($location_sortby=='city'){ echo $oct_location->city;}else{ echo $oct_location->state;} ?>" >
				<span class="oct-location-sort-icon"><i class="fa fa-th-list"></i></span>
					<a href="javascript:void(0);" data-toggle="pill">
						<span class="oct-location-name"><?php if($location_sortby=='city'){ echo $oct_location->city;}else{ echo $oct_location->state;} ?> (<?php echo $city_state_locations[$locationsort]; ?>)</span>
					</a>
				</li>	
			<?php } } ?>	
			</ul>
		</div>	
	</div>
	<div class="panel-body table-cell col-md-9 col-sm-9 col-xs-12 col-lg-9">
		<div class="oct-location-details tab-content col-md-12 col-sm-12 col-lg-12 col-xs-12">
			<!-- right side common menu for location -->
			<div class="oct-location-top-header">
				<span class="oct-location-name pull-left"></span>
				<div class="pull-right">
					<table>
						<tbody>
							<tr>
								<td>
									<button id="oct-add-new-location" class="btn btn-success" value="Add New Location"><i class="fa fa-plus custom-icon-space"></i><?php echo __("Add New Location","oct");?></button>
								</td>
								
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id="hr"></div>
			<div class="tab-pane active" id=""><!-- locations list -->
				<div class="tab-content oct-locations-details">
					<div id="accordion" class="panel-group">
						<ul class="nav nav-tab nav-stacked sortable-locations" id="sortable-locations" > <!-- sortable-locations -->
						<?php foreach($oct_locations as $oct_location){ 
							$staff->location_id = $oct_location->id;
							$staffcounts = $staff->total_location_providers();
							?>
							<li id="location_detail_<?php echo $oct_location->id; ?>"><div class="panel panel-default oct-location-panel" >
								<div class="panel-heading">
									<h4 class="panel-title">
										<div class="oct-col9">
											<i class="fa fa-th-list"></i>
											<span class="oct-location-title-name"><?php echo $oct_location->location_title; ?></span>
										</div>
										<div class="pull-right oct-col3">
												
											<div class="oct-col6">
												<label for="location-list-<?php echo $oct_location->id; ?>">
													<input type="checkbox" data-id="<?php echo $oct_location->id; ?>" class="update_location_status" <?php if($oct_location->status=='E'){echo 'checked'; } ?> id="location-list-<?php echo $oct_location->id; ?>" data-toggle="toggle" data-size="small" data-on="<?php echo __("Enable","oct");?>" data-off="<?php echo __("Disable","oct");?>" data-onstyle="success" data-offstyle="danger" >
												</label>
											</div>
											<div class="pull-right">
												<div class="oct-col2 p-r">
													<a data-poid="oct-popover-location<?php echo $oct_location->id; ?>" id="oct-delete-location<?php echo $oct_location->id; ?>" class="pull-right btn-circle btn-danger btn-sm oct-delete-popover" rel="popover" data-placement='bottom' title="<?php echo __("Delete this location?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Delete location","oct");?>"></i></a>
													<div class="oct-popover" id="oct-popover-location<?php echo $oct_location->id; ?>" style="display: none;">
														<div class="arrow"></div>
															<table class="form-horizontal" cellspacing="0">
																<tbody>
																	<tr>
																		<td>
																		<?php if($staffcounts>0){?>
																			<span class="oct-popover-title"><?php echo __("Unable to delete location,having linked staff","oct");?></span>
																			<?php }else{?>				
																			<button data-id="<?php echo $oct_location->id; ?>" value="Delete" class="btn btn-danger btn-sm mr-10 delete_location" type="submit"><?php echo __("Yes","oct");?></button>
																			<button data-poid="oct-popover-location<?php echo $oct_location->id; ?>" class="btn btn-default btn-sm oct-close-popover-delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button><?php } ?>
																		</td>
																	</tr>
																</tbody>
															</table>
													</div>
												</div>
												
											<div class="oct-show-hide pull-right">
													<input type="checkbox" name="oct-show-hide" class="oct-show-hide-checkbox" id="<?php echo $oct_location->id; ?>" >
													<label class="oct-show-hide-label" for="<?php echo $oct_location->id; ?>"></label>
												</div>
											</div>
										</div>
										
									</h4>
								</div>
								<div id="" class="location_detail panel-collapse collapse detail-id_<?php echo $oct_location->id; ?>">
									<div class="panel-body">
										<div class="oct-location-collapse-div col-sm-12 col-md-7 col-lg-7 col-xs-12">
											<form id="oct_update_location_<?php echo $oct_location->id;?>" method="post" type="" class="slide-toggle oct_update_location" >
												<table class="oct-create-location-table form-group-margin">
													<tbody>

														<tr>
															<td><label for="oct-location-name"><?php echo __("Location Title","oct");?></label></td>
															<td><div class="form-group"><input type="text" class="form-control" id="oct-location-name<?php echo $oct_location->id; ?>" value="<?php echo $oct_location->location_title; ?>" name="location_title" />
															</div>
															<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location title is used to display in frontend for bookings.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
															</td>
															
														</tr>
														
														<tr>
															<td><label for="oct-location-desc"><?php echo __("Description","oct");?></label></td>
															<td><div class="form-group">
															<textarea id="oct-location-desc<?php echo $oct_location->id; ?>" class="form-control"><?php echo $oct_location->description; ?></textarea>
															</div>
															<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location description is used for desribe about location.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
															</td>
														</tr>
														
														<tr>
															<td><label for="oct-location-image"><?php echo __("Image","oct");?></label></td>
															<td>
																<div class="oct-location-image-uploader">
																	<img id="bdll<?php echo $oct_location->id; ?>locimage" src="<?php if($oct_location->image==''){ echo $plugin_url_for_ajax.'/assets/images/location.png';}else{
																	echo site_url()."/wp-content/uploads".$oct_location->image;
																	}?>" class="oct-location-image br-100" height="100" width="100">
																	<label <?php if($oct_location->image==''){ echo "style='display:block'"; }else{ echo "style='display:none'"; } ?> for="oct-upload-imagebdll<?php echo $oct_location->id; ?>" class="oct-location-img-icon-label show_image_icon_add<?php echo $oct_location->id; ?>">
																		<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
																		<i class="pull-left fa fa-plus-circle fa-2x"></i>
																	</label>
																	<input data-us="bdll<?php echo $oct_location->id; ?>" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdll<?php echo $oct_location->id; ?>"  />
																	
																	<a id="oct-remove-location-imagebdll<?php echo $oct_location->id; ?>" <?php if($oct_location->image!=''){ echo "style='display:block;'";}  ?> class="pull-left br-4 btn-danger oct-remove-location-img btn-xs oct_remove_image" rel="popover" data-placement='bottom' title="Remove Image?"> <i class="fa fa-trash" title="Remove location Image"></i></a>
																	<a class="oct-tooltip-link image_tooltip_info" href="#" data-toggle="tooltip" title="<?php echo __("Use Image less than 2MB","oct");?>"><i class="fas fa-info-circle fa-lg "></i></a>
																	
																	<div style="display: none;" class="oct-popover " id="popover-oct-remove-location-imagebdll<?php echo $oct_location->id; ?>">
																		<div class="arrow"></div>
																		<table class="form-horizontal" cellspacing="0">
																			<tbody>
																				<tr>
																					<td>
																						<a href="javascript:void(0)" id="" value="Delete" data-mediaid="<?php echo $oct_location->id; ?>" data-mediasection='location' data-mediapath="<?php echo $oct_location->image;?>" data-imgfieldid="bdll<?php echo $oct_location->id;?>uploadedimg"
																						class="btn btn-danger btn-sm oct_delete_image"><?php echo __("Yes","oct");?></a>
																						<a href="javascript:void(0)" id="popover-oct-remove-location-imagebdll<?php echo $oct_location->id; ?>" class="btn btn-default btn-sm close_delete_popup" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</div>	
															
															<div id="oct-image-upload-popupbdll<?php echo $oct_location->id; ?>" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
																<div class="vertical-alignment-helper">
																	<div class="modal-dialog modal-md vertical-align-center">
																		<div class="modal-content">
																			<div class="modal-header">
																				<div class="col-md-12 col-xs-12">
																					<a data-us="bdll<?php echo $oct_location->id; ?>" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdll<?php echo $oct_location->id; ?>" ><?php echo __("Crop & Save","oct");?></a>
																					<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
																				</div>	
																			</div>
																			<div class="modal-body">
																				<img id="oct-preview-imgbdll<?php echo $oct_location->id; ?>" />
																			</div>
																			<div class="modal-footer">
																				<div class="col-md-12 np">
																					<div class="col-md-4 col-xs-12">
																						<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdll<?php echo $oct_location->id; ?>filesize" name="filesize" />
																					</div>	
																					<div class="col-md-4 col-xs-12">	
																						<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdll<?php echo $oct_location->id; ?>h" name="h" /> 
																					</div>
																					<div class="col-md-4 col-xs-12">	
																						<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdll<?php echo $oct_location->id; ?>w" name="w" />
																					</div>
																					<input type="hidden" id="bdll<?php echo $oct_location->id; ?>x1" name="x1" />
																					 <input type="hidden" id="bdll<?php echo $oct_location->id; ?>y1" name="y1" />
																					<input type="hidden" id="bdll<?php echo $oct_location->id; ?>x2" name="x2" />
																					<input type="hidden" id="bdll<?php echo $oct_location->id; ?>y2" name="y2" />
																					<input id="bdll<?php echo $oct_location->id; ?>bdimagetype" type="hidden" name="bdimagetype"/>
																					<input type="hidden" id="bdll<?php echo $oct_location->id; ?>bdimagename" name="bdimagename" value="" />
																					</div>
																			</div>							
																		</div>		
																	</div>			
																</div>			
															</div>
															</td>
														<input name="image" id="bdll<?php echo $oct_location->id;?>uploadedimg" type="hidden" value="<?php echo $oct_location->image; ?>" />
													
														</tr>
																							
														<tr>
															<td><label for="location-email<?php echo $oct_location->id; ?>"><?php echo __("Email","oct");?></label></td>
															<td><div class="form-group"><input type="email" class="form-control" id="location-email<?php echo $oct_location->id; ?>" name="email" value="<?php echo $oct_location->email; ?>"/>
															</div>
															<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location email is used for to identify your location for business.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
															
															</td>
														</tr>
														<tr>
															<td><label for="location-phone-number<?php echo $oct_location->id; ?>"><?php echo __("Phone","oct");?></label></td>
															<td><div class="form-group">
															<input type="tel" class="form-control" id="location-phone-number<?php echo $oct_location->id; ?>" name="phone" value="<?php echo $oct_location->phone; ?>" maxlength="10" />
															</div>
															<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location phone is used to find location easily.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
															</td>
														</tr>
														<tr>
														<td><?php echo __("Address","oct");?></td>
														<td>
															<div class="oct-col12"><textarea class="form-control" name="address" id="oct-location-address<?php echo $oct_location->id; ?>"><?php echo $oct_location->address; ?></textarea></div>
														</td>
													</tr>
													<tr>
														<td></td>
														<td>
															<div class="oct-col6 oct-w-50">
																<label><?php echo __("City","oct");?></label>
																<input type="text" class="form-control" id="oct-location-city<?php echo $oct_location->id; ?>" name="city" placeholder="City" value="<?php echo $oct_location->city; ?>" />
															</div>
															<div class="oct-col6 oct-w-50 float-right">
																<label><?php echo __("State","oct");?></label>
																<input type="text" class="form-control" id="oct-location-state<?php echo $oct_location->id; ?>" name="state" placeholder="State" value="<?php echo $oct_location->state; ?>" />
															</div>
														</td>
													</tr>
													<tr>
														<td></td>	
														<td>	
															<div class="oct-col6 oct-w-50">
																<label><?php echo __("Zip/Postal Code","oct");?></label>
																<input type="text" class="form-control" id="oct-location-zip<?php echo $oct_location->id; ?>" name="zip" placeholder="Zip" value="<?php echo $oct_location->zip; ?>" />
															</div>	
															<div class="oct-col6 oct-w-50 float-right">
																<label><?php echo __("Country","oct");?></label>
																<input type="text" class="form-control" id="oct-location-country<?php echo $oct_location->id; ?>" name="country" placeholder="Country" value="<?php echo $oct_location->country; ?>" />
															</div>	
															
														</td>
													</tr>
													</tbody>
												</table>
										</div>
										<?php /*<div class="col-sm-12 col-md-5 col-lg-5 col-xs-12">
											<div class="oct-location-map">
												<label><?php echo __("Map Location","oct");?></label>
												<input id="pac-input" class="controls" type="text" placeholder="Search Box">
												<div id="map"></div>
											</div>
										</div> */ ?>
										<div class="col-sm-12 col-md-7 col-lg-7 col-xs-12 mt-20 mb-20">		
											<a data-location_id="<?php echo $oct_location->id; ?>" name="" class="btn btn-success oct-btn-width col-sm-offset-2 update_location"><?php echo __("Save","oct");?></a>
											<button type="reset" class="btn btn-default  oct-btn-width ml-30"><?php echo __("Reset","oct");?></button>
										</div>	
										</form>
									</div>
								</div>
							</div>
							</li>
							<?php } ?>
														
							<li>
								<!-- add new service pop up -->
								<div class="panel panel-default oct-location-panel oct-add-new-location">
									<div class="panel-heading">
										<h4 class="panel-title">
											<div class="oct-col9">
												<span class="oct-location-title-name"><?php echo __("Add New Location","oct");?></span>
											</div>
											<div class="pull-right oct-col3">				
												<div class="pull-right">
													<div class="oct-show-hide pull-right">
														<input type="checkbox" name="oct-show-hide" checked="checked" class="oct-show-hide-checkbox" id="ladd" ><!--Added Serivce Id-->
														<label class="oct-show-hide-label" for="ladd"></label>
													</div>
												</div>
											</div>											
										</h4>
									</div>
									<div id="" class="location_detail panel-collapse collapse in detail_sp3 detail-id_ladd">
										<div class="panel-body">
										<form id="oct_create_location_cl" action="" method="post" class="slide-toggle" >
											<div class="oct-location-collapse-div col-sm-12 col-md-7 col-lg-7 col-xs-12">
													<table class="oct-create-location-table form-group-margin">
														<tbody>

															<tr>
																<td><label for="oct-location-name"><?php echo __("Location Title","oct");?></label></td>
																<td><div class="form-group">
																<input type="text" name="location_title" class="form-control" id="oct-location-name" />
																</div>
																<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location title is used to display in frontend for bookings.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
																</td>
															</tr>
															
															<tr>
																<td><label for="oct-location-desc"><?php echo __("Description","oct");?></label></td>
																<td>
																<div class="form-group">
																<textarea name="description" id="oct-location-desc" class="form-control"></textarea>
																</div>
																<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location description is used for desribe about location.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
																</td>
															</tr>
															<tr>
															<td><label for="oct-location-image"><?php echo __("Image","oct");?></label></td>
															<td>
																<div class="oct-location-image-uploader">
																	<img id="bdcllocimage" src="<?php echo $plugin_url_for_ajax; ?>/assets/images/location.png" class="oct-location-image br-100" height="100" width="100">
																	<label for="oct-upload-imagebdcl" class="oct-location-img-icon-label">
																		<i class="oct-camera-icon-common br-100 fa fa-camera"></i>
																		<i class="pull-left fa fa-plus-circle fa-2x"></i>
																	</label>
																		
																	<input data-us="bdcl" class="hide oct-upload-images" type="file" name="" id="oct-upload-imagebdcl"  />
																	
																	<a id="oct-remove-location-imagebdcl" class="pull-left br-100 btn-danger oct-remove-location-img btn-xs" rel="popover" data-placement='bottom' title="<?php echo __("Remove Image?","oct");?>"> <i class="fa fa-trash" title="<?php echo __("Remove location Image","oct");?>"></i></a>
																	<a class="oct-tooltip-link image_tooltip_info" href="#" data-toggle="tooltip" title="<?php echo __("Use Image less than 2MB","oct");?>"><i class="fas fa-info-circle fa-lg "></i></a>
																	<div id="popover-oct-remove-location-imagebdcl" style="display: none;">
																		<div class="arrow"></div>
																		<table class="form-horizontal" cellspacing="0">
																			<tbody>
																				<tr>
																					<td>
																						<a href="javascript:void(0)" id="" value="Delete" class="btn btn-danger btn-sm" type="submit"><?php echo __("Yes","oct");?></a>
																						<a href="javascript:void(0)" id="oct-close-popover-location-imagebdcl" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div><!-- end pop up -->
																</div>	
													<div id="oct-image-upload-popupbdcl" class="oct-image-upload-popup modal fade" tabindex="-1" role="dialog">
														<div class="vertical-alignment-helper">
															<div class="modal-dialog modal-md vertical-align-center">
																<div class="modal-content">
																	<div class="modal-header">
																		<div class="col-md-12 col-xs-12">
																			<a data-us="bdcl" class="btn btn-success oct_upload_img" data-imageinputid="oct-upload-imagebdcl"><?php echo __("Crop & Save","oct");?></a>
																			<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo __("Cancel","oct");?></button>
																		</div>	
																	</div>
																	<div class="modal-body">
																		<img id="oct-preview-imgbdcl" />
																	</div>
																	<div class="modal-footer">
																		<div class="col-md-12 np">
																			<div class="col-md-4 col-xs-12">
																				<label class="pull-left"><?php echo __("File size","oct");?></label> <input type="text" class="form-control" id="bdclfilesize" name="filesize" />
																			</div>	
																			<div class="col-md-4 col-xs-12">	
																				<label class="pull-left"><?php echo __("H","oct");?></label> <input type="text" class="form-control" id="bdclh" name="h" /> 
																			</div>
																			<div class="col-md-4 col-xs-12">	
																				<label class="pull-left"><?php echo __("W","oct");?></label> <input type="text" class="form-control" id="bdclw" name="w" />
																			</div>
																			<input type="hidden" id="bdclx1" name="x1" />
																								 <input type="hidden" id="bdcly1" name="y1" />
																								<input type="hidden" id="bdclx2" name="x2" />
																								<input type="hidden" id="bdcly2" name="y2" />
																								<input id="bdclbdimagetype" type="hidden" name="bdimagetype"/>
																								<input type="hidden" id="bdclbdimagename" name="bdimagename" value="" />
																		</div>
																	</div>							
																</div>		
															</div>			
														</div>			
													</div>
														</td>
													<input name="locationimage" id="bdcluploadedimg" type="hidden" value="" />
													</tr>
															<tr>
																<td><label for="location-email"><?php echo __("Email","oct");?></label></td>
																<td><div class="form-group">
																<input type="email" class="form-control" id="location-email" name="email" />
																</div>
																<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location email is used for to identify your location for business.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
																</td>
															</tr>
															<tr>
																<td><label for="location-phone-number"><?php echo __("Phone","oct");?></label></td>
																<td>
																<div class="form-group">
																<input type="tel" class="form-control" id="location-phone-number" name="phone" />
																</div>
																<a class="oct-tooltip-link" href="#" data-toggle="tooltip" title="<?php echo __("Location phone is used to find location easily.","oct");?>"><i class="fas fa-info-circle fa-lg"></i></a>
																</td>
															</tr>
															<tr>
															<td><?php echo __("Address","oct");?></td>
															<td>
																<div class="oct-col12"><textarea class="form-control" name="address"></textarea></div>
															</td>
														</tr>
														<tr>
															<td></td>
															<td>
																<div class="oct-col6 oct-w-50">
																	<label><?php echo __("City","oct");?></label>
																	<input type="text" class="form-control" id="" name="city" placeholder="<?php echo __("City","oct");?>" />
																</div>
																<div class="oct-col6 oct-w-50 float-right">
																	<label><?php echo __("State","oct");?></label>
																	<input type="text" class="form-control" id="" name="state" placeholder="<?php echo __("State","oct");?>" />
																</div>
															</td>
														</tr>
														<tr>
															<td></td>	
															<td>	
																<div class="oct-col6 oct-w-50">
																	<label><?php echo __("Zip/Postal Code","oct");?></label>
																	<input type="text" class="form-control" id="" name="zip" placeholder="<?php echo __("Zip/Postal Code","oct");?>" />
																</div>	
																<div class="oct-col6 oct-w-50 float-right">
																	<label><?php echo __("Country","oct");?></label>
																	<input type="text" class="form-control" id="" name="country" placeholder="<?php echo __("Country","oct");?>" />
																</div>	
																
															</td>
														</tr>
														</tbody>
													</table>
												
											</div>
											<?php /*<div class="col-sm-12 col-md-5 col-lg-5 col-xs-12">
												<div class="oct-location-map">
													<label><?php echo __("Map Location","oct");?></label>
													<input id="pac-input" class="controls" type="text" placeholder="Search Box">
													<div id="map"></div>
												</div>
											</div> */ ?>	
											<div class="col-sm-12 col-md-7 col-lg-7 col-xs-12 mt-20 mb-20">		
												<a href="javascript:void(0)" data-location_id="cl" id="oct_create_location" name="oct_create_location" class="btn btn-success oct-btn-width col-sm-offset-2"><?php echo __("Save","oct");?></a>
												<button type="reset" class="btn btn-default  oct-btn-width ml-30"><?php echo __("Reset","oct");?></button>
											</div>	
											
										</form>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
	include(dirname(__FILE__).'/footer.php');
?>
<script>
	var locationObj={"plugin_path":"<?php echo $plugin_url_for_ajax;?>"}
</script>