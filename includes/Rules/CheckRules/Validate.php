<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules\CheckRules;

use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;
use CRIncludes\Rules\Cart;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


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

            if(!empty($buy_data)){
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

        if (empty($valid_from)) {
            $valid_from = $current_date;
        }
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

    public static function isEnabled($rule){
        $enable_disable_toggle = get_post_meta($rule['rule_id'], 'enable_disable_toggle', true);
        if($enable_disable_toggle){
            return true;
        }

        return false;
    }


}