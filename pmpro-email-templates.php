<?php
/**
 * Plugin Name: PMPro Email Templates
 * Description: Define your own custom PMPro HTML Email Templates.
 * Author: Stranger Studios
 * Author URI: http://www.strangerstudios.com
 * Plugin URI: http://www.paidmembershipspro.com/add-ons/plugins-wordpress-repository/email-templates-admin-editor/
 * Version: .5.2
 */

/* Email Template Default Subjects (body is read from template files in /email/ ) */
global $pmproet_email_defaults;
$pmproet_email_defaults = array(
    'email_default' => __("An Email From !!sitename!!", "pmpro"),
    'email_admin_change' => __("Your membership at !!sitename!! has been changed", "pmpro"),
    'email_admin_change_admin' => __("Membership for !!user_login!! at !!sitename!! has been changed", "pmpro"),
    'email_billing' => __("Your billing information has been udpated at !!sitename!!", "pmpro"),
    'email_billing_admin' => __("Billing information has been udpated for !!user_login!! at !!sitename!!", "pmpro"),
    'email_billing_failure' => __("Membership Payment Failed at !!sitename!!", "pmpro"),
    'email_billing_failure_admin' => __("Membership Payment Failed For !!display_name!! at !!sitename!!", "pmpro"),
    'email_cancel' => __("Your membership at !!sitename!! has been CANCELLED", "pmpro"),
    'email_cancel_admin' => __("Membership for !!user_login!! at !!sitename!! has been CANCELLED", "pmpro"),
    'email_checkout_check' => __("Your membership confirmation for !!sitename!!", "pmpro"),
    'email_checkout_check_admin' => __("Member Checkout for !!membership_level_name!! at !!sitename!!", "pmpro"),
    'email_checkout_express' => __("Your membership confirmation for !!sitename!!", "pmpro"),
    'email_checkout_express_admin' => __("Member Checkout for !!membership_level_name!! at !!sitename!!", "pmpro"),
    'email_checkout_free' => __("Your membership confirmation for !!sitename!!", "pmpro"),
    'email_checkout_free_admin' => __("Member Checkout for !!membership_level_name!! at !!sitename!!", "pmpro"),
    'email_checkout_freetrial' => __("Your membership confirmation for !!sitename!!", "pmpro"),
    'email_checkout_freetrial_admin' => __("Member Checkout for !!membership_level_name!! at !!sitename!!", "pmpro"),
    'email_checkout_paid' => __("Your membership confirmation for !!sitename!!", "pmpro"),
    'email_checkout_paid_admin' => __("Member Checkout for !!membership_level_name!! at !!sitename!!", "pmpro"),
    'email_checkout_trial' => __("Your membership confirmation for !!sitename!!", "pmpro"),
    'email_checkout_trial_admin' => __("Member Checkout for !!membership_level_name!! at !!sitename!!", "pmpro"),
    'email_credit_card_expiring' => __("Credit Card on File Expiring Soon at !!sitename!!", "pmpro"),
    'email_invoice' => __("INVOICE for !!sitename!! membership", "pmpro"),
    'email_membership_expired' => __("Your membership at !!sitename!! has ended", "pmpro"),
    'email_membership_expiring' => __("Your membership at !!sitename!! will end soon", "pmpro"),
    'email_trial_ending' => __("Your trial at !!sitename!! is ending soon", "pmpro"),
);

function pmproet_setup() {
    add_submenu_page('pmpro-membershiplevels', __('Email Templates', 'pmpro'), __('Email Templates', 'pmpro'), 'manage_options', 'pmpro-email-templates', 'pmproet_admin_page');
}
add_action('admin_menu', 'pmproet_setup');

function pmproet_admin_page()
{
    require_once( plugin_dir_path(__FILE__) ) . "adminpages/emailtemplates.php";
}

//enqueue js/css
function pmproet_scripts() {
    if (!empty($_REQUEST['page']) && $_REQUEST['page'] == 'pmpro-email-templates') {
        wp_enqueue_script('pmproet', plugin_dir_url(__FILE__) . 'js/pmproet.js', array('jquery'), null, false);
        wp_enqueue_style('pmproet', plugin_dir_url(__FILE__) . 'css/pmproet.css');
    }
}
add_action('admin_enqueue_scripts', 'pmproet_scripts');

