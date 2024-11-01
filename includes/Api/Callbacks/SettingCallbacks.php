<?php
/**
 * @package CRBOGODeals
 *
 */


namespace CRIncludes\Api\Callbacks;


use CRIncludes\Base\BaseController;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class SettingCallbacks extends BaseController
{

    public function adminSettings()
    {
        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h1><?php esc_attr_e('Global Settings', 'woocommerce-bogo-discount-pricing-deals') ?></h1>
            <form method="post" action="options.php">
                <?php


                settings_fields("cr_bogo_rules_settings");

                // all the add_settings_field callbacks is displayed here
                do_settings_sections("cr-bogo-deals-options");
                // Add the submit button to serialize the options
                submit_button();

                ?>
            </form>
        </div>
        <?php
    }

    function display_header_options_content()
    {
        echo "Global Settings of the plugin";
    }

    function display_applyto_form_element()
    {
        //id and name of form element should be same as the setting name.
        ?>

        <select name="cr_bogo_rules_settings_applyto" id="cr_bogo_rules_settings_applyto">
            <option value="all" <?php selected(get_option('cr_bogo_rules_settings_applyto'), "all"); ?>><?php esc_html_e('All Matched Rules','woocommerce-bogo-discount-pricing-deals') ?></option>
            <!-- <option value="first" <?php /*selected(get_option('cr_bogo_rules_settings_applyto'), "first"); */?>><?php /*esc_html_e('First Matched Rule','woocommerce-bogo-discount-pricing-deals')*/?></option>-->
        </select>
        <?php
    }

    function display_rule_title_form_element()
    {
        //id and name of form element should be same as the setting name.

        $display_rule_title_option=get_option('cr_bogo_rules_settings_display_rule_title');
        ?>

        <input type="checkbox" id="product_page" name="cr_bogo_rules_settings_display_rule_title[product_page]"  <?php checked(isset($display_rule_title_option['product_page'])?true:false) ?> >
        <?php esc_html_e('Product Page','woocommerce-bogo-discount-pricing-deals');
    }

    function display_free_product_notice_form_element()
    {
        //id and name of form element should be same as the setting name.

        $display_free_product_notice_option=get_option('cr_bogo_rules_settings_display_free_product_notice');
        ?>

        <input type="checkbox" id="cart_page" name="cr_bogo_rules_settings_display_free_product_notice[cart_page]"  <?php checked(isset($display_free_product_notice_option['cart_page'])?true:false) ?> >
        <?php esc_html_e('Cart Page','woocommerce-bogo-discount-pricing-deals');
    }




}