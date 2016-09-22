<?php namespace indagare\cookies;

class PageCountAll {
	static $counted = false;

	static function getPageCountAll() {
		if ( isset( $_COOKIE["pagecountall"] ) ){
			$c = $_COOKIE["pagecountall"];
			if ( ! self::$counted )
				$c++;
		} else {
			$c = 0;
		}

		if ( ! self::$counted ) {
			$zg_blog_url_array = parse_url(get_bloginfo('url')); // Get URL of blog
			if(empty($zg_blog_url_array['path'])) {
				$zg_blog_url_array['path'] = '';
			}
			$zg_blog_url = $zg_blog_url_array['host']; // Get domain
			$zg_blog_url = str_replace('www.', '', $zg_blog_url);
			$zg_blog_url_dot = '.';
			$zg_blog_url_dot .= $zg_blog_url;
			$zg_path_url = $zg_blog_url_array['path']; // Get path
			$zg_path_url_slash = '/';
			$zg_path_url .= $zg_path_url_slash;
			$zg_cookie_expire = 1;
			setrawcookie("pagecountall", $c, (time()+($zg_cookie_expire*604800)), $zg_path_url, $zg_blog_url_dot, 0);
			self::$counted = true;
		}

		return $c;
	}
}
