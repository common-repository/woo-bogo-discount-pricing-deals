<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules\Range;

use CRIncludes\Helper\General;
use CRIncludes\Rules\Cart;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class RangeCart{

    public static function get($range, $quantity, $cart_products){

        $updated_cart_products=array();
        $range_products=array();


        //remove buy category based products
        foreach ($cart_products as $cart_product){
            if( !Cart::isBuyXGetX($cart_product['id']) ){
                $updated_cart_products[]=$cart_product;
            }
        }

        if(count($updated_cart_products)>=$quantity){
           foreach ($updated_cart_products as $updated_cart_product){
               $_product=wc_get_product($updated_cart_product['id']);
               $product_price[$_product->get_price()]=$updated_cart_product['id'];
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
