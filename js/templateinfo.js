function is_home() {
  return jQuery('body').hasClass('home');
}

function is_front_page() {
	// Need a reliable test for this
	return false;
}

function is_single( a ) {
	var b = jQuery('body');
	if(!a) {
		return b.hasClass('single');
	}
	if(b.hasClass('single') == false) {
		return false;
	}
	if(jQuery.isNumber(a))
		return ( b.hasClass('page-id-' + a) || b.hasClass('postid-' + a) );
	
	return false;
}

function is_template( t ) {
	var n = t.replace('/_/','-');
	return jQuery('body.page-template-' + n).length;
}


// page page-id-21552 page-template page-template-template-page-map page-template-template-page-map-php logged-in map windows firefox ff37


/*
is_front_page() 

The Administration Panels
is_admin()
When the Dashboard or the administration panels are being displayed.
is_network_admin()
When the Network Dashboard or the Network administration panels for multisite are being displayed.
Attention: The wp-login.php page is not an admin page. To check if this page is displayed, use the admin global variable $pagenow.

The Admin Bar
is_admin_bar_showing() 
Returns true if the admin bar will be displayed.
Note : To display or not this bar, use show_admin_bar(), this function should be called immediately upon plugins_loaded or placed in the theme's functions.php file.

A Single Post Page
is_single() 
When a single post of any post type (except attachment and page post types) is being displayed.
is_single( '17' ) 
When Post 17 is being displayed as a single Post.
is_single( 'Irish Stew' ) 
When the Post with Title "Irish Stew" is being displayed as a single Post.
is_single( 'beef-stew' ) 
When the Post with Post Slug "beef-stew" is being displayed as a single Post.
is_single( array( 17, 'beef-stew', 'Irish Stew' ) ) 
Returns true when the single post being displayed is either post ID 17, or the post_name is "beef-stew", or the post_title is "Irish Stew".
is_single( array( 17, 19, 1, 11 ) ) 
Returns true when the single post being displayed is either post ID = 17, post ID = 19, post ID = 1 or post ID = 11.
is_single( array( 'beef-stew', 'pea-soup', 'chili' ) ) 
Returns true when the single post being displayed is either the post_name "beef-stew", post_name "pea-soup" or post_name "chili".
is_single( array( 'Beef Stew', 'Pea Soup', 'Chili' ) ) 
Returns true when the single post being displayed is either the post_title is "Beef Stew", post_title is "Pea Soup" or post_title is "Chili".
Note: This function does not distinguish between the post ID, post title, or post name. A post named "17" would be displayed if a post ID of 17 was requested. Presumably the same holds for a post with the slug "17".

A Sticky Post
is_sticky() 
Returns true if "Stick this post to the front page" check box has been checked for the current post. In this example, no post ID argument is given, so the post ID for the Loop post is used.
is_sticky( '17' ) 
Returns true when Post 17 is considered a sticky post.
A Post Type is Hierarchical
is_post_type_hierarchical( $post_type ) 
Returns true if this $post_type has been set with hierarchical support when registered.
is_post_type_hierarchical( 'book' ) 
Returns true if the book post type was registered as having support for hierarchical.
A Post Type Archive
is_post_type_archive() 
Returns true on any post type archive.
is_post_type_archive( $post_type ) 
Returns true if on a post type archive page that matches $post_type.
is_post_type_archive( array( 'foo', 'bar', 'baz' ) ) 
Returns true if on a post type archive page that matches either "foo", "bar", or "baz".
To turn on post type archives, use 'has_archive' => true, when registering the post type.

A Comments Popup
is_comments_popup() 
When in Comments Popup window.
Any Page Containing Posts
comments_open()
When comments are allowed for the current Post being processed in the WordPress Loop.
pings_open()
When pings are allowed for the current Post being processed in the WordPress Loop.
A PAGE Page
This section refers to WordPress Pages, not any generic webpage from your blog, or in other words to the built in post_type 'page'.

is_page() 
When any Page is being displayed.
is_page( 42 ) 
When Page 42 (ID) is being displayed.
is_page( 'About Me And Joe' ) 
When the Page with a post_title of "About Me And Joe" is being displayed.
is_page( 'about-me' ) 
When the Page with a post_name (slug) of "about-me" is being displayed.
is_page( array( 42, 'about-me', 'About Me And Joe' ) ) 
Returns true when the Pages displayed is either post ID = 42, or post_name is "about-me", or post_title is "About Me And Joe".
is_page( array( 42, 54, 6 ) ) 
Returns true when the Pages displayed is either post ID = 42, or post ID = 54, or post ID = 6.
See also is_page() for more snippets.

Note: There is no function to check if a page is a sub-page. We can get around the problem:

if ( is_page() && $post->post_parent > 0 ) { 
    echo "This is a child page";
}
Is a Page Template
Allows you to determine whether or not you are in a page template or if a specific page template is being used.

is_page_template() 
Is a Page Template being used?
is_page_template( 'about.php' ) 
Is Page Template 'about' being used?
Note: if the file is in a subdirectory you must include this as well. Meaning that this should be the filepath in relation to your theme as well as the filename, for example page-templates/about.php.

A Category Page
is_category( $category ) 
When the actual page is associated with the $category Category.
is_category( '9' ) 
When the archive page for Category 9 is being displayed.
is_category( 'Stinky Cheeses' ) 
When the archive page for the Category with Name "Stinky Cheeses" is being displayed.
is_category( 'blue-cheese' ) 
When the archive page for the Category with Category Slug "blue-cheese" is being displayed.
is_category( array( 9, 'blue-cheese', 'Stinky Cheeses' ) ) 
Returns true when the category of posts being displayed is either term_ID 9, or slug "blue-cheese", or name "Stinky Cheeses".
in_category( '5' ) 
Returns true if the current post is in the specified category id (read more).
in_category( array( 1,2,3 ) ) 
Returns true if the current post is in either category 1, 2, or 3.
! in_category( array( 4,5,6 ) ) 
Returns true if the current post is NOT in either category 4, 5, or 6. Note the ! at the beginning.
Note: Be sure to check your spelling when testing: "is" and "in" are significantly different.

See also is_archive() and Category Templates.

A Tag Page
is_tag() 
When any Tag archive page is being displayed.
is_tag( 'mild' ) 
When the archive page for tag with the slug of 'mild' is being displayed.
is_tag( array( 'sharp', 'mild', 'extreme' ) ) 
Returns true when the tag archive being displayed has a slug of either "sharp", "mild", or "extreme".
has_tag() 
When the current post has a tag. Prior to 2.7, must be used inside The Loop.
has_tag( 'mild' ) 
When the current post has the tag 'mild'.
has_tag( array( 'sharp', 'mild', 'extreme' ) ) 
When the current post has any of the tags in the array.
See also is_archive() and Tag Templates.

A Taxonomy Page (and related)
is_tax

is_tax() 
When any Taxonomy archive page is being displayed.
is_tax( 'flavor' ) 
When a Taxonomy archive page for the flavor taxonomy is being displayed.
is_tax( 'flavor', 'mild') 
When the archive page for the flavor taxonomy with the slug of 'mild' is being displayed.
is_tax( 'flavor', array( 'sharp', 'mild', 'extreme' ) ) 
Returns true when the flavor taxonomy archive being displayed has a slug of either "sharp", "mild", or "extreme".
has_term

has_term() 
Check if the current post has any of given terms. The first parameter should be an empty string. It expects a taxonomy slug/name as a second parameter.
has_term( 'green', 'color' ) 
When the current post has the term 'green' from taxonomy 'color'.
has_term( array( 'green', 'orange', 'blue' ), 'color' ) 
When the current post has any of the terms in the array.
term_exists

term_exists( $term, $taxonomy, $parent ) 
Returns true if $term exists in any taxonomy. If $taxonomy is given, the term must exist in this one. The 3rd parameter $parent is also optional, if given, the term have to be a child of this parent, the taxonomy must be hierarchical.
is_taxonomy_hierarchical

is_taxonomy_hierarchical( $taxonomy ) 
Returns true if the taxonomy $taxonomy is hierarchical. To declare a taxonomy hierarchical, use 'hierarchical' => true when using register_taxonomy().
taxonomy_exists

taxonomy_exists( $taxonomy ) 
Returns true if $taxonomy has been registered on this site using register_taxonomy().
See also is_archive().

An Author Page
is_author() 
When any Author page is being displayed.
is_author( '4' ) 
When the archive page for Author number (ID) 4 is being displayed.
is_author( 'Vivian' ) 
When the archive page for the Author with Nickname "Vivian" is being displayed.
is_author( 'john-jones' ) 
When the archive page for the Author with Nicename "john-jones" is being displayed.
is_author( array( 4, 'john-jones', 'Vivian' ) ) 
When the archive page for the author is either user ID 4, or user_nicename "john-jones", or nickname "Vivian".
See also is_archive() and Author Templates.

A Multi-author Site
is_multi_author( ) 
When more than one author has published posts for a site. Available with Version 3.2.
A Date Page
is_date() 
When any date-based archive page is being displayed (i.e. a monthly, yearly, daily or time-based archive).
is_year() 
When a yearly archive is being displayed.
is_month() 
When a monthly archive is being displayed.
is_day() 
When a daily archive is being displayed.
is_time() 
When an hourly, "minutely", or "secondly" archive is being displayed.
is_new_day() 
If today is a new day according to post date. Should be used inside the loop.
See also is_archive().

Any Archive Page
is_archive() 
When any type of Archive page is being displayed. Category, Tag, other Taxonomy Term, custom post type archive, Author and Date-based pages are all types of Archives.
A Search Result Page
is_search() 
When a search result page archive is being displayed.
A 404 Not Found Page
is_404() 
When a page displays after an "HTTP 404: Not Found" error occurs.
A Paged Page
is_paged() 
When the page being displayed is "paged". This refers to an archive or the main page being split up over several pages and will return true on 2nd and subsequent pages of posts. This does not refer to a Post or Page whose content has been divided into pages using the <!--nextpage--> QuickTag. To check if a Post or Page has been divided into pages using the <!--nextpage--> QuickTag, see A_Paged_Page section.
An Attachment
is_attachment() 
When an attachment document to a post or Page is being displayed. An attachment is an image or other file uploaded through the post editor's upload utility. Attachments can be displayed on their own 'page' or template.
See also Using Image and File Attachments.

Attachment Is Image
wp_attachment_is_image( $post_id ) 
Returns true if the attached file to the post with ID equal to $post_id is an image. Mime formats and extensions allowed are: .jpg, .jpeg, .gif, et .png.
A Local Attachment
is_local_attachment( $url ) 
Returns true if the link passed in $url is a real attachment file from this site.
A Single Page, a Single Post, an Attachment or Any Other Custom Post Type
is_singular() 
Returns true for any is_single(), is_page(), or is_attachment().
is_singular( 'foo' ) 
Returns true if the post_type is "foo".
is_singular( array( 'foo', 'bar', 'baz' ) ) 
Returns true if the post_type is "foo", "bar", or "baz".
*/
