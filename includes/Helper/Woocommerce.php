<?php
/**
 *@package CRBOGODeals
 */

namespace CRIncludes\Helper;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class Woocommerce{


    public static function getCart(){

        global $woocommerce;

        $items =$woocommerce->cart->get_cart();

        return $items;
    }

    public static function cartTotalQuantity(){

        global $woocommerce;

        $cart_total=$woocommerce->cart->get_cart_contents_count();

        return $cart_total;
    }

    public static function cartSubtotal(){

        global $woocommerce;

        $cart_subtotal=$woocommerce->cart->get_subtotal();

        return $cart_subtotal;
    }

    public static function categoriesById($product_id){

        //check if product_id is simple or variable or a variation
        $categories=array();

        $product=wc_get_product($product_id);

        if($product->is_type('simple') || $product->is_type('variable')){

            $categories= get_the_terms($product_id, 'product_cat');

        }else{
           $categories=get_the_terms($product->get_parent_id(), 'product_cat');
        }

        return $categories;
    }

    public static function getProducts($categoryid)
    {
        if ( false === ( $rules = get_transient( 'category_based_products_'.$categoryid ) ) ) {
            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => '12',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                        'terms' => $categoryid,
                        'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                    ),
                    array(
                        'taxonomy' => 'product_visibility',
                        'field' => 'slug',
                        'terms' => 'exclude-from-catalog', // Possibly 'exclude-from-search' too
                        'operator' => 'NOT IN'
                    )
                )
            );
            $products = new \WP_Query($args);
            set_transient('category_based_products_'.$categoryid, $products->posts);
        }

        return get_transient('category_based_products_'.$categoryid);
    }

    /**
     * @param $product_id
     * @param $quantity
     * @throws \Exception
     */
    public static function addProductToCart($product_id, $quantity)
    {
        $found = false;
        //check if product already in cart
        if (sizeof(WC()->cart->get_cart()) > 0) {
            foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                $_product = $values['data'];
                if ($_product->get_id() == $product_id)
                    $found = true;
            }
            // if product not found, add it
            if (!$found)
                WC()->cart->add_to_cart($product_id, $quantity);
        } else {
            // if no products in cart, add it
            WC()->cart->add_to_cart($product_id, $quantity);
        }
    }

    public static function getIds($cart_categories){

        $cart_cat_ids=array();
        if(empty($cart_categories))
            return array();
        foreach ($cart_categories as $cart_category){
            $cart_cat_ids[]=wp_list_pluck($cart_category,'term_id');
        }

        return array_unique(call_user_func_array('array_merge',$cart_cat_ids));
    }

    public static function getFreeCartProducts(){
        $free_products_ids=array();
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        foreach ($items as $item => $values){
            $cart_product_price = $values['data']->get_price();
            if ($cart_product_price == 0) {
                $free_products_ids[]=$values['data']->get_id();
            }
        }
        return $free_products_ids;
    }


    public static function getAllProducts(){

        //get all products
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
        );

        return wp_list_pluck(get_posts($args),'ID');
    }

    public static function get_price_excluding_tax($product, $quantity = 1, $price = '')
    {
        return  wc_get_price_excluding_tax($product, array('qty' => $quantity));
    }

    public static function get_price_including_tax($product, $quantity = 1, $price = '')
    {
        return wc_get_price_excluding_tax($product, array('qty' => $quantity));
    }

}