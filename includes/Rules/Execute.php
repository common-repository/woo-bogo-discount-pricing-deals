<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules;


use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;
use CRIncludes\Rules\CheckRules\Validate;
use CRIncludes\Rules\Range\RangeCategory;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


/**
 * Class Execute
 * @package Includes\Rules
 */
class Execute
{


    public static function recursionFee($cart_products)
    {

        $recursive_rule_ids = General::getBuyxgetxRuleIds(true);
        //check here for apply first matched

        $coupons=array();

        foreach ($recursive_rule_ids as $recursive_rule_id) {

            if (Validate::checkValidity($recursive_rule_id) && Validate::isEnabled($recursive_rule_id)) {
                if ($recursive_rule_id['buy_type'] == 'buyxgetx_all_products') {
                    $rule_ids = Validate::getRuleIds();
                    $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
                    $updated_carts = Product::removeGetProducts($rule_ids, $updated_cart);
                    $excluded_product_ids = empty($recursive_rule_id['product_ids'])?array():$recursive_rule_id['product_ids'];
                    foreach ($updated_carts as $updated_cart) {
                        $cart_product_array = array();
                        $cart_product_array[] = $updated_cart['id'];
                        if ((count(array_diff($cart_product_array, $excluded_product_ids)) > 0)) {
                            $cart_items = Woocommerce::getCart();
                            if (wc_get_product($updated_cart['id'])->is_type('simple')) {
                                $recursive_cart_item = General::searchForProductId($updated_cart['id'], $cart_items);
                            } else {
                                $recursive_cart_item = General::searchForVariationId($updated_cart['id'], $cart_items);
                            }
                            $coupons=self::addRecursiveFee($recursive_cart_item, $updated_cart['id'], $coupons);
                        }
                    }
                } else {
                    foreach ($recursive_rule_id['product_ids'] as $recursive_product_id) {

                        if (!Cart::isFreeProduct($recursive_product_id)) {
                            if (in_array($recursive_product_id, array_column($cart_products, 'id'))) {
                                $cart_items = Woocommerce::getCart();
                                if (wc_get_product($recursive_product_id)->is_type('simple')) {
                                    $recursive_cart_item = General::searchForProductId($recursive_product_id, $cart_items);
                                } else {
                                    $recursive_cart_item = General::searchForVariationId($recursive_product_id, $cart_items);
                                }
                                $coupons=self::addRecursiveFee($recursive_cart_item, $recursive_product_id, $coupons);
                            }
                        }

                    }
                }


                if ($recursive_rule_id['buy_type'] == 'buyxgetx_all_categories') {
                    $rule_ids = Validate::getRuleIds();
                    $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
                    $updated_carts = Product::removeGetProducts($rule_ids, $updated_cart);

                    foreach ($updated_carts as $updated_cart) {

                        $cart_product_categories = $updated_cart['categories'];
                        $category_flag = (count(array_diff(wp_list_pluck($cart_product_categories, 'term_id'), $recursive_rule_id['category_ids'])) > 0);
                        if ($category_flag) {
                            $cart_items = Woocommerce::getCart();
                            if (wc_get_product($updated_cart['id'])->is_type('simple')) {
                                $recursive_cart_item = General::searchForProductId($updated_cart['id'], $cart_items);
                            } else {
                                $recursive_cart_item = General::searchForVariationId($updated_cart['id'], $cart_items);
                            }
                            $coupons= self::addRecursiveFee($recursive_cart_item, $updated_cart['id'], $coupons);
                        }
                    }
                }

                if ($recursive_rule_id['buy_type'] == 'buyxgetx_category') {
                    foreach ($recursive_rule_id['category_ids'] as $recursive_category_id) {
                        $rule_ids = Validate::getRuleIds();
                        $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
                        $updated_carts = Product::removeGetProducts($rule_ids, $updated_cart);
                        $cart_items = Woocommerce::getCart();

                        foreach ($updated_carts as $cart_product) {
                            if (!Cart::isFreeProduct($cart_product['id'])) {
                                if (in_array($recursive_category_id, wp_list_pluck($cart_product['categories'], 'term_id'))) {
                                    if (wc_get_product($cart_product['id'])->is_type('simple')) {
                                        $recursive_cart_item = General::searchForProductId($cart_product['id'], $cart_items);
                                    } else {
                                        $recursive_cart_item = General::searchForVariationId($cart_product['id'], $cart_items);
                                    }
                                    $coupons=  self::addRecursiveFee($recursive_cart_item, $cart_product['id'], $coupons);
                                }
                            }
                        }
                    }
                }


            }

        }

        return $coupons;

    }


