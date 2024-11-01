<?php


/**
 * @package CRBOGODeals
 */


namespace CRIncludes\Rules;

use CRIncludes\Api\TransientApi;
use CRIncludes\Helper\General;
use CRIncludes\Rules\CartRules\ExecuteCart;
use CRIncludes\Rules\CheckRules\Rule;
use CRIncludes\Rules\CheckRules\Validate;



/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly

/**
 * Class Hooks
 * @package Includes\Rules
 */
class Hooks
{

    private $cart_event_change_hook_flag = false;


    public $session_cart_products;


    public $rules;

    private $buyxgetx_coupons=array();



    public function __construct()
    {

    }


    public function register()
    {



        if(!is_admin()){

            /*
       * Add to Cart Event Hook
       */

            add_action('woocommerce_add_to_cart', array($this, 'addToCart'), 10, 6);


            /*
             * Remove from Cart Event Hook
             */
            add_action('woocommerce_remove_cart_item', array($this, 'removeCartItem'), 10, 2);

            /*
             * Update Cart Event Hook
             */
            add_action('woocommerce_after_cart_item_quantity_update', array($this, 'updateCartItem'), 10, 3);


            /*
             * Format price
             */
            add_filter('woocommerce_cart_item_price', array($this, 'formatPrice'), 10, 3);


            /*
             * Custom price and quantity
             */
            add_action('woocommerce_before_calculate_totals', array($this, 'addCustomPrice'));




            /**
             * Custom Coupons
             */


            add_action('woocommerce_cart_loaded_from_session', array($this, 'addCustomCoupons'));



            /**
             * Virtual Coupon
             */


            add_filter('woocommerce_get_shop_coupon_data', array($this, 'addVirtualCoupon'), 10, 2);

            add_action('woocommerce_after_calculate_totals', array($this, 'applyFakeCoupons'));


            /**
             * Display Rule Title on the Product Page if Option selection on the settings page
             */
            $display_rule_title_option = get_option('cr_bogo_rules_settings_display_rule_title');
            if (isset($display_rule_title_option['product_page']))
                add_filter('woocommerce_before_add_to_cart_form', array($this, 'priceTable'), 10, 3);


            /*
            * Display notice
            */
            $display_free_product_notice = get_option('cr_bogo_rules_settings_display_free_product_notice');
            if (isset($display_free_product_notice['cart_page']))
                add_action('woocommerce_before_cart', array($this, 'displayNotice'));

        }

        /*
        * Custom Post Type New Column Head- Validity
        */
        add_filter('manage_cr_bogo_deals_posts_columns', array($this, 'customColumnHead'));

        /*
         * Custom Post Type New Column Content- Validity
         */
        add_action('manage_cr_bogo_deals_posts_custom_column', array($this, 'customColumnContent'), 10, 2);



        /*
         * Save Post Meta
         */
        add_action('save_post_cr_bogo_deals', array($this, 'saveMeta'), 10, 3);

        /*
         * Hide unwanted options from publish metabox
         */
        add_action('admin_head-post.php', array($this,'hidePublishingActions'));
        add_action('admin_head-post-new.php', array($this,'hidePublishingActions'));



    }


