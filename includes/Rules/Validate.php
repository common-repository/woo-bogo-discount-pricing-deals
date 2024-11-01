<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;

/**
 * Class Validate
 * @package Includes\Rules
 */
class Validate
{


    /**
     * @return array
     */
    public static function getRuleIds()
    {

        $rule_ids = array();

        $rules = General::getRules();

        foreach ($rules as $rule) {

            $buy_data = General::getRuleMeta($rule->ID, 'rule_buy');

            $product_ids = array();
            $category_ids = array();

            foreach ($buy_data as $buy_datum) {

                $buy_type = $buy_datum['buytype'];
                if ($buy_type == 'product') {
                    $product_ids[] = $buy_datum['id'];
                } elseif ($buy_type == 'category') {
                    $category_ids[] = $buy_datum['id'];
                } elseif ($buy_type == 'buyxgetx_product') {
                    $product_ids = $buy_datum['id'];
                } elseif ($buy_type == 'buyxgetx_category') {
                    $category_ids = $buy_datum['id'];
                } elseif ($buy_type == 'buyxgetx_all_products') {
                    //consider all product ids excluding the exclude products
                    $product_ids=$buy_datum['id'];
                }elseif ($buy_type=='buyxgetx_all_categories'){
                    if(!empty($buy_datum['id'])){
                        $category_ids=$buy_datum['id'];
                    }
                }

            }

            $item = array(

                'rule_id' => $rule->ID,
                'product_ids' => $product_ids,
                'category_ids' => $category_ids,
                'buy_type' => isset($buy_data[0]['buytype'])?$buy_data[0]['buytype']:''
            );

            $rule_ids[] = $item;

        }

        return $rule_ids;
    }


    /**
     * @param array $rule_ids
     * @param array $cart_products
     * @return array
     */
    public static function getEligibleRules($rule_ids = array(), $cart_products = array())
    {


        $cart_product_ids = array_column($cart_products, 'id');

        $updated_cart = Product::removeBuyproducts($rule_ids, $cart_products); // for categories
        $updated_cart = Product::removeGetProducts($rule_ids, $updated_cart);
        $cart_categories = array_column($updated_cart, 'categories');

        $cart_category_ids = Woocommerce::getIds($cart_categories);


        $eligible_rule_ids = array();
        foreach ($rule_ids as $rule_id) {

            $product_ids = $rule_id['product_ids'];
            $category_ids = $rule_id['category_ids'];

            $product_flag = true;
            $category_flag = true;

            if (!empty($product_ids)) {
                $product_flag = (count($product_ids) == count(array_intersect($product_ids, $cart_product_ids)));
            }

            if (!empty($category_ids) || !empty($cart_categories)) {
                $category_flag = (count($category_ids) == count(array_intersect($category_ids, $cart_category_ids)));
            }

            if ($rule_id['buy_type'] == 'buyxgetx_all_products') {
                //if any one of the product ids is available in the cart_product_ids, product_flag=true
                if(!empty($product_ids))
                $product_flag = (count(array_diff($cart_product_ids, $product_ids)) > 0);
            }

            if($rule_id['buy_type']=='buyxgetx_product'){
                $product_flag=(count(array_intersect($cart_product_ids,$product_ids))>0);
            }

            if($rule_id['buy_type']=='buyxgetx_all_categories'){
                if(!empty($category_ids))
                $category_flag=(count(array_diff($cart_category_ids,$category_ids))>0);
            }

            if($rule_id['buy_type']=='buyxgetx_category'){
                $category_flag=(count(array_intersect($cart_category_ids,$category_ids))>0);
            }

            // check if rule is all products

            if ($product_flag == true && $category_flag == true) {
                $eligible_rule_ids[] = $rule_id;
            }
        }

            return $eligible_rule_ids;

    }


