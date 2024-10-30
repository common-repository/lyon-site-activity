<?php
	// die when the file is called directly
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		die;
	}
	// Define a variable and store an option name as the value
	$option_name = 'lsa_plugin_options';
	// Delete the option
	delete_option( $option_name );
	// Delete the option if in a multisite environment
	delete_site_option( $option_name );
