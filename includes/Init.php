<?php

/**
 * @package CRBOGODeals
 */

namespace CRIncludes;


/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


/**
 * Class Init
 * @package Includes
 */
final class Init{

    /**
     * Store all the classes inside an array
     *
     * @return array List of classes
     */
    public static function get_services(){
        return array(
            Base\Enqueue::class,
            Pages\Admin::class,
            Base\SettingsLink::class,
            Base\Ajax::class,
            Rules\Hooks::class,

        );
    }


    /**
     *Loop through all the classes, initialize and call
     * them if it exists
     */
    public static function register_services()
    {

        foreach (self::get_services() as $class){
            $service=self::instantiate($class);
            if(method_exists($service,'register')){
                $service->register();
            }
        }

    }

    /**
     * Initialize the class
     * @param $class class from the services array
     * @return new instance of the class
     */
    private static function instantiate($class){

        $service=new $class();
        return $service;
    }
}
