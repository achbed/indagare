<?php namespace indagare\cookies;

class FirstVisit {
    static function isFirstVisit() {
            if (isset($_COOKIE["first_visit"])){
	            return false;
            } else {

				$zg_blog_url_array = parse_url(get_bloginfo('url')); // Get URL of blog
				if(empty($zg_blog_url_array['path'])) {
					$zg_blog_url_array['path'] = '/';
				}
				$zg_blog_url = $zg_blog_url_array['host']; // Get domain
				$zg_blog_url = str_replace('www.', '', $zg_blog_url);
				$zg_blog_url_dot = '.';
				$zg_blog_url_dot .= $zg_blog_url;
				$zg_path_url = $zg_blog_url_array['path']; // Get path
				$zg_path_url_slash = '/';
				$zg_path_url .= $zg_path_url_slash;
				$zg_cookie_expire = 1;

                setrawcookie("first_visit", "1", time()+60*60*24*365*10, $zg_path_url, $zg_blog_url_dot, 0);

                return true;
            }
        }
}
