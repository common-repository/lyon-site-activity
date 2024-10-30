<?php

	// Block direct access to this file.
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	/**
	 * Plugin Name: Lyon Site Activity
	 * Plugin URI: https://wordpress.org/plugins/lyon-site-activity/
	 * Description: A simple, lightweight plugin that gives site administrators an at-a-glance view of recent content edits.
	 * Version: 2.0.2
	 * Author: Wheaton College
	 * Author URI: http://wheatoncollege.edu
	 * License: GPLv3
	 */

	require_once plugin_dir_path( __FILE__ ) . 'includes/lyon_site_activity_constants.php';

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-lyon-site-activity-activator.php
	 */
	function activate_lyon_site_activity() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-lyon-site-activity-activator.php';
		lyon_site_activity_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-lyon-site-activity-deactivator.php
	 */
	function deactivate_lyon_site_activity() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-lyon-site-activity-deactivator.php';
		lyon_site_activity_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_lyon_site_activity' );
	register_deactivation_hook( __FILE__, 'deactivate_lyon_site_activity' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-lyon-site-activity.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_lyon_site_activity() {

		if( ! is_admin() ) {
			return;
		}

		$plugin = new lyon_site_activity();
		$plugin->run();

	}

	run_lyon_site_activity();