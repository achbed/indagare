jQuery(document).ready(function($) {

	// The number of the next page to load (/page/x/).
	var pageNum = parseInt(pbd_alp.startPage) + 1;
	
	// The maximum number of pages the current query can return.
	var max = parseInt(pbd_alp.maxPages);
	
	// The link of the next page of posts.
	var nextLink = pbd_alp.nextLink;
	
	/**
	 * Replace the traditional navigation with our own,
	 * but only if there is at least one page of new posts to load.
	 */
	if(pageNum <= max) {
		// Insert the "More Posts" link.
//		$('#content')
//			.append('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')
//			.append('<p id="pbd-alp-load-posts"><a href="#">Load More Posts</a></p>');
			
//		$('#content')
//			.append('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')
//		$('#content section.results p.load-more')
//			.before('<div class="pbd-alp-placeholder-'+ pageNum +' "></div>')
		$('#content section.results p.load-more')
			.before('<div class="pbd-alp-placeholder-'+ pageNum +' placeholder"></div>')

		$('.load-more')	
			.append('<a href="#">Show more</a>');
			
		// Remove the traditional navigation.
		$('.navigation').remove();
	}
	
	
	/**
	 * Load new posts when the link is clicked.
	 */
//	$('#pbd-alp-load-posts a').click(function() {
	$('.load-more a').click(function() {
	
		// Are there more posts to load?
		if(pageNum <= max) {
		
			// Show that we're working.
			$(this).html('Loading...');
			
			console.log(nextLink);
			
//			$('.pbd-alp-placeholder-'+ pageNum).load(nextLink + ' .post',
			$('.pbd-alp-placeholder-'+ pageNum).load(nextLink + ' .hentry',
				function() {
					// Update page number and nextLink.
					pageNum++;
					nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);
					
					// Add a new placeholder, for when user clicks again.
//					$('#pbd-alp-load-posts')
//						.before('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')

//					$('#content')

//					$('#content section.results')
//					.append('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')
//					$('#content section.results p.load-more')
//					.before('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')
					$('#content section.results p.load-more')
					.before('<div class="pbd-alp-placeholder-'+ pageNum +' placeholder"></div>')
					
						
					// Update the button message.
					if(pageNum <= max) {
//						$('#pbd-alp-load-posts a').text('Load More Posts');
						$('.load-more a').html('Show more');
					} else {
//						$('#pbd-alp-load-posts a').text('No more posts to load.');
						$('.load-more a').html('No more to load');
					}

				}
			);
		} else {
//			$('#pbd-alp-load-posts a').append('.');
			$('.load-more a').append('.');
		}	
		
		return false;
	});
});