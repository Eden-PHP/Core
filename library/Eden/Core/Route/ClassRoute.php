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
class ClassRoute
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
     * Calls a class considering all routes.
     *
     * @param *string class
     * @param [variable..] arguments
     * @return object
     */
    public function call($class)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        $args = func_get_args();
        $class = array_shift($args);

        return $this->callArray($class, $args);
    }

    /**
     * Calls a class considering all routes.
     *
     * @param *string class
     * @param array arguments
     * @return object
     */
    public function callArray($class, array $args = array())
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        $route = $this->getRoute($class);

        if(is_object($route)) {
            return $route;
        }

        $reflect = new \ReflectionClass($route);
        if(method_exists($route, 'i')) {
            return MethodRoute::i()->callStatic($route, 'i', $args);
        }

        return $reflect->newInstanceArgs($args);
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
     * @return Eden\Core\Route\ClassRoute
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
     * @return Eden\Core\Route\ClassRoute
     */
    public function route($route, $class)
    {
        Argument::i()
            ->test(1, 'string', 'object')    //argument 1 must be a string or object
            ->test(2, 'string', 'object');    //argument 2 must be a string or object

        if(is_object($route)) {
            $route = get_class($route);
        }

        if(is_string($class)) {
            $class = $this->getRoute($class);
        }

        $this->route[strtolower($route)] = $class;
        return $this;
    }
}