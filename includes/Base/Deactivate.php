<?php


/**
 *@package CRBOGODeals
 */

namespace CRIncludes\Base;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class Deactivate{

    public static function deactivate(){
        flush_rewrite_rules();
    }

}
