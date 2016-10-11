<?php
/**
 * Creates an HTML snippet to implement a given hubspot form.
 *
 * @param string $portalid The PortalID for the form
 * @param string $formid The FormID for the form
 * @param boolean $return Optional. If false, the snippet is printed in place and null is returned. If true, the snippet is returned as a string and not printed.  Default is false.
 *
 * @return string|null The snippet or null.
 */
function render_hubspot( $portalid = '', $formid = '', $return = false ) {
	$r = '
<!--[if lte IE 8]>
	<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
<![endif]-->
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
<script>
  hbspt.forms.create({
    css: "",
    portalId: "'.$portalid.'",
    formId: "'.$formid.'"
  });
</script>';

	if ( empty( $return ) ) {
		print $r;
		return null;
	}
	return $r;
}
