<?php
/**
 * Custom Child Theme Functions - INDAGARE
 *
 * This file's parent directory can be moved to the wp-content/themes directory
 * to allow this Child theme to be activated in the Appearance - Themes section of the WP-Admin.
 *
 * Included is a basic theme setup that will add support for custom header images and custom
 * backgrounds. There are also a set of commented theme supports that can be uncommented if you need
 * them for backwards compatibility. If you are starting a new theme, these legacy functionality can be deleted.
 *
 * More ideas can be found in the community documentation for Thematic
 * @link http://docs.thematictheme.com
 *
 * @package ThematicSampleChildTheme
 * @subpackage ThemeInit
 */

require_once 'app/lib/wpdef.php';
require_once 'app/lib/wp_content.php';

// Theme updates
require 'theme-updates/theme-update-checker.php';
$Indagare_ThemeUpdateChecker = new ThemeUpdateChecker(
	'indagare', 'http://updates.whiteboardlabs.com/wp/?action=get_metadata&slug=indagare'
);

/**
 * Handles the wp_prepare_themes_for_js filter.
 * We use this to remove the parent theme from the list of themes in
 * the admin
 *
 * @param array $themes
 * @return array
 */
function ws_kill_parent_theme($themes) {
	unset( $themes['thematic'] );
	return $themes;
}
add_filter( 'wp_prepare_themes_for_js', 'ws_kill_parent_theme' );

/*
 * DEFAULTS
 * These set behaviors for various functions on the site that shouldn't change via user input
 * These may eventually be changed to theme settings.
 */

define("SHR_DEFAULT_RESERVATION_DATE", "now");
define("SHR_DEFAULT_RESERVATION_LENGTH", "+7 days");
define('SHR_THEME_FOLDER', __DIR__);

// The number of results to show in each "subsection" of the search results.  Ignored for Destination
// Guides.
define('INDG_SEARCHPAGE_SECTIONCOUNT', 8);

// Whether or not to open the Filters for each destination guide subpage.  True shows the filter block
// opened, False collapses it at page load.
define('INDG_ALWAYSSHOW_DESTFILTERS', true);

include_once('includes/utilities.php');
include_once('ajax-handler.php');
include_once('includes/search-destination.php');

/* The Following add_theme_support functions
 * will enable legacy Thematic Features
 * if uncommented.
 */

//add_theme_support( 'thematic_legacy_feedlinks' );
// add_theme_support( 'thematic_legacy_body_class' );
// add_theme_support( 'thematic_legacy_post_class' );
// add_theme_support( 'thematic_legacy_comment_form' );
// add_theme_support( 'thematic_legacy_comment_handling' );

// Include an update to allow old-style users to map to new capabilities.
include_once('includes/induser_caps.php');

/**
 * Load translations
 */
if ( ! load_theme_textdomain( 'indagare', get_stylesheet_directory() . '/languages' ) ) {
	header( 'X-DEBUG-FailedLoadingTextDomain: ' . $f );
}

/**
 * Filters for the various Login process screens.
 */

add_filter( 'password_hint', 'ind_password_hint', 10, 1 );
function ind_password_hint( $hint ) {
	$hint = __( 'The above password is only a suggestion. Feel free to delete it and choose your own.', 'indagare' );
	$hint .= '<br/><br/>';
	$hint .= __( 'The password must be at least eight characters long and contain both letters and numbers.  To make it stronger, include uppercase and lowercase letters as well as symbols like ! " ? $ % ^ &amp; ).', 'indagare' );
	return $hint;
}

add_filter( 'login_message', 'ind_login_message', 10, 1 );
function ind_login_message( $message ) {
	$reset_message = '<p class="message">' . __('Please enter your username or email address. You will receive a link to create a new password via email.') . '</p>';
	if($message == $reset_message) {
		$message = '<p class="message">' . __('Please enter your username or email address to reset your password. You will receive a link via email to continue the process.', 'indagare') . '</p>';
	}

	return $message;
}

function ind_validate_password_reset( $errors, $user ) {
	if ( $errors->get_error_code() ) {
		return;
	}

	if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
		$ok = true;
		if ( strlen( $_POST['pass1'] ) < 8 ) {
			$errors->add( 'password_reset_length', __( 'The password must be at least 8 characters long.' ) );
			$ok = false;
		}

		if ( preg_match( '/[a-z]/i', $_POST['pass1'] ) != 1 ) {
			$errors->add( 'password_reset_length', __( 'The password must contain one or more letters.' ) );
			$ok = false;
		}

		if ( preg_match( '/[0-9]/', $_POST['pass1'] ) != 1 ) {
			$errors->add( 'password_reset_length', __( 'The password must contain one or more numbers.' ) );
			$ok = false;
		}
	}
};

// add the action
add_action( 'validate_password_reset', 'ind_validate_password_reset', 10, 2 );


function ind_add_theme_caps(){
	global $pagenow;

	// gets the administrator role
	$admin = get_role( 'administrator' );

	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ){ // Test if theme is activated
		// Theme is activated
		$admin->add_cap( 'admin_toolbar', true );
		$admin->add_cap( 'admin_backend', true );
	}
}
add_action( 'load-themes.php', 'ind_add_theme_caps' );

/**
 * Hide the admin bar for non-admin users
 */
function ind_after_setup_theme() {
	if ( ! is_admin() ) {
		// We're not on an admin page.
		show_admin_bar( current_user_can( 'admin_toolbar' ) );
	}
}
add_action( 'after_setup_theme', 'ind_after_setup_theme');


function ind_restrict_admin_with_redirect() {
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		// Ajax Request.  Don't deny this.  Ever.
		return;
	}

	if ( ! current_user_can( 'admin_backend' ) ) {
		// The user doesnt have access to the back end.
		if ( current_user_can( 'manage_options' ) ) {
			// ... BUT they have manage_options permission.  Allow anyway.
			return;
		}
		wp_redirect( site_url() );
		exit;
	}
}
add_action( 'admin_init', 'ind_restrict_admin_with_redirect', 1 );


add_action('wp','indagare_wp_handle');
function indagare_wp_handle() {
	if(function_exists('acf_add_options_page'))
	acf_add_options_page(array(
		'page_title' 	=> 'Indagare Settings',
		'menu_title'	=> 'Indagare Settings',
		'menu_slug' 	=> 'acf-options',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

/**
 * Define theme setup
 */
function childtheme_setup() {

	/*
	 * Add support for custom background
	 *
	 * Allow users to specify a custom background image or color.
	 * Requires at least WordPress 3.4
	 *
	 * @link http://codex.wordpress.org/Custom_Backgrounds Custom Backgrounds
	 */
	add_theme_support( 'custom-background' );


	/**
	 * Add support for custom headers
	 *
	 * Customize to match your child theme layout and style.
	 * Requires at least WordPress 3.4
	 *
	 * @link http://codex.wordpress.org/Custom_Headers Custom Headers
	 */
	add_theme_support( 'custom-header', array(
		// Header image default
		'default-image' => '',
		// Header text display default
		'header-text' => true,
		// Header text color default
		'default-text-color' => '000',
		// Header image width (in pixels)
		'width'	=> '940',
		// Header image height (in pixels)
		'height' => '235',
		// Header image random rotation default
		'random-default' => false,
		// Template header style callback
		'wp-head-callback' => 'childtheme_header_style',
		// Admin header style callback
		'admin-head-callback' => 'childtheme_admin_header_style'
		)
	);

}
add_action('thematic_child_init', 'childtheme_setup');


/**
 * Custom Image Header Front-End Callback
 *
 * Defines the front-end style definitions for
 * the custom image header.
 * This style declaration will be output in the <head> of the
 * document just before the closing </head> tag.
 * Inline Syles and !important declarations
 * can be used to override these styles.
 *
 * @link http://codex.wordpress.org/Function_Reference/get_header_image get_header_image()
 * @link http://codex.wordpress.org/Function_Reference/get_header_textcolor get_header_textcolor()
 */
function childtheme_header_style() {
	?>
	<style type="text/css">
	<?php
	/* Declares the header image from the settings
	 * saved in WP-Admin > Appearance > Header
	 * as the background-image for div#branding.
	 */
	if ( get_header_image() && HEADER_IMAGE != get_header_image() ) {
		?>
		#branding {
			background:url('<?php header_image(); ?>') no-repeat 0 100%;
			margin-bottom:28px;
    		padding:44px 0 <?php echo HEADER_IMAGE_HEIGHT; ?>px 0; /* Bottom padding is the same height as the image */
    		overflow: visible;
}
		}
		<?php if ( 'blank' != get_header_textcolor() ) { ?>
		#blog-title, #blog-title a {
			color:#000;
		}
		#blog-description {
			padding-bottom: 22px;
		}
		<?php
		}

	}
	?>
	<?php
	/* This delcares text color for the Blog title and Description
	 * from the settings saved in WP-Admin > Appearance > Header\
	 * If not set the deafault color is set to #000
	 */
	if ( get_header_textcolor() ) {
		?>
		#blog-title, #blog-title a, #blog-description {
			color:#<?php header_textcolor(); ?>;
		}
		<?php
	}
	/* Removes header text if the
	 * "Do not diplay header text" setting is saved
	 * in WP-Admin > Appearance > Header
	 */
	if ( ! display_header_text() ) {
		?>
		#branding {
			background-position: center bottom;
			background-repeat: no-repeat;
			margin-top: 32px;
		}
		#blog-title, #blog-title a, #blog-description {
			display:none;
		}
		#branding {
			height:<?php echo HEADER_IMAGE_HEIGHT; ?>px;
			width:940px;
			padding:0;
		}
		<?php
	}
	?>
	</style>
	<?php

	if ( is_page_template ( 'template-page-intro.php' ) ) {

		$gallery = get_field('gallery');

		if ( $gallery ) {

			shuffle($gallery);

			$imageobj = $gallery[0];
			$imgsrc = $imageobj['url'];

?>
<style type="text/css">
	body { background-image:url('<?php echo $imgsrc ?>'); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: fixed; }
</style>
<?php
		}


	}
}


function ajax_export_destinations() {
	export_destinations( true );
	export_hotels( true );
	wp_send_json_success();
}
add_action( 'wp_ajax_do-export', 'ajax_export_destinations' );
add_action( 'wp_ajax_nopriv_do-export', 'ajax_export_destinations' );


/**
 * Custom Image Header Admin Callback
 *
 * Callback to defines the admin (back-end) style
 * definitions for the custom image header.
 * Customize the css to match your theme defaults.
 * The !important declarations override inline admin styles
 * to better represent a WYSIWYG of the front-end styling
 * that this child theme is currently designed to display.
 */
function childtheme_admin_header_style() {
	?>
	<style type="text/css">
	#headimg {
		background-position: left bottom;
		background-repeat:no-repeat;
		border:0 !important;
		height:auto !important;
		padding:0 0 <?php echo HEADER_IMAGE_HEIGHT + 22; /* change the added integer (22) to match your desired top padding */?>px 0;
		margin:0 0 28px 0;
	}

	#headimg h1 {
	    font-family:Arial,sans-serif;
	    font-size:34px;
	    font-weight:bold;
	    line-height:40px;
		margin:0;
	}
	#headimg a {
		color: #000;
		text-decoration: none;
	}
	#desc{
		font-family: Georgia;
    	font-size: 13px;
    	font-style: italic;
    }
	</style>
	<?php
}


add_filter('rewrite_rules_array', 'mmp_rewrite_rules');
function mmp_rewrite_rules($rules) {
    $newRules  = array();

    $newRules['destinations/?$'] = 'index.php?pagename=map';
    $newRules['destinations/articles/features?$'] = 'index.php?post_type=article&filter=features';
    $newRules['destinations/(article|offer|insidertrip)s+/?$'] = 'index.php?post_type=$matches[1]';
    $newRules['destinations/(hotel|shop|restaurant)s+/?$'] = 'index.php';
    $newRules['destinations/(activit|itinerar|librar)(y|ies)/?$'] = 'index.php';

    $newRules['destinations/([^/]*/){0,3}([^/]+)/(hotel|shop|restaurant|article|offer|insidertrip)s?/page/?([0-9]{1,})/?$'] = 'index.php?post_type=$matches[3]&destinations=$matches[2]&paged=$matches[4]';
    $newRules['destinations/([^/]*/){0,3}([^/]+)/(hotel|shop|restaurant|article|offer|insidertrip)s?/(.+)/?$'] = 'index.php?$matches[3]=$matches[4]';
    $newRules['destinations/([^/]*/){0,3}([^/]+)/(hotel|shop|restaurant|article|offer|insidertrip)s?/?$'] = 'index.php?post_type=$matches[3]&destinations=$matches[2]';

    $newRules['destinations/([^/]*/){0,3}([^/]+)/(activit|itinerar|librar)(y|ies)?/page/?([0-9]{1,})/?$'] = 'index.php?post_type=$matches[3]y&destinations=$matches[2]&paged=$matches[5]';
    $newRules['destinations/([^/]*/){0,3}([^/]+)/(activit|itinerar|librar)(y|ies)?/(.+)/?$'] = 'index.php?$matches[3]y=$matches[5]';
    $newRules['destinations/([^/]*/){0,3}([^/]+)/(activit|itinerar|librar)(y|ies)?/?$'] = 'index.php?post_type=$matches[3]y&destinations=$matches[2]';

    $newRules['destinations/(hotel|shop|restaurant|article|offer|insidertrip)s?/page/?([0-9]{1,})/?$'] = 'index.php?post_type=$matches[1]&paged=$matches[2]';

    $newRules['destinations/(activit|itinerar|librar)(y|ies)?/page/?([0-9]{1,})/?$'] = 'index.php?post_type=$matches[1]y&paged=$matches[3]';
    $newRules['destinations/(activit|itinerar|librar)(y|ies)?/?$'] = 'index.php';

    $newRules['destinations/([^/]*/)*([^/]+)/?$'] = 'index.php?destinations=$matches[2]';

    return array_merge($newRules, $rules);
}

//    add_action( 'wp_loaded','my_flush_rules' );
add_action( 'after_switch_theme','my_flush_rules' );

// Flush rules for including custom rewrite rules

function my_flush_rules(){
        $rules = get_option( 'rewrite_rules' );
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

// move destinations to its own menu, remove elsewhere
function adjust_the_wp_menu() {
    remove_submenu_page(
    	'edit.php',
        'edit-tags.php?taxonomy=destinationinterest'
    );
    remove_submenu_page(
    	'edit.php',
        'edit-tags.php?taxonomy=destinationseason'
    );
    remove_submenu_page(
    	'edit.php?post_type=hotel',
        'edit-tags.php?taxonomy=destinations&amp;post_type=hotel'
    );
    remove_submenu_page(
    	'edit.php?post_type=restaurant',
        'edit-tags.php?taxonomy=destinations&amp;post_type=restaurant'
    );
    remove_submenu_page(
    	'edit.php?post_type=shop',
        'edit-tags.php?taxonomy=destinations&amp;post_type=shop'
    );
    remove_submenu_page(
    	'edit.php?post_type=activity',
        'edit-tags.php?taxonomy=destinations&amp;post_type=activity'
    );
    remove_submenu_page(
    	'edit.php?post_type=itinerary',
        'edit-tags.php?taxonomy=destinations&amp;post_type=itinerary'
    );
    remove_submenu_page(
    	'edit.php?post_type=library',
        'edit-tags.php?taxonomy=destinations&amp;post_type=library'
    );
    remove_submenu_page(
    	'edit.php?post_type=article',
        'edit-tags.php?taxonomy=destinations&amp;post_type=article'
    );
    remove_submenu_page(
    	'edit.php?post_type=offer',
        'edit-tags.php?taxonomy=destinations&amp;post_type=offer'
    );
    remove_submenu_page(
    	'edit.php?post_type=insidertrip',
        'edit-tags.php?taxonomy=destinations&amp;post_type=insidertrip'
    );
    remove_submenu_page(
    	'upload.php',
        'edit-tags.php?taxonomy=destinations&amp;post_type=attachment'
    );
	add_menu_page('Destinations', 'Destinations', 'edit_posts', 'edit-tags.php?taxonomy=destinations', '', '', '37.5');

		add_submenu_page( 'edit-tags.php?taxonomy=destinations', 'Destinations', 'Destinations', 'manage_options', 'edit-tags.php?taxonomy=destinations');
		add_submenu_page( 'edit-tags.php?taxonomy=destinations', 'Destination Interests', 'Destination Interests', 'manage_options', 'edit-tags.php?taxonomy=destinationinterest');
		add_submenu_page( 'edit-tags.php?taxonomy=destinations', 'Destination Seasons', 'Destination Seasons', 'manage_options', 'edit-tags.php?taxonomy=destinationseason');

//	add_submenu_page('edit-tags.php?taxonomy=destinations','Destination Interest', 'Destination Interest', 'edit-tags.php?taxonomy=destinationinterest', '', '', '36.51');
//	add_submenu_page('edit-tags.php?taxonomy=destinations','Destination Season', 'Destination Season', 'edit-tags.php?taxonomy=destinationseason', '', '', '36.52');

}
add_action( 'admin_menu', 'adjust_the_wp_menu', 999 );

// contact form 7 only for admins
if(!defined('WPCF7_ADMIN_READ_CAPABILITY'))
	define( 'WPCF7_ADMIN_READ_CAPABILITY', 'manage_options' );
if(!defined('WPCF7_ADMIN_READ_WRITE_CAPABILITY'))
	define( 'WPCF7_ADMIN_READ_WRITE_CAPABILITY', 'manage_options' );

/**
 *	Add TinyMCE editor to the "Biographical Info" field in a user profile
 */
function kpl_user_bio_visual_editor( $user ) {
	// Requires WP 3.3+ and author level capabilities
	if ( function_exists('wp_editor') && current_user_can('publish_posts') ):
	?>
	<script type="text/javascript">
	(function($){
		// Remove the textarea before displaying visual editor
		$('#description').parents('tr').remove();
	})(jQuery);
	</script>

	<table class="form-table">
		<tr>
			<th><label for="description"><?php _e('Biographical Info'); ?></label></th>
			<td>
				<?php
				$description = get_user_meta( $user->ID, 'description', true);
				wp_editor( $description, 'description' );
				?>
				<p class="description"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></p>
			</td>
		</tr>
	</table>
	<?php
	endif;
}
add_action('show_user_profile', 'kpl_user_bio_visual_editor');
add_action('edit_user_profile', 'kpl_user_bio_visual_editor');

/**
 * Remove textarea filters from description field
 */
function kpl_user_bio_visual_editor_unfiltered() {
	remove_all_filters('pre_user_description');
}
add_action('admin_init','kpl_user_bio_visual_editor_unfiltered');

// dashboard for recent custom posts
function wps_recent_posts() {
   echo '<ol>'."\n";
          global $post;
          $args = array( 'numberposts' => 50, 'post_type' => array('hotel', 'restaurant', 'shop', 'activity', 'itinerary', 'library', 'article'), 'orderby' => 'date', 'order' => 'DESC' );
          $myposts = get_posts( $args );
                foreach( $myposts as $post ) : setup_postdata($post);
					$destinationstree = destinationstree();
					$dest = $destinationstree['dest'];
					$posttype =  get_post_type( $post );
					$postobj = get_post_type_object( $posttype );
					$postobjname = $postobj->labels->singular_name;
                    echo '<li><h4>'.get_the_title().' | <span class="details"><a href="/wp-admin/post.php?post='.$post->ID.'&action=edit">E</a> | <a target="_blank" href="'.get_permalink().'">V</a> | '.$dest->name.' | '.$postobjname.' | '.get_the_author_meta( 'display_name', $post->post_author ).' | <abbr>'.get_the_date('n/d/y').'</abbr></span></h4></li>'."\n";
          		endforeach;
   echo '</ol>'."\n";
}

function add_wps_recent_hotels() {
       wp_add_dashboard_widget( 'wps_recent_posts', __( 'Recent Posts' ), 'wps_recent_posts' );
}
add_action('wp_dashboard_setup', 'add_wps_recent_hotels' );

/**
* Conditional function to check if post belongs to term in a custom taxonomy.
*
* @param    tax        string                taxonomy to which the term belons
* @param    term    int|string|array    attributes of shortcode
* @param    _post    int                    post id to be checked
* @return             BOOL                True if term is matched, false otherwise
*/
function pa_in_taxonomy($tax, $term, $_post = NULL) {
    // if neither tax nor term are specified, return false
    if ( !$tax || !$term ) { return FALSE; }
    // if post parameter is given, get it, otherwise use $GLOBALS to get post
    if ( $_post ) {
        $_post = get_post( $_post );
    } else {
        $_post =& $GLOBALS['post'];
    }
    // if no post return false
    if ( !$_post ) { return FALSE; }
    // check whether post matches term belongin to tax
    $return = is_object_in_term( $_post->ID, $tax, $term );
    // if error returned, then return false
    if ( is_wp_error( $return ) ) { return FALSE; }
    return $return;
}

// destination terms array
function destterms($term_id) {

	$term = get_term( $term_id, 'destinations' );

	return $term;

}

// destinations tree
function destinationstree($post_id=false) {
global $post;

		if ( !$post_id ) {
			$terms = get_the_terms( $post->ID , 'destinations' );
		} else {
			$terms = get_the_terms( $post_id , 'destinations' );
		}
		if($terms) {
			foreach( $terms as $term ) {
				$destinationid = $term->term_id;
				$destinationstree = get_ancestors( $destinationid, 'destinations' );
				$destinationstree = array_reverse($destinationstree);
				$destdepth =  count($destinationstree);
			}
		}

		if ( $destdepth == 3 ) {
			$neigh = destterms($destinationid);
			$dest = destterms($destinationstree[2]);
			$reg = destterms($destinationstree[1]);
			$top = destterms($destinationstree[0]);
		} else if ( $destdepth == 2 ) {
			$dest = destterms($destinationid);
			$reg = destterms($destinationstree[1]);
			$top = destterms($destinationstree[0]);
		}

	return array(
		'neigh'=>$neigh,
		'dest'=>$dest,
		'reg'=>$reg,
		'top'=>$top
	);

}

// destinations tax tree
function destinationstaxtree($term_id=false) {
 	global $wp_query;

	if ( !$term_id ) {
		if (strpos($wp_query->query_vars['destinations'], ',') !== false) {
			$destinationcurrent = explode(',', $wp_query->query_vars['destinations']);
			$destinationcurrent = get_term_by( 'slug', $destinationcurrent[0], 'destinations' );
//			print_r ($destinationcurrent);
//			$destination = get_term( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$destination = get_term( $destinationcurrent->parent, 'destinations' );
		} else {
			$destination = get_term_by( 'slug', $wp_query->query_vars['destinations'], 'destinations' );
		}
		$destinationid = $destination->term_id;
	} else {
		$destinationid = $term_id;
	}

// debug
//	print_r ( 'term_id ' . $term_id );
//	print_r ( 'destinationid ' . $destinationid );

	$destinationstree = get_ancestors( $destinationid, 'destinations' );
	$destinationstree = array_reverse($destinationstree);
	$destdepth = count($destinationstree);

	if ( $destdepth == 3 ) {
		$dest = destterms($destinationstree[2]);
		$reg = destterms($destinationstree[1]);
		$top = destterms($destinationstree[0]);
	} else if ( $destdepth == 2 ) {
		$dest = destterms($destinationid);
		$reg = destterms($destinationstree[1]);
		$top = destterms($destinationstree[0]);
	} else if ( $destdepth == 1 ) {
		$dest = '';
		$reg = destterms($destinationid);
		$top = destterms($destinationstree[0]);
	}

	return array(
		'dest'=>$dest,
		'reg'=>$reg,
		'top'=>$top,
		'depth'=>$destdepth
	);

}



// variables
$swifttripurl = \indagare\config\Config::$swifttrip_url;

// user functions
include_once 'app/lib/user.php';
include_once 'app/lib/db.php';
include_once 'app/lib/cookie_counter.php';
include_once 'app/lib/first_visit.php';
include_once 'app/lib/page_counter.php';

// acf location field
include_once('includes/acf-location-field/acf-location.php');

// ACF custom field excerpt
// http://wordpress.org/support/topic/how-to-pull-excerpt-from-advanced-custom-field
// modded to remove open and close <p> tags
// use wp_trim_words to return words, wp_html_excerpt to return characters; the latter requires more editing to add ellipses.
function custom_field_excerpt($title,$tax) {
			global $post;
			if ( $tax ) {
				$text = get_field($title,$tax);
			} else {
				$text = get_field($title);
			}
			if ( '' != $text ) {
				$text = strip_shortcodes( $text );
				$text = apply_filters('the_content', $text);
				$text = str_replace(']]>', ']]>', $text);
				$excerpt_length = 20; // 20 words
				$excerpt_length_char = 160; // 160 characters
//				$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
				$excerpt_more = apply_filters('excerpt_more', '...');
				$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
//				$text = wp_html_excerpt( $text, $excerpt_length_char );
			}
//			return apply_filters('the_excerpt', $text);
			return $text;
}

// recently viewed pages
include_once('includes/last_viewed_posts.php');

// ajax load posts
include_once('includes/ajax-load-posts/ajax-load-posts.php');

// comment filters
include_once('includes/comments-filters.php');

// google map
include_once('includes/map.php');

// generate destination json
if ( is_admin() ) {
    include_once 'includes/destinations.php';
    include_once 'includes/map-locations.php';
}

function admin_styles() {
    wp_enqueue_style('admin-style', get_bloginfo('stylesheet_directory') . '/css/admin.css');
    wp_enqueue_style('qtip-style', get_bloginfo('stylesheet_directory') . '/css/jquery.qtip.css');
    wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css', array(), null);

    wp_register_script('admin-js', get_bloginfo('stylesheet_directory').'/js/admin.js', array('jquery'), '', true);
    wp_register_script('tinysort', get_bloginfo('stylesheet_directory').'/js/jquery.tinysort.min.js', array('jquery'), '', true);
    wp_register_script('qtip', get_bloginfo('stylesheet_directory').'/js/jquery.qtip.min.js', array('jquery'), '', true);
    wp_register_script('hammer', get_bloginfo('stylesheet_directory').'/js/hammer.min.js', array(), '', false);

    wp_enqueue_script('tinysort');
    wp_enqueue_script('qtip');
    wp_enqueue_script('hammer');
    wp_enqueue_script('admin-js');

    wp_register_script('velocity', get_bloginfo('stylesheet_directory').'/js/velocity.min.js', array(), '', false);
    wp_enqueue_script('velocity');

	// hide sidebar "add new" if not administrator
	if ( !current_user_can( 'administrator' ) ) {
	    wp_enqueue_style('admin-style-hide', get_bloginfo('stylesheet_directory') . '/css/admin-hide.css');
	}
}
add_action('admin_enqueue_scripts', 'admin_styles');

function admin_dequeue_scripts() {
	// remove version from Yoast SEO
    wp_dequeue_script('jquery-qtip');
}
add_action('wp_print_scripts','admin_dequeue_scripts');


add_theme_support( 'post-thumbnails');

add_action( 'init', 'my_register_image_sizes' );
//add_action( 'after_setup_theme', 'my_register_image_sizes' );

function my_register_image_sizes() {
	// Add new image sizes
	add_image_size('hero-full', 940, 460, true); // used for destination, library, home
	add_image_size('hero-medium', 620, 300, true); // used for region
	add_image_size('hero-review', 620, 413, true); // used for reviews - ie, hotel, restaurant, etc
	add_image_size('thumb-large', 300, 200, true); // used for destination landing page, home page, special offer page
	add_image_size('thumb-medium', 220, 146, true); // used for related items, and listing pages for hotel, restaurant
	add_image_size('thumb-small', 140, 95, true); // used for recent items
}

// custom image size in editor insertion
function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'hero-review' => __('Review Image'),
    ) );
}
add_filter( 'image_size_names_choose', 'my_custom_sizes' );

// strip image height and width in editor insertion
function my_image_downsize($value = false,$id = 0, $size = "medium") {
	if ( !wp_attachment_is_image($id) )
		return false;
	$img_url = wp_get_attachment_url($id);
	//Mimic functionality in image_downsize function in wp-includes/media.php
	if ( $intermediate = image_get_intermediate_size($id, $size) ) {
		$img_url = str_replace(basename($img_url), $intermediate['file'], $img_url);
	}
	elseif ( $size == 'thumbnail' ) {
		// fall back to the old thumbnail
		if ( $thumb_file = wp_get_attachment_thumb_file() && $info = getimagesize($thumb_file) ) {
			$img_url = str_replace(basename($img_url), basename($thumb_file), $img_url);
		}
	}
	if ( $img_url)
		return array($img_url, 0, 0);
	return false;
}
add_filter('image_downsize', 'my_image_downsize',1,3);

// image and caption replace in editor insertion
function editor_insert_image($html, $id, $caption, $title, $align, $url, $size) {
	list($imgsrc) = image_downsize($id, 'hero-review');
//	list($imgsrc) = image_downsize($id, $size);

    $html = "<div class=\"photo-gallery\">";
    $html .= "<img src=\"$imgsrc\" alt=\"$caption\">";
    if ($caption) {
        $html .= "<div class=\"caption\">$caption</div>";
    }
    $html .= "</div><!-- photo-gallery -->";
    return $html;
}
add_filter( 'image_send_to_editor', 'editor_insert_image', 10, 9 );

// grab thumbnail data
function get_the_post_thumbnail_data($intID = 0) {
    if($intID == 0) {
        return $intID;
    }
    $objDom = new SimpleXMLElement(get_the_post_thumbnail($intID));
    $arrDom = (array)$objDom;
    return (array)$arrDom['@attributes'];
}

// remove default hover and dropdown scripts from Thematic
function childtheme_no_superfish(){
	remove_theme_support('thematic_superfish');
}
add_action('thematic_child_init','childtheme_no_superfish');

// add scripts
function register_scripts() {
	$f = get_bloginfo('stylesheet_directory');

    wp_register_script('hammer', $f.'/js/hammer.min.js', array(), '', false);
	wp_register_script('tabs', $f.'/js/yetii-min.js', array('jquery','hammer'), '', false);
    wp_register_script('autocomplete', $f.'/js/jquery.autocomplete.mod.js', array('jquery'), '', true);
    wp_register_script('magnificpopup', $f.'/js/jquery.magnific-popup.min.js', array('jquery'), '', true);
    wp_register_script('rslidesalt', $f.'/js/responsiveslides.min.js', array('jquery'), '', true);
    wp_register_script('imagesloaded', $f.'/js/imagesloaded.pkgd.min.js', array('jquery'), '', true);
    wp_register_script('masonry', $f.'/js/masonry.pkgd.min.js', array('jquery'), '', true);
    wp_register_script('datepicker', $f.'/js/jquery-ui-1.10.3.custom.min.js', array('jquery'), '', false);
//    wp_register_script('qtip', $f.'/js/jquery.qtip.min.js', array('jquery'), '', true);
//    wp_register_script('responsivemap', $f.'/js/jquery.rwdImageMaps.min.js', array('jquery'), '', true);
    wp_register_script('customselect', $f.'/js/jquery.customSelect.min.js', array('jquery'), '', true);
    wp_register_script('lazyload', $f.'/js/jquery.lazyload.min.js', array('jquery'), '', true);
    wp_register_script('equalheight', $f.'/js/jquery.matchHeight.js', array('jquery'), '', true);

    wp_register_script('indagare.maps-locations.google', '//maps.googleapis.com/maps/api/js?v=3?key=AIzaSyAkv3l4uMtV3heGoszUd_LR-Xy7Qxeecmw&sensor=false', array('jquery'), '', false);

    wp_register_script('indagare.maps', $f.'/js/maps.js', array('jquery', 'indagare.maps-locations.google'), '', true);
    wp_register_script('indagare.maps.init', $f.'/js/maps.init.js', array('jquery'), '', false);
    wp_register_script('indagare.maps.destinations', $f.'/js/maps.destinations.js', array('jquery', 'indagare.maps-locations.google'), '', true);

    wp_register_script('template-page_footer', $f.'/js/template-page_footer.js', array('jquery'), '', true);
    wp_register_script('template-page-map_footer', $f.'/js/template-page-map_footer.js', array('jquery'), '', true);

    wp_register_script('velocity', $f.'/js/velocity.min.js', array('jquery'), '', false);
    wp_register_script('show.join.popup', $f.'/js/joinpopup.js', array('jquery'), '', true);

    wp_register_script('slick', $f.'/js/slick.min.js', array('jquery'), '', true);

    wp_localize_script( 'template-page_footer', 'ajax_login_object', array(
    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
    	'redirecturl' => home_url(),
    	'messages' => array(
    		'loading' => __('Logging in...'),
    		'thankyou' => __('Thank you','indagare'),
    		'thankyousignup' => __('Thank you for signing up.','indagare'),
    		'newsletter' => __("Indagare's e-Newsletter, full of travel buzz, is sent out every other week.",'indagare'),
    		'newsletteremailerr' => __('Please enter a valid email address to sign up for our email newsletter.','indagare'),
    		'alreadysignedup' => __('You are already signed up.','indagare'),
    		'showimages' => __('Show Images','indagare'),
    		'showmap' => __('Show Map','indagare'),
    		'hidemap' => __('Hide Map','indagare'),
    		'closemap' => __('Close Map','indagare'),
    		'fullscreen' => __('Full Screen','indagare'),
    	)
    ));
}
add_action('init', 'register_scripts');

global $ajaxlogin_processing;
$ajaxlogin_processing = false;

function ajax_login(){
	global $ajaxlogin_processing;
	$ajaxlogin_processing = true;
	header('Content-Type: application/json');

	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-login-nonce', 'security' );

	// Nonce is checked, get the POST data and sign user on
	$info = array();
	$info['user_login'] = $_POST['username'];
	$info['user_password'] = $_POST['password'];
	$info['remember'] = true;

	$user_signon = wp_signon( $info, false );
	if ( ! is_wp_error($user_signon) ) {
		wp_set_current_user($user_signon->ID);
		$account = \WPSF\Contact::get_account_wp();
		$token = '';
		if( method_exists( $account, 'get_ssotoken' ) ) {
			$token = $account->get_ssotoken();
		}
		echo json_encode( array(
			'login' => true,
			'ssotoken' => $token,
			'message' => __( 'Login successful, redirecting...' )
		) );
		die();
	}

	echo json_encode( array(
		'login' => false,
		'ssotoken' => '',
		'message' => __( 'Incorrect login - please try again' )
	) );
	die();
}
add_action( 'wp_ajax_ajaxlogin', 'ajax_login' );
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );

function enqueue_scripts() {
	$f = get_bloginfo('stylesheet_directory');

    wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css', array(), null);

    wp_enqueue_script('hammer');
    wp_enqueue_script('velocity');
	wp_enqueue_script('template-page_footer');
	wp_enqueue_style('datepicker-css', get_bloginfo('stylesheet_directory') . '/css/jquery-ui-1.10.3.custom.css');
	wp_enqueue_script('datepicker');

	if ( is_singular() ) {
		$destinationstree = destinationstree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
	}

	if ( is_archive() ) {
		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
		$depth = $destinationstree['depth'];

	}

	// home page - responsive slides
	if ( is_home() || is_front_page() ) {
	    wp_enqueue_script('rslidesalt');
    }

	// book page - responsive slides
	if (is_page_template ( 'template-page-book.php' ) ) {
	    wp_enqueue_script('rslidesalt');
    }

	// join page - responsive slides and equal height
	if (is_page_template ( 'template-page-user-signup.php' ) ) {
	    wp_enqueue_script('rslidesalt');
	    wp_enqueue_script('equalheight');
	    wp_enqueue_style('slickcss', $f.'/css/slick.css');
	    wp_enqueue_style('slicktheme', $f.'/css/slick-theme.css');
	    wp_enqueue_script('slick');
    }

	// why join page - responsive slides
	if (is_page_template ( 'template-page-why-join.php' ) ) {
	    wp_enqueue_script('rslidesalt');
    }

	// welcome page - responsive slides
	if (is_page_template ( 'template-page-welcome.php' ) ) {
	    wp_enqueue_script('rslidesalt');
    }

	// single hotel | restaurant | shop | activity | article | offer | insidertrip | itinerary archive - responsive slides
	if (
		is_singular( 'hotel' )
		|| is_singular( 'restaurant' )
		|| is_singular( 'shop' )
		|| is_singular( 'activity' )
		|| is_singular( 'article' )
		|| is_singular( 'offer' )
		|| is_singular( 'insidertrip' )
		|| is_post_type_archive('itinerary')
	) {
	    wp_enqueue_script('rslidesalt');
    }

	// mission page - imagesloaded and masonry
	if (is_page_template ( 'template-page-about-mission.php' ) ) {
	    wp_enqueue_script('imagesloaded');
	    wp_enqueue_script('masonry');
    }

	// signup page - tabs
	if (is_page_template ( 'template-page-user-signup-step-two.php' ) ) {
	    wp_enqueue_script('tabs');
    }

	// my account page - tabs
	if (is_page_template ( 'template-page-account-edit.php' ) ) {
	    wp_enqueue_script('tabs');
//	    wp_enqueue_script('customselect');
    }

	// article archive | map page | book page | home page | region level | destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer - autocomplete
	if ( is_archive() && get_query_var('post_type') == 'article'
		|| is_page_template ( 'template-page-map.php' )
		|| is_page_template ( 'template-page-book.php' )
		|| is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| ( is_archive() && $reg && $depth == 1 )
		|| ( is_home() || is_front_page() )
	) {
	    wp_enqueue_style('autocomplete-css', get_bloginfo('stylesheet_directory') . '/css/jquery.autocomplete.css');
	    wp_enqueue_script('autocomplete');

	    wp_enqueue_style('datepicker-css', get_bloginfo('stylesheet_directory') . '/css/jquery-ui-1.10.3.custom.css');
	    wp_enqueue_script('datepicker');
	}

	// map page tool tip REMOVED | responsive image map REMOVED | lazy load
	if (is_page_template ( 'template-page-map.php' ) ) {
		export_destinations( false );
	    wp_enqueue_script('lazyload');
	    wp_enqueue_script('template-page-map_footer');
	}

	// overlay script
   // wp_enqueue_style('magnificpopup-css', get_bloginfo('stylesheet_directory') . '/css/magnific-popup.css');
    wp_enqueue_script('magnificpopup');


	// load contact 7 css + js for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
	) {

		if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
			wpcf7_enqueue_styles();
		}
	}

    // custom select

}
add_action('get_header', 'enqueue_scripts');

function enqueue_scripts_here() {
	global $posts;
	global $post;

    wp_enqueue_script('customselect');
    wp_enqueue_script('tabs');

	if (
		// singular hotel | resaurant | shop | activity | article | offer | insidertrip
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'article' ) || is_singular( 'offer' ) || is_singular( 'insidertrip' )
		// itinerary archive
		|| (is_archive() && get_query_var('post_type') == 'itinerary')
/*
		// why join page
		|| ( is_page_template ( 'template-page-why-join.php' ) )
*/
	) {

		// queue up if there are gallery header images
//		$rows = get_field('gallery-header');
		$rowsraw = get_field('gallery-header', false, false);

		if($rowsraw) {
			register_new_royalslider_files(1);
		}

		// queue up if there are gallery shortcodes
		if ( gallery_shortcode($post->ID) ){
			register_new_royalslider_files(1);
		}

/*
		// why join page
		$rows = get_field('gallery');
		if($rows && is_page_template ( 'template-page-why-join.php' ) ) {
			register_new_royalslider_files(1);
		}
*/

	}

	if ( has_map() ) {
		wp_enqueue_script('indagare.maps');
		wp_enqueue_script('indagare.maps.init');
		wp_enqueue_script('indagare.maps-locations.google');
	}

	if( is_page_template ( 'template-page-map.php' ) ) {
		wp_enqueue_script('indagare.maps.destinations');
	}
}

add_action( 'wp_enqueue_scripts', 'enqueue_scripts_here' );

// load https
function loadhttp() {
	chkhttps();
}
add_action('get_header', 'loadhttp');


function childtheme_override_content_init() {
		global $thematic_content_length;

		$content = '';
		$thematic_content_length = '';

		if (is_home() || is_front_page()) {
			$content = 'full';
		} elseif (
			   is_posttype( 'hotel', POSTTYPE_ARCHIVEONLY )
			|| is_posttype( 'restaurant', POSTTYPE_ARCHIVEONLY )
			|| is_posttype( 'shop', POSTTYPE_ARCHIVEONLY )
			|| is_posttype( 'activity', POSTTYPE_ARCHIVEONLY )
			|| is_posttype( 'memberlevel', POSTTYPE_ARCHIVEONLY )
//			|| ( is_archive() && get_query_var('post_type') == 'library' )
//			|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		) {
			$content = 'full';
		} elseif (is_single()) {
			$content = 'full';
		} elseif (is_tag()) {
			$content = 'excerpt';
		} elseif (is_search()) {
			$content = 'full';
		} elseif (is_category()) {
			$content = 'excerpt';
		} elseif (is_author()) {
			$content = 'excerpt';
		} elseif (is_archive()) {
			$content = 'excerpt';
		}

		$thematic_content_length = apply_filters('thematic_content', $content);

}

// set featured image use to false for articles
$featured = false;

// initialize map and sharethis script
function headscript() {
global $post;
	$upload_dir = wp_upload_dir();

	if ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'article' ) || is_singular( 'offer' ) || is_singular( 'insidertrip' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
	) {

		// no map script for articles | insidertrip | offer
		if ( !is_singular('article') && !is_singular('insidertrip') && !is_singular('offer') ) {

?>
<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function(){

		gmap_initialize();

	});
</script>
<?php

		}

		// no sharethis for archives
		if ( !is_archive() ) {

?>
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">window.onload = function(){ stLight.options({publisher: "57b6201a-026d-422d-bb3f-937fdc9a3513", doNotHash: false, doNotCopy: false, hashAddressBar: false}); }</script>

<?php

		}

	}

}
add_action('wp_head', 'headscript');

function childtheme_body_class( $classes ) {
global $wp_query;
global $post;

	if ( is_page() && ( get_field('membership') == 'yes' ) ) {
		$classes[] = 'about join';
	}

	if ( is_page_template ( 'template-page-how-we-work.php' ) ) {
		$classes[] = 'ourprocess';
	}

	if ( is_page_template ( 'template-page-user-signup-step-two.php' ) ) {
		$classes[] = 'signup';
	}

	if ( is_page_template( 'template-page-user-site-invite.php' ) ) {
		$classes[] = 'site-invite';
	}

	if ( is_page() && ( get_field('about') == 'yes') || is_page_template ( 'template-page-about-founder.php' ) || is_author() ) {
		$classes[] = 'about';
	}

	if ( is_archive() && get_query_var('post_type') == 'press' ) {
		$classes[] = 'about press';
	}

	if ( is_archive() && get_query_var('post_type') == 'career' ) {
		$classes[] = 'about career';
	}

	if ( is_author() ) {
		$author = get_queried_object();
		$user = get_user_by('id',$author->ID);
		$userid = 'user_'.$user->ID;
		$authorgroup = get_field('author-group', $userid);

		if ( $authorgroup == 'team' ) {
			$classes[] = 'team';
		} else if ( $authorgroup == 'contributor' ) {
			$classes[] = 'contributor';
		}
	}

	if ( is_page_template ( 'template-page-about-founder.php' ) ) {
		$classes[] = 'founder';
	}

	if ( is_page_template ( 'template-page-about-team.php' ) || is_page_template ( 'template-page-about-contributors.php' ) ) {
		$classes[] = 'listall';
	}

	if ( is_page_template ( 'template-page-new.php' ) ) {
		$classes[] = 'new';
	}

	if ( is_page() && ( get_field('account') == 'yes') ) {
		$classes[] = 'about account';
	}

	if ( is_singular('offer') || ( is_archive() && get_query_var('post_type') == 'offer' ) ) {
		$classes[] = 'special offer';
	}

	if ( is_singular('insidertrip') ) {
		$classes[] = 'special insider';
	}

	if ( ( is_archive() && get_query_var('post_type') == 'insidertrip' ) ) {
		$classes[] = 'insiderlanding insider';
	}

	if ( is_page_template ( 'template-page-map.php' ) ) {
		$classes[] = 'map';
	}

	if ( is_page_template ( 'template-page-book.php' ) ) {
		$classes[] = 'book';
	}

	if ( is_page_template ( 'template-page-welcome.php' ) ) {
		$classes[] = 'welcome';
	}

	if ( is_page_template ( 'template-page-intro.php' ) ) {
		$classes[] = 'intro';
	}

	if ( is_singular('magazine') ) {
		$classes[] = 'magazine';
	}

return $classes;

}
add_filter( 'body_class', 'childtheme_body_class' );


// set doctype for HTML5
function child_create_doctype() {
    $content = '<!DOCTYPE html>' . "\n";
    $content .= '<html';
	return $content;
}
add_filter('thematic_create_doctype', 'child_create_doctype');

function meta() {
	$upload_dir = wp_upload_dir();
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8" />
<script type="text/javascript">
	var theme_path="<?php print get_bloginfo('stylesheet_directory'); ?>";
	var uploads_path="<?php print $upload_dir['url']; ?>";
</script>
	<?php
}
add_filter('wp_head','meta',1);

function headerIE() {

	echo '<!--[if lte IE 8]><script src="'.get_bloginfo('stylesheet_directory').'/js/html5.js"></script><link rel="stylesheet" href="'.get_bloginfo('stylesheet_directory').'/css/poor-ie.css" /><![endif]-->'."\n";
	echo '<!--[if !IE]> --><link rel="stylesheet" href="'.get_bloginfo('stylesheet_directory').'/css/font-adjust.css" /><![endif]-->'."\n";

}
add_filter('wp_head','headerIE',10);


function login_cookie() {
global $post;

	// single restaurant | shop | activity | article | insidertrip || archive insidertrip
	if (   is_posttype( 'restaurant', POSTTYPE_SINGLEONLY )
		|| is_posttype( 'shop', POSTTYPE_SINGLEONLY )
		|| is_posttype( 'activity', POSTTYPE_SINGLEONLY )
		|| is_posttype( 'article', POSTTYPE_SINGLEONLY )
		|| is_posttype( 'insidertrip' )
	) {

		if ( ! ind_logged_in() ) {
			indagare\cookies\Counter::updateCounter();
		}

		if( ! ind_logged_in() && ! indagare\cookies\Counter::updateCounter() ) {
			wp_enqueue_script('show.join.popup');
	    }

	// archive itinerary - lock out unless visitor is logged-in user
	} else if ( ! user_has_permission() ) {
		wp_enqueue_script('show.join.popup');
	}

}
add_filter('wp_head','login_cookie',20);

function childtheme_override_brandingopen() {}
function childtheme_override_blogtitle() {}
function childtheme_override_blogdescription() {}
function childtheme_override_brandingclose() {}

// custom nav
function childtheme_override_access() {
?>
  <div class="candy-wrapper">
    <div class="wrapper">
      <section id="branding" class="box"><a href="/"><img src="<?php echo get_bloginfo('stylesheet_directory') ?>/images/indagare-logo.png" alt="indagare-logo" /></a></section>
      <div id="menu-show-hide" class="box"><a href="#"><b class="menu" data-icon="&#xf0c9;"></b><b class="close-menu" data-icon="&#xf057;"></b></a></div>
      <section id="access" class="box collapsible">
        <ul id="nav">
          <li id="nav-magazine"><a href="/destinations/articles/features/">Dream</a><span class="show-subnav"><a href="#"></a></span>
            <div class="subnav">
              <div class="main-nav-item"><a href="/destinations/articles/features/">Read Featured Articles</a></div>
              <div class="nav-item">
                <h3>Newly Added</h3>
                <div class="subnav-related"><a href="/destinations/articles/">See All</a></div>
                <ul>
<?php
				$args = array('numberposts' => 11, 'post_type' => 'article', 'orderby' => array ( 'date' => 'DESC' ) );
//				$recent = new WP_Query($args);
				$recent = get_posts($args);
//				if($recent->have_posts() ) {
//					while ( $recent->have_posts() ) : $recent->the_post();
					foreach( $recent as $post ) : setup_postdata($post);
						echo '<li><a href="'.get_permalink($post->ID).'">'.get_the_title($post->ID).'</a></li>'."\n";
//					endwhile;
					endforeach;
//				}

				// wp_reset_postdata();
?>
                </ul>
                <div class="subnav-related"><a href="/magazines/">See Digital Magazine</a></div>
              </div>
              <div class="nav-item">
<?php
				$columns = get_terms( 'column', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

				if ( $columns ) {
					echo '<h3>Columns</h3>'."\n";
                	echo '<div class="subnav-related"><a href="/destinations/articles/">See All</a></div>'."\n";
					echo '<ul>'."\n";
						foreach ( $columns as $term ) {
							echo '<li><a href="/destinations/articles/?column='.$term->slug.'">'.$term->name.'</a></li>'."\n";
						}
					echo '</ul>'."\n";
				}
?>
              </div>
            </div>
          </li>
          <li id="nav-explore"><a href="/destinations/">Plan</a><span class="show-subnav"><a href="#"></a></span>
            <div class="subnav">
              <div class="main-nav-item"><a href="/destinations/">Tour Select Destinations</a></div>
              <div class="nav-item">
                <h3>Top Destinations</h3>
                <div class="subnav-related"><a href="/destinations/">See All</a></div>
<?php
$expltopdest = wp_nav_menu( array('menu' => 'explore-top-destinations','container' => '','container_id' => '','container_class' => '','menu_class' => '','echo' => false ));
echo $expltopdest;
?>
              </div>
              <div class="nav-item">
                <h3>Indagare Spotlight</h3>
                <div class="subnav-related"><a href="/destinations/">See All</a></div>
<?php
$spotlight = wp_nav_menu( array('menu' => 'explore-spotlight','container' => '','container_id' => '','container_class' => '','menu_class' => '','echo' => false ));
echo $spotlight;
?>
              </div>
            </div>
          </li>
          <li id="nav-book"><a href="/book/">Book</a><span class="show-subnav"><a href="#"></a></span>
            <div class="subnav">
              <div class="main-nav-item"><a href="/destinations/">View All Destinations</a></div>
              <div class="nav-item">
                <h3>Top Destinations</h3>
                <div class="subnav-related"><a href="/destinations/">See All</a></div>
<?php
$booktopdest = wp_nav_menu( array('menu' => 'book-top-destinations','container' => '','container_id' => '','container_class' => '','menu_class' => '','echo' => false ));
echo $booktopdest;
?>
              </div>
              <div class="nav-item">
                <h3>Top Hotels</h3>
<?php
$tophotels = wp_nav_menu( array('menu' => 'book-top-hotels','container' => '','container_id' => '','container_class' => '','menu_class' => '','echo' => false ));
echo $tophotels;
?>
                <div class="subnav-related"><a href="/destinations/offers/">See Partner Promotions</a></div>
                <div class="subnav-related"><a href="/destinations/insidertrips/">See Insider Trips</a></div>
              </div>
            </div>
          </li>
          <li id="nav-shop"><a href="http://www.shoplatitude.com/collections/indagare" target="_blank">Shop</a></li>
<?php
	if ( ind_logged_in() ) {
?>
          <li id="nav-account" class="loggedin single"><a href="#">Account</a><span class="show-subnav"><a href="#"></a></span>
            <div class="subnav">
              <div class="nav-item">
<?php
	$account = wp_nav_menu( array('menu' => 'account','container' => '','container_id' => '','container_class' => '','echo' => false ));
	if( is_user_logged_in() ) {
		$account = str_replace('</ul>', '<li><a href="' . wp_logout_url( get_permalink() ) . '">Log Out</a></li></ul>', $account);
	} else {
		$account = str_replace('</ul>', '<li><a href="' . get_bloginfo( 'stylesheet_directory' ) . '/logout.php">Log Out</a></li></ul>', $account);
	}
	echo $account;
?>
              </div>
            </div>
          </li>
<?php
	} else {
          echo '<li id="nav-login"><a href="#lightbox-login" class="lightbox-inline">Log In</a></li>'."\n";
?>
          <li id="nav-account" class="single"><a href="#">Join</a><span class="show-subnav"><a href="#"></a></span>
            <div class="subnav">
              <div class="nav-item">
<?php
	$footermembership = wp_nav_menu( array('menu' => 'footer-membership','container' => '','container_id' => '','container_class' => '','echo' => false ));
	echo $footermembership;
?>
              </div>
            </div>
          </li>
<?php
	}

?>
        </ul>
      </section>
      <section id="search-indagare" class="box collapsible">
        <form id="searchform" name="searchform" method="get" action="/">
          <label>Search</label>
        	<div class="form-combo">
          	<span class="form-item"><input type="text" id="search-site" name="s" class="element" /><b class="icon" data-icon="&#xf002;"></b></span>
          </div>
        </form>
      </section>
    </div>
  </div>
<?php
}

// above container
function child_abovecontainer() {
global $post;

	// start child_abovecontainer conditional
	if ( is_singular() ) {
		$destinationstree = destinationstree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
	}

	if ( is_archive() ) {
		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
		$depth = $destinationstree['depth'];

	}

	$destinationname = $dest->name;
	$destinationslug = $dest->slug;

	$hotel = new WP_Query(array('post_type' => 'hotel', 'destinations' => $destinationslug));
	$hotelcount  = $hotel->found_posts;

	$restaurant = new WP_Query(array('post_type' => 'restaurant', 'destinations' => $destinationslug));
	$restaurantcount  = $restaurant->found_posts;

	$shop = new WP_Query(array('post_type' => 'shop', 'destinations' => $destinationslug));
	$shopcount  = $shop->found_posts;

	$activity = new WP_Query(array('post_type' => 'activity', 'destinations' => $destinationslug));
	$activitycount  = $activity->found_posts;

	$itinerary = new WP_Query(array('post_type' => 'itinerary', 'destinations' => $destinationslug));
	$itinerarycount  = $itinerary->found_posts;

	$library = new WP_Query(array('post_type' => 'library', 'destinations' => $destinationslug));
	$librarycount  = $library->found_posts;

//	echo '<!-- destination '.$destinationslug.' '.$destinationname.' -->';

	// join pages common navigation
	if ( is_page() && get_field('membership') == 'yes' ) {
		$navjoin = wp_nav_menu( array('menu' => 'footer-membership','container' => 'div','container_id' => '','container_class' => 'header magazine contain','menu_id' => 'subnav-magazine','echo' => false ));
		echo $navjoin;
	}
	// end join pages common navigation

	// about pages common navigation
	if ( (is_page() && get_field('about') == 'yes') || is_author() || is_archive() && get_query_var('post_type') == 'press' || is_archive() && get_query_var('post_type') == 'career' ) {
		$navabout = wp_nav_menu( array('menu' => 'footer-about','container' => 'div','container_id' => '','container_class' => 'header magazine contain','menu_id' => 'subnav-magazine','echo' => false ));
		echo $navabout;
	}
	// end about pages common navigation

	// account common navigation
	if ( is_page() && get_field('account') == 'yes' ) {
		/*
		echo '<div class="header magazine contain">'."\n";
			echo '<ul id="subnav-magazine" class="menu">'."\n";
				echo '<li class="menu-item menu-item-type-post_type current-menu-item"><a href="/travel/">My Wish List</a></li>'."\n";
			echo '</ul>'."\n";
		echo '</div>'."\n";
		*/
		$navabout = wp_nav_menu( array('menu' => 'account','container' => 'div','container_id' => '','container_class' => 'header magazine contain','menu_id' => 'subnav-magazine','echo' => false ));
		if( is_user_logged_in() ) {
			$navabout = str_replace('</ul>', '<li><a href="' . wp_logout_url( get_permalink() ) . '">Log Out</a></li></ul>', $navabout);
		} else {
			$navabout = str_replace('</ul>', '<li><a href="'.get_bloginfo('stylesheet_directory').'/logout.php">Log Out</a></li></ul>', $navabout);
		}
		echo $navabout;
	}
	// end account pages common navigation

	// search page header
	if ( is_search() ) {

		echo '<div class="header magazine search">'."\n";
			echo '<h2>Search</h2>'."\n";
			$searchvalue = urldecode( $_GET['s'] );
			$searchvalue = sanitize_text_field( $searchvalue );
			if ( $_GET['filter'] ) {
				echo '<span class="results"><a href="/?s='.urlencode($searchvalue).'">Results for "'.$searchvalue.'"</a></span>'."\n";
			} else {
				echo '<span class="results">Results for "'.$searchvalue.'"</span>'."\n";
			}
		echo '</div><!-- .header -->'."\n";

	// home page
	} else if ( is_home() || is_front_page() ) {

		$rows = get_field('home-gallery');

		if ( $rows ) {

			$i = 0;

			shuffle($rows);

			echo '<div id="rslideswrapper">'."\n";

			echo '<ul class="hero rslides">'."\n";

			foreach($rows as $row) {

				if ( $i < 8 ) {

					$imageobj = $row['home-gallery-image'];
					$image = $imageobj['sizes']['hero-full'];

					echo '<li>'."\n";

						echo '<img src="'.$image.'" alt="">'."\n";
						echo '<a href="'.$row['home-gallery-url'].'">'."\n";
						echo '<span class="slide-caption">'."\n";
							echo '<h2>'.$row['home-gallery-title'].'</h2>'."\n";
							echo $row['home-gallery-content']."\n";
						echo '</span><!-- .slide-caption -->'."\n";
						echo '</a>'."\n";
					echo '</li>'."\n";

					$i++;

				}

			}

			echo '</ul><!--.hero.rslides-->'."\n";

			echo '</div>'."\n";

		}

	// end home page

	// hotel post | restaurant post | shop post | activity post
	} else if ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) ) {

		$destinationstree = destinationstree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];

		echo '<div class="header"><h1>'.$dest->name.'</h1></div>'."\n";
		echo '<div id="subnav" class="rainbow">'."\n";
			echo '<ul>'."\n";
				echo '<li id="subnav01"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/">Overview</a></li>'."\n";
				if ( $hotelcount !== 0 ) {
					echo '<li id="subnav02"';
					if ( is_singular( 'hotel' ) ) { echo ' class="active"'; }
					echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/hotels/">Stay</a></li>'."\n";
				}
				if ( $restaurantcount !== 0 ) {
					echo '<li id="subnav03"';
					if ( is_singular( 'restaurant' ) ) { echo ' class="active"'; }
					echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/restaurants/">Eat</a></li>'."\n";
				}
				if ( $shopcount !== 0 ) {
					echo '<li id="subnav04"';
					if ( is_singular( 'shop' ) ) { echo ' class="active"'; }
					echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/shops/">Shop</a></li>'."\n";
				}
				if ( $activitycount !== 0 ) {
					echo '<li id="subnav05"';
					if ( is_singular( 'activity' ) ) { echo ' class="active"'; }
					echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/activities/">See &amp; Do</a></li>'."\n";
				}
				if ( $itinerarycount !== 0 ) {
					echo '<li id="subnav06"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/itineraries/">Itinerary</a></li>'."\n";
				}
				if ( $librarycount !== 0 ) {
					echo '<li id="subnav07"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/library/">Library</a></li>'."\n";
				}
			echo '</ul>'."\n";
		echo '</div>'."\n";

	// end hotel post | restaurant post | shop post | activity post

	// archive for hotel | restaurant | shop | activity | itinerary | library
	} else if (
			( is_archive() && get_query_var('post_type') == 'hotel' )
			|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
			|| ( is_archive() && get_query_var('post_type') == 'shop' )
			|| ( is_archive() && get_query_var('post_type') == 'activity' )
			|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
			|| ( is_archive() && get_query_var('post_type') == 'library' )
		)
	 {

		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];

		// destination level with post type
		if ( $dest ) {

			echo '<div class="header"><h1>'.$dest->name.'<span class="return"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/"><b class="icon petite" data-icon="&#xf0d9;"></b> Back to '.$reg->name.'</a></span></h1></div>'."\n";
			echo '<div id="subnav" class="rainbow">'."\n";
				echo '<ul>'."\n";
					echo '<li id="subnav01"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/">Overview</a></li>'."\n";
					if ( $hotelcount !== 0 ) {
						echo '<li id="subnav02"';
						if ( get_query_var('post_type') == 'hotel' ) { echo ' class="active"'; }
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/hotels/">Stay</a></li>'."\n";
					}
					if ( $restaurantcount !== 0 ) {
						echo '<li id="subnav03"';
						if ( get_query_var('post_type') == 'restaurant' ) { echo ' class="active"'; }
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/restaurants/">Eat</a></li>'."\n";
					}
					if ( $shopcount !== 0 ) {
						echo '<li id="subnav04"';
						if ( get_query_var('post_type') == 'shop' ) { echo ' class="active"'; }
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/shops/">Shop</a></li>'."\n";
					}
					if ( $activitycount !== 0 ) {
						echo '<li id="subnav05"';
						if ( get_query_var('post_type') == 'activity' ) { echo ' class="active"'; }
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/activities/">See &amp; Do</a></li>'."\n";
					}
					if ( $itinerarycount !== 0 ) {
						echo '<li id="subnav06"';
						if ( get_query_var('post_type') == 'itinerary' ) { echo ' class="active"'; }
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/itineraries/">Itinerary</a></li>'."\n";
					}
					if ( $librarycount !== 0 ) {
						echo '<li id="subnav07"';
						if ( get_query_var('post_type') == 'library' ) { echo ' class="active"'; }
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/library/">Library</a></li>'."\n";
					}
				echo '</ul>'."\n";
			echo '</div>'."\n";

			// hero image for restaurant
			if ( is_archive() && get_query_var('post_type') == 'restaurant' )  {

				$imageobj = get_field('eat-header-image', 'destinations' . '_' . $dest->term_id);
				$image = $imageobj['sizes']['hero-full'];
				$caption = get_field('eat-header-image-caption', 'destinations' . '_' . $dest->term_id);
				$overview = get_field('eat-overview', 'destinations' . '_' . $dest->term_id);

				if ( $image || $overview ) {
					echo '<div class="hero">'."\n";
					if ( $image ) {
						echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
					}
					if ( $caption ) {
						echo '<p class="summary">'.$caption.'</p>'."\n";
					}
					if ( $overview ) {
						echo $overview;
					}
					echo '</div>'."\n";
				}
			// end hero image for restaurant

			// hero image for shop
			} else if ( is_archive() && get_query_var('post_type') == 'shop' )  {

				$imageobj = get_field('shop-header-image', 'destinations' . '_' . $dest->term_id);
				$image = $imageobj['sizes']['hero-full'];
				$caption = get_field('shop-header-image-caption', 'destinations' . '_' . $dest->term_id);
				$overview = get_field('shop-overview', 'destinations' . '_' . $dest->term_id);

				if ( $image || $overview ) {
					echo '<div class="hero">'."\n";
					if ( $image ) {
						echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
					}
					if ( $caption ) {
						echo '<p class="summary">'.$caption.'</p>'."\n";
					}
					if ( $overview ) {
						echo $overview;
					}
					echo '</div>'."\n";
				}
			// end hero image for shop

			// hero image for activity
			} else if ( is_archive() && get_query_var('post_type') == 'activity' )  {

				$imageobj = get_field('activity-header-image', 'destinations' . '_' . $dest->term_id);
				$image = $imageobj['sizes']['hero-full'];
				$caption = get_field('activity-header-image-caption', 'destinations' . '_' . $dest->term_id);
				$overview = get_field('activity-overview', 'destinations' . '_' . $dest->term_id);

				if ( $image || $overview ) {
					echo '<div class="hero">'."\n";
					if ( $image ) {
						echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
					}
					if ( $caption ) {
						echo '<p class="summary">'.$caption.'</p>'."\n";
					}
					if ( $overview ) {
						echo $overview;
					}
					echo '</div>'."\n";
				}
			// end hero image for activity

			// hero image for library
			} else if ( is_archive() && get_query_var('post_type') == 'library' )  {

				while ( have_posts() ) : the_post();
					echo '<div class="hero">'."\n";
					$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );

					$imageobj = get_field('destinations-library-image', 'option');
					$image = $imageobj['sizes']['hero-full'];
					$imagecaption = $imageobj['caption'];

					if ( $image ) {
						echo '<img src="'.$image.'" alt="destination-hero" />'."\n";

						if ( $image && $imagecaption ) {
							echo '<p>'.$imagecaption.'</p>'."\n";
						}

					}

					echo '</div>'."\n";
				endwhile;

			// end hero image for library
			}

		}

	// end archive for hotel | restaurant | shop | activity | itinerary | library

	// archive for offer
	} else if ( is_archive() && get_query_var('post_type') == 'offer' ) {

		echo '<div class="header top">'."\n";
			echo '<b class="icon custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b> <h1>Indagare Partner Promotions</h1>'."\n";
		echo '</div><!-- .header -->'."\n";

	// end archive for offer

	// archive for article
	} else if ( is_archive() && get_query_var('post_type') == 'article' ) {
		//////////////// ARTICLE

    	$filter = getLastPathSegment($_SERVER['REQUEST_URI']);

		echo '<div class="header magazine">'."\n";
			echo '<h2>Indagare <span class="highlight">Magazine</span></h2>'."\n";

			echo '<ul id="subnav-magazine">'."\n";
				if ( $filter == 'features' ) {
					echo '<li class="current"><a href="/destinations/articles/features/">Features</a></li>'."\n";
				} else {
					echo '<li><a href="/destinations/articles/features/">Features</a></li>'."\n";
				}
				if ( $_GET['column'] ) {
					echo '<li class="parent current"><a href="#">Columns</a>'."\n";
				} else {
					echo '<li class="parent"><a href="#">Columns</a>'."\n";
				}

				$columns = get_terms( 'column', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

				if ( $columns ) {
					echo '<ul class="subnav">'."\n";
						foreach ( $columns as $term ) {
							echo '<li><a href="/destinations/articles/?column='.$term->slug.'">'.$term->name.'</a></li>'."\n";
						}
					echo '</ul>'."\n";
				}
				echo '</li>'."\n";
				if ( $filter !== 'features' && !$_GET['column']) {
					echo '<li class="current"><a href="/destinations/articles/">All Articles</a></li>'."\n";
				} else {
					echo '<li><a href="/destinations/articles/">All Articles</a></li>'."\n";
				}
				echo '<li><a href="/magazines/">Digital Magazine</a></li>'."\n";
			echo '</ul><!-- #subnav-magazine -->'."\n";

		echo '</div><!-- .header -->'."\n";

		// hero article for articles landing page
		if ( $filter == 'features' ) {

			$args = array('posts_per_page' => 1, 'post_type' => 'article', 'meta_key' => 'hero-article', 'meta_value' => 'yes', 'orderby' => 'rand');

			$hero = new WP_Query($args);

			if($hero->have_posts() ) {

				echo '<ul class="hero rslides">'."\n";

				while ( $hero->have_posts() ) : $hero->the_post();

//					$rows = get_field('gallery-header');

					$rowsraw = get_field('gallery-header', false, false);

					if ( $rowsraw ) {
						$imageid = $rowsraw[0];
						$imageobj = wp_get_attachment_image_src( $imageid, 'hero-full' );
						$imgsrc = $imageobj[0];
					} else {
						$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
						$imgsrc = $imageobj[0];
					}

					$column = wp_get_post_terms( $post->ID, 'column' );

					echo '<li>'."\n";
						echo '<a href="'.get_permalink().'">'."\n";
						echo '<img src="'.$imgsrc.'" alt="Article">'."\n";
						echo '<span class="slide-caption">'."\n";
							echo '<h2>'.$column[0]->name.': '.get_the_title().'</h2>'."\n";
						echo '</span><!-- .slide-caption -->'."\n";
						echo '</a>'."\n";
					echo '</li>'."\n";

				endwhile;

				echo '</ul><!--.hero.rslides-->'."\n";

				wp_reset_postdata();

			}

		// article filters
		} else {

			echo '<div class="header filter">'."\n";
				echo '<h2>All Articles</h2>'."\n";
				echo '<button class="button filters">+ Show Filters</button>'."\n";

				echo '<div id="magazine-filters" class="">'."\n";
					echo '<h4>By Interests</h4>'."\n";

					$interests = get_terms( 'interest', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false) );

					if ( $interests ) {
						echo '<ul class="filter-interest">'."\n";
							foreach ( $interests as $term ) {
								$interesticon = get_field('data-icon', 'interest' . '_' . $term->term_id);
								echo '<li><a href="#" title="'.$term->slug.'"><b class="icon box" data-icon="&#'.$interesticon.';"></b>' .$term->name.'</a></li>'."\n";
							}
						echo '</ul>'."\n";
					}

					echo '<span class="filter-destination">'."\n";
						echo '<h4>By Destination</h4>'."\n";
						echo '<input id="inputdestination" type="text" placeholder="Filter by city or region" />'."\n";
						echo '<div class="autocomplete"></div>'."\n";
						if ( $_GET['destinations'] ) {
							echo '<input class="autocompletedestination" type="hidden" value="'.$_GET['destinations'].'" />'."\n";
						} else {
							echo '<input class="autocompletedestination" type="hidden" />'."\n";
						}
					echo '</span><!-- .filter-destination -->'."\n";

					echo '<span class="filter-apply">'."\n";
						$column = '';
						if(!empty($_GET['column'])) {
							$column = '?column='.$_GET['column'];
						}
						echo '<button class="button apply-filters">Apply Filters</button> or <a href="/destinations/articles/'.$column.'">remove all filters</a>'."\n";
					echo '</span><!-- .filter-apply -->'."\n";

				echo '</div><!-- #magazine-filters -->'."\n";

			echo '</div><!-- .header.filter -->'."\n";
	export_destinations( false );
?>
<script>
if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}
jQuery().ready(function($) {
	jQuery.ajax({
		url: "/export/datadestinations_ac.json"
	}).done(function(d) {
		$(".filter-destination input#inputdestination").autocomplete({
			resultsContainer: '.autocomplete',
			onItemSelect: function(item) {
				$('.autocompletedestination').val(item.data);
			},
			onNoMatch: function() {
			},
			data: d
		});
	});

	// toggle article interest as active
	$("ul.filter-interest li").click(function(e) {
		$(this).find('a').toggleClass('active-box');
	});

	// check for urlvars for article interests
	var interestschecked = getURLParameter('interest');
	var interestslist = interestschecked.split(",");

	// toggle article interest based on urlvars
	if ( interestslist.length > 0 ) {
		interestslist.forEach(function(item) {
			var ele = $('ul.filter-interest li').find('a[title='+item+']');
				ele.toggleClass('active-box');
		});
	}

	// check for urlvars for destination
	var destinationfilter = getURLParameter('destinations');

	// set destination filter based on urlvars
	if ( destinationfilter ) {
<?php
		$destination = get_term_by( 'slug', $_GET['destinations'], 'destinations' );
		$destinationname = $destination->name;
		echo '$(\'#inputdestination\').val(\''.$destinationname.'\');'."\n";
?>
	}

	// apply filters
    $('#magazine-filters button.apply-filters').click(function(event) {
   		event.preventDefault();

   		var interests = $("ul.filter-interest li a.active-box").map(function(){
	        return $(this).attr('title');
	    }).toArray();

   		var interestsurl = '';
   		var desturl = '';
			var urlparams = new Array();
			var urlvars = '/destinations/articles/';

			var column = getURLParameter('column');
			if ( !!column.length ) {
				if(column != 'null')
					urlparams.push('column=' + column);
			}

    	// get interests
			if ( interests.length > 1 ) {

				i = 1;
				interests.forEach(function(item) {

					if ( i !== 1 ) {
						interestsurl += ','+item;
					} else {
						interestsurl += item;
					}
					i++;

				});

			} else if ( interests.length == 1 ) {
					interestsurl = interests[0];
			}

			// get destination
			if ( $('.autocompletedestination').val() ) {
				desturl = $('.autocompletedestination').val();
			}

			// build urlvars based on filters selected
			if ( !!interestsurl.length ) {
				urlparams.push('interest=' + interestsurl);
			}

			if ( !!desturl.length ) {
				urlparams.push('destinations=' + desturl);
			}

			if( !!urlparams.length ) {
				urlvars += '\?' + urlparams.join('\&');
			}

			// change url if we've built a different URL
			var currenturl = new String(window.location.href);
			currenturl = currenturl.replace(/#.*$/, "");
			if ( !currenturl.endsWith( urlvars ) ) {
				window.location.href = urlvars;
			}

   	}); // end apply filters


});
</script>
<?php
		} // end article filters


	// end archive for article
	//////////////// END ARTICLE

	// article post
	} else if ( is_singular( 'article' ) ) {

    	$filter = getLastPathSegment($_SERVER['REQUEST_URI']);

		echo '<div class="header magazine">'."\n";
			echo '<h2>Indagare <span class="highlight">Magazine</span></h2>'."\n";

			echo '<ul id="subnav-magazine">'."\n";
				if ( $filter == 'features' ) {
					echo '<li class="current"><a href="/destinations/articles/features/">Features</a></li>'."\n";
				} else {
					echo '<li><a href="/destinations/articles/features/">Features</a></li>'."\n";
				}
				echo '<li class="parent"><a href="#">Columns</a>'."\n";

				$columns = get_terms( 'column', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

				if ( $columns ) {
					echo '<ul class="subnav">'."\n";
						foreach ( $columns as $term ) {
							echo '<li><a href="/destinations/articles/?column='.$term->slug.'">'.$term->name.'</a></li>'."\n";
						}
					echo '</ul>'."\n";
				}
				echo '</li>'."\n";

				wp_reset_postdata();

				if ( $filter !== 'features') {
					echo '<li class="current"><a href="/destinations/articles/">All Articles</a></li>'."\n";
				} else {
					echo '<li><a href="/destinations/articles/">All Articles</a></li>'."\n";
				}
				echo '<li><a href="/magazines/">Digital Magazine</a></li>'."\n";
			echo '</ul><!-- #subnav-magazine -->'."\n";

		echo '</div><!-- .header -->'."\n";

//		$rows = get_field('gallery-header');

		$rowsraw = get_field('gallery-header', false, false);

		if ( $rowsraw ) {
			echo '<div class="header slider">'."\n";
		} else {
			echo '<div class="header">'."\n";
		}
			echo '<h1>'.get_the_title($post->ID).'</h1>'."\n";
		echo '</div><!-- .header -->'."\n";

		if($rowsraw) {

			echo '<div id="gallery-header" class="photo-gallery hero">'."\n";
				echo '<div id="rslideswrapper">'."\n";

				echo '<ul class="hero rslides">'."\n";

				foreach($rowsraw as $imageid) {

					$imageobj = wp_get_attachment_image_src( $imageid, 'hero-full' );
					$imgsrc = $imageobj[0];
					$caption = get_post($imageid)->post_excerpt;

					echo '<li>'."\n";
						echo '<img class="rsImg" alt="'.$caption.'" src="'.$imgsrc.'">'."\n";
						if ( $caption ) {
							echo '<div class="caption">'.$caption.'</div>'."\n";
//							echo '<p class="summary">'.$caption.'</p>'."\n";
						}
					echo '</li>'."\n";


				}

				echo '</ul><!--.hero.rslides-->'."\n";

				echo '</div>'."\n";
 			echo '</div>'."\n";

		}

	// end article post

	// archive for magazine
	} else if ( is_archive() && get_query_var('post_type') == 'magazine' ) {

		echo '<div class="header magazine">'."\n";
			echo '<h2>Indagare <span class="highlight">Magazine</span></h2>'."\n";

			echo '<ul id="subnav-magazine">'."\n";
				echo '<li><a href="/destinations/articles/features/">Features</a></li>'."\n";
				echo '<li class="parent"><a href="#">Columns</a>'."\n";

				$columns = get_terms( 'column', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

				if ( $columns ) {
					echo '<ul class="subnav">'."\n";
						foreach ( $columns as $term ) {
							echo '<li><a href="/destinations/articles/?column='.$term->slug.'">'.$term->name.'</a></li>'."\n";
						}
					echo '</ul>'."\n";
				}
				echo '</li>'."\n";
				echo '<li><a href="/destinations/articles/">All Articles</a></li>'."\n";
				echo '<li class="current"><a href="/magazines/">Digital Magazine</a></li>'."\n";
			echo '</ul><!-- #subnav-magazine -->'."\n";

		echo '</div><!-- .header -->'."\n";

	// end archive for magazine

	// magazine post
	} else if ( is_singular( 'magazine' ) ) {

		echo '<div class="header magazine">'."\n";
			echo '<h2>Indagare <span class="highlight">Magazine</span></h2>'."\n";

			echo '<ul id="subnav-magazine">'."\n";
				echo '<li><a href="/destinations/articles/features/">Features</a></li>'."\n";
				echo '<li class="parent"><a href="#">Columns</a>'."\n";

				$columns = get_terms( 'column', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

				if ( $columns ) {
					echo '<ul class="subnav">'."\n";
						foreach ( $columns as $term ) {
							echo '<li><a href="/destinations/articles/?column='.$term->slug.'">'.$term->name.'</a></li>'."\n";
						}
					echo '</ul>'."\n";
				}
				echo '</li>'."\n";
				echo '<li><a href="/destinations/articles/">All Articles</a></li>'."\n";
				echo '<li class="current"><a href="/magazines/">Digital Magazine</a></li>'."\n";
			echo '</ul><!-- #subnav-magazine -->'."\n";

		echo '</div><!-- .header -->'."\n";

	// end magazine post

	// archive for insidertrip
	} else if ( is_archive() && get_query_var('post_type') == 'insidertrip' ) {

		$imageobj = get_field('insidertrip-header-image', 'option');

		$imageid = $imageobj[id];
		$image = $imageobj[sizes]['hero-full'];
		$overview = get_field('insidertrip-header-image-caption', 'option');
		$caption = get_post( $imageid )->post_excerpt;

		echo '<div class="header">'."\n";
			echo '<h1>Insider Trips</h1>'."\n";
		echo '</div><!-- .header -->'."\n";

		echo '<div class="hero">'."\n";
			echo '<img src="'.$image.'" alt="insider-hero" />'."\n";
			echo '<p class="summary">'.$caption.'</p>'."\n";
			echo '<p>'.$overview.'</p>'."\n";
		echo '</div><!-- .hero -->'."\n";

	// end archive for insidertrip

	// archive for region
	} else if ( $reg && $depth == 1  ) {

		echo '<div class="header"><h1>'.$reg->name.'</h1></div>'."\n";

	// end archive for destination

	// archive for destination
	} else if ( $dest && $depth == 2  ) {

		echo '<div class="header"><h1>'.$dest->name.'<span class="return"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/"><b class="icon petite" data-icon="&#xf0d9;"></b> Back to '.$reg->name.'</a></span></h1></div>'."\n";
			echo '<div id="subnav" class="rainbow">'."\n";
				echo '<ul>'."\n";
					echo '<li id="subnav01" class="active"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/">Overview</a></li>'."\n";
					if ( $hotelcount !== 0 ) {
						echo '<li id="subnav02"';
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/hotels/">Stay</a></li>'."\n";
					}
					if ( $restaurantcount !== 0 ) {
						echo '<li id="subnav03"';
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/restaurants/">Eat</a></li>'."\n";
					}
					if ( $shopcount !== 0 ) {
						echo '<li id="subnav04"';
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/shops/">Shop</a></li>'."\n";
					}
					if ( $activitycount !== 0 ) {
						echo '<li id="subnav05"';
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/activities/">See &amp; Do</a></li>'."\n";
					}
					if ( $itinerarycount !== 0 ) {
						echo '<li id="subnav06"';
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/itineraries/">Itinerary</a></li>'."\n";
					}
					if ( $librarycount !== 0 ) {
						echo '<li id="subnav07"';
						echo '><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/library/">Library</a></li>'."\n";
					}
				echo '</ul>'."\n";
			echo '</div>'."\n";

			$imageobj = get_field('header-image', 'destinations' . '_' . $dest->term_id);
			$image = $imageobj[sizes]['hero-full'];
			$caption = get_field('header-image-caption', 'destinations' . '_' . $dest->term_id);
			$overview = get_field('destination-overview', 'destinations' . '_' . $dest->term_id);
			$weather = get_field('weather-code', 'destinations' . '_' . $dest->term_id);

			if ( $image || $overview ) {
				echo '<div class="hero">'."\n";
				if ( $image ) {
					echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
				}
				if ( $caption ) {
					echo '<p class="summary">'.$caption.'</p>'."\n";
				}
				if ( $overview ) {
					echo $overview;
				}

				// weather code
				if (stristr($weather, ',') !== false) {
					require_once('includes/weather.php');
					print format_weather($weather);
				}

				echo '</div>'."\n";
			}
	// end archive for destination

	// map page
	} else if (is_page_template ( 'template-page-map.php' ) ) {
    include_once 'includes/map-locations.php';
		echo map_canvas(false, 'hero');
		maplocations();  // Make sure the JSON is there!
	// end map page

	// book page
	} else if (is_page_template ( 'template-page-book.php' ) ) {

		echo '<div class="header">'."\n";
		echo '<h1>'.get_the_title().'</h1>'."\n";
		echo '</div>'."\n";

		$rows = get_field('book-header');

		if ( $rows ) {

			shuffle($rows);

			echo '<div id="rslideswrapper">'."\n";

			echo '<ul class="hero rslides">'."\n";

			foreach($rows as $row) : setup_postdata($row);

				$destinationstree = destinationstree($row->ID);
				$dest = $destinationstree['dest'];
				$reg = $destinationstree['reg'];
				$top = $destinationstree['top'];

//				$rows = get_field('gallery-header',$row->ID);

				$rowsraw = get_field('gallery-header', $row->ID, false);

				if ( $rowsraw ) {
					$imageid = $rowsraw[0];
					$imageobj = wp_get_attachment_image_src( $imageid, 'hero-full' );
					$imgsrc = $imageobj[0];
				} else {
					$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($row->ID), 'hero-full' );
					$imgsrc = $imageobj[0];
				}

				echo '<li>'."\n";

					$benefit = get_field('benefit',$row->ID);

					echo '<img src="'.$imgsrc.'" alt="">'."\n";
					echo '<span class="slide-caption">'."\n";
						echo '<a href="'.get_permalink($row->ID).'">'."\n";
						echo '<h2>'.get_the_title($row->ID).'</h2>'."\n";
						echo '<p>'.$dest->name.', '.$reg->name.'</p>'."\n";
						echo '</a>'."\n";
						if ( $benefit ) {
							echo '<div class="benefitwrapper">'."\n";
							echo '<p class="benefitmore">See Indagare Plus Amenities</p>'."\n";
							echo '<div class="benefit">'."\n";
							echo $benefit[0]['benefit-content'];
							echo '</div>'."\n";
							echo '</div>'."\n";
						}
					echo '</span><!-- .slide-caption -->'."\n";
				echo '</li>'."\n";

			endforeach;

			echo '</ul><!--.hero.rslides-->'."\n";

			echo '</div>'."\n";

		}

	// end book page

	// sign up step one page
	} else if (is_page_template ( 'template-page-user-signup.php' ) ) {

/*
		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
*/

	// end sign up step one page

	// how to book page
	} else if (is_page_template ( 'template-page-how-to-book.php' ) ) {

/*
		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
*/

	// end how to book page

	// contact page
	} else if (is_page_template ( 'template-page-contact.php' ) ) {

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
	// end contact page

	// why join page
	} else if (is_page_template ( 'template-page-why-join.php' ) ) {

/*
		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		if ( $image || $overview ) {
			echo '<div class="hero contain">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( ! ind_logged_in() ) {
				echo '<a class="button primary floatright" href="/join/">Join</a>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
*/

	// end why join page

	// how we work page
	} else if (is_page_template ( 'template-page-how-we-work.php' ) ) {

/*
		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];

		if ( $image || $overview ) {
			echo '<div class="hero heronoborder contain">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			echo '</div>'."\n";
		}
*/

	// end how we work page

	// welcome page
	} else if ( is_page_template ( 'template-page-welcome.php' ) ) {

/*
		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;
*/
		$overview = $post->post_content;

		echo '<div class="header">'."\n";
		echo '<h1>'.get_the_title().'</h1>'."\n";
		echo '</div>'."\n";

/*
		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
*/

		$rowsraw = get_field('gallery-header', $post->ID, false);

		if($rowsraw) {

			echo '<div id="gallery-header" class="photo-gallery hero">'."\n";
				echo '<div id="rslideswrapper">'."\n";

				echo '<ul class="hero rslides">'."\n";

				foreach($rowsraw as $imageid) {

					$imageobj = wp_get_attachment_image_src( $imageid, 'hero-full' );
					$imgsrc = $imageobj[0];
					$caption = get_post($imageid)->post_excerpt;

					echo '<li>'."\n";
						echo '<img class="rsImg" alt="'.$caption.'" src="'.$imgsrc.'">'."\n";
						if ( $caption ) {
							echo '<div class="caption">'.$caption.'</div>'."\n";
						}
					echo '</li>'."\n";


				}

				echo '</ul><!--.hero.rslides-->'."\n";

				echo '</div>'."\n";

			if ( $overview ) {
				echo $overview;
			}

 			echo '</div>'."\n";

		}

	// end welcome page

	// new page
	} else if ( is_page_template ( 'template-page-new.php' ) ) {

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		echo '<div class="header">'."\n";
		echo '<h1>'.get_the_title().'</h1>'."\n";
		echo '</div>'."\n";

		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
	// end new page

	// password reset page || external login page
	} else if (
		is_page_template ( 'template-page-password-reset.php' ) ||
		is_page_template ( 'template-page-external-login.php' )
	) {

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";
		}
	// end password reset page

	// 404 page
	} else if ( is_404() ) {

		$args = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => '_wp_page_template',
					'value' => 'template-page-404.php', // template name as stored in the dB
				)
			)
		);

		$notfound = new WP_Query($args);

		while ($notfound->have_posts()) : $notfound->the_post();

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-full' );
		$image = $imageobj[0];
		$overview = $post->post_content;
		$caption = get_post( get_post_thumbnail_id() )->post_excerpt;

		echo '<div class="header">'."\n";
		echo '<h1>'.get_the_title().'</h1>'."\n";
		echo '</div>'."\n";

		if ( $image || $overview ) {
			echo '<div class="hero">'."\n";
			if ( $image ) {
				echo '<img src="'.$image.'" alt="destination-hero" />'."\n";
			}
			if ( $caption ) {
				echo '<p class="summary">'.$caption.'</p>'."\n";
			}
			if ( $overview ) {
				echo $overview;
			}
			echo '</div>'."\n";

		}

		endwhile;

	// end 404 page

	} // end child_abovecontainer conditional

}
add_filter('thematic_abovecontainer','child_abovecontainer');

