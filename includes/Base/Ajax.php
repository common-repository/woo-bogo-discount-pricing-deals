<?php


/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Base;

use CRIncludes\Base\BaseController;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class Ajax extends BaseController
{

    public function register()
    {


        //buyxgetx category
        add_action('wp_ajax_add_buyxgetx_category_row', array($this, 'addBuyxgetxCategoryRow'));



    }



    function addBuyxgetxCategoryRow(){

        echo '<label class="woo_cr_label">';
        esc_html_e('Category', 'woocommerce-bogo-discount-pricing-deals');
        echo '</label> <select name="woo_cr_buyxgetx_category" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="';
        esc_attr_e('Enter Buy and Get Product', 'woocommerce-bogo-discount-pricing-deals');
        echo '">';
        echo '<option value="default" >';
        esc_html_e('--Enter Buy and Get Category--', 'woocommerce-bogo-discount-pricing-deals');
        echo '</option>';
        $categories = get_terms( 'product_cat');
        if ($categories):

            foreach ($categories as $category):
                echo '<option value="' . esc_attr( $category->term_id ) . '" > ' .$category->name . '</option>';

            endforeach;
        endif;
        echo '</select><input type="number" name="woo_cr_buyxgetx_category_min_buy_quant"
                                       value="" id="woo_cr_buyxgetx_category_min_buy_quant"
                                       placeholder="';
        esc_attr_e('Min Buy Quantity', 'woocommerce-bogo-discount-pricing-deals');
        echo '">';
        echo '<input type="number" name="woo_cr_buyxgetx_category_max_buy_quant"
                                       value="" id="woo_cr_buyxgetx_category_max_buy_quant"
                                       placeholder="';
        esc_attr_e('Max Buy Quantity', 'woocommerce-bogo-discount-pricing-deals');
        echo '">';
        echo '<input type="number" name="woo_cr_buyxgetx_category_get_quant" min="1" value="" 
        id="woo_cr_buyxgetx_category_get_quant"
                                        placeholder="';
        esc_attr_e('Get Quantity', 'woocommerce-bogo-discount-pricing-deals');
        echo '" >';
                   echo '<input type="checkbox" name="woo_cr_buyxgetx_category_recursive" id="woo_cr_buyxgetx_category_recursive">';
        esc_html_e('Recursive', 'woocommerce-bogo-discount-pricing-deals');



        wp_die();
    }



}
