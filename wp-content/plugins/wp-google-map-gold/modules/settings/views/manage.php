<?php
/**
 * This class used to manage settings page in backend.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package Maps
 */

$wpgmp_settings = get_option( 'wpgmp_settings', true );

$form = new WPGMP_Template();
$form->set_header( esc_html__( 'General Setting(s)', 'wpgmp-google-map' ), $response );

$form->set_col( 2 );

$form->add_element(
	'text', 'wpgmp_api_key', array(
		'label'  => esc_html__( 'Google Maps API Key', 'wpgmp-google-map' ),
		'value'  => isset($wpgmp_settings['wpgmp_api_key']) ? $wpgmp_settings['wpgmp_api_key'] : "",
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
		'desc'   => esc_html__( 'Get here http://bit.ly/29Rlmfc maps api key and insert here.', 'wpgmp-google-map' ),
	)
);

$key_url = 'http://bit.ly/29Rlmfc';

if ( $wpgmp_settings['wpgmp_api_key'] == '' ) {

	$generate_link = '<a onclick=\'window.open("' . wp_slash( $key_url ) . '", "newwindow", "width=700, height=600"); return false;\' href=\'' . $key_url . '\' class="wpgmp_key_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Generate API Key', 'wpgmp-google-map' ) . '</a>';

	$form->add_element(
		'html', 'wpgmp_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);


} else {


	$generate_link = '<a href="javascript:void(0);" class="wpgmp_check_key fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Test API Key', 'wpgmp-google-map' ) . '</a><span class="wpgmp_maps_preview"></span>';

	$form->add_element(
		'html', 'wpgmp_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);


}

$form->set_col( 1 );