    public static function nonRecursionFee($cart_products)
    {

        $coupons=array();

        $non_recursive_rule_ids = General::getBuyxgetxRuleIds(false);
        //check here for apply first matched

        foreach ($non_recursive_rule_ids as $non_recursive_rule_id) {
            if (Validate::checkValidity($non_recursive_rule_id) && Validate::isEnabled($non_recursive_rule_id)) {
                $get_rules = General::getRuleMeta($non_recursive_rule_id['rule_id'], 'rule_get');

                foreach ($get_rules as $get_rule) {
                    if (!Cart::isFreeProduct($get_rule['id'])) {
                        if ($non_recursive_rule_id['buy_type'] == 'buyxgetx_all_categories') {
                            //all categories logic
                            $rule_ids = Validate::getRuleIds();
                            $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
                            $updated_carts = Product::removeGetProducts($rule_ids, $updated_cart);
                            foreach ($updated_carts as $updated_cart) {

                                $cart_product_categories = $updated_cart['categories'];
                                $category_flag = (count(array_diff(wp_list_pluck($cart_product_categories, 'term_id'), $non_recursive_rule_id['category_ids'])) > 0);
                                if ($category_flag) {
                                    $cart_items = Woocommerce::getCart();
                                    if (wc_get_product($updated_cart['id'])->is_type('simple')) {
                                        $non_recursive_cart_item = General::searchForProductId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    } else {
                                        $non_recursive_cart_item = General::searchForVariationId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    }
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag_product = isset($buyxgetx_flag[$updated_cart['id']]) ? $buyxgetx_flag[$updated_cart['id']] : false;

                                    //check quantity
                                    $non_recursive_cart_item_quantity = $non_recursive_cart_item_quantity - $get_rule['getquant'];
                                    if ($non_recursive_cart_item_quantity != 0 && $buyxgetx_flag_product) {
                                        $coupons = self::addNonRecursiveFee($non_recursive_cart_item, $get_rule['getquant'], $updated_cart['id'], $coupons);

                                    }

                                }
                            }

                        } elseif ($non_recursive_rule_id['buy_type'] == 'buyxgetx_category') {
                            //all categories logic
                            $rule_ids = Validate::getRuleIds();
                            $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
                            $updated_carts = Product::removeGetProducts($rule_ids, $updated_cart);
                            foreach ($updated_carts as $updated_cart) {

                                $cart_product_categories = $updated_cart['categories'];
                                $category_flag = (count(array_intersect(wp_list_pluck($cart_product_categories, 'term_id'), $non_recursive_rule_id['category_ids'])) > 0);
                                if ($category_flag) {
                                    $cart_items = Woocommerce::getCart();
                                    if (wc_get_product($updated_cart['id'])->is_type('simple')) {
                                        $non_recursive_cart_item = General::searchForProductId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    } else {
                                        $non_recursive_cart_item = General::searchForVariationId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    }
                                    $non_recursive_cart_item_quantity = $non_recursive_cart_item_quantity - $get_rule['getquant'];
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag_product = isset($buyxgetx_flag[$updated_cart['id']]) ? $buyxgetx_flag[$updated_cart['id']] : false;

                                    if ($non_recursive_cart_item_quantity != 0 && $buyxgetx_flag_product) {
                                        if ($non_recursive_cart_item_quantity >= $get_rule['min_buyquant'] && $non_recursive_cart_item_quantity <= $get_rule['max_buyquant']) {
                                            $coupons = self::addNonRecursiveFee($non_recursive_cart_item, $get_rule['getquant'], $updated_cart['id'], $coupons);
                                        }
                                    }


                                }
                            }
                        } elseif ($non_recursive_rule_id['buy_type'] == 'buyxgetx_product') {

                            foreach ($cart_products as $updated_cart) {
                                $cart_product_array = array();
                                $cart_product_array[] = $updated_cart['id'];
                                $product_flag = (count(array_intersect($cart_product_array, $non_recursive_rule_id['product_ids'])) > 0);
                                if ($product_flag) {
                                    $cart_items = Woocommerce::getCart();
                                    if (wc_get_product($updated_cart['id'])->is_type('simple')) {
                                        $non_recursive_cart_item = General::searchForProductId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    } else {
                                        $non_recursive_cart_item = General::searchForVariationId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    }
                                    //check quantity
                                    $non_recursive_cart_item_quantity = $non_recursive_cart_item_quantity - $get_rule['getquant'];

                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag_product = isset($buyxgetx_flag[$updated_cart['id']]) ? $buyxgetx_flag[$updated_cart['id']] : false;
                                    if ($non_recursive_cart_item_quantity != 0 && $buyxgetx_flag_product) {
                                        if ($non_recursive_cart_item_quantity >= $get_rule['min_buyquant'] && $non_recursive_cart_item_quantity <= $get_rule['max_buyquant']) {
                                            $coupons = self::addNonRecursiveFee($non_recursive_cart_item, $get_rule['getquant'], $updated_cart['id'], $coupons);
                                        }
                                    }

                                }
                            }
                        } elseif ($non_recursive_rule_id['buy_type'] == 'buyxgetx_all_products') {
                            $rule_ids = Validate::getRuleIds();
                            $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
                            $updated_carts = Product::removeGetProducts($rule_ids, $updated_cart);
                            $excluded_product_ids = empty($non_recursive_rule_id['product_ids']) ? array() : $non_recursive_rule_id['product_ids'];
                            foreach ($updated_carts as $updated_cart) {
                                $cart_product_array = array();
                                $cart_product_array[] = $updated_cart['id'];
                                $product_flag = (count(array_diff($cart_product_array, $excluded_product_ids)) > 0);
                                if ($product_flag) {
                                    $cart_items = Woocommerce::getCart();
                                    if (wc_get_product($updated_cart['id'])->is_type('simple')) {
                                        $non_recursive_cart_item = General::searchForProductId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    } else {
                                        $non_recursive_cart_item = General::searchForVariationId($updated_cart['id'], $cart_items);
                                        $non_recursive_cart_item_quantity = $non_recursive_cart_item['quantity'];
                                    }

                                    $non_recursive_cart_item_quantity = $non_recursive_cart_item_quantity - $get_rule['getquant'];
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag_product = isset($buyxgetx_flag[$updated_cart['id']]) ? $buyxgetx_flag[$updated_cart['id']] : false;


                                    if ($non_recursive_cart_item_quantity != 0 && $buyxgetx_flag_product) {

                                        $coupons = self::addNonRecursiveFee($non_recursive_cart_item, $get_rule['getquant'], $updated_cart['id'], $coupons);
                                    }


                                }
                            }
                        }
                    }
                }
            }
        }
            return $coupons;


    }



