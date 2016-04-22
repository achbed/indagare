<?php
/*
 * Template to render the destinations page.
 * 
 * Variables:
 * $destinations - A fully populated list of articles - one per destination
 */

// To debug knockout.js:    <pre data-bind="text: ko.toJSON($data, null, 2)"></pre>
?>
<div id="destinationsfilter" style="height:0;">
	<h3>Filter by:</h3>
	<div class="filterwrapper">
		<select class="customselect" id="selecttop2" data-bind="
		    value: selectedTopslug, 
		    options: listTopslug,
		    optionsText: 'name',
    		optionsValue: 'value',
    		optionsAfterRender: setTid">
		</select>
	</div>
	<div class="filterwrapper">
		<select class="customselect" id="selectregion2" data-bind="
		    value: selectedRegslug, 
		    options: filteredRegslug,
		    optionsText: 'name',
    		optionsValue: 'value',
    		optionsAfterRender: setTid">
		</select>
	</div>
</div>

<?php // Keep this hidden so we can animate changes as a block and minimize "jumping" ?>
<section id="destinationstage" class="all-destinations all-articles contain" data-bind="template: {
	    foreach: filteredDest,
	    beforeRemove: hideElement,
	    afterAdd: showElement,
      afterRender: postRenderHook}" style="display:none !important;">
	<article data-bind="attr:{'class': classes}">
		<a data-bind="attr:{'href': url}">
			<img data-bind="attr:{'src': image}" alt="Article">
			<span class="info">
				<h3 data-bind="html: name"></h3>
			</span>
		</a>
	</article>
</section>

<div class="destinationlist-wrapper" style="height:0;">
	<section id="destinationlist" class="all-destinations all-articles contain">
		<?php 
			// Print the destinations list here to give good SEO and spider data.
			print $destinations; 
		?>
	</section>
</div>