/*
 * AJAX Functions
 */

//get template data
function pmproet_get_template_data() {

    global $pmproet_email_defaults;

    $template = $_REQUEST['template'];

    //get template data
    $template_data['body'] = pmpro_getOption($template . '_body');
    $template_data['subject'] = pmpro_getOption($template . '_subject');
    $template_data['disabled'] = pmpro_getOption($template . '_disabled');

    if (empty($template_data['body'])) {
        //if not found, get template from PMPro email templates
        $template_data['body'] = file_get_contents( PMPRO_DIR . '/email/' . str_replace('email_', '', $template) . '.html');
    }

    if (empty($template_data['subject']) && $template != "email_header" && $template != "email_footer") {
        $template_data['subject'] = $pmproet_email_defaults[$template];
    }

    echo json_encode($template_data);
	
    exit;
}
add_action('wp_ajax_pmproet_get_template_data', 'pmproet_get_template_data');

//save template data
function pmproet_save_template_data() {

    //update this template's settings
    pmpro_setOption($_REQUEST['template'] . '_subject', stripslashes($_REQUEST['subject']));
    pmpro_setOption($_REQUEST['template'] . '_body', stripslashes($_REQUEST['body']));
    echo 'Template Saved';
    
	exit;
}
add_action('wp_ajax_pmproet_save_template_data', 'pmproet_save_template_data');

//reset template data
function pmproet_reset_template_data() {

    global $pmproet_email_defaults;

    $template = $_REQUEST['template'];

    delete_option('pmpro_' . $template . '_subject');
    delete_option('pmpro_' . $template . '_body');

    $template_data['subject'] = $pmproet_email_defaults[$template];
    $template_data['body'] = file_get_contents( PMPRO_DIR . '/email/' . str_replace('email_', '', $template) . '.html');

    echo json_encode($template_data);
    
	exit;
}
add_action('wp_ajax_pmproet_reset_template_data', 'pmproet_reset_template_data');

// disable template
function pmproet_disable_template() {
    $template = $_REQUEST['template'];
    $response['result'] = update_option('pmpro_' . $template . '_disabled', $_REQUEST['disabled']);
    $response['status'] = $_REQUEST['disabled'];
    echo json_encode($response);
	exit;
}
add_action('wp_ajax_pmproet_disable_template', 'pmproet_disable_template');



/* Filter Subject and Body */
function pmproet_email_filter($email) {
    
    //is this email disabled?
    if(pmpro_getOption('email_' . $email->template . '_disabled') == 'true')
        return false;

    $et_subject = pmpro_getOption('email_' . $email->template . '_subject');
    $et_header = pmpro_getOption('email_header_body');
    $et_body = pmpro_getOption('email_' . $email->template . '_body');
    $et_footer = pmpro_getOption('email_footer_body');

    if(file_exists( PMPRO_DIR . '/email/' . str_replace('email_', '', $email->template) . '.html')) {
        $default_body = file_get_contents( PMPRO_DIR . '/email/' . str_replace('email_', '', $email->template) . '.html');
    }
    else {
        $default_body = $email->body;
    }

    if($et_subject)
        $email->subject = $et_subject;

    //is header disabled?
    if(pmpro_getOption('email_header_disabled') != 'true') {
        if($et_header)
            $temp_content = $et_header;
        else
            $temp_content = file_get_contents( PMPRO_DIR . '/email/header.html');
    }

    if($et_body)
        $temp_content .= $et_body;
    else
        $temp_content .= $default_body;

    //is footer disabled?
    if(pmpro_getOption('email_footer_disabled') != 'true') {
        if($et_footer)
            $temp_content .= $et_footer;
        else
            $temp_content .= file_get_contents( PMPRO_DIR . '/email/footer.html');
    }
    
    $email->body = $temp_content;

    //replace data
    foreach($email->data as $key => $value)
    {
        $email->body = str_replace("!!" . $key . "!!", $value, $email->body);
        $email->subject = str_replace("!!" . $key . "!!", $value, $email->subject);
    }

    return $email;
}
add_filter('pmpro_email_filter', 'pmproet_email_filter');

