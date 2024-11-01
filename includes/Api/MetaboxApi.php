<?php


/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Api;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class MetaboxApi
{

    public $metaboxes = array();


    public function register()
    {

        if (!empty($this->metaboxes)) {
            add_action('add_meta_boxes', array($this, 'addMetabox'));

        }


    }



    public function setMetabox(array $args){

        $this->metaboxes=$args;

        return $this;

    }


    public function addMetabox()
    {

        foreach ($this->metaboxes as $metabox) {

                add_meta_box(
                    $metabox['id'],
                    $metabox['title'],
                    $metabox['callback'],
                    $metabox['page'],
                    $metabox['context'],
                    $metabox['priority'],
                    $metabox['args']
                );





        }
    }



}