<?php
/*
Plugin Name: Last Viewed Posts
Plugin URI: http://www.wpbeginner.com
Description: Show a list of posts (and pages) the visitor had recently viewed. It's cookie based. Every visitor has his own listing. This is not a global output for all users! Edit plugin-file to see and change options.
Author: Syed Balkhi
Version: 0.7.2
Author URI: http://www.wpbeginner.com
*/

/* Copyright 2007 Olaf Baumann  (http://zeitgrund.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Use:
For the ouput use the sidebar widget OR place following code just anywhere (outside the loop) into your theme (e.g. sidebar.php).
Note that the output will not appear if there's no cookie set (because cookies are disabled or the user didn't view any single post).
-------------------------------------------

<?php if (function_exists('zg_recently_viewed')):  if (isset($_COOKIE["WP-LastViewedPosts"])) { ?>
 <h2>Last viewed posts</h2>
 <?php zg_recently_viewed(); ?>
<?php }  endif; ?>

------------------------------------------- */

/* Here are some parameters you may want to change: */
$zg_cookie_expire = 360; // After how many days should the cookie expire? Default is 360.
$zg_number_of_posts = 6; // How many posts should be displayed in the list? Default is 10.
$zg_recognize_pages = false; // Should pages to be recognized and listed? Default is true.

/* Do not edit after this line! */

function zg_lwp_header() { // Main function is called every time a page/post is being generated
global $post;

 	if ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'article' ) ) {
		zg_lw_setcookie();
//	}	if (is_single()) {
//		zg_lw_setcookie();
	} else if (is_page()) {
		global $zg_recognize_pages;
		if ($zg_recognize_pages === true) {
			zg_lw_setcookie();
		}
	}
}

function zg_pregrepl_callback( $m ) {
	return 's:' . strlen( $m[2] ) . ':"' . $m[2] . '";';
}

function zg_lw_setcookie($post_id=false) { // Do the stuff and set cookie
global $wp_query;

	if ( $post_id ) {
		$zg_post_ID = $post_id; // Read post-ID
	} else {
		$zg_post_ID = $wp_query->post->ID; // Read post-ID
	}

if (! isset($_COOKIE["WP-LastViewedPosts"])) {
		$zg_cookiearray = array($zg_post_ID); // If there's no cookie set, set up a new array
	} else {
		$zg_cookiedata = stripslashes(  $_COOKIE["WP-LastViewedPosts"] );
		$zg_cookiedata = preg_replace_callback( '!s:(\d+):"(.*?)";!', "zg_pregrepl_callback", $zg_cookiedata );
//		$zg_cookiedata = preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $zg_cookiedata );
		$zg_cookiearray = unserialize( $zg_cookiedata ); // Read serialized array from cooke and unserialize it
		if (! is_array($zg_cookiearray)) {
			$zg_cookiearray = array($zg_post_ID); // If array is fucked up...just build a new one.
		}
	}
  	if (in_array($zg_post_ID, $zg_cookiearray)) { // If the item is already included in the array then remove it
		$zg_key = array_search($zg_post_ID, $zg_cookiearray);
		array_splice($zg_cookiearray, $zg_key, 1);
	}
	array_unshift($zg_cookiearray, $zg_post_ID); // Add new entry as first item in array
	global $zg_number_of_posts;
	while (count($zg_cookiearray) > $zg_number_of_posts) { // Limit array to xx (zg_number_of_posts) entries. Otherwise cut off last entry until the right count has been reached
		array_pop($zg_cookiearray);
	}
	$zg_blog_url_array = parse_url(get_bloginfo('url')); // Get URL of blog
	if(empty($zg_blog_url_array['path'])) {
		$zg_blog_url_array['path'] = '';
	}
	$zg_blog_url = $zg_blog_url_array['host']; // Get domain
	$zg_blog_url = str_replace('www.', '', $zg_blog_url);
	$zg_blog_url_dot = '.';
	$zg_blog_url_dot .= $zg_blog_url;
	$zg_path_url = (empty($zg_blog_url_array['path']) ? '' : $zg_blog_url_array['path']); // Get path
	$zg_path_url_slash = '/';
	$zg_path_url .= $zg_path_url_slash;
	global $zg_cookie_expire;
	setcookie("WP-LastViewedPosts", serialize($zg_cookiearray), (time()+($zg_cookie_expire*86400)), $zg_path_url, $zg_blog_url_dot, 0);
}

