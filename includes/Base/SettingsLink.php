<?php


/**
 *@package CRBOGODeals
 */

namespace CRIncludes\Base;

use CRIncludes\Base;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class SettingsLink extends BaseController{



    public function register(){
        add_filter("plugin_action_links_".$this->plugin_name ,array($this,'settings_link'));

    }

    function settings_link($links){
            $settings_link='<a href="edit.php?post_type=cr_bogo_deals">Settings</a>';
            array_push($links,$settings_link);
            return $links;

        }
}
