<?php

/**
 * @package CRBOGODeals
 */


namespace CRIncludes\Rules\CheckRules;

use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;
use CRIncludes\Rules\Execute;
use CRIncludes\Rules\Hooks;
use CRIncludes\Rules\Product;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class Rule extends Hooks {

    public function __construct()
    {

    }

    public function isEligible($rule, $cart_products, $rules){



        $updated_cart = Product::removeBuyproducts($rules, $cart_products); // for categories
        $updated_cart = Product::removeGetProducts($rules, $updated_cart);

        $cart_categories = array_column($updated_cart, 'categories');

        $cart_product_ids = array_column($cart_products, 'id');

        $cart_category_ids = Woocommerce::getIds($cart_categories);

        $product_ids = $rule['product_ids'];
        $category_ids = $rule['category_ids'];

        $product_flag = true;
        $category_flag = true;

        if (!empty($product_ids)) {
            $product_flag = (count($product_ids) == count(array_intersect($product_ids, $cart_product_ids)));
        }

        if (!empty($category_ids) || !empty($cart_categories)) {
            $category_flag = (count($category_ids) == count(array_intersect($category_ids, $cart_category_ids)));
        }

        if ($rule['buy_type'] == 'buyxgetx_all_products') {
            //if any one of the product ids is available in the cart_product_ids, product_flag=true
            $excluded_product_ids = empty($product_ids)?array():$product_ids;
            $product_flag = (count(array_diff($cart_product_ids, $excluded_product_ids)) > 0);
        }

        if($rule['buy_type']=='buyxgetx_product'){
            $product_flag=(count(array_intersect($cart_product_ids,$product_ids))>0);
        }

        if($rule['buy_type']=='buyxgetx_all_categories'){
            $excluded_category_ids = empty($category_ids)?array():$category_ids;
            $category_flag=(count(array_diff($cart_category_ids,$excluded_category_ids))>0);
        }

        if($rule['buy_type']=='buyxgetx_category'){
            $category_flag=(count(array_intersect($cart_category_ids,$category_ids))>0);
        }

        // check if rule is all products

        if ($product_flag == true && $category_flag == true) {
            return true;
        }

        return false;

    }

    public function isMatched($rule, $cart_products, $rules){

        //get method

        $rule_method = General::getRuleMeta($rule['rule_id'], 'rule_buy_method');

        $rule_buy_data = General::getRuleMeta($rule['rule_id'], 'rule_buy');

        $rule_matched = true;

        if ($rule_method == 'buyxgetx') {



        }

        if ($rule_matched) {
           return true;
        }

        return false;

    }




}