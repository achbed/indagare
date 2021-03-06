<?php
/**
 * Archive Template for Itinerary
 *
 * Displays an Archive index of post-type items. Other more specific archive templates
 * may override the display of this template for example the category.php.
 *
 * @package Thematic
 * @subpackage Templates
 *
 * @link http://codex.wordpress.org/Template_Hierarchy Codex: Template Hierarchy
 */

	// calling the header.php
	get_header();

	// action hook for placing content above #container
	thematic_abovecontainer();
?>

		<div id="container" class="standard">

		<?php
			// action hook for placing content above #content
			thematic_abovecontent();

			// filter for manipulating the element that wraps the content
			echo apply_filters( 'thematic_open_id_content', '<div id="content" class="itinerary">' . "\n\n" );

			// displays the page title
			thematic_page_title();

			// create the navigation above the content
//			thematic_navigation_above();

			if ( user_has_permission() ) {
        	// action hook for placing content above the archive loop
        	thematic_above_archiveloop();
			}

			// action hook creating the archive loop
			thematic_archiveloop();

        	// action hook for placing content below the archive loop
        	thematic_below_archiveloop();

			// create the navigation below the content
//			thematic_navigation_below();

			echo apply_filters( 'thematic_close_id_content', '</div><!-- #content -->' . "\n" );

        	// action hook for placing content below #content
	    	thematic_belowcontent();
		    ?>

		</div><!-- #container -->

<?php
	// action hook for placing content below #container
	thematic_belowcontainer();

	// calling the standard sidebar
	thematic_sidebar();

	// calling footer.php
	get_footer();
?>