    /**
     * Save all the rule Meta Values
     * @param $post_id
     */
    function saveMeta($post_id, $post, $update )
    {
        /*
                 * delete all transients
                 */


        (new TransientApi())->deleteAll();

        if ( 'trash' != $post->post_status ||  ! ( wp_is_post_revision( $post_id) || wp_is_post_autosave( $post_id ) )
        ) {


            /*
             * Buy metas
             */

            if(isset($_POST['woo_cr_rule_buy_method'])){

                $rule_buy_method = isset($_POST['woo_cr_rule_buy_method']) ? $_POST['woo_cr_rule_buy_method'] : '';
                update_post_meta($post_id, 'rule_buy_method', $rule_buy_method);
                switch ($rule_buy_method) {
                    case 'buyxgetx':
                        $rule_buy = isset($_POST['woo_cr_buyxgetx_buy_data']) ? $_POST['woo_cr_buyxgetx_buy_data'] : '';
                        update_post_meta($post_id, 'rule_buy', json_decode(stripslashes($rule_buy), true));
                        break;

                }


                /*
                 * Get Metas
                 */
                if ($rule_buy_method == 'buyxgetx') {
                    $rule_get = isset($_POST['woo_cr_buyxgetx_buy_data']) ? $_POST['woo_cr_buyxgetx_buy_data'] : '';
                }

                update_post_meta($post_id, 'rule_get', json_decode(stripslashes($rule_get), true));

                //validity
                $valid_from = isset($_POST['valid_from']) ? $_POST['valid_from'] : '';
                $valid_to = isset($_POST['valid_to']) ? $_POST['valid_to'] : '';
                update_post_meta($post_id, 'valid_from', $valid_from);
                update_post_meta($post_id, 'valid_to', $valid_to);

                //enable/disable toggle

                $enable_disable_toggle = isset($_POST['enable_disable_toggle']) ? $_POST['enable_disable_toggle'] : '';
                update_post_meta($post_id, 'enable_disable_toggle', $enable_disable_toggle);
            }


        }
    }

    /**
     * Add to cart Event
     *
     * @param $cart_item_key
     * @param $product_id
     * @param $quantity
     * @param $variation_id
     * @param $variation
     * @param $cart_item_data
     * @throws \Exception
     */
    function addToCart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {

        $current_item = array(
            'product_id' => $product_id,
            'quantity' => $quantity,
            'variation' => $variation,
            'variation_id' => $variation_id
        );

        Cart::setSessionAddedCartItems($current_item);

        $this->cart_event_change_hook_flag = true;

    }

    /**
     * @param $cart_item_key
     * @param $current_quantity
     * @param $old_quantity
     * @throws \Exception
     */
    function updateCartItem($cart_item_key, $current_quantity, $old_quantity)
    {

        Cart::setSessionUpdatedCartProducts($cart_item_key, $current_quantity);

        $this->cart_event_change_hook_flag = true;

    }


    /**
     * @param $cart_item_key
     * @param $cart
     * @throws \Exception
     */
    function removeCartItem($cart_item_key, $cart)
    {

        Cart::setSessionRemovedCartProducts($cart_item_key, $cart);

        $this->cart_event_change_hook_flag = true;

    }




    public function addCustomPrice($cart_object)
    {


        //get rules and iterate tru them
        $rules = Validate::getRuleIds();

        $cart_products = WC()->session->get('cart_products');
        $cart_products=is_null($cart_products)?array():$cart_products;

        $current_product = isset($cart_products[0]['current_item']['product_id']) ? $cart_products[0]['current_item']['product_id'] : null;
        $current_product_id=0;
        if (!is_null($current_product)) {
            if (wc_get_product($current_product)->is_type('variable')) {
                $current_product_id = $cart_products[0]['current_item']['variation_id'];
            } else {
                $current_product_id = $current_product;
            }
        }

        foreach ($rules as $rule) {


            if (Validate::checkValidity($rule) && Validate::isEnabled($rule)) {
                if ((new Rule)->isEligible($rule, $cart_products, $rules)) {


                    if ((new Rule())->isMatched($rule, $cart_products, $rules)) {

                        Execute::addToCart($rule);
                        Execute::setCustomCart($rule, $cart_object, $current_product_id, $this->cart_event_change_hook_flag);

                    }

                }
            }


        }

        $this->cart_event_change_hook_flag = false;


    }


