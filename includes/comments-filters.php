<?php

/* comments */
function child_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $depth;
?>
    
       	<li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
    	
    		<?php 
    			// action hook for inserting content above #comment
    			thematic_abovecomment();
    		?>
    		
    		<div class="comment-author vcard"><?php thematic_commenter_link() ?></div>
    		
    			<?php thematic_commentmeta(TRUE); ?>
    		
    			<?php  
    				if ( $comment->comment_approved == '0' ) {
    					echo "\t\t\t\t\t" . '<span class="unapproved">';
    					_e( 'Your comment is awaiting moderation', 'thematic' );
    					echo ".</span>\n";
    				}
    			?>
    			
            <div class="comment-content">
            
        		<?php comment_text() ?>
        		
    		</div>
    		
			<?php // echo the comment reply link with help from Justin Tadlock http://justintadlock.com/ and Will Norris http://willnorris.com/
				
/*
				if( $args['type'] == 'all' || get_comment_type() == 'comment' ) :
					comment_reply_link( array_merge( $args, array(
						'reply_text' => __( 'Reply','thematic' ), 
						'login_text' => __( 'Log in to reply.','thematic' ),
						'depth'      => $depth,
						'before'     => '<div class="comment-reply-link">', 
						'after'      => '</div>'
					)));
				endif;

*/

			?>
			
			<?php
				// action hook for inserting content above #comment
				thematic_belowcomment() 
			?>

<?php }


function child_list_comments_arg() {
	$content = 'type=comment&callback=child_comments';
	return apply_filters('list_comments_arg', $content);
}

function childtheme_override_commentmeta($print = TRUE) {

	$content = '<div class="comment-meta">' . 
				sprintf( _x('%s <span class="meta-sep">|</span> %s', '{$time} <span class="meta-sep">|</span> {$date}', 'thematic') , 
					get_comment_time(),
					get_comment_date() );

	if ( get_edit_comment_link() ) {
		$content .=	sprintf(' <span class="meta-sep">|</span><span class="edit-link"> <a class="comment-edit-link" href="%1$s" title="%2$s">%3$s</a></span>',
					get_edit_comment_link(),
					__( 'Edit comment' , 'thematic' ),
					__( 'Edit', 'thematic' ) );
		}
	
	$content .= '</div>' . "\n";
		
	return $print ? print(apply_filters('thematic_commentmeta', $content)) : apply_filters('thematic_commentmeta', $content);


}

function comments_remove_url($arg) {
    $arg['url'] = '';
    return $arg;
}
add_filter('comment_form_default_fields', 'comments_remove_url');

function comments_remove_notes_before($arg) {
    $arg['comment_notes_before'] = '';
    return $arg;
}
add_filter('comment_form_default_fields', 'comments_remove_notes_before');

function child_comments_remove_notes_before($args) {
    $args['comment_notes_before'] = '';
    return $args;
}
add_filter('thematic_comment_form_args', 'child_comments_remove_notes_before');

function comments_remove_notes_after($arg) {
    $arg['comment_notes_after'] = '';
    return $arg;
}
add_filter('comment_form_default_fields', 'comments_remove_notes_after');

function child_comments_remove_notes_after($args) {

    $args['comment_notes_after'] = '';
    return $args;
}
add_filter('thematic_comment_form_args', 'child_comments_remove_notes_after');

function child_get_comment_author_link() {
	echo '';
}
add_filter('thematic_commenter_link','child_get_comment_author_link');

?>