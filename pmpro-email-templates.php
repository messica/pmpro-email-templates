<?php
/**
 * Plugin Name: PMPro Email Templates
 * Description: Define your own custom PMPro Email Templates in the familiar WordPress editor.
 * Author: strangerstudios
 * Author URI: http://www.strangerstudios.com
 * Version: .1
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

    if (empty($template_data['body'])) {
        //if not found, get template from PMPro email templates
        $template_data['body'] = file_get_contents( PMPRO_DIR . '/email/' . str_replace('email_', '', $template) . '.html');
    }

    if (empty($template_data['subject']) && $template != "email_header" && $template != "email_footer") {
        $template_data['subject'] = $pmproet_email_defaults[$template];
    }

    echo json_encode($template_data);
	
    die();
}
add_action('wp_ajax_pmproet_get_template_data', 'pmproet_get_template_data');


/* Filter Subject and Body */
function pmproet_email_filter($email) {

    if (pmpro_getOption($email->template . '_subject'))
        $email->subject = pmpro_getOption('email_' . $email->template . '_subject');

    if (pmpro_getOption('email_header_body'))
        $email->body = pmpro_getOption('email_header_body');

    if (pmpro_getOption($email->template . '_body')) {
        $email->body .= pmpro_getOption('email_' . $email->template . '_body');
    }
    else {
        $email->body .= file_get_contents( PMPRO_DIR . '/email/' . $email->template . '.html');
    }

    if (pmpro_getOption('email_footer_body'))
        $email->body .= pmpro_getOption('email_footer_body');

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
    $pmpro_user_meta = $wpdb->get_row("SELECT * FROM wp_pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND status='active'");

    $invoice = new MemberOrder($data['invoice_id']);

    $data = array(

        //general data
        "name" => $user->display_name,
        "user_login" => $user->user_login,
        "sitename" => get_option("blogname"),
        "siteemail" => pmpro_getOption("from_email"),
        "membership_id" => $data['membership_id'],
        "membership_level_name" => $data['membership_level_name'],
        "display_name" => $user->display_name,
        "user_email" => $user->user_email,
        "login_link" => pmpro_url("account"),
        "levels_link" => pmpro_url("levels"),
        "enddate" => date(get_option('date_format'), $user->membership_level->enddate),

        //billing and checkout
        "billing_name" => $invoice->billing->name,
		"billing_street" => $invoice->billing->street,
		"billing_city" => $invoice->billing->city,
		"billing_state" => $invoice->billing->state,
		"billing_zip" => $invoice->billing->zip,
		"billing_country" => $invoice->billing->country,
		"billing_phone" => $invoice->billing->phone,
		"cardtype" => $invoice->cardtype,
		"accountnumber" => hideCardNumber($invoice->accountnumber),
		"expirationmonth" => $invoice->expirationmonth,
		"expirationyear" => $invoice->expirationyear,
        "membership_cost" => $data['membership_cost'],
        "instructions" => wpautop(pmpro_getOption("instructions")),
        "invoice_id" => $invoice->code,
        "invoice_total" => $pmpro_currency_symbol . number_format($invoice->total, 2),
        "invoice_date" => date(get_option('date_format'), $invoice->timestamp),
        "discount_code" => $data['discount_code'],
        "invoice_link" => pmpro_url("invoice", "?invoice=" . $invoice->code),
    );

    //billing address
    $data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
        $invoice->billing->street,
        "", //address 2
        $invoice->billing->city,
        $invoice->billing->state,
        $invoice->billing->zip,
        $invoice->billing->country,
        $invoice->billing->phone);

    //membership change
    if($user->membership_level->ID)
       $data["membership_change"] = sprintf(__("The new level is %s.", "pmpro"), $user->membership_level->name);
    else
       $data["membership_change"] = __("Your membership has been cancelled", "pmpro");

    if(!empty($user->membership_level->enddate))
        $data["membership_change"] .= ". " . sprintf(__("This membership will expire on %s", "pmpro"), date(get_option('date_format'), $user->membership_level->enddate));

    elseif(!empty($email->expiration_changed))
        $data["membership_change"] .= ". " . __("This membership does not expire", "pmpro");

    //membership expiration
    $data['membership_expiration'] = '';
    if ($pmpro_user_meta->enddate)
        $data['membership_expiration'] = "<p>" . sprintf(__("This membership will expire on %s.", "pmpro"), $pmpro_user_meta->enddate . "</p>\n");

    return $data;
}
add_filter('pmpro_email_data', 'pmproet_email_data', 10, 2);