/* Filter for Variables */
function pmproet_email_data($data, $email) {

    global $current_user, $pmpro_currency_symbol, $wpdb;

    $user = get_user_by('login', $data['user_login']);
    if(!$user)
        $user = $current_user;
    $pmpro_user_meta = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND status='active'");

	//make sure data is an array
	if(!is_array($data))
		$data = array();
	
	//general data	
    $new_data['sitename'] = get_option("blogname");
	$new_data['siteemail'] = pmpro_getOption("from_email");
	if(empty($new_data['login_link']))
		$new_data['login_link'] = wp_login_url;
	$new_data['levels_link'] = pmpro_url("levels");        
	
	//user data
	if(!empty($user))
	{
		$new_data['name'] = $user->display_name;
		$new_data['user_login'] = $user->user_login;
		$new_data['display_name'] = $user->display_name;
		$new_data['user_email'] = $user->user_email;
	}
	
	//membership data
	if(!empty($user->membership_level))
		$new_data['enddate'] = date(get_option('date_format'), $user->membership_level->enddata);
	
	//invoice data
	if(!empty($data['invoice_id']))
	{
	    $invoice = new MemberOrder($data['invoice_id']);
		if(!empty($invoice))
		{
			$new_data['billing_name'] = $invoice->billing->name;
			$new_data['billing_street'] = $invoice->billing->street;
			$new_data['billing_city'] = $invoice->billing->city;
			$new_data['billing_state'] = $invoice->billing->state;
			$new_data['billing_zip'] = $invoice->billing->zip;
			$new_data['billing_country'] = $invoice->billing->country;
			$new_data['billing_phone'] = $invoice->billing->phone;
			$new_data['cardtype'] = $invoice->cardtype;
			$new_data['accountnumber'] = hideCardNumber($invoice->accountnumber);
			$new_data['expirationmonth'] = $invoice->expirationmonth;
			$new_data['expirationyear'] = $invoice->expirationyear;
			$new_data['instructions'] = wpautop(pmpro_getOption('instructions'));
			$new_data['invoice_id'] = $invoice->code;
			$new_data['invoice_total'] = $pmpro_currency_symbol . number_format($invoice->total, 2);
			$new_data['invoice_link'] = pmpro_url('invoice', '?invoice=' . $invoice->code);
			
			 //billing address
			$new_data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
				$invoice->billing->street,
				"", //address 2
				$invoice->billing->city,
				$invoice->billing->state,
				$invoice->billing->zip,
				$invoice->billing->country,
				$invoice->billing->phone);
		}
	}        

    //membership change
    if(!empty($user->membership_level) && !empty($user->membership_level->ID))
       $new_data["membership_change"] = sprintf(__("The new level is %s.", "pmpro"), $user->membership_level->name);
    else
       $new_data["membership_change"] = __("Your membership has been cancelled", "pmpro");

    if(!empty($user->membership_level) && !empty($user->membership_level->enddate))
        $new_data["membership_change"] .= ". " . sprintf(__("This membership will expire on %s", "pmpro"), date(get_option('date_format'), $user->membership_level->enddate));

    elseif(!empty($email->expiration_changed))
        $new_data["membership_change"] .= ". " . __("This membership does not expire", "pmpro");

    //membership expiration
    $new_data['membership_expiration'] = '';
    if(!empty($pmpro_user_meta->enddate))
        $new_data['membership_expiration'] = "<p>" . sprintf(__("This membership will expire on %s.", "pmpro"), $pmpro_user_meta->enddate . "</p>\n");

	//now replace any new_data not already in data
	foreach($new_data as $key => $value)
	{
		if(!isset($data[$key]))
			$data[$key] = $value;
	}
		
    return $data;
}
add_filter('pmpro_email_data', 'pmproet_email_data', 10, 2);
