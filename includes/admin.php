<?php

// move destinations to its own menu in admin, remove elsewhere
function adjust_the_wp_menu() {
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
	add_menu_page(__('Destinations','indagare'), __('Destinations','indagare'), 'edit_posts', 'edit-tags.php?taxonomy=destinations', '', '', '50');

}
add_action( 'admin_menu', 'adjust_the_wp_menu', 999 );

// revise Tiny_MCE in editor
function customformatTinyMCE($init) {
	// Add block format elements you want to show in dropdown
	$init['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4,h5,h6';
	$init['theme_advanced_disable'] = 'forecolor,underline,strikethrough,wp_adv';
	$init['wordpress_adv_hidden'] = false;

	return $init;
}
// Modify Tiny_MCE init
add_filter('tiny_mce_before_init', 'customformatTinyMCE' );


?>