// override archive loop
function childtheme_override_archive_loop() {
	global $post;
 	global $wp_query;
	$max = $wp_query->max_num_pages;
	global $count;
	$count = $wp_query->found_posts;

	$destinationstree = destinationstaxtree();
	$dest = $destinationstree['dest'];
	$reg = $destinationstree['reg'];
	$top = $destinationstree['top'];
	$depth = $destinationstree['depth'];

// debug
//	print_r ( 'destination ' .$wp_query->query_vars['destinations'] );
//	print_r ( 'destinationstree ' . $destinationstree );

	$regionid = $reg->term_id;

	$destinationid = $dest->term_id;
	$destinationname = $dest->name;

	$itinerary = new WP_Query(array('post_type' => 'itinerary', 'destinations' => $destinationname));
	$itinerarycount  = $itinerary->found_posts;

	$library = new WP_Query(array('post_type' => 'library', 'destinations' => $destinationname));
	$librarycount  = $library->found_posts;

	// archive for hotel | restaurant | shop | activity
	if (
			( is_archive() && get_query_var('post_type') == 'hotel' )
			|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
			|| ( is_archive() && get_query_var('post_type') == 'shop' )
			|| ( is_archive() && get_query_var('post_type') == 'activity' )
		)
	{

		// label for post count - singular or plural
		$post_type_object = get_post_type_object(get_query_var('post_type'));
		if ( $count > 1 ) {
			$postypename = $post_type_object->labels->name;
		} else {
			$postypename = $post_type_object->labels->singular_name;
		}

// debug
//		print_r ( $reg );
//		print_r ( $dest );
//		print_r ( $depth );

		if ( $depth == 1 ) { // region level
			echo '<div class="header"><h2>'.$postypename.' ('.$count.') in '.$reg->name.'</h2>';
		} else { // destination level
			echo '<div class="header"><h2>'.$postypename.' ('.$count.') in '.$dest->name.', '.$reg->name.'</h2>';
		}
		if ( $count > 0 ) {
			echo '<p class="view-more"><a class="map" href="#"><b class="icon petite custom-icon" data-icon="&#xe000;"></b>Show Map</a></p>';
		}
		echo '</div>'."\n";

		echo map_canvas(true);
		echo map();

		if ( isset($_GET["hoteltype"]) || isset($_GET["restauranttype"]) || isset($_GET["shoptype"]) || isset($_GET["activitytype"])
			 || isset($_GET["destinations"]) || isset($_GET["benefit"]) || isset($_GET["editorspick"]) || isset($_GET["mealtype"]) ||
			 INDG_ALWAYSSHOW_DESTFILTERS ) {
			echo '<div id="filters" class="show-this">'."\n";
		} else {
			echo '<div id="filters">'."\n";
		}

		$filterid = 1;

			echo '<p class="open-close"><a href="#"><span class="title">Filter Results</span> <b class="icon open-this" data-icon="&#xf0d9;"><span>Open</span></b><b class="icon close-this" data-icon="&#xf0d7;"><span>Close</span></b></a></p>'."\n";
			echo '<div class="collapse">'."\n";
				echo '<div class="collapsegroup">'."\n";
					// type of post filter
					if ( get_query_var('post_type') == 'hotel' ) {
						echo '<h4>Type of Property</h4>'."\n";
					} else if ( get_query_var('post_type') == 'restaurant' ) {
						echo '<h4>Type of Restaurant</h4>'."\n";
					} else if ( get_query_var('post_type') == 'shop' ) {
						echo '<h4>Type of Shop</h4>'."\n";
					} else if ( get_query_var('post_type') == 'activity' ) {
						echo '<h4>Type of Activity</h4>'."\n";
					}

					echo '<ul id="posttypes" class="filter">'."\n";

					if ( get_query_var('post_type') == 'hotel' ) {
						$filtertype = 'hoteltype';
					} else if ( get_query_var('post_type') == 'restaurant' ) {
						$filtertype = 'restauranttype';
					} else if ( get_query_var('post_type') == 'shop' ) {
						$filtertype = 'shoptype';
					} else if ( get_query_var('post_type') == 'activity' ) {
						$filtertype = 'activitytype';
					}

						$args = array(
						  'hide_empty' => true,
						  'orderby' => 'name',
						  'order' => 'ASC'
						);
						$terms=get_terms($filtertype,$args);

// debug
//						print_r ( $terms );

						if  ($terms) {
						  foreach ($terms as $term ) {
							echo '<li><input type="checkbox" id="checkbox'.$filterid.'" value="'.$term->slug.'"> <label>'.$term->name.'</label></li>'."\n";
							$filterid++;
						  }
						}
					echo '</ul>'."\n";
					// end type of post filter

					// meal filter
					if ( get_query_var('post_type') == 'restaurant' ) {
						$meal = get_terms( 'mealtype', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

						if ( $meal ) {
							echo '<h4>Type of Meal</h4>'."\n";
							echo '<ul id="meals">'."\n";

								foreach ( $meal as $term ) {
									echo '<li><input type="checkbox" id="checkbox'.$filterid.'" value="'.$term->slug.'"> <label>'.$term->name.'</label></li>'."\n";
									$filterid++;
								}

							echo '</ul>'."\n";
						}
					}
					// end meal filter

				echo '</div>'."\n"; // close column
				echo '<div class="collapsegroup">'."\n";
					// neighborhood filter

					if ( $depth == 1 ) { // region level
						// return destinations under region - includes neighborhoods
						$neighraw = get_terms( 'destinations', array( 'child_of' => $regionid , 'hierarchical' => false, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

						// filter out neighborhoods and return only cities
						$neigh = array_filter($neighraw, function ($t) {
								$destinationstree = get_ancestors( $t->term_id, 'destinations' );
								$destinationstree = array_reverse($destinationstree);
								$destdepth = count($destinationstree);
								return $destdepth == 2;
						});

					} else { // destination level
						$neigh = get_terms( 'destinations', array( 'child_of' => $destinationid , 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );
					}

// debug
//					print_r ('neighborhoods ' . $neigh);
//					print_r ('number ' . count($neigh));
//					print_r ('depth ' . $depth);

					if ( $neigh ) {
						if ( $depth == 1 ) { // region level
							echo '<h4>Destination</h4>'."\n";
						} else { // destination level
							echo '<h4>Neighborhood</h4>'."\n";
						}
						echo '<ul id="neighborhoods" class="filter">'."\n";

							foreach ( $neigh as $term ) {
								if ( $depth == 1 ) { // region level
									echo '<li><input type="radio" name="neighborhood" id="checkbox'.$filterid.'" value="'.$term->slug.'"> <label>'.$term->name.'</label></li>'."\n";
								} else {
									echo '<li><input type="checkbox" id="checkbox'.$filterid.'" value="'.$term->slug.'"> <label>'.$term->name.'</label></li>'."\n";
								}
								$filterid++;
							}

						echo '</ul>'."\n";
					}
					// end neighborhood filter

					// hotel benefits filter
					if ( get_query_var('post_type') == 'hotel' ) {
					$benefits = get_terms( 'benefit', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );

						if ( $benefits ) {
							echo '<h4>Benefits</h4>'."\n";
							echo '<ul id="benefits" class="filter">'."\n";

								foreach ( $benefits as $term ) {
									echo '<li><input type="checkbox" id="checkbox'.$filterid.'" value="'.$term->slug.'"> <label>'.$term->name.'</label></li>'."\n";
									$filterid++;
								}

							echo '</ul>'."\n";
						}

					}
					// end hotel benefits filter

					// editor filter
					if ( get_query_var('post_type') !== 'hotel' ) {
					$editors = get_terms( 'editorspick', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );
					}

					if ( $editors ) {
						echo '<h4>Editor\'s Picks</h4>'."\n";
						echo '<ul id="editors" class="filter">'."\n";

							foreach ( $editors as $term ) {
								echo '<li><input type="checkbox" id="checkbox'.$filterid.'" value="'.$term->slug.'"> <label>'.$term->name.'</label></li>'."\n";
								$filterid++;
							}

						echo '</ul>'."\n";
					}
					// end editor filter

				echo '</div>'."\n"; // close column

				echo '<div class="buttons">'."\n";
				echo '<a id="clearfilters" class="button secondary" href="#">Clear</a>'."\n";
				echo '<a id="applyfilters" class="button primary" href="#">Apply</a>'."\n";
				echo '</div>'."\n";

				if ( $_GET['map'] ) {
					echo '<input class="showmap" type="hidden" value="show" />'."\n";
				} else {
					echo '<input class="showmap" type="hidden" />'."\n";
				}

	$queryposttype = get_query_var( 'post_type' );
	$queryposttype_info = array(
		'hotel' => 'hotels',
		'restaurant' => 'restaurants',
		'shop' => 'shops',
		'activity' => 'activities'
	);
?>
<script>
	var posttype = '';
	var posturl = '/destinations/<?php print $top->slug; ?>/<?php print $reg->slug; ?>/<?php print $dest->slug; ?>/';
	<?php if( array_key_exists( $queryposttype, $queryposttype_info ) ) : ?>
		posttype = '<?php print $queryposttype; ?>type';
		posturl += '<?php print $queryposttype_info[$queryposttype]; ?>/';
	<?php endif; ?>
</script>
<script type="text/javascript" src="<?php print get_bloginfo('stylesheet_directory'); ?>/js/post_filter.js"></script>
<?php

			echo '</div>'."\n"; // close collapse
		echo '</div>'."\n"; // close filters
		// end filters for hotel | restaurant | shop | activity

		echo '<section class="results">'."\n";

		while ( have_posts() ) : the_post();

			echo '<article id="post-';
			the_ID();
			echo '" ';
			post_class('contain');
			echo ' >'."\n";

			thematic_content();

			echo '</article><!-- #post -->'."\n";

		endwhile;

		if ( $max > 1 ) {
			echo '<p class="load-more"></p>';
		}

		echo '</section>'."\n";

	// end archive for hotel | restaurant | shop | activity

	// archive for offer
	} else if ( is_archive() && get_query_var('post_type') == 'offer' ) {

		// featured offer
		$args = array('numberposts' => 1, 'post_type' => 'offer', 'meta_key' => 'featured', 'meta_value' => 'yes', 'orderby' => 'rand');

		$featured = new WP_Query($args);

		if($featured->have_posts() ) {

			while ( $featured->have_posts() ) : $featured->the_post();

				// generate thumbnail from gallery header, if not, use featured image
//				$rows = get_field('gallery-header');

				$rowsraw = get_field('gallery-header', false, false);

				if ( $rowsraw ) {
					$imageid = $rowsraw[0];
					$imageobj = wp_get_attachment_image_src( $imageid, 'hero-medium' );
					$imgsrc = $imageobj[0];
				} else {
					$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-medium' );
					$imgsrc = $imageobj[0];
				}

				echo '<div id="hero-image">'."\n";
					if ( $imgsrc ) {
						echo '<img src="'.$imgsrc.'" alt="hero-region" />'."\n";
					}
				echo '</div><!-- #hero-image -->'."\n";

				echo '<div class="widget-wrapper">'."\n";
					echo '<div class="special-info">'."\n";
						echo '<a href="'.get_permalink().'">'."\n";
							echo '<h3>'.get_the_title().'</h3><b class="icon petite custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b>'."\n";
						echo '</a>'."\n";
							echo '<span class="location">'.get_field('subtitle').'</span>'."\n";

							$text = wpautop( get_the_content() );
							$text = substr( $text, 0, strpos( $text, '</p>' ) + 4 );
							$text = substr( $text, strpos( $text, '<p>' ), strlen($text) -3 );
							$text = strip_tags($text, '<a><strong><em><b><i>');
							$text = str_replace(']]>', ']]>', $text);
							$excerpt_length = 20; // 20 words
							$excerpt_more = apply_filters('excerpt_more', '...');
							$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

							echo '<p class="description">'.$text.'</p>'."\n";
							echo '<p class="description">'."\n";
								echo '<a class="book" href="'.get_permalink().'">Details</a>'."\n";
							echo '</p>'."\n";
					echo '</div><!-- .special-info -->'."\n";
				echo '</div><!-- .widget-wrapper -->'."\n";

				echo '<div class="header divider"></div>'."\n";

			endwhile;

		}

		wp_reset_postdata();

		// list view of offers
		echo '<section class="all-destinations contain results">'."\n";

		while ( have_posts() ) : the_post();

			$featured = get_field('featured');

			// skip featured offer
			if ( $featured !== 'yes' ) {

				echo '<article id="post-';
				the_ID();
				echo '" ';
				post_class('contain');
				echo ' >'."\n";

				thematic_content();

				echo '</article><!-- #post -->'."\n";

			}

		endwhile;

		if ( $max > 1 ) {
			echo '<p class="load-more"></p>';
		}

		echo '</section>'."\n";

	// end archive for offer

	// archive for insidertrip
	} else if ( is_archive() && get_query_var('post_type') == 'insidertrip' ) {

		// faqs
		echo '<div class="header">'."\n";
			echo '<h2>Frequently Asked Questions</h2>'."\n";
		echo '</div><!-- .header -->'."\n";

		$rows = get_field('faq','option');

		if($rows) {

			echo '<div id="faq">'."\n";

			foreach($rows as $row) {

				$q = $row['faq-question'];
				$a = $row['faq-answer'];

				echo '<h3>'.$q.'</h3>';
				echo $a;
			}

			echo '</div><!-- #faq -->'."\n";

		}

		// list view of current insidertrip
		echo '<article class="divider">'."\n";
			echo '<h2>Current Insider Trips</h2>'."\n";
		echo '</article>'."\n";

		$args = array('numberposts' => -1, 'post_type' => 'insidertrip', 'meta_key' => 'date-start', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'meta_query' => array(
				        array(
				            'key' => 'trip-state',
				            'value' => 'current'
				        ),
				    ));

		$insidertripcurrent = new WP_Query($args);

		if($insidertripcurrent->have_posts() ) {

			echo '<section class="all-destinations insider contain">'."\n";

			while ( $insidertripcurrent->have_posts() ) : $insidertripcurrent->the_post();

				// generate thumbnail from gallery header, if not, use featured image
//				$rows = get_field('gallery-header');

				$rowsraw = get_field('gallery-header', false, false);

				if ( $rowsraw ) {
					$imageid = $rowsraw[0];
					$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-large' );
					$imgsrc = $imageobj[0];
				} else {
					$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb-large' );
					$imgsrc = $imageobj[0];
				}

					echo '<article>'."\n";
						echo '<a href="'.get_permalink().'">'."\n";
						if ( $imgsrc ) {
							echo '<img src="'.$imgsrc.'" alt="Destination" />'."\n";
						}
						echo '<span class="info">'."\n";
							echo '<h3>'.get_the_title().'</h3>'."\n";

							$text = wpautop( get_the_content() );
							$text = substr( $text, 0, strpos( $text, '</p>' ) + 4 );
							$text = substr( $text, strpos( $text, '<p>' ), strlen($text) -3 );
							$text = strip_tags($text, '<a><strong><em><b><i>');
							$text = str_replace(']]>', ']]>', $text);
							$excerpt_length = 20; // 20 words
							$excerpt_more = apply_filters('excerpt_more', '...');
							$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

							echo '<p>'.$text.'</p>'."\n";
							echo '<a href="'.get_permalink().'">Read More</a>'."\n";
						echo '</span><!-- .info -->'."\n";
						echo '</a>'."\n";
					echo '</article>'."\n";

			endwhile;

			echo '</section>'."\n";

		}

		wp_reset_postdata();

		// list view of soldout insidertrip
		$args = array('numberposts' => -1, 'post_type' => 'insidertrip', 'meta_key' => 'date-start', 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_query' => array(
				        array(
				            'key' => 'trip-state',
				            'value' => 'soldout'
				        ),
				    ));

		query_posts($args);

		// are there soldout insidertrips
		if ( have_posts() ) {

			echo '<article class="divider">'."\n";
				echo '<h2>Sold Out Insider Trips</h2>'."\n";
			echo '</article>'."\n";

			echo '<section class="all-destinations mini contain">'."\n";

			while ( have_posts() ) : the_post();

				echo '<article id="post-';
				the_ID();
				echo '" ';
				post_class('contain');
				echo ' >'."\n";

				thematic_content();

				echo '</article><!-- #post -->'."\n";

			endwhile;

			echo '</section>'."\n";

		}

		// list view of past insidertrip
		echo '<article class="divider">'."\n";
			echo '<h2>Some Past Insider Trips</h2>'."\n";
		echo '</article>'."\n";

		echo '<section class="all-destinations mini contain">'."\n";

		$args = array('numberposts' => -1, 'post_type' => 'insidertrip', 'meta_key' => 'date-start', 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_query' => array(
				        array(
				            'key' => 'trip-state',
				            'value' => 'past'
				        ),
				    ));

		query_posts($args);

		while ( have_posts() ) : the_post();

			echo '<article id="post-';
			the_ID();
			echo '" ';
			post_class('contain');
			echo ' >'."\n";

			thematic_content();

			echo '</article><!-- #post -->'."\n";

		endwhile;

		echo '</section>'."\n";

	// end archive for insidertrip

	// archive for article
	} else if ( is_archive() && get_query_var('post_type') == 'article' ) {

		global $featured;

    	$filter = getLastPathSegment($_SERVER['REQUEST_URI']);

    	// articles landing page
		if ( $filter == 'features' ) {

			remove_all_filters('posts_orderby');

			$args = array('posts_per_page' => 2, 'post_type' => 'article', 'meta_key' => 'secondary-article', 'meta_value' => 'yes', 'orderby' => 'rand');

			$secondary = new WP_Query($args);

			if($secondary->have_posts() ) {

				echo '<section class="all-destinations all-articles featured contain">'."\n";

				while ( $secondary->have_posts() ) : $secondary->the_post();

					echo '<article id="post-';
					the_ID();
					echo '" ';
					post_class('contain');
					echo ' >'."\n";

					thematic_content();

					echo '</article><!-- #post -->'."\n";

				endwhile;

				echo '</section>'."\n";

			}

			wp_reset_postdata();

			$args = array('posts_per_page' => 9, 'post_type' => 'article', 'meta_key' => 'featured-article', 'meta_value' => 'yes', 'orderby' => 'date', 'order' => 'DESC');

			$featured = true;

			$secondary = new WP_Query($args);

			if($secondary->have_posts() ) {

				echo '<section class="all-destinations all-articles contain">'."\n";

				while ( $secondary->have_posts() ) : $secondary->the_post();

					echo '<article id="post-';
					the_ID();
					echo '" ';
					post_class('contain');
					echo ' >'."\n";

					thematic_content();

					echo '</article><!-- #post -->'."\n";

				endwhile;

				echo '</section>'."\n";

			$featured = false;

			}

			wp_reset_postdata();

		// just list view of articles
		}  else if ( $filter !== 'features' ) {
			echo '<section class="all-destinations all-articles contain results">'."\n";

			while ( have_posts() ) : the_post();

				echo '<article id="post-';
				the_ID();
				echo '" ';
				post_class('contain');
				echo ' >'."\n";

				thematic_content();

				echo '</article><!-- #post -->'."\n";

			endwhile;

			if ( $max > 1 ) {
				echo '<p class="load-more"></p>';
			}

			echo '</section>'."\n";
		}

	// end archive for article

	// archive for magazine
	} else if ( is_archive() && get_query_var('post_type') == 'magazine' ) {

		echo '<section class="all-destinations all-articles all-magazines featured contain">'."\n";

		while ( have_posts() ) : the_post();

			echo '<article id="post-';
			the_ID();
			echo '" ';
			post_class('contain');
			echo ' >'."\n";

			thematic_content();

			echo '</article><!-- #post -->'."\n";

		endwhile;

		if ( $max > 1 ) {
			echo '<p class="load-more"></p>';
		}

		echo '</section>'."\n";

	// end archive for magazine

	// archive for itinerary
	} else if (
			( is_archive() && get_query_var('post_type') == 'itinerary' )
		)
	{

		if ( $count > 0 ) {

			if ( current_user_can( 'ind_read_itinerary' ) ) {

				while ( have_posts() ) : the_post();

					thematic_content();

				endwhile;

			} else {

				echo '<p>Members-only content.</p>'."\n";

			}

		} else {

			echo '<p>There is not an itinerary for this destination.</p>'."\n";

		}

	// end archive for itinerary

	// archive for library
	} else if (
			( is_archive() && get_query_var('post_type') == 'library' )
		)
	{

		if ( $count > 0 ) {

			while ( have_posts() ) : the_post();

				thematic_content();

			endwhile;

		} else {

			echo '<p>There is not a library for this destination.</p>'."\n";

		}

	// end archive for library

	// archive for press
	} else if ( is_archive() && get_query_var('post_type') == 'press' ) {

		foreach(posts_by_year('press') as $year => $posts) :

			echo '<section class="all-destinations press contain">'."\n";
			echo '<h2>'.$year.'</h2>'."\n";

			foreach($posts as $post) : setup_postdata($post);

				echo '<article id="post-';
				the_ID();
				echo '" ';
//				post_class('contain');
				echo ' >'."\n";

				thematic_content();

//				echo '</article><!-- #post -->'."\n";
				echo '</article><!-- #post -->';

			endforeach;

			echo '</section>'."\n";

		endforeach;

	// end archive for press

	// archive for career
	} else if ( is_archive() && get_query_var('post_type') == 'career' ) {

		$imageobj = get_field('careers-header-image', 'option');
		$image = $imageobj['sizes']['hero-full'];

		$careersintro = get_field('careers-content', 'option');
		$careersgeneral = get_field('careers-content-general', 'option');

		echo '<div class="header">'."\n";

			echo '<p><img src="'.$image.'" alt="Careers" /></p>'."\n";

			echo $careersintro;

		echo '</div>'."\n";

		echo '<section class="all-destinations career contain">'."\n";

			while ( have_posts() ) : the_post();

					echo '<article id="post-';
					the_ID();
					echo '" ';
					echo ' >'."\n";

					thematic_content();

					echo '</article><!-- #post -->';

			endwhile;

		echo '</section>'."\n";

		echo '<div class="header">'."\n";
			echo '<h2>General Inquiry</h2>'."\n";
		echo '</div>'."\n";

		echo '<section class="all-destinations career contain">'."\n";

			echo '<article>'."\n";
			echo '<p>';
			echo $careersgeneral;
			echo ' <a class="lightbox-inline" href="#lightbox-contact-apply-general">Apply</a></p>'."\n";
			echo '</article><!-- #post -->';

		echo '</section>'."\n";

		echo '<div id="lightbox-contact-apply-general" class="lightbox white-popup contact mfp-hide">'."\n";
			echo '<header>'."\n";
				echo '<h2>Apply Now</h2>'."\n";
				echo '<h3>General Inquiry</h3>'."\n";
			echo '</header>'."\n";

		 echo do_shortcode('[contact-form-7 id="75335" title="Contact Apply Now General"]');

		echo '</div><!-- #lightbox -->'."\n";

		echo '<div class="header">'."\n";
			echo '<h2>Connect with Indagare</h2>'."\n";

			echo '<p class="social">'."\n";
	          echo '<a id="social-facebook" href="https://www.facebook.com/pages/Indagare-Travel/38863077107"><b class="icon custom-icon" data-icon="&#xe003;"><span>facebook</span></b></a> <a href="https://twitter.com/indagaretravel" id="social-twitter"><b class="icon custom-icon" data-icon="&#xe001;"><span>twitter</span></b></a> <a id="social-instagram" href="http://instagram.com/indagaretravel/"><b class="icon custom-icon" data-icon="&#xe618;"><span>instagram</span></b></a> <a id="social-linkedin" href="https://www.linkedin.com/company/indagare"><b class="icon custom-icon" data-icon="&#xe900;"><span>linkedin</span></b></a>'."\n";
    	    echo '</p>'."\n";
		echo '</div>'."\n";

	// end archive for career

	// archive for region
	} else if ( ( is_archive() && $reg && $depth == 1 ) )
	{

		$imageobj = get_field('header-image', 'destinations' . '_' . $reg->term_id);
		$image = $imageobj['sizes']['hero-medium'];

		if ( $image ) {
			echo '<div id="hero-image"><img src="'.$image.'" alt="hero-region" /></div>'."\n";
		}

		// region widget
		echo '<div class="widget-wrapper">'."\n";
		echo '<div id="booking-widget" class="simple">'."\n";
			echo '<ul class="book-type contain">'."\n";
				echo '<li>Book Hotels</li>'."\n";
				echo '<li><a href="#" id="bookflights">Book Flights</a></li>'."\n";
			echo '</ul>'."\n";
			echo '<form id="book-hotels">'."\n";
				echo '<div class="form-combo">'."\n";
					echo '<span class="form-item"><input type="text" id="book-destination" class="element acInput" placeholder="Destination or Hotel" /><b class="icon" data-icon="&#61442;"></b></span>'."\n";
					echo '<div class="autocomplete"></div>'."\n";
				echo '</div>'."\n";
				echo '<div class="form-combo">'."\n";
					echo '<span class="form-item"><input type="text" id="dep_date" class="element dateinput" placeholder="Check In (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
					echo '<!-- <div id="ui-datepicker-div"></div> -->'."\n";
					echo '<span class="form-item"><input type="text" id="ret_date" class="element dateinput" placeholder="Check Out (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
					echo '<!-- <div id="book-ckeck-out-cal"></div> -->'."\n";
				echo '</div>'."\n";
				echo '<div class="buttons">'."\n";
					echo '<button type="submit" class="primary button">Find Rooms</button>'."\n";
				echo '</div>'."\n";
				echo '<div id="last_selected"></div>'."\n";
				echo '<input class="autocompletedestination" type="hidden" />'."\n";
			echo '</form>'."\n";
			echo '<p class="view-all"><a href="/destinations/'.$top->slug.'/'.$reg->slug.'/hotels/">Or view all hotels in '.$reg->name.'</a></p>'."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
		// end region widget

?>

<script>
jQuery().ready(function($) {

	$("#book-destination").autocomplete({
	resultsContainer: '.autocomplete',
	onItemSelect: function(item) {
		$('.autocompletedestination').val(item.data);
	},
	onNoMatch: function() {
		$('#book-destination').val(bookingdestfield);
	},
	data: [
<?php
	$datadestinations = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json');
	$filtersbooking = json_decode($datadestinations);

	foreach($filtersbooking as $row) {
		$name = indg_decode_string( $row[2] );
		$namenoaccent = remove_accents($name);
		echo '["'.$name.'",'.json_encode($row[0]).',"destination"],'."\n";
		if ( $name !== $namenoaccent ) {
			echo '["'.$namenoaccent.'",'.json_encode($row[0]).',"destination"],'."\n";
		}
	}

	$datahotels = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json');
	$filtersbooking = json_decode($datahotels);

	foreach($filtersbooking as $row) {
		$name = indg_decode_string( $row[1] );
		$namenoaccent = remove_accents($name);
		echo '["'.$name.'",'.json_encode($row[2]).',"hotel"],'."\n";
		if ( $name !== $namenoaccent ) {
			echo '["'.$namenoaccent.'",'.json_encode($row[2]).',"hotel"],'."\n";
		}
	}
?>
	]
	});

<?php
		$regionid = $reg->term_id;
?>
	$('#book-destination').val('<?php echo addslashes(html_entity_decode($reg->name)); ?>');
	$('.autocompletedestination').val('<?php echo $regionid; ?>,destination');

	var bookingdestfield = $('input#book-destination').val();

});
</script>

<?php

		echo '<div class="header divider"><h2>All Destination Guides</h2></div>'."\n";
		echo '<section class="all-destinations contain" location="BBBB">'."\n";

//		$destinations = get_terms( 'destinations', array( 'child_of' => $reg->term_id , 'parent' => $reg->term_id, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true) );
		$destinations = get_terms( 'destinations', array( 'child_of' => $reg->term_id , 'parent' => $reg->term_id, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false) );

		if ( $destinations ) {
			foreach ( $destinations as $term ) {
//				$overview = get_field('destination-overview', 'destinations' . '_' . $term->term_id);
				$overview = custom_field_excerpt('destination-overview', 'destinations' . '_' . $term->term_id);
				$imageobj = get_field('header-image', 'destinations' . '_' . $term->term_id);
				$image = $imageobj['sizes']['thumb-large'];

				if ( $overview ) { // display only if destination has custom field content, regardless of whether it has posts associated with it

					echo '<article>'."\n";
						echo '<a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $term->slug.'/">'."\n";
							if ($imageobj) {
								echo '<img src="'.$image.'" alt="Destination" />'."\n";
							}
							echo '<h3>'.$term->name.'</h3>'."\n";
							echo '<p class="description">'.$overview.' <span class="read-more">Read More</span></p>'."\n";
						echo '</a>'."\n";
					echo '</article>'."\n";

				}
			}
		}

		echo '</section>'."\n";

	// end archive for region

	// archive for destination
	} else if ( ( is_archive() && $dest && $depth == 2 ) )
	{

		$cheatsheet = get_field('cheat-sheet-content', 'destinations' . '_' . $dest->term_id);
		$destcontent = get_field('destination-content', 'destinations' . '_' . $dest->term_id);
		$destquote = get_field('destination-quote', 'destinations' . '_' . $dest->term_id);
		$destcitation = get_field('destination-citation', 'destinations' . '_' . $dest->term_id);

		if ( $cheatsheet ) {
			echo '<div class="header"><h2>Cheat Sheet</h2></div>'."\n";
			echo '<div class="cheat-sheet">'."\n";
			echo $cheatsheet;
			echo '</div>'."\n";
		}

		if ( $destcontent ) {
			echo '<article class="divider">'."\n";
			echo '<h2>Lay of the Land</h2>'."\n";
			if ( $destquote ) {
				echo '<div class="pullquote">'."\n";
					echo '<blockquote>&ldquo;'.$destquote.'&rdquo;</blockquote>'."\n";
					if ( $destcitation ) {
						echo '<cite>~'.$destcitation.'</cite>'."\n";
					}
				echo '</div>'."\n";
			}
			echo $destcontent;
			echo '</article>'."\n";
		}

	// end archive for destination
	}

} // end override archive loop


// override author loop | team archive | contributor archive
function childtheme_override_author_loop() {
	global $post;

	$author = get_queried_object();

	$user = get_user_by('id',$author->ID);
	$userid = 'user_'.$user->ID;

	$authortitle = get_field('author-title', $userid);

	echo '<article class="detail">'."\n";

	echo '<h2>'.$user->first_name.' '.$user->last_name.', '.$authortitle.'</h2>'."\n";

	echo wpautop($user->description);

	echo '</article><!-- .detail -->'."\n";
} // end override author loop | team archive | contributor archive

// override search loop
function childtheme_override_search_loop() {
	global $post;
	global $wp_query;

	$rendered_terms = array();
	$search = urldecode( $_GET['s'] );
	$search = sanitize_text_field( $search );

	if ( $_GET['filter'] ) {
		$post_type = urldecode( $_GET['filter'] );
		$post_type = sanitize_key( $post_type );
	}

	if ( $post_type ) {
		$post_type_object = get_post_type_object($post_type);
		$categories = array ( $post_type => $post_type_object->labels->name );
	} else {
		$categories = array(
			'destinations' => 'Destination Guides', // destination taxonomy
			'hotel' => 'Hotels',
			'restaurant' => 'Restaurants',
			'shop' => 'Shops',
			'activity' => 'Activities',
			'article' => 'Articles',
			'itinerary' => 'Itineraries',
			'library' => 'Libraries',
			'offer' => 'Offers',
			'insidertrip' => 'Insider Trips',
		);
	}

	$searchresults = array();
	$loaded_pid = array();

	$homeid = get_option('page_on_front');

	while ( have_posts() ) {
		the_post();
		// remove home from results
		$postid = get_the_ID();
		if ( $homeid !== $postid ) {
			$termid = false;
			if($postid == -1) {
				$postid = 'term:'.$post->term_id;
				$termid = true;
			}
			if ( !in_array( $postid, $loaded_pid ) ) {
				$loaded_pid[] = $postid;
				if($termid) {
					$searchresults[$post->post_type][] = $post;
				} else {
					$sort = $post->post_title . ' '. $post->ID;
					if($post->post_type == 'article') {
						$sort = $post->post_date . ' '. $post->ID;
					}
					$searchresults[$post->post_type][$sort] = $post;
				}
			}
		}
	}

	foreach ($categories as $group => $t) {

		/* We'll also perform a check to see if there are results for a  post type. If there isn't, we'll omit it from the results */

		if (!array_key_exists($group, $searchresults))
			continue;

		if ( $group !== 'destinations' ) {
			echo '<section class="related-articles contain results">'."\n";
		}

		echo '<div class="header divider">';

		if ( $group == 'destinations' ) {
			echo '<h2>Destination Guides</h2>'."\n";
		} else {
			echo '<h2>'.$t.'</h2>'."\n";
		}

		if ( $group !== 'destinations' ) {
			if ( empty( $_GET['filter'] ) ) {
				if ( count( $searchresults[$group] ) > INDG_SEARCHPAGE_SECTIONCOUNT ) {
					echo '<p class="view-more"><a href="/?s='.urlencode( $search ).'&filter='.$group.'">View All Results</a></p>'."\n";
					if($group == 'article') {
						ksort($searchresults[$group]);
						$newarray = array_slice($searchresults[$group],-8,8,true);
						rsort($newarray);
						$searchresults[$group] = $newarray;
					} else {
						$keys = array_rand($searchresults[$group],INDG_SEARCHPAGE_SECTIONCOUNT);
						$newarray = array();
						foreach($keys as $k) {
							$newarray[$k] = $searchresults[$group][$k];
						}
						$searchresults[$group] = $newarray;
					}
				}
			} else if ($group == 'article') {
				ksort($searchresults[$group]);
				rsort($searchresults[$group]);
			}
			ksort($searchresults[$group]);
		}

		echo '</div>'."\n";

		if ( $group == 'destinations' ) {
			echo '<section class="all-destinations contain" location="AAAA">'."\n";
		}

		/* Ok, we now need to spit out any post data we want to display in our results */

		$destlist = array();
		$destcount = 0;
		$destskip = 0;

		foreach ($searchresults[$group] as $post) {
			if ( $group == 'destinations' && !$_GET['filter'] ) {

				$destination = get_term_by( 'name', $post->post_title, 'destinations' );
				$destinationid = $destination->term_id;
				$destinationstree = destinationstaxtree($destination->term_id);
				$dest = $destinationstree['dest'];
				$reg = $destinationstree['reg'];
				$top = $destinationstree['top'];
				$depth = $destinationstree['depth'];

				if ( $depth!==3 && $destcount > 0 && !in_array($destinationid,$destlist) ) {
					$destlist[] = $destinationid;
					$destskip = 0;
				} else if ( $depth!==3 && $destcount == 0 ) {
					$destlist[] = $destinationid;
					$destskip = 0;
				} else {
					$destskip = 1;
				}

				$destcount++;

//				echo $depth;

				if ( $depth == 1 ) {
					$destinations = get_terms( 'destinations', array( 'child_of' => $reg->term_id , 'parent' => $reg->term_id, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false) );
				} else if ( $depth == 2 ) {
					$destinations = get_terms( 'destinations', array( 'include' => array($destination->term_id), 'hide_empty' => false) );
				}

//						if ( $destinations && $depth !== 3 && $destskip == 0 ) {
				if ( $destinations && $destskip == 0 ) {
					foreach ( $destinations as $term ) {
						if(!in_array($term->term_id, $rendered_terms)) {
							$rendered_terms[] = $term->term_id;
							print render_destination_term($top, $reg, $term);
						}
					}
				}
			} else {
				echo '<article id="post-'.get_the_ID().'" ';
				post_class('contain');
				echo '>';
				thematic_postheader();
				echo '<div class="entry-content">';
				thematic_content();
				echo '</div><!-- .entry-content -->';
				thematic_postfooter();
				echo '</article><!-- #post -->';
			}
		}
		echo '</section>'."\n";

	}

} // end override search loop

// page title
function child_page_title($content) {
global $post;
	if ( is_archive() ) {
		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
		$depth = $destinationstree['depth'];
	}

	// nothing for destination report archives | article archives | offer archives | insidertrip archives | magazine archive | press archive | career archive | search
	if (
			( is_archive() && $reg && $depth == 1 )
			|| ( is_archive() && $dest && $depth == 2 && get_query_var('post_type') !== 'itinerary' )
			|| ( is_archive() && get_query_var('post_type') == 'hotel' )
			|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
			|| ( is_archive() && get_query_var('post_type') == 'shop' )
			|| ( is_archive() && get_query_var('post_type') == 'activity' )
//			|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
			|| ( is_archive() && get_query_var('post_type') == 'library' )
			|| ( is_archive() && get_query_var('post_type') == 'article' )
			|| ( is_archive() && get_query_var('post_type') == 'offer' )
			|| ( is_archive() && get_query_var('post_type') == 'insidertrip' )
			|| ( is_archive() && get_query_var('post_type') == 'magazine' )
			|| ( is_archive() && get_query_var('post_type') == 'press' )
			|| ( is_archive() && get_query_var('post_type') == 'career' )
			|| is_search()
			|| is_author()
		)
	 {

		$content = '';

	} else if ( is_posttype( 'itinerary', POSTTYPE_ARCHIVEONLY ) ) {
		// title only for logged in user
		if ( current_user_can( 'ind_read_itinerary' ) ) {
			$content = '';

			while ( have_posts() ) {
				the_post();
				// !!ALERT!! This will only display the last one.  Is this what we want??
				$content = '<h2>'.get_the_title().'</h2>'."\n";
				$content .= '<p>'.get_the_author_meta( 'display_name', $post->post_author ).' | '.get_the_time( get_option('date_format') ).'</p>'."\n";
			}
		} else {
			$content = '<h2>Members-Only Content</h2>'."\n";
		}
	}

	return $content;
}
add_filter('thematic_page_title','child_page_title');

// remove thematic post title
function childtheme_override_postheader() {
global $post;

	// hotel post | restaurant post | shop post | activity post | article post | offer post | insidertrip post
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' )
		|| is_singular( 'activity' ) || is_singular('article') || is_singular('offer') || is_singular('insidertrip')
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'offer' )
		|| ( is_archive() && get_query_var('post_type') == 'insidertrip' )
	) {

	// search page
	} else if ( is_search() ) {

	// home page
	} else if ( is_home() || is_front_page() ) {

	// about pages
	} else if ( is_page() && ( get_field('about') == 'yes') ) {

	// join pages
	} else if ( is_page() && ( (get_field('membership') == 'yes') || is_page_template ( 'template-page-welcome.php' ) ) ) {

	// new page
	} else if ( is_page_template ( 'template-page-new.php' ) ) {

	// password reset page
	} else if ( is_page_template ( 'template-page-password-reset.php' ) ) {

	// external login page
	} else if ( is_page_template ( 'template-page-external-login.php' ) ) {

	// wish list
	} else if ( is_page_template ( 'template-page-account-wish-list.php' ) ) {

	// my account page
	} else if ( is_page_template ( 'template-page-account-edit.php' ) ) {

	// map page
	} else if ( is_page_template ( 'template-page-map.php' ) ) {

	// book page
	} else if ( is_page_template ( 'template-page-book.php' ) ) {

	// intro page
	} else if (is_page_template ( 'template-page-intro.php' ) ) {

	} else {

 	   if ( is_404() || $post->post_type == 'page' || is_singular('magazine') ) {
 	       $postheader = thematic_postheader_posttitle();
 	   } else {
 	       $postheader = thematic_postheader_posttitle() . thematic_postheader_postmeta();
 	   }

 	   echo apply_filters( 'thematic_postheader', $postheader ); // Filter to override default post header

	}

}

