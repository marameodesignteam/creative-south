<?php
/**
 * The template for displaying search forms in Generate
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<form method="get" class="search-form" role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" aria-label="Search in the website">
	<label>
		<span class="screen-reader-text"><?php echo apply_filters( 'generate_search_label', _x( 'Search for:', 'label', 'generatepress' ) ); // WPCS: XSS ok, sanitization ok. ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr( apply_filters( 'generate_search_placeholder', _x( 'Search by keyword', 'placeholder', 'generatepress' ) ) ); // WPCS: XSS ok, sanitization ok. ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php echo esc_attr( apply_filters( 'generate_search_label', _x( 'Search for:', 'label', 'generatepress' ) ) ); // WPCS: XSS ok, sanitization ok. ?>">
	</label>
	<button type="submit" class="search-submit" value="<?php echo apply_filters( 'generate_search_button', _x( 'Search', 'submit button', 'generatepress' ) ); // WPCS: XSS ok, sanitization ok. ?>"><span class="sr-only"><?php echo apply_filters( 'generate_search_button', _x( 'Search', 'submit button', 'generatepress' ) ); // WPCS: XSS ok, sanitization ok. ?></span>
		<img class="desktop-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/images/search.svg" aria-hidden="true" alt="Search icon">
		<img class="mobile-icon" src="<?php echo get_stylesheet_directory_uri(); ?>/images/search-white.svg" aria-hidden="true" alt="Search icon">
	</button>
</form>
