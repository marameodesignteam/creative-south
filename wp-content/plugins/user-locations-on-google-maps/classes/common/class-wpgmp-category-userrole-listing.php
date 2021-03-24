<?php 

if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'WPGMP_Role_Category_Listing' ) ) {

	/**
	 * Class wpp_Rule_Table to display rules for manage.
	 * @author Flipper Code <hello@flippercode.com>
	 * @package Posts
	 */
	class WPGMP_Role_Category_Listing extends FlipperCode_List_Table_Helper {

		/**
		 * Intialize manage category table.
		 *
		 * @param array $tableinfo Table's properties.
		 */
		private $marker_data_wpgmp = array();
		private $marker_data_option;
		private $listing_screen_strings;

		public function __construct( $tableinfo ) {

			$this->marker_data_option = maybe_unserialize(get_option('wpgmp_userroles_category_data'));
			$this->get_marker_data();
			parent::__construct( $tableinfo );
		}
		/**
		 * Show marker image assigned to category.
		 *
		 * @param  array $item Category row.
		 * @return html       Image tag.
		 */

		function get_marker_data() {

			 $modelFactory = new WPGMP_Model();
	         $category_obj = $modelFactory->create_object( 'group_map' );
	         $this->marker_data_wpgmp   = $category_obj->fetch();
		}

		public function column_wp_role_icon( $item ) {

			$cat_id = $this->marker_data_option[$item['wp_role_id']];
			$icon_url = '';
			if (!empty($cat_id)) {

				foreach($this->marker_data_wpgmp as $cat){

				if($cat_id == $cat->group_map_id){
					$icon_url = $cat->group_marker;
					break;
				}
			}

			return sprintf( '<img src="' . $icon_url . '"/>');
			} else {
				return '---';
			}
		}
		/**
		 * Show category's parent name.
		 *
		 * @param  [type] $item Category row.
		 * @return string       Category name.
		 */
		public function column_wp_role_category( $item ) {

			$cat_id = $this->marker_data_option[$item['wp_role_id']];
			
			if (!empty($cat_id)) {
			    foreach($this->marker_data_wpgmp as $cat){

					if($cat_id == $cat->group_map_id){
						return $cat->group_map_title;
						break;
					}
				}
			} else {
				return '---';
			}
			
		}

		public function column_wp_role_title( $item ) {

			$actions = array();
			foreach ( $this->actions as $action ) {
				$action_slug  = sanitize_title( $action );
				$action_label = ucwords( $action );
				if ( 'edit' == $action_slug ) {
					$actions[ $action_slug ] = sprintf( '<a href="?page=%s&doaction=%s&' . 'wp_role_id' . '=%s">' . $action_label . '</a>', $this->admin_add_page_name, $action_slug, $item['wp_role_id'] );
					} 
			}
			return sprintf( '%1$s %2$s',$item['wp_role_title'], $this->row_actions( $actions ) );
		}
	}
	
}