// post content format
function child_singlepost($content) {
	global $post;
	global $wp_query;

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	// start child_singlepost conditional
	$basecontent = $content;

	// home page
	if ( is_home() || is_front_page() ) {

		$content = '';

		$content .= '<section class="all-destinations all-articles contain">'."\n";
			$content .= '<div class="header divider intro">'."\n";
				$content .= '<h2 class="center">'.get_field('home-intro-title').'</h2>'."\n";
				$content .= get_field('home-intro-content');
			$content .= '</div>'."\n";
		$content .= '</section>'."\n";

		$rows = get_field('home-featured');

		if($rows) {

			$i = 0;

			shuffle($rows);

			$content .= '<section class="all-destinations all-articles contain">'."\n";

			$content .= '<div class="widget-wrapper home">'."\n";
				$content .= '<div id="booking-widget" class="simple">'."\n";
					$content .= '<ul class="book-type contain">'."\n";
						$content .= '<li>Book Hotels</li>'."\n";
						$content .= '<li><a href="#" id="bookflights">Book Flights</a></li>'."\n";
					$content .= '</ul>'."\n";
					$content .= '<form id="book-hotels">'."\n";
						$content .= '<div class="form-combo">'."\n";
							$content .= '<span class="form-item"><input type="text" id="book-destination" class="element acInput" placeholder="Destination or Hotel" /><b class="icon" data-icon="&#61442;"></b></span>'."\n";
							$content .= '<div class="autocomplete"></div>'."\n";
						$content .= '</div>'."\n";
						$content .= '<div class="form-combo">'."\n";
							$content .= '<span class="form-item"><input type="text" id="dep_date" class="element dateinput" placeholder="Check In (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
							$content .= '<span class="form-item"><input type="text" id="ret_date" class="element dateinput" placeholder="Check Out (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
						$content .= '</div>'."\n";
						$content .= '<div class="buttons">'."\n";
							$content .= '<button type="submit" class="primary button">Find Rooms</button>'."\n";
						$content .= '</div>'."\n";
						$content .= '<div id="last_selected"></div>'."\n";
						$content .= '<input class="autocompletedestination" type="hidden" />'."\n";
					$content .= '</form>'."\n";
				$content .= '</div><!-- #booking-widget -->'."\n";
			$content .= '</div><!-- .widget-wrapper -->'."\n";

			$content .= '<script>'."\n";
			$content .= 'jQuery().ready(function($) {'."\n";

				$content .= '$("#book-destination").autocomplete({'."\n";
				$content .= 'resultsContainer: \'.autocomplete\','."\n";
				$content .= 'onItemSelect: function(item) {'."\n";
					$content .= '$(\'.autocompletedestination\').val(item.data);'."\n";
				$content .= '},'."\n";
				$content .= 'onNoMatch: function() {'."\n";
					$content .= '$(\'#book-destination\').val(bookingdestfield);'."\n";
				$content .= '}, '."\n";
				$content .= 'data: ['."\n";

				$datadestinations = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json');
				$filtersbooking = json_decode($datadestinations);

				foreach($filtersbooking as $row) {
					$name = indg_decode_string( $row[2] );
					$namenoaccent = remove_accents($name);
					$content .= '["'.$name.'",'.json_encode($row[0]).',"destination"],'."\n";
					if ( $name !== $namenoaccent ) {
						$content .= '["'.$namenoaccent.'",'.json_encode($row[0]).',"destination"],'."\n";
					}
				}

				$datahotels = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json');
				$filtersbooking = json_decode($datahotels);

				foreach($filtersbooking as $row) {
					$name = indg_decode_string( $row[1] );
					$namenoaccent = remove_accents($name);
					$content .= '["'.$name.'",'.json_encode($row[2]).',"hotel"],'."\n";
					if ( $name !== $namenoaccent ) {
						$content .= '["'.$namenoaccent.'",'.json_encode($row[2]).',"hotel"],'."\n";
					}
				}

				$content .= ']'."\n";
				$content .= '});'."\n";

				$content .= 'var bookingdestfield = $(\'input#book-destination\').val();'."\n";

			$content .= '});'."\n";
			$content .= '</script>'."\n";

			foreach($rows as $row) {

				if ( $i < 5 ) {

						$imageobj = $row['home-featured-image'];
						$image = $imageobj['sizes']['thumb-large'];

						$content .= '<article>'."\n";
							$content .= '<a href="'.$row['home-featured-url'].'">'."\n";
								if ( $image ) {
									$content .= '<img src="'.$image.'" alt="Related" />'."\n";
								}
								$content .= '<span class="info">'."\n";
									$content .= '<h4>'.$row['home-featured-heading'].'</h4>'."\n";
									$content .= '<h3>'.$row['home-featured-title'].'</h3>'."\n";
								$content .= '</span><!-- .info -->'."\n";
							$content .= '</a>'."\n";
						$content .= '</article>'."\n";

					$i++;
				}
			}

			$content .= '</section><!-- .all-destinations -->'."\n";
		}

		$rows = get_field('home-secondary');

		if($rows) {
			$content .= '<section class="related-articles contain">'."\n";

			foreach($rows as $row) {

				$imageobj = $row['home-secondary-image'];
				$image = $imageobj['sizes']['thumb-medium'];

				$content .= '<article>'."\n";
					$content .= '<a href="'.$row['home-secondary-url'].'">'."\n";
						if ( $image ) {
							$content .= '<img src="'.$image.'" alt="Related" />'."\n";
						}
						$content .= '<h4>'.$row['home-secondary-heading'].'</h4>'."\n";
						$content .= '<h3>'.$row['home-secondary-title'].'</h3>'."\n";
						$content .= '<p class="description">'.$row['home-secondary-content'].'</p>'."\n";
					$content .= '</a>'."\n";
				$content .= '</article>'."\n";
			}

			$content .= '</section><!-- .related-articles -->'."\n";
		}

	// end home page

	// hotel post | restaurant post | shop post | activity post
	}  else if ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) ) {

		$content = '';

		$destinationstree = destinationstree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];

		// parse filters to use for permalinks
		parse_str($_SERVER['QUERY_STRING'], $urlvars);
		$urlvars = http_build_query($urlvars);

		if ( is_singular( 'hotel' ) && $urlvars ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/hotels/?'.$urlvars.'"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Hotel Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'hotel' ) ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/hotels/"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Hotel Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'restaurant' ) && $urlvars ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/restaurants/?'.$urlvars.'"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Restaurant Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'restaurant' ) ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/restaurants/"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Restaurant Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'shop' ) && $urlvars ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/shops/?'.$urlvars.'"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Shop Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'shop' ) ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/shops/"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Shop Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'activity' ) && $urlvars ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/activities/?'.$urlvars.'"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Activity Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		} else if ( is_singular( 'activity' ) ) {
			$content .= '<p class="nav"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/activities/"><b class="icon petite custom-icon" data-icon="&#xf0d9;"></b> Back to Activity Listings for '.$dest->name.', '.$reg->name.'</a></p>'."\n";
		}

		$content .= '<article class="detail">'."\n";
		$content .= '<div class="vcard">'."\n";
			$content .= '<div class="heading">'."\n";
				$content .= '<h2 class="org">'.get_the_title().'</h2>'."\n";
				$content .= '<p class="ind-meta">';
				if (pa_in_taxonomy('benefit', 'special-offer')) {
					$content .= '<b class="icon petite custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b> ';
				}
				if (pa_in_taxonomy('benefit', 'indagare-plus')) {
					$content .= '<b class="icon petite custom-icon" data-icon="&#xe009;" id="ind-plus"><span>Plus</span></b> ';
				}
				if (pa_in_taxonomy('hoteltype', 'indagare-picks')) {
					$content .= '<b class="icon petite custom-icon" data-icon="&#xe00a;" id="ind-picks"><span>Plus</span></b> ';
				}
				if (pa_in_taxonomy('hoteltype', 'indagare-adored')) {
					$content .= '<b class="icon petite custom-icon" data-icon="&#xe00b;" id="ind-adored"><span>Adored</span></b>';
				}
				$content .= '</p>'."\n";
			$content .= '</div>'."\n";
			// hotel subtitle
			if ( get_field('subtitle') ) {
				$content .= '<p class="tagline">'.get_field('subtitle').'</p>'."\n";
			}
			$content .= '<p>'."\n";
				$content .= '<span class="adr"><span class="street-address">'.get_field('address-display').'</span></span>'."\n";
				if ( get_field('address-display-2') ) {
					$content .= '<span class="adr"><span class="street-address">'.get_field('address-display-2').'</span></span>'."\n";
				}
				if ( get_field('phone') ) {
					$content .= '<span class="pre"><span class="tel">'.get_field('phone').'</span></span>'."\n";
				}
				if ( get_field('phone-alternate') ) {
					$content .= '<span class="pre"><span class="tel">'.get_field('phone-alternate').'</span></span>'."\n";
				}
				if ( get_field('url') ) {
//					$content .= '<a class="url n" href="'.get_field('url').'">'.get_field('url-display').'</a>'."\n";
					$content .= '<a target="_blank" class="url n" href="http://'.get_field('url').'">'.get_field('url-display').'</a>'."\n";
				}

			$content .= '</p>'."\n";
		$content .= '</div>'."\n";
		$content .= '<p class="view-more"><a class="map" href="#"><b class="icon petite custom-icon" data-icon="&#xe000;"></b>Show Map</a>';

				$content .= '<span id="selectors">'."\n";
				$content .= '<span class="selectorstitle">What\'s Nearby:</span> '."\n";
//					$content .= '<div id="toggle-layers" style="position: relative;">'."\n";
						$content .= '<span id="Hotel" class="togglelayer">'."\n";
							$content .= 'Hotels'."\n";
							$content .= '<input id="HotelCheckbox" class="poicategory" style="display: none;" type="checkbox" value="Hotel" class="checkbox" />'."\n";
						$content .= '</span>'."\n";
						$content .= '<span id="Restaurant" class="togglelayer">'."\n";
							$content .= 'Restaurants'."\n";
							$content .= '<input id="RestaurantCheckbox" class="poicategory" style="display: none;" type="checkbox" value="Restaurant" class="checkbox" />'."\n";
						$content .= '</span>'."\n";
						$content .= '<span id="Shop" class="togglelayer">'."\n";
							$content .= 'Shops'."\n";
							$content .= '<input id="ShopCheckbox" class="poicategory" style="display: none;" type="checkbox" value="Shop" class="checkbox" />'."\n";
						$content .= '</span>'."\n";
						$content .= '<span id="Activity" class="togglelayer">'."\n";
							$content .= 'Sights'."\n";
							$content .= '<input id="ActivityCheckbox" class="poicategory" style="display: none;" type="checkbox" value="Activity" class="checkbox" />'."\n";
						$content .= '</span>'."\n";
						//					$content .= '</div>'."\n";
				$content .= '</span>'."\n";

		$content .= '</p>'."\n";

		$content .= map_canvas(true);
		$content .= map();

