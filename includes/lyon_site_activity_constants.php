<?php

	// Block direct access to this file.
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	/**
	 * These are some constants created to make changing out specific variables easier on the admin.
	 *
	 * @link       https://bitbucket.org/WheatonCollegeMA/lyon_site_activity
	 * @since      1.0.0
	 *
	 * @package    lyon-site-activity
	 * @subpackage lyon-site-activity/includes
	 */

	if( defined( 'WP_POST_REVISIONS' ) && ( WP_POST_REVISIONS === true || intval( WP_POST_REVISIONS ) > 0 ) ) {
		define( 'LSA_POST_REVISIONS', TRUE );
	}else {
		define( 'LSA_POST_REVISIONS', FALSE );
	}

	define( 'LSA_DEFAULT_OPTIONS', array( "lsa_post_tax_types" => array( "Post Types" => array( "post" => array( "Posts" => array( "new", "modify", "trash" ) ), "page" => array( "Pages" => array( "new", "modify", "trash" ) ) ) ) ) );