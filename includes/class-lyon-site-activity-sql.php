<?php

	// Block direct access to this file.
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	/**
	 * Stores all the SQL functions that will return the desired results
	 *
	 * @since      1.0.0
	 * @package    lyon-site-activity
	 * @subpackage lyon-site-activity/includes
	 * @author     Wheaton College <web@wheatoncollege.edu>
	 */
	class Lyon_Site_Activity_SQL {

		/**
		 * The array of actions registered with WordPress.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
		 */

		/**
		 * Initialize the collections used to maintain the actions and filters.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
		}

		public function get_taxonomies( $taxonomy ) {
			global $wpdb;

			return $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DISTINCT
				        CONCAT('<a target=\"_blank\" href=\"" . home_url() . "/wp-admin/term.php?taxonomy=',tt.taxonomy,'&tag_ID=',t.term_id,'\">', t.term_id,'</a>') AS 'Term ID'
	                    ,t.name
	                    ,t.slug
	                    ,tt.taxonomy
	                    ,tt.count    
	                FROM 
	                    {$wpdb->prefix}terms t    
	                JOIN
	                    {$wpdb->prefix}term_taxonomy tt
	                ON
	                    t.term_id = tt.term_id
	                WHERE 
	                    taxonomy = %s
	                ORDER BY
	                        t.term_id DESC
	                LIMIT 5;",
					$taxonomy
				)
			);
		}

		public function get_latest_attachments( $mime_type ) {
			global  $wpdb;

			return $wpdb->get_results(
				$wpdb->prepare(
					"SELECT CONCAT('<a target=\"_blank\" href=\"" . home_url() . "/?p=',p.ID,'\">', p.ID,'</a>') AS 'Post ID'
						,p.post_title
                    	,u.display_name AS user
                    	,DATE_FORMAT(p.post_modified,'%c/%e/%Y %h:%i %p') AS 'Post Modified'
	                    ,DATE_FORMAT(p.post_date,'%c/%e/%Y %h:%i %p') AS 'Post Date'
						,CONCAT('<a target=\"_blank\" href=\"" . home_url() . "/?p=',p.post_parent,'\">', p.post_parent,'</a>') AS 'Post Parent ID'
						,p.post_mime_type
					FROM
						{$wpdb->prefix}posts p
					JOIN
						{$wpdb->prefix}users u
					ON
						p.post_author = u.ID
					WHERE
						p.post_mime_type = %s
					AND
						p.post_type = 'attachment'
					AND
						p.post_date BETWEEN NOW()-INTERVAL 2 WEEK AND NOW()
					ORDER BY
						p.post_date DESC LIMIT 1000;",
					$mime_type
				)
			);
		}

		public function get_latest_posts( $post_type ) {
			global $wpdb;

			return $wpdb->get_results(
				$wpdb->prepare(
					"SELECT CONCAT('<a target=\"_blank\" href=\"" . home_url() . "/?p=',p.ID,'\">', p.ID,'</a>') AS 'Post ID'
                    	,p.post_title
                    	,u.display_name AS user
                    	,DATE_FORMAT(p.post_modified,'%c/%e/%Y %h:%i %p') AS 'Post Modified'
	                    ,DATE_FORMAT(p.post_date,'%c/%e/%Y %h:%i %p') AS 'Post Date'
  	                    ,p.post_status
	                FROM 
	                    {$wpdb->prefix}posts p 
	                JOIN
	                    {$wpdb->prefix}users u 
	                ON 
	                    p.post_author = u.ID	
	                WHERE 
	                    post_status NOT IN ('inherit','auto-draft','private','draft','trash')
	                AND
	                    post_type = %s
	                AND
	                    p.post_date BETWEEN NOW()-INTERVAL 2 WEEK AND NOW()	
	                ORDER BY 
	                    p.post_date DESC LIMIT 10;",
					$post_type
				)
			);
		}

		public function get_modified_posts( $post_type ) {
			global $wpdb;

			if( LSA_POST_REVISIONS ) {
				return $wpdb->get_results(
					$wpdb->prepare(
						"SELECT
	                    CONCAT('<a target=\"_blank\" href=\"" . home_url() . "/?p=',wp.ID,'\">', wp.ID,'</a>') AS 'Post ID'
	                    ,wp.post_title
	                    ,(	SELECT 
	                            u.display_name 
	                        FROM 
	                            {$wpdb->prefix}posts p
	                        JOIN
	                            {$wpdb->prefix}users u 
	                        ON 
	                            p.post_author = u.ID	
	                        WHERE 
	                            p.post_parent = wp.ID
	                        AND
	                            p.post_type = 'revision' 
	                        ORDER BY 
	                            p.post_modified 
	                        DESC LIMIT 1
	                    ) AS 'user'
	                    ,DATE_FORMAT(wp.post_modified,'%c/%e/%Y %h:%i %p') AS 'Post Modified'
	                    ,DATE_FORMAT(wp.post_date,'%c/%e/%Y %h:%i %p') AS 'Post Date'
	                    ,wp.post_status
	                FROM 
	                    {$wpdb->prefix}posts wp
	                WHERE 
	                    wp.post_status NOT IN ('inherit','auto-draft','private','draft','trash')
		            AND
		                wp.post_type = %s
		            AND
		                wp.post_modified BETWEEN NOW()-INTERVAL 2 WEEK AND NOW()
	                ORDER BY 
		                wp.post_modified DESC
	                LIMIT 10;",
						$post_type
					)
				);
			}else {
				return $wpdb->get_results(
					$wpdb->prepare(
						"SELECT
	                    CONCAT('<a target=\"_blank\" href=\"" . home_url() . "/?p=',wp.ID,'\">', wp.ID,'</a>') AS 'Post ID'
	                    ,wp.post_title
	                    ,DATE_FORMAT(wp.post_modified,'%c/%e/%Y %h:%i %p') AS 'Post Modified'
	                    ,DATE_FORMAT(wp.post_date,'%c/%e/%Y %h:%i %p') AS 'Post Date'
	                    ,wp.post_status
	                FROM 
	                    {$wpdb->prefix}posts wp
	                WHERE 
	                    wp.post_status NOT IN ('inherit','auto-draft','private','draft','trash')
		            AND
		                wp.post_type = %s
		            AND
		                wp.post_modified BETWEEN NOW()-INTERVAL 2 WEEK AND NOW()
	                ORDER BY 
		                wp.post_modified DESC
	                LIMIT 10;",
						$post_type
					)
				);
			}
		}

		public function get_trashed_posts( $post_type ) {
			global $wpdb;

			if( LSA_POST_REVISIONS ) {
				return $wpdb->get_results(
					$wpdb->prepare(
						"SELECT
	                    wp.ID AS 'Post ID'
	                    ,wp.post_title
	                    ,(	SELECT 
	                            u.display_name 
	                        FROM 
	                            {$wpdb->prefix}posts p
	                        JOIN
	                            {$wpdb->prefix}users u 
	                        ON 
	                            p.post_author = u.ID	
	                        WHERE 
	                            p.post_parent = wp.ID
	                        AND
	                            p.post_type = 'revision' 
	                        ORDER BY 
	                            p.post_modified 
	                        DESC LIMIT 1
	                    ) AS 'user'
	                    ,DATE_FORMAT(wp.post_modified,'%c/%e/%Y %h:%i %p') AS 'Post Modified'
	                    ,DATE_FORMAT(wp.post_date,'%c/%e/%Y %h:%i %p') AS 'Post Date'
	                FROM 
	                    {$wpdb->prefix}posts wp
	                WHERE 
	                    wp.post_status = 'trash'
	                AND
	                	wp.post_type = %s
	                ORDER BY 
	                    wp.post_modified DESC
	                LIMIT 10;",
						$post_type
					)
				);
			}else {
				return $wpdb->get_results(
					$wpdb->prepare(
						"SELECT
	                    wp.ID AS 'Post ID'
	                    ,wp.post_title
	                    ,DATE_FORMAT(wp.post_modified,'%c/%e/%Y %h:%i %p') AS 'Post Modified'
	                    ,DATE_FORMAT(wp.post_date,'%c/%e/%Y %h:%i %p') AS 'Post Date'
	                FROM 
	                    {$wpdb->prefix}posts wp
	                WHERE 
	                    wp.post_status = 'trash'
	                AND
	                	wp.post_type = %s
	                ORDER BY 
	                    wp.post_modified DESC
	                LIMIT 10;",
						$post_type
					)
				);
			}
		}
	}