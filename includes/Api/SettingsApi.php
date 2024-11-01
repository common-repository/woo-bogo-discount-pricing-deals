<?php


/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Api;

use CRIncludes\Api\Callbacks\SettingCallbacks;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class SettingsApi
{

    public $admin_pages = array();

    public $settings = array();

    public $sections = array();

    public $fields = array();


    public function register()
    {


        if (!empty($this->admin_pages)) {

            add_action('admin_menu', array($this, 'addAdminMenu'));

        }

        if(!empty($this->settings)){

            add_action('admin_init',array($this,'registerCustomFields'));
        }

    }



    public function addPages(array $pages)
    {

        $this->admin_pages = $pages;

        return $this;

    }

    public function setSettings(array $settings)
    {

        $this->settings = $settings;

        return $this;

    }


    public function setSections(array $sections)
    {

        $this->sections = $sections;

        return $this;

    }


    public function setFields(array $fields)
    {

        $this->fields = $fields;

        return $this;

    }


    public function addAdminMenu()
    {

        foreach ($this->admin_pages as $admin_page) {
            if ($admin_page['submenu'] == true) {
                add_submenu_page(
                    $admin_page['admin_slug'],
                    $admin_page['page_title'],
                    $admin_page['menu_title'],
                    $admin_page['capability'],
                    $admin_page['menu_slug'],
                    $admin_page['callback']
                );


            } else {
                add_menu_page(
                    $admin_page['page_title'],
                    $admin_page['menu_title'],
                    $admin_page['capability'],
                    $admin_page['menu_slug'],
                    $admin_page['callback'],
                    $admin_page['icon_url'],
                    $admin_page['position']
                );
            }

        }
    }


    public function registerCustomFields(){

        //register settings

        foreach ($this->settings as $setting) {
            register_setting($setting['option_group'], $setting['option_name'],
                (isset($setting['callback']) ? $setting['callback'] : ''));
        }

        //add settings section

        foreach ($this->sections as $section) {

            add_settings_section($section['id'], $section['title'],
                (isset($section['callback']) ? $section['callback'] : ''), $section['page']);
        }

        //add settings field

        foreach ($this->fields as $field) {
            add_settings_field($field['id'], $field['title'],
                (isset($field['callback']) ? $field['callback'] : ''),
                $field['page'], $field['section'], (isset($field['args']) ? $field['args'] : ''));
        }


    }



}