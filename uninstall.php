<?php

/**
 * Trigger this on plugin uninstall
 *
 * @package CRBOGODeals
 */

if(!defined('WP_UNINSTALL_PLUGIN')){ die; }

//clear plugin database data

$cr_rules=get_posts(array('post_type'=>'cr_rule','numberposts'=>-1));

foreach ($cr_rules as $cr_rule){

    $meta_rules=get_post_meta($cr_rule->ID);

    foreach ($meta_rules as $key=>$value){
        delete_post_meta($rule_id,$key);
    }

    wp_delete_post($cr_rule->ID,true);

}