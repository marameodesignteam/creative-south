<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Articles JSON-LD
function seopress_automatic_rich_snippets_articles_option($schema_datas) {
    //if no data
    if (0 != count(array_filter($schema_datas, 'strlen'))) {
        $article_type 					           = $schema_datas['type'];
        $article_title 					          = $schema_datas['title'];
        $article_author 					         = $schema_datas['author'];
        $article_img 					            = $schema_datas['img'];
        $article_coverage_start_date	 = $schema_datas['coverage_start_date'];
        $article_coverage_start_time	 = $schema_datas['coverage_start_time'];
        $article_coverage_end_date 		 = $schema_datas['coverage_end_date'];
        $article_coverage_end_time 		 = $schema_datas['coverage_end_time'];
        $article_speakable 		         = $schema_datas['speakable'];

        $json = [
            '@context'         => seopress_check_ssl() . 'schema.org/',
            '@type'            => $article_type,
            'datePublished'    => get_the_date('c'),
            'dateModified'     => get_the_modified_date('c'),
        ];

        if (function_exists('seopress_rich_snippets_articles_canonical_option') && '' != seopress_rich_snippets_articles_canonical_option()) {
            $json['mainEntityOfPage'] = [
                '@type' => 'WebPage',
                '@id'   => seopress_rich_snippets_articles_canonical_option(),
            ];
        }

        $json['headline'] = $article_title;

        $author = get_the_author();
        if ('' != $article_author) {
            $author = $article_author;
        }

        $json['author'] = [
            '@type' => 'Person',
            'name'  => $author,
        ];

        if ('' != $article_img) {
            $json['image'] = [
                '@type' => 'ImageObject',
                'url'   => $article_img,
            ];
        }

        if (function_exists('seopress_rich_snippets_articles_publisher_option') && '' != seopress_rich_snippets_articles_publisher_option()) {
            $json['publisher'] = [
                '@type' => 'Organization',
                'name'  => seopress_rich_snippets_articles_publisher_option(),
            ];
            if ('' != seopress_rich_snippets_articles_publisher_logo_option()) {
                $json['publisher']['logo'] = [
                    '@type'  => 'ImageObject',
                    'url'    => seopress_rich_snippets_articles_publisher_logo_option(),
                    'width'  => seopress_rich_snippets_articles_publisher_logo_width_option(),
                    'height' => seopress_rich_snippets_articles_publisher_logo_height_option(),
                ];
            }
        }

        if ($article_coverage_start_date && $article_coverage_start_time && 'LiveBlogPosting' == $article_type) {
            $json['coverageStartTime'] = $article_coverage_start_date . 'T' . $article_coverage_start_time;
        }

        if ($article_coverage_end_date && $article_coverage_end_time && 'LiveBlogPosting' == $article_type) {
            $json['coverageEndTime'] = $article_coverage_end_date . 'T' . $article_coverage_end_time;
        }

        if ('ReviewNewsArticle' == $article_type) {
            $json['itemReviewed'] = [
                '@type' => 'Thing',
                'name'  => get_the_title(),
            ];
        }

        $json['description'] = wp_trim_words(esc_html(get_the_excerpt()), 30);

        if ('' != $article_speakable) {
            $json['speakable'] = [
                '@type'       => 'SpeakableSpecification',
                'cssSelector' => $article_speakable,
            ];
        }

        $json = array_filter($json);

        $json = apply_filters('seopress_schemas_auto_article_json', $json);

        $json = '<script type="application/ld+json">' . json_encode($json) . '</script>' . "\n";

        $json = apply_filters('seopress_schemas_auto_article_html', $json);

        echo $json;
    }
}
