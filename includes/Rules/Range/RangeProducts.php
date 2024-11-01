<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules\Range;


use CRIncludes\Helper\Woocommerce;
use CRIncludes\Rules\Cart;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class RangeProducts{


    public static function get($range, $quantity){

        $updated_all_products=array();
        $range_products=array();

        //get all products of get_category_id
        $get_products=wp_list_pluck(get_posts(array(
            'post_type'=>'product','posts_per_page' => -1,
        )),'ID');
        $free_cart_products=Woocommerce::getFreeCartProducts();
        foreach ($get_products as $get_product){
            $_product=wc_get_product($get_product);
            if($_product->is_type('variable')){
                $product_variations=$_product->get_available_variations();
                foreach ($product_variations as $product_variation){
                    if( !Cart::isBuyXGetX($product_variation['variation_id']) && !in_array($product_variation['variation_id'],$free_cart_products)){
                        $updated_all_products[]=$product_variation['variation_id'];
                    }
                }
            }else{
                if( !Cart::isBuyXGetX($get_product) && !in_array($get_product,$free_cart_products)){
                    $updated_all_products[]=$get_product;
                }
            }

        }

        if(count($updated_all_products)>=$quantity){
            foreach ($updated_all_products as $updated_all_product){
                $_product=wc_get_product($updated_all_product);
                $product_price[$_product->get_price()]=$updated_all_product;
            }
            if($range=='lowest')
                ksort($product_price);
            else
                krsort($product_price);
            $range_products=array_slice($product_price,0,$quantity,true);

        }

        return $range_products;
    }
}