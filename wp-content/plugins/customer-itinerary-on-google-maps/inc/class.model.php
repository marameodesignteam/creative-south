<?php 



if ( ! defined( 'ABSPATH' ) ) {



	die( 'You are not allowed to call this page directly.' );



}



if ( ! class_exists( 'CIWPGMP_Model_User_Trip' ) ) {





	class CIWPGMP_Model_User_Trip {



        private $errorsMessage = array();



        private $validationMsg = array();



        private $updateTripValidationMsg = array();



        private $createTripValidationMsg = array();



        function __construct() { }





        function wptp_get_user_registred_trips() {



            $registred_trips = array();



            $all_trips = CIWPGMP_On_GoogleMaps::wptp_get_all_current_user_trips();



            if(is_array($all_trips) && count($all_trips) > 0 ) {



                foreach($all_trips as $trip) {



                    if( $trip->post_author != get_current_user_id() )



                    continue;



                    $registred_trips[] = $trip->post_title;



                }



            }

            return $registred_trips;



        }



        

        function wptp_create_new_trip() {



            $registred_trips = $this->wptp_get_user_registred_trips();



            if ( isset( $_POST['wptp_create_trip'] ) ) {



                if ( !empty( $_REQUEST['_nonce'] ) ) {



                    $nonce = sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) );



                }



                if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'submit-trip-name-nonce' ) ) {



                    die( 'Cheating...' );



                }



                if(wp_verify_nonce($nonce, 'submit-trip-name-nonce')) {


                    $trip_title = sanitize_text_field($_POST['wptp_trip_name']);

                    if (empty($trip_title)) {

                        $this->validationMsg[] = esc_html__(' Please enter trip name ', 'ciwpgmp-google-maps');



                    }

                }



                if(in_array($trip_title, $registred_trips)) {



                    $this->validationMsg[] = esc_html__(' Trip already exists ', 'ciwpgmp-google-maps');

                  

                }



                if( count($this->validationMsg) > 0 ) {



                    $this->createTripValidationMsg = $this->validationMsg;



                }



                else {



                    $post_data = array(



                        'post_title'	=> $trip_title,



                        'post_status'   => 'publish', 



                        'post_type'     => 'my_trips',



                        'post_author'	=> get_current_user_id()



                    );



                    $post_id = wp_insert_post( $post_data);
                    if(isset($_POST['add_current_location_to_trip']) && $_POST['add_current_location_to_trip'] == 'true') {
                        $current_post_id[] = $_POST['current_post_id'];
                        update_post_meta( $post_id, '_wptp_trip_locations', $current_post_id );
                    }


                }



                return $this->createTripValidationMsg;



            }



        }



        function wptp_update_trip() {



            $registred_trips = $this->wptp_get_user_registred_trips();





            if ( isset( $_POST['wptp_update_trip'] )  ) {





                if ( isset( $_REQUEST['_wpnonce'] ) ) {

                $nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

                if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) )
                die( 'Cheating...' );


                    $trip_title = sanitize_text_field($_POST['wptp_trip_name']);
                    if (empty($trip_title)) {



                        $this->validationMsg[] = esc_html__('Please enter trip name. ', 'ciwpgmp-google-maps');



                    }



                if( isset($_GET['post']) && !empty($_GET['post']) ) {



                    $current_post = get_the_title($_GET['post']);



                    $current_trip = array_search($current_post, $registred_trips);



                    unset($registred_trips[$current_trip]);



                }



                if(in_array($trip_title, $registred_trips)) {



                     $this->validationMsg[] = esc_html__(' Trip already exists!. ', 'ciwpgmp-google-maps');



                }



                if( count($this->validationMsg) > 0 ) {



                    $this->updateTripValidationMsg = $this->validationMsg;



                } else {



                    $post_id = $_POST['post_id'];



                    $post_data = array(



                        'ID'            => $post_id,



                        'post_title'    => $trip_title,



                        'post_status'   => 'publish',



                        'post_author'	=> get_current_user_id()



                    );





                    if( !isset($_POST['locations']) ) {



                        $_POST['locations'] = array();



                    }



                    if(isset($_POST['remaining_locations']) && !empty($_POST['remaining_locations']) ) {



                        $post_locations = array_merge($_POST['locations'], $_POST['remaining_locations']);



                    }else{



                        $post_locations = $_POST['locations'];



                    }



                    wp_update_post( $post_data );



                    update_post_meta( $post_id, '_wptp_trip_locations', $post_locations );



                }

                return $this->updateTripValidationMsg;



            }

        }







        function add_locations_in_trip() {



            if ( isset( $_POST['wptp_add_location_in_trip'] )  ) {



                

                if ( !empty( $_REQUEST['_nonce'] ) ) {



                    $nonce = sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) );



                }



                if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'add-trip-place-nonce' ) ) {



                    die( 'Cheating...' );



                }



                if (empty($_POST['trip_post_id'])) {



                    $this->validationMsg[] = esc_html__(' Please select a trip to add this location. ', 'ciwpgmp-google-maps');



                }



                $old_post_id = false;



                $post_id = $_POST['trip_post_id'];



                $trip_location_id = get_post_meta( $post_id, '_wptp_trip_locations', true );



                $trip_location_ids = maybe_unserialize($trip_location_id);



                if(empty($trip_location_ids))



                $trip_location_ids = array();



                if(is_array($trip_location_ids) && !empty($trip_location_ids) ) {



                    if (in_array(get_the_ID(), $trip_location_ids)) {



                        $old_post_id = true;

                    }



                }



                if($old_post_id) {

                    $this->validationMsg[] = esc_html__(' Location already exists in this trip ', 'ciwpgmp-google-maps');



                } 



                if( count($this->validationMsg) > 0 ) {



                    $this->errorsMessage = $this->validationMsg;



                }else {



                    $p_id= get_the_ID();



                    $trip_location_ids[] = $p_id;



                    update_post_meta( $post_id, '_wptp_trip_locations', $trip_location_ids );



                }

                return $this->errorsMessage;

            }

        }

    }

}