    public function formatPrice($price, $cart_item, $cart_item_key)
    {


        //get rules and iterate tru them
        $rules = Validate::getRuleIds();

        $cart_products = WC()->session->get('cart_products');

        $cart_products=is_null($cart_products)?array():$cart_products;


        foreach ($rules as $rule) {

            if (Validate::checkValidity($rule) && Validate::isEnabled($rule)) {
                if ((new Rule)->isEligible($rule, $cart_products, $rules)) {


                    if ((new Rule())->isMatched($rule, $cart_products, $rules)) {

                        $price = Execute::formatPrice($rule, $price, $cart_item);

                    }

                }
            }



        }


        return $price;

    }


    /**
     *
     */
    function priceTable()
    {

        $rules = General::getRules();
        if ($rules) {
            global $product;
            $id = $product->get_id();
            $categories = wp_get_post_terms($id, 'product_cat', array('fields' => 'ids'));
            foreach ($rules as $rule) {
                $rule_buy = get_post_meta($rule->ID, 'rule_buy', true);
                if (!empty($rule_buy)) {
                    foreach ($rule_buy as $buy) {
                        if (isset($buy['id'])) {
                            if ($buy['id'] == $id) {
                                echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                            } elseif ($product->is_type('variable')) {
                                //variable ids check
                                $variations = $product->get_available_variations();
                                foreach ($variations as $variation) {
                                    if ($variation['variation_id'] == $buy['id']) {
                                        echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                        break;
                                    }
                                }
                            }
                            if (in_array($buy['id'], $categories)) {
                                echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                //category
                            }
                        }
                        if ($buy['buytype'] == 'buyxgetx_product') {
                            foreach ($buy['id'] as $buy_product_id) {
                                if ($product->is_type('variable')) {
                                    $product_variations = $product->get_available_variations();
                                    foreach ($product_variations as $product_variation) {
                                        if ($product_variation['variation_id'] == $buy_product_id) {
                                            echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                            break;
                                        }
                                    }
                                } elseif ($buy_product_id == $id) {
                                    echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                }
                            }
                        } elseif ($buy['buytype'] == 'buyxgetx_category') {
                            foreach ($buy['id'] as $buy_product_id) {
                                if (in_array($buy_product_id, $categories)) {
                                    echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                }
                            }
                        } elseif ($buy['buytype'] == 'buyxgetx_all_products') {
                            $excluded = empty($buy['id']) ? array() : $buy['id'];
                            if ($product->is_type('variable')) {
                                $product_variations = $product->get_available_variations();
                                foreach ($product_variations as $product_variation) {
                                    if (!in_array($product_variation['variation_id'], $excluded)) {
                                        echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                        break;
                                    }
                                }
                            } else {
                                if (!in_array($id, $excluded)) {
                                    echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                }
                            }

                        } elseif ($buy['buytype'] == 'buyxgetx_all_categories') {
                            foreach ($categories as $category) {
                                $excluded = empty($buy['id']) ? array() : $buy['id'];
                                if (!in_array($category, $excluded)) {

                                    echo esc_attr_e(get_the_title($rule->ID), 'woocommerce-bogo-discount-pricing-deals') . "<br>";
                                    break;
                                }
                            }
                        }
                    }
                }

            }

        }


    }

    /**
     * @param $defaults
     * @return mixed
     */
    function customColumnHead($defaults)
    {
        unset($defaults['date']);
        $defaults['valid_from'] = 'Valid From';
        $defaults['valid_to'] = 'Valid To';
        $defaults['enabled_disabled']='Enabled/Disabled';
        return $defaults;
    }

    /**
     * @param $column
     * @param $post_id
     */
    function customColumnContent($column, $post_id)
    {
        $valid_from = get_post_meta($post_id, "valid_from", true);
        $valid_to = get_post_meta($post_id, "valid_to", true);
        $valid_from = str_replace('T', ' ', $valid_from);
        $valid_to = str_replace('T', ' ', $valid_to);

        $enabled_disabled_toggle=get_post_meta($post_id,'enable_disable_toggle',true);

        switch ($column) {
            case 'valid_from':
                echo $valid_from;
                break;
            case 'valid_to':
                echo $valid_to;
                break;
            case 'enabled_disabled':
                if($enabled_disabled_toggle){
                    ?>
                    <div style="background: #a6ff98; width: 55px; color: #0a0a0a">
                        Enabled
                    </div>
                    <?php
                }else{
                    ?>
                    <div style="background: #ff8c87; width: 55px; color: #0a0a0a    ">
                        Disabled
                    </div>
                    <?php
                }
                break;

        }
    }

