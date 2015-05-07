<?php

global $pmproet_email_functions, $pmproet_email_defaults, $pmproet_test_order_id;

/*
 * Make sure we have a test order for testing emails
 */
$pmproet_test_order_id = get_option('pmproet_test_order_id');
$test_order = new MemberOrder($pmproet_test_order_id);
if(empty($test_order->id)) {
    $test_order->notes = __('This is a test order used with the PMPro Email Templates addon.', 'pmpro');
    $test_order->saveOrder();
    $pmproet_test_order_id = $test_order->id;
    update_option('pmproet_test_order_id', $pmproet_test_order_id);
}

/*
 * Email Template Default Subjects (body is read from template files in /email/ )
 */
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