$language = array(
	'en'    => esc_html__( 'ENGLISH', 'wpgmp-google-map' ),
	'ar'    => esc_html__( 'ARABIC', 'wpgmp-google-map' ),
	'eu'    => esc_html__( 'BASQUE', 'wpgmp-google-map' ),
	'bg'    => esc_html__( 'BULGARIAN', 'wpgmp-google-map' ),
	'bn'    => esc_html__( 'BENGALI', 'wpgmp-google-map' ),
	'ca'    => esc_html__( 'CATALAN', 'wpgmp-google-map' ),
	'cs'    => esc_html__( 'CZECH', 'wpgmp-google-map' ),
	'da'    => esc_html__( 'DANISH', 'wpgmp-google-map' ),
	'de'    => esc_html__( 'GERMAN', 'wpgmp-google-map' ),
	'el'    => esc_html__( 'GREEK', 'wpgmp-google-map' ),
	'en-AU' => esc_html__( 'ENGLISH (AUSTRALIAN)', 'wpgmp-google-map' ),
	'en-GB' => esc_html__( 'ENGLISH (GREAT BRITAIN)', 'wpgmp-google-map' ),
	'es'    => esc_html__( 'SPANISH', 'wpgmp-google-map' ),
	'fa'    => esc_html__( 'FARSI', 'wpgmp-google-map' ),
	'fi'    => esc_html__( 'FINNISH', 'wpgmp-google-map' ),
	'fil'   => esc_html__( 'FILIPINO', 'wpgmp-google-map' ),
	'fr'    => esc_html__( 'FRENCH', 'wpgmp-google-map' ),
	'gl'    => esc_html__( 'GALICIAN', 'wpgmp-google-map' ),
	'gu'    => esc_html__( 'GUJARATI', 'wpgmp-google-map' ),
	'hi'    => esc_html__( 'HINDI', 'wpgmp-google-map' ),
	'hr'    => esc_html__( 'CROATIAN', 'wpgmp-google-map' ),
	'hu'    => esc_html__( 'HUNGARIAN', 'wpgmp-google-map' ),
	'id'    => esc_html__( 'INDONESIAN', 'wpgmp-google-map' ),
	'it'    => esc_html__( 'ITALIAN', 'wpgmp-google-map' ),
	'iw'    => esc_html__( 'HEBREW', 'wpgmp-google-map' ),
	'ja'    => esc_html__( 'JAPANESE', 'wpgmp-google-map' ),
	'kn'    => esc_html__( 'KANNADA', 'wpgmp-google-map' ),
	'ko'    => esc_html__( 'KOREAN', 'wpgmp-google-map' ),
	'lt'    => esc_html__( 'LITHUANIAN', 'wpgmp-google-map' ),
	'lv'    => esc_html__( 'LATVIAN', 'wpgmp-google-map' ),
	'ml'    => esc_html__( 'MALAYALAM', 'wpgmp-google-map' ),
	'it'    => esc_html__( 'ITALIAN', 'wpgmp-google-map' ),
	'mr'    => esc_html__( 'MARATHI', 'wpgmp-google-map' ),
	'nl'    => esc_html__( 'DUTCH', 'wpgmp-google-map' ),
	'no'    => esc_html__( 'NORWEGIAN', 'wpgmp-google-map' ),
	'pl'    => esc_html__( 'POLISH', 'wpgmp-google-map' ),
	'pt'    => esc_html__( 'PORTUGUESE', 'wpgmp-google-map' ),
	'pt-BR' => esc_html__( 'PORTUGUESE (BRAZIL)', 'wpgmp-google-map' ),
	'pt-PT' => esc_html__( 'PORTUGUESE (PORTUGAL)', 'wpgmp-google-map' ),
	'ro'    => esc_html__( 'ROMANIAN', 'wpgmp-google-map' ),
	'ru'    => esc_html__( 'RUSSIAN', 'wpgmp-google-map' ),
	'sk'    => esc_html__( 'SLOVAK', 'wpgmp-google-map' ),
	'sl'    => esc_html__( 'SLOVENIAN', 'wpgmp-google-map' ),
	'sr'    => esc_html__( 'SERBIAN', 'wpgmp-google-map' ),
	'sv'    => esc_html__( 'SWEDISH', 'wpgmp-google-map' ),
	'tl'    => esc_html__( 'TAGALOG', 'wpgmp-google-map' ),
	'ta'    => esc_html__( 'TAMIL', 'wpgmp-google-map' ),
	'te'    => esc_html__( 'TELUGU', 'wpgmp-google-map' ),
	'th'    => esc_html__( 'THAI', 'wpgmp-google-map' ),
	'tr'    => esc_html__( 'TURKISH', 'wpgmp-google-map' ),
	'uk'    => esc_html__( 'UKRAINIAN', 'wpgmp-google-map' ),
	'vi'    => esc_html__( 'VIETNAMESE', 'wpgmp-google-map' ),
	'zh-CN' => esc_html__( 'CHINESE (SIMPLIFIED)', 'wpgmp-google-map' ),
	'zh-TW' => esc_html__( 'CHINESE (TRADITIONAL)', 'wpgmp-google-map' ),
);

$form->add_element(
	'select', 'wpgmp_language', array(
		'label'   => esc_html__( 'Map Language', 'wpgmp-google-map' ),
		'current' => isset($wpgmp_settings['wpgmp_language']) ? $wpgmp_settings['wpgmp_language'] : 'en',
		'desc'    => esc_html__( 'Choose your language for map. Default is English.', 'wpgmp-google-map' ),
		'options' => $language,
		'before'  => '<div class="fc-4">',
		'after'   => '</div>',
	)
);

$form->add_element(
	'radio', 'wpgmp_scripts_place', array(
		'label'           => esc_html__( 'Include Scripts in ', 'wpgmp-google-map' ),
		'radio-val-label' => array(
			'header' => esc_html__( 'Header', 'wpgmp-google-map' ),
			'footer' => esc_html__( 'Footer (Recommanded)', 'wpgmp-google-map' ),
		),
		'current'         => isset($wpgmp_settings['wpgmp_scripts_place']) ? $wpgmp_settings['wpgmp_scripts_place'] : "footer",
		'class'           => 'chkbox_class',
		'default_value'   => 'footer',
	)
);

$form->add_element(
	'radio', 'wpgmp_scripts_minify', array(
		'label'           => esc_html__( 'Minify Scripts', 'wpgmp-google-map' ),
		'radio-val-label' => array(
			'yes' => esc_html__( 'Yes', 'wpgmp-google-map' ),
			'no' => esc_html__( 'No', 'wpgmp-google-map' ),
		),
		'current'         => isset($wpgmp_settings['wpgmp_scripts_minify']) ? $wpgmp_settings['wpgmp_scripts_minify'] : "yes",
		'class'           => 'chkbox_class',
		'default_value'   => 'yes',
	)
);