    function displayNotice()
    {


        //get rules and iterate tru them
        $rules = Validate::getRuleIds();

        $cart_products = WC()->session->get('cart_products');

        $cart_products=is_null($cart_products)?array():$cart_products;


        foreach ($rules as $rule) {

            if (Validate::checkValidity($rule) && Validate::isEnabled($rule)) {
                if ((new Rule)->isEligible($rule, $cart_products, $rules)) {


                    if ((new Rule())->isMatched($rule, $cart_products, $rules)) {

                        wc_print_notice("You Get Free Product(s) :)", "success");
                        break;

                    }

                }
            }



        }
    }

    

    /**
     * Apply fake coupon to cart
     *
     * @access public
     * @return void
     */
    public function applyFakeCoupons()
    {
        global $woocommerce;

        $buyxgetx_coupons = $this->buyxgetx_coupons;


        $coupons=$buyxgetx_coupons;

        foreach ($coupons as $coupon_code=>$coupon_values) {

            $coupon_code = apply_filters('woocommerce_coupon_code', $coupon_code);
            if(!$woocommerce->cart->has_discount($coupon_code)){
                // Add coupon
                $woocommerce->cart->applied_coupons[] = $coupon_code;


            }

        }



    }

    public function addVirtualCoupon($unknown_param, $old_coupon_code)
    {

        $buyxgetx_coupons = $this->buyxgetx_coupons;


        $coupons=$buyxgetx_coupons;

        if (in_array($old_coupon_code,array_keys($coupons))) {

            $coupon=$coupons[$old_coupon_code];

            $discount_type = 'fixed_cart';
            $amount = $coupon['discount'];


            $coupon = array(
                'id' => 321123 . rand(2, 9),
                'amount' => $amount,
                'individual_use' => false,
                'product_ids' => array(),
                'exclude_product_ids' => array(),
                'usage_limit' => '',
                'usage_limit_per_user' => '',
                'limit_usage_to_x_items' => '',
                'usage_count' => '',
                'expiry_date' => '',
                'apply_before_tax' => 'yes',
                'free_shipping' => false,
                'product_categories' => array(),
                'exclude_product_categories' => array(),
                'exclude_sale_items' => false,
                'minimum_amount' => '',
                'maximum_amount' => '',
                'customer_email' => '',
            );

            $coupon['type'] = $discount_type;


            return $coupon;
        }
    }



    public function addCustomCoupons(){


        $this->nocache();



        $cart_products = WC()->session->get('cart_products');

        $cart_products = is_null($cart_products) ? array() : $cart_products;



        /*
         * Buy X Get X - Recursive Fee
         */

        $buyxgetx_recursive_coupons=Execute::recursionFee($cart_products);

        /*
         * Buy X Get X- Non Recursive Fee
         */

        $buyxgetx_nonrecursive_coupons=Execute::nonRecursionFee($cart_products);


        $buyxgetx_coupons=array_merge($buyxgetx_recursive_coupons,$buyxgetx_nonrecursive_coupons);

        $this->buyxgetx_coupons=array_merge($this->buyxgetx_coupons,$buyxgetx_coupons);



    }


    public function nocache()
    {
        if (is_page('cart') || is_cart()) {
            if (headers_sent()) return false;
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Expires: Wed, 17 Sep 1975 21:32:10 GMT');
            return true;
        }

        return false;

    }

    public function hidePublishingActions(){
        $my_post_type = 'cr_bogo_deals';
        global $post;
        if($post->post_type == $my_post_type){
            echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
        }
    }


}