<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules;

use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;
use CRIncludes\Rules\Range\RangeCart;
use CRIncludes\Rules\Range\RangeCategory;
use CRIncludes\Rules\Range\RangeProducts;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly

/**
 * Class Product
 * @package Includes\Rules
 */
class Product
{


    /**
     * @return array
     */
    public static function ruleGetIds($rules)
    {


        $rule_get_ids = array();

        if (!is_array($rules[0])) {
            $rules = wc_list_pluck($rules, 'ID');
        }
        foreach ($rules as $rule) {
            $rule_id = isset($rule['rule_id']) ? $rule['rule_id'] : $rule;
            $rule_get = General::getRuleMeta($rule_id, 'rule_get');
            if(!empty($rule_get)){
                foreach ($rule_get as $get_products) {
                    if ($get_products['gettype'] == 'product') {
                        $rule_get_ids[] = $get_products['id'];
                    }
                }
            }


        }

        return $rule_get_ids;
    }


    public static function removeGetProducts($rules, $cart_products)
    {

        $rule_get_ids = self::ruleGetIds($rules);
        $updated_cart_products = array();

        //get range_get_product_ids in array
        //  $rule_range_get_ids=self::getRangeProducts($cart_products,$rules);

        foreach ($cart_products as $cart_product) {

            if (!in_array($cart_product['id'], $rule_get_ids)) {
                $updated_cart_products[] = $cart_product;
            }

        }

        return $updated_cart_products;

    }

    public static function getRangeProducts($cart_products, $rule)
    {

        $range_products=array();

        //iterate tru rules to find the range rules
        //get the products (array) based on the type of range rule

        $rule_get_data = General::getRuleMeta($rule['rule_id'], 'rule_get');
        foreach ($rule_get_data as $rule_get_datum) {
            if ($rule_get_datum['gettype'] == 'range') {
                if ($rule_get_datum['getfrom'] == 'cart') {
                    $products = RangeCart::get($rule_get_datum['getrange'], $rule_get_datum['getquant'], $cart_products);
                } elseif ($rule_get_datum['getfrom'] == 'category') {
                    $products = RangeCategory::get($rule_get_datum['getrange'], $rule_get_datum['getquant'], $rule_get_datum['getcategoryid']);
                } elseif ($rule_get_datum['getfrom'] == 'products') {
                    $products = RangeProducts::get($rule_get_datum['getrange'], $rule_get_datum['getquant']);
                }
                if (!empty($products))
                    $range_products[] = $products;
            }
        }


        if (!empty($range_products))
            $range_products = call_user_func_array('array_merge', $range_products);

        return $range_products;
    }

    /**
     * @param $rules
     * @param $cart_products
     * @return array
     */
    public static function removeBuyproducts($rules, $cart_products)
    {

        $rule_product_ids=array_column($rules,'products_ids');
        $rule_buy_ids=array();
        if(!empty($rule_product_ids)){
            $rule_buy_ids = call_user_func_array('array_merge', $rule_product_ids);

        }

        $updated_cart_products = array();

        foreach ($cart_products as $cart_product) {

            if (!in_array($cart_product['id'], $rule_buy_ids)) {
                $updated_cart_products[] = $cart_product;
            }

        }

        return $updated_cart_products;

    }
}
