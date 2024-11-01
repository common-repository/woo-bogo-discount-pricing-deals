<?php


/**
 *@package CRBOGODeals
 */

namespace CRIncludes\Base;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class Activate{

    public static function activate( $wp = '3.1', $php = '5.6' ) {
        global $wp_version;
        if ( version_compare( PHP_VERSION, $php, '<' ) )
            $flag = 'PHP';
        elseif
        ( version_compare( $wp_version, $wp, '<' ) )
            $flag = 'WordPress';
        else{

            return;
        }
        $version = 'PHP' == $flag ? $php : $wp;
        deactivate_plugins( basename( __FILE__ ) );
        wp_die('<p>The <strong>WooCommerce Dynamic Pricing and BOGO Deals</strong> plugin requires '.$flag.'  version '.$version.' or greater.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
        flush_rewrite_rules();
    }

}
