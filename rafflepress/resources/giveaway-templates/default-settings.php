<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template partial loaded via require_once inside a function (rafflepress_lite_*_page / render / email builder); its top-level variables are function-local, not global.
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$wp_timezone_string           = get_option( 'timezone_string' );
$rafflepress_default_timezone = 'UTC';
if ( ! empty( $wp_timezone_string ) ) {
	$rafflepress_default_timezone = $wp_timezone_string;
}
$rafflepress_default_settings = '{  
    "api_key":"",
    "updates":"none",
    "updates_to":"",
    "slug":"rafflepress",
    "disable_rafflepress_notifications":false,
    "default_timezone":"' . $rafflepress_default_timezone . '"
 }';
