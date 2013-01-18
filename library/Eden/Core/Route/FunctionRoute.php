<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core\Route;

use Eden\Core\Argument;
use Eden\Core\Base;

/**
 * Definition for overriding classes.
 * This class also provides methods to list out various routes
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class FunctionRoute
{
    protected static $instance = null;
    protected $route = array();    //class registry

    /**
     * One of the hard thing about instantiating classes is
     * that design patterns can impose different ways of
     * instantiating a class. The word "new" is not flexible.
     * Authors of classes should be able to control how a class
     * is instantiated, while leaving others using the class
     * oblivious to it. All in all its one less thing to remember
     * for each class call. By default we instantiate classes with
     * this method.
     *
     * @param [mixed[,mixed..]]
     * @return object
     */
    public static function i()
    {
        $class = __CLASS__;
        if(is_null(self::$instance)) {
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Calls a function considering all routes.
     *
     * @param *string class
     * @param mixed[,mixed..] arguments
     * @return object
     */
    public function call($function)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        $args = func_get_args();
        $function = array_shift($args);

        return $this->callArray($function, $args);
    }

    /**
     * Calls a function considering all routes.
     *
     * @param *string class
     * @param array arguments
     * @return object
     */
    public function callArray($function, array $args = array())
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        $function = $this->getRoute($function);

        //try to run the function using PHP call_user_func_array
        return call_user_func_array($function, $args);
    }

    /**
     * Returns the class that will be routed to given the route.
     *
     * @param *string the class route name
     * @return string|variable
     */
    public function getRoute($route)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        if($this->isRoute($route)) {
            return $this->route[strtolower($route)];
        }

        return $route;
    }

    /**
     * Returns all class routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->route;
    }

    /**
     * Checks to see if a name is a route
     *
     * @param *string
     * @return bool
     */
    public function isRoute($route)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        return isset($this->route[strtolower($route)]);
    }

    /**
     * Unsets the route
     *
     * @param *string the class route name
     * @return Eden\Core\Route\FunctionRoute
     */
    public function release($route)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        if($this->isRoute($route)) {
            unset($this->route[strtolower($route)]);
        }

        return $this;
    }

    /**
     * Routes a class
     *
     * @param *string the class route name
     * @param *string the name of the class to route to
     * @return Eden\Core\Route\FunctionRoute
     */
    public function route($route, $function)
    {
        Argument::i()
            ->test(1, 'string')        //argument 1 must be a string
            ->test(2, 'string');    //argument 2 must be a string

        $function = $this->getRoute($function);

        $this->route[strtolower($route)] = $function;
        return $this;
    }
}