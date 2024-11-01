<?php
/**
 * @package CRBOGODeals
 */

namespace CRIncludes\Base;

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


class BaseController{

    public $plugin_path;

    public $plugin_url;

    public $plugin_name;

    public function __construct()
    {

        $this->plugin_path=plugin_dir_path($this->dirname_r(__FILE__, 3));

        $this->plugin_url= plugin_dir_url($this->dirname_r(__FILE__,2));

        $this->plugin_name=plugin_basename($this->dirname_r(__FILE__,3)).'/cr-bogo-deals.php';

    }

    function dirname_r($path, $count=1){
        if ($count > 1){
            return dirname($this->dirname_r($path, --$count));
        }else{
            return dirname($path);
        }
    }

}