//		$rows = get_field('gallery-header');

		$rowsraw = get_field('gallery-header', false, false);

		if($rowsraw) {

			$content .= '<div id="gallery-header" class="photo-gallery hero heronopadding heronoborder">'."\n";
				$content .= '<div id="rslideswrapper">'."\n";

				$content .= '<ul class="hero rslides">'."\n";

				foreach($rowsraw as $imageid) {

					$imageobj = wp_get_attachment_image_src( $imageid, 'hero-review' );
					$imgsrc = $imageobj[0];
					$caption = get_post($imageid)->post_excerpt;

					$image = $imageobj[sizes]['hero-review'];

					$content .= '<li>'."\n";
						$content .= '<img class="rsImg" alt="'.$caption.'" src="'.$imgsrc.'">'."\n";
						if ( $caption) {
							$content .= '<div class="caption">'.$caption.'</div>'."\n";
//							$content .= '<p class="summary">'.$caption.'</p>'."\n";
						}
					$content .= '</li>'."\n";


				}

				$content .= '</ul><!--.hero.rslides-->'."\n";

				$content .= '</div>'."\n";
			$content .= '</div>'."\n";


		}

		// article meta for favorites and social links
		$content .= article_meta($post->ID);

		$content .= $basecontent;

		$content .= '<p class="author">&ndash; '.get_the_author_meta( 'display_name', $post->post_author ).'</p>'."\n";

		$content .= '</article>'."\n";

		// benefits
		$rows = get_field('benefit');

		if ($rows) {

			$content .= '<section class="benefits contain">'."\n";

			foreach ( $rows as $benefit ) {

				$content .= '<article>'."\n";
				$content .= '<h3>'.$benefit['benefit-title'].'</h3>'."\n";
				$content .= $benefit['benefit-content'];
				$content .= '</article>'."\n";

			}

			$content .= '</section>'."\n";

		}

		// related hotels
		$rows = get_field('related-hotels');

		if ($rows) {

			$content .= '<div class="header divider"><h2>Also Recommended</h2></div>'."\n";

			$content .= '<section class="related-articles contain">'."\n";

				foreach ( $rows as $hotel ) {

					$content .= '<article>'."\n";
						$content .= '<a href="'. get_permalink($hotel) .'">'."\n";

//							$images = get_field('gallery-header',$hotel);

							$imagesraw = get_field('gallery-header',$hotel,false);

							if ( $imagesraw ) {
								$imageid = $imagesraw[0];
								$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-medium' );
								$imgsrc = $imageobj[0];
							}

							$content .= '<img src="'.$imgsrc.'" alt="Related" />'."\n";

							$content .= '<h3>'.get_the_title($hotel).'</h3>'."\n";
						$content .= '</a>'."\n";
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

	// archives for hotel | restaurant | shop | activity
	} else if (

		( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )

	) {

		// parse filters to use for permalinks
		parse_str($_SERVER['QUERY_STRING'], $urlvars);
		$urlvars = http_build_query($urlvars);

		// generate thumbnail from gallery header, if not, use featured image
//		$rows = get_field('gallery-header');

		$rowsraw = get_field('gallery-header', false, false);

		if ( $rowsraw ) {
			$imageid = $rowsraw[0];
			$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-medium' );
			$imgsrc = $imageobj[0];
		} else {
			$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb-medium' );
			$imgsrc = $imageobj[0];
		}

		$content = '';

//		$content .= '<article class="contain">'."\n";
			if ( $urlvars ) {
				$content .= '<a href="'.get_permalink().'?'.$urlvars.'">'."\n";
			} else {
				$content .= '<a href="'.get_permalink().'">'."\n";
			}
				if ( $imgsrc ) {
					$content .= '<div class="photo"><img src="'.$imgsrc.'" alt="Related" /></div>'."\n";
				}
				$content .= '<div class="matter">'."\n";
					$content .= '<div class="heading">'."\n";
						$content .= '<h2>'.get_the_title().'</h2>'."\n";
						$content .= '<p class="ind-meta">';
						if (pa_in_taxonomy('benefit', 'special-offer')) {
							$content .= '<b class="icon petite custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b> ';
						}
						if (pa_in_taxonomy('benefit', 'indagare-plus')) {
							$content .= '<b class="icon petite custom-icon" data-icon="&#xe009;" id="ind-plus"><span>Plus</span></b> ';
						}
						if (pa_in_taxonomy('hoteltype', 'indagare-picks')) {
							$content .= '<b class="icon petite custom-icon" data-icon="&#xe00a;" id="ind-picks"><span>Picks</span></b> ';
						}
						if (pa_in_taxonomy('hoteltype', 'indagare-adored')) {
							$content .= '<b class="icon petite custom-icon" data-icon="&#xe00b;" id="ind-adored"><span>Adored</span></b>';
						}
						$content .= '</p>'."\n";
					$content .= '</div>'."\n";
					if ( get_field('subtitle') ) {
						$content .= '<p class="tagline">'.get_field('subtitle').'</p>'."\n";
					}

					$text = wpautop( get_the_content() );
					$text = substr( $text, 0, strpos( $text, '</p>' ) + 4 );
					$text = substr( $text, strpos( $text, '<p>' ), strlen($text) -3 );
					$text = strip_tags($text, '<a><strong><em><b><i>');
					$text = str_replace(']]>', ']]>', $text);
					$excerpt_length = 20; // 20 words
//					$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
					$excerpt_more = apply_filters('excerpt_more', '...');
					$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

					if ( get_query_var('post_type') == 'hotel' ) {
						$content .= '<p class="description">'.$text.' <span class="read-more">Review and Rates</span></p>'."\n";
					} else {
						$content .= '<p class="description">'.$text.' <span class="read-more">Read more</span></p>'."\n";
					}

				$content .= '</div>'."\n";
			$content .= '</a>'."\n";
//		$content .= '</article>'."\n";

	// end archives for hotel | restaurant | shop | activity

	// offer post
	}  else if ( is_singular( 'offer' ) ) {

		$content = '';

		$content .= '<div class="header">'."\n";
			$content .= '<b class="icon custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b> <h1>Indagare Partner Promotions<span class="return"><a href="/destinations/offers/"><b class="icon petite" data-icon="&#xf0d9;"></b> Back to Partner Promotions</a></span></h1>'."\n";
		$content .= '</div>'."\n";
		$content .= '<article class="detail">'."\n";
			$content .= '<div class="vcard">'."\n";
				$content .= '<div class="heading">'."\n";
					$content .= '<h2 class="org">'.get_the_title().'</h2>'."\n";
					$content .= '<p class="ind-meta">'."\n";
						$content .= '<b class="icon petite custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b>'."\n";
					$content .= '</p>'."\n";
				$content .= '</div>'."\n";
				$content .= '<p class="tagline">'.get_field('subtitle').'</p>'."\n";

			$content .= '</div>  '."\n";

//			$rows = get_field('gallery-header');

			$rowsraw = get_field('gallery-header', false, false);

			if($rowsraw) {

				$content .= '<div id="gallery-header" class="photo-gallery hero heronopadding heronoborder">'."\n";
					$content .= '<div id="rslideswrapper">'."\n";

					$content .= '<ul class="hero rslides">'."\n";

					foreach($rowsraw as $imageid) {

						$imageobj = wp_get_attachment_image_src( $imageid, 'hero-review' );
						$imgsrc = $imageobj[0];
						$caption = get_post($imageid)->post_excerpt;

						$image = $imageobj[sizes]['hero-review'];

						$content .= '<li>'."\n";
							$content .= '<img class="rsImg" alt="'.$caption.'" src="'.$imgsrc.'">'."\n";
							if ( $caption) {
								$content .= '<div class="caption">'.$caption.'</div>'."\n";
	//							$content .= '<p class="summary">'.$caption.'</p>'."\n";
							}
						$content .= '</li>'."\n";


					}

					$content .= '</ul><!--.hero.rslides-->'."\n";

					$content .= '</div>'."\n";
				$content .= '</div>'."\n";

			}

			// article meta for favorites and social links
			$content .= article_meta($post->ID);

			$content .= $basecontent;

		$content .= '</article>'."\n";

	// end offer post

	// archives offer
	} else if ( is_archive() && get_query_var('post_type') == 'offer' ) {

		// generate thumbnail from gallery header, if not, use featured image
//		$rows = get_field('gallery-header');

		$rowsraw = get_field('gallery-header', false, false);

		if ( $rowsraw ) {
			$imageid = $rowsraw[0];
			$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-large' );
			$imgsrc = $imageobj[0];
		} else {
			$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb-large' );
			$imgsrc = $imageobj[0];
		}

		$content = '';

		$content .= '<a href="'.get_permalink().'">'."\n";
			if ( $imgsrc ) {
				$content .= '<img src="'.$imgsrc.'" alt="Destination" />'."\n";
			}
			$content .= '<h3>'.get_the_title().'</h3><b class="icon petite custom-icon" data-icon="&#xe600;" id="ind-offers"><span>Offers</span></b>'."\n";
		$content .= '</a>'."\n";
			$content .= '<span class="location">'.get_field('subtitle').'</span>'."\n";

			$text = wpautop( get_the_content() );
			$text = substr( $text, 0, strpos( $text, '</p>' ) + 4 );
			$text = substr( $text, strpos( $text, '<p>' ), strlen($text) -3 );
			$text = strip_tags($text, '<a><strong><em><b><i>');
			$text = str_replace(']]>', ']]>', $text);
			$excerpt_length = 20; // 20 words
			$excerpt_more = apply_filters('excerpt_more', '...');
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

			$content .= '<p class="description">'.$text.'</p>'."\n";
			$content .= '<p class="description">'."\n";
				$content .= '<a class="book" href="'.get_permalink().'">Details</a>'."\n";
			$content .= '</p>'."\n";

	// end archives offer

	// insidertrip post
	}  else if ( is_singular( 'insidertrip' ) ) {

		$content = '';

		$content .= '<div class="header">'."\n";
			$content .= '<h1>Insider Trips<span class="return"><a href="/destinations/insidertrips/"><b class="icon petite" data-icon="&#xf0d9;"></b> Back to Insider Trips</a></span></h1>'."\n";
		$content .= '</div>'."\n";
		$content .= '<article class="detail">'."\n";
			$content .= '<div class="vcard">'."\n";
				$content .= '<div class="heading">'."\n";
					$content .= '<h2 class="org">'.get_the_title().'</h2>'."\n";
				$content .= '</div>'."\n";
				$content .= '<p class="tagline">'.get_field('subtitle').'</p>'."\n";
				$content .= '<a class="lightbox-inline book" href="#lightbox-contact-insidertrip" class="book">Inquire Now</a>'."\n";
			$content .= '</div>  '."\n";

//			$rows = get_field('gallery-header');

			$rowsraw = get_field('gallery-header', false, false);

			if($rowsraw) {

				$content .= '<div id="gallery-header" class="photo-gallery hero heronopadding heronoborder">'."\n";
					$content .= '<div id="rslideswrapper">'."\n";

					$content .= '<ul class="hero rslides">'."\n";

					foreach($rowsraw as $imageid) {

						$imageobj = wp_get_attachment_image_src( $imageid, 'hero-review' );
						$imgsrc = $imageobj[0];
						$caption = get_post($imageid)->post_excerpt;

						$image = $imageobj[sizes]['hero-review'];

						$content .= '<li>'."\n";
							$content .= '<img class="rsImg" alt="'.$caption.'" src="'.$imgsrc.'">'."\n";
							if ( $caption) {
								$content .= '<div class="caption">'.$caption.'</div>'."\n";
	//							$content .= '<p class="summary">'.$caption.'</p>'."\n";
							}
						$content .= '</li>'."\n";


					}

					$content .= '</ul><!--.hero.rslides-->'."\n";

					$content .= '</div>'."\n";
				$content .= '</div>'."\n";

			}

			// article meta for favorites and social links
			$content .= article_meta($post->ID);

			$content .= $basecontent;

		$content .= '</article>'."\n";

	// end insidertrip post

	// archives insidertrip
	} else if ( is_archive() && get_query_var('post_type') == 'insidertrip' ) {

		// generate thumbnail from gallery header, if not, use featured image
//		$rows = get_field('gallery-header');

		$rowsraw = get_field('gallery-header', false, false);

		if ( $rowsraw ) {
			$imageid = $rowsraw[0];
			$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-large' );
			$imgsrc = $imageobj[0];
		} else {
			$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb-large' );
			$imgsrc = $imageobj[0];
		}

		$content = '';

			if ( $imgsrc ) {
				$content .= '<a href="'.get_permalink().'">'."\n";
				$content .= '<img src="'.$imgsrc.'" alt="Destination" />'."\n";
				$content .= '</a>'."\n";
			}
			$content .= '<span class="info">'."\n";
				$content .= '<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>'."\n";
				$content .= '<span class="date">'.get_field('subtitle').'</span>'."\n";

				$text = wpautop( get_the_content() );
				$text = substr( $text, 0, strpos( $text, '</p>' ) + 4 );
				$text = substr( $text, strpos( $text, '<p>' ), strlen($text) -3 );
				$text = strip_tags($text, '<a><strong><em><b><i>');
				$text = str_replace(']]>', ']]>', $text);
				$excerpt_length = 20; // 20 words
				$excerpt_more = apply_filters('excerpt_more', '...');
				$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

				$content .= '<p>'.$text.'</p>'."\n";
				$content .= '<a href="'.get_permalink().'">Read More</a>'."\n";
			$content .= '</span><!-- .info -->'."\n";

	// end archives insidertrip

	// article post
	} else if ( is_singular( 'article' ) ) {

		$content = '';

		$content .= '<article class="magazine detail">'."\n";

		$content .= $basecontent;

		$content .= '<p class="author">&ndash; '.get_the_author_meta( 'display_name', $post->post_author ).' on '.get_the_time( get_option('date_format') ).'</p>'."\n";

		// article meta for favorites and social links
		$content .= article_meta($post->ID);

		$content .= '</article>'."\n";

	// end article post

	// archives for article
	} else if (
		( is_archive() && get_query_var('post_type') == 'article' )
	) {

		global $featured;

    	$filter = getLastPathSegment($_SERVER['REQUEST_URI']);

//		$rows = get_field('gallery-header');

		$imgsize = 'thumb-large';
		// larger image for secondary article
		if ( $filter == 'features' && !$featured ) {
			$imgsize = 'hero-review';
		}

		$imgsrc = _get_firstimage( 'gallery-header', $imgsize, SHR_FIRSTIMAGE_ALL, false, $value );
		$imgsrc = $imgsrc['src'];
		if ( $imgsize != 'hero-review' ) {
			$imgsrc = str_replace( '620x413', '300x200', $imgsrc );
		}
		if ( empty( $imgsrc ) ) {
			$imgsrc = get_bloginfo('stylesheet_directory').'/images/blank-thumb-large.png';
		}
/*
		$rowsraw = get_field('gallery-header', false, false);

		if ( $rowsraw ) {
			$imageid = $rowsraw[0];
			// larger image for secondary article
			if ( $filter == 'features' && !$featured) {
				$imageobj = wp_get_attachment_image_src( $imageid, 'hero-review' );
			// smaller image for regular loop
			} else {
				$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-large' );
			}
			$imgsrc = $imageobj[0];
		} else if ( catch_that_image($post->ID) ) {
				$imgsrc = catch_that_image($post->ID);
				$imgsrc = str_replace('620x413', '300x200', $imgsrc);
		} else {
			// larger image for secondary article
			if ( $filter == 'features' && !$featured) {
				$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-review' );
			// smaller image for regular loop
			} else {
				$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb-large' );
			}
			$imgsrc = $imageobj[0];
		}
*/
		$column = wp_get_post_terms( $post->ID, 'column' );

		$content = '';

		$content .='<a href="'.get_permalink().'">'."\n";
			$content .='<img src="'.$imgsrc.'" alt="Article" />'."\n";
			$content .='<span class="info">'."\n";
				$content .='<h4>'.$column[0]->name.'</h4>'."\n";
				$content .='<h3>'.get_the_title().'</h3>'."\n";
			$content .='</span><!-- .info -->'."\n";
		$content .='</a>'."\n";

	// end archives for article

	// magazine post
	} else if ( is_singular( 'magazine' ) ) {

		$subtitle = get_field('subtitle');
		$issuu = get_field('magazine-issuu');
		$pdfobj = get_field('magazine-pdf');

		$content = '';

		$content .= '<article class="magazine detail">'."\n";

		$content .= '<iframe id="issuu" src="https://issuu.com/indagare/docs/'.$issuu.'?mode=window&amp;printButtonEnabled=false&amp;shareButtonEnabled=false&amp;searchButtonEnabled=false&amp;backgroundColor=%23ffffff"></iframe>'."\n";

		$content .= '<h4>'.$subtitle.'</h4>'."\n";
		$content .= $basecontent;

		$content .= '<p><a class="button secondary" target="_blank" href="'.$pdfobj['url'].'">View PDF</a></p>'."\n";

		$content .= '</article>'."\n";

	// end magazine post

	// archives for magazine
	} else if (
		( is_archive() && get_query_var('post_type') == 'magazine' )
	) {

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'hero-review' );
		$imgsrc = $imageobj[0];
		$subtitle = get_field('subtitle');
		$pdfobj = get_field('magazine-pdf');

		$current = ( $paged == 1 && $wp_query->current_post == 0 );
		$allowed = ( current_user_can( 'ind_read_magazine_archive' ) ||
					( $current /* && current_user_can( 'ind_read_magazine' ) */ ) );

		$content = '';

		$content .= '<div class="magazine">'."\n";

		if ( $allowed ) {
			$content .=' <a href="'.get_permalink().'">'."\n";
		}

		if ( $imgsrc ) {
			$content .='<img src="'.$imgsrc.'" alt="Magazine" />'."\n";
		} else {
			$content .= '<img src="'.get_bloginfo('stylesheet_directory').'/images/blank-thumb-large.png" alt="Article" />'."\n";
		}

		if ( $allowed ) {
			$content .= '</a>'."\n";

			$content .= '<a href="'.get_permalink().'">'."\n";
			$content .= '<div class="rollover">'."\n";
			$content .= '<h4 class="more">View this issue</h4>'."\n";
			$content .= '</div><!-- .rollover -->'."\n";
			$content .= '</a>'."\n";
		} else if ( ! ind_logged_in() ) {
			$content .= '<a href="/join/">'."\n";
			$content .= '<div class="rollover">'."\n";
			$content .= '<h4 class="more">Join today to see this issue</h4>'."\n";
			$content .= '</div><!-- .rollover -->'."\n";
			$content .= '</a>'."\n";
		} else {
			$content .= '<a href="/account/">'."\n";
			$content .= '<div class="rollover">'."\n";
			$content .= '<h4 class="more">Upgrade today to see this issue</h4>'."\n";
			$content .= '</div><!-- .rollover -->'."\n";
			$content .= '</a>'."\n";
		}

		$content .='<span class="info">'."\n";
		if ( $allowed ) {
			$content .= '<p class="links"><a class="button secondary" target="_blank" href="'.$pdfobj['url'].'">View PDF</a></p>'."\n";
		}

		if ( $current ) {
			$content .='<h4>Current Issue: '.$subtitle.'</h4>'."\n";
		} else {
			$content .='<h4>'.$subtitle.'</h4>'."\n";
		}
		if ( $allowed ) {
			$content .='<h3><a href="'.get_permalink().'">'.get_the_title().'</a></h3>'."\n";
		} else {
			$content .='<h3>'.get_the_title().'</h3>'."\n";
		}
		$content .= $basecontent;
		$content .='</span><!-- .info -->'."\n";

		$content .= '</div>'."\n";

	// end archives for magazine

	// archives for itinerary
	} else if (

		( is_archive() && get_query_var('post_type') == 'itinerary' )

	) {

		$content = '';

		$rows = get_field('itinerary-section');

		if($rows) {
			foreach($rows as $row) {

				$subtitle = $row['subtitle'];
				$daypart = $row['itinerary-content'];
				$gallery = $row['itinerary-gallery'];

				$content .= '<div class="divider">'."\n";
				$content .= '<h2>'.$subtitle.'</h2>'."\n";

				if ( $gallery ) {

/*
					$content .= '<div class="photo-gallery">'."\n";
					$content .= '<div class="royalSlider rsUni">'."\n";

					foreach($gallery as $imageobj) {

						$image = $imageobj['sizes']['hero-review'];

						$content .= '<div>'."\n";
						$content .= '<img class="rsImg" alt="'.$imageobj['caption'].'" src="'.$image.'">'."\n";
						if ( $imageobj['caption'] ) {
							$content .= '<div class="caption">'.$imageobj['caption'].'</div>'."\n";
						}
						$content .= '</div>'."\n";

					}

					$content .= '</div>'."\n";
					$content .= '</div>'."\n";

*/
					$content .= '<div class="photo-gallery hero heronopadding heronoborder">'."\n";
						$content .= '<div class="rslideswrapper">'."\n";

						$content .= '<ul class="hero heronopadding rslides">'."\n";

						foreach($gallery as $imageobj) {

							$image = $imageobj['sizes']['hero-review'];

							$content .= '<li>'."\n";
								$content .= '<img class="rsImg" alt="'.$imageobj['caption'].'" src="'.$image.'">'."\n";
								if ( $imageobj['caption'] ) {
									$content .= '<div class="caption">'.$imageobj['caption'].'</div>'."\n";
//									$content .= '<p class="summary">'.$imageobj['caption'].'</p>'."\n";
								}
							$content .= '</li>'."\n";


						}

						$content .= '</ul><!--.hero.rslides-->'."\n";

						$content .= '</div>'."\n";
					$content .= '</div>'."\n";

				}

				$content .= $daypart;
				$content .= '</div>'."\n";
			}
		}
	// end archives for itinerary

	// archives for library
	} else if (

		( is_archive() && get_query_var('post_type') == 'library' )

	) {

		$content = '';

		$rows = get_field('group-book');

		if($rows) {
			foreach($rows as $row) {

				$subtitle = $row['subtitle'];
				$group = $row['group-content'];

				$content .= '<div class="divider">'."\n";
				$content .= '<h2>'.$subtitle.'</h2>'."\n";
				$content .= $group;
				$content .= '</div>'."\n";
			}
		}
	// end archives for library

	// archives for press
	} else if (
		( is_archive() && get_query_var('post_type') == 'press' )
	) {

			$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );
			$imgsrc = $imageobj[0];
			$presslink = get_field('press-pdf');

			$content = '';

			$content .= '<a href="'.$presslink['url'].'" target="_blank">'."\n";
				if ( $imgsrc ) {
					$content .= '<img src="'.$imgsrc.'" alt="press item" />'."\n";
				} else {
					$content .= '<img src="'.get_bloginfo('stylesheet_directory').'/images/blank-thumb-press.png" alt="press item" />'."\n";
				}
				$content .= '<h3>'.get_the_title().'</h3>'."\n";
			$content .= '</a>'."\n";
			$content .= '<span class="date">'.get_the_date('M/Y').'</span>'."\n";

	// end archives for press

	// archives for career
	} else if (
		( is_archive() && get_query_var('post_type') == 'career' )
	) {

			$content = '';

			$content .= '<p><strong>'.get_the_title().'</strong></p>'."\n";
			$content .= '<p>'.get_the_excerpt().' <a class="more" href="#">Read More</a> | <a class="apply lightbox-inline" href="#lightbox-contact-apply-'.$post->ID.'">Apply</a></p>'."\n";

			$content .= '<div class="more">'."\n";
			$content .= $basecontent;
			$content .= '</div>'."\n";


			$content .= '<div id="lightbox-contact-apply-'.$post->ID.'" class="lightbox white-popup contact mfp-hide">'."\n";
				$content .= '<header>'."\n";
					$content .= '<h2>Apply Now</h2>'."\n";
					$content .= '<h3>'.get_the_title().'</h3>'."\n";
				$content .= '</header>'."\n";

			 $content .= do_shortcode('[contact-form-7 id="75334" title="Contact Apply Now"]');

			$content .= '</div><!-- #lightbox -->'."\n";


	// end archives for career

	// search page
	} else if ( is_search() ) {

			$content = '';

			$permalink = get_permalink($post->ID);

			// remove end of link if library or itinerary to return archive page
			if (strpos($permalink,'library') !== false) {
				$permalink = dirname($permalink);
			} else if (strpos($permalink,'itineraries') !== false) {
				$permalink = dirname($permalink);
			}

			$content .= '<a href="'.$permalink.'">'."\n";

				$imgsrc = '';
				// generate thumbnail from gallery header, if not, use featured image
				if ( $post->post_type == 'itinerary' ) {
					$itinerary = get_field('itinerary-section',$post->ID);
					if(!empty($itinerary[0]['itinerary-gallery']))
						$rows = $itinerary[0]['itinerary-gallery'];
					if(!empty($rows[0]['sizes']['thumb-medium']))
						$imgsrc = $rows[0]['sizes']['thumb-medium'];
					if(empty($imgsrc)) {
						$imgsrc = _get_firstimage( 'itinerary-gallery', 'thumb-medium', SHR_FIRSTIMAGE_ALL, false, $post->ID );
						$imgsrc = $imgsrc['src'];
					}
				} else {
//					$rows = get_field('gallery-header',$post->ID);
					$imgsrc = _get_firstimage( 'gallery-header', 'thumb-medium', SHR_FIRSTIMAGE_GALLERY, false, $post->ID );
					$imgsrc = $imgsrc['src'];
				}

				// If we don't have an image yet, use the library image (if applicable)
				if ( empty($imgsrc) && ( $post->post_type == 'library' ) ) {
					$imageobj = get_field('destinations-library-image', 'option');
					$imgsrc = $imageobj['sizes']['thumb-medium'];
				}

				// If we don't have an image yet, use the first content image
				if ( empty($imgsrc) ) {
					$imgsrc = _get_firstcontentimage( $post->ID );
					$imgsrc = str_replace('620x413', '220x146', $imgsrc);
				}

				// If we still don't have an image, use the attached post image
				if ( empty($imgsrc) ) {
					$imgsrc = _get_firstimage( 'gallery-header', 'thumb-medium', SHR_FIRSTIMAGE_ATTACH, false, $post->ID );
					$imgsrc = $imgsrc['src'];
				}

				// As a last possible resort, use the theme blank thumb
				if ( empty($imgsrc) ) {
					$imgsrc = get_bloginfo('stylesheet_directory').'/images/blank-thumb-medium-logo.png';
				}

				$content .= '<img src="'.$imgsrc.'" alt="Related" />'."\n";

				$content .= '<h3>'.get_the_title($post->ID).'</h3>'."\n";

				if ( $post->post_type == 'library' ) {
					$rows = get_field('group-book');
					$text = strip_shortcodes($rows[0]['group-content']);
				} else if ( $post->post_type == 'itinerary' ) {
					$rows = get_field('itinerary-section');
					$text = strip_shortcodes($rows[0]['itinerary-content']);
				} else {
					$text = strip_shortcodes( $post->post_content );
				}
				$text = str_replace(']]>', ']]>', $text);
				$text = str_replace('At a Glance', '', $text);
				$excerpt_length = 10; // 15 words
				$excerpt_more = apply_filters('excerpt_more', '...');
				$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

				$content .= '<p class="description">'.$text.'</p>'."\n";
			$content .= '</a>'."\n";

	// end search page

	// map page
	} else if (is_page_template ( 'template-page-map.php' ) ) {

		if (isset($_GET['destinations'])) {

			$term = get_term_by( 'slug', $_GET['destinations'], 'destinations' );
			$destinationstree = destinationstaxtree($term->term_id);
			$dest = $destinationstree['dest'];
			$reg = $destinationstree['reg'];
			$top = $destinationstree['top'];
			header('Location: /destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug);
		}

		print '<script type="text/javascript" src="'.get_bloginfo('stylesheet_directory').'/js/knockout-3.3.0.js"></script>';
		print '<script type="text/javascript" src="'.get_bloginfo('stylesheet_directory').'/js/knockout.mapping-2.4.1.js"></script>';
		print "\n";

		$content = '';

		include_once('includes/destinations.php');
		make_destinations();

		$destinations_rendered = get_destinations_list();
		$content .= '<script type="text/javascript" src="'.get_bloginfo('stylesheet_directory').'/js/destinations.js"></script>'."\n";
    $content .= theme_render_template( 'page--destinations', array( 'destinations' => $destinations_rendered ) );

	// end map page

	// book page
	} else if (is_page_template ( 'template-page-book.php' ) ) {

		export_destinations( false );
		export_hotels( false );

		$content .= '<section class="all-destinations all-articles contain">'."\n";

		$content .= '<div class="header">'."\n";
		$content .= '<h2>'.get_field('book-widget-title').'</h2>'."\n";
		$content .= '</div>'."\n";

		$content .= '<div class="widget-wrapper book">'."\n";
			$content .= '<div id="booking-widget" class="simple">'."\n";
				$content .= '<ul class="book-type contain">'."\n";
					$content .= '<li>Book Hotels</li>'."\n";
					$content .= '<li><a href="#" id="bookflights">Book Flights</a></li>'."\n";
				$content .= '</ul>'."\n";
				$content .= '<form id="book-hotels">'."\n";
					$content .= '<div class="form-combo">'."\n";
						$content .= '<span class="form-item"><input type="text" id="book-destination" class="element acInput" placeholder="Destination or Hotel" /><b class="icon" data-icon="&#61442;"></b></span>'."\n";
						$content .= '<div class="autocomplete"></div>'."\n";
					$content .= '</div>'."\n";
					$content .= '<div class="form-combo form-combo-date">'."\n";
						$content .= '<span class="form-item"><input type="text" id="dep_date" class="element dateinput" placeholder="Check In (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
						$content .= '<span class="form-item"><input type="text" id="ret_date" class="element dateinput" placeholder="Check Out (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
					$content .= '</div>'."\n";
					$content .= '<div class="buttons">'."\n";
						$content .= '<button type="submit" class="primary button">Find Rooms</button>'."\n";
					$content .= '</div>'."\n";
					$content .= '<div id="last_selected"></div>'."\n";
					$content .= '<input class="autocompletedestination" type="hidden" />'."\n";
				$content .= '</form>'."\n";
			$content .= '</div><!-- #booking-widget -->'."\n";
		$content .= '</div><!-- .widget-wrapper -->'."\n";

		$content .= '<script>'."\n";
		$content .= 'jQuery().ready(function($) {'."\n";

			$content .= '$("#book-destination").autocomplete({'."\n";
			$content .= 'resultsContainer: \'.autocomplete\','."\n";
			$content .= 'onItemSelect: function(item) {'."\n";
				$content .= '$(\'.autocompletedestination\').val(item.data);'."\n";
			$content .= '},'."\n";
			$content .= 'onNoMatch: function() {'."\n";
				$content .= '$(\'#book-destination\').val(bookingdestfield);'."\n";
			$content .= '}, '."\n";
			$content .= 'data: ['."\n";

			$datadestinations = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json');
			$filtersbooking = json_decode($datadestinations);

			foreach($filtersbooking as $row) {
				$name = indg_decode_string( $row[2] );
				$namenoaccent = remove_accents($name);
				$content .= '["'.$name.'",'.json_encode($row[0]).',"destination"],'."\n";
				if ( $name !== $namenoaccent ) {
					$content .= '["'.$namenoaccent.'",'.json_encode($row[0]).',"destination"],'."\n";
				}
			}

			$datahotels = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json');
			$filtersbooking = json_decode($datahotels);

			foreach($filtersbooking as $row) {
				$name = indg_decode_string( $row[1] );
				$namenoaccent = remove_accents($name);
				$content .= '["'.$name.'",'.json_encode($row[2]).',"hotel"],'."\n";
				if ( $name !== $namenoaccent ) {
					$content .= '["'.$namenoaccent.'",'.json_encode($row[2]).',"hotel"],'."\n";
				}
			}

			$content .= ']'."\n";
			$content .= '});'."\n";

			$content .= 'var bookingdestfield = $(\'input#book-destination\').val();'."\n";

		$content .= '});'."\n";
		$content .= '</script>'."\n";

		$content .= '<div class="header divider">'."\n";
		$content .= '<h2>'.get_field('book-help-main-title').'</h2>'."\n";
		$content .= get_field('book-help-main-content');
		$content .= '</div>'."\n";

		$content .= '</section><!-- .all-destinations -->'."\n";

		$content .= '<section class="all-destinations all-articles contain">'."\n";

			$rows = get_field('book-help');

			foreach ( $rows as $row ) {

				$helptitle = $row['book-help-title'];
				$helpcontent = $row['book-help-content'];
				$imageobj = $row['book-help-image'];
				$image = $imageobj['sizes']['large'];

				$content .= '<article>'."\n";
				$content .= '<img src="'.$image.'" />'."\n";
				$content .= '<h3>'.$helptitle.'</h3>'."\n";
				$content .= $helpcontent."\n";
				$content .= '</article>'."\n";

			}

		$content .= '</section><!-- .all-destinations -->'."\n";

		$content .= '<div class="header">'."\n";
		$content .= '<h2 class="center spacebefore"><a class="contact lightbox-inline" href="#lightbox-contact-team">'.get_field('book-contact-cta').'</a></h2>'."\n";
		$content .= '</div>'."\n";

		$rows = get_field('book-interests');

		if ( $rows ) {

			$content .= '<div class="header divider">'."\n";
			$content .= '<h2>'.get_field('book-interests-title').'</h2>'."\n";
			$content .= get_field('book-interests-content');
			$content .= '</div>'."\n";

			$content .= '<section class="recent-articles interests contain">'."\n";

				foreach ( $rows as $row ) {

					$image = $row['book-interest-image'];
					$imgsrc = $image['sizes']['thumb-small'];
					$url = $row['book-interest-link'];

					$content .= '<article class="filter">'."\n";
						if ( $url ) {
							$content .= '<a href="'.$url.'">'."\n";
						} else {
							$content .= '<span>'."\n";
						}
							$content .= '<img src="'.$imgsrc.'" alt="Interest" />'."\n";
							$content .= '<h3>'.$row['book-interest-title'].'</h3>'."\n";
						if ( $url ) {
							$content .= '</a>'."\n";
						} else {
							$content .= '</span>'."\n";
						}
					$content .= '</article>'."\n";

				}

			$content .= '</section><!-- .recent-articles-->'."\n";

		}

	// end book page

	// sign up step one page
	} else if (is_page_template ( 'template-page-user-signup.php' ) ) {

		$content = '';

		$content .= '<h1>'.get_the_title().'</h1>'."\n";
		$content .= $basecontent;

		$array = \WPSF\Membership::query_sellable();
		if ( ! is_wp_error( $array ) ) {

			$content .= '<div class="memberlevelsnav">'."\n";
//			$content .= '<a href="#" class="rslides_nav prev">Previous</a>'."\n";
//			$content .= '<a href="#" class="rslides_nav next">Next</a>'."\n";
			$content .= '</div>'."\n";
			$content .= '<section class="all-destinations memberlevels contain">'."\n";
			$sorted = array();
			foreach ( $array as $m ) {
				$m->load_post();
				$kamt = 0;
				if ( ! empty( $m->Amount__c ) ) {
					$kamt = $m->Amount__c;
				}
				if(empty($m->post) ) {
					$k = 10000 + $kamt;
				} else {
					$k = get_field( 'sort', $m->post->ID );
					if ( empty( $k ) ) {
						$k = 10000 + $kamt;
					}
				}
				$k = intval( $k );
				while ( array_key_exists( $k, $sorted ) ) {
					$k++;
				}
				$sorted[$k] = $m;
			}
			ksort( $sorted );
			foreach ( $sorted as $m ) {
				$content .= $m->render();
			}
			$content .= '</section>'."\n";
		}

		$rows = get_field('gallery');

		if ( $rows ) {

			$content .= '</div></div></div></div></div></div>'."\n";

			$content .= '<div class="image-wrapper">'."\n";

			$content .= '<ul class="rslides">'."\n";

				foreach($rows as $row) {

					$quote = $row['gallery-quote'];
					$citation = $row['gallery-citation'];
					$imageobj = $row['gallery-image'];
					$image = $imageobj['url'];

					$content .= '<li>'."\n";
						$content .= '<img src="'.$image.'" alt="" />'."\n";
						if ( $quote ) {
							$content .= '<div class="quotewrapper">'."\n";
								$content .= '<div class="quoteinner">'."\n";
									$content .= '<p>&ldquo;'.$quote.'&rdquo;';
									if ( $citation ) {
										$content .= '<br /><em>'.$citation.'</em>';
									}
									$content .= '</p>';
								$content .= '</div>'."\n";
							$content .= '</div>'."\n";
						}
					$content .= '</li>'."\n";

				}

			$content .= '</ul><!--.hero.rslides-->'."\n";

			$content .= '<div class="rslides_tabs_wrapper"></div>'."\n";

			$content .= '</div>'."\n";

			$content .= '<div class="candy-wrapper contain"><div class="candy-inner"><div class="container standard"><div class="content"><div class="hentry"><div class="entry-content">'."\n";

		}

		$content .= '<div class="join-contact">'."\n";
		$content .= '<div class="left"><h4>Question about Indagare? </h4></div>';
		$content .= '<div class="right"><span>Contact Us:</span> <a href="tel:+12129882611">212-988-2611</a>&nbsp;|&nbsp;<a href="mailto:membership@indagare.com">membership@indagare.com</a></div>';
		$content .= '</div>'."\n";

/*
		$rows = get_field('join-quote');
		if ( $rows ) {

			$i = 0;

			shuffle($rows);

			$content .= '<div id="rslideswrapper">'."\n";

			$content .= '<ul class="hero rslides">'."\n";

			foreach($rows as $row) {

					$quotecontent = $row['join-quote-content'];
					$quotecitation = $row['join-quote-citation'];

					$content .= '<li>'."\n";
						$content .= '<blockquote><span class="openclose">&#8220;</span>'.$quotecontent.'<span class="openclose">&#8221;</span></blockquote>'."\n";
						$content .= '<cite> ~ '.$quotecitation.'</cite>'."\n";
					$content .= '</li>'."\n";


			}

			$content .= '</ul><!--.hero.rslides-->'."\n";

			$content .= '</div>'."\n";

		}
*/

	// end sign up step one page

	// sign up step two page
	} else if (is_page_template ( 'template-page-user-signup-step-two.php' ) ) {
    	$content = \indagare\wp\WPContent::getContent('signup');
	// end sign up step two page

	// site invite email link landing page
	//} else if (is_page_template ( 'template-page-user-site-invite.php' ) ) {
    //	$content = \indagare\wp\WPContent::getContent('invite');
	// end site invite email link landing page

	// contact page
	} else if (is_page_template ( 'template-page-contact.php' ) ) {

		$content = do_shortcode('[contact-form-7 id="28536" title="Contact"]');

	// end contact page

	// how to book page
	} else if (is_page_template ( 'template-page-how-to-book.php' ) ) {

		$content = '';

		$content .= '<h1>'.get_the_title().'</h1>'."\n";

		$rows = get_field('faq');

		$i = 1;

		if($rows) {
			foreach($rows as $row) {

				$q = $row['faq-question'];

				$content .= '<div>'."\n";
					$content .= '<h2><a href="#faq'.$i.'">'.$q.'</a></h2>'."\n";
				$content .= '</div>'."\n";

				$i++;
			}
		}

		$content .= '<div class="join-cta"><a href="/join/">Join</a></div>'."\n";

		$i = 1;

		if($rows) {
			foreach($rows as $row) {

				$q = $row['faq-question'];
				$a = $row['faq-answer'];

				$content .= '<div id="faq'.$i.'">'."\n";
					$content .= '<h2>'.$q.'</h2>'."\n";
					$content .= $a;
				$content .= '</div>'."\n";

				$i++;
			}
		}

		$content .= '<div class="join-contact">'."\n";
		$content .= '<div class="left"><h4>Question about Indagare? </h4></div>';
		$content .= '<div class="right"><span>Contact Us:</span> <a href="tel:+12129882611">212-988-2611</a>&nbsp;|&nbsp;<a href="mailto:membership@indagare.com">membership@indagare.com</a></div>';
		$content .= '</div>'."\n";

	// end how to book page

	// 	how we work page
	} else if (is_page_template ( 'template-page-how-we-work.php' ) ) {

		$rows = get_field('steps');

		$content = '';

		$content .= '<h1>'.get_the_title().'</h1>'."\n";

		$content .= $basecontent;

		$i = 1;

		if ( $rows ) {

			foreach($rows as $row) {

				$steptitle = $row['step-title'];
				$stepcontent = $row['step-content'];
				$imageobj = $row['step-image'];
				$image = $imageobj['url'];

				if ($imageobj) {
					$content .= '</div></div></div></div></div></div>'."\n";
					$content .= '<div class="image-wrapper"><img src="'.$image.'" alt="" /></div>'."\n";
					$content .= '<div class="candy-wrapper contain"><div class="candy-inner"><div class="container standard"><div class="content"><div class="hentry"><div class="entry-content">'."\n";
				}

				$content .= '<div class="step">'."\n";
				$content .= '<div class="stepnumber">'.$i.'</div>'."\n";
				$content .= '<h2>'.$steptitle.'</h2>'."\n";
				$content .= $stepcontent;
				$content .= '</div>'."\n";

				$content .= '<div class="join-cta"><a href="/join/">Join</a></div>'."\n";

				$i++;

			}

		}

		$content .= '<div class="join-contact">'."\n";
		$content .= '<div class="left"><h4>Question about Indagare? </h4></div>';
		$content .= '<div class="right"><span>Contact Us:</span> <a href="tel:+12129882611">212-988-2611</a>&nbsp;|&nbsp;<a href="mailto:membership@indagare.com">membership@indagare.com</a></div>';
		$content .= '</div>'."\n";

	// end how we work page

	// why join page
	} else if (is_page_template ( 'template-page-why-join.php' ) ) {

		$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );

		$content = '';

		if ( $imgsrc ) {

			$content .= '<div class="contain">'."\n";

			$content .= '<div class="believeright"><img src="'.$imgsrc[0].'" /></div>'."\n";
			$content .= '<div class="believeleft">'."\n";

		}

		$content .= '<h1>'.get_the_title().'</h1>'."\n";

		$content .= $basecontent;

		if ( $imgsrc ) {

			$content .= '</div>'."\n";

			$content .= '</div>'."\n";
		}

		$content .= '<div class="join-cta"><a href="/join/">Join</a></div>'."\n";

		$content .= '<div class="header"><h2>What We Do Best</h2></div>'."\n";

		$rows = get_field('benefit');

		if($rows) {

			$content .= '<section class="all-destinations contain indbenefit">'."\n";

				foreach($rows as $row) {

					$benefittitle = $row['benefit-title'];
					$benefitcontent = $row['benefit-content'];
					$imageobj = $row['benefit-image'];
					$image = $imageobj['url'];

					$content .= '<article>'."\n";
						$content .= '<img src="'.$image.'" alt="" />'."\n";
						$content .= '<h3>'.$benefittitle.'</h3>'."\n";
						$content .= $benefitcontent;
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

		$rows = get_field('gallery');

		if ( $rows ) {

			$content .= '</div></div></div></div></div></div>'."\n";

			$content .= '<div class="image-wrapper">'."\n";

			$content .= '<ul class="rslides">'."\n";

				foreach($rows as $row) {

					$quote = $row['gallery-quote'];
					$citation = $row['gallery-citation'];
					$imageobj = $row['gallery-image'];
					$image = $imageobj['url'];

					$content .= '<li>'."\n";
						$content .= '<img src="'.$image.'" alt="" />'."\n";
						if ( $quote ) {
							$content .= '<div class="quotewrapper">'."\n";
								$content .= '<div class="quoteinner">'."\n";
									$content .= '<p>&ldquo;'.$quote.'&rdquo;';
									if ( $citation ) {
										$content .= '<br /><em>'.$citation.'</em>';
									}
									$content .= '</p>';
								$content .= '</div>'."\n";
							$content .= '</div>'."\n";
						}
					$content .= '</li>'."\n";

				}

			$content .= '</ul><!--.hero.rslides-->'."\n";

			$content .= '<div class="rslides_tabs_wrapper"></div>'."\n";

			$content .= '</div>'."\n";

			$content .= '<div class="candy-wrapper contain"><div class="candy-inner"><div class="container standard"><div class="content"><div class="hentry"><div class="entry-content">'."\n";

		}

		$content .= '<div class="join-cta"><a href="/join/">Join</a></div>'."\n";

		$content .= '<div class="header"><h2>The Indagare Advantage</h2></div>'."\n";

		$rows = get_field('advantage');

		if($rows) {

			$content .= '<section class="all-destinations contain advantage">'."\n";

				foreach($rows as $row) {

					$advantagetitle = $row['advantage-title'];
					$advantagecontent = $row['advantage-content'];

					$content .= '<article>'."\n";
						$content .= '<h3>'.$advantagetitle.'</h3>'."\n";
						$content .= $advantagecontent;
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

		$content .= '<div class="join-contact">'."\n";
		$content .= '<div class="left"><h4>Question about Indagare? </h4></div>';
		$content .= '<div class="right"><span>Contact Us:</span> <a href="tel:+12129882611">212-988-2611</a>&nbsp;|&nbsp;<a href="mailto:membership@indagare.com">membership@indagare.com</a></div>';
		$content .= '</div>'."\n";

	// end why join page

	// welcome page
	} else if (is_page_template ( 'template-page-welcome.php' ) ) {

		$content = '';

		$content .= '<div class="header"><h2>Getting Started</h2></div>'."\n";

		$rows = get_field('getting-started');

		if($rows) {

			$content .= '<section class="all-destinations contain">'."\n";

				foreach($rows as $row) {

					$starttitle = $row['getting-started-title'];
					$startcontent = $row['getting-started-content'];
					$starturl = $row['getting-started-url'];
					$imageobj = $row['getting-started-image'];
					$image = $imageobj['sizes']['thumb-large'];

					$content .= '<article>'."\n";
						$content .= '<a href="'.$starturl.'">'."\n";
						if ($imageobj) {
							$content .= '<img src="'.$image.'" alt="" />'."\n";
						}
						$content .= '<h3>'.$starttitle.'</h3>'."\n";
						$content .= $startcontent;
						$content .= '<span class="read-more">Read More</span>';
						$content .= '</a>'."\n";
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

		$content .= '<div class="header divider"><h2>Popular Destinations</h2><p class="view-more"><a href="/">View All Destinations</a></p></div>'."\n";

		$rows = get_field('welcome-destination', 'option');
		shuffle($rows);

		if($rows) {

			$i = 0;

			$content .= '<section class="related-articles contain">'."\n";

				foreach($rows as $row) {

					if ( $i < 4 ) {

						$content .= '<article>'."\n";

						$destinationstree = destinationstaxtree(implode($row));
						$dest = $destinationstree['dest'];
						$reg = $destinationstree['reg'];
						$top = $destinationstree['top'];

						$imageobj = get_field('header-image', 'destinations' . '_' . $dest->term_id);
						$image = $imageobj['sizes']['thumb-medium'];
						$overview = get_field('destination-overview', 'destinations' . '_' . $dest->term_id);

						$overview = strip_shortcodes( $overview );
						$overview = str_replace(']]>', ']]>', $overview);
						$excerpt_length = 15; // 15 words
						$excerpt_more = apply_filters('excerpt_more', '...');
						$overview = wp_trim_words( $overview, $excerpt_length, $excerpt_more );


							$content .= '<a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/">'."\n";
							if ($imageobj) {
								$content .= '<img src="'.$image.'" alt="" />'."\n";
							}
							$content .= '<h3>'.$dest->name.'</h3>'."\n";
							$content .= '<p class="description">'.$overview.'</p>'."\n";
							$content .= '</a>'."\n";

						$content .= '</article>'."\n";

						$i++;

					}

				}

			$content .= '</section>'."\n";

		}
	// end welcome page

	// intro page
	} else if (is_page_template ( 'template-page-intro.php' ) ) {

		$content = '';

		$content .= '<div class="introtease">'."\n";

			$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
			$imgsrc = $imageobj[0];

			if ( $imgsrc ) {
				$content .= '<img class="intrologo" src="'.$imgsrc.'" alt="Indagare" />'."\n";
			}

			$content .= $basecontent;

		$content .= '</div>'."\n";

		$rows = get_field('column');

		if ( $rows ) {

			$content .= '<section class="all-destinations contain">'."\n";

				foreach($rows as $row) {

					$imageobj = $row['column-icon'];
					$imgsrc = $imageobj['url'];
					$coltitle = $row['column-title'];
					$colcontent = $row['column-content'];

					$content .= '<article>'."\n";
						$content .= '<img src="'.$imgsrc.'" />'."\n";
						$content .= '<h3>'.$coltitle.'</h3>'."\n";
						$content .= $colcontent;
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

		$rows = get_field('button');

		if ( $rows ) {

			$content .= '<section class="all-destinations contain center">'."\n";

				foreach($rows as $row) {

					$buttonclass = $row['button-class'];

					$content .= '<article>'."\n";
						if ( $buttonclass ) {
							$content .= '<a href="'.$row['button-url'].'" class="'.$row['button-class'].'">'.$row['button-content'].'</a>'."\n";
						} else {
							$content .= '<a href="'.$row['button-url'].'">'.$row['button-content'].'</a>'."\n";
						}
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}


	// end intro page

	// new page
	} else if (is_page_template ( 'template-page-new.php' ) ) {

		$content = '';

		$rows = get_field('new-features');

		if($rows) {

			$content .= '<section class="all-destinations contain" location="EEEE">'."\n";

				foreach($rows as $row) {

					$newtitle = $row['new-features-title'];
					$newcontent = $row['new-features-content'];

					$content .= '<article>'."\n";
						$content .= '<h3>'.$newtitle.'</h3>'."\n";
						$content .= $newcontent;
					$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

		$content .= '<div class="header divider">'."\n";

		$imageobj = get_field('new-features-callout-image');
		$imgsrc = $imageobj['sizes']['thumb-medium'];

		if ( $imgsrc ) {
			$content .= '<div class="callout calloutimg"><img src="'.$imgsrc.'" /></div><div class="callout callouttext"><strong>'.get_field('new-features-callout-content').'</strong></div>'."\n";
		} else {
			$content .= '<div class="callout callouttext"><strong>'.get_field('new-features-callout-content').'</strong></div>'."\n";
		}

		$content .= '</div>'."\n";

		$content .= '<div class="header divider"><h2>Enter the new Indagare now &#8211; pick an article to experience the Indagare redesign:</p></div>'."\n";

		$rows = get_field('new-articles');

		if($rows) {

			$content .= '<section class="related-articles contain">'."\n";

				foreach($rows as $row) {

						$content .= '<article>'."\n";
						$content .= '<a href="'.get_permalink($row).'">'."\n";
						$content .= '<h3>'.get_the_title($row).'</h3>'."\n";
						$content .= '</a>'."\n";
						$content .= '</article>'."\n";

				}

			$content .= '</section>'."\n";

		}

	// end new page

	// my account page
	} else if ( is_page_template ( 'template-page-account-edit.php' ) ) {
		if ( ind_logged_in() ) {
			$content = \indagare\wp\WPContent::getContent("account");
		} else {
			$content = '<p>You need to log in to see this page.</p>'."\n";
		}

	// end my account page

	// wish list page
	} else if ( is_page_template ( 'template-page-account-wish-list.php' ) ) {

		$content = '';

		if ( ! ind_logged_in() ) {

//			header('Location: /' );

			$content = '<p>You need to log in to see this page.</p>'."\n";


		} else {

			$favorites = indagare\users\User::getFavorites();

			if ( $favorites ) {

				$content .= '<section class="related-articles contain">'."\n";

				foreach ( $favorites as $favorite ) {

					$favoritepost = get_post($favorite->article_id);

					$content .= '<article>'."\n";
						$content .= '<a href="'.get_permalink($favoritepost->ID).'">'."\n";

							$imgsrc = _get_firstimage( 'gallery-header', 'thumb-medium', SHR_FIRSTIMAGE_ALL, false, $value );
							$imgsrc = str_replace( '620x413', '220x146', $imgsrc['src'] );
							if ( empty( $imgsrc ) ) {
								$imgsrc = get_bloginfo('stylesheet_directory').'/images/blank-thumb-medium-logo.png';
							}
							// generate thumbnail from gallery header, if not, use featured image
//							$rows = get_field('gallery-header',$favoritepost->ID);
/*
							$rowsraw = get_field('gallery-header',$favoritepost->ID,false);

							if ( $rowsraw ) {
								$imageid = $rowsraw[0];
								$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-medium' );
								$imgsrc = $imageobj[0];
							} else if ( catch_that_image($favoritepost->ID) ) {
								$imgsrc = catch_that_image($favoritepost->ID);
								$imgsrc = str_replace('620x413', '220x146', $imgsrc);
							} else {
								$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($favoritepost->ID), 'thumb-medium' );
								$imgsrc = $imageobj[0];
							}
*/
							$content .= '<img src="'.$imgsrc.'" alt="Related" />'."\n";

							$content .= '<h3>'.get_the_title($favoritepost->ID).'</h3>'."\n";

							$text = strip_shortcodes( $favoritepost->post_content );
							$text = str_replace(']]>', ']]>', $text);
							$text = str_replace('At a Glance', '', $text);
							$excerpt_length = 15; // 15 words
							$excerpt_more = apply_filters('excerpt_more', '...');
							$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

							$content .= '<p class="description">'.$text.'</p>'."\n";
						$content .= '</a>'."\n";
					$content .= '</article>';

				}

				$content .= '</section><!-- .related-articles -->'."\n";

			}

		}

	// end wish list page

	// password reset page
	} else if (is_page_template ( 'template-page-password-reset.php' ) ) {

		$content = '';

		$content .= '<div class="wpcf7">'."\n";
			$content .= '<form id="form-reset" class="login" method="post" novalidate="">'."\n";
				$content .= '<div id="field1-container" class="field">'."\n";
					$content .= '<label for="field1">Email</label>'."\n";
					$content .= '<input type="text" name="email" id="email" required="required" placeholder="Your email">'."\n";
				$content .= '</div>'."\n";

				$content .= '<div id="form-submit" class="field clearfix submit">'."\n";
					$content .= '<label for=""></label>'."\n";
				  $content .= ' <input type="submit" value="Submit Request" class="button primary">'."\n";
				$content .= '</div>'."\n";

				$content .= '<div class="field message">'."\n";
				$content .= '</div>'."\n";

			$content .= '</form>'."\n";
		$content .= '</div>'."\n";

	// end password reset page

	// external login page
	} else if (is_page_template ( 'template-page-external-login.php' ) ) {

		$content = '';
		$content .= '<div class="styledform">'."\n";
			$content .= '<form id="form-external-login" class="login" method="post" novalidate="" action="/wp-content/themes/indagare/app/lib/external_login.php?submit=yes">'."\n";
				$content .= '<div id="field1-container" class="field">'."\n";
					$content .= '<label for="field1">Username</label>'."\n";
					$content .= '<input type="text" name="externaluser" id="externaluser" required="required" placeholder="Username">'."\n";
				$content .= '</div>'."\n";
				$content .= '<div id="field1-container" class="field">'."\n";
					$content .= '<label for="field1">Password</label>'."\n";
					$content .= '<input type="text" name="externalpassword" id="externalpassword" required="required" placeholder="Password">'."\n";
				$content .= '</div>'."\n";
				$getparams=array("pc","gdsType","cin","cout");
				foreach ($getparams as $keyget => $valueget)
				{
					if (isset($_GET[$valueget]))
					{
						$content .='<input type="hidden" name="'.$valueget.'" id="'.$valueget.'" value="'.$_GET[$valueget].'">'."\n";
					}
					else
					{
						$content .='<input type="hidden" name="'.$valueget.'" id="'.$valueget.'" value="">'."\n";
					}
				};
				$content .= '<div id="form-submit" class="field clearfix submit">'."\n";
					$content .= '<label for=""></label>'."\n";
				  $content .= ' <input type="submit" value="Login" class="button primary">'."\n";
				$content .= '</div>'."\n";

				$content .= '<div class="field message">'."\n";
				$content .= '</div>'."\n";

			$content .= '</form>'."\n";
		$content .= '</div>'."\n";

	// end external login page

	// about mission page
	} else if (is_page_template ( 'template-page-about-mission.php' ) ) {

	$statement = get_field('mission-statement');
	$rows = get_field('mission');

	$content = '';

	if ( $statement ) {
		$content .= '<h2 class="mission">'.$statement.'</h2>'."\n";
	}

	if ( $rows ) {

		$content .= '<section id="masonry" class="contain">'."\n";
			$content .= '<article class="grid-sizer"></article>'."\n";

		foreach ( $rows as $row ) {

			$imageurl = $row['mission-url'];
			$imageobj = $row['mission-image'];
			$image = $imageobj['sizes']['large'];
			$imagesize = getimagesize($image);

			if ( $imagesize[0] == 140 && $imagesize[1] == 140 ) {
				$content .= '<article class="item">'."\n";
			} else if ( $imagesize[0] == 300 && $imagesize[1] == 140 || $imagesize[0] == 600 && $imagesize[1] == 280 ) {
				$content .= '<article class="item med">'."\n";
			} else if ( $imagesize[0] == 300 && $imagesize[1] == 300 ) {
				$content .= '<article class="item med">'."\n";
			} else if ( $imagesize[0] == 460 && $imagesize[1] == 300 ) {
				$content .= '<article class="item large">'."\n";
			} else {
				$content .= '<article class="item">'."\n";
			}
			if ( $imageurl ) {
				$content .= '<a href="'.$imageurl.'"><img src="'.$image.'" alt="item" /></a>'."\n";
			} else {
				$content .= '<img src="'.$image.'" alt="item" />'."\n";
			}
			$content .= '</article>'."\n";

		}

		$content .= '</section><!-- .all-destinations.contain -->'."\n";

	}

	// end about mission page

	// about founder page
	} else if (is_page_template ( 'template-page-about-founder.php' ) ) {

		$content = '';
		$content .= '<h2>'.get_the_title().'</h2>'."\n";
		$content .= $basecontent;

	// end about founder page

	// about team page | about contributor page
	} else if (is_page_template ( 'template-page-about-team.php' ) | is_page_template ( 'template-page-about-contributors.php' ) ) {

		$content = '';

		if ( is_page_template ( 'template-page-about-team.php' ) ) {
			$args = array( 'meta_key' => 'author-group', 'meta_value' => 'team' );
		} else {
			$args = array( 'meta_key' => 'author-group', 'meta_value' => 'contributor' );
		}

		$user_query = new WP_User_Query( $args );

		$authors = array();

		$i = 0;

		foreach ( $user_query->results as $user ) {
			$authors[$i] = $user;
			$i++;
		}

		usort ( $authors, 'sort_my_users_by_lastname' );

		// User Loop
		if ( ! empty( $authors ) ) {

			$content .= '<section class="all-destinations press team contain">'."\n";

			foreach ( $authors as $user ) {

				$userid = 'user_'.$user->ID;

				$authortitle = get_field('author-title', $userid);
				$imageobj = get_field('author-image', $userid);
				$image = $imageobj['sizes']['large'];

				$rows = get_field('author-recently-visited', $userid);

				$content .= '<article>'."\n";
					$content .= '<div class="thumbnail">'."\n";
						$content .= '<a href="'.get_author_posts_url( $user->ID ).'">'."\n";
							if ( $image ) {
								$content .= '<img src="'.$image.'" alt="team member" />'."\n";
							} else {
								$content .= '<img src="'.get_bloginfo('stylesheet_directory').'/images/blank-thumb-team.png" alt="team member" />'."\n";
							}
						$content .= '</a>'."\n";
						if ( $rows ) {
							$content .= '<div class="rollover">'."\n";
								$content .= '<h4>'.$user->first_name.' Recently Visited</h4>'."\n";
								$content .= '<ul>'."\n";
									foreach ( $rows as $row ) {
										$content .= '<li>'.$row['author-recently-visited-title'].'</li>'."\n";
									}
								$content .= '</ul>'."\n";
								$content .= '<a href="'.get_author_posts_url( $user->ID ).'" class="more">Read More</a>'."\n";
							$content .= '</div><!-- .rollover -->'."\n";
						}
					$content .= '</div><!-- .thumbnail -->'."\n";
						$content .= '<span class="info">'."\n";
							$content .= '<a href="'.get_author_posts_url( $user->ID ).'">'."\n";
								$content .= '<h3>'.$user->display_name.'</h3>'."\n";
							$content .= '</a>'."\n";
							$content .= '<span class="date">'.$authortitle.'</span>'."\n";
						$content .= '</span><!-- .info -->'."\n";
				$content .= '</article>'."\n";
			}

			$content .= '</section>'."\n";
		} else {
		}

	// end about team page | about contributor page

	} // end child_singlepost conditional

	return $content;
}
add_filter('the_content','child_singlepost');

// 404 page content
function childtheme_override_404_content() {
	global $post;

	$args = array(
		'post_type' => 'page',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_wp_page_template',
				'value' => 'template-page-404.php', // template name as stored in the dB
			)
		)
	);

	$notfound = new WP_Query($args);

	while ($notfound->have_posts()) : $notfound->the_post();

		echo '<div class="header"><h2>Getting Started</h2></div>'."\n";

		$rows = get_field('getting-started');

		if($rows) {

			echo '<section class="all-destinations contain" location="FFFF">'."\n";

				foreach($rows as $row) {

					$starttitle = $row['getting-started-title'];
					$startcontent = $row['getting-started-content'];
					$starturl = $row['getting-started-url'];
					$imageobj = $row['getting-started-image'];
					$image = $imageobj['sizes']['thumb-large'];

					echo '<article>'."\n";
						echo '<a href="'.$starturl.'">'."\n";
						if ($imageobj) {
							echo '<img src="'.$image.'" alt="" />'."\n";
						}
						echo '<h3>'.$starttitle.'</h3>'."\n";
						echo $startcontent;
						echo '<span class="read-more">Read More</span>';
						echo '</a>'."\n";
					echo '</article>'."\n";

				}

			echo '</section>'."\n";

		}

	endwhile;

	echo '<div class="header divider"><h2>Popular Destinations</h2><p class="view-more"><a href="/">View All Destinations</a></p></div>'."\n";

	$rows = get_field('welcome-destination', 'option');
	shuffle($rows);

	if($rows) {

		$i = 0;

		echo '<section class="related-articles contain">'."\n";

			foreach($rows as $row) {

				if ( $i < 4 ) {

					echo '<article>'."\n";

					$destinationstree = destinationstaxtree(implode($row));
					$dest = $destinationstree['dest'];
					$reg = $destinationstree['reg'];
					$top = $destinationstree['top'];

					$imageobj = get_field('header-image', 'destinations' . '_' . $dest->term_id);
					$image = $imageobj['sizes']['thumb-medium'];
					$overview = get_field('destination-overview', 'destinations' . '_' . $dest->term_id);

					$overview = strip_shortcodes( $overview );
					$overview = str_replace(']]>', ']]>', $overview);
					$excerpt_length = 15; // 15 words
					$excerpt_more = apply_filters('excerpt_more', '...');
					$overview = wp_trim_words( $overview, $excerpt_length, $excerpt_more );


						echo '<a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/">'."\n";
						if ($imageobj) {
							echo '<img src="'.$image.'" alt="" />'."\n";
						}
						echo '<h3>'.$dest->name.'</h3>'."\n";
						echo '<p class="description">'.$overview.'</p>'."\n";
						echo '</a>'."\n";

					echo '</article>'."\n";

					$i++;

				}

			}

		echo '</section>'."\n";

	}

} // end 404 page content

// remove thematic post footer
function childtheme_override_postfooter() {}

// below container
function child_belowcontainer() {
global $post;

	if ( is_archive() ) {
		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
		$depth = $destinationstree['depth'];
	}

	if ( is_singular() ) {
		$destinationstree = destinationstree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
	}

	// sidebar for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
	) {
		// primary
		echo '<div id="primary">'."\n";
				// booking widget
				echo '<div id="booking-widget" class="double">'."\n";
					echo '<ul class="book-type contain">'."\n";
						echo '<li>Book Hotels</li>'."\n";
						echo '<li><a href="#" id="bookflights">Book Flights</a></li>'."\n";
					echo '</ul>'."\n";
					echo '<form id="book-hotels">'."\n";
						echo '<div class="form-combo">'."\n";
							echo '<span class="form-item"><input type="text" id="book-destination" class="element acInput" placeholder="Destination or Hotel" /><b class="icon" data-icon="&#61442;"></b></span>'."\n";
							echo '<div class="autocomplete"></div>'."\n";
						echo '</div>'."\n";
						echo '<div class="form-combo">'."\n";
							echo '<span class="form-item"><input type="text" id="dep_date" class="element dateinput" placeholder="Check In (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
							echo '<!-- <div id="ui-datepicker-div"></div> -->'."\n";
							echo '<span class="form-item"><input type="text" id="ret_date" class="element dateinput" placeholder="Check Out (optional)" /><b class="icon" data-icon="&#61555;"></b></span>'."\n";
							echo '<!-- <div id="book-ckeck-out-cal"></div> -->'."\n";
						echo '</div>'."\n";
						echo '<div class="buttons">'."\n";
							echo '<button type="submit" class="primary button">Find Rooms</button>'."\n";
						echo '</div>'."\n";
						echo '<div id="last_selected"></div>'."\n";
						echo '<input class="autocompletedestination" type="hidden" />'."\n";
					echo '</form>'."\n";
					if ( $depth !== 1 ) {
						echo '<p class="view-all"><a href="/destinations/'.$top->slug.'/'.$reg->slug.'/hotels/">Or view all hotels in '.$reg->name.'</a></p>'."\n";
					}
				echo '</div>'."\n";
				// end booking widget
?>

<script>
jQuery().ready(function($) {

	$("#book-destination").autocomplete({
	resultsContainer: '.autocomplete',
	onItemSelect: function(item) {
		$('.autocompletedestination').val(item.data);
	},
	onNoMatch: function() {
		$('#book-destination').val(bookingdestfield);
	},
	data: [
<?php
	$datadestinations = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json');
	$filtersbooking = json_decode($datadestinations);

	foreach($filtersbooking as $row) {
		$name = indg_decode_string( $row[2] );
		$namenoaccent = remove_accents($name);
		echo '["'.$name.'",'.json_encode($row[0]).',"destination"],'."\n";
		if ( $name !== $namenoaccent ) {
			echo '["'.$namenoaccent.'",'.json_encode($row[0]).',"destination"],'."\n";
		}
	}

	$datahotels = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json');
	$filtersbooking = json_decode($datahotels);

	foreach($filtersbooking as $row) {
		$name = indg_decode_string( $row[1] );
		$namenoaccent = remove_accents($name);
		echo '["'.$name.'",'.json_encode($row[2]).',"hotel"],'."\n";
		if ( $name !== $namenoaccent ) {
			echo '["'.$namenoaccent.'",'.json_encode($row[2]).',"hotel"],'."\n";
		}
	}
?>
	]
	});

<?php
	if ( is_singular( 'hotel' ) ) {
		$hotelbooking = get_field('booking');

		// check if there is a booking code for the hotel - if not, do not autofill
		if ( $hotelbooking !== '' ) {

			if ( strlen($hotelbooking) < 7 ) {
				$hotelbooking = str_pad($hotelbooking, 7, "0", STR_PAD_LEFT);
			} else if ( strlen($hotelbooking) > 7 ) {
				$hotelbooking = substr($hotelbooking,-7);
			}

?>
	$('#book-destination').val('<?php echo addslashes(html_entity_decode(get_the_title())); ?>');
	$('.autocompletedestination').val('<?php echo $hotelbooking; ?>,hotel');

<?php
		}

	} else if ( is_archive() && get_query_var('post_type') == 'hotel' && $depth == 3 ) {
		$destid = $dest->term_id;
?>
	$('#book-destination').val('<?php echo addslashes(html_entity_decode($dest->name)); ?>');
	$('.autocompletedestination').val('<?php echo $destid; ?>,destination');

<?php
	} else if ( is_archive() && get_query_var('post_type') == 'hotel' && $depth == 1 ) {
		$regionid = $reg->term_id;
?>
	$('#book-destination').val('<?php echo addslashes(html_entity_decode($reg->name)); ?>');
	$('.autocompletedestination').val('<?php echo $regionid; ?>,destination');

<?php
	} else if (
		is_archive() && get_query_var('post_type') == 'hotel' && $depth == 2
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
	) {
		$destid = $dest->term_id;
?>
	$('#book-destination').val('<?php echo addslashes(html_entity_decode($dest->name)); ?>');
	$('.autocompletedestination').val('<?php echo $destid; ?>,destination');

	var bookingdestfield = $('input#book-destination').val();

<?php
	}
?>

});
</script>

<?php

	// ad for destination top level | hotel | restaurant | shop | activity | itinerary | library
	if (
		( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
	) {

		$imageobj = get_field('banner-image', 'destinations' . '_' . $dest->term_id);
		$imgsrc = $imageobj['url'];
		$bannerurl = get_field('banner-url', 'destinations' . '_' . $dest->term_id);
		$bannerstart = get_field('banner-start', 'destinations' . '_' . $dest->term_id);
		$bannerend = get_field('banner-end', 'destinations' . '_' . $dest->term_id);

		if ( ! empty( $_GET['_y'] ) )
			$year = absint( $_GET['_y'] );
		else
			$year = trim( date( 'Y ') );

		if ( ! empty( $_GET['_m'] ) && in_array( $month = absint( $_GET['_m'] ), range( 1, 12 ) ) )
			$month = zeroise( $month, 2 );
		else
			$month = date( 'm' );

		if ( ! empty( $_GET['_d'] ) && in_array( $day = absint( $_GET['_m'] ), range( 1, 31 ) ) )
			$day = zeroise( $day, 2 );
		else
			$day = date( 'd' );

		$fulldate = $year.$month.$day;

		if ( $imageobj && ( $bannerstart <= $fulldate || is_null( $bannerstart ) || $bannerstart === false || $bannerstart == '' ) && ( $bannerend >= $fulldate || is_null( $bannerend ) || $bannerend === false || $bannerend == '' ) ) {

			if ( $bannerurl ) {
				echo '<div class="bannerad"><a href="'.$bannerurl.'" target="_blank"><img src="'.$imgsrc.'" /></a></div>';
			} else {
				echo '<div class="bannerad"><img src="'.$imgsrc.'" /></div>';
			}

		}


	}

			echo '<a class="contact lightbox-inline" href="#lightbox-contact-team"><img src="'.get_bloginfo('stylesheet_directory').'/images/contact-phone.png" /></a>'."\n";

			// sidebar newsletter signup

			echo '<div id="form-buzz" class="newsletter-signup-wrapper">'."\n";
				echo '<h2>The Buzz</h2>'."\n";
				echo '<p>Subscribe to our free e-Newsletter for current travel news and tips.</p>'."\n";
				echo '<form class="newsletter-signup-form" method="POST" action="'.get_bloginfo('stylesheet_directory').'/includes/email-signup.php">'."\n";
				  echo '<div class="form-combo">'."\n";
					echo '<span class="form-item"><input type="text" name="email" id="newsletter-signup-buzz" class="element newsletter-signup-input" placeholder="Your email address" /></span>'."\n";
				  echo '</div>'."\n";
				  echo '<div class="buttons">'."\n";
					echo '<button type="submit" class="primary button">Submit</button>'."\n";
				  echo '</div>'."\n";
				echo '</form>'."\n";
			echo '</div>'."\n";


		echo '</div>'."\n";
		// end primary

		// sidebar offers | plus | adored content - not on itinerary or library
		if (
			( is_archive() && get_query_var('post_type') == 'itinerary' )
			|| ( is_archive() && get_query_var('post_type') == 'library' )
		) { } else {

			// secondary
			echo '<div id="secondary">'."\n";
				echo '<section class="aside">'."\n";
					echo '<article>'."\n";
						echo '<h2 class="icon custom-icon" data-icon="&#xe009;" id="ind-plus">Indagare Plus</h2>'."\n";
						$plus = get_field('indagare-plus', 'option');
						if ( $plus ) {
							echo $plus;
						}
					echo '</article>'."\n";
					echo '<article>'."\n";
						echo '<h2 class="icon custom-icon" data-icon="&#xe00a;" id="ind-picks">Indagare Picks</h2>'."\n";
						$picks = get_field('indagare-picks', 'option');
						if ( $picks ) {
							echo $picks;
						}
					echo '</article>'."\n";
					echo '<article>'."\n";
						echo '<h2 class="icon custom-icon" data-icon="&#xe00b;" id="ind-adored">Indagare Adored</h2>'."\n";
						$adored = get_field('indagare-adored', 'option');
						if ( $adored ) {
							echo $adored;
						}
					echo '</article>'."\n";
					echo '<article>'."\n";
						echo '<a href="/destinations/offers/"><h2 class="icon custom-icon" data-icon="&#xe600;" id="ind-offers">Indagare Partner Promotions</h2></a>'."\n";
						$offers = get_field('indagare-special-offers', 'option');
						if ( $offers ) {
							echo $offers;
						}
					echo '</article>'."\n";

					// insidertrip sidebar display for destinatation top level
					if ( ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') ) ) {

						$args = array('numberposts' => -1, 'post_type' => 'insidertrip', 'destinations' => $reg->slug, 'meta_key' => 'date-start', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'meta_query' => array(
										array(
											'key' => 'trip-state',
											'value' => 'current'
										),
									));

						$insidertriprelated = new WP_Query($args);

						if($insidertriprelated->have_posts() ) {

							echo '<article class="custom contain">'."\n";
								echo '<h2>Insider Trip</h2>'."\n";
								echo '<p>Immersive, small-group journeys designed by Indagare founder Melissa Biggs Bradley to some of the world&rsquo;s inspiring destinations.</p>'."\n";

								while ( $insidertriprelated->have_posts() ) : $insidertriprelated->the_post();

									// generate thumbnail from gallery header, if not, use featured image
//									$rows = get_field('gallery-header');

									$rowsraw = get_field('gallery-header', false, false);

									if ( $rowsraw ) {
										$imageid = $rowsraw[0];
										$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-small' );
										$imgsrc = $imageobj[0];
									}

									echo '<div class="contain">'."\n";
									echo '<p class="thumbnail"><a href="'.get_permalink().'"><img src="'.$imgsrc.'" alt="insider trip" /></a></p>'."\n";
									echo '<p class="link"><a href="'.get_permalink().'">See the insider trip for '.get_the_title().'</a></p>'."\n";
									echo '</div>'."\n";

								endwhile;

							echo '</article>'."\n";
						}
					}

				echo '</section>'."\n";
			echo '</div>'."\n";
			// end secondary

		}
		// end sidebar offers | plus | adored content

	} // end sidebar for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library

	// sidebar for insidertrip archive
	if ( is_archive() && get_query_var('post_type') == 'insidertrip' ) {

		// secondary
		echo '<div id="secondary">'."\n";
			echo '<div class="widget">'."\n";
				echo '<blockquote><span class="openclose">&#8220;</span>'.get_field('insidertrip-quote','option').'<span class="openclose">&#8221;</span></blockquote>'."\n";
				echo '<cite> ~ '.get_field('insidertrip-citation','option').'</cite>'."\n";
			echo '</div><!-- .widget -->'."\n";

			echo '<a class="contact lightbox-inline" href="#lightbox-contact-insidertrip"><img src="'.get_bloginfo('stylesheet_directory').'/images/contact.png" /></a>'."\n";

		echo '</div><!-- #primary -->'."\n";

	} // end sidebar for insidertrip archive

	// sidebar for insidertrip
	if ( is_singular( 'insidertrip' ) ) {

		$rows = get_field('recommended-trip');

		if ( $rows ) {

			// primary
			echo '<div id="primary">'."\n";
				echo '<div class="widget">'."\n";
					echo '<h3>Also Recommended</h3>'."\n";
					echo '<ul>'."\n";

					foreach ( $rows as $trip ) {

//						$images = get_field('gallery-header',$trip);

						$imagesraw = get_field('gallery-header', $trip, false);

/*
						if ( $images ) {
							$imageobj = $images[0];
							$imgsrc = $imageobj['sizes']['thumb-large'];
						}
*/

						if ( $imagesraw ) {
							$imageid = $imagesraw[0];
							$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-large' );
							$imgsrc = $imageobj[0];
						}


						echo '<li class="trip">'."\n";
							if ( $imgsrc ) {
//								echo '<a href="'.get_permalink($row['recommended-trip-post'][0]).'"><img src="'.$imgsrc.'" alt="" /></a>'."\n";
								echo '<a href="'.get_permalink($trip).'"><img src="'.$imgsrc.'" alt="" /></a>'."\n";
							}
//							echo '<h4><a href="'.get_permalink($row['recommended-trip-post'][0]).'">'.get_the_title($row['recommended-trip-post'][0]).'</a></h4>'."\n";
							echo '<h4><a href="'.get_permalink($trip).'">'.get_the_title($trip).'</a></h4>'."\n";
							echo '<p class="tagline">'.get_field('subtitle',$trip).'</p>'."\n";
						echo '</li><!-- .hotel -->'."\n";

					}

					echo '</ul>'."\n";
				echo '</div><!-- .widget -->'."\n";
			echo '</div><!-- #primary -->'."\n";

		}

	} // end sidebar for insidertrip

	// sidebar for author page
	if ( is_author() ) {

		$author = get_queried_object();

		$user = get_user_by('id',$author->ID);
		$userid = 'user_'.$user->ID;

		$imageobj = get_field('author-image', $userid);
		$image = $imageobj['sizes']['large'];

		if ($image) {
			echo '<div id="primary">'."\n";

				echo '<div class="widget">'."\n";
					echo '<ul class="rslides">'."\n";
						echo '<li>'."\n";
							echo '<img src="'.$image.'" alt="">'."\n";
						echo '</li>'."\n";
					echo '</ul><!--.hero.rslides-->'."\n";
				echo '</div><!-- .widget -->'."\n";

			echo '</div><!-- #primary -->'."\n";
		}

		$rowsscout = get_field('author-currently-scouting', $userid);
		$rows = get_field('author-recently-visited', $userid);

		if ( $rowsscout ) {

			echo '<section class="recent-articles contain">'."\n";
				echo '<div class="header divider"><h2>';
				echo sprintf( __( '%s is Currently Scouting', 'indagare'), $user->first_name );
				echo '</h2></div>'."\n";
				foreach ( $rowsscout as $row ) {
					$imageobj = $row['author-currently-scouting-image'];
					$image = $imageobj['sizes']['thumb-small'];

					echo '<article>'."\n";
						echo '<a href="'.$row['author-currently-scouting-url'].'">'."\n";
							if ( $image ) {
								echo '<img src="'.$image.'" alt="'.__('Related','indagare').'" />'."\n";
							} else {
								echo '<img src="'.get_bloginfo('stylesheet_directory').'/images/blank-thumb-small-logo.png" alt="'.__('Related','indagare').'" />'."\n";
							}
							echo '<h3>'.$row['author-currently-scouting-title'].'</h3>'."\n";
						echo '</a>'."\n";
					echo '</article>'."\n";
				}
			echo '</section><!-- .recent-articles -->'."\n";

		}

		if ( $rows ) {

			echo '<section class="recent-articles contain">'."\n";
				echo '<div class="header divider"><h2>';
				echo sprintf( __( '%s Recently Visited', 'indagare' ), $user->first_name );
				echo '</h2></div>'."\n";
				foreach ( $rows as $row ) {
					$imageobj = $row['author-recently-visited-image'];
					$image = $imageobj['sizes']['thumb-small'];

					echo '<article>'."\n";
						echo '<a href="'.$row['author-recently-visited-url'].'">'."\n";
							if ( $image ) {
								echo '<img src="'.$image.'" alt="'.__('Related','indagare').'" />'."\n";
							} else {
								echo '<img src="'.get_bloginfo('stylesheet_directory').'/images/blank-thumb-small-logo.png" alt="'.__('Related','indagare').'" />'."\n";
							}
							echo '<h3>'.$row['author-recently-visited-title'].'</h3>'."\n";
						echo '</a>'."\n";
					echo '</article>'."\n";
				}
			echo '</section><!-- .recent-articles -->'."\n";

		}

	} // end sidebar for author page

	// sidebar for founder page
	if ( is_page_template('template-page-about-founder.php') ) {
		global $post;

		$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );
		$image = $imageobj[0];

		if ($image) {
			echo '<div id="primary">'."\n";

				echo '<div class="widget">'."\n";
					echo '<ul class="rslides">'."\n";
						echo '<li>'."\n";
							echo '<img src="'.$image.'" alt="">'."\n";
						echo '</li>'."\n";
					echo '</ul><!--.hero.rslides-->'."\n";
				echo '</div><!-- .widget -->'."\n";

			echo '</div><!-- #primary -->'."\n";
		}

	    $rowsscout = get_field('currently-scouting');
	    $rows = get_field('recently-visited');
	    $foundername = __("Melissa", 'indagare');

	    if ( $rowsscout ) {

	        echo '<section class="recent-articles contain">'."\n";
				echo '<div class="header divider"><h2>';
				echo sprintf( __( '%s is Currently Scouting', 'indagare'), $foundername );
				echo '</h2></div>'."\n";
	            foreach ( $rowsscout as $row ) {
	                $imageobj = $row['currently-scouting-image'];
	                $image = $imageobj['sizes']['thumb-small'];

	                echo '<article>'."\n";
	                    echo '<a href="'.$row['currently-scouting-url'].'">'."\n";
	                        if ( $image ) {
	                            echo '<img src="'.$image.'" alt="'.__('Related','indagare').'" />'."\n";
	                        } else {
	                            echo '<img src="'.get_bloginfo('stylesheet_directory').'/images/blank-thumb-small-logo.png" alt="'.__('Related','indagare').'" />'."\n";
	                        }
	                        echo '<h3>'.$row['currently-scouting-title'].'</h3>'."\n";
	                    echo '</a>'."\n";
	                echo '</article>'."\n";
	            }
	        echo '</section><!-- .recent-articles -->'."\n";

	    }

	    if ( $rows ) {

	        echo '<section class="recent-articles contain">'."\n";
				echo '<div class="header divider"><h2>';
				echo sprintf( __( '%s Recently Visited', 'indagare'), $foundername );
				echo '</h2></div>'."\n";
	            foreach ( $rows as $row ) {
	                $imageobj = $row['recently-visited-image'];
	                $image = $imageobj['sizes']['thumb-small'];

	                echo '<article>'."\n";
	                    echo '<a href="'.$row['recently-visited-url'].'">'."\n";
	                        echo '<img src="'.$image.'" alt="'.__('Related','indagare').'" />'."\n";
	                        echo '<h3>'.$row['recently-visited-title'].'</h3>'."\n";
	                    echo '</a>'."\n";
	                echo '</article>'."\n";
	            }
	        echo '</section><!-- .recent-articles -->'."\n";

	    }

	} // end sidebar for founder page

	// sidebar for how we work page
	if ( is_page_template('template-page-how-we-work.php') ) {

/*
		echo '<div id="primary">'."\n";

			echo '<a class="contact" href="/why-join/"><img src="'.get_bloginfo('stylesheet_directory').'/images/contact-how-we-work.png"></a>'."\n";

			$rows = get_field('sidebar-content');

			if ( $rows ) {

				foreach ( $rows as $row ) {

					$imageobj = $row['sidebar-image'];
					$image = $imageobj['url'];
					$title = $row['sidebar-title'];
					$text = $row['sidebar-text'];
					$link = $row['sidebar-link'];

					echo '<div class="item">'."\n";
						if ( $image ) {
							echo '<img src="'.$image.'" />'."\n";
						}
						if ( $link ) {
							echo '<a href="'.$link.'" target="_blank"><h3>'.$title.'</h3></a>'."\n";
						} else {
							echo '<h3>'.$title.'</h3>'."\n";
						}
						echo $text;
					echo '</div>'."\n";

				}

			}

		echo '</div><!-- #primary -->'."\n";

*/

	} // end sidebar for how we work page

	// sidebar for article
	if (
		is_singular( 'article' )
	) {

		echo '<div id="primary" class="magazine">'."\n";

			$column = wp_get_post_terms( $post->ID, 'column' );
			$interests = wp_get_post_terms( $post->ID, 'interest' );


			echo '<div class="widget">'."\n";
				echo '<h3>Column</h3>'."\n";
				echo '<ul>'."\n";
					echo '<li><a href="/destinations/articles/?column='.$column[0]->slug.'">'.$column[0]->name.'</a></li>'."\n";
				echo '</ul>'."\n";
			echo '</div><!-- .widget -->'."\n";

			if ( $interests ) {
				echo '<div class="widget">'."\n";
					echo '<h3>Interest</h3>'."\n";
					echo '<ul>'."\n";
					foreach ( $interests as $term ) {
						echo '<li><a href="/destinations/articles/?interest='.$term->slug.'">'.$term->name.'</a></li>'."\n";
					}
					echo '</ul>'."\n";
				echo '</div><!-- .widget -->'."\n";
			}

			if ( $dest ) {
				echo '<div class="widget">'."\n";
					echo '<h3>Destination</h3>'."\n";
					echo '<ul>'."\n";
//						echo '<li><a href="/destinations/articles/?destinations='.$dest->slug.'">'.$dest->name.'</a></li>'."\n";
						echo '<li><a href="/search/?s='.$dest->name.'">'.$dest->name.'</a></li>'."\n";
					echo '</ul>'."\n";
				echo '</div><!-- .widget -->'."\n";
			}

			$quote = get_field('article-quote');
			$citation = get_field('article-citation');

			if ( $quote ) {
				echo '<div class="widget">'."\n";
					echo '<h3>Quotable</h3>'."\n";
					echo '<blockquote><span class="openclose">&#8220;</span>'.$quote.'<span class="openclose">&#8221;</span></blockquote>'."\n";
					if ( $citation ) {
						echo '<cite> ~ '.$citation.'</cite>'."\n";
					}
				echo '</div><!-- .widget -->'."\n";
			}

			// related hotels
			$rows = get_field('related-hotels');

			if ($rows) {
				echo '<div class="widget">'."\n";

					echo '<h3>Related Hotels</h3>'."\n";
					echo '<ul>'."\n";

						foreach ( $rows as $hotel ) {

//							$images = get_field('gallery-header',$hotel);

							$imagesraw = get_field('gallery-header',$hotel,false);

							if ( $imagesraw ) {
								$imageid = $imagesraw[0];
								$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-large' );
								$imgsrc = $imageobj[0];
							}

							echo '<li class="hotel">'."\n";
								echo '<a href="'.get_permalink($hotel).'"><img src="'.$imgsrc.'" alt="" /></a>'."\n";
								echo '<h4><a href="'.get_permalink($hotel).'">'.get_the_title($hotel).'</a></h4>'."\n";
								echo '<span class="caption">'.get_field('subtitle',$hotel).'</span>'."\n";
							echo '</li><!-- .hotel -->'."\n";

						}

					echo '</ul>'."\n";
				echo '</div><!-- .widget -->'."\n";
			}

			echo '<a class="contact" href="/why-join/"><img src="'.get_bloginfo('stylesheet_directory').'/images/contact-magazine.png"></a>'."\n";

		echo '</div><!-- #primary -->'."\n";

	} // end sidebar for article

	// related destinations for destination
	if ( $dest && $depth == 2 ) {

		$related = get_field('related', 'destinations' . '_' . $dest->term_id);
		if(!empty($related)) {
			foreach($related as $i) {
				$itm = array('name' => $i->name, 'value' => $i->slug, 'term_id' => $i->term_id);

				$imageobj = _get_firstimage('header-image', 'thumb-large', SHR_FIRSTIMAGE_ALL, false, 'destinations' . '_' . $i->term_id);
				$image = $imageobj['src'];
				if ( $image ) {
					$itm['img'] = $image;
				}

				$relatedlist[] = $itm;
			}
		}

		if(!empty($relatedlist)) {

			$relatedlist = sortArray( $relatedlist, 'name' );

	  		echo '<div class="header divider"><h2>Beyond&hellip; '.$dest->name.'</h2><p>Consider combining your trip with one of these destinations.</p></div>'."\n";

			echo '<section class="related-articles related-destinations contain">'."\n";

	  		foreach ( $relatedlist as $relateditem ) {

	  			$relateditemtree = destinationstaxtree($relateditem['term_id']);
	  			$relateddest = $relateditemtree['dest'];
	  			$relatedreg = $relateditemtree['reg'];
	  			$relatedtop = $relateditemtree['top'];

				echo '<article>'."\n";
					echo '<a href="/destinations/'.$relatedtop->slug .'/'. $relatedreg->slug .'/'. $relateddest->slug.'/">'."\n";
						if ( $relateditem['img'] ) {
							echo '<img src="'.$relateditem['img'].'" alt="Related" />'."\n";
						}
						echo '<h3>'.$relateditem['name'].'</h3>'."\n";
					echo '</a>'."\n";
				echo '</article>'."\n";

	  		}

	  		echo '</section>'."\n";

		}

	}
	// end related destinations for destination

	// related articles for region | destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | welcome | 404
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| ( is_archive() && $reg && $depth == 1 && !get_query_var('post_type') )
		|| is_page_template ( 'template-page-welcome.php' )
		|| is_404()
	) {

		// recent or related articles
		if ( is_page_template ( 'template-page-welcome.php' ) ) {
			$args = array('numberposts' => -1, 'post_type' => 'article', 'orderby' => 'date', 'order' => 'DESC');
		} else if ( $reg && $depth == 1 ) {
			$args = array('numberposts' => -1, 'post_type' => 'article', 'destinations' => $reg->slug, 'meta_key' => 'related-article', 'meta_value' => 'yes', 'orderby' => 'rand');
		} else {
			$args = array('numberposts' => -1, 'post_type' => 'article', 'destinations' => $dest->slug, 'meta_key' => 'related-article', 'meta_value' => 'yes', 'orderby' => 'rand');
		}

		$related = new WP_Query($args);

//		echo $related->found_posts;

		if($related->have_posts() ) {

			$i = 0;

			if ( is_page_template ( 'template-page-welcome.php' ) || is_404() ) {
		  		echo '<div class="header divider"><h2>Recent Articles</h2><p class="view-more"><a href="/destinations/articles/">View All Articles</a></p></div>'."\n";
			} else if ( $reg && $depth == 1 ) {
//		  		echo '<div class="header divider"><h2>Related Articles</h2><p class="view-more"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/articles/">View All Related Articles</a></p></div>'."\n";
		  		echo '<div class="header divider"><h2>Related Articles</h2><p class="view-more"><a href="/destinations/articles/?destinations='.$reg->slug .'">View All Related Articles</a></p></div>'."\n";
	  		} else {
//		  		echo '<div class="header divider"><h2>Related Articles</h2><p class="view-more"><a href="/destinations/'.$top->slug .'/'. $reg->slug .'/'. $dest->slug.'/articles/">View All Related Articles</a></p></div>'."\n";
		  		echo '<div class="header divider"><h2>Related Articles</h2><p class="view-more"><a href="/destinations/articles/?destinations='.$dest->slug .'">View All Related Articles</a></p></div>'."\n";
	  		}

			echo '<section class="related-articles contain">'."\n";

				while ( $related->have_posts() ) : $related->the_post();

					if ( $i < 4 ) {

//						$rows = get_field('gallery-header');

						$imgsrc = _get_firstimage( 'gallery-header', 'thumb-medium', SHR_FIRSTIMAGE_ALL, false );
						$imgsrc = str_replace( '620x413', '300x200', $imgsrc['src'] );
/*
						$rowsraw = get_field('gallery-header', false, false);

						if ( $rowsraw ) {
							$imageid = $rowsraw[0];
							$imageobj = wp_get_attachment_image_src( $imageid, 'thumb-medium' );
							$imgsrc = $imageobj[0];
						} else if ( catch_that_image($value) ) {
							$imgsrc = catch_that_image($value);
							$imgsrc = str_replace('620x413', '300x200', $imgsrc);
						} else {
							$imageobj = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumb-medium' );
							$imgsrc = $imageobj[0];
						}
*/
						$text = strip_shortcodes( $post->post_content );
						$text = str_replace(']]>', ']]>', $text);
						$excerpt_length = 15; // 15 words
						$excerpt_more = apply_filters('excerpt_more', '...');
						$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

						echo '<article>'."\n";
							echo '<a href="'.get_permalink($post->ID).'">'."\n";
								if ( $imgsrc ) {
									echo '<img src="'.$imgsrc.'" alt="Related" />'."\n";
								}
								echo '<h3>'.$post->post_title.'</h3>'."\n";
									echo '<p class="description">'.$text.'</p>'."\n";
							echo '</a>'."\n";
						echo '</article>'."\n";

						$i++;

					}

				endwhile;

			echo '</section>'."\n";

        }

        wp_reset_postdata();
		// end related articles

	} // end related aritcles for region | destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library

	// recently viewed for region | destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer | insidertrip | welcome | book
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular('offer') || is_singular('insidertrip')
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && get_query_var('post_type') == 'insidertrip' )
		|| ( is_archive() && get_query_var('post_type') == 'offer' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| ( is_archive() && $reg && $depth == 1 && !get_query_var('post_type') )
		|| is_page_template ( 'template-page-welcome.php' )
		|| is_page_template ( 'template-page-book.php' )
	) {

		// recently viewed
		if (function_exists('zg_recently_viewed') && !is_page_template ( 'template-page-welcome.php' )):  if (isset($_COOKIE["WP-LastViewedPosts"])) {
			echo '<div class="header divider"><h2>Recently Viewed</h2></div>'."\n";
			echo '<section class="recent-articles contain">'."\n";

			zg_recently_viewed();

			echo '</section>'."\n";
		 } else {
			echo '<div class="header divider"></div>'."\n";
		 } endif;
		 // end recently viewed

	} // end recently viewed for region | destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer

	//  join for region | destination archive | post or archive for hotel restaurant shop activity itinerary library article offer press | home | why join | new | search | 404
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' )
		|| is_singular('article') || is_singular('offer')
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && get_query_var('post_type') == 'article' )
		|| ( is_archive() && get_query_var('post_type') == 'magazine' )
		|| ( is_archive() && get_query_var('post_type') == 'press' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| ( is_archive() && $reg && $depth == 1 )
		|| ( is_page_template( 'template-page-map.php' ))
		|| ( is_page_template( 'template-page-new.php' ))
		|| is_home() || is_front_page()
		|| is_search()
		|| is_author() || ( is_page_template( 'template-page-about-team.php' ) || ( is_page_template( 'template-page-about-mission.php' ) ) || ( is_page_template( 'template-page-about-founder.php' ) ) )
		|| is_page_template( 'template-page-why-join.php')
		|| is_404()
	 ) {

		// is user logged in
		if ( ! ind_logged_in() ) {

			echo '<div id="join-today" class="contain">'."\n";
				echo '<div class="join-indagare">'."\n";
					echo '<p class="action">Become an Indagare Member Today</p>'."\n";
					echo '<p class="action-button"><a href="/join/" class="button">Join</a> <a href="#lightbox-login" class="lightbox-inline">or sign in</a></p>'."\n";
				echo '</div>'."\n";
			echo '</div>'."\n";

		}

	}
	// end join for region | destination archive | post or archive for hotel restaurant shop activity itinerary library

	// home page | !why join page
//	if (is_home() || is_front_page() || is_page_template ( 'template-page-why-join.php' ) ) {
	if (is_home() || is_front_page() ) {
		echo '<span class="dictionary">Indagare <span class="gray">(in&bull;da&bull;ga&bull;re) <em>verb (latin).</span> To discover, explore, seek, scout.</em></span>'."\n";
	} // end why join page


}
add_filter('thematic_belowcontainer','child_belowcontainer');

// footer content
function childtheme_override_siteinfoopen() {}
function childtheme_override_siteinfo() {
?>
  <div class="candy-wrapper">
    <section id="who-we-are">
<?php
	$whoweare = get_field('footer-who-we-are', 'option');

	if ( $whoweare ) {

		echo $whoweare;

	}
?>
    </section>
    <section id="subsidiary">
      <div id="first" class="newsletter-signup-wrapper">
        <h4><?php print __('Newsletter','indagare'); ?></h4>
        <p><?php print __('Receive our free email newsletter full of travel news, tips and advice.','indagare'); ?></p>
        <form class="newsletter-signup-form" method="POST" action="<?php echo get_bloginfo('stylesheet_directory'); ?>/includes/email-signup.php">
          <div class="form-combo">
          	<span class="form-item"><input type="text" name="email" id="newsletter-signup" class="element newsletter-signup-input" placeholder="<?php print __('Your email address','indagare'); ?>" /></span>
          </div>
          <div class="form-combo">
          	<span class="form-item"><input type="text" name="fname" class="element newsletter-signup-fname" placeholder="<?php print __('First name (optional)','indagare'); ?>" /></span>
          	<span class="form-item"><input type="text" name="lname" class="element newsletter-signup-lname" placeholder="<?php print __('Last name (optional)','indagare'); ?>" /></span>
          </div>
          <div class="buttons">
          	<button type="submit" class="primary button"><?php print __('Submit','indagare'); ?></button>
          </div>
        </form>
      </div>
      <div id="fourth">
        <h4><a class="colheader" href="/contact/"><?php print __('Connect','indagare'); ?></a></h4>
        <p class="vcard">
          <span class="adr">
            <span class="street-address">950 Third Avenue </span>
            <span class="locality">New York</span>, <span class="region">NY</span> <span class="postal-code">10022</span>
          </span>
          <span class="tel">(212) 988-2611</span>
          <a class="email" href="mailto:info@indagare.com"><?php print __('Email Us','indagare'); ?></a>
        </p>
        <p class="social">
          <a id="social-facebook" href="https://www.facebook.com/pages/Indagare-Travel/38863077107"><b class="icon custom-icon" data-icon="&#xe003;"><span><?php print __('facebook','indagare'); ?></span></b></a> <a href="https://twitter.com/indagaretravel" id="social-twitter"><b class="icon custom-icon" data-icon="&#xe001;"><span><?php print __('twitter','indagare'); ?></span></b></a> <a id="social-instagram" href="http://instagram.com/indagaretravel/"><b class="icon custom-icon" data-icon="&#xe618;"><span><?php print __('instagram','indagare'); ?></span></b></a>
        </p>
      </div>
      <div id="second">
        <h4><a class="colheader" href="/why-join/"><?php print __('Membership','indagare'); ?></a></h4>
<?php
	$footermembership = wp_nav_menu( array('menu' => 'footer-membership','container' => '','container_id' => '','container_class' => '','menu_class' => 'footer-nav','echo' => false ));
	echo $footermembership;
?>
      </div>
      <div id="third">
        <h4><a class="colheader" href="/mission/"><?php print __('About','indagare'); ?></a></h4>
<?php
	$footerabout = wp_nav_menu( array('menu' => 'footer-about','container' => '','container_id' => '','container_class' => '','menu_class' => 'footer-nav','echo' => false ));
	echo $footerabout;
?>
      </div>
    </section>
    <section id="siteinfo">
      <p>&copy; 2007 - <?php echo Date('Y'); ?> Indagare Travel, Inc. All rights reserved. Use of this site constitutes acceptance of our <a href="/terms-of-use/">Terms of Use</a> and <a href="/privacy-policy/">Privacy Policy</a>.</p>
    </section>
  </div>
<?php
}
function childtheme_override_siteinfoclose() {}



function child_after() {
global $post;

// use global $count for item total
global $count;

	if ( is_archive() ) {
		$destinationstree = destinationstaxtree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
		$depth = $destinationstree['depth'];
	}

	if ( is_singular() ) {
		$destinationstree = destinationstree();
		$dest = $destinationstree['dest'];
		$reg = $destinationstree['reg'];
		$top = $destinationstree['top'];
	}

	// first visit modal - intro page on home page only
	if ( indagare\cookies\FirstVisit::isFirstVisit() ) {

//		include_once 'includes/first-visit.php';

//		first_visit();

		if ( is_home() || is_front_page() ) {

			header('Location: /intro/');

		}

	} // end first visit modal - intro page

	// email signup modal
    if ( ind_show_email_popup() ) {
?>
<div id="lightbox-email-signup" class="lightbox lightbox-two-col lightbox-no-borders white-popup mfp-hide">
	<header>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/modal-signup.jpg" alt="" />
		<h2>Welcome!</h2>
		<p>Please share your email to continue reading some of Indagare's latest travel stories and destination guides.</p>
	</header>

	<footer>
		<div id="emailsignup" class="newsletter-signup-wrapper">
		<form class="login newsletter-signup-form" method="POST" action="<?php echo get_bloginfo('stylesheet_directory'); ?>/includes/email-signup.php" novalidate>
			<div id="field1-container" class="field clearfix">
			   <div class="column one-third"><input type="text" name="email" id="field1" class="newsletter-signup-input" required="required" placeholder="Your email address"></div>
			   <div class="column one-third"><input type="text" name="fname" class="newsletter-signup-fname" placeholder="First name (optional)"></div>
			   <div class="column one-third"><input type="text" name="lname" class="newsletter-signup-lname" placeholder="Last name (optional)"></div>
			</div>

			<div id="form-submit" class="clearfix submit">
			   <input type="submit" value="Submit" class="button">
			</div>
		</form>
		<p>You will receive our free e-Newsletter full of travel news and insider recommendations.</p>
		</div>
		<p>By clicking "Submit," you accept our <a href="/terms-of-use/">Terms of Use</a> and <a href="/privacy-policy/">Privacy Policy</a>.</p>

		<h3><strong>Member Login</strong></h3>
		<p>Already a member? Please <a href="#lightbox-login" class="lightbox-inline">log in</a>.</p>
	</footer>

</div><!-- #lightbox-email-signup -->
<script>
jQuery(document).ready(function($) {

	$.magnificPopup.open({
	  items: {
		type: 'inline',
		src: '#lightbox-email-signup', // can be a HTML string, jQuery object, or CSS selector
		midClick: true
	  },
//	  modal: true
	});

});
</script>
<?php
	} // endemail signup modal

	// login modal
	if ( ! ind_logged_in() ) {
?>
<div id="lightbox-login" class="lightbox white-popup login mfp-hide">
	<header>
		<h2>Member Login</h2>
	</header>

	<form id="form-login" class="login ajax-login" method="post" novalidate<?php
	if (is_page_template ( 'template-page-intro.php' ) ) {
		print ' data-successurl="/"';
	}
	?>>
		<div id="field1-container" class="field">
			<label for="field1">Username</label>
			<input type="text" name="username" id="field1" required="required" placeholder="Your username">
		</div>

		<div id="field2-container" class="field">
			<label for="field2">Password</label>
			<input type="password" name="password" id="field2" required="required" value="" placeholder="Your password">
		</div>

		<div id="form-submit" class="field clearfix submit">
			<label for=""></label>
		   <input type="submit" value="Submit" class="button primary">
		   <a id="forgot" href="/wp-login.php?action=lostpassword" class="button secondary">Forgot Password</a>
		</div>

		<div class="field message">
		</div>

        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
	</form>

</div><!-- #lightbox-login -->

<?php
	} // end login modal

	// content lockout modal
	if ( ! ind_logged_in() ) {
?>
<div id="lightbox-join" class="lightbox white-popup mfp-hide">
	<header>
		<h2>Sorry!</h2>
		<p>You've exceeded the amount of content available to non-paying members. We would love to have you as part of our community.
		If you're not ready to join, <a href="/">return to our hotel reviews</a> &#8212; these are available to everyone.</p>
	</header>
	<div class="column one-third first">
		<h3>Why Join?</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/join-1.jpg" alt="" />
		<ul>
			<li>To enjoy unlimited access to the online content and our Black Book magazines.</li>
			<li>To receive special rates &amp; amenities at hundreds of hotels and resorts worldwide.</li>
			<li>To benefit from customized trip planning from our expert team.</li>
			<li>To gain access to Insider Trips, events and more.</li>
		</ul>
		<a href="/why-join/" class="button">Learn More</a>
	</div><!-- .column -->

	<div class="column one-third">
		<h3>Join Now</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/join-2.jpg" alt="" />
		<ul>
			<li>Four levels of membership designed for everyone from the leisure traveler to the corporate client.</li>
			<li>Savings on just one hotel booking usually surpass the cost of a basic membership thanks to special rates.</li>
			<li>Skip the application and become a member now by using the Referral Code: <strong>IndagareTravel</strong>.</li>
		</ul>
		<a href="/join/?referralcode=IndagareTravel" class="button">Join Now</a>
	</div><!-- .column -->

	<div class="column one-third last">
		<h3>Already a Member?</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/join-3.jpg" alt="" />

		<form id="form-login" class="login ajax-login" method="post" novalidate>
			<div id="field1-container" class="field">
			   <label for="field1">
					Username or Email
			   </label>
			   <input type="text" name="username" id="field1" required="required" placeholder="Your username">
			</div>

			<div id="field2-container" class="field">
			   <label for="field2">
					Password
			   </label>
			   <input type="password" name="password" id="field2" required="required" placeholder="Your password">
			</div>

			<div id="form-submit" class="field clearfix submit">
			   <input type="submit" value="Login" class="button">
			   <a id="forgot" href="/wp-login.php?action=lostpassword" class="button secondary">Forgot Password</a>
			</div>

			<div class="field message">
			</div>

        	<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
   		</form>
	</div><!-- .column -->
	<footer id="emailsignup" class="newsletter-signup-wrapper">
		<h4>Sign Up: Travel Newsletter</h4>
		<p>Receive our free, bimonthly e-Newsletter full of travel stories, reviews and insider recommendations.</p>
		<form class="login newsletter-signup-form" method="POST" action="<?php echo get_bloginfo('stylesheet_directory'); ?>/includes/email-signup.php" novalidate>
			<div id="field1-container" class="field clearfix">
			   <div class="column one-third"><input type="text" name="email" id="field1" class="newsletter-signup-input" required="required" placeholder="Your email address"></div>
			   <div class="column one-third"><input type="text" name="fname" class="newsletter-signup-fname" placeholder="First name (optional)"></div>
			   <div class="column one-third"><input type="text" name="lname" class="newsletter-signup-lname" placeholder="Last name (optional)"></div>
			</div>

			<div id="form-submit" class="clearfix submit">
			   <input type="submit" value="Submit" class="button">
			</div>
		</form>

	</footer>
</div><!-- #lightbox-join -->
<?php
	} else if ( ! user_has_permission() ) {
		// User is logged in but still has no permission. This dialog should prompt them to upgrade or renew their membership
?>
<div id="lightbox-join" class="lightbox white-popup mfp-hide">
	<header>
		<h2>Sorry!</h2>
		<p>You've exceeded the amount of content available to non-paying members. We would love to have you as part of our community.
		If you're not ready to renew your membership, <a href="/">return to our hotel reviews</a> &#8212; these are available to everyone.</p>
	</header>
	<div class="column one-half first">
		<h3>Why Renew?</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/join-1.jpg" alt="" />
		<ul>
			<li>To enjoy unlimited access to the online content and our Black Book magazines.</li>
			<li>To receive special rates &amp; amenities at hundreds of hotels and resorts worldwide.</li>
			<li>To benefit from customized trip planning from our expert team.</li>
			<li>To gain access to Insider Trips, events and more.</li>
		</ul>
		<a href="/why-join/" class="button">Learn More</a>
	</div><!-- .column -->

	<div class="column one-half">
		<h3>Renew or Upgrade Now</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/join-2.jpg" alt="" />
		<ul>
			<li>Four levels of membership designed for everyone from the leisure traveler to the corporate client.</li>
			<li>Savings on just one hotel booking usually surpass the cost of a basic membership thanks to special rates.</li>
		</ul>
		<a href="/account" class="button">Renew Now</a>
	</div><!-- .column -->
</div><!-- #lightbox-join -->
<?php
	}// end content lockout modal

	// lightbox interstitial modals for booking and flights
	if ( ! ind_logged_in() ) {
?>
<div id="lightbox-interstitial" class="lightbox lightbox-two-col white-popup mfp-hide contain">
	<div class="column one-half">
		<h3>Book as a Guest</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/book-left.jpg" alt="" />
		<p>You are about to check room availability as a guest. If you would like to take advantage of our member rates and benefits, please <a href="/join/">join Indagare now</a>.</p>

		<form id="book-interstitial" class="book-interstitial login" method="post" novalidate>
			<div id="form-submit" class="field clearfix submit">
			   <input type="submit" value="Book Now" class="button">
			</div>
		</form>
	</div><!-- .column -->

	<div class="column one-half">
		<h3>Book as a Member</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/book-right.jpg" alt="" />
		<p>Sign in to be able to book the best rates and amenities available only to Indagare members. If you do not see the special Indagare plus rates, contact our <a href="/contact/">Bookings Team</a>.</p>

		<form id="form-interstitial" class="login" method="post" novalidate>
			<div id="field1-container" class="field">
			   <label for="field1">
					Username or Email
			   </label>
			   <input type="text" name="username" id="field1" required="required" placeholder="Your username">
			</div>

			<div id="field2-container" class="field">
			   <label for="field2">
					Password
			   </label>
			   <input type="password" name="password" id="field2" required="required" placeholder="Your password">
			</div>

			<div id="form-submit" class="field clearfix submit">
			   <input type="submit" value="Sign In" class="button">
			   <a id="forgot" href="/wp-login.php?action=lostpassword" class="button secondary">Forgot Password</a>
			</div>

			<div class="field message">
			</div>

        	<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
        </form>
	</div><!-- .column -->
</div><!-- #lightbox-interstitial -->
<div id="lightbox-interstitial-flights" class="lightbox lightbox-two-col white-popup mfp-hide contain">
	<div class="column">
		<h3>Book as a Member</h3>
		<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/book-right-flights.jpg" alt="" />

		<form id="form-interstitial-flights" class="login" method="post" novalidate>
			<div id="field1-container" class="field">
			   <label for="field1">
					Username or Email
			   </label>
			   <input type="text" name="username" id="field1" required="required" placeholder="Your username">
			</div>

			<div id="field2-container" class="field">
			   <label for="field2">
					Password
			   </label>
			   <input type="password" name="password" id="field2" required="required" placeholder="Your password">
			</div>

			<div id="form-submit" class="field clearfix submit">
			   <input type="submit" value="Sign In" class="button">
			   <a id="forgot" href="/wp-login.php?action=lostpassword" class="button secondary">Forgot Password</a>
				<a href="/join/" class="button">Join</a>
			</div>

			<div class="field message">
			</div>

			<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
		</form>
	</div><!-- .column -->
</div><!-- #lightbox-interstitial-flights -->
<?php
	} // end lightbox interstitial modals for booking and flights

	// signup modals
	if (is_page_template ( 'template-page-user-signup-step-two.php' ) ) {
?>
<div id="lightbox-signup-application" class="lightbox white-popup login mfp-hide">
	<header>
		<h2>Thank you for applying for Indagare membership</h2>
	</header>

	<p>Thank you for submitting an application to Indagare. We appreciate your interest in joining our community and will respond to you shortly.</p>

</div><!-- #lightbox-signup-application -->

<div id="lightbox-signup-error" class="lightbox white-popup login mfp-hide">
	<header>
		<h2>Payment Error</h2>
	</header>

	<p>There was an error verifying your credit card information for payment. Please go to the <a href="/account#billing-tab">Billing section</a>
	of your account, and try processing your payment again.</p>

	<p class="tiny" id="errordetail"></p>
</div><!-- #lightbox-signup-error -->

<div id="lightbox-signup-complete" class="lightbox white-popup login mfp-hide">
	<header>
		<h2>Thank you for joining Indagare</h2>
	</header>

	<p>Here's your new membership information for your records. A confirmation message has been sent to the email in your account.</p>

	<p id="membercardholder"><strong>Member Name:</strong> <span></span></p>
	<p id="memberlevel"><strong>Membership Level:</strong> <span></span></p>
	<p id="memberlength"><strong>Duration:</strong> <span></span></p>
	<p id="membercost"><strong>Price:</strong> <span></span></p>
	<p id="memberdate"><strong>Signup Date:</strong> <span></span></p>
	<p id="memberenddate"><strong>Valid Through:</strong> <span></span></p>
	<p id="membercard"><strong>Payment Using Credit Card Ending In:</strong> <span></span></p>
	<p id="membertransaction"><strong>Transaction Code:</strong> <span></span></p>

	<input id="membercomplete" type="submit" value="Continue" class="button">

</div><!-- #lightbox-signup-complete -->

<?php
	} // end signup modals

	if ( is_page_template( 'template-page-user-site-invite.php' ) ) {
		?>
<div id="lightbox-signup-error" class="lightbox white-popup login mfp-hide">
	<header>
		<h2>Account Error</h2>
	</header>

	<p>There was an error creating your account. Please call customer support at <a href="tel:+12129882611">212-988-2611</a> and reference the following error:</p>

	<p class="tiny" id="errordetail"></p>
</div><!-- #lightbox-signup-error -->
<?php
	}

	// my account modals
	if (is_page_template ( 'template-page-account-edit.php' ) ) {
?>
<div id="lightbox-signup-error" class="lightbox white-popup login mfp-hide">
	<header>
		<h2 id="signup-error-title">Credit Card Payment Error</h2>
	</header>

	<p id="signup-error-message">There was an error verifying your credit card information for payment. Please check the information that you entered and try again.</p>

</div><!-- #lightbox-signup-error -->

<div id="lightbox-signup-complete" class="lightbox white-popup login mfp-hide">
	<header>
		<h2>Thank you for renewing your membership with Indagare</h2>
	</header>

	<p>Here's your new membership information for your records. A confirmation message has been sent to the email in your account.</p>

	<p id="memberdate"><strong>Signup Date:</strong> <span></span></p>
	<p id="memberlevel"><strong>Membership Level:</strong> <span></span></p>
	<p id="membercost"><strong>Price:</strong> <span></span></p>
	<p id="memberlength"><strong>Duration:</strong> <span></span></p>
	<p id="membercardholder"><strong>Cardholder Name:</strong> <span></span></p>
	<p id="membercard"><strong>Payment Using Credit Card Ending In:</strong> <span></span></p>
	<p id="membertransaction"><strong>Transaction Code:</strong> <span></span></p>

	<input id="membercomplete" type="submit" value="Continue" class="button">

</div><!-- #lightbox-signup-complete -->

<?php
	} // end my account modals

	// contact modal for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer | book page
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| (is_page_template ( 'template-page-book.php' ) )
	) {

?>
<div id="lightbox-contact-team" class="lightbox white-popup contact detailed mfp-hide">
	<header>
		<h2>Contact the Indagare Team</h2>
	</header>


<?php

echo do_shortcode('[contact-form-7 id="'.\indagare\config\Config::$bookingform_detailed_id.'" title="Contact Booking Detailed"]');

?>

</div><!-- #lightbox -->
<?php

	} // end contact modal for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer

	// contact friend modal for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer | insidertrip | article
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' ) || is_singular( 'insidertrip' ) || is_singular( 'article' )
	) {

?>
<div id="lightbox-contact-friend" class="lightbox white-popup contact mfp-hide">
	<header>
		<h2>Share This Page</h2>
		<h3><?php echo get_the_title() ?></h3>
	</header>


<?php

// do not change across versions
echo do_shortcode('[contact-form-7 id="38938" title="Contact Tell a Friend"]');

?>

</div><!-- #lightbox -->
<?php

	} // end contact friend modal for destination top level | hotel post | restaurant post | shop post | activity post | itinerary | library | offer | insidertrip | article

	// contact modal for insidertrip post | insidertrip archive
	if (
		is_singular( 'insidertrip' ) || ( is_archive() && get_query_var('post_type') == 'insidertrip' )
	) {

?>
<div id="lightbox-contact-insidertrip" class="lightbox white-popup contact mfp-hide">
	<header>
		<h2>Contact Us</h2>
	</header>


<?php

echo do_shortcode('[contact-form-7 id="32337" title="Contact Insider Trips"]');

?>

</div><!-- #lightbox -->
<?php

	} // end contact modal for insidertrip post

?><?php /*
  <script type="text/javascript" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/js/template-page_footer.js"></script>

<?php if (is_page_template ( 'template-page-map.php' ) ) :  // map page ?>
	<?php export_destinations( false ); ?>
	<script type="text/javascript" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/js/template-page-map_footer.js"></script>
<?php endif; // end map page ?>
*/ ?><?php
	// booking widget - date selector, destination input field, URL builder
	if (
		is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' )
		|| ( is_archive() && $reg && $depth == 1 )
		|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
		|| ( is_archive() && get_query_var('post_type') == 'hotel' )
		|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
		|| ( is_archive() && get_query_var('post_type') == 'shop' )
		|| ( is_archive() && get_query_var('post_type') == 'activity' )
		|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
		|| ( is_archive() && get_query_var('post_type') == 'library' )
		|| is_home() || is_front_page()
		|| is_page_template ( 'template-page-book.php' ) ) : ?>
	<script type="text/javascript" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/js/booking_widget.js"></script>
<?php endif;  // end booking widget - date selector, destination input field, URL builder ?>

<script>
	<?php if ( $count ) { ?>
		var itemcount = <?php echo $count ?>;
	<?php } else { ?>
		var itemcount = 0;
	<?php } ?>
	var login_redirect = '<?php $_SERVER["HTTP_REFERER"] ?>';
	var swifttripurl = '<?php global $swifttripurl; echo $swifttripurl; ?>';

	jQuery().ready(function($) {
		<?php if ( indagare\cookies\FirstVisit::isFirstVisit() ) : 	// first visit modal   ?>
			$.magnificPopup.open({
			  items: {
				type: 'inline',
				src: '#lightbox-first', // can be a HTML string, jQuery object, or CSS selector
				midClick: true
			  },
			});
		<?php endif; // end first visit modal ?>

		<?php	if ( is_archive() && get_query_var('post_type') == 'insidertrip' ) : 	// insider trip archive ?>
			$("#faq p").hide();
		  $("#faq h3").click(function () {
		    $(this).next("#faq p").slideToggle(500);
		    $(this).toggleClass("expanded");
		  });
		<?php endif; // end insider trip archive  ?>

	}); <?php //=== End jQuery.ready ?>

	<?php
		// booking widget - date selector, destination input field, URL builder
		if (
			is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'offer' )
			|| ( is_archive() && $reg && $depth == 1 )
			|| ( is_archive() && $dest && $depth == 2 && !get_query_var('post_type') )
			|| ( is_archive() && get_query_var('post_type') == 'hotel' )
			|| ( is_archive() && get_query_var('post_type') == 'restaurant' )
			|| ( is_archive() && get_query_var('post_type') == 'shop' )
			|| ( is_archive() && get_query_var('post_type') == 'activity' )
			|| ( is_archive() && get_query_var('post_type') == 'itinerary' )
			|| ( is_archive() && get_query_var('post_type') == 'library' )
			|| is_home() || is_front_page()
			|| is_page_template ( 'template-page-book.php' ) ) : ?>

		var ssotokenvalue_default = 'x4T306PLm1KWuXktHqtGzw%3D%3D';
		var ssotokenvalue = ssotokenvalue_default;
		<?php if ( ind_logged_in() ) {
			$account = \WPSF\Contact::get_account_wp();
			if ( method_exists( $account, 'get_ssotoken' ) ) {
				print 'ssotokenvalue = "' . $account->get_ssotoken() . '";';
			}
		}
		?>
		<?php endif; ?>

////////////////////////////////////////////////////////////////////////////
jQuery().ready(function($) {
	<?php if (is_page_template ( 'template-page-password-reset.php' ) ) :  // password reset page ?>
		/*
		$("#form-reset").submit(function(event) {
			var url = theme_path+'/process_password_reset.php';
			emailvars = $('#form-reset #email').val();

			if (!!emailvars.length ) {
				$.ajax({
					   type: "POST",
					   url: url,
					   data: $("#form-reset").serialize(),
					   success: function(data)
					   {
						   var json = $.parseJSON(data);

						   if ( json.email == true ) {

								$('#form-reset .message').html('<p>Please check your email for a message with a reset link.</p>').fadeIn(1500);

						   } else {

								$('#form-reset .message').html('<p>No matching email address - please try again.</p>').fadeIn(1500).fadeOut(1500);
						   }
					   }

				});

			}

			event.preventDefault();

		});
		*/
<?php endif;  // end password reset page ?>

<?php
 	// home page
 	if (is_home() || is_front_page() ) {
?>
  $(function() {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: false,           // Boolean: Show pager, true or false
		nav: true,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs').insertAfter('#rslideswrapper');
  });

<?php
	} // end home page

 	// book page
 	if ( is_page_template('template-page-book.php') ) {
?>

	$('.benefitwrapper').click(function() {
		$(this).find('.benefit').slideToggle();
		$(this).toggleClass('open');
	});

  $(function() {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: true,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs').insertAfter('#rslideswrapper');
  });

<?php
	} // end book page

 	// join page
 	if ( is_page_template('template-page-user-signup.php') ) {

/*
  $(function() {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 6000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: false,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs').insertAfter('#rslideswrapper');
  });
*/

?>

	$('.memberlevels').slick({
        arrows: true,
        appendArrows: $('.memberlevelsnav'),
		slidesToShow: 3,
		infinite: false,
		responsive: [
		{
			breakpoint: 730,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				infinite: false
			}
		},
		{
			breakpoint: 480,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				infinite: true
			}
		}
	    ],
	    prevArrow: '<a href="#" class="rslides_nav prev">Previous</a>',
	    nextArrow: '<a href="#" class="rslides_nav next">Next</a>',
	});

/*
	$('.memberlevelsnav .next').on('click', function(){
		event.preventDefault();
		$('.memberlevels').slick('slickNext');
	});

	$('.memberlevelsnav .prev').on('click', function(){
		event.preventDefault();
		$('.memberlevels').slick('slickPrev');
	});
*/

    $('.memberlevelitems').matchHeight();
    $('.memberlevelrecap').matchHeight();

<?php
	} // end join page

 	// welcome page
 	if ( is_page_template('template-page-welcome.php') ) {
?>

  $(function() {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 6000,          // Integer: Time between slide transitions, in milliseconds
		pager: false,           // Boolean: Show pager, true or false
		nav: true,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs').insertAfter('#rslideswrapper');
  });

<?php
	} // end welcome page

	// about mission page - imagesloaded and masonry
	if ( is_page_template('template-page-about-mission.php') ) {
?>

	var $container = $('#masonry');
	// initialize
	$container.masonry({
	  columnWidth: '.grid-sizer',
	  gutter: 19,
	  itemSelector: '.item'
	});
	// layout Masonry again after all images have loaded
	$container.imagesLoaded( function() {
	  $container.masonry();
	});


<?php
	} // end about mission page - imagesloaded and masonry

	// map page
	if ( is_page_template('template-page-map.php') ) {
?>

//	$("img.lazy").lazyload({
//		threshold : 200
//	});

	$(function() {
		$("img.lazy").lazyload({
			threshold : 200,
			event : "preload"
		});
	});

	$(window).bind("load", function() {
		$("img.lazy").trigger("preload")
	});

/*
	$('area').each(function(){
	   $(this).qtip({
			show: {
				solo: true,
				event: 'click mouseenter'
			},
			hide: {
					event: false,
					inactive: 3000
			},
			 content: {
			 text: $('.'+$(this).attr('id')+'')
			 },
			position: {
				my: 'top center',
				at: 'center',
				viewport: true
			},
			style: {
				classes: 'qtip-bootstrap'
			}
		});
	});

	$('img[usemap]').rwdImageMaps();
*/

<?php
	} // end map page

 	// archive for career
 	if ( is_archive() && get_query_var('post_type') == 'career' ) {
?>

	$('a.more').click(function() {
		event.preventDefault();
		var txt = $(this).parent().next("div.more").is(':visible') ? <?php echo __('Read More','indagare');?> : <?php echo __('Read Less','indagare');?>;
		$(this).text(txt);
		$(this).parent().next("div.more").slideToggle(500);
	});

<?php
	} // end archive for career

?>

	$('.customselect').customSelect();
	$('.customselect').wrap('<span class="customSelectWrap"></span>');
	//$('#user_prefix').customSelect().val(user.title);
	/* $('#user_prefix').customSelect({
	      customValue: true,
	      windowFormatter: function (value) {
	        return user.title;
	      }
	    }); */

}); // end jQuery().ready(function($)

	function getURLParameter(name) {
		return decodeURIComponent(
			(location.search.match(RegExp("[?|&]"+name+'=(.+?)(&|$)'))||[,null])[1]
		);
	}

