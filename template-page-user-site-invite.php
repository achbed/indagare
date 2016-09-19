<?php
/**
 * Template Name: Site Invite Email Landing Page
 *
 * @package Thematic
 * @subpackage Templates
 */

if ( ind_logged_in() ) {
	// We should never display this if the user is currently logged in.
	wp_redirect( home_url( '/account/' ) );
	exit();
}

if ( empty( $_GET['c'] ) || empty( $_GET['h'] ) ) {
	// No data.  Redirect to ..... homepage?
	wp_redirect( home_url( '/' ) );
	exit();
}

$cid = $_GET['c'];
$hash = $_GET['h'];
$c = new \WPSF\Contact( $cid );
if ( $hash != $c->get_invite_hash() ) {
	// Hash error. Redirect to ..... homepage for now, we need to
	// build an error page
	wp_redirect( home_url( '/' ) );
	exit();
}

$errors = new WP_Error();
$error_classes = array(
	'wpsf_multilink_id' => 'invite-error',
	'wpsf_multilink_email' => 'invite-error',
	'wpsf_emailupdated' => 'invite-message',
	'wpsf_passwordreset' => 'invite-message',
);

$args = array(
	'meta_key' => 'wpsf_contactid',
	'meta_value' => $cid,
);
$wp_user_query = new WP_User_Query( $args );
$users = $wp_user_query->get_results();
if ( ! empty( $users ) ) {
	if( count( $users ) > 1 ) {
		// We have more than one user account with the requested ContactID.  Bail!
		$errors->add( 'wpsf_multilink_id', __( '<strong>ERROR</strong>: Multiple Logins tied to this Contact.' ) );
	} else {
		// We have an existing user account with that ContactID.
		$user = array_pop( $users );

		// Verify the account email.
		$userdata = get_userdata( $user->ID );
		if( $userdata->user_email != $c['Email'] ) {
			wp_update_user( array( 'ID' => $user->ID, 'user_email' => $c['Email'] ) );
			$errors->add( 'wpsf_emailupdated', __( 'The email address on your account has been updated.' ) );
		}
		reset_user_password( $user->login );

		$errors->add( 'wpsf_passwordreset', __( 'You should receive an email shortly that will contain a link to update your login information.' ) );
	}
} else {
	// Check for an account with the email address and no
	$args = array(
		'search' => $c['Email'],
		'search_columns' => array( 'user_email' ),
	);
	$wp_user_query = new WP_User_Query( $args );
	$users = $wp_user_query->get_results();
	if ( ! empty( $users ) ) {
		// We have an existing user account with that email.
		if( count( $users ) > 1 ) {
			// We have more than one user account with the requested ContactID's email.  Bail!
			$errors->add( 'wpsf_multilink_email', __( '<strong>ERROR</strong>: Multiple Logins tied to this Contact Email.' ) );
		} else {
			// We have an existing user account with that ContactID.
			$user = array_pop( $users );
			reset_user_password( $user->login );

			$errors->add( 'wpsf_passwordreset', __( 'You should receive an email shortly that will contain a link to update your login information.' ) );
		}
	} else {
		// We have no exiting account.  Yay!
	}
}

?>


<?php get_header(); ?>
<?php thematic_abovecontainer(); ?>
<div id="container" class="standard">
	<?php thematic_abovecontent(); ?>
	<?php echo apply_filters( 'thematic_open_id_content', '<div id="content">' . "\n" ); ?>
		<?php get_sidebar('page-top'); ?>
		<?php thematic_abovepost(); ?>
		<div id="post-site-invite">
			<?php thematic_postheader(); ?>
			<div class="entry-content">
				<?php if ( $errors->get_error_code() === '' ): // We don't have any errors.  Present the form. ?>
					<div id="signup-form-container">
						<div class="tab">
							<div class="tab-content">

							    <h2>Account Information</h2>

							    <form id="accountinfo-form" class="editing clearfix">
									<div field-instance="username" id="field-wp-username" class="input-field field clearfix iform-row-3col iform-row-clear">
										<input name="username" id="wp-username" type="text">
										<label for="wp-username">Username</label>
									    <span class="errmsg">Username is not available.  Please try another one.</span>
									</div>

									<div field-instance="password1" id="field-wp-password1" class="input-field field clearfix iform-row-3col">
										<input name="pwd1" id="wp-password1" type="password" validate-group="pw" validate-type="password">
										<label for="wp-password1">Password</label>
									    <span class="errmsg">Passwords must:<br/>
									    	<ul>
											 	<li id="passlen">Be at least <span id="passlen_num">6</span> characters long</li>
											 	<li id="passcase">Contain a mix of uppercase and lowercase letters</li>
											 	<li id="passnum">Contain at least one number</li>
											 	<li id="passchar">Contain at least one special character (non-letter or number)</li>
										 	</ul>
									 	</span>
									</div>

									<div field-instance="password2" id="field-wp-password2" class="input-field field clearfix iform-row-3col">
										<input name="pwd2" id="wp-password2" type="password" validate-group="pw" validate-type="password-verify">
										<label for="wp-password2">Verify Password</label>
									    <span class="errmsg">Passwords must match.</span>
									</div>

								</form>

							    <div class="inputgroup hidden">
								    <input type="hidden" name="ContactID" id="ContactID" value="<?php print $cid; ?>">
						        </div>

							    <div class="inputgroup">
						        	<div class="field"><label></label><input type="Button" name="subTab3" id="subTab3" class="button primary" value="Create Login"></div>
						    	</div>
							</div>
						</div>
					</div>
				<?php else: ?>
					<div id="invite-messages">
					<?php foreach ( $errors->get_error_codes() as $code ): ?>
						<div class="invite-message-item <?php if ( ! empty( $error_classes[$code] ) ) { print $error_classes[$code]; } ?>">
							<?php print $errors->get_error_message( $code ); ?>
						</div>
					<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div><!-- .entry-content -->
		</div><!-- #post -->
		<?php thematic_belowpost(); ?>
		<?php get_sidebar( 'page-bottom' ); ?>

	</div><!-- #content -->
	<?php thematic_belowcontent(); ?>
</div><!-- #container -->

<?php thematic_belowcontainer(); ?>
<?php thematic_sidebar(); ?>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery.scrollTo.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery-confirm.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/shr.validate.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/invite.js"></script>
<?php get_footer(); ?>
