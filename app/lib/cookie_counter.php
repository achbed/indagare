<?php namespace indagare\cookies;
    class Counter {
        static function updateCounter() {
            //echo setcookie("pagecount", '1');
            //print "cookie counter <br>";
            //print_r($_COOKIE);
            if (isset($_COOKIE["pagecount"])){
                $c = $_COOKIE["pagecount"];
            }
            else {
                $c = 0;
            }

            if ($c > 10) {
                return false;
            }
            else {
                $c++;

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

                setrawcookie("pagecount", $c, (time()+($zg_cookie_expire*86400)), $zg_path_url, $zg_blog_url_dot, 0);
            }
            return true;
        }
    }
