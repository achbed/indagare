<div class="join">
	<div class="filters filtersflip show-this">
		<p class="open-close">
			<span class="title membertitle"><?php the_title(); ?></span>
			<span class="rate"><?php the_field( 'field_56670c2bd87f2' ); ?></span>
			<a class="button primary" href="/signup/?mb=<?php print get_the_ID(); ?>">Join</a>
		</p>
		<div class="collapse">
			<div class="collapsegroup">
				<?php print get_the_content(); ?>
			</div>
		</div>
	</div>
</div>