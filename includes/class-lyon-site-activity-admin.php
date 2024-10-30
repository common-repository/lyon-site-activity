<?php
	// Block direct access to this file.
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @since      1.0.0
	 * @package    lyon-site-activity
	 * @subpackage lyon-site-activity/includes
	 * @author     Wheaton College <web@wheatoncollege.edu>
	 */
	class Lyon_Site_Activity_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;

		/**
		 * Give us access to the class that holds all the SQL requests
		 *
		 * @access  private
		 */
		private $lyon_site_activity_sql;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 *
		 * @param      string $plugin_name The name of this plugin.
		 * @param      string $version The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {
			$this->plugin_name = $plugin_name;
			$this->version     = $version;
		}

		/**
		 * Adds a menu item under the "Settings" folder in the admin view
		 */
		public function add_settings_page() {
			add_options_page( __( 'Site Activity' ), __( 'Site Activity' ), 'manage_options', 'site-activity-settings', array( $this, 'site_activity_settings_callback' ) );
		}

		public function site_activity_settings_callback() {
			// Enqueue styles and scripts specific to this page only.  We don't need to load them everywhere in the admin.
			wp_enqueue_style( 'lsa_styles', dirname( plugins_url( '', __FILE__ ) ) . '/assets/styles.css', array(), $this->version, 'screen' );
			wp_enqueue_script( 'lsa_scripts', dirname( plugins_url( '', __FILE__ ) ) . '/assets/scripts.js', array( 'jquery' ), $this->version, true );
			?>
            <div class="wrap" id="site-activity-settings">
                <form action='options.php' method='post'>
                    <h1>Lyon Site Activity Settings</h1>
					<?php


						if ( function_exists('wp_nonce_field') )
							wp_nonce_field('plugin-name-action_' . "yep");
						settings_fields( 'lsa_plugin_options' );
						do_settings_sections( 'site-activity-settings' );
						submit_button();
					?>
                </form>
            </div>
			<?php
		}

		public function settings_init(  ) {
			$args = array( 'sanitize_callback' => array( $this, 'lsa_plugin_options_validate' ) );
			register_setting( 'lsa_plugin_options', 'lsa_plugin_options', $args );
			add_settings_section( 'lsa_main_section', __( 'Post Types and Taxonomies to Monitor' ), array( $this, 'lsa_main_section_callback' ), 'site-activity-settings' );
			add_settings_field( 'lsa_post_tax_types', '', array( $this, 'lsa_post_tax_types_render' ), 'site-activity-settings', 'lsa_main_section' );
		}

		public function lsa_main_section_callback() {
			echo __( 'Check off the post types and taxonomies you wish to monitor.  "Latest" will show you the latest posts created, "Last Modified" will show you which items were recently modified, and "Trashed" will show which items have been recently trashed.' );
		}

		public function lsa_post_tax_types_render() {
			$options = get_option( 'lsa_plugin_options', LSA_DEFAULT_OPTIONS );
			if( empty( $options ) ) {
				$options = LSA_DEFAULT_OPTIONS;
			}

			$use_built_in_post_types = array( 'post', 'page' );
			$use_built_in_taxonomies = array( 'category', 'post_tag' );
			$ignore_custom_post_types = array( 'wp_router_page' );

			$post_tax_types = array();
			$post_tax_types['Post Types'] = get_post_types( array( '_builtin' => true ), 'objects' );
			foreach( $post_tax_types['Post Types'] as $type => $obj ) {
				if ( ! in_array( $type, $use_built_in_post_types ) ) {
					unset( $post_tax_types['Post Types'][$type] );
				}
			}
			$post_tax_types['Custom Post Types'] = get_post_types( array( '_builtin' => false ), 'objects' );
			foreach( $post_tax_types['Custom Post Types'] as $type => $obj ) {
				if ( in_array( $type, $ignore_custom_post_types ) ) {
					unset( $post_tax_types['Custom Post Types'][$type] );
				}
			}
			$post_tax_types['Taxonomies'] = get_taxonomies( array( '_builtin' => true ), 'objects' );
			foreach( $post_tax_types['Taxonomies'] as $type => $obj ) {
				if ( ! in_array( $type, $use_built_in_taxonomies ) ) {
					unset( $post_tax_types['Taxonomies'][$type] );
				}
			}
			$post_tax_types['Custom Taxonomies'] = get_taxonomies( array( '_builtin' => false ), 'objects' );
			$post_tax_types['Attachments']['pdf'] = (object) array( 'name' => 'pdf', 'label' => 'PDFs', 'mime_type' => 'application/pdf' );

			foreach( $post_tax_types as $label => $post_tax_type ) {
				if ( ! empty( $post_tax_type ) ) {
					?>
                    <ul class="lsa_cpt_list">
                        <h2><?php echo $label; ?></h2>

						<?php
							foreach ( $post_tax_type as $post_tax ) {
								// Prevent warning for in_array function if this variable is null
								if ( null === $options['lsa_post_tax_types'][ $label ][ $post_tax->name ][ $post_tax->label ] ) {
									$options['lsa_post_tax_types'][ $label ][ $post_tax->name ][ $post_tax->label ] = array();
								}
								?>
                                <li>
                                    <input value="<?php echo $post_tax->name; ?>" type="checkbox" name="lsa_plugin_options[lsa_post_tax_types][<?php echo $label; ?>][<?php echo $post_tax->name; ?>]" id="<?php echo $post_tax->name; ?>">
                                    <label for="<?php echo $post_tax->name; ?>"><?php echo $post_tax->label; ?> (<?php echo ( 'Attachments' === $label ? $post_tax->mime_type : $post_tax->name ); ?>)</label>
                                    <ul>
                                        <li>
											<?php if( 'Attachments' === $label ) { ?>
                                                <input type="checkbox" class="subOption" name="lsa_plugin_options[lsa_post_tax_types][<?php echo $label; ?>][<?php echo $post_tax->name; ?>][<?php echo $post_tax->label; ?>][]" value="<?php echo $post_tax->mime_type; ?>" id="<?php echo $post_tax->name; ?>_new"<?php if ( in_array( $post_tax->mime_type, $options['lsa_post_tax_types'][$label][$post_tax->name][$post_tax->label] ) ) { echo ' checked=checked'; } ?>>
											<?php } else { ?>
                                                <input type="checkbox" class="subOption" name="lsa_plugin_options[lsa_post_tax_types][<?php echo $label; ?>][<?php echo $post_tax->name; ?>][<?php echo $post_tax->label; ?>][]" value="new" id="<?php echo $post_tax->name; ?>_new"<?php if ( in_array( 'new', $options['lsa_post_tax_types'][$label][$post_tax->name][$post_tax->label] ) ) { echo ' checked=checked'; } ?>>
											<?php } ?>
                                            <label for="<?php echo $post_tax->name; ?>_new">Latest</label>
                                        </li>
										<?php if( $label !== 'Taxonomies' && $label !== 'Custom Taxonomies' && $label !== 'Attachments' ) { ?>
                                            <li>
                                                <input type="checkbox" class="subOption" name="lsa_plugin_options[lsa_post_tax_types][<?php echo $label; ?>][<?php echo $post_tax->name; ?>][<?php echo $post_tax->label; ?>][]" value="modify" id="<?php echo $post_tax->name; ?>_modified"<?php if ( in_array( 'modify', $options['lsa_post_tax_types'][$label][$post_tax->name][$post_tax->label] ) ) {
													echo ' checked=checked';
												} ?>>
                                                <label for="<?php echo $post_tax->name; ?>_modified">Last Modified</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" class="subOption" name="lsa_plugin_options[lsa_post_tax_types][<?php echo $label; ?>][<?php echo $post_tax->name; ?>][<?php echo $post_tax->label; ?>][]" value="trash" id="<?php echo $post_tax->name; ?>_trash"<?php if ( in_array( 'trash', $options['lsa_post_tax_types'][$label][$post_tax->name][$post_tax->label] ) ) {
													echo ' checked=checked';
												} ?>>
                                                <label for="<?php echo $post_tax->name; ?>_trash">Trashed</label>
                                            </li>
										<?php } ?>
                                    </ul>
                                </li>
							<?php } ?>
                    </ul>
					<?php
				}
			}
			?>
            <div style="flex: 1 0 100%; text-align: right;" class="version">Lyon Site Activity<br />Version: <?php echo $this->version; ?></div>
			<?php
        }

		// Validate user data for some/all of your input fields
		public function lsa_plugin_options_validate( $input ) {
			return $input; // return validated input
		}

		/**
		 * Function to add a user friendly link to the plugins page under this plugin to take them directly to the
		 * Site Activity page we are creating via this plugin.
		 *
		 * @param $links
		 *
		 * @return mixed
		 */
		public function add_action_links( $links ) {
			$new_links = array(
				'<a href="' . admin_url( 'tools.php?page=site-activity' ) . '">Site Activity</a>',
			);

			return array_merge( $links, $new_links );
		}

		/**
		 * Adds a menu item under the "Tools" folder in the admin view
		 */
		public function add_site_activity_tool() {
			add_management_page( __( 'Site Activity' ), __( 'Site Activity' ), 'activate_plugins', 'site-activity', array( $this, 'site_activity_tools_callback' ) );
		}

		/**
		 * Outputs the tables and other data to the page under the "Tools" folder in the admin view
		 */
		public function site_activity_tools_callback() {
			// Enqueue styles and scripts specific to this page only.  We don't need to load them everywhere in the admin.
			wp_enqueue_style( 'lsa_styles', dirname( plugins_url( '', __FILE__ ) ) . '/assets/styles.css', array(), $this->version, 'screen' );
			wp_enqueue_script( 'lsa_scripts', dirname( plugins_url( '', __FILE__ ) ) . '/assets/scripts.js', array( 'jquery' ), $this->version, true );
			?>

            <div class="wrap">
                <h1>Site Activity</h1>
				<?php echo $this->show_navigation(); ?>
				<?php echo $this->show_tables(); ?>
                <div style="text-align: right;" class="version">Lyon Site Activity<br />Version: <?php echo $this->version; ?></div>
            </div>

			<?php
		}

		/**
		 * Will display all the tables and their data that were selected to be displayed via the settings page.  Utilizes
		 * WordPress' built in WP_List_Table class to format the data and allow for further customization
		 */
		private function show_tables() {
			// Pull in all the SQL information here, so it only runs when this page is viewed, and not every time an
			// admin page is loaded.
			if( ! class_exists( 'Lyon_Site_Activity_SQL' ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'class-lyon-site-activity-sql.php';
			}
			// Set a variable to make accessing the class arguments easier
			$this->lyon_site_activity_sql = new Lyon_Site_Activity_SQL();

			// Our class extends the WP_List_Table class, so we need to make sure that it's there
			if( ! class_exists( 'WP_List_Table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			}
			// Finally, include our own extension of the WP_List_Table class.
			if( ! class_exists( 'Link_List_Table' ) ) {
				require_once( dirname( __FILE__ ) . '/class-lyon-site-activity-list-table.php' );
			}
			//Prepare Table of elements
			$wp_list_table = new Link_List_Table();

			// Get the plugin options, so we know what to show
			$options = get_option( 'lsa_plugin_options', LSA_DEFAULT_OPTIONS )['lsa_post_tax_types'];
			if( empty( $options ) ) {
				$options = LSA_DEFAULT_OPTIONS['lsa_post_tax_types'];
			}

			foreach( $options as $label => $post_tax_type ) {
				foreach( $post_tax_type as $post_type => $post_info ) {
					echo '<section id="' . $post_type . '">';
					if( $label === 'Taxonomies' || $label === 'Custom Taxonomies' ) {
						foreach ( $post_info as $post_type_label => $sections ) {
							$wp_list_table->table_title = 'Last 5 Added ' . $post_type_label;
							$wp_list_table->db_results  = $this->lyon_site_activity_sql->get_taxonomies( $post_type );
							$wp_list_table->prepare_items();
							$wp_list_table->display();
						}
					}

					if( $label === 'Post Types' || $label === 'Custom Post Types' ) {
						foreach ( $post_info as $post_type_label => $sections ) {
							if( in_array( 'new', $sections ) ) {
								$wp_list_table->table_title = 'Latest 10 ' . $post_type_label;
								$wp_list_table->db_results  = $this->lyon_site_activity_sql->get_latest_posts( $post_type );
								$wp_list_table->prepare_items();
								$wp_list_table->display();
							}
							if( in_array( 'modify', $sections ) ) {
								$wp_list_table->table_title = 'Last 10 Modified ' . $post_type_label;
								$wp_list_table->db_results  = $this->lyon_site_activity_sql->get_modified_posts( $post_type );
								$wp_list_table->prepare_items();
								$wp_list_table->display();
							}
							if( in_array( 'trash', $sections ) ) {
								$wp_list_table->table_title = 'Last 10 Trashed ' . $post_type_label;
								$wp_list_table->db_results  = $this->lyon_site_activity_sql->get_trashed_posts( $post_type );
								$wp_list_table->prepare_items();
								$wp_list_table->display();
							}
						}
					}

					if( $label === 'Attachments' ) {
						foreach ( $post_info as $post_type_label => $sections ) {
							$wp_list_table->table_title = 'Latest 10 ' . $post_type_label;
							$wp_list_table->db_results  = $this->lyon_site_activity_sql->get_latest_attachments( $sections[0] );
							$wp_list_table->prepare_items();
							$wp_list_table->display();
						}
					}

					echo '</section>';
				}
			}
		}

		/**
		 * Creates and displays the navigation elements of the page
		 * @return string
		 */
		private function show_navigation() {

			// Get the plugin options, so we know what to show
			$options = get_option( 'lsa_plugin_options', LSA_DEFAULT_OPTIONS )['lsa_post_tax_types'];
			if( empty( $options ) ) {
				$options = LSA_DEFAULT_OPTIONS['lsa_post_tax_types'];
			}

			$html = '<nav id="site-activity-navigation">';

			foreach( $options as $label => $post_tax_type ) {
				foreach ( $post_tax_type as $post_type => $post_info ) {
					$html .= '<a href="#' . $post_type . '">' . key( $post_info ) . '</a>';
				}
			}

			$html .= '</nav>';

			return $html;
		}

	}