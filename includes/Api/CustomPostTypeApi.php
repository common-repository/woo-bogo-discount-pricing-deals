<?php


/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Api;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class CustomPostTypeApi
{

    public function register()
    {
        add_action('init', array($this, 'registerPostType'));


    }

    function registerPostType()
    {

        $args = array(
            'labels' => array(
                'name' => __('Woo BOGO Deals'),
                'singular_name' => __('Woo BOGO Deal'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New BOGO Rule'),
                'edit_item' => __('Edit BOGO Rule'),
                'new_item' => __('New BOGO Rule'),
                'view_item' => __('View BOGO Rule'),
                'search_items' => __('Search Rules'),
                'not_found' => __('Nothing found'),
                'not_found_in_trash' => __('Nothing found in Trash'),
            ),
            'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
            'publicly_queriable' => true,  // you should be able to query it
            'show_ui' => true,  // you should be able to edit it in wp-admin
            'exclude_from_search' => true,  // you should exclude it from search results
            'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
            'has_archive' => false,  // it shouldn't have archive page
            'rewrite' => false,  // it shouldn't have rewrite rules
            'supports' => array('title'),
            'menu_icon' => 'dashicons-cart'
        );

        register_post_type('cr_bogo_deals', $args);

    }

}