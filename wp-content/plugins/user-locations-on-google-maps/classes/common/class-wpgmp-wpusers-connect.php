<?php

   class WPGMP_WPUsers_Connect {

      protected $wp_option_name = 'wpgmp_userroles_category_data';
      protected $dbdata;
      protected $all_wpgmp_cat;
      private $msgstrings = array();
      private $is_required;

      public function __construct($args) {

         $this->msgstrings = $args['msgstrings'];   
         $this->wpuc_register_hooks();
      }

      public function wpuc_register_hooks() {

         $this->is_required = maybe_unserialize(get_option('woowpgmp_required_address_field', true));

         if(is_admin()) {
            add_action('admin_menu', array($this, 'wpuc_common_category_plugin_menu'));
            add_action('admin_init', array($this, 'wpuc_common_assign_category_form_handling'));
            add_action( 'admin_enqueue_scripts', array($this, 'wpuc_add_flippercode_ui_css') );
         }
         
         add_action('init', array($this, 'wpuc_get_wpgmp_categories'));
         $isWooInstalled = in_array( 'woocommerce/woocommerce.php',get_option('active_plugins' ) );

         $isBuddypressInstalled = in_array( 'buddypress/bp-loader.php',get_option('active_plugins' ) );
      }

      function wpuc_add_flippercode_ui_css() {
         $screen = get_current_screen();
         if($screen->id == 'users_page_assigned-marker-category-role')
         wp_enqueue_style( 'wpuc-flippercode-ui',  plugins_url('wp-google-map-gold/assets/css/flippercode-ui.css', false));
         
         if($screen->id == 'users_page_update-marker-category-role')
         wp_enqueue_style( 'wpuc-common-backend-css',  plugin_dir_url( __FILE__ ).'/css/wpuc_common_backend.css', false);
      }

      function wpuc_get_wpgmp_categories() {

         $this->dbdata = maybe_unserialize(get_option($this->wp_option_name));
         $modelFactory = new WPGMP_Model();
         $category_obj = $modelFactory->create_object( 'group_map' );
         $this->all_wpgmp_cat   = $category_obj->fetch();
      }

      function wpuc_common_category_plugin_menu() {

         add_submenu_page('users.php', $this->msgstrings['submenu_heading'], $this->msgstrings['submenu_heading'], 'administrator', 'assigned-marker-category-role', array( $this, 'wpuc_assign_role_category_listing_html' ) );

         add_submenu_page(null, $this->msgstrings['submenu_page_heading'], $this->msgstrings['submenu_page_heading'],'manage_options','update-marker-category-role',array($this,'wpuc_assign_category_page_callback') );

      }

      function wpuc_assign_role_category_listing_html() {

         if (!class_exists('WPGMP_Role_Category_Listing'))
         require_once( 'class-wpgmp-category-userrole-listing.php' );

         $translatedStrings = $this->msgstrings['listing_screen_strings']; 
         global $wpdb;
         $sortable  = array( 'wp_role_title' );
         global $wp_roles;
         if ( ! isset( $wp_roles ) )
             $wp_roles = new WP_Roles();

         $all = $wp_roles->get_names();
         $row_data = array();
         foreach($all as $key => $role){
            $row_data[] = array('wp_role_id' => $key,'wp_role_title' => $role,'wp_role_category'=>'','wp_role_icon' => '');
         }

         $tableinfo = array(
            'external'             =>  $row_data,
            'textdomain'              => 'wpgmp-google-map',
            'singular_label'          => $translatedStrings['singular_label'],
            'plural_label'            => $translatedStrings['plural_label'],
            'admin_listing_page_name' => 'assigned-marker-category-role',
            'admin_add_page_name'     => 'update-marker-category-role',
            'primary_col'             => 'wp_role_id',
            'columns'                 => $this->msgstrings['tblCoumnsInfo'],
            'sortable'                => $sortable,
            'per_page'                => 10,
            'col_showing_links'       => 'wp_role_title',
            'searchExclude'           => array(),
            'bulk_actions'            => array(),
            'translation' => array(
               'manage_heading'      => $translatedStrings['manage_heading'],
               'add_button'          => '',
               'update_msg'          => $translatedStrings['update_msg'],
            ),
         );

         return new WPGMP_Role_Category_Listing( $tableinfo );

      
      }

      function wpuc_validate_user_location_filed_update_error( $errors, $update, $user) {

         if(isset($_POST['wpgmp_autocomplete_control']) ) {
            if(empty($_POST['wpgmp_autocomplete_control']) ) {
               $errors->add("address_required",apply_filters('woowpgmp_validation_message', $this->msgstrings['address_validation_message'] ) );
            }
         }

         return $errors;
      }

      function wpuc_common_assign_category_form_handling() {
      
         $data = $this->dbdata;

         if ( isset( $_POST['wpgmp_save_assigned_cat'] )  ) {

            if ( !empty( $_REQUEST['_nonce'] ) ) {
               $nonce = sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) );
            }
            
            if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'submit-role-category-nonce' ) ) {
               die( 'Cheating...' );
            }

            if(wp_verify_nonce($nonce, 'submit-role-category-nonce')) {

               if ( ! current_user_can( 'administrator' ) ) {
                  die( 'You are not allowed to save changes!' );
               }

               $role_to_update = $_GET['wp_role_id'];
               $data[$role_to_update]  = $_POST['marker_category_id'];
               update_option($this->wp_option_name, $data);
               $this->dbdata = $data;

            }
         }
      }

      function wpuc_assign_category_page_callback() {
         
         $all_wpgmp_cat = $this->all_wpgmp_cat;

         $get_categories = array();
         $data = $this->dbdata;
      
         if(is_ssl() ) {
            $action = 'https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
         } else {
             $action = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
         }
         ?>
         <div class="fc-back">
            <div class="fc-form-group ">
               <div class="back-button">
                  <a class="button" href="<?php echo admin_url( 'users.php?page=assigned-marker-category-role'); ?>"><?php echo $this->msgstrings['back_btn_text']; ?></a>
               </div>
            </div>

            <form method = "post" action="<?php echo $action; ?>" enctype="multipart/form-data">
               <div class="fc-form-group ">
                  <h4 class="fc-title-blue">
                     <?php echo $this->msgstrings['table_heading_assign_cat']  .' - '. ucwords($_GET['wp_role_id']); ?>
                  </h4>
               </div>

               <table class="roles-with-marker">
                 <tr>
                     <th><?php echo $this->msgstrings['table_heading_cat_choose']; ?></th>
                     <th><?php echo $this->msgstrings['table_heading_cat_title']; ?></th>
                     <th><?php echo $this->msgstrings['table_heading_icon']; ?></th>
                 </tr> <?php
                     foreach ($all_wpgmp_cat as $key => $value) {
                     $get_categories[] = $value; ?>
                 <tr>
                     <td><input type="radio" class="radio_class" name="marker_category_id" value="<?php echo $value->group_map_id; ?>" <?php checked( $data[$_GET['wp_role_id']], $value->group_map_id ); ?> >
                     </td>
                     <td><?php echo $value->group_map_title; ?></td>
                     <td><img src="<?php echo $value->group_marker; ?>"/></td>
                 </tr>
               <?php } ?>
                  <tr>
                     <td><input type="radio" class="radio_class" name="marker_category_id" value="" <?php checked( $data[$_GET['wp_role_id']], '' ); ?> >
                     </td>
                     <td> <?php echo $this->msgstrings['none_text']; ?></td>
                     <td>---</td>
                 </tr>
               </table>

               <div class="fc-form-group ">
                  <div class="fc-12">
                    <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('submit-role-category-nonce') ?>">
                    <input type="submit" class="btn btn-primary wp-core-ui button-primary" name="wpgmp_save_assigned_cat" value="<?php echo $this->msgstrings['submit_btn_text']; ?>">
                  </div>
               </div>
            </form>
         </div> <?php
      }
   }