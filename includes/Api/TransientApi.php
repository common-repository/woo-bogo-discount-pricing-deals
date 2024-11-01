<?php


/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Api;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class TransientApi
{

    public function __construct()
    {

    }

    public function deleteAll(){

        global $wpdb;

        $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('%_transient_crbogo_%')" );

    }

}