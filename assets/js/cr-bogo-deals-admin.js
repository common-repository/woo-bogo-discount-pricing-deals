jQuery(document).ready(function ($) {



    /*
     Hide metaboxes- initial or display metabox according to the selected method
      */

    if ($("#woo_cr_rule_buy_method option:selected").val() != 'default') {

        var method_selected = $("#woo_cr_rule_buy_method option:selected").val();
        switch (method_selected) {
            case 'buyxgetx':

                $('#woo_cr_get_metabox').hide();
                $('#woo_cr_buyxgety_metabox').hide();
                $('#woo_cr_quantitybased_metabox').hide();
                $('#woo_cr_subtotalbased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').show();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
            case 'buyxgety':

                $('#woo_cr_quantitybased_metabox').hide();
                $('#woo_cr_subtotalbased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').hide();
                $('#woo_cr_buyxgety_metabox').show();
                $('#woo_cr_get_metabox').show();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
            case 'quantitybased':
                $('#woo_cr_quantitybased_metabox').show();
                $('#woo_cr_get_metabox').show();
                $('#woo_cr_buyxgety_metabox').hide();
                $('#woo_cr_subtotalbased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').hide();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
            case 'subtotalbased':
                $('#woo_cr_subtotalbased_metabox').show();
                $('#woo_cr_get_metabox').show();
                $('#woo_cr_buyxgety_metabox').hide();
                $('#woo_cr_quantitybased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').hide();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
        }
    } else {
        $('#woo_cr_buyxgety_metabox, #woo_cr_quantitybased_metabox, #woo_cr_subtotalbased_metabox, #woo_cr_get_metabox, #woo_cr_buyxgetx_metabox, #woo_cr_exclude_products_metabox, #woo_cr_exclude_categories_metabox').hide();

    }


    /*
    based on choice of method display metabox
     */
    $('#woo_cr_rule_buy_method').change(function () {

        var method_choice = $(this).val();
        switch (method_choice) {
            case 'buyxgetx':
                $('#woo_cr_buyxgetx_metabox').show();
                $('#woo_cr_get_metabox').hide();
                $('#woo_cr_buyxgety_metabox').hide();
                $('#woo_cr_quantitybased_metabox').hide();
                $('#woo_cr_subtotalbased_metabox').hide();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
            case 'buyxgety':
                $('#woo_cr_buyxgety_metabox').show();
                $('#woo_cr_get_metabox').show();
                $('#woo_cr_quantitybased_metabox').hide();
                $('#woo_cr_subtotalbased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').hide();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();

                break;
            case 'quantitybased':
                $('#woo_cr_buyxgety_metabox').hide();
                $('#woo_cr_quantitybased_metabox').show();
                $('#woo_cr_get_metabox').show();
                $('#woo_cr_subtotalbased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').hide();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
            case 'subtotalbased':
                $('#woo_cr_buyxgety_metabox').hide();
                $('#woo_cr_quantitybased_metabox').hide();
                $('#woo_cr_buyxgetx_metabox').hide();
                $('#woo_cr_subtotalbased_metabox').show();
                $('#woo_cr_get_metabox').show();
                $('#woo_cr_exclude_products_metabox').hide();
                $('#woo_cr_exclude_categories_metabox').hide();
                break;
        }
    });


    $('#publish').on('click', function () {

        if ($("#woo_cr_rule_buy_method option:selected").val() != 'default') {
            var method_selected = $("#woo_cr_rule_buy_method option:selected").val();
            switch (method_selected) {
                case 'buyxgetx':
                    if ($("#woo_cr_buyxgetx_option option:selected").val() != 'default') {
                        var buyxgetx_method_selected = $("#woo_cr_buyxgetx_option option:selected").val();
                        var buyxgetx_buy = new Array();
                        switch (buyxgetx_method_selected) {
                            case 'product':
                                $('select[name=woo_cr_buyxgetx_product]').each(function () {
                                    var arr = {};
                                    var id = $(this).val();
                                    var min_buy_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_min_buy_quant]').val();
                                    var max_buy_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_max_buy_quant]').val();
                                    var get_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_get_quant]').val();
                                    arr['id'] = id;
                                    arr['min_buyquant'] = min_buy_quant;
                                    arr['max_buyquant'] = max_buy_quant;
                                    arr['getquant'] = get_quant;
                                    arr['gettype'] = 'buyxgetx_product';
                                    arr['buytype'] = 'buyxgetx_product';
                                    arr['option']='product';
                                    arr['recursive'] = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_recursive]').is(':checked');
                                    buyxgetx_buy.push(arr);
                                });
                                break;
                            case 'allproducts':

                                $('select[name=woo_cr_buyxgetx_all_products]').each(function () {
                                    var arr = {};
                                    var id = $(this).val();
                                    var min_buy_quant = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_min_buy_quant]').val();
                                    var max_buy_quant = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_max_buy_quant]').val();
                                    var get_quant = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_get_quant]').val();
                                    arr['id'] = id;
                                    arr['min_buyquant'] = min_buy_quant;
                                    arr['max_buyquant'] = max_buy_quant;
                                    arr['getquant'] = get_quant;
                                    arr['gettype'] = 'buyxgetx_all_products';
                                    arr['buytype'] = 'buyxgetx_all_products';
                                    arr['option']='allproducts';
                                    arr['recursive'] = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_recursive]').is(':checked');
                                    buyxgetx_buy.push(arr);
                                });

                                break;
                            case 'category':
                                $('select[name=woo_cr_buyxgetx_category]').each(function () {
                                    var arr = {};
                                    var id = $(this).val();
                                    var min_buy_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_min_buy_quant]').val();
                                    var max_buy_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_max_buy_quant]').val();
                                    var get_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_get_quant]').val();
                                    arr['id'] = id;
                                    arr['min_buyquant'] = min_buy_quant;
                                    arr['max_buyquant'] = max_buy_quant;
                                    arr['getquant'] = get_quant;
                                    arr['gettype'] = 'buyxgetx_category';
                                    arr['buytype'] = 'buyxgetx_category';
                                    arr['option']='category';
                                    arr['recursive'] = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_recursive]').is(':checked');
                                    buyxgetx_buy.push(arr);
                                });
                                break;
                            case 'allcategories':
                                $('select[name=woo_cr_buyxgetx_all_categories]').each(function () {
                                    var arr = {};
                                    var id = $(this).val();
                                    var min_buy_quant = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_min_buy_quant]').val();
                                    var max_buy_quant = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_max_buy_quant]').val();
                                    var get_quant = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_get_quant]').val();
                                    arr['id'] = id;
                                    arr['min_buyquant'] = min_buy_quant;
                                    arr['max_buyquant'] = max_buy_quant;
                                    arr['getquant'] = get_quant;
                                    arr['gettype'] = 'buyxgetx_all_categories';
                                    arr['buytype'] = 'buyxgetx_all_categories';
                                    arr['option']='allcategories';
                                    arr['recursive'] = $('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_recursive]').is(':checked');
                                    buyxgetx_buy.push(arr);
                                });
                                break;
                        }
                    }

                    $('#woo_cr_buyxgetx_buy_data').val(JSON.stringify(buyxgetx_buy));
                    break;
                case 'buyxgety':
                    var buyxgety_buy = new Array();

                    $('select[name=woo_cr_buyxgety_product]').each(function () {
                        var arr = {};
                        var buyid = $(this).val();
                        var min_buy_quant = $(this).closest('.woo_cr_buyxgety_product_category_newrow').find('input[name=woo_cr_buyxgety_min_product_quant]').val();
                        var max_buy_quant = $(this).closest('.woo_cr_buyxgety_product_category_newrow').find('input[name=woo_cr_buyxgety_max_product_quant]').val();
                        arr['buytype'] = 'product';
                        arr['id'] = buyid;
                        arr['min_buyquant'] = min_buy_quant;
                        arr['max_buyquant'] = max_buy_quant;
                        buyxgety_buy.push(arr);
                    });

                    $('select[name=woo_cr_buyxgety_category]').each(function () {
                        var arr = {};
                        var buyid = $(this).val();
                        var min_buy_quant = $(this).closest('.woo_cr_buyxgety_product_category_newrow').find('input[name=woo_cr_buyxgety_min_category_quant]').val();
                        var max_buy_quant = $(this).closest('.woo_cr_buyxgety_product_category_newrow').find('input[name=woo_cr_buyxgety_max_category_quant]').val();
                        arr['buytype'] = 'category';
                        arr['id'] = buyid;
                        arr['min_buyquant'] = min_buy_quant;
                        arr['max_buyquant'] = max_buy_quant;
                        buyxgety_buy.push(arr);
                    });


                    $('#woo_cr_buyxgety_buy_data').val(JSON.stringify(buyxgety_buy));
                    break;
                case 'quantitybased':
                    var quantitybased_range = new Array();

                    var arr = {};
                    arr['min'] = $('input[name=woo_cr_quantitybased_min_value]').val();
                    arr['max'] = $('input[name=woo_cr_quantitybased_max_value]').val();
                    quantitybased_range.push(arr);

                    $('#woo_cr_quantitybased_buy_data').val(JSON.stringify(quantitybased_range));
                    break;

                case 'subtotalbased':
                    var subtotalbased_range = new Array();

                    var arr = {};
                    arr['min'] = $('input[name=woo_cr_subtotalbased_min_value]').val();
                    arr['max'] = $('input[name=woo_cr_subtotalbased_max_value]').val();
                    subtotalbased_range.push(arr);

                    $('#woo_cr_subtotalbased_buy_data').val(JSON.stringify(subtotalbased_range));
                    break;
            }
        }


        /*
        get scripts to save product-woo_cr_get_option
         */

        $product_get = new Array();
        $('select[name=woo_cr_get_product]').each(function () {
            var arr = {};
            var getids = $(this).val();
            var getquant = $(this).closest('.woo_cr_get_product_range_newrow').find('input[type=number]').val();
            arr['gettype'] = 'product';
            arr['id'] = getids;
            arr['getquant'] = getquant;
            $product_get.push(arr);
        });
        $('input[name=woo_cr_get_range_quant]').each(function () {
            var arr = {};
            var getquant = $(this).val();
            var getrange = $(this).closest('.woo_cr_get_product_range_newrow').find('select[name=woo_cr_get_range_option]').val();
            var getrangefrom = $(this).closest('.woo_cr_get_product_range_newrow').find('select[name=woo_cr_get_rangefrom_option]').val();
            arr['gettype'] = 'range';
            arr['getquant'] = getquant;
            arr['getrange'] = getrange;
            arr['getfrom'] = getrangefrom;
            if (getrangefrom == 'category') {
                $getcategoryid = $(this).closest('.woo_cr_get_product_range_newrow').find('select[name=woo_cr_get_rangefrom_category]').val();
                arr['getcategoryid'] = $getcategoryid;
            }
            $product_get.push(arr);
        });

        $('#woo_cr_get_product_data').val(JSON.stringify($product_get));


    });
    


    //remove product row button click
    $('#woo_cr_buyxgety_delete_product_category_btn').click(function (event) {
        event.preventDefault();
        var checkbox_remove = $('input[type=checkbox][name="woo_cr_add_product_category_row"]:checked');
        checkbox_remove.closest('div').remove();

    });


    /*
    Get scripts
     */


    /*
      Add new row product
     */
    $("#woo_cr_get_add_btn").click(function (event) {
        event.preventDefault();
        if ($("#woo_cr_get_option option:selected").val() != "default") {
            var option_checked = $("#woo_cr_get_option option:selected").val();
            if (option_checked == 'product') {
                $(".woo_cr_get_product_range_content").append("<div class='woo_cr_get_product_range_newrow'" +
                    " id='woo_cr_get_product_range_row'> <br><input type='checkbox' " +
                    "name='woo_cr_get_add_product_range_row'>Product<select class='wc-product-search' " +
                    " name='woo_cr_get_product' id='woo_cr_get_product' " +
                    "style='width: 50%;'  data-placeholder='Enter Get Products  ' " +
                    "data-action='woocommerce_json_search_products_and_variations'> </select>" +
                    " <input type='number' min='1' name='woo_cr_get_product_quant' " +
                    " id='woo_cr_get_product_quant' placeholder='Enter Get Quantity'  ></div>");
                $('.wc-product-search').trigger('wc-enhanced-select-init');
            } else if (option_checked == 'range') {
                $(".woo_cr_get_product_range_content").append("<div class='woo_cr_get_product_range_newrow'" +
                    " id='woo_cr_get_product_range_row'> <br><input type='checkbox' " +
                    "name='woo_cr_get_add_product_range_row'>Range<input type='number' name='woo_cr_get_range_quant'" +
                    "id='woo_cr_get_range_quant' placeholder='Enter Get Quantity'> <select name='woo_cr_get_range_option'" +
                    "id='woo_cr_get_range_option' class='woo_cr_get_rangefrom_option'><option value='default'>--Range--</option><option value='lowest'>Lowest Priced</option>" +
                    "<option value='highest'>Highest Priced</option></select><select name='woo_cr_get_rangefrom_option'" +
                    "id='woo_cr_get_rangefrom_option'><option value='default'>--From--</option><option value='cart'>" +
                    "Cart</option><option value='products'>All Products</option><option value='category'>Category</option></select>" +
                    "</div>");
            }
        }

    });




    /*
    Buy x get x scripts
     */

    /*
      Add new row
     */


    $("#woo_cr_buyxgetx_option").change(function (event) {

        if ($("#woo_cr_buyxgetx_option option:selected").val() != "default") {
            var option_checked = $("#woo_cr_buyxgetx_option option:selected").val();
            if (option_checked == 'product') {
                $('.woo_cr_buyxgetx_product_category_newrow').each(function () {
                    $(this).remove();
                });
                $(".woo_cr_buyxgetx_product_category_content").append("<div class='woo_cr_buyxgetx_product_category_newrow'" +
                    " id='woo_cr_buyxgetx_product_category_row'>  <label\n" +
                    "                       class='woo_cr_label'>Product</label>" +
                    "<select class='wc-product-search' " +
                    " name='woo_cr_buyxgetx_product' id='woo_cr_buyxgetx_product' " +
                    "style='width:50%;' multiple='multiple' data-placeholder='Enter Buy and Get Product  ' " +
                    "data-action='woocommerce_json_search_products_and_variations'> </select>" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_product_min_buy_quant' " +
                    " id='woo_cr_buyxgetx_product_min_buy_quant' placeholder='Min Buy Quantity'  >" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_product_max_buy_quant' " +
                    " id='woo_cr_buyxgetx_product_max_buy_quant' placeholder='Max Buy Quantity'  >" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_product_get_quant' " +
                    " id='woo_cr_buyxgetx_product_get_quant' placeholder='Get Quantity'  >" +
                    " <input type='checkbox' name='woo_cr_buyxgetx_product_recursive' " +
                    " id='woo_cr_buyxgetx_product_recursive' >Recursive</div>");

                $('.wc-product-search').trigger('wc-enhanced-select-init');


            }
            else if (option_checked == 'category') {

                $('.woo_cr_buyxgetx_product_category_newrow').each(function () {
                    $(this).hide();
                });

                var data = {
                    'action': 'add_buyxgetx_category_row'
                };
                $.post(ajaxurl, data, function (response) {
                    var $newCategoryRow = $("<div/>")   // creates a div element
                        .attr("id", "woo_cr_buyxgetx_product_category_row")  // adds the id
                        .addClass("woo_cr_buyxgetx_product_category_newrow")   // add a class
                        .html(response);
                    $(".woo_cr_buyxgetx_product_category_content").append($newCategoryRow);
                    $('.wc-product-search').trigger('wc-enhanced-select-init');
                });
            }
            else if(option_checked=='allproducts'){
                $('.woo_cr_buyxgetx_product_category_newrow').each(function () {
                    $(this).remove();
                });

                $(".woo_cr_buyxgetx_product_category_content").append("<div class='woo_cr_buyxgetx_product_category_newrow'" +
                    " id='woo_cr_buyxgetx_product_category_row'>  " +

                    "<input type='number' min='1' name='woo_cr_buyxgetx_all_products_min_buy_quant' " +
                    " id='woo_cr_buyxgetx_all_products_min_buy_quant' placeholder='Min Buy Quantity'  >" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_all_products_max_buy_quant' " +
                    " id='woo_cr_buyxgetx_all_products_max_buy_quant' placeholder='Max Buy Quantity'  >" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_all_products_get_quant' " +
                    " id='woo_cr_buyxgetx_all_products_get_quant' placeholder='Get Quantity'  >" +
                    " <input type='checkbox' name='woo_cr_buyxgetx_all_products_recursive' " +
                    " id='woo_cr_buyxgetx_all_products_recursive' >Recursive</div>");

                $('.wc-product-search').trigger('wc-enhanced-select-init');
            }else if(option_checked=='allcategories'){
                $('.woo_cr_buyxgetx_product_category_newrow').each(function () {
                    $(this).remove();
                });

                $(".woo_cr_buyxgetx_product_category_content").append("<div class='woo_cr_buyxgetx_product_category_newrow'" +
                    " id='woo_cr_buyxgetx_product_category_row'>  " +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_all_categories_min_buy_quant' " +
                    " id='woo_cr_buyxgetx_all_categories_min_buy_quant' placeholder='Min Buy Quantity'  >" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_all_categories_max_buy_quant' " +
                    " id='woo_cr_buyxgetx_all_categories_max_buy_quant' placeholder='Max Buy Quantity'  >" +
                    "<input type='number' min='1' name='woo_cr_buyxgetx_all_categories_get_quant' " +
                    " id='woo_cr_buyxgetx_all_categories_get_quant' placeholder='Get Quantity'  >" +
                    " <input type='checkbox' name='woo_cr_buyxgetx_all_categories_recursive' " +
                    " id='woo_cr_buyxgetx_all_categories_recursive' >Recursive</div>");

            }
        }

    });

    /*
    Remove row
     */

    $('#woo_cr_buyxgetx_delete_product_category_btn').click(function (event) {
        event.preventDefault();
        var checkbox_remove = $('input[type=checkbox][name="woo_cr_add_buyxgetx_product_category_row"]:checked');
        checkbox_remove.closest('div').remove();

    });


    /*disable quant when recursive is selected*/

    //product

    $('.woo_cr_buyxgetx_content').on('change', 'input[name=woo_cr_buyxgetx_product_recursive]', function () {

        var buy_min_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_min_buy_quant]');
        var buy_max_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_max_buy_quant]');
        var get_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_product_get_quant]');

        if ($('input[name=woo_cr_buyxgetx_product_recursive]').is(":checked")) {

            buy_min_quant.attr('disabled', 'disabled');
            buy_max_quant.attr('disabled', 'disabled');
            get_quant.attr('disabled', 'disabled');

        } else {

            buy_min_quant.removeAttr('disabled');
            buy_max_quant.removeAttr('disabled');
            get_quant.removeAttr('disabled');

        }

    });
    var buy_min_quant = $('input[name=woo_cr_buyxgetx_product_min_buy_quant]');
    var buy_max_quant = $('input[name=woo_cr_buyxgetx_product_max_buy_quant]');
    var get_quant = $('input[name=woo_cr_buyxgetx_product_get_quant]');

    if ($('input[name=woo_cr_buyxgetx_product_recursive]').is(":checked")) {

        buy_min_quant.attr('disabled', 'disabled');
        buy_max_quant.attr('disabled', 'disabled');
        get_quant.attr('disabled', 'disabled');

    } else {

        buy_min_quant.removeAttr('disabled');
        buy_max_quant.removeAttr('disabled');
        get_quant.removeAttr('disabled');

    }

    //all products

    $('.woo_cr_buyxgetx_content').on('change', 'input[name=woo_cr_buyxgetx_all_products_recursive]', function () {

        var all_products_buy_min_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_min_buy_quant]');
        var all_products_buy_max_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_max_buy_quant]');
        var all_products_get_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_products_get_quant]');

        if ($('input[name=woo_cr_buyxgetx_all_products_recursive]').is(":checked")) {

            all_products_buy_min_quant.attr('disabled', 'disabled');
            all_products_buy_max_quant.attr('disabled', 'disabled');
            all_products_get_quant.attr('disabled', 'disabled');

        } else {

            all_products_buy_min_quant.removeAttr('disabled');
            all_products_buy_max_quant.removeAttr('disabled');
            all_products_get_quant.removeAttr('disabled');

        }

    });
    var all_products_buy_min_quant = $('input[name=woo_cr_buyxgetx_all_products_min_buy_quant]');
    var all_products_buy_max_quant = $('input[name=woo_cr_buyxgetx_all_products_max_buy_quant]');
    var all_products_get_quant = $('input[name=woo_cr_buyxgetx_all_products_get_quant]');

    if ($('input[name=woo_cr_buyxgetx_all_products_recursive]').is(":checked")) {

        all_products_buy_min_quant.attr('disabled', 'disabled');
        all_products_buy_max_quant.attr('disabled', 'disabled');
        all_products_get_quant.attr('disabled', 'disabled');

    } else {

        all_products_buy_min_quant.removeAttr('disabled');
        all_products_buy_max_quant.removeAttr('disabled');
        all_products_get_quant.removeAttr('disabled');

    }

    //category

    $('.woo_cr_buyxgetx_content').on('change', 'input[name=woo_cr_buyxgetx_category_recursive]', function () {

        var category_buy_min_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_min_buy_quant]');
        var category_buy_max_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_max_buy_quant]');
        var category_get_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_category_get_quant]');

        if ($('input[name=woo_cr_buyxgetx_category_recursive]').is(":checked")) {

            category_buy_min_quant.attr('disabled', 'disabled');
            category_buy_max_quant.attr('disabled', 'disabled');
            category_get_quant.attr('disabled', 'disabled');

        } else {

            category_buy_min_quant.removeAttr('disabled');
            category_buy_max_quant.removeAttr('disabled');
            category_get_quant.removeAttr('disabled');

        }

    });
    var category_buy_min_quant = $('input[name=woo_cr_buyxgetx_category_min_buy_quant]');
    var category_buy_max_quant = $('input[name=woo_cr_buyxgetx_category_max_buy_quant]');
    var category_get_quant = $('input[name=woo_cr_buyxgetx_category_get_quant]');

    if ($('input[name=woo_cr_buyxgetx_category_recursive]').is(":checked")) {

        category_buy_min_quant.attr('disabled', 'disabled');
        category_buy_max_quant.attr('disabled', 'disabled');
        category_get_quant.attr('disabled', 'disabled');

    } else {

        category_buy_min_quant.removeAttr('disabled');
        category_buy_max_quant.removeAttr('disabled');
        category_get_quant.removeAttr('disabled');

    }

    //all categories

    $('.woo_cr_buyxgetx_content').on('change', 'input[name=woo_cr_buyxgetx_all_categories_recursive]', function () {

        var all_categories_buy_min_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_min_buy_quant]');
        var all_categories_buy_max_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_max_buy_quant]');
        var all_categories_get_quant = $(this).closest('.woo_cr_buyxgetx_product_category_newrow').find('input[name=woo_cr_buyxgetx_all_categories_get_quant]');

        if ($('input[name=woo_cr_buyxgetx_all_categories_recursive]').is(":checked")) {

            all_categories_buy_min_quant.attr('disabled', 'disabled');
            all_categories_buy_max_quant.attr('disabled', 'disabled');
            all_categories_get_quant.attr('disabled', 'disabled');

        } else {

            all_categories_buy_min_quant.removeAttr('disabled');
            all_categories_buy_max_quant.removeAttr('disabled');
            all_categories_get_quant.removeAttr('disabled');

        }

    });
    var all_categories_buy_min_quant = $('input[name=woo_cr_buyxgetx_all_categories_min_buy_quant]');
    var all_categories_buy_max_quant = $('input[name=woo_cr_buyxgetx_all_categories_max_buy_quant]');
    var all_categories_get_quant = $('input[name=woo_cr_buyxgetx_all_categories_get_quant]');

    if ($('input[name=woo_cr_buyxgetx_all_categories_recursive]').is(":checked")) {

        all_categories_buy_min_quant.attr('disabled', 'disabled');
        all_categories_buy_max_quant.attr('disabled', 'disabled');
        all_categories_get_quant.attr('disabled', 'disabled');

    } else {

        all_categories_buy_min_quant.removeAttr('disabled');
        all_categories_buy_max_quant.removeAttr('disabled');
        all_categories_get_quant.removeAttr('disabled');

    }

    //show exclude products metabox only when all products of buyxgetx is selected

    /*
   based on choice of method display metabox
    */
    $('#woo_cr_buyxgetx_option').change(function () {

        var method_choice = $(this).val();
        if(method_choice=='allproducts'){
            $('#woo_cr_exclude_products_metabox').show();
        }else{
            $('#woo_cr_exclude_products_metabox').hide();
        }
    });

    if ($("#woo_cr_buyxgetx_option option:selected").val() != 'default') {

        var buyxgetx_method_selected = $("#woo_cr_buyxgetx_option option:selected").val();
        if(buyxgetx_method_selected=='allproducts') {
            $('#woo_cr_exclude_products_metabox').show();
        }else{
            $('#woo_cr_exclude_products_metabox').hide();
        }
    } else {
        $('#woo_cr_exclude_products_metabox').hide();

    }

    //show exclude categories metabox only when all categories of buyxgetx is selected

    /*
   based on choice of method display metabox
    */
    $('#woo_cr_buyxgetx_option').change(function () {

        var method_choice = $(this).val();
        if(method_choice=='allcategories'){
            $('#woo_cr_exclude_categories_metabox').show();
        }else{
            $('#woo_cr_exclude_categories_metabox').hide();
        }
    });

    if ($("#woo_cr_buyxgetx_option option:selected").val() != 'default') {

        var buyxgetx_method_selected = $("#woo_cr_buyxgetx_option option:selected").val();
        if(buyxgetx_method_selected=='allcategories') {
            $('#woo_cr_exclude_categories_metabox').show();
        }else{
            $('#woo_cr_exclude_categories_metabox').hide();
        }
    } else {
        $('#woo_cr_exclude_categories_metabox').hide();

    }



});