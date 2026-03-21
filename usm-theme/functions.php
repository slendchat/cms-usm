<?php
/**
 * Theme functions.
 *
 * @package usm-theme
 */

function usm_theme_enqueue_styles() {
  wp_enqueue_style(
    'usm-theme-style',
    get_stylesheet_uri(),
    array(),
    wp_get_theme()->get( 'Version' )
  );
}
add_action( 'wp_enqueue_scripts', 'usm_theme_enqueue_styles' );