    public static function getMatchedRules($eligibleRules = array(), $cart_products = array(), $allRules = array())
    {


        $matchedRules = array();

        foreach ($eligibleRules as $eligibleRule) {


            //get method

            $rule_method = General::getRuleMeta($eligibleRule['rule_id'], 'rule_buy_method');

            $rule_buy_data = General::getRuleMeta($eligibleRule['rule_id'], 'rule_buy');

            $rule_matched = true;

            if ($rule_method == 'buyxgetx') {

                foreach ($rule_buy_data as $rule_buy_datum) {

                    if ($rule_buy_datum['recursive']) {

                        if ($rule_buy_datum['buytype'] == 'buyxgetx_product') {

                            //check here if rule_buy_datum is simple or variable
                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $current_cart_item_array=array();
                            $current_cart_product_array[]=$current_cart_item;
                            $specific_products_flag=(count(array_intersect($current_cart_product_array,$rule_buy_datum['id']))>0);

                            if($specific_products_flag){
                                $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                $specific_products_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                $specific_products_current_quantity=(isset($specific_products_current_quantities_meta[$current_cart_item])?$specific_products_current_quantities_meta[$current_cart_item]:0)*2;
                                if($cart_products[0]['current_item']['quantity']==$specific_products_current_quantity){
                                    if ($flag == false || !$flag) {
                                        $flag_meta[$current_cart_item]=true;
                                        $specific_products_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $specific_products_current_quantities_meta);

                                    }
                                }else{
                                    $flag_meta[$current_cart_item]=true;
                                    $specific_products_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                    General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                    General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $specific_products_current_quantities_meta);

                                }
                            }

                        }

                        if ($rule_buy_datum['buytype'] == 'buyxgetx_category') {


                            //

                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $current_cart_item_categories=wp_list_pluck(Woocommerce::categoriesById($current_cart_item),'term_id');
                            $specific_categories_flag=(count(array_intersect($current_cart_item_categories,$rule_buy_datum['id']))>0);

                            if($specific_categories_flag){
                                $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                $specific_categories_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                $specific_categories_current_quantity=(isset($specific_categories_current_quantities_meta[$current_cart_item])?$specific_categories_current_quantities_meta[$current_cart_item]:0)*2;
                                if($cart_products[0]['current_item']['quantity']==$specific_categories_current_quantity){
                                    if ($flag == false || !$flag) {
                                        $flag_meta[$current_cart_item]=true;
                                        $specific_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $specific_categories_current_quantities_meta);

                                    }
                                }else{
                                    $flag_meta[$current_cart_item]=true;
                                    $specific_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                    General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                    General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $specific_categories_current_quantities_meta);

                                }
                            }



                        }

                        //recursive buyxgetx_all_products

                        if ($rule_buy_datum['buytype'] == 'buyxgetx_all_products') {

                            //get all products excluding the products to be excluded

                            $excluded_product_ids=empty($rule_buy_datum['id'])?array():$rule_buy_datum['id'];


                            //get all cart product ids
                            //iterate tru them, if cart product id is not in excluded products
                           foreach ($cart_products as $cart_product){
                                $cart_product_array[]=$cart_product['id'];

                               if(((count(array_diff($cart_product_array, $excluded_product_ids)) > 0) || empty($excluded_product_ids))){
                                   $rule_buy_product_id = $cart_product['id'];
                                   $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                   $flag=isset($flag_meta[$rule_buy_product_id])?$flag_meta[$rule_buy_product_id]:false;
                                   $all_products_flags=$flag_meta;
                                   $all_products_current_quantities=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                   if (wc_get_product($rule_buy_product_id)->is_type('simple')) {
                                       if ($cart_products[0]['current_item']['product_id'] == $rule_buy_product_id) {
                                           //  $all_products_current_quantity = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity');
                                           $current_quantity=$all_products_current_quantities[$rule_buy_product_id]*2;
                                           if ($current_quantity == $cart_products[0]['current_item']['quantity']) {
                                               if ($flag == false || !$flag) {
                                                   //check if quantity is changed
                                                   $all_products_flags[$rule_buy_product_id]=true;
                                                   $all_products_current_quantities[$rule_buy_product_id]=$cart_products[0]['current_item']['quantity'];
                                                   General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$all_products_flags);
                                                   General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities);
                                               }
                                           } else {
                                               $all_products_flags[$rule_buy_product_id]=true;
                                               $all_products_current_quantities[$rule_buy_product_id]=$cart_products[0]['current_item']['quantity'];
                                               General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$all_products_flags);
                                               General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities);
                                           }

                                       }
                                   } else {
                                       if ($cart_products[0]['current_item']['variation_id'] == $rule_buy_product_id) {
                                           $current_quantity = (isset($all_products_current_quantities[$rule_buy_product_id])?$all_products_current_quantities[$rule_buy_product_id]:0)*2;
                                           if ($current_quantity == $cart_products[0]['current_item']['quantity']) {
                                               if ($flag == false || !$flag) {
                                                   $all_products_flags[$rule_buy_product_id]=true;
                                                   $all_products_current_quantities[$rule_buy_product_id]=$cart_products[0]['current_item']['quantity'];
                                                   General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$all_products_flags);
                                                   General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities);
                                               }
                                           } else {
                                               $all_products_flags[$rule_buy_product_id]=true;
                                               $all_products_current_quantities[$rule_buy_product_id]=$cart_products[0]['current_item']['quantity'];
                                               General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$all_products_flags);
                                               General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities);}
                                       }
                                   }

                               }
                           }



                        }

                        if($rule_buy_datum['buytype']=='buyxgetx_all_categories'){
                            //get all categories of current cart item
                            //if it is not in the excluded categories then do the magic
                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $current_cart_item_categories=wp_list_pluck(Woocommerce::categoriesById($current_cart_item),'term_id');
                            $all_categories_flag=(count(array_diff($current_cart_item_categories,$rule_buy_datum['id']))>0);

                            if($all_categories_flag || empty($rule_buy_datum['id'])){
                                $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                $all_categories_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                $all_categories_current_quantity=(isset($all_categories_current_quantities_meta[$current_cart_item])?$all_categories_current_quantities_meta[$current_cart_item]:0)*2;
                                if($cart_products[0]['current_item']['quantity']==$all_categories_current_quantity){
                                    if ($flag == false || !$flag) {
                                        $flag_meta[$current_cart_item]=true;
                                        $all_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_categories_current_quantities_meta);

                                    }
                                }else{
                                    $flag_meta[$current_cart_item]=true;
                                    $all_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                    General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                    General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_categories_current_quantities_meta);

                                }
                            }
                        }

                    } else {

                        //check quantity
                        if ($rule_buy_datum['buytype'] == 'buyxgetx_product') {

                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $current_cart_item_array=array();
                            $current_cart_item_array[]=$current_cart_item;
                            $all_products_flag=(count(array_intersect($current_cart_item_array,$rule_buy_datum['id']))>0);
                            $spec_flag_meta=General::getRuleMeta($eligibleRule['rule_id'],'spec_products_non_recursive_flag');
                            if($all_products_flag){
                                $current_quantity=$cart_products[0]['current_item']['quantity'];
                                if(($current_quantity >=$rule_buy_datum['min_buyquant'] && $current_quantity <=$rule_buy_datum['max_buyquant'])){
                                    $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                    $all_products_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                    $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                    $all_products_current_quantity=(isset($all_products_current_quantities_meta[$current_cart_item])?$all_products_current_quantities_meta[$current_cart_item]:0)+$rule_buy_datum['getquant'];
                                    if($cart_products[0]['current_item']['quantity']==$all_products_current_quantity){
                                        if ($flag == false || !$flag) {
                                            $flag_meta[$current_cart_item]=true;
                                            $all_products_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities_meta);

                                        }
                                    }else{
                                        $flag_meta[$current_cart_item]=true;
                                        $all_products_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities_meta);

                                    }
                                }else{
                                    $spec_flag_meta[$current_cart_item]=false;
                                    General::setRuleMeta($eligibleRule['rule_id'], 'spec_products_non_recursive_flag' ,$spec_flag_meta);
                                    $rule_matched = false;
                                    break;
                                }



                            }

                        }

                        if ($rule_buy_datum['buytype'] == 'buyxgetx_category') {

                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $current_cart_item_categories=wp_list_pluck(Woocommerce::categoriesById($current_cart_item),'term_id');
                            $all_categories_flag=(count(array_intersect($current_cart_item_categories,$rule_buy_datum['id']))>0);
                            $spec_flag_meta=General::getRuleMeta($eligibleRule['rule_id'],'spec_products_non_recursive_flag');
                            if($all_categories_flag){
                                $current_quantity=$cart_products[0]['current_item']['quantity'];
                                if($current_quantity >=$rule_buy_datum['min_buyquant'] && $current_quantity <=$rule_buy_datum['max_buyquant']){
                                    $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                    $all_categories_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                    $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                    $all_categories_current_quantity=(isset($all_categories_current_quantities_meta[$current_cart_item])?$all_categories_current_quantities_meta[$current_cart_item]:0)+$rule_buy_datum['getquant'];
                                    if($cart_products[0]['current_item']['quantity']==$all_categories_current_quantity){
                                        if ($flag == false || !$flag) {
                                            $flag_meta[$current_cart_item]=true;
                                            $all_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_categories_current_quantities_meta);

                                        }
                                    }else{
                                        $flag_meta[$current_cart_item]=true;
                                        $all_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_categories_current_quantities_meta);

                                    }
                                }else{
                                    $spec_flag_meta[$current_cart_item]=false;
                                    General::setRuleMeta($eligibleRule['rule_id'], 'spec_products_non_recursive_flag' ,$spec_flag_meta);
                                    $rule_matched = false;
                                    break;
                                }



                            }


                        }

                        if ($rule_buy_datum['buytype'] == 'buyxgetx_all_products') {


                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $current_cart_item_array=array();
                            $current_cart_item_array[]=$current_cart_item;

                            $excluded_product_ids=empty($rule_buy_datum['id'])?array():$rule_buy_datum['id'];
                            $all_products_flag=(count(array_diff($current_cart_item_array,$excluded_product_ids))>0);
                            $spec_flag_meta=General::getRuleMeta($eligibleRule['rule_id'],'spec_products_non_recursive_flag');
                            if($all_products_flag || empty($excluded_product_ids)){
                                $current_quantity=$cart_products[0]['current_item']['quantity'];
                                if($current_quantity >=$rule_buy_datum['min_buyquant'] && $current_quantity<=$rule_buy_datum['max_buyquant']){
                                    $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                    $all_products_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                    $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                    $all_products_current_quantity=(isset($all_products_current_quantities_meta[$current_cart_item])?$all_products_current_quantities_meta[$current_cart_item]:0)+$rule_buy_datum['getquant'];
                                    if($cart_products[0]['current_item']['quantity']==$all_products_current_quantity){
                                        if ($flag == false || !$flag) {
                                            $flag_meta[$current_cart_item]=true;
                                            $all_products_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities_meta);

                                        }
                                    }else{
                                        $flag_meta[$current_cart_item]=true;
                                        $all_products_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_products_current_quantities_meta);

                                    }
                                }else{
                                    $spec_flag_meta[$current_cart_item]=false;
                                    General::setRuleMeta($eligibleRule['rule_id'], 'spec_products_non_recursive_flag' ,$spec_flag_meta);
                                    $rule_matched = false;
                                    break;
                                }



                            }


                        }

                        if($rule_buy_datum['buytype']=='buyxgetx_all_categories'){
                            //get all categories of current cart item
                            //if it is not in the excluded categories then do the magic
                            if(wc_get_product($cart_products[0]['current_item']['product_id'])->is_type('simple')){
                                $current_cart_item=$cart_products[0]['current_item']['product_id'];
                            }else{
                                $current_cart_item=$cart_products[0]['current_item']['variation_id'];
                            }
                            $excluded_categories=empty($rule_buy_datum['id'])?array():$rule_buy_datum['id'];
                            $current_cart_item_categories=wp_list_pluck(Woocommerce::categoriesById($current_cart_item),'term_id');
                            $all_categories_flag=(count(array_diff($current_cart_item_categories,$excluded_categories))>0);
                            $spec_flag_meta=General::getRuleMeta($eligibleRule['rule_id'],'spec_products_non_recursive_flag');

                            if($all_categories_flag || empty($excluded_categories)){
                                $current_quantity=$cart_products[0]['current_item']['quantity'];
                                if($current_quantity >=$rule_buy_datum['min_buyquant'] && $current_quantity <=$rule_buy_datum['max_buyquant']){
                                    $flag_meta = General::getRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag');
                                    $all_categories_current_quantities_meta=General::getRuleMeta($eligibleRule['rule_id'],'all_products_current_quantity');
                                    $flag=isset($flag_meta[$current_cart_item])?$flag_meta[$current_cart_item]:false;
                                    $all_categories_current_quantity=(isset($all_categories_current_quantities_meta[$current_cart_item])?$all_categories_current_quantities_meta[$current_cart_item]:0)+$rule_buy_datum['getquant'];
                                    if($cart_products[0]['current_item']['quantity']==$all_categories_current_quantity){
                                        if ($flag == false || !$flag) {
                                            $flag_meta[$current_cart_item]=true;
                                            $all_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                            General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_categories_current_quantities_meta);

                                        }
                                    }else{
                                        $flag_meta[$current_cart_item]=true;
                                        $all_categories_current_quantities_meta[$current_cart_item]=$cart_products[0]['current_item']['quantity'];
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_recursive_flag' ,$flag_meta);
                                        General::setRuleMeta($eligibleRule['rule_id'], 'all_products_current_quantity', $all_categories_current_quantities_meta);

                                    }
                                }else{
                                    $spec_flag_meta[$current_cart_item]=false;
                                    General::setRuleMeta($eligibleRule['rule_id'], 'spec_products_non_recursive_flag' ,$spec_flag_meta);

                                    $rule_matched = false;
                                    break;
                                }



                            }


                        }
                    }

                }

            }

            if ($rule_matched) {
                $matchedRules[] = $eligibleRule;
            }


        }

        return $matchedRules;

    }

    public static function getValidMatchedRules($matched_rules)
    {

        $valid_matched_rules = array();

        foreach ($matched_rules as $matched_rule) {
            if (self::checkValidity($matched_rule)) {
                $valid_matched_rules[] = $matched_rule;
            }
        }

        return $valid_matched_rules;
    }


    /**
     * @param $rule
     * @return bool
     */
    public static function checkValidity($rule)
    {

        $valid_from = get_post_meta($rule['rule_id'], 'valid_from', true);
        $valid_to = empty(get_post_meta($rule['rule_id'], 'valid_to', true)) ? '2099-01-01T00:00' : get_post_meta($rule['rule_id'], 'valid_to', true);
        $valid_from = str_replace('T', ' ', $valid_from);
        $valid_to = str_replace('T', ' ', $valid_to);

        $current_date=date('Y-m-d H:i');

        $valid = false;
        if ($valid_from <= $current_date && $current_date <= $valid_to) {
            $valid = true;

        }

        return $valid;
    }

    public static function getFirstMatchedRule($cart_products)
    {


        //get all activated rules
        $activated_rules = General::getActivatedRules();
        //get all buyxgetx- recur rules
        $recursive_rule_ids = General::getBuyxgetxRuleIds(true);
        $activated_recursive_rules = array();
        $combined_activated_rules = array();
        foreach ($recursive_rule_ids as $recursive_rule_id) {

            if (Validate::checkValidity($recursive_rule_id)) {
                foreach ($recursive_rule_id['product_ids'] as $recursive_product_id) {

                    if (!Cart::isFreeProduct($recursive_product_id)) {
                        if (in_array($recursive_product_id, array_column($cart_products, 'id'))) {
                            $activated_recursive_rules[] = $recursive_rule_id;
                        }
                    }
                }
            }
        }

        $combined_activated_rules[] = array_column($activated_recursive_rules, 'rule_id');
        $combined_activated_rules[] = wp_list_pluck($activated_rules, 'ID');
        $combined_activated_rules = call_user_func_array('array_merge', $combined_activated_rules);
        sort($combined_activated_rules);
        //combine all ruleids
        //return the first id

        if (!empty($combined_activated_rules)) {
            $combined_activated_rules = $combined_activated_rules[0];
        } else {
            $combined_activated_rules = 0;
        }
        return $combined_activated_rules;
    }


}