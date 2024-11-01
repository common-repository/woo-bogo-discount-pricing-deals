<?php
/**
 *@package CRBOGODeals
 */

namespace CRIncludes\Helper;


/*
 * To avoid being called directly
 */



use CRIncludes\Rules\CheckRules\Validate;

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


/**
 * Class General
 * @package Includes\Helper
 */
class General{

    /**
     * @return mixed
     */
    public static function getRules(){


        if ( false === ( $rules = get_transient( 'crbogo_rules' ) ) ) {
            // It wasn't there, so regenerate the data and save the transient
            if (post_type_exists('cr_bogo_deals')) {
                $args = array(
                    'numberposts' => -1,
                    'post_type' => 'cr_bogo_deals'
                );

                $rules = get_posts($args);
            }
            set_transient( 'crbogo_rules', $rules );
        }

        return get_transient('crbogo_rules');
    }

    /**
     * @param $post_id
     * @param $meta_key
     * @return mixed|string
     */
    public static function getRuleMeta($post_id, $meta_key){

        $metavalue='';

        if(metadata_exists('post', $post_id, $meta_key)){

            $metavalue=get_post_meta($post_id,$meta_key,true);

        }

        return $metavalue;

    }

    /**
     * @param $post_id
     * @param $meta_key
     * @param $meta_value
     */
    public static function setRuleMeta($post_id, $meta_key, $meta_value){

        update_post_meta($post_id,$meta_key,$meta_value);
    }

    /**
     * @param $id
     * @param $array
     * @return null
     */
    public static function searchForId($id, $array) {
        foreach ($array as  $key=>$val) {
            if ($val['id'] == $id) {
                return $array[$key];
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param $array
     * @return null
     */
    public static function searchForProductId($id, $array) {
        foreach ($array as  $key=>$val) {
            if ($val['product_id'] == $id) {
                return $array[$key];
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param $array
     * @return null
     */
    public static function searchForVariationId($id, $array) {
        foreach ($array as  $key=>$val) {
            if ($val['variation_id'] == $id) {
                return $array[$key];
            }
        }
        return null;
    }

    public static function getBuyxgetxRuleIds($recursion=true){
        $rules=Validate::getRuleIds();
        $recursion_rule_ids=array();
        $non_recursion_rule_ids=array();
        foreach ($rules as $rule){

            $rule_buy_data=General::getRuleMeta($rule['rule_id'],'rule_buy');
            if(!empty($rule_buy_data)){
                foreach ($rule_buy_data as $rule_buy_datum){
                    if(array_key_exists('recursive', $rule_buy_datum)){

                        if($rule_buy_datum['recursive']==true){
                            $recursion_rule_ids[]=$rule;
                        }else{
                            $non_recursion_rule_ids[]=$rule;
                        }

                    }
                }

            }

        }

        if($recursion==true){
            return $recursion_rule_ids;
        }else{
            return $non_recursion_rule_ids;
        }


    }

    public static function getActivatedRules(){
        $activated_rules=array();
        $rules=self::getRules();
        foreach ($rules as $rule){
            if(self::getRuleMeta($rule->ID,'active')){
                $activated_rules[]=$rule;
            }
        }

        return $activated_rules;
    }

    public static function getExcludedProducts($exclude_products){
        $product_ids=array();
        $all_products = Woocommerce::getAllProducts();
        foreach ($all_products as $all_product) {
            //get variations
            $_product = wc_get_product($all_product);
            if ($_product->is_type('variable')) {
                $_product_variations = $_product->get_available_variations();
                foreach ($_product_variations as $product_variation) {
                    if (!in_array($product_variation['variation_id'], $exclude_products)) {
                        $product_ids[] = $product_variation['variation_id'];
                    }
                }
            } else {
                if (!in_array($all_product, $exclude_products)) {
                    $product_ids[] = $all_product;
                }
            }

        }

        return $product_ids;
    }
}