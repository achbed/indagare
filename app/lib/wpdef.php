<?php

if(!defined('WP_CONTENT_DIR')) {
	$a = explode( DIRECTORY_SEPARATOR, ltrim( __DIR__, DIRECTORY_SEPARATOR ) );
	array_pop($a); //lib
	array_pop($a); //app
	array_pop($a); //indagare
	array_pop($a); //theme
	$n = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $a);
	define('WP_CONTENT_DIR', $n );
}
require_once WP_CONTENT_DIR . '/indagare_config.php';
