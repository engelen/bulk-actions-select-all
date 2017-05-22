<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if access directly

/**
 * @since 1.0
 */
class BASA_Admin {

	/**
	 * Plugin class instance
	 *
	 * @var BASA
	 * @since 1.0
	 */
	public $basa;

	/**
	 * Constructor. Set up object and add action and filter callbacks.
	 *
	 * @since 1.0
	 */
	public function __construct( BASA $basa ) {
		$this->basa = $basa;

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'check_admin_referer', array( $this, 'handle_bulkactions' ), 10, 2 );
	}

	/**
	 * @since 1.0
	 * @see action:admin_enqueue_scripts
	 */
	public function scripts( $hook ) {
		global $wp_list_table;

		wp_register_script( 'basa-admin', BASA_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ) );
		wp_register_style( 'basa-admin', BASA_PLUGIN_URL . 'assets/css/admin.css' );

		if ( in_array( $hook, array( 'edit.php', 'edit-tags.php' ) ) && ! empty( $wp_list_table ) ) {
			$total_items = $wp_list_table->get_pagination_arg( 'total_items' );
			$per_page = $wp_list_table->get_pagination_arg( 'per_page' );

			if ( $total_items && $per_page && $total_items > $per_page ) {
				wp_enqueue_script( 'basa-admin' );
				wp_enqueue_style( 'basa-admin' );

				$all_entries_selected = __( 'All <strong>%d</strong> entries are now selected.', 'basa' );
				$all_entries_on_page_selected = __( 'All <strong>%d</strong> entries on this page have been selected.', 'basa' );
				$select_all = __( 'Select all <strong>%d</strong> entries.', 'basa' );
				$deselect_all = __( 'Deselect all.', 'basa' );

				wp_localize_script( 'basa-admin', 'BASA_Admin', array(
					'total_items' => $total_items,
					'items_per_page' => $per_page,
					'i18n' => array(
						'all_x_entries_selected' => $all_entries_selected,
						'all_x_entries_on_page_selected' => $all_entries_on_page_selected,
						'select_all_x_entries' => $select_all,
						'deselect_all' => $deselect_all
					)
				) );
			}
		}
	}

	/**
	 * Handle all operations associated with the bulk actions this plugin provides.
	 *
	 * @since 1.0
	 */
	public function handle_bulkactions( $action, $result ) {
		global $wp_list_table, $wp_query;

		$bulk_object_type = false;

		if ( $action == 'bulk-posts' ) $bulk_object_type = 'post';
		if ( $action == 'bulk-tags' ) $bulk_object_type = 'term';

		// Check current results of admin referer check and check action
		if ( ! $result || ! $bulk_object_type ) {
			return;
		}

		// Check list table object
		if ( empty( $wp_list_table ) ) {
			return;
		}

		// Check list table bulk action
		if (
			( $bulk_object_type == 'post' && ! in_array( $wp_list_table->current_action(), array( 'trash', 'untrash', 'delete' ) ) )
			||
			( $bulk_object_type == 'term' && ! in_array( $wp_list_table->current_action(), array( 'bulk-delete' ) ) )
		) {
			return;
		}

		// Check bulk delete action parameters (list of object IDs)
		if (
			( $bulk_object_type == 'post' && empty( $_REQUEST['post'] ) )
			||
			( $bulk_object_type == 'term' && empty( $_REQUEST['delete_tags'] ) )
		) {
			return;
		}

		// Check whether "Select all" action was chosen
		if ( empty( $_REQUEST['basa-selectall'] ) || empty( $_REQUEST['basa-num-items'] ) ) {
			return;
		}

		// Check whether a number of items was passed
		$num_items = intval( $_REQUEST['basa-num-items'] );

		if ( ! $num_items ) {
			return;
		}

		// Check whether taxonomy and callback arguments were supplied
		$wp_list_table->prepare_items();

		if ( $bulk_object_type == 'term' && ( empty( $wp_list_table->callback_args ) || empty( $wp_list_table->screen->taxonomy ) ) ) {
			return;
		}

		// Posts
		if ( $bulk_object_type == 'post' ) {
			add_filter( 'request', array( $this, 'request_all_ids' ) );
			wp_edit_posts_query();
			remove_filter( 'request', array( $this, 'request_all_ids' ) );

			$num_posts = count( $wp_query->posts );

			if ( $num_items != $num_posts ) {
				return;
			}

			$_REQUEST['post'] = $wp_query->posts;
		}

		// Terms
		if ( $bulk_object_type == 'term' ) {
			// Current taxomomy
			$taxonomy = $wp_list_table->screen->taxonomy;

			// Arguments for fetching terms
			$args = wp_parse_args( $wp_list_table->callback_args, array(
				'page' => 1,
				'number' => 20,
				'search' => '',
				'hide_empty' => 0
			) );

			$args['number'] = 0;
			$args['offset'] = 0;
			$args['fields'] = 'ids';
			unset( $args['page'] );

			// Fetch terms
			$terms = get_terms( $taxonomy, $args );

			if ( $num_items != count( $terms ) ) {
				return;
			}

			$_REQUEST['delete_tags'] = $terms;
		}
	}

	/**
	 * Alter the "request" hook query parameter to include all posts.
	 * Hook is intended for temporary use, i.e. it should be added directly before and removed directly after a function call
	 * such as wp_edit_posts_query().
	 *
	 * @see action:request
	 */
	public function request_all_ids( $query_vars ) {
		$query_vars['posts_per_page'] = -1;
		$query_vars['fields'] = 'ids';

		return $query_vars;
	}

}
