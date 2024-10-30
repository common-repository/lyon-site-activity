<?php

	// Block direct access to this file.
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	/**
	 * Define the internationalization functionality
	 *
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @link       https://bitbucket.org/WheatonCollegeMA/lyon_site_activity
	 * @since      1.0.0
	 *
	 * @package    lyon-site-activity
	 * @subpackage lyon-site-activity/includes
	 */

	/**
	 * Define the internationalization functionality.
	 *
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @since      1.0.0
	 * @package    lyon-site-activity
	 * @subpackage lyon-site-activity/includes
	 * @author     Wheaton College <web@wheatoncollege.edu>
	 */
	class Lyon_Site_Activity_i18n {


		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				'lyon-site-activity',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);

		}


	}
