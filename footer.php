<?php
/**
 * Footer Template
 *
 * This template closes #main div and displays the #footer div.
 * 
 * Thematic Action Hooks: thematic_abovefooter thematic_belowfooter thematic_after
 * Thematic Filters: thematic_close_wrapper can be used to remove the closing of the #wrapper div
 * 
 * @package Thematic
 * @subpackage Templates
 */
?>
		<?php // action hook for placing content above the closing of the #main div
			thematic_abovemainclose();
		?>
		
			</div><!-- .candy-wrapper -->
		</div><!-- #main -->
    	
    	<?php
			// action hook for placing content above the footer
			thematic_abovefooter();
		
			// Filter provided for altering output of the footer opening element
//    		echo ( apply_filters( 'thematic_open_footer', '<div id="footer">' ) );
			if ( !is_page_template ( 'template-page-intro.php' ) ) {
	    		echo ( apply_filters( 'thematic_open_footer', '<footer>' ) );
	    	}
    	?>	
        	
        	<?php
        		// action hook creating the footer 
				if ( !is_page_template ( 'template-page-intro.php' ) ) {
    	    		thematic_footer();
    	    	}
        	?>
        	
		<?php
			// Filter provided for altering output of the footer closing element
//    		echo ( apply_filters( 'thematic_close_footer', '</div><!-- #footer -->' . "\n" ) );
			if ( !is_page_template ( 'template-page-intro.php' ) ) {
	    		echo ( apply_filters( 'thematic_close_footer', '</footer><!-- #footer -->' . "\n" ) );
	    	}
   
   			// action hook for placing content below the footer
			thematic_belowfooter();
    	?>
    	
	<?php
		// Filter provided for altering output of wrapping element follows the body tag  
    	if ( apply_filters( 'thematic_close_wrapper', true ) ) 
//    		echo ( '</div><!-- #wrapper .hfeed -->' . "\n" );
	

		// action hook for placing content before closing the BODY tag
		thematic_after(); 
		
		// calling WordPress' footer action hook
		wp_footer();
	?>

</body>
</html>