</script>
<?php

	$gallerycount = 0;

	// singular hotel | restaurant | shop | activity | article | offer | insidertrip
	if ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular( 'article' ) || is_singular( 'offer' ) || is_singular( 'insidertrip' ) ) {
//		$gallery = get_field('gallery-header');
		$gallery = get_field('gallery-header',false,false);
		if ( $gallery ) {
//			$gallerycount++;
			$gallerycount = count($gallery);
		}
	// itinerary archive
	} else if ( is_archive() && get_query_var('post_type') == 'itinerary' ) {
		$rows = get_field('itinerary-section');
		if( $rows) {
			foreach($rows as $row) {
				$gallery = $row['itinerary-gallery'];
				if ($gallery) {
					$gallerycount++;
				}
			}
		}
	// sign up step one page
	} else if ( is_page_template ( 'template-page-user-signup.php' ) ) {
		$gallery = get_field('gallery');
		if ( $gallery ) {
			$gallerycount = count($gallery);
		}

	// why join page
	} else if ( is_page_template ( 'template-page-why-join.php' ) ) {
		$gallery = get_field('gallery');
		if ( $gallery ) {
			$gallerycount = count($gallery);
		}
	}

	echo '<!-- gallerycount '.$gallerycount.' -->'."\n";

	if (
		$gallerycount > 1 && !is_singular( 'hotel' ) && !is_singular( 'restaurant' ) && !is_singular( 'shop' ) && !is_singular( 'activity' ) && !is_singular('article') && !is_singular('offer') && !is_singular( 'insidertrip' ) && !is_post_type_archive('itinerary')
		&& !is_page_template ( 'template-page-user-signup.php' ) && !is_page_template ( 'template-page-why-join.php' )
	) {
?>
<script>
jQuery(document).ready(function($) {
	$(".royalSlider").royalSlider({
	autoScaleSlider: false,
	autoWidth: null,
	autoHeight: null,
	imageScalePadding: 0,
	imageScaleMode:'none',
	imageAlignCenter: false,
	autoHeight: true,
	transitionType:'fade',
	globalCaption:false,
	arrowsNav: true,
	controlNavigation: 'bullets',
	controlsInside: true,
	loop: true,
		autoPlay: {
		// autoplay options go gere
		enabled: false,
		pauseOnHover: true
		}
	 });
});
</script>
<?php
	} else if ( $gallerycount > 1 && ( is_singular( 'hotel' ) || is_singular( 'restaurant' ) || is_singular( 'shop' ) || is_singular( 'activity' ) || is_singular('article') || is_singular('offer') || is_singular( 'insidertrip' ) ) ) {
?>
<script>
jQuery(document).ready(function($) {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: true,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs').insertAfter('#rslideswrapper');
});
</script>
<?php
	} else if ( $gallerycount > 1 && is_post_type_archive('itinerary') ) {
?>
<script>
jQuery(document).ready(function($) {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: true,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs').each(function() {
		$(this).insertAfter($(this).parent());
	})

});
</script>
<?php
	} else if ( $gallerycount > 1 && is_page_template ( 'template-page-user-signup.php' ) ) {
?>
<script>
jQuery(document).ready(function($) {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: false,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs_wrapper').append( $('.rslides_tabs') );

});
</script>
<?php
	} else if ( $gallerycount > 1 && is_page_template ( 'template-page-why-join.php' ) ) {
?>
<script>
jQuery(document).ready(function($) {
    $(".rslides").responsiveSlides({
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 4000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: false,             // Boolean: Show navigation, true or false
		pause: true
	});

	$('.rslides_tabs_wrapper').append( $('.rslides_tabs') );

});
</script>
<?php
	}

}
add_action('thematic_after','child_after');