$form->add_element(
	'checkbox', 'wpgmp_country_specific', array(
		'label'         => esc_html__( 'Enable Country Restriction', 'wpgmp-google-map' ),
		'value'         => 'true',
		'current'       => isset( $wpgmp_settings['wpgmp_country_specific'] ) ? $wpgmp_settings['wpgmp_country_specific'] : '',
		'desc'          => esc_html__( 'Apply country restriction on search results & autosuggestions.', 'wpgmp-google-map' ),
		'class'         => 'chkbox_class switch_onoff',
		'data'          => array( 'target' => '.enable_retrict_countries' ),
		'default_value' => 'false',
	)
);
		
		$countries = "Afghanistan,AF
Albania,AL
Algeria,DZ
American Samoa,AS
Andorra,AD
Angola,AO
Anguilla,AI
Antarctica,AQ
Antigua and Barbuda,AG
Argentina,AR
Armenia,AM
Aruba,AW
Australia,AU
Austria,AT
Azerbaijan,AZ
Bahamas,BS
Bahrain,BH
Bangladesh,BD
Barbados,BB
Belarus,BY
Belgium,BE
Belize,BZ
Benin,BJ
Bermuda,BM
Bhutan,BT
Bosnia and Herzegovina,BA
Botswana,BW
Bouvet Island,BV
Brazil,BR
British Indian Ocean Territory,IO
Brunei Darussalam,BN
Bulgaria,BG
Burkina Faso,BF
Burundi,BI
Cambodia,KH
Cameroon,CM
Canada,CA
Cape Verde,CV
Cayman Islands,KY
Central African Republic,CF
Chad,TD
Chile,CL
China,CN
Christmas Island,CX
Cocos (Keeling) Islands,CC
Colombia,CO
Comoros,KM
Congo,CG
Cook Islands,CK
Costa Rica,CR
Croatia,HR
Cuba,CU
CuraÃ§ao,CW
Cyprus,CY
Czech Republic,CZ
Denmark,DK
Djibouti,DJ
Dominica,DM
Dominican Republic,DO
Ecuador,EC
Egypt,EG
El Salvador,SV
Equatorial Guinea,GQ
Eritrea,ER
Estonia,EE
Ethiopia,ET
Falkland Islands (Malvinas),FK
Faroe Islands,FO
Fiji,FJ
Finland,FI
France,FR
French Guiana,GF
French Polynesia,PF
French Southern Territories,TF
Gabon,GA
Gambia,GM
Georgia,GE
Germany,DE
Ghana,GH
Gibraltar,GI
Greece,GR
Greenland,GL
Grenada,GD
Guadeloupe,GP
Guam,GU
Guatemala,GT
Guernsey,GG
Guinea,GN
Guinea-Bissau,GW
Guyana,GY
Haiti,HT
Heard Island and McDonald Islands,HM
Holy See (Vatican City State),VA
Honduras,HN
Hong Kong,HK
Hungary,HU
Iceland,IS
India,IN
Indonesia,ID
Iran,IR
Iraq,IQ
Ireland,IE
Isle of Man,IM
Israel,IL
Italy,IT
Jamaica,JM
Japan,JP
Jersey,JE
Jordan,JO
Kazakhstan,KZ
Kenya,KE
Kiribati,KI
Korea, Democratic People's Republic of,KP
Korea, Republic of,KR
Kuwait,KW
Kyrgyzstan,KG
Lao People's Democratic Republic,LA
Latvia,LV
Lebanon,LB
Lesotho,LS
Liberia,LR
Libya,LY
Liechtenstein,LI
Lithuania,LT
Luxembourg,LU
Macao,MO
Macedonia,MK
Madagascar,MG
Malawi,MW
Malaysia,MY
Maldives,MV
Mali,ML
Malta,MT
Marshall Islands,MH
Martinique,MQ
Mauritania,MR
Mauritius,MU
Mayotte,YT
Mexico,MX
Micronesia,FM
Moldova,MD
Monaco,MC
Mongolia,MN
Montenegro,ME
Montserrat,MS
Morocco,MA
Mozambique,MZ
Myanmar,MM
Namibia,NA
Nauru,NR
Nepal,NP
Netherlands,NL
New Caledonia,NC
New Zealand,NZ
Nicaragua,NI
Niger,NE
Nigeria,NG
Niue,NU
Norfolk Island,NF
Northern Mariana Islands,MP
Norway,NO
Oman,OM
Pakistan,PK
Palau,PW
Palestine,PS
Panama,PA
Papua New Guinea,PG
Paraguay,PY
Peru,PE
Philippines,PH
Pitcairn,PN
Poland,PL
Portugal,PT
Puerto Rico,PR
Qatar,QA
RÃ©union,RE
Romania,RO
Russian Federation,RU
Rwanda,RW
Saint Kitts and Nevis,KN
Saint Lucia,LC
Saint Martin (French part),MF
Saint Pierre and Miquelon,PM
Saint Vincent and the Grenadines,VC
Samoa,WS
San Marino,SM
Sao Tome and Principe,ST
Saudi Arabia,SA
Senegal,SN
Serbia,RS
Seychelles,SC
Sierra Leone,SL
Singapore,SG
Sint Maarten,SX
Slovakia,SK
Slovenia,SI
Solomon Islands,SB
Somalia,SO
South Africa,ZA
South Georgia and the South Sandwich Islands,GS
South Sudan,SS
Spain,ES
Sri Lanka,LK
Sudan,SD
Suriname,SR
Svalbard and Jan Mayen,SJ
Swaziland,SZ
Sweden,SE
Switzerland,CH
Syrian Arab Republic,SY
Taiwan,TW
Tajikistan,TJ
Tanzania,TZ
Thailand,TH
Timor-Leste,TL
Togo,TG
Tokelau,TK
Tonga,TO
Trinidad and Tobago,TT
Tunisia,TN
Turkey,TR
Turkmenistan,TM
Turks and Caicos Islands,TC
Tuvalu,TV
Uganda,UG
Ukraine,UA
United Arab Emirates,AE
United Kingdom,GB
United States,US
United States Minor Outlying Islands,UM
Uruguay,UY
Uzbekistan,UZ
Vanuatu,VU
Venezuela,VE
Viet Nam,VN
Virgin Islands, British,VG
Virgin Islands, U.S.,VI
Wallis and Futuna,WF
Western Sahara,EH
Yemen,YE
Zambia,ZM
Zimbabwe,ZW";

