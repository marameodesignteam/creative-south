<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Rich Snippets
//=================================================================================================
//Rich Snippets JSON-LD
if ('1' == seopress_rich_snippets_enable_option()) { //Is RS enable
    if (is_single() || is_singular()) {
        //If Disable all automatic schemas doesn't exist, then continue
        if ( ! get_post_meta(get_the_ID(), '_seopress_pro_rich_snippets_disable_all', true)) {
            //Manual option
            function seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace) {
                if ( ! empty($post_meta_key)) {
                    foreach ($post_meta_key as $key => $value) {
                        //Init
                        $_post_meta_value = null;

                        //Single datas
                        if ('opening_hours' == $key) {
                            if ( ! empty($seopress_pro_schemas[0][$id]['rich_snippets_' . $schema_name][$key]) && function_exists('seopress_if_key_exists') && true === seopress_if_key_exists($seopress_pro_schemas[0][$id]['rich_snippets_' . $schema_name][$key], 'open')) {
                                $_post_meta_value = $seopress_pro_schemas[0][$id]['rich_snippets_' . $schema_name][$key];
                            } else {
                                $_post_meta_value = get_post_meta($id, $value, true);
                                $_post_meta_value = $_post_meta_value['seopress_pro_rich_snippets_lb_opening_hours'];
                            }
                        } else {
                            $post_meta_value = get_post_meta($id, $value, true);
                        }

                        //Global datas
                        $manual_global 									= get_post_meta($id, $value . '_manual_global', true);

                        $manual_img_global 								      = get_post_meta($id, $value . '_manual_img_global', true);
                        $manual_img_library_global 						= get_post_meta($id, $value . '_manual_img_library_global', true);

                        $manual_date_global 							= get_post_meta($id, $value . '_manual_date_global', true);

                        $manual_time_global 							= get_post_meta($id, $value . '_manual_time_global', true);

                        $manual_rating_global 							= get_post_meta($id, $value . '_manual_rating_global', true);

                        $manual_custom_global 							= get_post_meta($id, $value . '_manual_custom_global', true);

                        $cf 											= get_post_meta($id, $value . '_cf', true);

                        $tax 											= get_post_meta($id, $value . '_tax', true);

                        $lb 											= get_post_meta($id, $value . '_lb', true);

                        //From current single post
                        if ( ! empty($_post_meta_value) && 7 === count($_post_meta_value)) {
                            $_post_meta_value = $_post_meta_value;
                        } elseif ('manual_single' == $post_meta_value || 'manual_img_single' == $post_meta_value || 'manual_date_single' == $post_meta_value || 'manual_time_single' == $post_meta_value || 'manual_rating_single' == $post_meta_value || 'manual_custom_single' == $post_meta_value) {
                            if (isset($seopress_pro_schemas[0][$id]['rich_snippets_' . $schema_name][$key])) {
                                $_post_meta_value = $seopress_pro_schemas[0][$id]['rich_snippets_' . $schema_name][$key];
                            }
                        } elseif ('manual_global' == $post_meta_value) {
                            if ('' != $manual_global) {
                                $_post_meta_value = $manual_global;
                            }
                        } elseif ('manual_img_global' == $post_meta_value) {
                            if ('' != $manual_img_global) {
                                $_post_meta_value = $manual_img_global;
                            }
                        } elseif ('manual_img_library_global' == $post_meta_value) {
                            if ('' != $manual_img_library_global) {
                                $_post_meta_value = $manual_img_library_global;
                            }
                        } elseif ('manual_date_global' == $post_meta_value) {
                            if ('' != $manual_date_global) {
                                $_post_meta_value = $manual_date_global;
                            }
                        } elseif ('manual_time_global' == $post_meta_value) {
                            if ('' != $manual_time_global) {
                                $_post_meta_value = $manual_time_global;
                            }
                        } elseif ('manual_rating_global' == $post_meta_value) {
                            if ('' != $manual_rating_global) {
                                $_post_meta_value = $manual_rating_global;
                            }
                        } elseif ('manual_custom_global' == $post_meta_value) {
                            if ('' != $manual_custom_global) {
                                $_post_meta_value = $manual_custom_global;
                            }
                        } elseif ('manual_lb_global' == $post_meta_value) {
                            if ('' != $lb) {
                                $_post_meta_value = $lb;
                            }
                        } elseif ('custom_fields' == $post_meta_value) {
                            if ('' != $cf) {
                                $_post_meta_value = get_post_meta(get_the_ID(), $cf, true);
                            }
                        } elseif ('custom_taxonomy' == $post_meta_value) {
                            if ('' != $tax) {
                                $_post_meta_value ='';
                                if (taxonomy_exists($tax)) {
                                    $terms = wp_get_post_terms(get_the_ID(), $tax, ['fields' => 'names']);
                                    if ( ! empty($terms) && ! is_wp_error($terms)) {
                                        $_post_meta_value = $terms[0];
                                    }
                                }
                            }
                        } elseif ('none' != $post_meta_value) { //From schema single post
                            $_post_meta_value = str_replace($sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace, $post_meta_value);
                        }

                        //Push value to array
                        $schema_datas[$key] = $_post_meta_value;
                    }

                    return $schema_datas;
                }
            }

            //Articles JSON-LD
            function seopress_automatic_rich_snippets_articles_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $article_type 					          = $schema_datas['type'];
                    $article_title 					         = $schema_datas['title'];
                    $article_img 					           = $schema_datas['img'];
                    $article_coverage_start_date	= $schema_datas['coverage_start_date'];
                    $article_coverage_start_time	= $schema_datas['coverage_start_time'];
                    $article_coverage_end_date 		= $schema_datas['coverage_end_date'];
                    $article_coverage_end_time 		= $schema_datas['coverage_end_time'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org",';
                    if ('' != $article_type) {
                        $html .= '"@type": ' . json_encode($article_type) . ',';
                    }
                    if (function_exists('seopress_rich_snippets_articles_canonical_option') && '' != seopress_rich_snippets_articles_canonical_option()) {
                        $html .= '"mainEntityOfPage": {
									"@type": "WebPage",
									"@id": ' . json_encode(seopress_rich_snippets_articles_canonical_option()) . '
								},';
                    }
                    if ('' != $article_title) {
                        $html .= '"headline": ' . json_encode($article_title) . ',';
                    }
                    if ('' != $article_img) {
                        $html .= '"image": {
									"@type": "ImageObject",
									"url": ' . json_encode($article_img) . '
								},';
                    }
                    $html .= '"datePublished": "' . get_the_date('c') . '",
							"dateModified": ' . json_encode(get_the_modified_date('c')) . ',
							"author": {
								"@type": "Person",
								"name": ' . json_encode(get_the_author()) . '
							},';

                    if (function_exists('seopress_rich_snippets_articles_publisher_option') && '' != seopress_rich_snippets_articles_publisher_option()) {
                        $html .= '"publisher": {
									"@type": "Organization",
									"name": ' . json_encode(seopress_rich_snippets_articles_publisher_option()) . ',';
                        if ('' != seopress_rich_snippets_articles_publisher_logo_option()) {
                            $html .= '"logo": {
											"@type": "ImageObject",
											"url": ' . json_encode(seopress_rich_snippets_articles_publisher_logo_option()) . ',
											"width": ' . json_encode(seopress_rich_snippets_articles_publisher_logo_width_option()) . ',
											"height": ' . json_encode(seopress_rich_snippets_articles_publisher_logo_height_option()) . '
										}';
                        }
                        $html .= '},';
                    }

                    if ($article_coverage_start_date && $article_coverage_start_time && 'LiveBlogPosting' == $article_type) {
                        $html .= '"coverageStartTime": "' . $article_coverage_start_date . 'T' . $article_coverage_start_time . '",';
                    }

                    if ($article_coverage_end_date && $article_coverage_end_time && 'LiveBlogPosting' == $article_type) {
                        $html .= '"coverageEndTime": "' . $article_coverage_end_date . 'T' . $article_coverage_end_time . '",';
                    }

                    if ('ReviewNewsArticle' == $article_type) {
                        $html .= '"itemReviewed": {"@type": "Thing", "name":"' . get_the_title() . '"},';
                    }

                    $html .= '"description": ' . json_encode(wp_trim_words(esc_html(get_the_excerpt()), 30));
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_article_html', $html);

                    echo $html;
                }
            }

            //Local Business JSON-LD
            function seopress_automatic_rich_snippets_lb_option($schema_datas) {
                $lb_name 							        = $schema_datas['name'];
                $lb_type 							        = $schema_datas['type'];
                $lb_img 							         = $schema_datas['img'];
                $lb_street_addr 					   = $schema_datas['street_addr'];
                $lb_city 							        = $schema_datas['city'];
                $lb_state 							       = $schema_datas['state'];
                $lb_pc 								         = $schema_datas['pc'];
                $lb_country 						      = $schema_datas['country'];
                $lb_lat 							         = $schema_datas['lat'];
                $lb_lon 							         = $schema_datas['lon'];
                $lb_website 						      = $schema_datas['website'];
                $lb_tel 							         = $schema_datas['tel'];
                $lb_price 							       = $schema_datas['price'];
                $lb_serves_cuisine 					= $schema_datas['serves_cuisine'];
                $lb_opening_hours 					 = $schema_datas['opening_hours'];

                if ('' != $lb_img) {
                    $lb_img = json_encode($lb_img);
                }

                if ('' != $lb_name) {
                    $lb_name = json_encode($lb_name);
                }

                if ('' != $lb_type) {
                    $lb_type = json_encode($lb_type);
                } else {
                    $lb_type = 'LocalBusiness';
                }

                if ('' != $lb_street_addr) {
                    $lb_street_addr = json_encode($lb_street_addr);
                }

                if ('' != $lb_city) {
                    $lb_city = json_encode($lb_city);
                }

                if ('' != $lb_state) {
                    $lb_state = json_encode($lb_state);
                }

                if ('' != $lb_pc) {
                    $lb_pc = json_encode($lb_pc);
                }

                if ('' != $lb_country) {
                    $lb_country = json_encode($lb_country);
                }

                if ('' != $lb_lat) {
                    $lb_lat = json_encode($lb_lat);
                }

                if ('' != $lb_lon) {
                    $lb_lon = json_encode($lb_lon);
                }

                if ('' != $lb_website) {
                    $lb_website = json_encode($lb_website);
                }

                if ('' != $lb_tel) {
                    $lb_tel = json_encode($lb_tel);
                }

                if ('' != $lb_price) {
                    $lb_price = json_encode($lb_price);
                }

                if ('' != $lb_serves_cuisine) {
                    $lb_serves_cuisine = json_encode($lb_serves_cuisine);
                }

                if ('' != $lb_opening_hours) {
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                    $seopress_pro_rich_snippets_lb_opening_hours_option ='';

                    foreach ($lb_opening_hours as $key => $day) {//DAY
                        if ( ! array_key_exists('open', $day)) {//CLOSED?
                            foreach ($day as $keys => $ampm) {//AM/PM
                                if (array_key_exists('open', $ampm)) {//OPEN?
                                    $seopress_pro_rich_snippets_lb_opening_hours_option .= '{ ';
                                    $seopress_pro_rich_snippets_lb_opening_hours_option .= '"@type": "OpeningHoursSpecification",';
                                    $seopress_pro_rich_snippets_lb_opening_hours_option .= '"dayOfWeek": "' . $days[$key] . '", ';

                                    foreach ($ampm as $_key => $value) {//HOURS
                                        if ('start' == $_key) {//START AM/PM
                                            $seopress_pro_rich_snippets_lb_opening_hours_option .= '"opens": "';
                                            foreach ($value as $__key => $time) {
                                                $seopress_pro_rich_snippets_lb_opening_hours_option .= $time;
                                                if ('hours' == $__key) {
                                                    $seopress_pro_rich_snippets_lb_opening_hours_option .= ':';
                                                }
                                            }
                                            $seopress_pro_rich_snippets_lb_opening_hours_option .= '",';
                                        }
                                        if ('end' == $_key) {//CLOSE AM/PM
                                            $seopress_pro_rich_snippets_lb_opening_hours_option .= '"closes": "';
                                            foreach ($value as $__key => $time) {
                                                $seopress_pro_rich_snippets_lb_opening_hours_option .= $time;
                                                if ('hours' == $__key) {
                                                    $seopress_pro_rich_snippets_lb_opening_hours_option .= ':';
                                                }
                                            }
                                            $seopress_pro_rich_snippets_lb_opening_hours_option .= '"';
                                        }
                                    }

                                    $seopress_pro_rich_snippets_lb_opening_hours_option .= '|';
                                }
                            }
                        }
                    }
                }

                $html = '<script type="application/ld+json">';
                $html .= '{"@context" : "' . seopress_check_ssl() . 'schema.org","@type" : ' . $lb_type . ',';
                if (isset($lb_img)) {
                    $html .= '"image": ' . $lb_img . ', ';
                }
                if (function_exists('seopress_pro_rich_snippets_lb_id_option') && '' != seopress_pro_rich_snippets_lb_id_option()) {
                    $html .= '"@id": ' . json_encode(seopress_pro_rich_snippets_lb_id_option()) . ',';
                }

                if (isset($lb_street_addr) || isset($lb_city) || isset($lb_state) || isset($lb_pc) || isset($lb_country)) {
                    $html .= '"address": {
						"@type": "PostalAddress",';
                    if (isset($lb_street_addr)) {
                        $html .= '"streetAddress": ' . $lb_street_addr . ',';
                    }
                    if (isset($lb_city)) {
                        $html .= '"addressLocality": ' . $lb_city . ',';
                    }
                    if (isset($lb_state)) {
                        $html .= '"addressRegion": ' . $lb_state . ',';
                    }
                    if (isset($lb_pc)) {
                        $html .= '"postalCode": ' . $lb_pc . ',';
                    }
                    if (isset($lb_country)) {
                        $html .= '"addressCountry": ' . $lb_country;
                    }
                    $html .= '},';
                }

                if (isset($lb_lat) || isset($lb_lon)) {
                    $html .= '"geo": {
						"@type": "GeoCoordinates",';
                    if (isset($lb_lat)) {
                        $html .= '"latitude": ' . $lb_lat . ',';
                    }
                    if (isset($lb_lon)) {
                        $html .= '"longitude": ' . $lb_lon;
                    }
                    $html .= '},';
                }

                if (isset($lb_website)) {
                    $html .= '"url": ' . $lb_website . ',';
                }

                if (isset($lb_tel)) {
                    $html .= '"telephone": ' . $lb_tel . ',';
                }

                if (isset($lb_price)) {
                    $html .= '"priceRange": ' . $lb_price . ',';
                }

                if (isset($lb_serves_cuisine) &&
                    (
                        '"FoodEstablishment"' == $lb_type
                        || '"Bakery"' == $lb_type
                        || '"BarOrPub"' == $lb_type
                        || '"Brewery"' == $lb_type
                        || '"CafeOrCoffeeShop"' == $lb_type
                        || '"FastFoodRestaurant"' == $lb_type
                        || '"IceCreamShop"' == $lb_type
                        || '"Restaurant"' == $lb_type
                        || '"Winery"' == $lb_type
                    )
                ) {
                    $html .= '"servesCuisine": ' . $lb_serves_cuisine . ',';
                }

                if (isset($seopress_pro_rich_snippets_lb_opening_hours_option)) {
                    $html .= '"openingHoursSpecification": [';

                    $explode              = array_filter(explode('|', $seopress_pro_rich_snippets_lb_opening_hours_option));
                    $seopress_comma_count = count($explode);
                    for ($i = 0; $i < $seopress_comma_count; ++$i) {
                        $html .= $explode[$i];
                        if ($i < ($seopress_comma_count - 1)) {
                            $html .= '}, ';
                        } else {
                            $html .= '} ';
                        }
                    }

                    $html .= '],';
                }
                if (isset($lb_name)) {
                    $html .= '"name": ' . $lb_name;
                } else {
                    $html .= '"name": "' . get_bloginfo('name') . '"';
                }
                $html = trim($html, ',');
                $html .= '}';
                $html .= '</script>';
                $html .= "\n";

                $html = apply_filters('seopress_schemas_auto_lb_html', $html);

                echo $html;
            }

            //FAQ JSON-LD
            function seopress_automatic_rich_snippets_faq_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $faq_q 							= $schema_datas['q'];
                    $faq_a 							= $schema_datas['a'];
                    if (('' != $faq_q) && ('' != $faq_a)) {
                        $html = '<script type="application/ld+json">';
                        $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org",
							"@type": "FAQPage",
							"name": "FAQ","mainEntity": [{"@type": "Question","name": ' . json_encode($faq_q) . ',"answerCount": 1,"acceptedAnswer": {"@type": "Answer","text": ' . json_encode($faq_a) . '}}]}';
                        $html .= '</script>';
                        $html .= "\n";

                        $html = apply_filters('seopress_schemas_auto_faq_html', $html);

                        echo $html;
                    }
                }
            }

            //Courses JSON-LD
            function seopress_automatic_rich_snippets_courses_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $courses_title 							 = $schema_datas['title'];
                    $courses_desc 							  = $schema_datas['desc'];
                    $courses_school 						 = $schema_datas['school'];
                    $courses_website 						= $schema_datas['website'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org",
							"@type": "Course",';
                    if ('' != $courses_title) {
                        $html .= '"name": ' . json_encode($courses_title) . ',';
                    }
                    if ('' != $courses_desc) {
                        $html .= '"description": ' . json_encode($courses_desc) . ',';
                    }
                    if ('' != $courses_school) {
                        $html .= '"provider": {
									"@type": "Organization",
									"name": ' . json_encode($courses_school) . ',
									"sameAs": ' . json_encode($courses_website) . '
								}';
                    }
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_course_html', $html);

                    echo $html;
                }
            }

            //Recipes JSON-LD
            function seopress_automatic_rich_snippets_recipes_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $recipes_name 							      = $schema_datas['name'];
                    $recipes_desc 							      = $schema_datas['desc'];
                    $recipes_cat 							       = $schema_datas['cat'];
                    $recipes_img 							       = $schema_datas['img'];
                    $recipes_prep_time 						  = $schema_datas['prep_time'];
                    $recipes_cook_time 						  = $schema_datas['cook_time'];
                    $recipes_calories 						   = $schema_datas['calories'];
                    $recipes_yield 							     = $schema_datas['yield'];
                    $recipes_keywords 						   = $schema_datas['keywords'];
                    $recipes_cuisine 						    = $schema_datas['cuisine'];
                    $recipes_ingredient 					  = $schema_datas['ingredient'];
                    $recipes_instructions 					= $schema_datas['instructions'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org/",';
                    $html .= '"@type": "Recipe",';

                    if ('' != $recipes_name) {
                        $html .= '"name": ' . json_encode($recipes_name) . ',';
                    }
                    if ('' != $recipes_cat) {
                        $html .= '"recipeCategory": ' . json_encode($recipes_cat) . ',';
                    }
                    if ('' != $recipes_img) {
                        $html .= '"image": ' . json_encode($recipes_img) . ',';
                    }
                    if (get_the_author()) {
                        $html .= '"author": {
									"@type": "Person",
									"name": ' . json_encode(get_the_author()) . '
								},';
                    }
                    if (get_the_date()) {
                        $html .= '"datePublished": "' . get_the_date('Y-m-j') . '",';
                    }
                    if ('' != $recipes_desc) {
                        $html .= '"description": ' . json_encode($recipes_desc) . ',';
                    }
                    if ($recipes_prep_time) {
                        $html .= '"prepTime": ' . json_encode('PT' . $recipes_prep_time . 'M') . ',';
                    }
                    if ('' != $recipes_cook_time) {
                        $html .= '"totalTime": ' . json_encode('PT' . $recipes_cook_time . 'M') . ',';
                    }
                    if ('' != $recipes_yield) {
                        $html .= '"recipeYield": ' . json_encode($recipes_yield) . ',';
                    }
                    if ('' != $recipes_keywords) {
                        $html .= '"keywords": ' . json_encode($recipes_keywords) . ',';
                    }
                    if ('' != $recipes_cuisine) {
                        $html .= '"recipeCuisine": ' . json_encode($recipes_cuisine) . ',';
                    }
                    if ('' != $recipes_ingredient) {
                        $recipes_ingredient = preg_split('/\r\n|[\r\n]/', $recipes_ingredient);
                        if ( ! empty($recipes_ingredient)) {
                            $i     = '0';
                            $count = count($recipes_ingredient);

                            $html .= '"recipeIngredient": [';
                            foreach ($recipes_ingredient as $value) {
                                $html .= json_encode($value);
                                if ($i < $count - 1) {
                                    $html .= ',';
                                }
                                ++$i;
                            }
                            $html .= '],';
                        }
                    }
                    if ('' != $recipes_instructions) {
                        $recipes_instructions = preg_split('/\r\n|[\r\n]/', $recipes_instructions);
                        if ( ! empty($recipes_instructions)) {
                            $i     = '0';
                            $count = count($recipes_instructions);

                            $html .= '"recipeInstructions": [';
                            foreach ($recipes_instructions as $value) {
                                $html .= '{"@type": "HowToStep","text":' . json_encode($value) . '}';
                                if ($i < $count - 1) {
                                    $html .= ',';
                                }
                                ++$i;
                            }
                            $html .= '],';
                        }
                    }
                    if ('' != $recipes_calories) {
                        $html .= '"nutrition": {
									"@type": "NutritionInformation",
									"calories": ' . json_encode($recipes_calories) . '
								}';
                    }
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_recipe_html', $html);

                    echo $html;
                }
            }

            //Jobs JSON-LD
            function seopress_automatic_rich_snippets_jobs_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $jobs_name 							           = $schema_datas['name'];
                    $jobs_desc 							           = $schema_datas['desc'];
                    $jobs_date_posted 					      = $schema_datas['date_posted'];
                    $jobs_valid_through 				     = $schema_datas['valid_through'];
                    $jobs_employment_type 				   = $schema_datas['employment_type'];
                    $jobs_identifier_name 				   = $schema_datas['identifier_name'];
                    $jobs_identifier_value 				  = $schema_datas['identifier_value'];
                    $jobs_hiring_organization 			= $schema_datas['hiring_organization'];
                    $jobs_hiring_same_as 				    = $schema_datas['hiring_same_as'];
                    $jobs_hiring_logo 					      = $schema_datas['hiring_logo'];
                    $jobs_hiring_logo_width 			  = $schema_datas['hiring_logo_width'];
                    $jobs_hiring_logo_height 			 = $schema_datas['hiring_logo_height'];
                    $jobs_address_street 				    = $schema_datas['address_street'];
                    $jobs_address_locality 				  = $schema_datas['address_locality'];
                    $jobs_address_region 				    = $schema_datas['address_region'];
                    $jobs_postal_code 					      = $schema_datas['postal_code'];
                    $jobs_country 						         = $schema_datas['country'];
                    $jobs_remote 						          = $schema_datas['remote'];
                    $jobs_salary 						          = $schema_datas['salary'];
                    $jobs_salary_currency 				   = $schema_datas['salary_currency'];
                    $jobs_salary_unit 					      = $schema_datas['salary_unit'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org/",';
                    $html .= '"@type": "JobPosting",';

                    if ('' != $jobs_name) {
                        $html .= '"title": ' . json_encode($jobs_name) . ',';
                    }
                    if ('' != $jobs_desc) {
                        $html .= '"description": ' . json_encode($jobs_desc) . ',';
                    }
                    if ('' != $jobs_identifier_name && '' != $jobs_identifier_value) {
                        $html .= '"identifier": {
									"@type": "PropertyValue",
									"name": ' . json_encode($jobs_identifier_name) . ',
									"value": ' . json_encode($jobs_identifier_value) . '
								},';
                    }
                    if ('' != $jobs_date_posted) {
                        $html .= '"datePosted" : ' . json_encode($jobs_date_posted) . ',';
                    }
                    if ('' != $jobs_valid_through) {
                        $html .= '"validThrough" : ' . json_encode($jobs_valid_through) . ',';
                    }
                    if ('' != $jobs_employment_type) {
                        $html .= '"employmentType" : ' . json_encode($jobs_employment_type) . ',';
                    }
                    if ('' != $jobs_hiring_organization && '' != $jobs_hiring_same_as && '' != $jobs_hiring_logo) {
                        $html .= '"hiringOrganization" : {
									"@type" : "Organization",
									"name" : ' . json_encode($jobs_hiring_organization) . ',
									"sameAs" : ' . json_encode($jobs_hiring_same_as) . ',
									"logo" : ' . json_encode($jobs_hiring_logo) . '
								},';
                    }
                    if ('' != $jobs_address_street || '' != $jobs_address_locality || '' != $jobs_address_region || '' != $jobs_postal_code || '' != $jobs_country) {
                        $html .= '"jobLocation": {
									"@type": "Place",
										"address": {
										"@type": "PostalAddress",';
                        if ('' != $jobs_address_street) {
                            $html .= '"streetAddress": ' . json_encode($jobs_address_street) . ',';
                        }
                        if ('' != $jobs_address_locality) {
                            $html .= '"addressLocality": ' . json_encode($jobs_address_locality) . ',';
                        }
                        if ('' != $jobs_address_region) {
                            $html .= '"addressRegion": ' . json_encode($jobs_address_region) . ',';
                        }
                        if ('' != $jobs_postal_code) {
                            $html .= '"postalCode": ' . json_encode($jobs_postal_code) . ',';
                        }
                        if ('' != $jobs_country) {
                            $html .= '"addressCountry": ' . json_encode($jobs_country);
                        }
                        $html .= '}
									},';
                    }
                    if ('' != $jobs_remote && '' != $jobs_country) {
                        $html .= '"jobLocationType": "TELECOMMUTE",';
                    }
                    if ('' != $jobs_salary && '' != $jobs_salary_currency && '' != $jobs_salary_unit) {
                        $html .= '"baseSalary": {
									"@type": "MonetaryAmount",
									"currency": ' . json_encode($jobs_salary_currency) . ',
									"value": {
									"@type": "QuantitativeValue",
									"value": ' . json_encode($jobs_salary) . ',
									"unitText": ' . json_encode($jobs_salary_unit) . '
									}
								}';
                    }
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_job_html', $html);

                    echo $html;
                }
            }

            //Videos JSON-LD
            function seopress_automatic_rich_snippets_videos_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $videos_name 							     = $schema_datas['name'];
                    $videos_description 					= $schema_datas['description'];
                    $videos_img 							      = $schema_datas['img'];
                    $videos_duration 						  = $schema_datas['duration'];
                    $videos_url 							      = $schema_datas['url'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org",
							"@type": "VideoObject",';
                    if ('' != $videos_name) {
                        $html .= '"name": ' . json_encode($videos_name) . ',';
                    }
                    if ('' != $videos_description) {
                        $html .= '"description": ' . json_encode($videos_description) . ',';
                    }
                    if ('' != $videos_img) {
                        $html .= '"thumbnailUrl": ' . json_encode($videos_img) . ',';
                    }
                    if (get_the_date()) {
                        $html .= '"uploadDate": "' . get_the_date('c') . '",';
                    }
                    if ('' != $videos_duration) {
                        $time   = explode(':', $videos_duration);
                        $sec 	  = isset($time[2]) ? $time[2] : 00;
                        $min 	  = ($time[0] * 60.0 + $time[1] * 1.0);

                        $html .= '"duration": ' . json_encode('PT' . $min . 'M' . $sec . 'S') . ',';
                    }
                    if ('' != seopress_rich_snippets_videos_publisher_option()) {
                        $html .= '"publisher": {
									"@type": "Organization",
									"name": ' . json_encode(seopress_rich_snippets_videos_publisher_option()) . ',
									"logo": {
										"@type": "ImageObject",
										"url": ' . json_encode(seopress_rich_snippets_videos_publisher_logo_option()) . '
									}
								},';
                    }
                    if ('' != $videos_url) {
                        $html .= '"contentUrl": ' . json_encode($videos_url) . ',
								"embedUrl": ' . json_encode($videos_url) . '';
                    }
                    $html .= '}';
                    $html = trim($html, ',');
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_video_html', $html);

                    echo $html;
                }
            }

            //Events JSON-LD
            function seopress_automatic_rich_snippets_events_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    //Init
                    global $post;

                    $events_type 							              = $schema_datas['type'];
                    $events_name 							              = $schema_datas['name'];
                    $events_desc 							              = $schema_datas['desc'];
                    $events_img 							               = $schema_datas['img'];
                    $events_start_date 						         = $schema_datas['start_date'];
                    $events_start_time 						         = $schema_datas['start_time'];
                    $events_end_date 						           = $schema_datas['end_date'];
                    $events_end_time 						           = $schema_datas['end_time'];
                    $events_previous_start_date 			   = $schema_datas['previous_start_date'];
                    $events_previous_start_time 			   = $schema_datas['previous_start_time'];
                    $events_location_name 					       = $schema_datas['location_name'];
                    $events_location_url 					        = $schema_datas['location_url'];
                    $events_location_address 				     = $schema_datas['location_address'];
                    $events_offers_name 					         = $schema_datas['offers_name'];
                    $events_offers_cat 						         = $schema_datas['offers_cat'];
                    $events_offers_price 					        = $schema_datas['offers_price'];
                    $events_offers_price_currency 			 = $schema_datas['offers_price_currency'];
                    $events_offers_availability 			   = $schema_datas['offers_availability'];
                    $events_offers_valid_from_date 			= $schema_datas['offers_valid_from_date'];
                    $events_offers_valid_from_time 			= $schema_datas['offers_valid_from_time'];
                    $events_offers_url 						         = $schema_datas['offers_url'];
                    $events_performer 						          = $schema_datas['performer'];
                    $events_status 							            = $schema_datas['status'];
                    $event_attendance_mode 					      = $schema_datas['attendance_mode'];

                    if ('events_start_date' === $events_start_date && 'events_start_time' === $events_start_time) {
                        if (get_post_meta($post->ID, '_EventStartDateUTC', true)) {
                            $events_start_date = get_post_meta($post->ID, '_EventStartDateUTC', true);
                        } elseif (get_post_meta($post->ID, '_EventStartDate', true)) {
                            $events_start_date = get_post_meta($post->ID, '_EventStartDate', true);
                        }
                        $events_start_date = explode(' ', $events_start_date);
                        $events_start_date = $events_start_date[0] . 'T' . $events_start_date[1];
                    } elseif ('' != $events_start_date && '' != $events_start_time) {
                        $events_start_date = $events_start_date . 'T' . $events_start_time;
                    }

                    if ('events_end_date' === $events_end_date && 'events_end_time' === $events_end_time) {
                        if (get_post_meta($post->ID, '_EventEndDateUTC', true)) {
                            $events_end_date = get_post_meta($post->ID, '_EventEndDateUTC', true);
                        } elseif (get_post_meta($post->ID, '_EventEndDate', true)) {
                            $events_end_date = get_post_meta($post->ID, '_EventEndDate', true);
                        }
                        $events_end_date = explode(' ', $events_end_date);
                        $events_end_date = $events_end_date[0] . 'T' . $events_end_date[1];
                    } elseif ('' != $events_end_date && '' != $events_end_time) {
                        $events_end_date = $events_end_date . 'T' . $events_end_time;
                    }

                    if ('' != $events_previous_start_date && '' != $events_previous_start_time) {
                        $events_previous_start_date = $events_previous_start_date . 'T' . $events_previous_start_time;
                    }

                    if ('' != $events_offers_valid_from_date && '' != $events_offers_valid_from_time) {
                        $events_offers_valid_from_date = $events_offers_valid_from_date . 'T' . $events_offers_valid_from_time;
                    }

                    if ('' != $events_status) {
                        $events_status = seopress_check_ssl() . 'schema.org/' . $events_status;
                    }

                    if ('events_cost' === $events_offers_price) {
                        if (get_post_meta($post->ID, '_EventCost', true)) {
                            $events_offers_price = get_post_meta($post->ID, '_EventCost', true);
                        }
                    }

                    if ('events_currency' === $events_offers_price_currency) {
                        if (get_post_meta($post->ID, '_EventCurrencySymbol', true)) {
                            $events_offers_price_currency = get_post_meta($post->ID, '_EventCurrencySymbol', true);
                        }
                    }

                    if ('events_location_name' === $events_location_name) {
                        if (get_the_title(get_post_meta($post->ID, '_EventVenueID', true))) {
                            $events_location_name = get_the_title(get_post_meta($post->ID, '_EventVenueID', true));
                        }
                    }

                    if ('events_website' === $events_location_url) {
                        if (get_post_meta($post->ID, '_EventURL', true)) {
                            $events_location_url = get_post_meta($post->ID, '_EventURL', true);
                        }
                    }

                    if ('events_location_address' === $events_location_address) {
                        $event_id                = get_post_meta($post->ID, '_EventVenueID', true);
                        $events_location_address = [];
                        if (get_post_meta($event_id, '_VenueAddress', true)) {
                            $events_location_address[] = get_post_meta($event_id, '_VenueAddress', true);
                        }
                        if (get_post_meta($event_id, '_VenueCity', true)) {
                            $events_location_address[] = get_post_meta($event_id, '_VenueCity', true);
                        }
                        if (get_post_meta($event_id, '_VenueProvince', true)) {
                            $events_location_address[] = get_post_meta($event_id, '_VenueProvince', true);
                        }
                        if (get_post_meta($event_id, '_VenueStateProvince', true)) {
                            $events_location_address[] = get_post_meta($event_id, '_VenueStateProvince', true);
                        }
                        if (get_post_meta($event_id, '_VenueZip', true)) {
                            $events_location_address[] = get_post_meta($event_id, '_VenueZip', true);
                        }
                        if (get_post_meta($event_id, '_VenueCountry', true)) {
                            $events_location_address[] = get_post_meta($event_id, '_VenueCountry', true);
                        }
                        if ( ! empty($events_location_address)) {
                            $events_location_address = implode(', ', $events_location_address);
                        }
                    }

                    $html = '<script type="application/ld+json">';
                    $html .= '{
							"@context": "' . seopress_check_ssl() . 'schema.org",';
                    if ('' != $events_type) {
                        $html .= '"@type": ' . json_encode($events_type) . ',';
                    }
                    if ('' != $events_name) {
                        $html .= '"name": ' . json_encode($events_name) . ',';
                    }
                    if ('' != $events_desc) {
                        $html .= '"description": ' . json_encode($events_desc) . ',';
                    }
                    if ('' != $events_img) {
                        $html .= '"image": ' . json_encode($events_img) . ',';
                    }
                    if ('' != $events_location_url) {
                        $html .= '"url": ' . json_encode($events_location_url) . ',';
                    }
                    if ('' != $events_start_date) {
                        $html .= '"startDate": ' . json_encode($events_start_date) . ',';
                    }
                    if ('' != $events_end_date) {
                        $html .= '"endDate": ' . json_encode($events_end_date) . ',';
                    }
                    if ($events_status == seopress_check_ssl() . 'schema.org/EventRescheduled' && '' != $events_previous_start_date) {
                        $html .= '"previousStartDate": ' . json_encode($events_previous_start_date) . ',';
                    }
                    if ('' != $events_status && 'none' != $events_status) {
                        $html .= '"eventStatus": ' . json_encode($events_status) . ',';
                    }
                    if ('' != $event_attendance_mode && 'none' != $event_attendance_mode) {
                        if (
                                    ('OnlineEventAttendanceMode' == $event_attendance_mode && '' != $events_location_url)
                                    ||
                                    ('MixedEventAttendanceMode' == $event_attendance_mode && '' != $events_location_url)
                                ) {
                            $html .= '"eventAttendanceMode": ' . json_encode($event_attendance_mode) . ',';
                        }
                    }
                    if ('' != $events_location_name && '' != $events_location_address) {
                        if ('OnlineEventAttendanceMode' == $event_attendance_mode && '' != $events_location_url) {
                            $html .= '"location": {
										"@type":"VirtualLocation",
										"url": ' . json_encode($events_location_url) . '
									},';
                        } elseif ('MixedEventAttendanceMode' == $event_attendance_mode && '' != $events_location_url) {
                            $html .= '"location": [{
										"@type":"VirtualLocation",
										"url": ' . json_encode($events_location_url) . '
									},
									{
										"@type": "Place",
										"name": ' . json_encode($events_location_name) . ',
										"address": ' . json_encode($events_location_address) . '
									}],';
                        } else {
                            $html .= '"location": {
										"@type": "Place",
										"name": ' . json_encode($events_location_name) . ',
										"address": ' . json_encode($events_location_address) . '
									},';
                        }
                    }
                    if ('' != $events_offers_name) {
                        $sp_offers = '"offers": [{
									"@type": "Offer",
									"name": ' . json_encode($events_offers_name) . ',';
                        if ('' != $events_offers_cat) {
                            $sp_offers .= '"category": ' . json_encode($events_offers_cat) . ',';
                        }
                        if ('' != $events_offers_price) {
                            $sp_offers .= '"price": ' . json_encode($events_offers_price) . ',';
                        }
                        if ('' != $events_offers_price_currency) {
                            $sp_offers .= '"priceCurrency": ' . json_encode($events_offers_price_currency) . ',';
                        }
                        if ('' != $events_offers_url) {
                            $sp_offers .= '"url": ' . json_encode($events_offers_url) . ',';
                        }
                        if ('' != $events_offers_availability) {
                            $sp_offers .= '"availability": ' . json_encode($events_offers_availability) . ',';
                        }
                        if ('' != $events_offers_valid_from_date) {
                            $sp_offers .= '"validFrom": ' . json_encode($events_offers_valid_from_date);
                        }
                        $sp_offers = trim($sp_offers, ',');
                        if ('' != $events_performer) {
                            $sp_offers .= '}],';
                        } else {
                            $sp_offers .= '}]';
                        }
                        $html .= $sp_offers;
                    }
                    if ('' != $events_performer) {
                        $html .= '"performer": {
									"@type": "Person",
									"name": ' . json_encode($events_performer) . '
								}';
                    }
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_event_html', $html);

                    echo $html;
                }
            }

            //Products JSON-LD
            function seopress_automatic_rich_snippets_products_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    //Init
                    global $post;
                    global $product;

                    $products_name 							= $schema_datas['name'];
                    if ('' == $products_name) {
                        $products_name = the_title_attribute('echo=0');
                    }

                    $products_description 					= $schema_datas['description'];
                    if ('' == $products_description) {
                        $products_description = wp_trim_words(esc_html(get_the_excerpt()), 30);
                    }

                    $products_img 							= $schema_datas['img'];
                    if ('' == $products_img && '' != get_the_post_thumbnail_url(get_the_ID(), 'large')) {
                        $products_img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    }

                    $products_price 						= $schema_datas['price'];
                    if (isset($product) && '' == $products_price && method_exists($product, 'get_price') && '' != $product->get_price()) {
                        $products_price = $product->get_price();
                    }

                    $products_price_valid_date 				= $schema_datas['price_valid_date'];

                    if (isset($product) && '' == $products_price_valid_date && method_exists($product, 'get_date_on_sale_to') && '' != $product->get_date_on_sale_to()) {
                        $products_price_valid_date = $product->get_date_on_sale_to();
                        $products_price_valid_date = $products_price_valid_date->date('m-d-Y');
                    }

                    $products_sku 							= $schema_datas['sku'];
                    if (isset($product) && '' == $products_sku && method_exists($product, 'get_sku') && '' != $product->get_sku()) {
                        $products_sku = $product->get_sku();
                    }

                    $products_global_ids 						= $schema_datas['global_ids'];

                    if (isset($product) && '' == $products_global_ids && method_exists($product, 'get_id') && '' != get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true) && 'none' != get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true)) {
                        $products_global_ids = get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true);
                    }

                    $products_global_ids_value 						= $schema_datas['global_ids_value'];

                    if (isset($product) && '' == $products_global_ids_value && method_exists($product, 'get_id') && '' != get_post_meta($product->get_id(), 'sp_wc_barcode_field', true) && 'none' != get_post_meta($product->get_id(), 'sp_wc_barcode_field', true)) {
                        $products_global_ids_value = get_post_meta($product->get_id(), 'sp_wc_barcode_field', true);
                    }

                    $products_brand 						= $schema_datas['brand'];

                    $products_currency 						= $schema_datas['currency'];
                    if ('' == $products_currency && function_exists('get_woocommerce_currency') && get_woocommerce_currency()) {
                        $products_currency = get_woocommerce_currency();
                    } elseif ('' == $products_currency && function_exists('edd_get_currency') && edd_get_currency()) {
                        $products_currency = edd_get_currency();
                    } elseif ('' == $products_currency) {
                        $products_currency = 'USD';
                    }

                    $products_condition 					= $schema_datas['condition'];
                    if ('' == $products_condition) {
                        $products_condition = seopress_check_ssl() . 'schema.org/NewCondition';
                    }

                    $products_availability 					= $schema_datas['availability'];

                    if ('' == $products_availability) {
                        $products_availability = seopress_check_ssl() . 'schema.org/InStock';
                    }

                    $html = '<script type="application/ld+json">';
                    $html .= '{
						"@context": "' . seopress_check_ssl() . 'schema.org/",
						"@type": "Product",';
                    if ($products_name) {
                        $html .= '"name": ' . json_encode($products_name) . ',';
                    }
                    if ('' != $products_img) {
                        $html .= '"image": ' . json_encode($products_img) . ',';
                    }
                    if ('' != $products_description) {
                        $html .= '"description": ' . json_encode($products_description) . ',';
                    }
                    if ('' != $products_sku) {
                        $html .= '"sku": ' . json_encode($products_sku) . ',';
                    }
                    if ('' != $products_global_ids && '' != $products_global_ids_value) {
                        $html .= json_encode($products_global_ids) . ': ' . json_encode($products_global_ids_value) . ',';
                    }

                    //brand
                    if ('' != $products_brand) {
                        $html .= '"brand": {
								"@type": "Brand",
								"name": ' . json_encode($products_brand) . '
							},';
                    }

                    if (isset($product) && true === comments_open(get_the_ID())) {//If Reviews is true
                        //review
                        $args = [
                                'meta_key'    => 'rating',
                                'number'      => 1,
                                'status'      => 'approve',
                                'post_status' => 'publish',
                                'parent'      => 0,
                                'orderby'     => 'meta_value_num',
                                'order'       => 'DESC',
                                'post_id'     => get_the_ID(),
                                'post_type'   => 'product',
                            ];

                        $comments = get_comments($args);

                        if ( ! empty($comments)) {
                            $html .= '"review": {
									"@type": "Review",
									"reviewRating": {
										"@type": "Rating",
										"ratingValue": ' . json_encode(get_comment_meta($comments[0]->comment_ID, 'rating', true)) . '
									},
									"author": {
										"@type": "Person",
										"name": ' . json_encode(get_comment_author($comments[0]->comment_ID)) . '
									}
								},';
                        }

                        //aggregateRating
                        if (isset($product) && method_exists($product, 'get_review_count') && $product->get_review_count() >= 1) {
                            $html .= '"aggregateRating": {
									"@type": "AggregateRating",
									"ratingValue": "' . $product->get_average_rating() . '",
									"reviewCount": "' . json_encode($product->get_review_count()) . '"
								},';
                        }
                    }

                    if (isset($product) && method_exists($product, 'is_type') && $product->is_type('variable')) {
                        $offers     = '"offers" : [';
                        $variations = $product->get_available_variations();

                        $i               = 1;
                        $totalVariations = count($variations);

                        foreach ($variations as $key => $value) {
                            $product_global_ids = $schema_datas['global_ids'];
                            $product_barcode    = $schema_datas['global_ids_value'];

                            if ((empty($product_global_ids) || 'none' === $product_global_ids) || (empty($product_barcode) || 'none' === $product_barcode)) {
                                if (isset($value['seopress_global_ids']) && ! empty($value['seopress_global_ids'])) {
                                    $product_global_ids         = $value['seopress_global_ids'];
                                }
                                if (isset($value['seopress_barcode']) && ! empty($value['seopress_barcode'])) {
                                    $product_barcode            = $value['seopress_barcode'];
                                }
                            }

                            $variation                  = wc_get_product($value['variation_id']);
                            $variation_price_valid_date = '';
                            if (isset($variation) && '' == $variation_price_valid_date && method_exists($variation, 'get_date_on_sale_to') && '' != $variation->get_date_on_sale_to()) {
                                $variation_price_valid_date = $variation->get_date_on_sale_to();
                                $variation_price_valid_date = $variation_price_valid_date->date('m-d-Y');
                            } else {
                                if ( ! empty($schema_datas['price_valid_date'])) {
                                    try {
                                        $date                       = new \DateTime($schema_datas['price_valid_date']);
                                        $variation_price_valid_date = $date->format('m-d-Y');
                                    } catch (\Exception $e) {
                                        $variation_price_valid_date = $schema_datas['price_valid_date'];
                                    }
                                }
                            }

                            if ((empty($product_global_ids) || 'none' === $product_global_ids) && ! empty($products_global_ids)) {
                                $product_global_ids = $products_global_ids;
                                $product_barcode    = $products_global_ids_value;
                            }

                            if (empty($product_barcode) && ! empty($products_global_ids_value)) {
                                $product_global_ids = $products_global_ids;
                                $product_barcode    = $products_global_ids_value;
                            }

                            $availability =  sprintf('%s%s/InStock', seopress_check_ssl(), 'schema.org');
                            if ( ! $value['is_in_stock']) {
                                $availability =  sprintf('%s%s/OutOfStock', seopress_check_ssl(), 'schema.org');
                            }

                            $sku = $schema_datas['sku'];
                            if (empty($sku) || 'none' === $sku || $product->get_sku() === $sku) {
                                $sku = empty($value['sku']) ? $product->get_sku() : $value['sku'];
                            }

                            if (isset($variation) && method_exists($variation, 'get_price') && '' != $variation->get_price()) {
                                $variation_price = $variation->get_price();
                            }

                            $offers .= '
                                {
                                    "@type": "Offer",
                                    "url": ' . json_encode(get_permalink()) . ',
                                    "sku": "' . $sku . '",
                                    "price": ' . $variation_price . ',
                                    "priceCurrency": "' . $products_currency . '",
                                    "itemCondition": ' . json_encode($products_condition) . ',
                                    "availability": "' . $availability . '"
                                ';

                            if ( ! empty($product_global_ids) && 'none' !== $product_global_ids && ! empty($product_barcode)) {
                                $offers .= sprintf(', "%s" : "%s"', $product_global_ids, $product_barcode);
                            }

                            if ($variation_price_valid_date) {
                                $offers .= sprintf(', "%s" : "%s"', 'priceValidUntil', $variation_price_valid_date);
                            }

                            $offers .= '}';

                            if ($i != $totalVariations) {
                                $offers .= ',';
                            }
                            ++$i;
                        }
                        $offers .= ']';
                        $html .= $offers;
                    } elseif ('' != $products_price) {
                        $html .= '"offers": {
								"@type": "Offer",
								"url": ' . json_encode(get_permalink()) . ',
								"priceCurrency": ' . json_encode($products_currency) . ',
								"price": ' . json_encode($products_price) . ',
								"priceValidUntil": ' . json_encode($products_price_valid_date) . ',
								"itemCondition": ' . json_encode($products_condition) . ',
								"availability": ' . json_encode($products_availability) . '
							}';
                    }

                    $html = trim($html, ',');
                    $html .= '}';

                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_product_html', $html);
                    echo $html;
                }
            }

            //Software App JSON-LD
            function seopress_automatic_rich_snippets_softwareapp_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    //Init

                    $softwareapp_name 					   = $schema_datas['name'];
                    $softwareapp_os 					     = $schema_datas['os'];
                    $softwareapp_cat 					    = $schema_datas['cat'];
                    $softwareapp_rating 				  = $schema_datas['rating'];
                    $softwareapp_price 					  = $schema_datas['price'];
                    $softwareapp_currency 				= $schema_datas['currency'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
						"@context": "' . seopress_check_ssl() . 'schema.org/",
						"@type": "SoftwareApplication",';
                    if ('' != $softwareapp_name) {
                        $html .= '"name": ' . json_encode($softwareapp_name) . ',';
                    }
                    if ('' != $softwareapp_os) {
                        $html .= '"operatingSystem": ' . json_encode($softwareapp_os) . ',';
                    }
                    if ('' != $softwareapp_cat) {
                        $html .= '"applicationCategory": ' . json_encode($softwareapp_cat) . ',';
                    }
                    if ('' != $softwareapp_rating) {
                        $html .= '"review": {
							"@type": "Review",
								"reviewRating": {
										"@type": "Rating",
										"ratingValue": ' . json_encode($softwareapp_rating) . '
									},
									"author": {
										"@type": "Person",
										"name": ' . json_encode(get_the_author()) . '
									}
								},';
                    }
                    if ('' != $softwareapp_price && '' != $softwareapp_currency) {
                        $html .= '"offers": {
							"@type": "Offer",';
                        $html .= '"price": ' . json_encode($softwareapp_price) . ',';
                        $html .= '"priceCurrency": ' . json_encode($softwareapp_currency);
                        $html .= '}';
                    }
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_softwareapp_html', $html);

                    echo $html;
                }
            }

            //Service JSON-LD
            function seopress_automatic_rich_snippets_services_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    //Init
                    global $product;

                    $service_name 							      = $schema_datas['name'];
                    $service_type 							      = $schema_datas['type'];
                    $service_desc 							      = $schema_datas['description'];
                    $service_img 							       = $schema_datas['img'];
                    $service_area 							      = $schema_datas['area'];
                    $service_provider_name					= $schema_datas['provider_name'];
                    $service_lb_img							     = $schema_datas['lb_img'];
                    $service_provider_mob 					= $schema_datas['provider_mobility'];
                    $service_slogan 						     = $schema_datas['slogan'];
                    $service_street_addr 					 = $schema_datas['street_addr'];
                    $service_city 							      = $schema_datas['city'];
                    $service_state 							     = $schema_datas['state'];
                    $service_postal_code 					 = $schema_datas['pc'];
                    $service_country 						    = $schema_datas['country'];
                    $service_lat							        = $schema_datas['lat'];
                    $service_lon 							       = $schema_datas['lon'];
                    $service_tel 							       = $schema_datas['tel'];
                    $service_price 							     = $schema_datas['price'];

                    $html = '<script type="application/ld+json">';
                    $html .= '{
						"@context": "' . seopress_check_ssl() . 'schema.org/",
						"@type": "Service",';
                    if ('' != $service_name) {
                        $html .= '"name": ' . json_encode($service_name) . ',';
                    }
                    if ('' != $service_type) {
                        $html .= '"serviceType": ' . json_encode($service_type) . ',';
                    }
                    if ('' != $service_desc) {
                        $html .= '"description": ' . json_encode($service_desc) . ',';
                    }
                    if ('' != $service_img) {
                        $html .= '"image": ' . json_encode($service_img) . ',';
                    }
                    if ('' != $service_area) {
                        $html .= '"areaServed": ' . json_encode($service_area) . ',';
                    }
                    if ('' != $service_provider_mob) {
                        $html .= '"providerMobility": ' . json_encode($service_provider_mob) . ',';
                    }
                    if ('' != $service_slogan) {
                        $html .= '"slogan": ' . json_encode($service_slogan) . ',';
                    }
                    //Provider
                    if ('' != $service_provider_name) {
                        $html .= '"provider": {
								"@type": "LocalBusiness",';
                        $html .= '"name": ' . json_encode($service_provider_name) . ',';

                        if ('' != $service_tel) {
                            $html .= '"telephone": ' . json_encode($service_tel) . ',';
                        }
                        if ('' != $service_lb_img) {
                            $html .= '"image": ' . json_encode($service_lb_img) . ',';
                        }
                        if ('' != $service_price) {
                            $html .= '"priceRange": ' . json_encode($service_price) . ',';
                        }

                        //Address
                        if (isset($service_street_addr) || isset($service_city) || isset($service_state) || isset($service_postal_code) || isset($service_country)) {
                            $html .= '"address": {
									"@type": "PostalAddress",';
                            if (isset($service_street_addr)) {
                                $html .= '"streetAddress": ' . json_encode($service_street_addr) . ',';
                            }
                            if (isset($service_city)) {
                                $html .= '"addressLocality": ' . json_encode($service_city) . ',';
                            }
                            if (isset($service_state)) {
                                $html .= '"addressRegion": ' . json_encode($service_state) . ',';
                            }
                            if (isset($service_postal_code)) {
                                $html .= '"postalCode": ' . json_encode($service_postal_code) . ',';
                            }
                            if (isset($service_country)) {
                                $html .= '"addressCountry": ' . json_encode($service_country);
                            }
                            $html .= '},';
                        }
                        //GPS
                        if ('' != $service_lat || '' != $service_lon) {
                            $html .= '"geo": {
									"@type": "GeoCoordinates",';
                            if (isset($service_lat)) {
                                $html .= '"latitude": ' . json_encode($service_lat) . ',';
                            }
                            if (isset($service_lon)) {
                                $html .= '"longitude": ' . json_encode($service_lon);
                            }
                            $html .= '}';
                        }
                        if (isset($product) && true === comments_open(get_the_ID())) {//If Reviews is true
                            $html .= '},';
                        } else {
                            $html .= '}';
                        }
                    }

                    if (isset($product) && true === comments_open(get_the_ID())) {//If Reviews is true
                        //review
                        $args = [
                                'meta_key'    => 'rating',
                                'number'      => 1,
                                'status'      => 'approve',
                                'post_status' => 'publish',
                                'parent'      => 0,
                                'orderby'     => 'meta_value_num',
                                'order'       => 'DESC',
                                'post_id'     => get_the_ID(),
                                'post_type'   => 'product',
                            ];

                        $comments = get_comments($args);

                        if ( ! empty($comments)) {
                            $html .= '"review": {
									"@type": "Review",
									"reviewRating": {
											"@type": "Rating",
										"ratingValue": ' . json_encode(get_comment_meta($comments[0]->comment_ID, 'rating', true)) . '
									},
									"author": {
										"@type": "Person",
											"name": ' . json_encode(get_comment_author($comments[0]->comment_ID)) . '
									}
									},';
                        }

                        //aggregateRating
                        if (isset($product) && $product->get_review_count() >= 1) {
                            $html .= '"aggregateRating": {
									"@type": "AggregateRating",
									"ratingValue": "' . $product->get_average_rating() . '",
									"reviewCount": "' . json_encode($product->get_review_count()) . '"
									}';
                        }
                    }
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_service_html', $html);

                    echo $html;
                }
            }

            //Review JSON-LD
            function seopress_automatic_rich_snippets_review_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $review_item 							  = $schema_datas['item'];
                    $review_type 							  = $schema_datas['item_type'];
                    $review_img 							   = $schema_datas['img'];
                    $review_rating 							= $schema_datas['rating'];

                    if ($review_type) {
                        $type = $review_type;
                    } else {
                        $type = 'Thing';
                    }

                    $html = '<script type="application/ld+json">';
                    $html .= '{
						"@context": "' . seopress_check_ssl() . 'schema.org/",
						"@type": "Review",';
                    if ($review_item) {
                        $html .= '"itemReviewed":{"@type":' . json_encode($type) . ',"name":' . json_encode($review_item);
                    }
                    if ('' != $review_item && '' == $review_img) {
                        $html .= '},';
                    } else {
                        $html .= ',';
                    }
                    if ('' != $review_img) {
                        $html .= '"image": {"@type":"ImageObject","url":' . json_encode($review_img) . '}';
                    }
                    if ('' != $review_item && '' != $review_img) {
                        $html .= '},';
                    }
                    if ('' != $review_rating) {
                        $html .= '"reviewRating":{"@type":"Rating","ratingValue":' . json_encode($review_rating) . '},';
                    }
                    $html .= '"datePublished":"' . get_the_date('c') . '",';
                    $html .= '"author":{"@type":"Person","name":' . json_encode(get_the_author()) . '}';
                    $html = trim($html, ',');
                    $html .= '}';
                    $html .= '</script>';
                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_review_html', $html);

                    echo $html;
                }
            }

            //Custom JSON-LD
            function seopress_automatic_rich_snippets_custom_option($schema_datas) {
                //if no data
                if (0 != count(array_filter($schema_datas, 'strlen'))) {
                    $custom 							= $schema_datas['custom'];

                    $variables = null;
                    $variables = apply_filters('seopress_dyn_variables_fn', $variables);

                    $post                                     = $variables['post'];
                    $term                                     = $variables['term'];
                    $seopress_titles_title_template           = $variables['seopress_titles_title_template'];
                    $seopress_titles_description_template     = $variables['seopress_titles_description_template'];
                    $seopress_paged                           = $variables['seopress_paged'];
                    $the_author_meta                          = $variables['the_author_meta'];
                    $sep                                      = $variables['sep'];
                    $seopress_excerpt                         = $variables['seopress_excerpt'];
                    $post_category                            = $variables['post_category'];
                    $post_tag                                 = $variables['post_tag'];
                    $post_thumbnail_url                       = $variables['post_thumbnail_url'];
                    $get_search_query                         = $variables['get_search_query'];
                    $woo_single_cat_html                      = $variables['woo_single_cat_html'];
                    $woo_single_tag_html                      = $variables['woo_single_tag_html'];
                    $woo_single_price                         = $variables['woo_single_price'];
                    $woo_single_price_exc_tax                 = $variables['woo_single_price_exc_tax'];
                    $woo_single_sku                           = $variables['woo_single_sku'];
                    $author_bio                               = $variables['author_bio'];
                    $seopress_get_the_excerpt                 = $variables['seopress_get_the_excerpt'];
                    $seopress_titles_template_variables_array = $variables['seopress_titles_template_variables_array'];
                    $seopress_titles_template_replace_array   = array_map('htmlentities', $variables['seopress_titles_template_replace_array']);
                    $seopress_excerpt_length                  = $variables['seopress_excerpt_length'];

                    preg_match_all('/%%_cf_(.*?)%%/', $custom, $matches); //custom fields

                    if ( ! empty($matches)) {
                        $seopress_titles_cf_template_variables_array = [];
                        $seopress_titles_cf_template_replace_array   = [];

                        foreach ($matches['0'] as $key => $value) {
                            $seopress_titles_cf_template_variables_array[] = $value;
                        }

                        foreach ($matches['1'] as $key => $value) {
                            $seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID, $value, true));
                        }
                    }

                    preg_match_all('/%%_ct_(.*?)%%/', $custom, $matches2); //custom terms taxonomy

                    if ( ! empty($matches2)) {
                        $seopress_titles_ct_template_variables_array = [];
                        $seopress_titles_ct_template_replace_array   = [];

                        foreach ($matches2['0'] as $key => $value) {
                            $seopress_titles_ct_template_variables_array[] = $value;
                        }

                        foreach ($matches2['1'] as $key => $value) {
                            $term = wp_get_post_terms($post->ID, $value);
                            if ( ! is_wp_error($term)) {
                                $seopress_titles_ct_template_replace_array[] = esc_attr($term[0]->name);
                            }
                        }
                    }

                    //Default
                    $custom = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $custom);

                    //Custom fields
                    if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                        $custom = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $custom);
                    }

                    //Custom terms taxonomy
                    if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
                        $custom = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $custom);
                    }

                    $html = wp_specialchars_decode($custom, ENT_COMPAT);

                    $html .= "\n";

                    $html = apply_filters('seopress_schemas_auto_custom_html', $html);

                    echo $html;
                }
            }

            //Dynamic variables
            global $post;
            global $product;

            /*Excerpt length*/
            $seopress_excerpt_length = 50;
            $seopress_excerpt_length = apply_filters('seopress_excerpt_length', $seopress_excerpt_length);

            /*Excerpt*/
            $seopress_excerpt ='';
            if ( ! is_404() && '' != $post) {
                if (has_excerpt($post->ID)) {
                    $seopress_excerpt = get_the_excerpt();
                }
            }
            if ('' != $seopress_excerpt) {
                $seopress_get_the_excerpt = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($seopress_excerpt), true)))), $seopress_excerpt_length);
            } elseif ('' != $post) {
                if ('' != get_post_field('post_content', $post->ID)) {
                    $seopress_get_the_excerpt = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post->ID), true))))), $seopress_excerpt_length);
                } else {
                    $seopress_get_the_excerpt = null;
                }
            } else {
                $seopress_get_the_excerpt = null;
            }

            if ('' != $post) {
                if ('' != get_post_field('post_content', $post->ID)) {
                    $seopress_get_the_content = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post->ID), true))))), $seopress_excerpt_length);
                } else {
                    $seopress_get_the_content = null;
                }
            } else {
                $seopress_get_the_content = null;
            }

            /*Author name*/
            $the_author_meta ='';
            $the_author_meta = get_the_author_meta('display_name', $post->post_author);

            if ( ! function_exists('seopress_social_knowledge_img_option')) {
                function seopress_social_knowledge_img_option() {
                    $seopress_social_knowledge_img_option = get_option('seopress_social_option_name');
                    if ( ! empty($seopress_social_knowledge_img_option)) {
                        foreach ($seopress_social_knowledge_img_option as $key => $seopress_social_knowledge_img_value) {
                            $options[$key] = $seopress_social_knowledge_img_value;
                        }
                        if (isset($seopress_social_knowledge_img_option['seopress_social_knowledge_img'])) {
                            return $seopress_social_knowledge_img_option['seopress_social_knowledge_img'];
                        }
                    }
                }
            }

            /*Date on sale from*/
            $get_date_on_sale_from ='';
            if (isset($product) && method_exists($product, 'get_date_on_sale_from')) {
                $get_date_on_sale_from = $product->get_date_on_sale_from();
                if ('' != $get_date_on_sale_from) {
                    $get_date_on_sale_from = $get_date_on_sale_from->date('m-d-Y');
                }
            }

            /*Date on sale to*/
            $get_date_on_sale_to ='';
            if (isset($product) && method_exists($product, 'get_date_on_sale_to')) {
                $get_date_on_sale_to = $product->get_date_on_sale_to();
                if ('' != $get_date_on_sale_to) {
                    $get_date_on_sale_to = $get_date_on_sale_to->date('m-d-Y');
                }
            }

            /*product cat*/
            $product_cat_term_list ='';
            if (taxonomy_exists('product_cat')) {
                $terms = wp_get_post_terms(get_the_ID(), 'product_cat', ['fields' => 'names']);
                if ( ! empty($terms) && ! is_wp_error($terms)) {
                    $product_cat_term_list = $terms[0];
                }
            }

            /*regular price*/
            $get_regular_price ='';
            if (isset($product) && method_exists($product, 'get_regular_price')) {
                $get_regular_price = $product->get_regular_price();
            }

            /*sale price*/
            $get_sale_price ='';
            if (isset($product) && method_exists($product, 'get_sale_price')) {
                $get_sale_price = $product->get_sale_price();
            }

            /*sale price with tax (regular price as fallback if not available)*/
            $get_sale_price_with_tax ='';
            if (isset($product) && method_exists($product, 'get_price') && function_exists('wc_get_price_including_tax')) {
                $get_sale_price_with_tax = wc_get_price_including_tax($product, ['price' => $get_sale_price]);
            }

            /*sku*/
            $get_sku ='';
            if (isset($product) && method_exists($product, 'get_sku')) {
                $get_sku = $product->get_sku();
            }

            /*barcode type*/
            $get_barcode_type ='';
            if (isset($product) && method_exists($product, 'get_id') && get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true)) {
                $get_barcode_type = get_post_meta($product->get_id(), 'sp_wc_barcode_type_field', true);
            }

            /*barcode*/
            $get_barcode ='';
            if (isset($product) && method_exists($product, 'get_id') && get_post_meta($product->get_id(), 'sp_wc_barcode_field', true)) {
                $get_barcode = get_post_meta($product->get_id(), 'sp_wc_barcode_field', true);
            }

            /*stock*/
            $get_stock ='';
            if (isset($product) && method_exists($product, 'managing_stock') && true === $product->managing_stock()) { //if managing stock
                if (method_exists($product, 'is_in_stock') && true === $product->is_in_stock()) {
                    $get_stock = seopress_check_ssl() . 'schema.org/InStock';
                } else { //OutOfStock
                    $get_stock = seopress_check_ssl() . 'schema.org/OutOfStock';
                }
            } elseif (isset($product) && method_exists($product, 'managing_stock') && false === $product->managing_stock() && method_exists($product, 'get_stock_status') && $product->get_stock_status()) {
                if ('instock' == $product->get_stock_status()) {
                    $get_stock = seopress_check_ssl() . 'schema.org/InStock';
                } else { //OutOfStock
                    $get_stock = seopress_check_ssl() . 'schema.org/OutOfStock';
                }
            }

            $sp_schemas_dyn_variables = [
                'site_title',
                'tagline',
                'site_url',
                'post_id',
                'post_title',
                'post_excerpt',
                'post_content',
                'post_permalink',
                'post_author_name',
                'post_date',
                'post_updated',
                'knowledge_graph_logo',
                'post_thumbnail',
                'post_author_picture',
                'product_regular_price',
                'product_sale_price',
                'product_price_with_tax',
                'product_date_from',
                'product_date_to',
                'product_sku',
                'product_barcode_type',
                'product_barcode',
                'product_category',
                'product_stock',
            ];

            $sp_schemas_dyn_variables_replace = [
                get_bloginfo('name'),
                get_bloginfo('description'),
                get_home_url(),
                get_the_ID(),
                the_title_attribute('echo=0'),
                $seopress_get_the_excerpt,
                $seopress_get_the_content,
                get_permalink(),
                $the_author_meta,
                get_the_date('c'),
                get_the_modified_date('c'),
                seopress_social_knowledge_img_option(),
                get_the_post_thumbnail_url($post, 'full'),
                get_avatar_url(get_the_author_meta('ID')),
                $get_regular_price,
                $get_sale_price,
                $get_sale_price_with_tax,
                $get_date_on_sale_from,
                $get_date_on_sale_to,
                $get_sku,
                $get_barcode_type,
                $get_barcode,
                $product_cat_term_list,
                $get_stock,
            ];

            //Request schemas based on post type / rules
            $args = [
                'post_type'      => 'seopress_schemas',
                'posts_per_page' => -1,
                //'fields' => 'ids',
            ];

            $sp_schemas_query = new WP_Query($args);
            $current_post     = $post;
            $sp_schemas_ids   = [];

            if ($sp_schemas_query->have_posts()) {
                while ($sp_schemas_query->have_posts()) {
                    $sp_schemas_query->the_post();
                    if (get_post_meta(get_the_ID(), '_seopress_pro_rich_snippets_rules', true) &&
                        seopress_is_content_valid_for_schemas($current_post->ID)) {
                        $sp_schemas_ids[] = get_the_ID();
                    }
                }
            }
            wp_reset_postdata();

            if ( ! empty($sp_schemas_ids)) {
                foreach ($sp_schemas_ids as $id) {
                    //Datas
                    $schema_datas = [];

                    //Type
                    $seopress_pro_rich_snippets_type 					= get_post_meta($id, '_seopress_pro_rich_snippets_type', true);

                    //Datas
                    $seopress_pro_schemas                           	= get_post_meta($post->ID, '_seopress_pro_schemas');

                    $disable 											= get_post_meta($post->ID, '_seopress_pro_rich_snippets_disable', true);
                    if (is_array($disable) && array_key_exists($id, $disable)) {
                        continue;
                    }

                    //Article
                    if ('articles' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 					= 'article';

                        $post_meta_key = [
                            'type' 						           => '_seopress_pro_rich_snippets_article_type',
                            'title' 					           => '_seopress_pro_rich_snippets_article_title',
                            'img' 						            => '_seopress_pro_rich_snippets_article_img',
                            'coverage_start_date' 		=> '_seopress_pro_rich_snippets_article_coverage_start_date',
                            'coverage_start_time' 		=> '_seopress_pro_rich_snippets_article_coverage_start_time',
                            'coverage_end_date' 		  => '_seopress_pro_rich_snippets_article_coverage_end_date',
                            'coverage_end_time' 		  => '_seopress_pro_rich_snippets_article_coverage_end_time',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_articles_option($schema_datas);
                    }

                    //Local Business
                    if ('localbusiness' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'lb';

                        $post_meta_key = [
                            'name'           => '_seopress_pro_rich_snippets_lb_name',
                            'type'           => '_seopress_pro_rich_snippets_lb_type',
                            'img'            => '_seopress_pro_rich_snippets_lb_img',
                            'street_addr'    => '_seopress_pro_rich_snippets_lb_street_addr',
                            'city'           => '_seopress_pro_rich_snippets_lb_city',
                            'state'          => '_seopress_pro_rich_snippets_lb_state',
                            'pc'             => '_seopress_pro_rich_snippets_lb_pc',
                            'country'        => '_seopress_pro_rich_snippets_lb_country',
                            'lat'            => '_seopress_pro_rich_snippets_lb_lat',
                            'lon'            => '_seopress_pro_rich_snippets_lb_lon',
                            'website'        => '_seopress_pro_rich_snippets_lb_website',
                            'tel'            => '_seopress_pro_rich_snippets_lb_tel',
                            'price'          => '_seopress_pro_rich_snippets_lb_price',
                            'serves_cuisine' => '_seopress_pro_rich_snippets_lb_serves_cuisine',
                            'opening_hours'  => '_seopress_pro_rich_snippets_lb_opening_hours',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_lb_option($schema_datas);
                    }

                    //FAQ
                    if ('faq' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'faq';

                        $post_meta_key = [
                            'q' => '_seopress_pro_rich_snippets_faq_q',
                            'a' => '_seopress_pro_rich_snippets_faq_a',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_faq_option($schema_datas);
                    }

                    //Courses
                    if ('courses' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'courses';

                        $post_meta_key = [
                            'title'   => '_seopress_pro_rich_snippets_courses_title',
                            'desc'    => '_seopress_pro_rich_snippets_courses_desc',
                            'school'  => '_seopress_pro_rich_snippets_courses_school',
                            'website' => '_seopress_pro_rich_snippets_courses_website',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_courses_option($schema_datas);
                    }

                    //Recipes
                    if ('recipes' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'recipes';

                        $post_meta_key = [
                            'name'         => '_seopress_pro_rich_snippets_recipes_name',
                            'desc'         => '_seopress_pro_rich_snippets_recipes_desc',
                            'cat'          => '_seopress_pro_rich_snippets_recipes_cat',
                            'img'          => '_seopress_pro_rich_snippets_recipes_img',
                            'prep_time'    => '_seopress_pro_rich_snippets_recipes_prep_time',
                            'cook_time'    => '_seopress_pro_rich_snippets_recipes_cook_time',
                            'calories'     => '_seopress_pro_rich_snippets_recipes_calories',
                            'yield'        => '_seopress_pro_rich_snippets_recipes_yield',
                            'keywords'     => '_seopress_pro_rich_snippets_recipes_keywords',
                            'cuisine'      => '_seopress_pro_rich_snippets_recipes_cuisine',
                            'ingredient'   => '_seopress_pro_rich_snippets_recipes_ingredient',
                            'instructions' => '_seopress_pro_rich_snippets_recipes_instructions',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_recipes_option($schema_datas);
                    }

                    //Jobs
                    if ('jobs' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'jobs';

                        $post_meta_key = [
                            'name'                => '_seopress_pro_rich_snippets_jobs_name',
                            'desc'                => '_seopress_pro_rich_snippets_jobs_desc',
                            'date_posted'         => '_seopress_pro_rich_snippets_jobs_date_posted',
                            'valid_through'       => '_seopress_pro_rich_snippets_jobs_valid_through',
                            'employment_type'     => '_seopress_pro_rich_snippets_jobs_employment_type',
                            'identifier_name'     => '_seopress_pro_rich_snippets_jobs_identifier_name',
                            'identifier_value'    => '_seopress_pro_rich_snippets_jobs_identifier_value',
                            'hiring_organization' => '_seopress_pro_rich_snippets_jobs_hiring_organization',
                            'hiring_same_as'      => '_seopress_pro_rich_snippets_jobs_hiring_same_as',
                            'hiring_logo'         => '_seopress_pro_rich_snippets_jobs_hiring_logo',
                            'hiring_logo_width'   => '_seopress_pro_rich_snippets_jobs_hiring_logo_width',
                            'hiring_logo_height'  => '_seopress_pro_rich_snippets_jobs_hiring_logo_height',
                            'address_street'      => '_seopress_pro_rich_snippets_jobs_address_street',
                            'address_locality'    => '_seopress_pro_rich_snippets_jobs_address_locality',
                            'address_region'      => '_seopress_pro_rich_snippets_jobs_address_region',
                            'postal_code'         => '_seopress_pro_rich_snippets_jobs_postal_code',
                            'country'             => '_seopress_pro_rich_snippets_jobs_country',
                            'remote'              => '_seopress_pro_rich_snippets_jobs_remote',
                            'salary'              => '_seopress_pro_rich_snippets_jobs_salary',
                            'salary_currency'     => '_seopress_pro_rich_snippets_jobs_salary_currency',
                            'salary_unit'         => '_seopress_pro_rich_snippets_jobs_salary_unit',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_jobs_option($schema_datas);
                    }

                    //Videos
                    if ('videos' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'videos';

                        $post_meta_key = [
                            'name'        => '_seopress_pro_rich_snippets_videos_name',
                            'description' => '_seopress_pro_rich_snippets_videos_description',
                            'img'         => '_seopress_pro_rich_snippets_videos_img',
                            'duration'    => '_seopress_pro_rich_snippets_videos_duration',
                            'url'         => '_seopress_pro_rich_snippets_videos_url',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_videos_option($schema_datas);
                    }

                    //Events
                    if ('events' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'events';

                        $post_meta_key = [
                            'type'                   => '_seopress_pro_rich_snippets_events_type',
                            'name'                   => '_seopress_pro_rich_snippets_events_name',
                            'desc'                   => '_seopress_pro_rich_snippets_events_desc',
                            'img'                    => '_seopress_pro_rich_snippets_events_img',
                            'start_date'             => '_seopress_pro_rich_snippets_events_start_date',
                            'start_time'             => '_seopress_pro_rich_snippets_events_start_time',
                            'end_date'               => '_seopress_pro_rich_snippets_events_end_date',
                            'end_time'               => '_seopress_pro_rich_snippets_events_end_time',
                            'previous_start_date'    => '_seopress_pro_rich_snippets_events_previous_start_date',
                            'previous_start_time'    => '_seopress_pro_rich_snippets_events_previous_start_time',
                            'location_name'          => '_seopress_pro_rich_snippets_events_location_name',
                            'location_url'           => '_seopress_pro_rich_snippets_events_location_url',
                            'location_address'       => '_seopress_pro_rich_snippets_events_location_address',
                            'offers_name'            => '_seopress_pro_rich_snippets_events_offers_name',
                            'offers_cat'             => '_seopress_pro_rich_snippets_events_offers_cat',
                            'offers_price'           => '_seopress_pro_rich_snippets_events_offers_price',
                            'offers_price_currency'  => '_seopress_pro_rich_snippets_events_offers_price_currency',
                            'offers_availability'    => '_seopress_pro_rich_snippets_events_offers_availability',
                            'offers_valid_from_date' => '_seopress_pro_rich_snippets_events_offers_valid_from_date',
                            'offers_valid_from_time' => '_seopress_pro_rich_snippets_events_offers_valid_from_time',
                            'offers_url'             => '_seopress_pro_rich_snippets_events_offers_url',
                            'performer'              => '_seopress_pro_rich_snippets_events_performer',
                            'status'                 => '_seopress_pro_rich_snippets_events_status',
                            'attendance_mode'        => '_seopress_pro_rich_snippets_events_attendance_mode',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_events_option($schema_datas);
                    }

                    //Products
                    if ('products' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'product';

                        $post_meta_key = [
                            'name'             => '_seopress_pro_rich_snippets_product_name',
                            'description'      => '_seopress_pro_rich_snippets_product_description',
                            'img'              => '_seopress_pro_rich_snippets_product_img',
                            'price'            => '_seopress_pro_rich_snippets_product_price',
                            'price_valid_date' => '_seopress_pro_rich_snippets_product_price_valid_date',
                            'sku'              => '_seopress_pro_rich_snippets_product_sku',
                            'brand'            => '_seopress_pro_rich_snippets_product_brand',
                            'global_ids'       => '_seopress_pro_rich_snippets_product_global_ids',
                            'global_ids_value' => '_seopress_pro_rich_snippets_product_global_ids_value',
                            'currency'         => '_seopress_pro_rich_snippets_product_price_currency',
                            'condition'        => '_seopress_pro_rich_snippets_product_condition',
                            'availability'     => '_seopress_pro_rich_snippets_product_availability',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_products_option($schema_datas);
                    }

                    //Software Application
                    if ('softwareapp' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'softwareapp';

                        $post_meta_key = [
                            'name'     => '_seopress_pro_rich_snippets_softwareapp_name',
                            'os'       => '_seopress_pro_rich_snippets_softwareapp_os',
                            'cat'      => '_seopress_pro_rich_snippets_softwareapp_cat',
                            'price'    => '_seopress_pro_rich_snippets_softwareapp_price',
                            'currency' => '_seopress_pro_rich_snippets_softwareapp_currency',
                            'rating'   => '_seopress_pro_rich_snippets_softwareapp_rating',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_softwareapp_option($schema_datas);
                    }

                    //Service
                    if ('services' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'service';

                        $post_meta_key = [
                            'name'              => '_seopress_pro_rich_snippets_service_name',
                            'type'              => '_seopress_pro_rich_snippets_service_type',
                            'description'       => '_seopress_pro_rich_snippets_service_description',
                            'img'               => '_seopress_pro_rich_snippets_service_img',
                            'area'              => '_seopress_pro_rich_snippets_service_area',
                            'provider_name'     => '_seopress_pro_rich_snippets_service_provider_name',
                            'lb_img'            => '_seopress_pro_rich_snippets_service_lb_img',
                            'provider_mobility' => '_seopress_pro_rich_snippets_service_provider_mobility',
                            'slogan'            => '_seopress_pro_rich_snippets_service_slogan',
                            'street_addr'       => '_seopress_pro_rich_snippets_service_street_addr',
                            'city'              => '_seopress_pro_rich_snippets_service_city',
                            'state'             => '_seopress_pro_rich_snippets_service_state',
                            'pc'                => '_seopress_pro_rich_snippets_service_pc',
                            'country'           => '_seopress_pro_rich_snippets_service_country',
                            'lat'               => '_seopress_pro_rich_snippets_service_lat',
                            'lon'               => '_seopress_pro_rich_snippets_service_lon',
                            'tel'               => '_seopress_pro_rich_snippets_service_tel',
                            'price'             => '_seopress_pro_rich_snippets_service_price',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_services_option($schema_datas);
                    }

                    //Review
                    if ('review' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'review';

                        $post_meta_key = [
                            'item'      => '_seopress_pro_rich_snippets_review_item',
                            'item_type' => '_seopress_pro_rich_snippets_review_item_type',
                            'img'       => '_seopress_pro_rich_snippets_review_img',
                            'rating'    => '_seopress_pro_rich_snippets_review_rating',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_review_option($schema_datas);
                    }

                    //Custom
                    if ('custom' == $seopress_pro_rich_snippets_type) {
                        //Schema type
                        $schema_name 									= 'custom';

                        $post_meta_key = [
                            'custom' => '_seopress_pro_rich_snippets_custom',
                        ];

                        //Get datas
                        $schema_datas = seopress_automatic_rich_snippets_manual_option($id, $schema_name, $post_meta_key, $seopress_pro_schemas, $sp_schemas_dyn_variables, $sp_schemas_dyn_variables_replace);

                        //Output schema in JSON-LD
                        seopress_automatic_rich_snippets_custom_option($schema_datas);
                    }
                }
            }
        }
    }
}

/**
 * Check of the post is valid for any schema.
 *
 * @since 3.8.1
 *
 * @author Julio Potier
 *
 * @param (int) $post_id
 *
 * @return (bool)
 **/
function seopress_is_content_valid_for_schemas($post_id) {
    $_post  	= get_post($post_id);
    $_cpt   	= get_post_type($_post);
    $_taxos 	= get_post_taxonomies($_post);
    $_terms 	= array_flip(wp_list_pluck(wp_get_post_terms($post_id, array_keys(seopress_get_taxonomies())), 'term_id'));
    $rules  	= get_post_meta(get_the_ID(), '_seopress_pro_rich_snippets_rules', true);

    if ( ! is_array($rules)) {
        $rules = seopress_get_default_schemas_rules($rules);
    }
    $conditions = seopress_get_schemas_conditions();
    $filters    = seopress_get_schemas_filters();
    $html       = '';
    foreach ($rules as $or => $values) {
        $flag = 0;
        foreach ($values as $and => $value) {
            $filter = $filters[$value['filter']];
            $cond   = $conditions[$value['cond']];
            if ('post_type' === $value['filter'] && post_type_exists($value['cpt']) &&
                (($value['cpt'] === $_cpt && 'equal' === $value['cond']) || ($value['cpt'] !== $_cpt && 'not_equal' === $value['cond']))
            ) {
                ++$flag;
            }
            if ('taxonomy' === $value['filter'] && term_exists((int) $value['taxo']) &&
                ((isset($_terms[$value['taxo']]) && 'equal' === $value['cond']) || ( ! isset($_terms[$value['taxo']]) && 'not_equal' === $value['cond']))
            ) {
                ++$flag;
            }

            if ($flag === count($values)) {
                return true;
            }
        }
    }

    return false;
}
