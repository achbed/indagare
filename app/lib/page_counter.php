<?php namespace indagare\cookies;

class PageCountAll {
    static function getPageCountAll() {
            if (isset($_COOKIE["pagecountall"])){
	            $c = $_COOKIE["pagecountall"];
	            $c++;
            } else {
            	$c = 0;
            }
            
			$zg_blog_url_array = parse_url(get_bloginfo('url')); // Get URL of blog
			$zg_blog_url = $zg_blog_url_array['host']; // Get domain
			$zg_blog_url = str_replace('www.', '', $zg_blog_url);
			$zg_blog_url_dot = '.';
			$zg_blog_url_dot .= $zg_blog_url;
			$zg_path_url = $zg_blog_url_array['path']; // Get path
			$zg_path_url_slash = '/';
			$zg_path_url .= $zg_path_url_slash;
			$zg_cookie_expire = 1;

// set cookie for one week
//			setrawcookie("pagecountall", $c, (time()+($zg_cookie_expire*86400)), $zg_path_url, $zg_blog_url_dot, 0);
			setrawcookie("pagecountall", $c, (time()+($zg_cookie_expire*604800)), $zg_path_url, $zg_blog_url_dot, 0);
			
			return $c;

	}
}

