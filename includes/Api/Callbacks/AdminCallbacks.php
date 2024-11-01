<?php
/**
 * @package CRBOGODeals
 *
 */


namespace CRIncludes\Api\Callbacks;


use CRIncludes\Base\BaseController;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class AdminCallbacks extends BaseController
{


    public function ruleMethodMetabox($post)
    {

        $method_option = get_post_meta($post->ID, 'rule_buy_method', true);
        ?>

        <div class="woo_cr_rule_method_content">
            <label for="woo_cr_rule_buy_method"
                   class="woo_cr_label"><?php esc_html_e('Choose Method', 'woocommerce-bogo-discount-pricing-deals') ?></label>


            <select name="woo_cr_rule_buy_method" id="woo_cr_rule_buy_method">
                <option value="default"><?php esc_html_e('--Choose Method--', 'woocommerce-bogo-discount-pricing-deals'); ?></option>
                <option <?php if ('buyxgetx' == $method_option) esc_html_e( 'selected','woocommerce-bogo-discount-pricing-deals'); ?>
                        value="buyxgetx"><?php esc_html_e('Buy X Get X', 'woocommerce-bogo-discount-pricing-deals') ?>
                </option>
                <option <?php if ('buyxgety' == $method_option) esc_html_e( 'selected','woocommerce-bogo-discount-pricing-deals'); ?>
                        value="buyxgety" disabled="disabled"><?php esc_html_e('Buy X Get Y- Pro', 'woocommerce-bogo-discount-pricing-deals') ?>
                </option>
                <option <?php if ('cart_subtotal' == $method_option) echo 'selected'; ?>
                        value="cart_subtotal" disabled="disabled"><?php _e('Cart Subtotal- Pro', 'woocommerce-bogo-discount-pricing-deals') ?>
                </option>
                <option <?php if ('cart_total' == $method_option) echo 'selected'; ?>
                        value="cart_total" disabled="disabled"><?php _e('Cart Total- Pro', 'woocommerce-bogo-discount-pricing-deals') ?>
                </option>
                <option <?php if ('cart_total_items' == $method_option) echo 'selected'; ?>
                        value="cart_total_items" disabled="disabled"><?php _e('Cart Total Items- Pro', 'woocommerce-bogo-discount-pricing-deals') ?>
                </option>
                <option <?php if ('cart_total_quantity' == $method_option) echo 'selected'; ?>
                        value="cart_total_quantity" disabled="disabled"><?php _e('Cart Total Quantity- Pro', 'woocommerce-bogo-discount-pricing-deals') ?>
                </option>
            </select>
        </div>

        <?php

    }

    public function ruleBuyxgetxMetabox($post)
    {
        ?>
        <div class="woo_cr_buyxgetx_content">
            <input type="hidden" name="woo_cr_buyxgetx_buy_data" id="woo_cr_buyxgetx_buy_data">
            <div class="woo_cr_buyxgetx_option_content">
                <?php
                $buy_option = get_post_meta($post->ID, "rule_buy");
                woocommerce_wp_select(array(
                    'id' => 'woo_cr_buyxgetx_option',
                    'name' => 'woo_cr_buyxgetx_option',
                    'label' => false,
                    'value' => isset($buy_option[0][0]['option'])?$buy_option[0][0]['option']:'',
                    'options' => array(
                        'default' => __('--Select Type--', 'woocommerce-bogo-discount-pricing-deals'),
                        'product' => __('Specific Products', 'woocommerce-bogo-discount-pricing-deals'),
                        'category' => __('Specific Categories', 'woocommerce-bogo-discount-pricing-deals'),
                        'allproducts' => __('All Products', 'woocommerce-bogo-discount-pricing-deals'),
                        'allcategories' => __('All Categories', 'woocommerce-bogo-discount-pricing-deals')
                    )
                ));

                ?>



            </div>
            <br>
            <div class="woo_cr_buyxgetx_product_category_container">

                <div class="woo_cr_buyxgetx_product_category_content">
                    <?php

                    if (isset($buy_option[0])) {
                        if ($buy_option[0]) {
                            foreach ($buy_option[0] as $item => $value) {
                                if (isset($value['buytype'])) {
                                    if ($value['buytype'] == 'buyxgetx_product') {
                                        ?>
                                        <div class="woo_cr_buyxgetx_product_category_newrow"
                                             id="woo_cr_buyxgetx_product_category_row">

                                            <label
                                                    class="woo_cr_label"><?php esc_html_e('Product', 'woocommerce-bogo-discount-pricing-deals') ?></label>

                                            <select class="wc-product-search" name='woo_cr_buyxgetx_product' multiple="multiple"
                                                    id='woo_cr_buyxgetx_product'
                                                    style="width: 50%;"
                                                    data-placeholder="<?php esc_attr_e('Buy and Get Product', 'woocommerce-bogo-discount-pricing-deals'); ?>"
                                                    data-action="woocommerce_json_search_products_and_variations">
                                                <?php
                                                foreach ($value['id'] as $product_id) {
                                                    $product = wc_get_product($product_id);
                                                    if (is_object($product)) {
                                                        echo '<option value="' . esc_attr($product_id) . '" selected="selected"> ' . get_the_title($product_id) . '</option>';
                                                    }

                                                }

                                                ?>
                                            </select>

                                            <input type='number' name='woo_cr_buyxgetx_product_min_buy_quant' min="1"
                                                   value="<?php isset($value['min_buyquant']) ? esc_attr_e($value['min_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_product_min_buy_quant'
                                                   placeholder="<?php esc_attr_e('Min Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>" >  </select>

                                            <input type='number' name='woo_cr_buyxgetx_product_max_buy_quant' min="1"
                                                   value="<?php isset($value['max_buyquant']) ? esc_attr_e($value['max_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_product_max_buy_quant'
                                                   placeholder="<?php esc_attr_e('Max Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_product_get_quant' min="1"
                                                   value="<?php isset($value['getquant']) ? esc_attr_e($value['getquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>"
                                                   id='woo_cr_buyxgetx_product_get_quant'
                                                   placeholder="<?php esc_attr_e('Get Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">
                                            <input type='checkbox' name='woo_cr_buyxgetx_product_recursive'
                                                   id='woo_cr_buyxgetx_product_recursive'
                                                <?php isset($value['recursive']) && $value['recursive'] == true ? esc_attr_e('checked','woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>>
                                            <?php esc_html_e('Recursive', 'woocommerce-bogo-discount-pricing-deals'); ?>


                                        </div>

                                        <?php
                                    } elseif ($value['buytype'] == 'buyxgetx_category') {
                                        ?>

                                        <div class="woo_cr_buyxgetx_product_category_newrow"
                                             id="woo_cr_buyxgetx_product_category_row">
                                            <label
                                                    class="woo_cr_label"><?php esc_html_e('Category', 'woocommerce-bogo-discount-pricing-deals') ?></label>

                                            <!--Category Select-->
                                            <select name="woo_cr_buyxgetx_category" style="width: 50%;"
                                                    class="wc-enhanced-select" multiple="multiple"
                                                    data-placeholder="<?php esc_attr_e('Enter Buy and Get Category', 'woocommerce-bogo-discount-pricing-deals'); ?>">
                                                <option value="default"><?php esc_html_e('--Select a Category--', 'woocommerce-bogo-discount-pricing-deals'); ?></option>
                                                <?php
                                                $categories = get_terms('product_cat');

                                                if ($categories) {
                                                    foreach ($categories as $category) {
                                                        ?>
                                                        <option <?php if(!empty($value['id'])) if (in_array( $category->term_id, $value['id'] )) esc_html_e( 'selected','woocommerce-bogo-discount-pricing-deals'); ?>
                                                                value="<?php esc_attr_e($category->term_id,'woocommerce-bogo-discount-pricing-deals') ?>"><?php esc_html_e( $category->name,'woocommerce-bogo-discount-pricing-deals') ?></option>
                                                        <?php


                                                    }

                                                }

                                                ?>
                                            </select>

                                            <input type='number' name='woo_cr_buyxgetx_category_min_buy_quant' min="1"
                                                   value="<?php isset($value['min_buyquant']) ? esc_attr_e($value['min_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_category_min_buy_quant'
                                                   placeholder="<?php esc_attr_e('Min Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_category_max_buy_quant' min="1"
                                                   value="<?php isset($value['max_buyquant']) ? esc_attr_e($value['max_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_category_max_buy_quant'
                                                   placeholder="<?php esc_attr_e('Max Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_category_get_quant' min="1"
                                                   value="<?php isset($value['getquant']) ? esc_attr_e($value['getquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>"
                                                   id='woo_cr_buyxgetx_category_get_quant'
                                                   placeholder="<?php esc_attr_e('Get Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">
                                            <input type='checkbox' name='woo_cr_buyxgetx_category_recursive'
                                                   id='woo_cr_buyxgetx_category_recursive'
                                                <?php isset($value['recursive']) && $value['recursive'] == true ? esc_attr_e('checked','woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>>
                                            <?php esc_html_e('Recursive', 'woocommerce-bogo-discount-pricing-deals'); ?>

                                        </div>

                                        <?php

                                    } elseif ($value['buytype'] == 'buyxgetx_all_products') {
                                        ?>

                                        <div class="woo_cr_buyxgetx_product_category_newrow"
                                             id="woo_cr_buyxgetx_product_category_row">

                                            <input type='number' name='woo_cr_buyxgetx_all_products_min_buy_quant'
                                                   min="1"
                                                   value="<?php isset($value['min_buyquant']) ? esc_attr_e($value['min_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_all_products_min_buy_quant'
                                                   placeholder="<?php esc_attr_e('Min Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_all_products_max_buy_quant'
                                                   min="1"
                                                   value="<?php isset($value['max_buyquant']) ? esc_attr_e($value['max_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_all_products_max_buy_quant'
                                                   placeholder="<?php esc_attr_e('Max Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_all_products_get_quant' min="1"
                                                   value="<?php isset($value['getquant']) ? esc_attr_e($value['getquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>"
                                                   id='woo_cr_buyxgetx_all_products_get_quant'
                                                   placeholder="<?php esc_attr_e('Get Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">
                                            <input type='checkbox' name='woo_cr_buyxgetx_all_products_recursive'
                                                   id='woo_cr_buyxgetx_all_products_recursive'
                                                <?php isset($value['recursive']) && $value['recursive'] == true ? esc_attr_e('checked','woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>>
                                            <?php esc_html_e('Recursive', 'woocommerce-bogo-discount-pricing-deals'); ?>

                                        </div>

                                        <?php
                                    }elseif ($value['buytype']=='buyxgetx_all_categories'){
                                        ?>
                                        <div class="woo_cr_buyxgetx_product_category_newrow"
                                             id="woo_cr_buyxgetx_product_category_row">

                                            <input type='number' name='woo_cr_buyxgetx_all_categories_min_buy_quant'
                                                   min="1"
                                                   value="<?php isset($value['min_buyquant']) ? esc_attr_e($value['min_buyquant'],'woocommerce-bogo-discount-pricing-deals') :esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_all_categories_min_buy_quant'
                                                   placeholder="<?php esc_attr_e('Min Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_all_categories_max_buy_quant'
                                                   min="1"
                                                   value="<?php isset($value['max_buyquant']) ? esc_attr_e($value['max_buyquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals'); ?>"
                                                   id='woo_cr_buyxgetx_all_categories_max_buy_quant'
                                                   placeholder="<?php esc_attr_e('Max Buy Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">

                                            <input type='number' name='woo_cr_buyxgetx_all_categories_get_quant' min="1"
                                                   value="<?php isset($value['getquant']) ? esc_attr_e($value['getquant'],'woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>"
                                                   id='woo_cr_buyxgetx_all_categories_get_quant'
                                                   placeholder="<?php esc_attr_e('Get Quantity', 'woocommerce-bogo-discount-pricing-deals'); ?>">
                                            <input type='checkbox' name='woo_cr_buyxgetx_all_categories_recursive'
                                                   id='woo_cr_buyxgetx_all_categories_recursive'
                                                <?php isset($value['recursive']) && $value['recursive'] == true ? esc_attr_e('checked','woocommerce-bogo-discount-pricing-deals') : esc_attr_e('','woocommerce-bogo-discount-pricing-deals') ?>>
                                            <?php esc_html_e('Recursive', 'woocommerce-bogo-discount-pricing-deals'); ?>


                                        </div>

                                        <?php
                                    }
                                }


                            }
                        }
                    }


                    ?>

                </div>

            </div>

        </div>

        <?php
    }

    public function ruleExcludeProductsMetabox($post)
    {

        ?>
        <div class="woo_cr_buyxgetx_exclude_products_content">
            <?php
            $buy_option = get_post_meta($post->ID, "rule_buy");
            if (isset($buy_option[0])) {
                if ($buy_option[0]) {
                    foreach ($buy_option[0] as $item => $value) {
                        if (isset($value['buytype'])) {
                            if ($value['buytype'] == 'buyxgetx_all_products') {
                                ?>
                                <select class="wc-product-search" name='woo_cr_buyxgetx_all_products'
                                        id='woo_cr_buyxgetx_all_products' multiple="multiple"
                                        style="width: 50%;"
                                        data-placeholder="<?php esc_attr_e('Products to be excluded', 'woocommerce-bogo-discount-pricing-deals'); ?>"
                                        data-action="woocommerce_json_search_products_and_variations">
                                    <?php
                                    foreach ($value['id'] as $product_id) {
                                        $product = wc_get_product($product_id);
                                        if (is_object($product)) {
                                            echo '<option value="' . esc_attr($product_id) . '" selected="selected"> ' . get_the_title($product_id) . '</option>';
                                        }

                                    }
                                    ?>
                                </select>
                                <?php
                            } else {
                                ?>
                                <select class="wc-product-search" name='woo_cr_buyxgetx_all_products'
                                        id='woo_cr_buyxgetx_all_products' multiple="multiple"
                                        style="width: 50%;"
                                        data-placeholder="<?php esc_attr_e('Products to be excluded', 'woocommerce-bogo-discount-pricing-deals'); ?>"
                                        data-action="woocommerce_json_search_products_and_variations">
                                </select>
                                <?php
                            }
                        }
                    }
                }
            }else {
                ?>
                <select class="wc-product-search" name='woo_cr_buyxgetx_all_products'
                        id='woo_cr_buyxgetx_all_products' multiple="multiple"
                        style="width: 50%;"
                        data-placeholder="<?php esc_attr_e('Products to be excluded', 'woocommerce-bogo-discount-pricing-deals'); ?>"
                        data-action="woocommerce_json_search_products_and_variations">
                </select>
                <?php
            }
            ?>

        </div>
        <?php
    }

    public function ruleExcludeCategoriesMetabox($post)
    {

        ?>
        <div class="woo_cr_buyxgetx_exclude_categories_content">
            <?php
            $buy_option = get_post_meta($post->ID, "rule_buy");
            if (isset($buy_option[0])) {
                if ($buy_option[0]) {
                    foreach ($buy_option[0] as $item => $value) {
                        if (isset($value['buytype'])) {
                            if ($value['buytype'] == 'buyxgetx_all_categories' && !is_null($value['id'])) {
                                ?>

                                <select name="woo_cr_buyxgetx_all_categories" id="woo_cr_buyxgetx_all_categories"
                                        style="width: 50%;" class="wc-enhanced-select"  multiple="multiple"
                                        data-placeholder="<?php esc_attr_e('Categories to be excluded', 'woocommerce-bogo-discount-pricing-deals'); ?>"" >

                                <?php


                                $categories = get_terms('product_cat');
                                if ($categories) {
                                    foreach ($categories as $category) {
                                      ?>
                                           <option <?php if (in_array( $category->term_id, $value['id'] )) esc_html_e( 'selected','woocommerce-bogo-discount-pricing-deals'); ?>
                                                   value="<?php esc_attr_e($category->term_id,'woocommerce-bogo-discount-pricing-deals') ?>"><?php esc_html_e( $category->name,'woocommerce-bogo-discount-pricing-deals') ?></option>
                                           <?php


                                    }

                                }
                                ?>
                                </select>
                                <?php
                            } else {
                                ?>
                                <select class="wc-enhanced-select" name='woo_cr_buyxgetx_all_categories'
                                        id='woo_cr_buyxgetx_all_categories' multiple="multiple"
                                        style="width: 50%;"
                                        data-placeholder="<?php esc_attr_e('Categories to be excluded', 'woocommerce-bogo-discount-pricing-deals'); ?>"
                                       >
                                    <?php
                                    $categories = get_terms('product_cat');
                                    if ($categories) {
                                        foreach ($categories as $category) {
                                            ?>
                                            <option value="<?php esc_attr_e($category->term_id,'woocommerce-bogo-discount-pricing-deals') ?>"><?php esc_html_e( $category->name,'woocommerce-bogo-discount-pricing-deals') ?></option>
                                            <?php
                                        }

                                    }
                                    ?>
                                </select>
                                <?php
                            }
                        }
                    }
                }
            }else {
                ?>
                <select class="wc-enhanced-select" name='woo_cr_buyxgetx_all_categories'
                        id='woo_cr_buyxgetx_all_categories' multiple="multiple"
                        style="width: 50%;"
                        data-placeholder="<?php esc_attr_e('Categories to be excluded', 'woocommerce-bogo-discount-pricing-deals'); ?>"
                >
                    <?php
                    $categories = get_terms('product_cat');
                    if ($categories) {
                        foreach ($categories as $category) {
                            ?>
                            <option value="<?php esc_attr_e($category->term_id,'woocommerce-bogo-discount-pricing-deals') ?>"><?php esc_html_e( $category->name,'woocommerce-bogo-discount-pricing-deals') ?></option>
                            <?php
                        }

                    }
                    ?>
                </select>
                <?php
            }
            ?>

        </div>
        <?php
    }

    public function ruleEnableDisableMetabox($post){
        $enable_disable_toggle = get_post_meta($post->ID, "enable_disable_toggle", true);
        ?>
        <label class="cr-bogo-switch">
            <input type="checkbox" name="enable_disable_toggle" id="enable_disable_toggle"  <?php if ($enable_disable_toggle) echo 'checked'; ?>>
            <span class="cr-bogo-slider"></span>
        </label>
        <?php
    }



    public function ruleValidityMetabox($post)
    {
        $valid_from = get_post_meta($post->ID, "valid_from", true);
        $valid_to = get_post_meta($post->ID, "valid_to", true);

        $current_date = date('Y-m-d H:i');
        ?>
        <div class="woo_cr_validity_content">

            <label class="woo_cr_label"><?php esc_html_e('Current Date and Time : ', 'woocommerce-bogo-discount-pricing-deals');;
                esc_html_e( $current_date,'woocommerce-bogo-discount-pricing-deals') ?></label>

            <br><br>

            <?php esc_html_e('From', 'woocommerce-bogo-discount-pricing-deals') ?> <input type="datetime-local"
                                                                value="<?php esc_attr_e($valid_from,'woocommerce-bogo-discount-pricing-deals') ?>" name="valid_from"
                                                                id="valid_from">
            <?php esc_html_e('To', 'woocommerce-bogo-discount-pricing-deals') ?> <input type="datetime-local" value="<?php print_r($valid_to) ?>"
                                                              name="valid_to"
                                                              id="valid_to"><?php esc_html_e("Leave 'To' empty if no expiry", 'woocommerce-bogo-discount-pricing-deals') ?>
        </div>

        <?php

    }


}