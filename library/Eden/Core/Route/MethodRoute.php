<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core\Route;

use Eden\Core\Base;
use Eden\Core\Argument;

/**
 * Definition for overloading methods.
 * This class also provides methods to list out various routes
 * and has the ability to call methods, static methods and
 * functions passing arguments as an array.
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class MethodRoute
{
    const ERROR_CLASS_NOT_EXISTS = 'Invalid class call: %s->%s(). Class does not exist.';
    const ERROR_METHOD_NOT_EXISTS = 'Invalid class call: %s->%s(). Method does not exist.';

    protected static $instance = null;
    protected $route = array(); //method registry

    public static function i()
    {
        $class = __CLASS__;
        if(is_null(self::$instance)) {
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Calls a method considering all routes
     *
     * @param *string|object the class name
     * @param *string the method name
     * @param array the arguments you want to pass into the method
     * @return mixed
     */
    public function call($class, $method, array $args = array())
    {
        //argument test
        Argument::i()
            ->test(1, 'string', 'object') //argument 1 must be string or object
            ->test(2, 'string');          //argument 2 must be string

        $instance = null;
        if(is_object($class)) {
            $instance = $class;
            $class = get_class($class);
        }

        $classRoute     = ClassRoute::i();
        $isClassRoute     = $classRoute->isRoute($class);
        $isMethodRoute    = $this->isRoute($class, $method);

        //method might be a route
        //lets make sure we are dealing with the right method
        //this also checks class as well
        list($class, $method) = $this->getRoute($class, $method);

        //class does not exist
        if(!is_object($class) && !class_exists($class)) {
            //throw exception
            Exception::i()
                ->setMessage(self::ERROR_CLASS_NOT_EXISTS)
                ->addVariable($class)
                ->addVariable($method)
                ->trigger();
        }

        //method does not exist
        if(!$isClassRoute && !$isMethodRoute && !method_exists($class, $method)) {
            //throw exception
            Exception::i()
                ->setMessage(self::ERROR_METHOD_NOT_EXISTS)
                ->addVariable($class)
                ->addVariable($method)
                ->trigger();
        }

        //if there is a route or no instance
        if($isClassRoute || !$instance || get_class($instance) != $class) {
            $instance = $classRoute->call((string) $class);
        }

        return call_user_func_array(array($instance, $method), $args);
    }

    /**
     * Calls a static method considering all routes
     *
     * @param *string|object the class name
     * @param *string the method name
     * @param array the arguments you want to pass into the method
     * @return mixed
     */
    public function callStatic($class, $method, array $args = array())
    {
        //argument test
        Argument::i()
            ->test(1, 'string', 'object') //argument 1 must be string or object
            ->test(2, 'string');          //argument 2 must be string

        if(is_object($class)) {
            $class = get_class($class);
        }

        $isClassRoute     = ClassRoute::i()->isRoute($class);
        $isMethodRoute    = $this->isRoute($class, $method);

        //method might be a route
        //lets make sure we are dealing with the right method
        //this also checks class as well
        list($class, $method) = $this->getRoute($class, $method);

        //class does not exist
        if(!is_object($class) && !class_exists($class)) {
            //throw exception
            Exception::i()
                ->setMessage(self::ERROR_CLASS_NOT_EXISTS)
                ->addVariable($class)
                ->addVariable($method)
                ->trigger();
        }

        //method does not exist
        if(!$isClassRoute && !$isMethodRoute && !method_exists($class, $method)) {
            //throw exception
            Exception::i()
                ->setMessage(self::ERROR_METHOD_NOT_EXISTS)
                ->addVariable($class)
                ->addVariable($method)
                ->trigger();
        }

        if(is_object($class)) {
            $class = get_class($class);
        }

        return call_user_func_array($class.'::'.$method, $args); // As of 5.2.3
    }

    /**
     * Returns the class and method that will be routed to given the route.
     *
     * @param *string the class route name
     * @param *string the class route method
     * @return array|variable
     */
    public function getRoute($class, $method)
    {
        Argument::i()
            ->test(1, 'string')  //argument 1 must be a string
            ->test(2, 'string'); //argument 2 must be a string

        if($this->isRoute(null, $method)) {
            return $this->route[null][strtolower($method)];
        }

        $class = ClassRoute::i()->getRoute($class);

        if($this->isRoute($class, $method)) {
            return $this->route[strtolower($class)][strtolower($method)];
        }

        return array($class, $method);
    }

    /**
     * Returns all method routes
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
     * @param *string|null
     * @param *string
     * @return bool
     */
    public function isRoute($class, $method)
    {
        Argument::i()
            ->test(1, 'string', 'null')  //argument 1 must be a string
            ->test(2, 'string');         //argument 2 must be a string

        if(is_string($class)) {
            $class = strtolower($class);
        }

        return isset($this->route[$class][strtolower($method)]);
    }

    /**
     * Unsets the route
     *
     * @param *string the class route name
     * @param *string the method route name
     * @return Eden\Core\Route\MethodRoute
     */
    public function release($class, $method)
    {
        Argument::i()
            ->test(1, 'string')  //argument 1 must be a string
            ->test(2, 'string'); //argument 2 must be a string

        if($this->isRoute($class, $method)) {
            unset($this->route[strtolower($class)][strtolower($method)]);
        }

        return $this;
    }

    /**
     * Routes a method.
     *
     * @param *string the class route name
     * @param *string the method route name
     * @param *string the name of the class to route to
     * @param string|null the name of the method to route to
     * @return Eden\Core\Route\MethodRoute
     */
    public function route($source, $alias, $class, $method = null)
    {
        //argument test
        Argument::i()
            ->test(1, 'string', 'object', 'null')    //argument 1 must be a string, object or null
            ->test(2, 'string')                        //argument 2 must be a string
            ->test(3, 'string', 'object')            //argument 3 must be a string or object
            ->test(4, 'string');                    //argument 4 must be a string

        if(is_object($source)) {
            $source = get_class($source);
        }

        //if the method is not a string
        if(!is_string($method)) {
            $method = $alias;
        }

        $route = ClassRoute::i();
        if(!is_null($source)) {
            $source = $route->getRoute($source);
            $source = strtolower($source);
        }

        if(is_string($class)) {
            $class = $route->getRoute($class);
        }

        $this->route[$source][strtolower($alias)] = array($class, $method);

        return $this;
    }
}