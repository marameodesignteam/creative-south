<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

function seopress_get_schema_metaboxe_custom($seopress_pro_rich_snippets_data, $key_schema = 0){

	$seopress_pro_rich_snippets_custom  = isset($seopress_pro_rich_snippets_data['_seopress_pro_rich_snippets_custom']) ? $seopress_pro_rich_snippets_data['_seopress_pro_rich_snippets_custom'] : "";

	?>
	<div class="wrap-rich-snippets-item wrap-rich-snippets-custom">
		<p class="seopress-notice notice notice-info">
			<?php $pre = '<pre>'.htmlspecialchars('<script type="application/ld+json">your custom schema</script>').'</pre>'; ?>
			<?php /* translators: %s: <script type="application/ld+json">your custom schema</script> */ echo sprintf(__('Build your custom schema. Don\'t forget to include the script tag: %s','wp-seopress-pro'),$pre); ?>
		</p>
		<p>
			<label for="seopress_pro_rich_snippets_custom_meta">
				<?php _e( 'Custom schema', 'wp-seopress-pro' ); ?>
			</label>
			<textarea rows="25" id="seopress_pro_rich_snippets_custom_meta" name="seopress_pro_rich_snippets_data[<?php echo $key_schema; ?>][seopress_pro_rich_snippets_custom]" placeholder="<?php echo esc_html__('eg: <script type="application/ld+json">{
				"@context": "https://schema.org/",
				"@type": "Review",
				"itemReviewed": {
				"@type": "Restaurant",
				"image": "http://www.example.com/seafood-restaurant.jpg",
				"name": "Legal Seafood",
				"servesCuisine": "Seafood",
				"telephone": "1234567",
				"address" :{
					"@type": "PostalAddress",
					"streetAddress": "123 William St",
					"addressLocality": "New York",
					"addressRegion": "NY",
					"postalCode": "10038",
					"addressCountry": "US"
				}
				},
				"reviewRating": {
				"@type": "Rating",
				"ratingValue": "4"
				},
				"name": "A good seafood place.",
				"author": {
				"@type": "Person",
				"name": "Bob Smith"
				},
				"reviewBody": "The seafood is great.",
				"publisher": {
				"@type": "Organization",
				"name": "Washington Times"
				}
			}</script>','wp-seopress-pro'); ?>" aria-label="<?php _e('Custom schema','wp-seopress-pro'); ?>"><?php echo $seopress_pro_rich_snippets_custom; ?></textarea>
		</p>
		<?php
		if (function_exists('seopress_get_locale') && seopress_get_locale() =='fr') {
			$global_var = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-metas/';
		} else {
			$global_var = 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/';
		}
		?>
		<p class="description">
			<span class="seopress-help dashicons dashicons-external"></span><?php /* translators: %s: documentation link */ echo sprintf( __( '<a href="%s" target="_blank">You can use dynamic variables in your schema.</a>', 'wp-seopress-pro' ), $global_var ); ?>
		</p>
	</div>
	<?php
}
