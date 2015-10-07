<?php
//only admins can get this
if ( ! function_exists( "current_user_can" ) || ( ! current_user_can( "manage_options" ) && ! current_user_can( "pmpro_emailtemplates" ) ) ) {
	die( __( "You do not have permissions to perform this action.", "pmproet" ) );
}

global $pmproet_test_order_id, $wpdb, $msg, $msgt, $pmproet_email_defaults, $current_user;
$pmproet_test_order_id = get_option( 'pmproet_test_order_id' );

require_once( PMPRO_DIR . "/adminpages/admin_header.php" );
?>

	<form action="" method="post" enctype="multipart/form-data">
	<h2><?php _e( 'Email Templates', 'pmproet' ); ?></h2>
	<table class="form-table">
	<tr class="status hide-while-loading" style="display:none;">
		<th scope="row" valign="top"></th>
		<td>
			<div id="message">
				<p class="status_message"></p>
			</div>

		</td>
	</tr>
	<tr>
	<th scope="row" valign="top">
		<label for="pmpro_email_template_switcher">Email Template</label>
	</th>
	<td>
	<select name="pmpro_email_template_switcher" id="pmpro_email_template_switcher">
	<option value="" selected="selected">--- Select a Template to Edit ---</option>
	<option value="header"><?php _e('Email Header', 'pmproet'); ?></option>
	<option value="footer"><?php _e('Email Footer', 'pmproet'); ?></option>
	<?php foreach ( $pmproet_email_defaults as $key => $template ): ?>
	<option value="<?php echo $key; ?>"><?php echo $template['description']; ?></option>
	<?php endforeach; ?>
	</select>
	<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" id="pmproet-spinner" style="display:none;"/>
	<hr>
	</td>
	</tr>
	<tr class="hide-while-loading">
		<th scope="row" valign="top"></th>
		<td>
			<label><input id="email_template_disable" name="email_template_disable" type="checkbox"/><span
					id="disable_label">Disable this email?</span></label>

			<p id="disable_description" class="description small">Emails with this template will not be sent.</p>
		</td>
	</tr>
	<tr class="hide-while-loading">
		<th scope="row" valign="top"><label for="email_template_subject">Subject</label></th>
		<td>
			<input id="email_template_subject" name="email_template_subject" type="text" size="100"/>
		</td>
	</tr>
	<tr class="hide-while-loading">
		<th scope="row" valign="top"><label for="email_template_body">Body</label></th>
		<td>
			<div id="template_editor_container">
				<textarea rows="10" cols="80" name="email_template_body" id="email_template_body"></textarea>
			</div>
		</td>
	</tr>
	<tr class="hide-while-loading">
		<th scope="row" valign="top"></th>
		<td>
			<?php _e( 'Send a test email to ', 'pmproet' ); ?>
			<input id="test_email_address" name="test_email_address" type="text"
			       value="<?php echo $current_user->user_email; ?>"/>
			<input id="send_test_email" class="button" name="send_test_email" value="Save Template and Send Email"
			       type="button"/>

			<p class="description">
				<a hre="<?php echo add_query_arg( array( 'page'  => 'pmpro-orders',
				                                          'order' => $pmproet_test_order_id
				), admin_url( 'admin.php' ) ); ?>"
				   target="_blank"><?php _e( 'Click here to edit the order used for test emails.', 'pmproet' ); ?></a>
				<?php _e( 'Your current membership will be used for any membership level data.', 'pmproet' ); ?>
			</p>
		</td>
	</tr>
	<tr class="controls hide-while-loading">
		<th scope="row" valign="top"></th>
		<td>
			<p class="submit">
				<input id="submit_template_data" name="save_template" type="button" class="button-primary"
				       value="Save Template"/>
				<input id="reset_template_data" name="reset_template" type="button" class="button"
				       value="Reset Template"/>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row" valign="top"></th>
		<td>
			<h3>Variable Reference</h3>

			<div id="template_reference" style="overflow:scroll;height:250px;width:800px;;">
				<table class="widefat striped">
					<tr>
						<th colspan=2>General Settings / Membership Info</th>
					</tr>
					<tr>
						<td>!!name!!</td>
						<td> Display Name (Profile/Edit User > Display name publicly as)</td>
					</tr>
					<tr>
						<td>!!user_login!!</td>
						<td> Username</td
					</tr>
					<tr>
						<td>!!sitename!!</td>
						<td> Site Title</td
					</tr>
					<tr>
						<td>!!siteemail!!</td>
						<td> Site Email Address (General Settings > Email OR Memberships > Email Settings)</td
					</tr>
					<tr>
						<td>!!membership_id!!</td>
						<td> Membership Level ID</td
					</tr>
					<tr>
						<td>!!membership_level_name!!</td>
						<td> Membership Level Name</td
					</tr>
					<tr>
						<td>!!membership_change!!</td>
						<td> Membership Level Change</td
					</tr>
					<tr>
						<td>!!membership_expiration!!</td>
						<td> Membership Level Expiration</td
					</tr>
					<tr>
						<td>!!display_name!!</td>
						<td> Display Name (Profile/Edit User > Display name publicly as)</td
					</tr>
					<tr>
						<td>!!enddate!!</td>
						<td> User Subscription End Date</td
					</tr>
					<tr>
						<td>!!user_email!!</td>
						<td> User Email</td
					</tr>
					<tr>
						<td>!!login_link!!</td>
						<td> Login URL</td
					</tr>
					<tr>
						<td>!!levels_link!!</td>
						<td> Membership Levels Page URL</td
					</tr>
					<tr>
						<th colspan=2>Billing Information</th>
					</tr>
					<tr>
						<td>!!billing_address!!</td>
						<td> Billing Info Complete Address</td
					</tr>
					<tr>
						<td>!!billing_name!!</td>
						<td> Billing Info Name</td
					</tr>
					<tr>
						<td>!!billing_street!!</td>
						<td> Billing Info Street Address</td
					</tr>
					<tr>
						<td>!!billing_city!!</td>
						<td> Billing Info City</td
					</tr>
					<tr>
						<td>!!billing_state!!</td>
						<td> Billing Info State</td
					</tr>
					<tr>
						<td>!!billing_zip!!</td>
						<td> Billing Info ZIP Code</td
					</tr>
					<tr>
						<td>!!billing_country!!</td>
						<td> Billing Info Country</td
					</tr>
					<tr>
						<td>!!billing_phone!!</td>
						<td> Billing Info Phone #</td
					</tr>
					<tr>
						<td>!!cardtype!!</td>
						<td> Credit Card Type</td
					</tr>
					<tr>
						<td>!!accountnumber!!</td>
						<td> Credit Card Number (last 4 digits))</td
					</tr>
					<tr>
						<td>!!expirationmonth!!</td>
						<td> Credit Card Expiration Month (mm format)</td
					</tr>
					<tr>
						<td>!!expirationyear!!</td>
						<td> Credit Card Expiration Year (yyyy format)</td
					</tr>
					<tr>
						<td>!!membership_cost!!</td>
						<td> Membership Level Cost Text</td
					</tr>
					<tr>
						<td>!!instructions!!</td>
						<td> Payment Instructions (used in Checkout - Email Template)</td
					</tr>
					<tr>
						<td>!!invoice_id!!</td>
						<td> Invoice ID</td
					</tr>
					<tr>
						<td>!!invoice_total!!</td>
						<td> Invoice Total</td
					</tr>
					<tr>
						<td>!!invoice_date!!</td>
						<td> Invoice Date</td
					</tr>
					<tr>
						<td>!!discount_code!!</td>
						<td> Discount Code Applied</td
					</tr>
					<tr>
						<td>!!invoice_link!!</td>
						<td> Invoice Page URL</td
					</tr>
				</table>
			</div>
		</td>
	</tr>
	</table>
	<?php wp_nonce_field( 'pmproet', 'security' ); ?>
	</form>

	<?php
	require_once( PMPRO_DIR . "/adminpages/admin_footer.php" );
	?>