// article meta for favorites and social links
function article_meta($postID) {
	$articlemeta = '<div class="article-meta contain">'."\n";

	// add to favorites
	if ( ind_logged_in() ) {

		$articlemeta .= '<p class="user-meta">';

		if ( indagare\users\User::hasFavorite($postID) ) {
			$articlemeta .= '<a href="'.get_bloginfo('stylesheet_directory').'/favorite.php?action=remove&postid='.$postID.'"><b class="icon" data-icon="&#xf097;"></b> Remove from Wish List</a>';
		} else {
			$articlemeta .= '<a href="'.get_bloginfo('stylesheet_directory').'/favorite.php?action=add&postid='.$postID.'"><b class="icon" data-icon="&#xf097;"></b> Add to Wish List</a>';
		}

//		$articlemeta .= ' <a href="#"><b class="icon" data-icon="&#xf08a;"></b> Like</a> (19 likes)';

		$articlemeta .= '</p>'."\n";
	}

		$articlemeta .= '<p class="social-meta"><span class="icon-label">Share:</span> <span id="social-facebook"><b class="icon custom-icon" data-icon="&#xe003;"><span class="st_facebook"displayText=""></span></b></span> <span id="social-twitter"><b class="icon custom-icon" data-icon="&#xe001;"><span class="st_twitter"></span></b></span> <span id="social-pinterest"><b class="icon custom-icon" data-icon="&#xe007;"><span class="st_pinterest"></span></b></span> <a id="social-email" class="contact lightbox-inline" href="#lightbox-contact-friend"><b class="icon custom-icon" data-icon="&#xe614;"></b></a></p>'."\n";
//		$articlemeta .= '<p class="social-meta"><span class="icon-label">Share:</span> <span id="social-facebook"><b class="icon custom-icon" data-icon="&#xe003;"><span class="st_facebook"displayText=""></span></b></span> <span id="social-twitter"><b class="icon custom-icon" data-icon="&#xe001;"><span class="st_twitter"></span></b></span> <span id="social-pinterest"><b class="icon custom-icon" data-icon="&#xe007;"><span class="st_pinterest"></span></b></span></p>'."\n";
	$articlemeta .= '</div>'."\n";

	return $articlemeta;

}