$countrieslist = explode("\n", $countries);

$newchoose_continent = array();

foreach($countrieslist as $country) {

	$country = explode(",", $country);
	$newchoose_continent[] = array(
				 'id'   => trim($country[count($country) -1 ]),
				 'text' => trim($country[0]),
			 );
}

		if( isset($wpgmp_settings['wpgmp_countries']) ) {
			$selected_restricted_countries = $wpgmp_settings['wpgmp_countries'];	
		} else {
			$selected_restricted_countries = array();
		}

		$form->add_element(
			'category_selector', 'wpgmp_countries', array(
				'label'    => esc_html__( 'Choose Countries', 'wpgmp-google-map' ),
				'data'     => $newchoose_continent,
				'current'  => ( isset( $selected_restricted_countries ) and ! empty( $selected_restricted_countries ) ) ? $selected_restricted_countries : array(),
				'desc'     => esc_html__( 'Some places of different countries have same zipcodes. If your product delivery area falls under such category, you can specify your prefer countries here. By this google api will provide quick and more accurate results without confliction with similar zipcode of other country. Useful only if you are not specifying zipcodes directly in textbox.', 'wpgmp-google-map' ),

				'class'    => 'enable_retrict_countries',
				'before'   => '<div class="fc-8">',
				'after'    => '</div>',
				'multiple' => 'true',
				'show'     => 'false',
			)
		);

		$form->add_element(
			'group', 'location_metabox_settings', array(
				'value'  => esc_html__( 'Meta Box Settings', 'wpgmp-google-map' ),
				'before' => '<div class="fc-12">',
				'after'  => '</div>',
			)
		);

		$args              = array(
			'public'   => true,
			'_builtin' => false,
		);
		$post_type_options = array(
			'all'  => esc_html__( 'All', 'wpgmp-google-map' ),
			'post' => esc_html__( 'Posts', 'wpgmp-google-map' ),
			'page' => esc_html__( 'Page', 'wpgmp-google-map' ),
		);
		$custom_post_types = get_post_types( $args, 'names' );
		foreach ( $custom_post_types as $post_type ) {
			$post_type_options[ sanitize_title( $post_type ) ] = ucwords( $post_type );
		}

		if( isset($wpgmp_settings['wpgmp_allow_meta']) ) {
			$selected_values = maybe_unserialize( $wpgmp_settings['wpgmp_allow_meta'] );
		} else {
			$selected_values = array();
		}
		

		$form->add_element(
			'multiple_checkbox', 'wpgmp_allow_meta[]', array(
				'label'         => esc_html__( 'Hide Meta Box', 'wpgmp-google-map' ),
				'value'         => $post_type_options,
				'current'       => $selected_values,
				'class'         => 'chkbox_class ',
				'default_value' => '',
			)
		);

		$form->add_element(
			'checkbox', 'wpgmp_metabox_map', array(
				'label'   => esc_html__( 'Hide Map', 'wpgmp-google-map' ),
				'value'   => 'true',
				'current' => isset($wpgmp_settings['wpgmp_metabox_map']) ? $wpgmp_settings['wpgmp_metabox_map'] : '',
				'desc'    => esc_html__( 'Hide map showing in the meta box.', 'wpgmp-google-map' ),
				'class'   => 'chkbox_class',
			)
		);


		$form->add_element(
			'group', 'location_extra_fields', array(
				'value'  => esc_html__( 'Extra Field(s)', 'wpgmp-google-map' ),
				'before' => '<div class="fc-12">',
				'after'  => '</div>',
			)
		);

		if( get_option( 'wpgmp_location_extrafields' ) ) {

			$data['location_extrafields'] = maybe_unserialize( get_option( 'wpgmp_location_extrafields' ) );
	
		} else {
			$data['location_extrafields'] = array();
		}
		
		if ( isset( $data['location_extrafields'] ) && !empty($data['location_extrafields']) ) {
			$ex = 0;
			foreach ( $data['location_extrafields'] as $i => $label ) {
				$form->set_col( 2 );
				$form->add_element(
					'text', 'location_extrafields[' . $ex . ']', array(
						'value'       => ( isset( $data['location_extrafields'][ $i ] ) and ! empty( $data['location_extrafields'][ $i ] ) ) ? $data['location_extrafields'][ $i ] : '',
						'desc'        => '',
						'class'       => 'location_newfields form-control',
						'placeholder' => esc_html__( 'Field Label', 'wpgmp-google-map' ),
						'before'      => '<div class="fc-4">',
						'after'       => '</div>',
						'desc'        => esc_html__( 'Placehoder - ', 'wpgmp-google-map' ) . '{' . sanitize_title( $data['location_extrafields'][ $i ] ) . '}',
					)
				);
				$form->add_element(
					'button', 'location_newfields_repeat[' . $ex . ']', array(
						'value'  => esc_html__( 'Remove', 'wpgmp-google-map' ),
						'desc'   => '',
						'class'  => 'repeat_remove_button fc-btn fc-btn-default fc-btn-sm',
						'before' => '<div class="fc-4">',
						'after'  => '</div>',
					)
				);

				$ex++;
			}
		}

		$form->set_col( 2 );

		if ( isset( $data['location_extrafields'] )   && !empty($data['location_extrafields']) ) {
			
			$next_index = $ex; 
		} else {
			$next_index = 0;
			}

			$form->add_element(
				'text', 'location_extrafields[' . $next_index . ']', array(
					'value'       => ( isset( $data['location_extrafields'][ $next_index ] ) && ! empty( $data['location_extrafields'][ $next_index ] ) ) ? $data['location_extrafields'][ $next_index ] : '',
					'desc'        => '',
					'class'       => 'location_newfields form-control',
					'placeholder' => esc_html__( 'Field Label', 'wpgmp-google-map' ),
					'before'      => '<div class="fc-4">',
					'after'       => '</div>',
				)
			);

			$form->add_element(
				'button', 'location_newfields_repeat', array(
					'value'  => esc_html__( 'Add More...', 'wpgmp-google-map' ),
					'desc'   => '',
					'class'  => 'repeat_button fc-btn fc-btn-default btn-sm',
					'before' => '<div class="fc-4">',
					'after'  => '</div>',
				)
			);


			$form->set_col( 1 );

			$form->add_element(
				'group', 'map_troubleshooting', array(
					'value'  => esc_html__( 'Troubleshooting', 'wpgmp-google-map' ),
					'before' => '<div class="fc-12">',
					'after'  => '</div>',
				)
			);



			$form->add_element(
				'checkbox', 'wpgmp_auto_fix', array(
					'label'   => esc_html__( 'Auto Fix', 'wpgmp-google-map' ),
					'value'   => 'true',
					'current' => isset($wpgmp_settings['wpgmp_auto_fix']) ? $wpgmp_settings['wpgmp_auto_fix'] : '',
					'desc'    => esc_html__( 'If map is not visible somehow, turn on auto fix and check the map.', 'wpgmp-google-map' ),
					'class'   => 'chkbox_class',
				)
			);

			$form->add_element(
				'checkbox', 'wpgmp_debug_mode', array(
					'label'   => esc_html__( 'Turn On Debug Mode', 'wpgmp-google-map' ),
					'value'   => 'true',
					'current' => isset($wpgmp_settings['wpgmp_debug_mode']) ? $wpgmp_settings['wpgmp_debug_mode'] : '',
					'desc'    => esc_html__( 'If map is not visible somehow even auto fix in turned on, please turn on debug mode and contact support team to analysis javascript console output.', 'wpgmp-google-map' ),
					'class'   => 'chkbox_class',
				)
			);

			$form->add_element(
				'group', 'map_gdpr', array(
					'value'  => esc_html__( 'Cookies Acceptance Setting', 'wpgmp-google-map' ),
					'before' => '<div class="fc-12">',
					'after'  => '</div>',
				)
			);

			$form->add_element(
				'checkbox', 'wpgmp_gdpr', array(
					'label'   => esc_html__( 'Enable Cookies Acceptance', 'wpgmp-google-map' ),
					'value'   => 'true',
					'desc'    => esc_html__( 'Maps will be not visible until visitor accept the cookies policy. You can display cookies message using popular cookies plugins. e.g cookies-notice wordpress plugin', 'wpgmp-google-map' ),
					'current' => isset($wpgmp_settings['wpgmp_gdpr']) ? $wpgmp_settings['wpgmp_gdpr'] : "",
					'class'   => 'chkbox_class switch_onoff',
					'data'    => array( 'target' => '.wpgmp_gdpr_setting' ),
				)
			);

			$form->add_element(
				'textarea', 'wpgmp_gdpr_msg', array(
					'label'                => esc_html__( '"No Map" Notice', 'wpgmp-google-map' ),
					'desc'                 => esc_html__( 'Show message instead of map until visitor accept the cookies policy. HTML Tags are allowed. Leave it blank for no message.', 'wpgmp-google-map' ),
					'value'                => isset($wpgmp_settings['wpgmp_gdpr_msg']) ? $wpgmp_settings['wpgmp_gdpr_msg'] : "",
					'textarea_fc-dividers' => 10,
					'textarea_name'        => 'wpgmp_gdpr_msg',
					'class'                => 'form-control wpgmp_gdpr_setting',
					'show'                 => 'false',
				)
			);
			
			$form->add_element(	'hidden', 'wpgmp_version', array( 'value' => WPGMP_VERSION )	);

			$form->add_element(
				'submit', 'wpgmp_save_settings', array(
					'value' => esc_html__( 'Save Setting', 'wpgmp-google-map' ),
				)
			);
			$form->add_element(
				'hidden', 'operation', array(
					'value' => 'save',
				)
			);
			$form->add_element(
				'hidden', 'page_options', array(
					'value' => 'wpgmp_api_key,wpgmp_scripts_place',
				)
			);
			$form->render();
