<?php
/*
Plugin Name:  User Locations On Google Maps
Plugin URI: http://www.flippercode.com/
Description: An Advanced Google Map Pro plugin extention/add-on that display loggedin user current locations on google map with help of Geolocation.
Version: 1.0.0
Author: flippercode
Author URI: http://www.flippercode.com/
Text Domain: user-locations-on-google-maps
Domain Path: /lang/
*/


if ( ! class_exists( 'WPGMP_Extension_Helper' ) ) {
     $pluginClass = plugin_dir_path( __FILE__ ) . 'classes/common/class-wpgmp-extension-helper.php';
    if ( file_exists( $pluginClass ) ) {
        include( $pluginClass );
    }
}

if ( ! defined( 'ABSPATH' ) ) {
    die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'WP_User_Location_On_Google_Maps' ) ) {
/**
 * 
 */
class WP_User_Location_On_Google_Maps extends WPGMP_Extension_Helper {

        private $wp_role_cat_data = 'wpgmp_userroles_category_data';
        private $proceed = false;
        private $daysDiff = '';
        private $pluginUrl = '';
        private $pluginDir = '';

        function __construct() {
            
            $this->pluginUrl = plugin_dir_url( __FILE__ );
            $this->pluginDir = plugin_dir_path( __FILE__ );
            $this->wpul_check_plugin_dependancy();
            if($this->proceed) {
                $this->wpul_register_plugin_hooks();
            }
            
        }

        function load_files() {
            
            if(!class_exists('WPGMP_WPUsers_Connect')) {

                require_once( 'classes/common/class-wpgmp-wpusers-connect.php' );
                $args = array('msgstrings' => $this->msgstrings);
                new WPGMP_WPUsers_Connect($args);
            }
        }

        function setup_strings() {
            
            $this->msgstrings['submit_btn_text'] = esc_html__( 'Submit', 'user-locations-on-google-maps' );
            $this->msgstrings['none_text'] = esc_html__( 'None', 'user-locations-on-google-maps' );
            $this->msgstrings['table_heading_icon'] = esc_html__( 'Icon', 'user-locations-on-google-maps' );
            $this->msgstrings['table_heading_cat_title'] = esc_html__( 'Category Title', 'user-locations-on-google-maps' );
            $this->msgstrings['table_heading_cat_choose'] = esc_html__( 'Choose', 'user-locations-on-google-maps' );
            $this->msgstrings['table_heading_assign_cat'] = esc_html__( 'Assign category to', 'user-locations-on-google-maps' );
            $this->msgstrings['back_btn_text'] = esc_html__( 'Back', 'user-locations-on-google-maps' );
            $this->msgstrings['submenu_heading'] = esc_html__( 'Assign Role Category', 'user-locations-on-google-maps' );
            $this->msgstrings['submenu_page_heading'] = esc_html__( 'Assign Role Marker Category', 'user-locations-on-google-maps' );
            $this->msgstrings['address_label_text'] = esc_html__( 'Address', 'user-locations-on-google-maps' );
            $this->msgstrings['wpgmp_ext_domain'] = 'user-locations-on-google-maps';
            $this->msgstrings['message_current_loc'] = esc_html__( 'Your Current Location - ', 'user-locations-on-google-maps' );

            $this->msgstrings['listing_screen_strings'] = array(
                'manage_heading'      => esc_html__( 'List Of User Roles With Associated Marker Category', 'user-locations-on-google-maps' ),
                'add_button'          => '',
                'update_msg'          => esc_html__( 'Marker category updated successfully.', 'user-locations-on-google-maps' ),
                'singular_label'          => esc_html__( 'Marker category', 'user-locations-on-google-maps' ),
                'plural_label'          => esc_html__( 'Marker categories', 'user-locations-on-google-maps' ),
            );
            $this->msgstrings['tblCoumnsInfo'] = array(
                'wp_role_id' => esc_html__( 'WP User ID', 'user-locations-on-google-maps' ),
                'wp_role_title'   => esc_html__( 'WP User Role', 'user-locations-on-google-maps' ),
                'wp_role_category'      => esc_html__( 'WPGMP Category Assigned', 'user-locations-on-google-maps' ),
                'wp_role_icon'      => esc_html__( 'Icon', 'user-locations-on-google-maps' ),
            );
            
        }


        function wpul_register_plugin_hooks() {

            $this->setup_strings();
            $this->load_files();
            
            if( is_admin() ) {
                add_action( 'admin_head', array($this, 'wpul_localize_script') );
                add_filter( 'wpgmp_input_field_save_entity_data',array($this,'wpul_addon_custom_fields_html'));
                add_action( 'admin_enqueue_scripts', array($this, 'wpul_register_admin_script'));
                add_action( 'edit_user_profile', array($this, 'wpul_address_field_wpuser_profile_fields'));
                add_action( 'show_user_profile', array($this, 'wpul_address_field_wpuser_profile_fields'));
                add_action( 'edit_user_profile_update', array($this, 'wpul_update_location_user_profile_fields' ) );
                add_action( 'profile_update', array($this, 'wpul_update_location_user_profile_fields' ) );
            }

            add_action( 'plugins_loaded', array($this,'wpul_load_textdomain' ));     
            add_action( 'wp_ajax_wpul_ajax_call',array( $this, 'wpul_ajax_call' ) );
            add_action( 'wp_ajax_nopriv_wpul_ajax_call', array( $this, 'wpul_ajax_call' ) );
            add_action( 'wp_head', array($this, 'wpul_localize_script') );
            add_filter( 'wpgmp_marker_source', array($this, 'wpul_marker_source_users'), 100, 2);
            add_action( 'init', array($this, 'wpul_get_days_to_last_update_location') );
            
        }

        function wpul_check_plugin_dependancy() {
                
                if ( ! function_exists( 'is_plugin_active_for_network' ) )
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                
                //GoogleMaps dependency
                $is_google_maps_installed = in_array( 'wp-google-map-gold/wp-google-map-gold.php',get_option('active_plugins' ) ) ;
                $is_google_maps_active = ( is_plugin_active_for_network( 'wp-google-map-gold/wp-google-map-gold.php' ) ) ? true : false;
                $this->googlemapsMissing = (!$is_google_maps_installed && !$is_google_maps_active) ? true : false;

                $this->proceed = !($this->googlemapsMissing) ? true : false;
                if(!$this->proceed)
                add_action( 'admin_notices', array( $this, 'wpul_admin_notices' ) ); 

        }

        function wpul_admin_notices() {
        
            if($this->googlemapsMissing)
            $this->wpul_missing_dependency();
            
        }

        function wpul_get_days_to_last_update_location() {

            if(is_user_logged_in()) {

                $user_id = get_current_user_id();
                $stored_data = get_user_meta($user_id, 'wpgmp_user_current_location', true);
                if(is_array($stored_data) && !empty($stored_data)) {
                    $old_time = $stored_data['time'];
                    $last_updated_date = date('m/d/Y', $old_time);
                    $today_date = date("m/d/Y");
                    $this->daysDiff = $this->wpul_diff_between_last_updated_date($last_updated_date, $today_date);
                }
            }
        }

        function wpul_get_wpgmp_cat_obj() {

            $modelFactory = new WPGMP_Model();
            $category_obj = $modelFactory->create_object( 'group_map' );
            $categories   = $category_obj->fetch();

            return $categories;
        }

        function wpul_get_cat_icon_for_user($user) {

            $category_icon = '';

            $categories = $this->wpul_get_wpgmp_cat_obj();
            $get_user_role =  $user->roles[0];
            $get_category_role = maybe_unserialize(get_option($this->wp_role_cat_data));
            foreach ($get_category_role as $key => $cat_id) {
                if($get_user_role == $key) {
                   $get_cat_id = $cat_id;
                   break;
                }
            }
            foreach ( $categories as $category ) {
                   if($category->group_map_id == $cat_id) {
                      $category_icon = $category->group_map_title;
                      break;
                   } else {
                      $category_icon = '';
                   }
                }
            return $category_icon;
        }

        function wpul_missing_dependency() {
            ?>
            <div class="notice notice-error">
                <p><a target="_blank" href="https://codecanyon.net/item/advanced-google-maps-plugin-for-wordpress/5211638"><?php esc_html_e('WP Google Maps Pro','user-locations-on-google-maps'); ?></a><?php esc_html_e( ' is required for User Locations On Google Maps Add-On plugin to work. Please install and configure WP Google Maps pro first.', 'user-locations-on-google-maps' ); ?></p>
            </div>
            <?php
        }

        function wpul_register_admin_script() {
            
            $screen = get_current_screen();
           
            if( $screen->id == 'profile' || $screen->id == 'user-edit' || $screen->id == 'wp-google-map-pro_page_wpgmp_form_map')
                wp_enqueue_script( 'wpul-backend-js',$this->pluginUrl .'assets/js/backend.js',array ('jquery'),'',true );
        }

        function wpul_get_user_current_address($userId) {

            $get_address_json = get_user_meta( $userId, 'wpgmp_user_current_location', true );
            $full_address = maybe_unserialize($get_address_json);
           
            return $full_address;
        }

        function wpul_address_field_wpuser_profile_fields($user) {
         
            $get_address = get_user_meta( $user->ID, 'wpgmp_user_current_location', true );
            $user_address = ( isset( $get_address['address'] ) && ! empty( $get_address['address'] ) ) ? $get_address['address'] : '';
            ?>
            <table class="form-table">
                <tr class="user-address-wrap">
                   <th><label for="address"><?php echo apply_filters('woowpgmp_address_field_label', $this->msgstrings['address_label_text']);?></label>
                   </th>
                   <td><input type="text" class="wpgmp_autocomplete_control regular-text" name="wpgmp_autocomplete_control" id="wpgmp_autocomplete_control_id" value="<?php echo $user_address; ?>" /></td>
                   <td><input type="hidden" class="wpgmp_autocomplete_control" name="formatted_address" /></td>
                   <td><input type="hidden" class="wpgmp_autocomplete_lat" name="latitude" /></td>

                   <td><input type="hidden" class="wpgmp_autocomplete_lng" name="longitude" /></td>
                   <td><input type="hidden" class="wpgmp_autocomplete_city" name="city" /></td>
                   <td><input type="hidden" class="wpgmp_autocomplete_state" name="state" /></td>
                   <td><input type="hidden" class="wpgmp_autocomplete_country" name="country" /></td>
                </tr>
            </table>

             <?php 
            if( !empty($get_address) ) {
                
                $category_icon = $this->wpul_get_cat_icon_for_user($user);
            
                $infowindowmsg = $this->msgstrings['message_current_loc'] .$get_address['address'];
                $infowindowmsg = apply_filters('wpgmp_woo_account_details_infowindow_msg',$infowindowmsg);
                $display_map_args = array('height' => '300','zoom' => '10','language' => 'en',
                                    'map_type' => 'ROADMAP', 'map_draggable' => 'true' );

                $map = apply_filters( 'wpul_profile_map_args' , $display_map_args );

                echo do_shortcode( '[display_map height="'.$map['height'].'" zoom="'.$map['zoom'].'" language="'.$map['language'].'" map_type="'.$map['map_type'].'" map_draggable="'.$map['map_draggable'].'" marker1=" '.$get_address['lat'].' | '.$get_address['lng'].' | title | '.$infowindowmsg.' | '.$category_icon.' "]');
            }
        }

        function wpul_post_user_data($data) {

            $user_data['address']       = $data['formatted_address'];
            $user_data['lat']           = $data['latitude'];
            $user_data['lng']           = $data['longitude'];
            $user_data['city']          = $data['city'];
            $user_data['state']         = $data['state'];
            $user_data['country']       = $data['country'];
            $user_data['time']          = time();
            $user_data = apply_filters( 'wpul_usermeta_data' , $user_data );
            return $user_data;
        }

        function wpul_update_location_user_profile_fields( $user_id ) {

            if(isset($_POST['formatted_address']) && !empty($_POST['formatted_address'])) {

                $user_data = $this->wpul_post_user_data($_POST);
                if(is_array($user_data) && !empty( $user_data ) ) {

                    update_user_meta( $user_id, 'wpgmp_user_current_location', $user_data );
                }
            }
        }

        function wpul_get_processed_message($raw_msg,$user){

            $placeholders_result = array();
            $customer_message = $raw_msg;

            $user_role = $user->roles;
            if(!empty($user_role) && is_array($user_role) && count($user_role) > 0)
                $user_role = implode(", " , $user_role);

            $user_meta = get_user_meta($user->ID);
            $user_data  = array();
            foreach ($user_meta as $key => $value) {
                if(isset($value[0]) ) {
                    
                    $value[0] = trim($value[0]);
                    $value[0] = maybe_unserialize($value[0]);
                    if($key == 'wpgmp_user_current_location') {
                        $value[0] = $value[0]['address'];
                    }
                    if(is_array($value[0])) {

                        $isIndexed = array_values($value[0]) === $value[0];
                        if($isIndexed)
                        $value[0] = implode(', ',$value[0]);
                        $value[0] = apply_filters('wpul_user_meta_value',$value[0],$key,$user->ID);
                    }

                    $user_data['{'.$key.'}'] =  $value[0];
                }
            }
            
            $customer_meta = array(
                '{user_name}'           => isset($user->display_name) ? $user->display_name : '',
                '{first_name}'          => isset($user_data['first_name']) ? $user_data['first_name'] : '',
                '{last_name}'           => isset($user_data['last_name']) ? $user_data['last_name'] : '',
                '{user_email}'          => isset($user->user_email) ? $user->user_email : '',
                '{user_role}'           => $user_role,
            );

            $placeholers_to_process = array_merge($customer_meta, $user_data);
            $customer_message = strtr($customer_message, $placeholers_to_process);

            $placeholders_result['customer_meta'] = $customer_meta;
            
            $placeholders_result['user_message'] = $customer_message;
            $placeholders_result['placeholers_to_process'] = $placeholers_to_process;
            $placeholders_result['user_role'] = $user_role;
    
            return $placeholders_result;

        }

        function wpul_marker_source_users($markers, $map_id) {

            $extention_data = $this->wpul_get_extention_data($map_id);

            if(isset($extention_data['extensions_fields']['wpul_settings']['wp_customer_enable']) && $extention_data['extensions_fields']['wpul_settings']['wp_customer_enable'] == 'true' ) {


                $get_selected_role =  isset($extention_data['extensions_fields']['wpul_settings']['wp_roles']) ? $extention_data['extensions_fields']['wpul_settings']['wp_roles'] : '';

                if(empty($get_selected_role)) {
                    $args = array(
                     'role' => -1,
                    );

                } else {

                    $args = array(
                     'role__in' => $get_selected_role,
                    );
                }
                $args = apply_filters('wpul_wp_user_query_args', $args);
                
                $user_query = new WP_User_Query( $args );

                if ( ! empty( $user_query->get_results() ) ) {
                    $users = array();
                    foreach ( $user_query->get_results() as $user ) {
                        $get_address = get_user_meta( $user->ID, 'wpgmp_user_current_location', true );
                        $full_address = $this->wpul_get_user_current_address($user->ID);
                        if( empty($full_address) ||  empty($get_address) )
                        continue;

                        $already_exists = $this->wpgmp_skip_markers_if_already_exists($markers, $user->ID);
                        if($already_exists)
                        continue;

                        $result = $this->wpgmp_get_marker_icon_userrole($user->ID);

                        $icon_url = isset($result['icon_url']) ? $result['icon_url'] : '';
                        $assigned_cat_of_user  = isset($result['cat_title']) ? $result['cat_title'] : '';

                        $user_message = $extention_data['extensions_fields']['wpul_settings']['user_messages'];

                        $placeholders_result = $this->wpul_get_processed_message( $user_message, $user);
                        $user_role = $placeholders_result['user_role'];
                        
                        $placeholers_to_process = $placeholders_result['placeholers_to_process'];

                        foreach ($placeholers_to_process as $key => $item) {
                            $placeholers_to_process[trim($key, '{}')] = $item;
                            unset($placeholers_to_process[$key]);   
                        }

                        $user_record = array(
                            'user_name'         => $user->display_name,
                            'user_message'      => '<p class="user_biographical_info">'.$placeholders_result['user_message'].'</p>',
                            'user_id'           => $user->ID,
                            'user_address'      => $full_address['address'],
                            'user_lat'          => $full_address['lat'],
                            'user_lng'          => $full_address['lng'],
                            'user_country'      => $full_address['country'],
                            'user_state'        => $full_address['state'],
                            'user_city'         => $full_address['city'],
                            'user_category'     => !empty($assigned_cat_of_user) ? $assigned_cat_of_user : '',
                            'marker'                => (isset( $icon_url) && !empty($icon_url)) ?  $icon_url : '',
                            'user_image'        => '<img class="user_profile_img" src='.get_avatar_url($user->ID).'>',
                            'user_profile_data' => $placeholers_to_process,
                        ); 

                        $users[] = $user_record;
                    }

                    $marker = array();

                    $users = apply_filters('wpul_users_data',$users,$map_id);

                    foreach ( $users as $key => $user ) {

                        $marker['category']     = $user['user_category'];
                        $marker['id']           = $user['user_id']; 
                        $marker['title']        = $user['user_name']; 
                        $marker['address']      = $user['user_address']; 
                        $marker['message']      = $user['user_message']; 
                        $marker['latitude']     = $user['user_lat'];
                        $marker['longitude']    = $user['user_lng']; 
                        $marker['country']      = $user['user_country'];
                        $marker['state']        = $user['user_state'];
                        $marker['city']         = $user['user_city'];
                        $marker['icon']         = $user['marker'];
                        $marker['marker_image'] = $user['user_image'];
                        $marker['extra_fields'] = $user['user_profile_data'];

                        $markers[] = $marker;
                    }
                }
            }
            return $markers;
        }

        function wpul_diff_between_last_updated_date($last_updated_date, $today_date) { 
            
            $diff = strtotime($last_updated_date) - strtotime($today_date); 
            return abs(round($diff / 86400)); 
        }

        function wpul_ajax_call($data) {

            $data = $_POST;
            $user_data = array();

            if(is_user_logged_in()) {
                
                $user_id = get_current_user_id();
                $stored_data = get_user_meta($user_id, 'wpgmp_user_current_location', true);

                $remember_days =  isset($extention_data['extensions_fields']['wpul_settings']['days_to_ask_location']) ? $extention_data['extensions_fields']['wpul_settings']['days_to_ask_location'] : '1';

                if(empty($stored_data) ) {

                    $user_data = $this->wpul_post_user_data($_POST);
                    
                    update_user_meta( $user_id, 'wpgmp_user_current_location', $user_data );
                    $resp['update_meta'] = true;

                }
                else if( !empty($stored_data) && $this->daysDiff >= $remember_days ) {

                    $user_data = $this->wpul_post_user_data($_POST);
                    
                    update_user_meta( $user_id, 'wpgmp_user_current_location', $user_data );
                    $resp['update_meta'] = true;

                }else{

                    $resp['update_meta'] = false;
                }
                
            }
           
            echo json_encode( $resp );
            exit;

        }

        function wpul_localize_js_script() {

            wp_register_script( "ajax_script", $this->pluginUrl .'assets/js/wpul-user-location.js', array('jquery') );
            wp_localize_script( 'ajax_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'ajax_script' );

        }

        function wpul_localize_script() {
           
            if(is_user_logged_in()) {

                if( is_admin() ) {
                    if( class_exists('WPGMP_Helper') ) {
                        WPGMP_Helper::wpgmp_register_map_frontend_resources();
                    }
                }

                $user_id = get_current_user_id();
                $stored_data = get_user_meta($user_id, 'wpgmp_user_current_location', true);
                
                if(is_array($stored_data) && !empty($stored_data)) {

                    $remember_days =  isset($extention_data['extensions_fields']['wpul_settings']['days_to_ask_location']) ? $extention_data['extensions_fields']['wpul_settings']['days_to_ask_location'] : '1';

                    if($this->daysDiff >= $remember_days ) {

                        $this->wpul_localize_js_script();
                    }

                } else{

                    $this->wpul_localize_js_script();
                } 
            }

        }

        function wpul_load_textdomain(){
            load_plugin_textdomain('user-locations-on-google-maps', false, basename(dirname(__FILE__)) . '/lang');
        }

        function wpul_get_extention_data($current_map_id) {

            $model_factory = new WPGMP_Model();
            $map_obj = $model_factory->create_object( 'map' );
            $map_data = $map_obj->fetch( array( array( 'map_id', '=', $current_map_id ) ) );
            $map_control_settings = $map_data[0]->map_all_control;
            $map_control_settings = maybe_unserialize($map_control_settings);
            $extention_data = $map_control_settings;
            return $extention_data;
        }

        function wpul_addon_custom_fields_html($markup){

            $screen = get_current_screen();
            
            if(!empty($screen->id) && !($screen->id == 'wp-google-map-pro_page_wpgmp_form_map'))

            return $markup;

            if(!empty($_GET['map_id'])) {

                $extention_data = $this->wpul_get_extention_data($_GET['map_id']);
            }


            $wp_addon_settings_markup    = '';
            $wp_addon_settings_markup = '</div></div>';

            $wp_addon_settings_markup .= 
                '<div class="fc-form-group ">
                    <div class="fc-12">
                        <h4 class="fc-title-blue group-element">'.esc_html__('User Locations on Google Maps ( Addon Settings )','user-locations-on-google-maps').'</h4>
                    </div>
                </div>';

            $enable_wp = FlipperCode_HTML_Markup::field_checkbox('extensions_fields[wpul_settings][wp_customer_enable]',array(
                'value'   => 'true',
                'id'      => 'wp_customer_enable',
                'current' => isset( $extention_data['extensions_fields']['wpul_settings']['wp_customer_enable'] ) ? $extention_data['extensions_fields']['wpul_settings']['wp_customer_enable'] : '',
                'desc'    => esc_html__( 'Please check to enable User\'s on map.', 'user-locations-on-google-maps' ),
                'class'   => 'chkbox_class switch_onoff',
                'data' => array( 'target' => '.wp_controls_user_location' ),
            ));

            $wp_addon_settings_markup .=
            '<div class="fc-form-group ">
                <div class="fc-3">
                    <label for="enable_wp">'.esc_html__('Enable Addon','user-locations-on-google-maps').'</label>
                </div>
                <div class="fc-8">'
                    .$enable_wp.
                '</div>
            </div>';
            
            $get_roles = get_editable_roles();
            foreach ($get_roles as $key => $role) {

                $roles[$key] = $role['name'];
            }

            $role_markp = FlipperCode_HTML_Markup::field_multiple_checkbox('extensions_fields[wpul_settings][wp_roles][]', array(
                'value'         => $roles,
                'current'       => isset($extention_data['extensions_fields']['wpul_settings']['wp_roles']) ? $extention_data['extensions_fields']['wpul_settings']['wp_roles'] : '',
                'class'         => 'chkbox_class',
                'default_value' => '',
                'desc'    => esc_html__( 'Please select roles to show on map.', 'user-locations-on-google-maps' ),
                )
            );

            $wp_addon_settings_markup .=
            '<div class="fc-form-group wp_controls_user_location" style="display:none;">
                <div class="fc-3">
                    <label for="select_group">'.esc_html__('Select Roles\'s to Display','user-locations-on-google-maps').'</label>
                </div>
                <div class="fc-8">'.$role_markp.'</div>
            </div>';

            $days_to_ask_location_markp = FlipperCode_HTML_Markup::field_text('extensions_fields[wpul_settings][days_to_ask_location]', array(
                'value'       => ( isset( $extention_data['extensions_fields']['wpul_settings']['days_to_ask_location'] ) and ! empty( $extention_data['extensions_fields']['wpul_settings']['days_to_ask_location'] ) ) ? $extention_data['extensions_fields']['wpul_settings']['days_to_ask_location'] : '1',
                    'placeholder' => esc_html__( 'Enter No of days to ask user location', 'wpgmp-google-map' ),
                    'desc'    => esc_html__( 'Please enter no of days to ask loggedin user location.', 'user-locations-on-google-maps' ),
                )
            );

            $wp_addon_settings_markup .=
            '<div class="fc-form-group wp_controls_user_location" style="display:none;">
                <div class="fc-3">
                    <label for="select_group">'.esc_html__('Ask Location After Days','user-locations-on-google-maps').'</label>
                </div>
                <div class="fc-8">'.$days_to_ask_location_markp.'</div>
            </div>';

            $wp_addon_settings_markup .= 
            '<div class="fc-form-group wp_controls_user_location" style="display:none;">
                <div class="fc-12">
                    <h4 class="fc-title-blue group-element">'.esc_html__('Infowinddow message for users','user-locations-on-google-maps').'</h4>
                </div>
            </div>';

            $customer_placeholders = array(
                '{user_id}',
                '{user_name}',
                '{user_image}',
                '{first_name}',
                '{last_name}',
                '{wpgmp_user_current_location}',
                '{user_email}',
                '{meta_field_name}',
            );
            $default_value = 'Hello My Name is <b>{first_name} {last_name}</b>. <br/><br/>
                                Thank You <b>{user_name}</b><br/>
                                Contact Email - <b>{user_email}</b>';

            $default_value = ( isset( $extention_data['extensions_fields']['wpul_settings']['user_messages'] ) and '' != $extention_data['extensions_fields']['wpul_settings']['user_messages'] ) ? $extention_data['extensions_fields']['wpul_settings']['user_messages'] : $default_value;

            $infowindow_textarea = FlipperCode_HTML_Markup::field_textarea('extensions_fields[wpul_settings][user_messages]', array(
                'label'         => esc_html__( 'Infowindow Message', 'wpgmp-google-map' ),
                'value'         => ( isset( $extention_data['extensions_fields']['wpul_settings']['user_messages'] ) and ! empty( $extention_data['extensions_fields']['wpul_settings']['user_messages'] ) ) ? $extention_data['extensions_fields']['wpul_settings']['user_messages'] : '',
                'desc'          => esc_html__( 'Enter message.', 'wpgmp-google-map' ),
                'textarea_rows' => 10,
                'textarea_name' => 'user_messages',
                'class'         => 'form-control',
                'id'            => 'wpul_infomessage',
                'value'         => $default_value,
            ));

            $wp_addon_settings_markup .= 
            '<div class="fc-form-group wp_controls_user_location" style="display:none;">
                <div class="fc-3">
                    <label for="select_group">'.esc_html__('Infowindow for Message','user-locations-on-google-maps').'</label>
                </div>
                <div class="fc-8">'.$infowindow_textarea.'
                    <div class="fc_supported_placeholder" id="wpul_placeholders_div" style="display:none">
                        <ul class="fc_placeholders fc-hidden-placeholder" style="display: block;">
                            <li>' . implode( '</li><li>', $customer_placeholders) . '</li>
                        </ul> 
                    </div>
                </div>
            </div>';

            $wp_addon_settings_markup .=
            '<div class="fc-form-group wp_controls_user_location" style="display:none;">
                <div class="fc-3">
                    <label for="infowindow_message">&nbsp;</label>
                </div>
                <div class="fc-8">
                    <input type="button" name="fc_reset" id="wpul_reset_message" class="fc-btn fc-btn-submit" value="Reset">
                    <input type="button" name="fc_show_placeholders" id="wpul_show_placeholders" class="fc-btn fc-btn-submit" value="Show Placeholders">
                </div>
            </div>';

            $wp_addon_settings_markup .='<div class="fc-form-group"><div class="fc-8">';

            $html = ''; 

            $html .= $wp_addon_settings_markup;

            $html .= $markup;

            return $html;

        }

}

new WP_User_Location_On_Google_Maps();

}