function zg_recently_viewed_array() { // Output post array for one element
	if (isset($_COOKIE["WP-LastViewedPosts"])) {
		$zg_cookiedata = stripslashes(  $_COOKIE["WP-LastViewedPosts"] );
		$zg_cookiedata = preg_replace_callback( '!s:(\d+):"(.*?)";!', "zg_pregrepl_callback", $zg_cookiedata );
//		$zg_cookiedata = preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $zg_cookiedata );
		$zg_post_IDs = unserialize( $zg_cookiedata ); // Read serialized array from cooke and unserialize it
		if(is_array($zg_post_IDs)) {
			foreach ($zg_post_IDs as $value) {
				$args = array ( 'post_type' => 'property', 'post__in' => array($value) );
				$postlist = get_posts($args);
				wp_reset_postdata();
				return $postlist;
			}
		}
	}

	// Return an empty array if we didn't get any posts due to cookie issues
	return array();
}

function zg_recently_viewed() { // Output
	if (isset($_COOKIE["WP-LastViewedPosts"])) {
		//echo "Cookie was set.<br/>";  // For bugfixing - uncomment to see if cookie was set
		//echo $_COOKIE["WP-LastViewedPosts"]; // For bugfixing (cookie content)
		$zg_cookiedata = stripslashes(  $_COOKIE["WP-LastViewedPosts"] );
		$zg_cookiedata = preg_replace_callback( '!s:(\d+):"(.*?)";!', "zg_pregrepl_callback", $zg_cookiedata );
		//$zg_cookiedata = preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $zg_cookiedata );
		$zg_post_IDs = unserialize( $zg_cookiedata ); // Read serialized array from cooke and unserialize it

		if(is_array($zg_post_IDs)) {
			foreach ($zg_post_IDs as $value) { // Do output as long there are posts
				global $wpdb;
				$zg_get_title = $wpdb->get_results("SELECT post_title FROM $wpdb->posts WHERE ID = '$value+0' LIMIT 1");

				foreach($zg_get_title as $zg_title_out) {

	//				echo "<li><a href=\"". get_permalink($value+0) . "\" title=\"". $zg_title_out->post_title . "\">". $zg_title_out->post_title . "</a></li>\n"; // Output link and title

					echo '<article>'."\n";
					echo '<a href="'. get_permalink($value+0) .'">'."\n";
/*
						// generate thumbnail from gallery header, if not, use featured image
					$rows = get_field('gallery-header',$value);

					if ( $rows ) {
						$imageobj = $rows[0];
						$imgsrc = $imageobj['sizes']['thumb-small'];
					} else if ( catch_that_image($value) ) {
						$imgsrc = catch_that_image($value);
						$imgsrc = str_replace('620x413', '140x95', $imgsrc);
					} else {
						$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($value), 'thumb-small' );
						$imgsrc = $imageobj[0];
					}
*/
						$imgsrc = _get_firstimage( 'gallery-header', 'thumb-small', SHR_FIRSTIMAGE_ALL, false, $value );
						$imgsrc = str_replace( '620x413', '140x95', $imgsrc['src'] );
						if ( empty( $imgsrc ) ) {
							$imgsrc = get_bloginfo('stylesheet_directory').'/images/blank-thumb-small-logo.png';
						}

						echo '<img src="'.$imgsrc.'" alt="Related" />'."\n";
						echo '<h3>'.$zg_title_out->post_title.'</h3>'."\n";
					echo '</a>'."\n";
					echo '</article>'."\n";
				}
			}
		} else {
			// echo "Invalid cookie found.";  // For bugfixing - uncomment to see if cookie didn't contain an array of posts
		}
	} else {
		//echo "No cookie found.";  // For bugfixing - uncomment to see if cookie was not set
	}
}

function zg_lwp_widget($args) { // Widget output
	extract($args);
	$options = get_option('zg_lwp_widget');
	$title = htmlspecialchars(stripcslashes($options['title']), ENT_QUOTES);
	$title = empty($options['title']) ? 'Last viewed posts' : $options['title'];
	if (isset($_COOKIE["WP-LastViewedPosts"])) {
		echo $before_widget . $before_title . $title . $after_title;
		zg_recently_viewed();
		echo $after_widget;
	}
}

function zg_lwp_widget_control() { // Widget control
	$options = $newoptions = get_option('zg_lwp_widget');
	if ( $_POST['lwp-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['lwp-title']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('zg_lwp_widget', $options);
	}
	$title = attribute_escape( $options['title'] );
	?>
	<p><label for="lwp-title">
	<?php _e('Title:') ?> <input type="text" style="width:250px" id="lwp-title" name="lwp-title" value="<?php echo $title ?>" /></label>
	</p>
	<input type="hidden" name="lwp-submit" id="lwp-submit" value="1" />
	<?php
}

function zg_lwp_init() { // Widget init
  	if ( !function_exists('wp_register_sidebar_widget') )
  		return;
	wp_register_sidebar_widget('lwp_widget','Last Viewed Posts','zg_lwp_widget');
	wp_register_widget_control('lwp_widget_control','Last Viewed Posts','zg_lwp_widget_control'/*, 250, 100*/);
}

add_action('get_header','zg_lwp_header');
add_action('widgets_init', 'zg_lwp_init');
?>