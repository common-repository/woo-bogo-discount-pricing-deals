<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Rules;

use CRIncludes\Helper\General;
use CRIncludes\Helper\Woocommerce;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class Cart{

    /**
     * @param array $current_item
     * @return array
     */
    public static function setSessionAddedCartItems($current_item=array()){


        WC()->session->set('cart_products','');

        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $cart_products = array();


        foreach ($items as $item => $values) {

            $cart_product_price = $values['data']->get_price();
            if ($cart_product_price != 0) {
                $cart_item_id=$values['data']->get_id();
                $cart_item_quant=$values['quantity'];
                $cart_item_categories= Woocommerce::categoriesById($values['data']->get_id());

                $cart_item = array(
                    'current_item'=>$current_item,
                    'id' => $cart_item_id,
                    'quant' => $cart_item_quant,
                    'categories' => $cart_item_categories,

                );
                $cart_products[] = $cart_item;

            }
        }

        WC()->session->set('cart_products',$cart_products);

        return $cart_products;
    }

    /**
     * @return array
     */
    public static function addedCartData(){

        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $totalquant = 0;
        $line_subtotal = 0;
        $total_cartitems=0;

        foreach ($items as $item => $values) {
            $total_cartitems++;
            $totalquant += $values['quantity'];
            $line_subtotal += isset($values['line_total']) ? $values['line_total'] : $values['quantity'] * $values['data']->get_price();
        }

        $fee=WC()->cart->get_fees();
        $cart_data = array(
            'total_quantities' => $totalquant,
            'subtotal' => $line_subtotal,
            'total_cartitems' => $total_cartitems
        );

        $_SESSION['cart_data']=$cart_data;
        $_SESSION['cart']=Woocommerce::getCart();
        return $cart_data;
    }

    /**
     * @param $cart_item_key
     * @param $current_quantity
     * @return array
     */
    public static function setSessionUpdatedCartProducts($cart_item_key, $current_quantity){

        WC()->session->set('cart_products','');

        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $cart_products = array();
        $updated_product_id = $items[$cart_item_key]['product_id'];
        $_SESSION['current_item']= $items[$cart_item_key];

        foreach ($items as $item => $values) {
            $cart_product_price = $values['data']->get_price();
            if ($cart_product_price != 0) {
                $current_quant = $values['quantity'];
                if ($values['data']->get_id() == $updated_product_id) {
                    $current_quant = $current_quantity;
                }
                if ($current_quant != 0) {
                    $cart_item = array(
                        'current_item'=>$items[$cart_item_key],
                        'id' => $values['data']->get_id(),
                        'quant' => $current_quant,
                        'categories' => Woocommerce::categoriesById($values['data']->get_id()),

                    );
                }

                $cart_products[] = $cart_item;
            }
        }


        WC()->session->set('cart_products',$cart_products);

        return $cart_products;
    }

    /**
     * @param $cart_item_key
     * @param $current_quantity
     * @return array
     */
    public static function updatedCartData($cart_item_key, $current_quantity){

        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $updated_subtotal=0;
        $updated_quantity=0;
        $updated_no_cartitems=0;
        $updated_product_id = $items[$cart_item_key]['product_id'];


        foreach ($items as $item => $values) {
            $cart_product_price = $values['data']->get_price();
            if ($cart_product_price != 0) {
                $updated_no_cartitems++;
                $current_quant = $values['quantity'];
                if ($values['data']->get_id() == $updated_product_id) {
                    $updated_subtotal += $cart_product_price * $current_quantity;
                    $updated_quantity += $current_quantity;
                } else {
                    $updated_subtotal += $cart_product_price * $current_quant;
                    $updated_quantity += $current_quant;
                }
            }
        }
        $cart_data = array(
            'current_item'=>$items[$cart_item_key],
            'total_quantities' => $updated_quantity,
            'subtotal' => $updated_subtotal,
            'total_cartitems' =>$updated_no_cartitems
        );

        $_SESSION['cart_data']=$cart_data;

        return $cart_data;
    }

    /**
     * @param $cart_item_key
     * @param $cart
     * @return array
     */
    public static function setSessionRemovedCartProducts($cart_item_key, $cart){

        WC()->session->set('cart_products','');


        $removed_product_id = $cart->cart_contents[$cart_item_key]['product_id'];
        $removed_variation_id=$cart->cart_contents[$cart_item_key]['variation_id'];
        $removed_product_quant = $cart->cart_contents[$cart_item_key]['quantity'];


        //iterate tru cartitems and subtract quantity of item to remove and check with rule
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $cart_products = array();


        foreach ($items as $item => $values) {
            $cart_product_price = $values['data']->get_price();
            if ($cart_product_price != 0) {
                $current_quant = $values['quantity'];
                if ($values['data']->get_id() == $removed_product_id || $values['data']->get_id()==$removed_variation_id) {
                    $current_quant = $values['quantity'] - $removed_product_quant;

                }
                if ($current_quant != 0) {
                    $cart_item = array(
                        'current_item'=>$items[$cart_item_key],
                        'id' => $values['data']->get_id(),
                        'quant' => $current_quant,
                        'categories' => Woocommerce::categoriesById($values['data']->get_id()),
                    );

                    $cart_products[] = $cart_item;
                }


            }
        }

        WC()->session->set('cart_products',$cart_products);

        return $cart_products;

    }

    public static function isFreeProduct($product_id=0, $variation_id=0){

        global $woocommerce;
        $items = $woocommerce->cart->get_cart();


        foreach ($items as $item => $values) {
            $cart_product_price = $values['data']->get_price();
            if  ( ($product_id == $values['data']->get_id() && $cart_product_price == 0) || ($variation_id==$values['data']->get_id() && $cart_product_price==0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $cart_item_key
     * @param $cart
     * @return array
     */
    public static function removedCartData($cart_item_key, $cart){

        $removed_product_id = $cart->cart_contents[$cart_item_key]['product_id'];
        $removed_variation_id=$cart->cart_contents[$cart_item_key]['variation_id'];
        $removed_product_quant = $cart->cart_contents[$cart_item_key]['quantity'];

        $removed_product_subtotal = $cart->cart_contents[$cart_item_key]['line_total'];
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $total_cartitems=0;

        foreach ($items as $item => $values) {
            $cart_product_price = $values['data']->get_price();
            if ($cart_product_price != 0) {
                $current_quant = $values['quantity'];
                if ($values['data']->get_id() == $removed_product_id || $values['data']->get_id()==$removed_variation_id) {
                    $current_quant = $values['quantity'] - $removed_product_quant;
                }
                if ($current_quant != 0) {
                    $total_cartitems++;
                    $cart_item = array(
                        'id' => $values['data']->get_id(),
                        'quant' => $current_quant,
                        'categories' => get_the_terms($values['data']->get_id(), 'product_cat')
                    );

                    $cart_products[] = $cart_item;
                }


            }
        }
        $cart_data = array(
            'total_quantities' => $woocommerce->cart->get_cart_contents_count()-$removed_product_quant,
            'subtotal' => $woocommerce->cart->get_subtotal()-$removed_product_subtotal,
            'total_cartitems' =>$total_cartitems
        );

        $_SESSION['cart_data']=$cart_data;
        return $cart_data;
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


    public static function isBuyXGetX($product_id=0, $variation_id=0){


        //get buyxgetx rules

        $rules=General::getRules();

        $buyxgetx=array();

        foreach ($rules as $rule){
            $rule_buy=General::getRuleMeta($rule->ID,'rule_buy');
            if(isset($rule_buy[0]['buytype'])){
                if($rule_buy[0]['buytype']=='buyxgetx_product'){
                    $buyxgetx[]=$rule_buy[0]['id'];
                }
            }
            
        }

        if(in_array($product_id,$buyxgetx) || in_array($variation_id,$buyxgetx)){
            return true;
        }
        return false;

    }


    public static function isValidRuleProduct($product_id=0, $variation_id=0){

        $rules=General::getRules();
        $valid_rules_buy_prod_data=array();
        $valid_rules_buy_cat_data=array();
        foreach ($rules as $rule){

                $buy_type=General::getRuleMeta($rule->ID,'rule_buy');
                foreach ($buy_type as $value){
                    if($value['buytype']=='product'){
                        $valid_rules_buy_prod_data[]=$value['id'];
                    }elseif ($value['buytype']=='category'){
                        $valid_rules_buy_cat_data[]=$value['id'];
                    }elseif ($value['buytype']=='buyxgetx_category'){
                        $valid_rules_buy_cat_data=$value['id'];
                    }elseif ($value['buytype']=='buyxgetx_product'){
                        $valid_rules_buy_prod_data=$value['id'];
                    }
                }


        }

        //get category of product_id
        $post_categories = wp_list_pluck(Woocommerce::categoriesById($product_id),'term_id');

        if(in_array($product_id,$valid_rules_buy_prod_data) || in_array($variation_id,$valid_rules_buy_prod_data)){
            return true;
        }elseif( (count($post_categories) >= count(array_intersect($post_categories, $valid_rules_buy_cat_data)))){
            return true;
        }
        return false;

    }




}