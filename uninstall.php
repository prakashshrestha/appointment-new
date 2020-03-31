<?php 

	/* if uninstall not called from WordPress exit */
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit;
	}

	include_once( 'objects/class_uninstall.php' );
	$uninstaller = new octabook_uninstall();
	
	/* remove ak wordpress roles */
	$uninstaller->remove_oct_roles();
	
	/* remove ak tables */
	$uninstaller->remove_oct_mysql_tables();
	
	/* remove ak wp options */
	$uninstaller->remove_oct_wp_options();
	
	/* remove ak wp Pages */
	$uninstaller->remove_oct_wp_pages();
	
	
?>