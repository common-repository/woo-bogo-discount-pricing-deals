<?php
/**
 * Plugin name: Woocommerce BOGO Discount Pricing Deals
 * Plugin URI: http://www.flycart.org
 * Description: Simple BOGO Deals
 * Author: Flycart Technologies LLP
 * Author URI: https://www.flycart.org
 * Slug: woo-bogo-discount-pricing-deals
 * Text Domain:  woocommerce-bogo-discount-pricing-deals
 * Version: 1.1.3
 * Requires at least: 4.6.1
 * WC requires at least: 3.0
 * WC tested up to: 3.3
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly

/*
 * Composer autoload
 */

if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

/**
 * Plugin Activation code- change
 */
function activate_crbogodeals()
{
    \CRIncludes\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_crbogodeals');

/**
 * Plugin Deactivation code
 */
function deactivate_crbogodeals()
{

    \CRIncludes\Base\Deactivate::deactivate();

}
register_deactivation_hook(__FILE__, 'deactivate_crbogodeals');


/*
 * Initialize all classes as services
 */

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    if(class_exists('CRIncludes\\Init')){
        CRIncludes\Init::register_services();
    }
}