    public static function addRecursiveFee($cart_item, $get_product, $coupons)
    {

        if (!is_null($cart_item['data'])) {
            $price = get_option('woocommerce_prices_include_tax') == 'yes' ? $cart_item['data']->get_price() : Woocommerce::get_price_including_tax($cart_item['data']);

            $quantity = $cart_item['quantity'];
            $get_quantity = $quantity / 2;
            if (is_float($get_quantity)) {
                $get_quantity = floor($get_quantity);
            }
            $discount = ($price * $get_quantity);
            $product_tile=html_entity_decode(get_the_title($get_product));
            $coupon_code=strtolower($product_tile. '- Quantity:' .$get_quantity);
            $coupons[$coupon_code]=array(
                'product_id'=>$get_product,
                'quantity'=>$get_quantity,
                'product_title'=>$product_tile,
                'discount'=>$discount
            );
        }

        return $coupons;


    }

    public static function addNonRecursiveFee($cart_item, $get_quantity, $get_product, $coupons)
    {
        if (!is_null($cart_item['data'])) {
            $price = get_option('woocommerce_prices_include_tax') == 'yes' ? $cart_item['data']->get_price() : Woocommerce::get_price_including_tax($cart_item['data']);

            $discount = ($price * $get_quantity);
            $product_tile=html_entity_decode(get_the_title($get_product));
            $coupon_code=strtolower($product_tile. '- Quantity:' .$get_quantity);
            $coupons[$coupon_code]=array(
                'product_id'=>$get_product,
                'quantity'=>$get_quantity,
                'product_title'=>$product_tile,
                'discount'=>$discount
            );
        }

        return $coupons;
    }

    public static function addToCart($rule)
    {

            $rule_get = General::getRuleMeta($rule['rule_id'], 'rule_get');
            if(!empty($rule_get)){
                foreach ($rule_get as $get_products) {
                    if ($get_products['gettype'] == 'product') {
                        Woocommerce::addProductToCart($get_products['id'], $get_products['getquant']);
                    }
                }
            }




    }

