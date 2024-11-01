<?php


/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Base;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly



class Enqueue extends BaseController
{

    public function register()
    {

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_print_scripts',array($this,'enqueue_admin_print_scripts'));
        add_action('wp_enqueue_scripts', array($this,'enqueue_front_scripts' ));

    }

    function enqueue_admin_scripts()
    {

        //enqueue all scripts if admin and posttype=cr_bogo_deals
        global $pagenow, $typenow;
        if (empty($typenow) && !empty($_GET['post'])) {
            $post = get_post(isset($_GET['post']) ? sanitize_text_field($_GET['post']): '');
            $typenow = $post->post_type;
        }
        if (is_admin() && $typenow=='cr_bogo_deals') {
            if ($pagenow=='post-new.php' OR $pagenow=='post.php') {
                //css
                wp_enqueue_style('cr_style', $this->plugin_url.'assets/css/cr-bogo-deals-style.css', __FILE__);
                //js

                wp_enqueue_script('cr_metabox_script', $this->plugin_url.'assets/js/cr-bogo-deals-admin.js', array('jquery'), null, true);

            }
        }




    }

    function enqueue_admin_print_scripts(){
        global $pagenow, $typenow;
        if (empty($typenow) && !empty($_GET['post'])) {
            $post = get_post(isset($_GET['post']) ? sanitize_text_field($_GET['post']): '');
            $typenow = $post->post_type;
        }
        if (is_admin() && $typenow=='cr_bogo_deals') {
            if ($pagenow=='post-new.php' OR $pagenow=='post.php') {
                //woocommerce product multi select
                wp_enqueue_script('wc-enhanced-select'); // if your are using recent versions
                wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');
            }

    }
    }

    function enqueue_front_scripts(){
        if(!is_admin()){
            wp_enqueue_script('cr_front_script', $this->plugin_url.'assets/js/cr-bogo-deals-frontend.js', array('jquery'), null, true);
            wp_enqueue_style( 'dashicons' );
        }

    }

}
