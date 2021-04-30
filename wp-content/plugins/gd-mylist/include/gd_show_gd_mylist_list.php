<?php

class gd_show_gd_mylist_list extends gd_mylist_plugin
{

    public function __construct()
    {
        add_action('gd_mylist_list', array($this, 'gd_show_gd_mylist_list'), 11, 2);
        add_shortcode('show_gd_mylist_list', array($this, 'gd_show_gd_mylist_list'), 11, 2);
    }

    public function post_query($user_id)
    {
        $obj = [
            'user_id' => $user_id,
            'table' => $this->var_setting()['table'],
            'table_posts' => $this->var_setting()['table_posts'],
            'table_users' => $this->var_setting()['table_users'],
        ];
        $postsList = new gd_dbQuery();
        return $postsList->postsList($obj);
    }

    public function list_item($post)
    {
        $output = []; 
        $type = 'post_list';
        $postId = $post->posts_id;
        $postAuthorId = $post->authors_id;
        $postAuthorName = $post->authors_name;
        $postTitle = $post->posts_title;
        $portTitleLang = $this->extract_title($postTitle);
        $postAddress = get_field('_wpgmp_location_address',$post->posts_id);
        $postLat = get_field('_wpgmp_metabox_latitude',$post->posts_id);
        $postLong = get_field('_wpgmp_metabox_longitude',$post->posts_id);
        $postDigitalAddress_cs = get_field('digital_adress',$post->posts_id);
        if ((strpos($postDigitalAddress_cs, 'https://') !== false) || (strpos($postDigitalAddress_cs, 'http://') !== false)) {
            $postDigitalAddress = $postDigitalAddress_cs;
        }else{
            $postDigitalAddress = ($postDigitalAddress_cs != '') ? 'https://'.$postDigitalAddress_cs : '';
        }
        $postDescription = get_field('description',$post->posts_id);
        $cateID = get_field('category_id',$post->posts_id);
        $categs = [
	        1 => 'Festivals',
	        2 => 'Galleries & Studios',
	        3 => 'Creative Retail & Markets',
	        4 => 'Public Art',
	        8 => 'Museum & Heritage',
	        9 => 'Music & Performance',
        ];
        $postCategory = $categs[$cateID];
        $mediaAttached = get_attached_media('', $post->posts_id); 
        $num_media = count($mediaAttached); 
        $imageArr = [];
        $videoArr = [];
        $set_first = FALSE;
        foreach($mediaAttached as $item) : 
            $type = $item->post_mime_type;
            if($type == 'video/mp4') {
                $url = $item->guid;
                array_push($videoArr,['type'=>$type,'url'=>$url,'class' => $set_first == FALSE ? 'active' : '']);
            }
            else{
                $url  = wp_get_attachment_image_url($item->ID, 'card-tour');
                array_push($imageArr,['type'=>$type,'url'=>$url,'class' => $set_first == FALSE ? 'active' : '']);
            }
            $set_first = TRUE;
        endforeach;

        $carousel_control = "";
        if($num_media > 1){
            $carousel_control = 
            '<a class="carousel-control-prev" href="#location-slide-'.$post->posts_id.'" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#location-slide-'.$post->posts_id.'" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>';
        }else{
            $carousel_control = '';
        }

        $dont_miss = get_field('dont_miss', $post->posts_id);

        $postUrl = get_permalink($postId);
        $user_id = $this->current_user_id();
        $args = array(
            'styletarget' => 'mylist',
            'item_id' => $postId,
        );

        if (strpos($postTitle, '<!--:') !== false || strpos($postTitle, '[:') !== false) { //means use mqtranlate or qtranlate-x
            $posttitle = $portTitleLang[$lang];
        } else {
            $posttitle = $postTitle;
        }

        $output = [
            'postId' => $postId,
            'posturl' => $postUrl,
            'postimage' => wp_get_attachment_url(get_post_thumbnail_id($postId)),
            'posttitle' => $postTitle,
            'postaddress' => $postAddress,
            'postlat' => $postLat,
            'postlong' => $postLong,
            'postdigitaladdress' => $postDigitalAddress,
            'postcategory' => $postCategory,
            'postmediaimage' => $imageArr,
            'postmediavideo' => $videoArr,
            'postcarouselcontrol' => $carousel_control,
            'postdescription' => $postDescription,
            'postdate' => get_the_date('F j, Y', $postId),
            'postAuthorName' => $postAuthorName,
            'postdontmiss'=> !empty($dont_miss) ? 'dont-miss-item' : 'basic-item',
            'showRemove' => [
                'itemid' => $postId,
                'styletarget' => 'mylist',
                'userid' => $user_id,
                'label' => __('Delete from Trip', 'gd-mylist'),
                'icon' => $this->stored_setting()['fontawesome_btn_remove'],
            ],
        ];

        return $output;
    }

