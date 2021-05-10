<?php

defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');

class WP_SEOPress_Async_Clean_Old_Schema extends WP_SEOPress_Background_Process
{
    /**
     * @var string
     */
	protected $action = 'seopress_clean_old_schema';


    protected function task($item)
    {
        error_log("[Clean data migrate]");

        if(function_exists('switch_to_blog')){
			switch_to_blog($item["site_id"]);
		}

		global $wpdb;

		$query = "DELETE pm ";
		$query .= "FROM {$wpdb->postmeta} pm ";
		$query .= "INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id ";
		$query .= 'WHERE 1=1 ';
		$query .= "AND p.post_type != 'seopress_schemas' ";
		$query .= "AND pm.meta_key LIKE '_seopress_pro_rich_snippets%' ";

        $wpdb->query($query);


        if(function_exists('restore_current_blog')){
			restore_current_blog();
		}

        return false;
    }

    protected function complete()
    {
        parent::complete();
		error_log("[Complete Clean Data]");
    }
}
