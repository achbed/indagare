<?php

define( 'SHR_FIRSTIMAGE_ALL', 255 );
define( 'SHR_FIRSTIMAGE_GALLERY', 1 );
define( 'SHR_FIRSTIMAGE_CONTENT', 2 );
define( 'SHR_FIRSTIMAGE_ATTACH', 4 );
define( 'SHR_FIRSTIMAGE_DEFAULT', ( SHR_FIRSTIMAGE_GALLERY | SHR_FIRSTIMAGE_ATTACH ) );

/**
 * Gets the first image in a gallery and its caption (if any).  If no gallery exists, then it attempts
 * to load the thumbnail image attached to the post.
 * 
 * @param $galleryname string The name of the gallery field to load.
 * @param $imgsize string The image size reference to load.  Default is 'full'
 * @param $mode int One or more of the following ANDed together.  If more than one is used,
 * loading happens in the order below.  Default is ( SHR_FIRSTIMAGE_GALLERY | SHR_FIRSTIMAGE_ATTACH ).
 *     	SHR_FIRSTIMAGE_ALL:    		Attempts to load from all available sources.
 *     	SHR_FIRSTIMAGE_GALLERY:		Loads from an attached gallery
 * 			SHR_FIRSTIMAGE_CONTENT:		Loads from the post content
 * 			SHR_FIRSTIMAGE_ATTACH:		Loads from the post image attachment
 * @param $caption boolean True loads the post's excerpt as a caption, false leaves the caption blank. Default is true.
 * @param $postid int The post ID to load.  False uses the current global $post ID.  Default is False.
 * 
 * @return array The image information.  'id' contains the post ID, 'src' is a link to the attached image, and 'caption' is the caption (if any).
 */
function _get_firstimage( $galleryname, $imgsize = 'full', $mode = SHR_FIRSTIMAGE_DEFAULT, $caption = true, $postid = false ) {
	global $post;
	
	$r = array( 'id' => null, 'src' => '', 'caption' => '', 'from' => -1 );
	$_get_firstimage_recursive = true;
	
	if ( $mode & SHR_FIRSTIMAGE_GALLERY ) {
			// Remove the mode flag, to simplify logic later.
		$mode = $mode ^ SHR_FIRSTIMAGE_GALLERY;
			
			// Get the ID
			$rowsraw = get_field( $galleryname, $postid, false );
			if ( !is_array( $rowsraw ) ) {
				$rowsraw = array( $rowsraw );
			}
			if ( !empty( $rowsraw[0] ) ) {
				// We have an ID from the gallery.
				$r['id'] = $rowsraw[0];
				$r['from'] = SHR_FIRSTIMAGE_GALLERY;
		} else if ( empty( $mode ) ) {
			// We're only supposed to get a gallery, but we don't have one.
			return $r;
			}
		}
		
		// If we don't already have an image ID, try to find one via attachment.
	if ( empty( $r['id'] ) && ( $mode & SHR_FIRSTIMAGE_ATTACH ) ) {
			// Remove the mode flag, to simplify logic later.
		$mode = $mode ^ SHR_FIRSTIMAGE_ATTACH;
					
			if ( is_null( $postid ) || ( $postid === false ) ) {
				// We have no specified post ID.
				if ( empty( $post->ID ) ) {
					// We have no global current post either.  Bail.
					return $r;
				}
				$postid = $post->ID;
			}
			
			$r['id'] = get_post_thumbnail_id( $postid );
			$r['from'] = SHR_FIRSTIMAGE_ATTACH;
		}
	
		// If we don't already have an image ID, try to find one via post content.
	if ( empty( $r['id'] ) && ( $mode & SHR_FIRSTIMAGE_CONTENT ) ) {
			// Remove the mode flag, to simplify logic later.
		$mode = $mode ^ SHR_FIRSTIMAGE_CONTENT;
			
		$r['src'] = _get_firstcontentimage( $postid );
		if ( !empty( $r['src'] ) || empty( $mode ) ) {
				// If we already have a source image, or we don't want to continue,
				$r['from'] = SHR_FIRSTIMAGE_CONTENT;
				$r['id'] = $postid;
				return $r;
			}
		}
		
		if( !empty( $r['id'] ) ) {
			// We have an ID.  Get the image.
			$imageobj = wp_get_attachment_image_src( $r['id'], $imgsize );
			
			if( !is_array( $imageobj ) ) {
				$imageobj = array( $imageobj );
			}
			
			if ( !empty( $imageobj[0] ) ) {
				$r['src'] = $imageobj[0];
			}
			
			// We have an image. Get the caption if we're supposed to.
			if( $caption && !empty( $r['src'] ) ) {
				$p = get_post( $r['id'] );
				if( !empty( $p ) && is_object( $p ) ) {
					$r['caption'] = $p->post_excerpt;
				}
			}
	}

	return $r;
}


/**
 * Gets the size of a gallery for a given post
 * 
 * @param $galleryname string The name of the gallery field to load.
 * @param $postid int The post ID to check for a gallery.  If False, uses the current post.  Default is false.
 * 
 * @return int The number of images in the gallery for this 
 */
function _get_gallerysize( $galleryname, $postid = false ) {
	$rowsraw = get_field( $galleryname, $postid, false );
	if ( empty( $rowsraw ) ) {
		return 0;
	}
	
	return count( $rowsraw );
}

// Prevents infinite loops when loading content
$_get_firstcontentimage_recursive = array();

/**
 * Strips and returns the first image in the post content (if any).
 * 
 * @param int $post_id The ID of the post to scan for an image.  False or Null uses the current post.  Default is false.
 * 
 * @return string The src parameter of the first image tag in the post content.  If no image tag is found, returns an empty string.
 */
function _get_firstcontentimage( $post_id = false ) {
	global $_get_firstcontentimage_recursive;
	
	if($post_id === false) {
		$post_id = null;
	}
	
	$the_post = get_post( $post_id );
	if( is_null( $the_post ) ) {
		return '';
	}
	
	$the_content = $the_post->post_content;
	preg_match_all( '/<img\s+.*src=[\'"]([^>\'"]+)[\'"][^>]*>/i',  $the_content, $matches );
	
	if( !empty( $matches[1][0] ) ) {
		return $matches[1][0];
	}
	
	if ( !in_array( $post_id, $_get_firstcontentimage_recursive ) ) {
		// Only apply the filter if we're scanning this post for the first time.
		$_get_firstcontentimage_recursive[] = $post_id;
		$the_content = apply_filters( 'the_content', $the_content );
		if(($key = array_search($post_id, $_get_firstcontentimage_recursive)) !== false) {
		    unset($_get_firstcontentimage_recursive[$key]);
		}
	}
	
	preg_match_all( '/<img\s+.*src=[\'"]([^>\'"]+)[\'"][^>]*>/i',  $the_content, $matches );
	
	if( empty( $matches[1][0] ) ) {
		return '';
	}
	
	return $matches[1][0];
}