    public function extract_title($postTitle)
    {
        // support for mqtranlate and qtranlate-x
        $titles = null;

        if (strpos($postTitle, '<!--:') !== false) {
            $regexp = '/<\!--:(\w+?)-->([^<]+?)<\!--:-->/i';
        } else {
            $regexp = '/\:(\w{2})\]([^\[]+?)\[/';
        }

        if (preg_match_all($regexp, $postTitle, $matches)) {
            $titles = array();
            $count = count($matches[0]);
            for ($i = 0; $i < $count; ++$i) {
                $titles[$matches[1][$i]] = $matches[2][$i];
            }
        }

        return $titles;
    }

    public function gd_show_gd_mylist_list($atts)
    {
        global $wpdb;
        $posts = null;
        $user_id = $this->current_user_id();
        $locale = get_locale();
        $lang = substr($locale, 0, 2);
        $isShowListPage = true;
        $output = '';
        $listAr = [];
        if (isset($_GET['itinerary'])) {
            $user_id_share = $_GET['itinerary'];
        } else {
            $user_id_share = null;
        }

        //whatsapp get id
        $url = $_SERVER['REQUEST_URI'];
        $arUrl = explode('itinerary_', $url);
        if (isset($arUrl[1])) {
            $user_id_share = $arUrl[1];
        }

        extract(shortcode_atts(array(
            'share_list' => 'yes',
            'show_count' => 'yes',
        ), $atts));

        if ($user_id_share) {
            $user_id = $user_id_share;
        }

        $posts = $this->post_query($user_id);

        if ($posts != null) {
            $listAr['showList'] = true;
            if ($share_list === 'yes') {
                $type = 'share_list';
                $html = '';
                $permalink = get_permalink();
                if (strpos($permalink, '?') !== false) {
                    $pageid = $permalink . '&';
                } else {
                    $pageid = $permalink . '?';
                }
                $listAr['share'] = [
                    'showShare' => true,
                    'share_label' => __('Share your Tour', 'gd-mylist'),
                    'pageid' => $pageid,
                    'userid' => $user_id,
                ];
            }

            if ($show_count === 'yes') {
                $type = 'item_count';
                $html = '';
                $count = $wpdb->num_rows;
                $listAr['count'] = [
                    'showCount' => true,
                    'count_label' => __('Total items', 'gd-mylist'),
                    'count' => $count,
                ];
            }

            foreach ($posts as $post) {
                $listAr['listitem'][$post->posts_id] = $this->list_item($post);
            }

            $output .= '<script type="text/javascript">';
            $output .= 'var myListData = ';
            $output .= json_encode($listAr);
            $output .= '</script>';
        } else {
            $listAr['showEmpty'] = [
                'empty_label' => __("Sorry! Your don't have documents.", 'gd-mylist'),
            ];
            $output .= '<script type="text/javascript">';
            $output .= 'var myListData = ';
            $output .= json_encode($listAr);
            $output .= '</script>';
        }
        $output .= '<div id="myList_list"></div>';
        print($output);
    }

}
