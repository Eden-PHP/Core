<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core;

use Eden\Core\Route\ClassRoute;
use Eden\Core\Route\MethodRoute;
use Eden\Core\Route\FunctionRoute;

/**
 * Definition for overloading methods and overriding classes.
 * This class also provides methods to list out various routes
 * and has the ability to call methods, static methods and
 * functions passing arguments as an array.
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Route extends Base
{
    protected static $instance = null;

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
     * Either instantiates a class given the arguments
     * or reurns the class router
     *
     * @param string|null
     * @param array
     * @return object
     */
    public function getClass($class = null, array $args = array())
    {
        //argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');

        $route = ClassRoute::i();

        if(is_null($class)) {
            return $route;
        }

        return $route->callArray($class, $args);
    }

    /**
     * Either calls a function given the arguments
     * or reurns the function router
     *
     * @param string|null
     * @param array
     * @return object
     */
    public function getFunction($function = null, array $args = array())
    {
        //argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');

        $route = FunctionRoute::i();

        if(is_null($function)) {
            return $route;
        }

        return $route->callArray($function, $args);
    }

    /**
     * Either calls a method given the arguments
     * or reurns the method router
     *
     * @param string|object|null
     * @param string|null
     * @param array
     * @return object
     */
    public function getMethod($class = null, $method = null, array $args = array())
    {
        Argument::i()
            ->test(1, 'string', 'object', 'null')  //argument 1 must be a string, object or null
            ->test(2, 'string', 'null');           //argument 2 must be a string or null


        $route = MethodRoute::i();

        if(is_null($class) || is_null($method)) {
            return $route;
        }

        return $route->call($class, $method, $args);
    }
}