    /**
     *
     */
    public static function setCustomCart($rule, $cart_object, $current_product_id, $cart_event_change_hook_flag)
    {


        $rule_get = General::getRuleMeta($rule['rule_id'], 'rule_get');
        if(!empty($rule_get)){
            foreach ($rule_get as $get_products) {
                if ($get_products['gettype'] == 'product') {

                } elseif ($get_products['gettype'] == 'buyxgetx_product') {

                    if ($get_products['recursive']) {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {
                            if (wc_get_product($value['product_id'])->is_type('simple')) {
                                $cart_product = $value['product_id'];
                            } else {
                                $cart_product = $value['variation_id'];
                            }
                            $cart_product_array = array();
                            $cart_product_array[] = $cart_product;
                            $specific_products_flag = (count(array_intersect($cart_product_array, $get_products['id'])) > 0);
                            if ($specific_products_flag && ($current_product_id == $cart_product) && $cart_event_change_hook_flag) {
                                $product_quantity = $value['quantity'];

                                WC()->cart->set_quantity($cart_key, $product_quantity * 2, false);

                            }
                        }
                    } else {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {
                            if (wc_get_product($value['product_id'])->is_type('simple')) {
                                $cart_product = $value['product_id'];
                            } else {
                                $cart_product = $value['variation_id'];
                            }
                            $cart_product_array = array();
                            $cart_product_array[] = $cart_product;
                            $specific_products_flag = (count(array_intersect($cart_product_array, $get_products['id'])) > 0);
                            if ($specific_products_flag && ($current_product_id == $cart_product) && $cart_event_change_hook_flag) {
                                $product_quantity = $value['quantity'];
                                if ($product_quantity >= $get_products['min_buyquant'] && $product_quantity <= $get_products['max_buyquant']) {
                                    WC()->cart->set_quantity($cart_key, $product_quantity + $get_products['getquant'], false);
                                    $buyxgetx_flag=WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$cart_product]=true;
                                    WC()->session->set('buyxgetx_flag',$buyxgetx_flag);
                                }else{
                                    $buyxgetx_flag=WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$cart_product]=false;
                                    WC()->session->set('buyxgetx_flag',$buyxgetx_flag);
                                }
                            }

                        }
                    }

                } elseif ($get_products['gettype'] == 'buyxgetx_all_products') {

                    $excluded_product_ids = empty($get_products['id'])?array():$get_products['id'];
                    if ($get_products['recursive']) {

                        foreach ($cart_object->cart_contents as $cart_key => $value) {

                            $cart_product_array = array();
                            $cart_variation_array = array();
                            if (wc_get_product($value['product_id'])->is_type('simple')) {
                                $cart_product=$value['product_id'];
                                $cart_product_array[] = $value['product_id'];
                            } else {
                                $cart_product=$value['variation_id'];
                                $cart_variation_array[] = $value['variation_id'];
                            }


                            if ((count(array_diff($cart_product_array, $excluded_product_ids)) > 0) && ($current_product_id == $cart_product)&& $cart_event_change_hook_flag) {

                                $product_quantity = $value['quantity'];

                                WC()->cart->set_quantity($cart_key, $product_quantity * 2, false);

                            } elseif (count(array_diff($cart_variation_array, $excluded_product_ids)) > 0 && ($current_product_id == $cart_product) && $cart_event_change_hook_flag) {

                                $product_quantity = $value['quantity'];
                                WC()->cart->set_quantity($cart_key, $product_quantity * 2, false);

                            }

                        }

                    } else {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {
                            $cart_product_array = array();
                            $cart_variation_array = array();
                            $cart_product_array[] = $value['product_id'];
                            $cart_variation_array[] = $value['variation_id'];
                            if (wc_get_product($value['product_id'])->is_type('simple')) {
                                $product_id = $value['product_id'];
                            } else {
                                $product_id = $value['variation_id'];
                            }
                            $product_id_array = array();
                            $product_id_array[] = $product_id;
                            if ((count(array_diff($product_id_array, $excluded_product_ids)) > 0) && ($current_product_id == $product_id)&& $cart_event_change_hook_flag) {
                                $product_quantity = $value['quantity'];
                                if ($product_quantity >= $get_products['min_buyquant'] && $product_quantity <= $get_products['max_buyquant']) {
                                    WC()->cart->set_quantity($cart_key, $product_quantity + $get_products['getquant'], false);
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$product_id] = true;
                                    WC()->session->set('buyxgetx_flag', $buyxgetx_flag);
                                } else {
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$product_id] = false;
                                    WC()->session->set('buyxgetx_flag', $buyxgetx_flag);
                                }
                            }


                        }
                    }


                } elseif ($get_products['gettype'] == 'buyxgetx_category') {
                    if ($get_products['recursive']) {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {
                            $cart_item_categories = wp_list_pluck(Woocommerce::categoriesById($value['product_id']), 'term_id');
                            $specific_categories_flag = (count(array_intersect($cart_item_categories, $get_products['id'])) > 0 && $cart_event_change_hook_flag);
                            if ($specific_categories_flag && ($current_product_id == $value['product_id'] || $current_product_id == $value['variation_id'])) {
                                $product_quantity = $value['quantity'];
                                WC()->cart->set_quantity($cart_key, $product_quantity * 2, false);

                            }
                        }
                    } else {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {
                            if (wc_get_product($value['product_id'])->is_type('simple')) {
                                $product_id = $value['product_id'];
                            } else {
                                $product_id = $value['variation_id'];
                            }
                            $cart_item_categories = wp_list_pluck(Woocommerce::categoriesById($value['product_id']), 'term_id');
                            $specific_categories_flag = (count(array_intersect($cart_item_categories, $get_products['id'])) > 0);
                            if ($specific_categories_flag && ($current_product_id == $value['product_id'] || $current_product_id == $value['variation_id']) && $cart_event_change_hook_flag) {
                                $product_quantity = $value['quantity'];
                                if ($product_quantity >= $get_products['min_buyquant'] && $product_quantity <= $get_products['max_buyquant']) {
                                    WC()->cart->set_quantity($cart_key, $product_quantity + $get_products['getquant'], false);
                                    $buyxgetx_flag=WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$product_id]=true;
                                    WC()->session->set('buyxgetx_flag',$buyxgetx_flag);
                                }else{
                                    $buyxgetx_flag=WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$product_id]=false;
                                    WC()->session->set('buyxgetx_flag',$buyxgetx_flag);
                                }
                            }

                        }
                    }
                } elseif ($get_products['gettype'] == 'buyxgetx_all_categories') {
                    if ($get_products['recursive']) {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {
                            $cart_item_categories = wc_list_pluck(Woocommerce::categoriesById($value['product_id']), 'term_id');
                            $excluded_categories=empty($get_products['id'])?array():$get_products['id'];
                            $all_categories_flag = (count(array_diff($cart_item_categories, $excluded_categories)) > 0);
                            if ($all_categories_flag && ($current_product_id == $value['product_id'] || $current_product_id == $value['variation_id']) && $cart_event_change_hook_flag) {
                                $product_quantity = $value['quantity'];

                                WC()->cart->set_quantity($cart_key, $product_quantity * 2, false);

                            }
                        }
                    } else {
                        foreach ($cart_object->cart_contents as $cart_key => $value) {

                            if (wc_get_product($value['product_id'])->is_type('simple')) {
                                $product_id = $value['product_id'];
                            } else {
                                $product_id = $value['variation_id'];
                            }
                            $cart_item_categories = wc_list_pluck(Woocommerce::categoriesById($value['product_id']), 'term_id');
                            $excluded_categories=empty($get_products['id'])?array():$get_products['id'];

                            $all_categories_flag = (count(array_diff($cart_item_categories, $excluded_categories)) > 0);
                            if ($all_categories_flag && ($current_product_id == $value['product_id'] || $current_product_id == $value['variation_id']) && $cart_event_change_hook_flag) {
                                $product_quantity = $value['quantity'];
                                if ($product_quantity >= $get_products['min_buyquant'] && $product_quantity <= $get_products['max_buyquant']) {
                                    WC()->cart->set_quantity($cart_key, $product_quantity + $get_products['getquant'], false);
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$product_id] = true;
                                    WC()->session->set('buyxgetx_flag', $buyxgetx_flag);
                                } else {
                                    $buyxgetx_flag = WC()->session->get('buyxgetx_flag');
                                    $buyxgetx_flag[$product_id] = false;
                                    WC()->session->set('buyxgetx_flag', $buyxgetx_flag);
                                }
                            }
                        }
                    }
                }

            }

        }


    }


    public static function formatPrice($rule, $price, $cart_item)
    {


        $rule_get = General::getRuleMeta($rule['rule_id'], 'rule_get');


        return $price;
    }

}