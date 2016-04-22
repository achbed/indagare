<?php
/*
 * Template to render the current weather into a widget.
 * 
 * Variables:
 * $current - Current temperature (in F)
 * $min - Minimum forecast temperature for the next 24 hours (in F)
 * $max - Maximum forecast temperature for the next 24 hours (in F)
 * $url - URL to get the full forecast
 * $localtime - The local time as a Time object
 * $localtime_formatted - The local time formatted for print
 */

?>
<div class="weather">
	<?php if (!empty($current)) : ?>
	<span class="temp"><?php print $current; ?>&deg;F</span>
	<?php endif; ?>
	<span class="time"><?php print $timeformat; ?></span>
</div>