// Modify Tiny_MCE init
function customformatTinyMCE($init) {
	// Add block format elements you want to show in dropdown
//	$init['theme_advanced_blockformats'] = 'p,h2,h4';
//	$init['theme_advanced_disable'] = 'forecolor,underline,strikethrough,wp_adv';
//	$init['wordpress_adv_hidden'] = false;

	$init['block_formats'] = 'Paragraph=p;Header 2=h2;Header 4=h4';
	$init['toolbar1'] = 'template,|,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,spellchecker,wp_fullscreen,wp_adv';
	$init['toolbar2'] = 'formatselect,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help';

	return $init;
}
add_filter('tiny_mce_before_init', 'customformatTinyMCE' );

// display hotel booking ID column in admin
add_filter('manage_hotel_posts_columns', 'new_add_hotel_post_thumbnail_column', 5);
function new_add_hotel_post_thumbnail_column($cols) {
    $cols['booking'] = __('Booking ID');
    return $cols;
}

add_action('manage_hotel_posts_custom_column', 'new_display_hotel_post_thumbnail_column', 5, 2);
   function new_display_hotel_post_thumbnail_column($col, $id) {
       switch($col) {
           case 'booking':
           if (function_exists('get_field')){
              //get the acf value
              $booking = get_field('booking');
               if( $booking ) {
               		echo $booking;
               }
           }
           break;
       }
   }

// modify number of destinations listed on admin page
function admindestinationsperpage(){
	$destinationslistlength = get_field('destinations-list-length', 'option');
	if ( $destinationslistlength ) {
		$get_items_per_page = $destinationslistlength;
	} else {
		$get_items_per_page = 25;
	}

	return $get_items_per_page;
}
add_filter('edit_destinations_per_page', 'admindestinationsperpage');


// sort by name for review listings of hotel | restaurant | shop | activity || sort by reverse date for articles
function sort_by_name(&$query) {
	if ( $query->is_admin ) return;

	if ( !$query->is_main_query() ) return;

	if ($query->query['post_type'] == 'memberlevel' ) return;

	if ( $query->is_search ) {
		$query->set( 'posts_per_page', -1);
  	if ( !empty( $_GET['filter'] ) ) {
			$query->set( 'post_type', $_GET['filter'] );
		} else {
			$query->set( 'post_type', array( 'hotel', 'restaurant', 'shop', 'activity', 'itinerary', 'library', 'article', 'offer', 'insidertrip' ) );
		}
	}

	if ( is_archive() ) {
		if (	in_array($query->query['post_type'], array(
						'hotel',
						'restaurant',
						'shop',
						'activity',
						'offer'
					)
				)
			) {
			$query->set('orderby', array( 'post_title' => 'ASC' ) );

	  } else if ( $query->query['post_type'] == 'article' && getLastPathSegment($_SERVER['REQUEST_URI']) !== 'features' ) {
		  // order articles by reverse date except features page
	    $query->set( 'orderby', array( 'date' => 'DESC' ) );
	  }
	}
}
add_action('pre_get_posts', 'sort_by_name');

// draft display in admin
function my_custom_recent_drafts( $query ) {
	if (
		isset( $query->query_vars['post_status'] ) && $query->query_vars['post_status'] == 'draft'
		&& isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'post'
		&& isset( $query->query_vars['author'] ) && $query->query_vars['author'] == $GLOBALS['current_user']->ID
		&& isset( $query->query_vars['posts_per_page'] ) && $query->query_vars['posts_per_page'] == 5
		&& isset( $query->query_vars['orderby'] ) && $query->query_vars['orderby'] == 'modified'
		&& isset( $query->query_vars['order'] ) && $query->query_vars['order'] == 'DESC'
	) {
		// show all post types
		$query->query_vars['post_type'] = 'any';
		// show 10 drafts
		$query->query_vars['posts_per_page'] = 10;
		// if admin or editor, show drafts of all users
		if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
			unset( $query->query_vars['author'] );
		}
	}
	return $query;
}
add_action( 'pre_get_posts', 'my_custom_recent_drafts' );

add_filter('new_royalslider_skins', 'new_royalslider_add_custom_skin', 10, 2);
function new_royalslider_add_custom_skin($skins) {
      $skins['myCustomSkin'] = array(
           'label' => 'The custom skin',
           'path' => get_stylesheet_directory_uri() . '/css/slider.css'  // get_stylesheet_directory_uri returns path to your theme folder
      );
      return $skins;
}

function my_order_cats($args,$taxonomies){
    //Check we are admin side
    if(is_admin() && function_exists('get_current_screen')){
        $taxonomy = $taxonomies[0];
        $screen = get_current_screen();

        //Check screen ID and taxonomy and changes $args where appropriate.
        if( $screen->id=='edit-destinations' && $taxonomy=='destinations'){
            $args['orderby']='name'; //preserves order of subcategories.
            $args['order']='asc'; //or desc
        }
    }
    return $args;
}
add_action('get_terms_args','my_order_cats',10,2);

// add filters in admin for custom post types based on custom taxonomy
add_action( 'restrict_manage_posts', 'todo_restrict_manage_posts' );
add_filter('parse_query','todo_convert_restrict');
function todo_restrict_manage_posts() {
    global $typenow;
//    $args   =   array( 'public' => true, '_builtin' => true );
    $args   =   array( 'public' => true, '_builtin' => false);
    $post_types = get_post_types($args);
    //var_dump($post_types);
    if ( in_array($typenow, $post_types) && $typenow !== 'magazine' && $typenow !== 'press'  && $typenow !== 'career' ) { // exclude magazine and press and career
        $filter = get_object_taxonomies($typenow,'objects');
        /*$filters    = array(
                        'hotel' => array('destinations','hoteltype'),
                        'restaurant' => array('destinations','restauranttype','mealtype'),
                        'shop' => array('destinations','shoptype'),
                        'activity' => array('destinations','activitytype'),
                        'itinerary' => array('destinations'),
                        'library' => array('destinations'),
                        'article' => array('destinations','column','interest'),
                        'offer' => array('destinations'),
                        'insidertrip' => array('destinations')
        );
        $filter = $filters[$typenow];*/
 		if(empty($filter)) return;
        //var_dump($filters);
        foreach ($filter as $tax_obj) {
			//$tax_obj = get_taxonomy($tax_slug);
			wp_dropdown_categories(array(
				'show_option_all' => __('All '.$tax_obj->label ),
				'taxonomy' => $tax_obj->name,
				'name' => $tax_obj->name,
				'orderby' => 'name',
				'selected' => $_GET[$tax_obj->query_var],
				'hierarchical' => $tax_obj->hierarchical,
				'show_count' => false,
				'hide_empty' => false
			));
        }
    }
}
function todo_convert_restrict($query) {
    global $pagenow;
    global $typenow;
    if ($pagenow=='edit.php') {
        $filters = get_object_taxonomies($typenow);
 		if(empty($filters)) return;
        foreach ($filters as $tax_slug) {
            $var = &$query->query_vars[$tax_slug];
            if ( isset($var) ) {
                $term = get_term_by('id',$var,$tax_slug);
                $var = $term->slug;
            }
        }
    }
}
function override_is_tax_on_post_search($query) {
    global $pagenow;
    $qv = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($qv['taxonomy']) && isset($qv['s'])) {
        $query->is_tax = true;
    }
}
add_filter('parse_query','override_is_tax_on_post_search');

function getLastPathSegment($url) {
    $path = parse_url($url, PHP_URL_PATH); // to get the path from a whole URL
    $pathTrimmed = trim($path, '/'); // normalise with no leading or trailing slash
    $pathTokens = explode('/', $pathTrimmed); // get segments delimited by a slash

    if (substr($path, -1) !== '/') {
        array_pop($pathTokens);
    }
    return end($pathTokens); // get the last segment
}

// export functions on updates
function export_destinations( $flush = true ) {
	if( !$flush ) {
		$files = array(
			$_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/datadestinations_ac.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/dataregions.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/dataregions_ac.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/datacities.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/datacities_ac.json');
		foreach ( $files as $f ) {
			if( !file_exists( $f ) ) {
				$flush = true;
			}
		}
	}

	if(!$flush) {
		return;
	}

	$destinations = get_terms( 'destinations', array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false) );

	$regions = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destinationstree = array_reverse($destinationstree);
		$destdepth = count($destinationstree);
		return $destdepth == 1;
	});

	$cities = array_filter($destinations, function ($t) {
		$destinationstree = get_ancestors( $t->term_id, 'destinations' );
		$destinationstree = array_reverse($destinationstree);
		$destdepth = count($destinationstree);
		return $destdepth == 2;
	});

	$datadestinations = array();
	$datadestac = array();
	$dataregions = array();
	$dataregionsac = array();
	$datacities = array();
	$datacitiesac = array();
	$i = 0;

	// cities
	foreach ( $cities as $term ) {
		$datadestinations[$i][] = $term->term_id;
		$datadestinations[$i][] = $term->slug;
		$datadestinations[$i][] = $term->name;

		$datacities[$i][] = $term->term_id;
		$datacities[$i][] = $term->slug;
		$datacities[$i][] = $term->name;

		$datacitiesac[$i][] = $term->name;
		$datacitiesac[$i][] = $term->slug;

		$datadestac[$i][] = $term->name;
		$datadestac[$i][] = $term->slug;

		$i++;
	}

	$j = $i;

	// regions
	foreach ( $regions as $term ) {
		$datadestinations[$i][] = $term->term_id;
		$datadestinations[$i][] = $term->slug;
		$datadestinations[$i][] = $term->name;

		$dataregions[$i-$j][] = $term->term_id;
		$dataregions[$i-$j][] = $term->slug;
		$dataregions[$i-$j][] = $term->name;

		$dataregionsac[$i-$j][] = $term->name;
		$dataregionsac[$i-$j][] = $term->slug;

		$datadestac[$i][] = $term->name;
		$datadestac[$i][] = $term->slug;

		$i++;
	}

	$file = fopen($_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.txt', 'w');
	$headers = array('destination_id','slug','name');
	fputcsv($file, $headers);
		foreach ($datadestinations as $fields) {
			fputcsv($file,$fields);
		}
	fclose($file);

	$jsondestinations = json_encode($datadestinations);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json', $jsondestinations);

	$jsondestinations = json_encode($datadestac);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations_ac.json', $jsondestinations);

	$jsonregions = json_encode($dataregions);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/dataregions.json', $jsonregions);

	$jsonregions = json_encode($dataregionsac);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/dataregions_ac.json', $jsonregions);

	$jsoncities = json_encode($datacities);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/datacities.json', $jsoncities);

	$jsoncities = json_encode($datacitiesac);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/datacities_ac.json', $jsoncities);
	export_bookingwidget();
}
add_action('edited_destinations', 'export_destinations', 10, 1);
add_action('created_destinations', 'export_destinations', 10, 1);
add_action('delete_destinations', 'export_destinations', 10, 1);

function export_bookingwidget() {
	$datadestinations = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datadestinations.json');
	$filtersbooking = json_decode($datadestinations);

	$data = array();

	foreach($filtersbooking as $row) {
		$name = indg_decode_string( $row[2] );
		$namenoaccent = remove_accents($name);
		$data[] = array( $name , $row[0], "destination" );
		if ( $name !== $namenoaccent ) {
			$data[] = array( $namenoaccent , $row[0], "destination" );
		}
	}

	$datahotels = file_get_contents($path = $_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json');
	$filtersbooking = json_decode($datahotels);

	foreach($filtersbooking as $row) {
		$name = indg_decode_string( $row[1] );
		$namenoaccent = remove_accents($name);
		$data[] = array( $name , $row[2], "hotel" );
		if ( $name !== $namenoaccent ) {
			$data[] = array( $namenoaccent , $row[2], "hotel" );
		}
	}

	$json = json_encode($data);
	file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/bookingwidget.json', $json);
}

function export_hotels( $flush = true ) {
	if( !$flush ) {
		$files = array(
			$_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/datahotelsurls.json',
			$_SERVER['DOCUMENT_ROOT'].'/export/datahotelsurls.txt',
			$_SERVER['DOCUMENT_ROOT'].'/export/datahotelstaxids.txt');
		foreach ( $files as $f ) {
			if( !file_exists( $f ) ) {
				$flush = true;
			}
		}
	}

	if(!$flush) {
		return;
	}

	global $post;

	//if ( $post->post_type == 'hotel' ) {

		$args = array('numberposts' => -1, 'post_type' => 'hotel', 'orderby' => 'name', 'order' => 'ASC', 'post_status' => 'publish', 'fields' => 'ids');
		$hotels = get_posts($args);

		$datahotels = array();
		$datahotelsurls = array();
		$datahotelstaxids = array();
		$i = 0;

		$file_urls = fopen($_SERVER['DOCUMENT_ROOT'].'/export/datahotelsurls.txt', 'w');
		$file_ids = fopen($_SERVER['DOCUMENT_ROOT'].'/export/datahotelstaxids.txt', 'w');

		$headers = array('sabre_code','hotel_url','hotel_name');
		fputcsv($file_urls, $headers);

		$headers = array('region_id','destination_id','sabre_code','hotel_name');
		fputcsv($file_ids, $headers);

		foreach( $hotels as $hotel ) {
			$booking = get_field( 'booking', $hotel, false );

			if ( $booking ) {
				if ( strlen($booking) < 7 ) {
					$booking = str_pad($booking, 7, "0", STR_PAD_LEFT);
				} else if ( strlen($booking) > 7 ) {
					$booking = substr($booking,-7);
				}

				$destinationstree = destinationstree($hotel);
				$dest = $destinationstree['dest'];
				$reg = $destinationstree['reg'];
				$title = get_the_title($hotel);
				$link = get_permalink($hotel);

				$urls = array( $booking, $link, $title );

				$datahotels[] = array( $hotel, $title, $booking );
				$datahotelsurls[] = $urls;

				fputcsv( $file_urls, $urls );
				fputcsv( $file_ids, array( $reg->term_id, $dest->term_id, $booking, $title ) );
			}
		}

		fclose($file_urls);
		fclose($file_ids);

		$jsonhotels = json_encode($datahotels);
		file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/datahotels.json', $jsonhotels);

		$jsonhotelsurls = json_encode($datahotelsurls);
		file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/export/datahotelsurls.json', $jsonhotelsurls);
	//}

	export_bookingwidget();
}
add_action('save_post', 'export_hotels');
add_action('delete_post', 'export_hotels');

function export_hotels_quick($new_status, $old_status, $post) {
	if ( $post->post_type == 'hotel' ) {
		export_hotels( true );
	}
}
add_action('transition_post_status', 'export_hotels_quick', 10, 3);
// end export functions on updates

 update_option('image_default_link_type','none');

// prevent search with no terms from going to home page
add_filter( 'request', 'my_request_filter' );
function my_request_filter( $query_vars ) {
    if( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
        $query_vars['s'] = " ";
    }
    return $query_vars;
}

// sort by last name for team and contributor pages | WP_User_Query
function sort_my_users_by_lastname( $a, $b ) {
	if ( $a->last_name == $b->last_name ) {
		return 0;
	}

	return ( $a->last_name < $b->last_name ) ? -1 : 1;
}

/**
 * Sorting array of associative arrays - multiple row sorting using a closure.
 * See also: http://the-art-of-web.com/php/sortarray/
 *
 * @param array $data input-array
 * @param string|array $fields array-keys
 * @license Public Domain
 * @return array
 */
 function sortArray( $data, $field ) {
    $field = (array) $field;
    uasort( $data, function($a, $b) use($field) {
        $retval = 0;
        foreach( $field as $fieldname ) {
            if( $retval == 0 ) $retval = strnatcmp( $a[$fieldname], $b[$fieldname] );
        }
        return $retval;
    } );
    return $data;
}

// remove prev/next links in header generated by Yoast SEO
if (defined('WPSEO_VERSION')) {
    function custom_wpseo_override() {

    global $wpseo_front;

//    remove_action('wpseo_head', array($wpseo_front, 'canonical'), 20);
//    add_action('wpseo_head', 'custom_wpseo_canonical', 20);
    remove_action('wpseo_head', array($wpseo_front, 'adjacent_rel_links'), 21);
    add_action('wpseo_head', 'custom_wpseo_adjacent_rel_links', 21);
    }
    add_action('init','custom_wpseo_override');

	function custom_wpseo_canonical() {}
	function custom_wpseo_adjacent_rel_links() {}

}

// https redirect
function chkhttps(){
    if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
        $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header("Location: $redirect");
    }
}

function chkhttp() {
	if ( isset($_SERVER['HTTPS']) ) {
		unset($_SERVER['HTTPS']);
        $redirect = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header("Location: $redirect");
	}
}

function indg_decode_string( $string ) {
	$result = str_replace('&#8217;', chr(146), $string);
	$result = html_entity_decode($result);
	return $result;
}

function posts_by_year($posttype) {
  // array to use for results
  $years = array();

  // get posts from WP
  $posts = get_posts(array(
    'numberposts' => -1,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => $posttype,
    'post_status' => 'publish'
  ));

  // loop through posts, populating $years arrays
  foreach($posts as $post) {
    $years[date('Y', strtotime($post->post_date))][] = $post;
  }

  // reverse sort by year
  krsort($years);

  return $years;
}


define('POSTTYPE_ARCHIVEONLY', -1);
define('POSTTYPE_SINGLEONLY', 0);
define('POSTTYPE_ARCHIVEORSINGLE', 1);

/**
 * Checks the current post for a given post type.  Returns true if found, false if not.
 *
 * @param string $type The post type to check for
 * @param int $archive Whether or not to check for an archive post.  1 will return true
 * for both singular posts and archive pages for the given type, 0 will return true only
 * for single posts, and -1 will return true only for archive pages for the given type.
 */
function is_posttype( $type, $archive = POSTTYPE_ARCHIVEORSINGLE ) {
	if( is_singular( $type ) && ( $archive != POSTTYPE_ARCHIVEONLY ) ) return true;
	if( ( $archive == POSTTYPE_SINGLEONLY ) || !is_archive() ) return false;
	return ( get_query_var( 'post_type' ) == $type );
}

function has_map() {
	return ( is_page_template ( 'template-page-map.php' )  ||
			is_posttype('hotel') ||
			is_posttype('restaurant') ||
			is_posttype('shop') ||
			is_posttype('activity') );
}

function wp_logout_url_stay() {
	return process_and_stay('wp_logout_url');
}

function process_and_stay( $func ) {
	$pageURL  = 'http';
	if ( ! empty( $_SERVER['HTTPS'] ) ) $pageURL .= 's';
	$pageURL .= '://';
	$pageURL .= $_SERVER['HTTP_HOST'];
	$pageURL .= $_SERVER['REQUEST_URI'];
	if(is_callable($func)) {
		return $func($pageURL);
	}
}

function reset_user_password( $login ) {
	$errors = new WP_Error();

	$user_data = get_user_by('login', $login);

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
		return $errors;
	}

	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = get_password_reset_key( $user_data );

	if ( is_wp_error( $key ) ) {
		return $key;
	}

	$message = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

	if ( is_multisite() ) {
		$blogname = get_current_site()->site_name;
	} else {
		/*
		 * The blogname option is escaped with esc_html on the way into the database
		 * in sanitize_option we want to reverse this for the plain text arena of emails.
		 */
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	}

	$title = sprintf( __('[%s] Password Reset'), $blogname );

	/**
	 * Filters the subject of the password reset email.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
	 *
	 * @param string  $title      Default email title.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 */
	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

	/**
	 * Filters the message body of the password reset mail.
	 *
	 * @since 2.8.0
	 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
	 *
	 * @param string  $message    Default mail message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 */
	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

	if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
		wp_die( __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

	return true;
}

/**
 * Checks to see if the current logged in user has permission to view this page/item/something.
 *
 * @return boolean True if they have permission, False if not.
 */
function user_has_permission() {
	global $wp_query;

	if ( is_posttype( 'itinerary', POSTTYPE_ARCHIVEORSINGLE ) ) {
		return current_user_can( 'ind_read_itinerary' );
	}

	if ( is_posttype( 'magazine', POSTTYPE_SINGLEONLY ) ) {
		// Check if this is the most recent post somehow
	}

	if ( is_posttype( 'magazine', POSTTYPE_ARCHIVEONLY ) ) {
		return true;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$current = ( $paged == 1 && $wp_query->current_post == 0 );
		return ( current_user_can( 'ind_read_magazine_archive' ) ||
			( $current /* && current_user_can( 'ind_read_magazine' ) */ ) );
	}

	return true;
}

function ind_show_email_popup() {
	if ( isset( $_GET['modalemail'] ) ) {
		return true;
	}

	if ( ind_logged_in() ) {
		// We're logged in.  Never show this thing.
		return false;
	}

	// Don't count these page templates towards the email signup
	$templates = array(
		'template-page-user-signup.php',
		'template-page-why-join.php',
		'template-page-intro.php',
		'template-page-about-mission.php',
		'template-page-user-signup-step-two.php',
		'template-page-account-edit.php',
		'template-page-how-we-work.php',
		'template-page-how-to-book.php',
	);

	$this_template = basename( get_page_template() );
	foreach( $templates as $t ) {
		if ( $t == $this_template ) {
			return false;
		}
	}

	if ( \indagare\cookies\PageCountAll::getPageCountAll() == 5 ) {
		return true;
	}

	return false;
}