<?php

defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');

add_action( 'action_seopress_action_scheduler_migrate_schema', 'seopress_action_scheduler_migrate_schema', 10, 2 );

function seopress_action_scheduler_migrate_schema($site_id, $batch_id)
{
	error_log("[site_id] : " . $site_id . " [batch_id] : " . $batch_id);

	include_once plugin_dir_path(__FILE__) . '../admin/updater/migrate-schema.php';

	if(function_exists('get_blog_option')){
		$counter = get_blog_option($site_id, '_seopress_migrate_schema_current');
	}
	else{
		$counter = get_option('_seopress_migrate_schema_current');
	}
	if(!$counter){
		$counter = 0;
	}

	if(function_exists('switch_to_blog')){
		switch_to_blog($site_id);
	}

	global $wpdb;

	if($site_id == 1){
		$from = sprintf("%s%s", $wpdb->base_prefix, 'options');
	}
	else{
		$from = sprintf("%s%s_%s", $wpdb->base_prefix, $site_id, 'options');
	}

	$query = 'SELECT * ';
	$query .= "FROM {$from} o ";
	$query .= 'WHERE 1=1 ';
	$query .= "AND o.option_name = '%s' ";

	$batch = $wpdb->get_results(sprintf($query, $batch_id), ARRAY_A);

	if(empty($batch)){
		return;
	}
	$data = maybe_unserialize($batch[0]["option_value"]);
	$post_ids = $data["post_ids"];

	if(empty($post_ids)){

		if(function_exists('restore_current_blog')){
			restore_current_blog();
		}

		if(function_exists('delete_blog_option')){
			delete_blog_option($site_id, $batch_id);
		}
		else{
			delete_option($batch_id);
		}
		return;
	}

	foreach($post_ids as $post_id){
		$counter++;
		error_log("[post_id] : " . $post_id["post_id"]);
		seopress_migrate_schema_by_post_id($post_id["post_id"]);
	}

	if(function_exists('update_blog_option')){
		update_blog_option($site_id, '_seopress_migrate_schema_current', $counter);
	}
	else{
		update_option('_seopress_migrate_schema_current', $counter);
	}


	if(function_exists('restore_current_blog')){
		restore_current_blog();
	}

	if(function_exists('delete_blog_option')){
		delete_blog_option($site_id, $batch_id);
	}
	else{
		delete_option($batch_id);
	}
}
