<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules\CartRules;


use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;
use CRIncludes\Rules\Product;
use CRIncludes\Rules\Range\RangeCart;
use CRIncludes\Rules\Range\RangeCategory;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


/**
 * Class Execute
 * @package Includes\Rules\CartRules
 */
class ExecuteCart
{


    public static function rangeFee($rule, $cart_products, $wc_cart, $rules)
    {


        $rule_buy_data = General::getRuleMeta($rule['rule_id'], 'rule_buy');
        foreach ($rule_buy_data as $rule_buy_datum) {
            if ($rule_buy_datum['buytype'] == 'category') {
                //remove get product from cart
                $range_products = Product::getRangeProducts($cart_products, $rule);

                $updated_cart_products = array();

                foreach ($cart_products as $cart_product) {

                    if (!in_array($cart_product['id'], $range_products)) {
                        $updated_cart_products[] = $cart_product;
                    }

                }
                $cart_category_quantity = 0;
                foreach ($updated_cart_products as $cart_value) {
                    foreach ($cart_value['categories'] as $cart_category) {
                        if ($cart_category->term_id == $rule_buy_datum['id']) {
                            $cart_category_quantity += $cart_value['quant'];
                        }
                    }
                }

                if ($cart_category_quantity != 0) {
                    if ($cart_category_quantity >= $rule_buy_datum['min_buyquant'] && $cart_category_quantity <= $rule_buy_datum['max_buyquant']) {
                        $range_products = Product::getRangeProducts($cart_products, $rule);
                    }
                }
            }else{
                $range_products=Product::getRangeProducts($cart_products,$rule);
            }
        }


        try{
            foreach ($range_products as $product) {
                Woocommerce::addProductToCart($product, 1);
            }
        }catch (\Exception $e){

        }

        if (!empty($range_products)) {
            foreach ($range_products as $get_prod_id) {
                $_product = wc_get_product($get_prod_id);
                $product_price = $_product->get_price();
                $get_discount = -($product_price);
                $wc_cart->add_fee("Free - " . get_the_title($get_prod_id), $get_discount, true);

            }
        }

    }


}
