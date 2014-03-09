<?php
//only admins can get this
if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_emailtemplates")))
{
    die(__("You do not have permissions to perform this action.", "pmpro"));
}

global $wpdb, $msg, $msgt, $pmproet_email_defaults;

//save settings
global $pmpro_pages;
if(!empty($_REQUEST['savesettings']))
{
    fb('savesettings request');
    fb($_REQUEST);

    //update this template's settings
    pmpro_setOption($_REQUEST['pmpro_email_template_switcher'] . '_subject', $_REQUEST['email_template_subject']);
    pmpro_setOption($_REQUEST['pmpro_email_template_switcher'] . '_body', $_REQUEST['template_body']);

    //assume success
    $msg = true;
    $msgt = "Your email template settings have been updated.";
}

require_once(PMPRO_DIR . "/adminpages/admin_header.php");
?>

    <form action="" method="post" enctype="multipart/form-data">
        <h2><?php _e('Email Templates', 'pmpro');?></h2>

        <table class="form-table">
            <tr class="status" style="display:none;">
                <th scope="row" valign="top"></th>
                <td>
                    <p class="status_message"></p>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                    <label for="pmpro_email_template_switcher">Email Template</label>
                </th>
                <td>
                    <select name="pmpro_email_template_switcher" id="pmpro_email_template_switcher">
                        <option value="" selected="selected">--- Select a Template to Edit ---</option>
                        <option value="email_header">Email Header</option>
                        <option value="email_footer">Email Footer</option>
                        <option value="email_default">Default Email</option>
                        <option value="email_admin_change">Admin Change</option>
                        <option value="email_admin_change_admin">Admin Change (admin)</option>
                        <option value="email_billing">Billing</option>
                        <option value="email_billing_admin">Billing (admin)</option>
                        <option value="email_billing_failure">Billing Failure</option>
                        <option value="email_billing_failure_admin">Billing Failure (admin)</option>
                        <option value="email_cancel">Cancel</option>
                        <option value="email_cancel_admin">Cancel (admin)</option>
                        <option value="email_checkout_check">Checkout - Check</option>
                        <option value="email_checkout_check_admin">Checkout - Check (admin)</option>
                        <option value="email_checkout_express">Checkout - PayPal Express</option>
                        <option value="email_checkout_express_admin">Checkout - PayPal Express (admin)</option>
                        <option value="email_checkout_free">Checkout - Free</option>
                        <option value="email_checkout_free_admin">Checkout - Free (admin)</option>
                        <option value="email_checkout_freetrial">Checkout - Free Trial</option>
                        <option value="email_checkout_freetrial_admin">Checkout - Free Trial (admin)</option>
                        <option value="email_checkout_paid">Checkout - Paid</option>
                        <option value="email_checkout_paid_admin">Checkout - Paid (admin)</option>
                        <option value="email_checkout_trial">Checkout - Trial</option>
                        <option value="email_checkout_trial_admin">Checkout - Trial (admin)</option>
                        <option value="email_credit_card_expiring">Credit Card Expiring</option>
                        <option value="email_invoice">Invoice</option>
                        <option value="email_membership_expired">Membership Expired</option>
                        <option value="email_membership_expiring">Membership Expiring</option>
                        <option value="email_trial_ending">Trial Ending</option>
                    </select>
                    <img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" id="pmproet-spinner" style="display:none;" />
                    <hr>
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
                        <?php wp_editor('', 'blank'); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top"></th>
                <td>
                    <p class="submit">
                        <input id="submit_template_data" name="savesettings" type="submit" class="button-primary" value="Save Settings" />
                    </p>
                </td>
            </tr>
        </table>

    </form>

<?php
require_once(PMPRO_DIR . "/adminpages/admin_footer.php");
?>