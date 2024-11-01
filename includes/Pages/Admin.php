<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Pages;

use CRIncludes\Api\Callbacks\SettingCallbacks;
use CRIncludes\Api\CustomPostTypeApi;
use CRIncludes\Api\MetaboxApi;
use CRIncludes\Base\BaseController;
use CRIncludes\Api\SettingsApi;
use CRIncludes\Api\Callbacks\AdminCallbacks;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly



class Admin extends BaseController
{


    public $settings;

    public $pages = array();

    public $callbacks;

    public $settingsCallbacks;

    public $custom_post_type;

    public $metaboxArgs=array();

    public $metabox;


    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->settingsCallbacks= new SettingCallbacks();

        $this->custom_post_type = new CustomPostTypeApi();

        $this->metabox=new MetaboxApi();

        $this->custom_post_type->register();

        $this->setPages();

        $this->setSettings();

        $this->setSections();

        $this->setFields();

        $this->setMetaboxes();

        $this->metabox->setMetabox($this->metaboxArgs)->register();

        $this->settings->addPages($this->pages)->register();

    }

    public function setPages()
    {

        $this->pages = array(

            [
                'submenu' => true,
                'admin_slug'=>'edit.php?post_type=cr_bogo_deals',
                'page_title' => 'Settings',
                'menu_title' => 'Settings',
                'capability' => 'manage_options',
                'menu_slug' => 'cr_bogo_deals_settings',
                'callback' => array($this->settingsCallbacks, 'adminSettings')
            ]

        );

    }

    public function setSettings()
    {

        $args = array(

            array(

                'option_group' => 'cr_bogo_rules_settings',
                'option_name' => 'cr_bogo_rules_settings_applyto',

            ),
            array(

                'option_group' => 'cr_bogo_rules_settings',
                'option_name' => 'cr_bogo_rules_settings_pricelimit',

            ),
            array(

                'option_group' => 'cr_bogo_rules_settings',
                'option_name' => 'cr_bogo_rules_settings_display_rule_title',

            ),
            array(

                'option_group' => 'cr_bogo_rules_settings',
                'option_name' => 'cr_bogo_rules_settings_display_free_product_notice',

            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections()
    {

        $args = array(

            array(

                'id' => 'cr_bogo_rules_settings',
                'title' => 'Settings',
                'callback' => array($this->settingsCallbacks, 'display_header_options_content'),
                'page' => 'cr-bogo-deals-options'

            )
        );

        $this->settings->setSections($args);
    }


    public function setFields()
    {


        $args = array(



            array(

                'id' => 'cr_bogo_rules_settings_applyto',
                'title' => 'Apply',
                'callback' => array($this->settingsCallbacks, 'display_applyto_form_element'),
                'page' => 'cr-bogo-deals-options',
                'section' => 'cr_bogo_rules_settings',


            ),

            array(

                'id' => 'cr_bogo_rules_settings_display_rule_title',
                'title' => 'Display Rule Title',
                'callback' => array($this->settingsCallbacks, 'display_rule_title_form_element'),
                'page' => 'cr-bogo-deals-options',
                'section' => 'cr_bogo_rules_settings',

            ),

            array(

                'id' => 'cr_bogo_rules_settings_display_free_product_notice',
                'title' => 'Display Notice',
                'callback' => array($this->settingsCallbacks, 'display_free_product_notice_form_element'),
                'page' => 'cr-bogo-deals-options',
                'section' => 'cr_bogo_rules_settings',

            )


        );

        $this->settings->setFields($args);
    }

    /*
     * Create all  metaboxes here :)
     */
    public function setMetaboxes()
    {

        $this->metaboxArgs = array(

            //main method option metabox
           [
                'id' => 'woo_cr_method_metabox',
                'title' => 'Buy',
                'callback' => array($this->callbacks, 'ruleMethodMetabox'),
                'page' => 'cr_bogo_deals',
                'context' => 'normal',
                'priority' => 'high',
                'args' => array()

            ],


            //Buy X Get X metabox
            [
                'id' => 'woo_cr_buyxgetx_metabox',
                'title' => 'Buy X Get X Rule',
                'callback' => array($this->callbacks, 'ruleBuyxgetxMetabox'),
                'page' => 'cr_bogo_deals',
                'context' => 'advanced',
                'priority' => 'high',
                'args' => array()

            ],



            //Exclude Products metabox- Buy X Get X - All Products
            [
                'id' => 'woo_cr_exclude_products_metabox',
                'title' => 'Exclude Products',
                'callback' => array($this->callbacks, 'ruleExcludeProductsMetabox'),
                'page' => 'cr_bogo_deals',
                'context' => 'advanced',
                'priority' => 'high',
                'args' => array()

            ],

            //Exclude Products metabox- Buy X Get X - All Products
            [
                'id' => 'woo_cr_exclude_categories_metabox',
                'title' => 'Exclude Categories',
                'callback' => array($this->callbacks, 'ruleExcludeCategoriesMetabox'),
                'page' => 'cr_bogo_deals',
                'context' => 'advanced',
                'priority' => 'high',
                'args' => array()

            ],

            //Validity metabox
            [
                'id' => 'woo_cr_validity_metabox',
                'title' => 'Validity',
                'callback' => array($this->callbacks, 'ruleValidityMetabox'),
                'page' => 'cr_bogo_deals',
                'context' => 'advanced',
                'priority' => 'high',
                'args' => array()

            ],

            //Enable/Disable metabox
            [
                'id' => 'woo_cr_enable_disable_metabox',
                'title' => 'Enable/Disable',
                'callback' => array($this->callbacks, 'ruleEnableDisableMetabox'),
                'page' => 'cr_bogo_deals',
                'context' => 'side',
                'priority' => 'high',
                'args' => array()

            ]






        );


    }


}