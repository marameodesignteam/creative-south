<?php

  if(!class_exists('WPGMP_Extension_Helper')) {

    class WPGMP_Extension_Helper {


      protected $wp_option_name = 'wpgmp_userroles_category_data';
      protected $dbdata;
      protected $all_wpgmp_cat;
          
      public function __construct() {}

      function wpgmp_common_plugin_activation_options() {

        $this->dbdata = maybe_unserialize(get_option($this->wp_option_name));
        $data = $this->dbdata;

        if(empty($data)) {

            global $wp_roles;
            if( ! isset( $wp_roles ) )
              $wp_roles = new WP_Roles();
             
            $all = $wp_roles->get_names();
            $data = array();
            foreach($all as $key => $role){
              $data[$key] = '';
            }
            update_option( $this->wp_option_name, $data );

        }
      }

      function wpgmp_get_marker_icon_userrole($uid) {

        $data = maybe_unserialize(get_option($this->wp_option_name));

        $modelFactory = new WPGMP_Model();
        $category_obj = $modelFactory->create_object( 'group_map' );
        $all_wpgmp_cat   = $category_obj->fetch();

        $user_meta = get_userdata($uid);
        $user_roles = $user_meta->roles;
        $cat_id = '';
        foreach ($user_roles as $user_role ) {
          $cat = $data[$user_role];
          if(!empty($cat)) {
            $cat_id = $cat;
            break;
          }
        }

        $result = array();
        if(!empty($all_wpgmp_cat) && is_array($all_wpgmp_cat) ) {
          foreach($all_wpgmp_cat as $cat){

            if($cat_id == $cat->group_map_id){
              $result['icon_url'] = $cat->group_marker;
              $result['cat_title'] = $cat->group_map_title;
              break;
            }
          }
        }
        return $result;
      }

      function wpgmp_skip_markers_if_already_exists($markers, $user_id) {

          $already_exists = false;

          if(!empty($markers) && is_array($markers)) {

            foreach ($markers as $marker) {
            
              if( $marker['id'] == $user_id ) {
                $already_exists = true;
                break;
              } 
            }
          }
          return $already_exists;
      }
      
    }
  }