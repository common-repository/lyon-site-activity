<?php

	class Link_List_Table extends WP_List_Table {

		/**
		 * @var $db_results Object of results from $wpdb->get_results();
		 */
		public $db_results;

		/**
		 * @var $columns Array to hold the columns internally
		 */
		private $columns;

		/**
		 * @var $table_before String to include title of the form
		 */
		public $table_title;


		/**
		 * Constructor, we override the parent to pass our own arguments
		 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
		 */
		function __construct() {

			parent::__construct( array(
				'singular' => 'wp_list_test_link', //Singular label
				'plural'   => 'wp_list_test_links', //plural label, also this well be one of the table css class
				'ajax'     => false //We won't support Ajax for this table
			) );

		}

		/**
		 * Add extra markup in the toolbars before or after the list
		 *
		 * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
		 */
		function extra_tablenav( $which ) {

			if ( $which == "top" ) {
				//The code that goes before the table is here
				echo "<h3>$this->table_title</h3>";
			}
			if ( $which == "bottom" ) {
				//The code that goes after the table is there
				echo "";
			}

		}

		/**
		 * Define the columns that are going to be used in the table
		 *
		 * @return array $columns, the array of columns to use with the table
		 */
		function get_columns() {
			return $columns = $this->columns;
		}

		/**
		 * Prepare the table with different parameters, pagination, columns and table elements
		 */
		function prepare_items() {
			$this->items = $this->db_results;
			$this->_column_headers = array();

			if ( ! is_array( $this->db_results ) || empty( $this->db_results ) ) {
				return false;
			}
			// Get the table header cells by formatting first row's keys
			$this->columns = array();
			$keys = get_object_vars( $this->db_results[0] );
			foreach ( $keys as $index => $row_key ) {
				$this->columns[ $index ] = ucwords( str_replace( '_', ' ', $index ) ); // capitalise and convert underscores to spaces
			}
			$data = array();
			foreach ( $this->db_results as $row ) {
				$data[] = get_object_vars( $row );
			}
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );
		}

		/**
		 * Display the rows of records in the table
		 * @return string, echo the markup of the rows
		 */
		function display_rows() {

			//Get the records registered in the prepare_items method
			$records = $this->items;
			//Get the columns registered in the get_columns and get_sortable_columns methods
			list( $columns, $hidden ) = $this->get_column_info();
			//Loop for each record
			if ( ! empty( $records ) ) {
				foreach ( $records as $rec ) {
					//Open the line
					echo '<tr>';
					foreach ( $columns as $column_name => $column_display_name ) {
						//Style attributes for each col
						$class = "class='$column_name column-$column_name'";
						$style = "";
						if ( in_array( $column_name, $hidden ) ) {
							$style = ' style="display:none;"';
						}
						$attributes = $class . $style;
						//Display the cell
						echo '<td ' . $attributes . '>' . $rec->{$column_name} . '</td>';
					}
					//Close the line
					echo '</tr>';
				}

			}

		}

		/**
		 * Message to be displayed when there are no items
		 *
		 * @since 3.1.0
		 */
		public function no_items() {
			_e( 'No activity for the past 2 weeks.' );
		}

	}