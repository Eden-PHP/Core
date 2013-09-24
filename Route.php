<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core;

/**
 * Definition for overriding classes.
 * This class also provides methods to list out various routes
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Route
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

		//get the true class
        $route = $this->get($class);
		
		//if route is already an abject
        if(is_object($route)) {
			//return it
            return $route;
        }
		
		//if the static method i exists
        if(method_exists($route, 'i')) {
			//instantiate it
			return forward_static_call_array(array($route, 'i'), $args);
        }
		
		//instantiate it
        $reflect = new \ReflectionClass($route);
        return $reflect->newInstanceArgs($args);
    }

    /**
     * Returns the class that will be routed to given the route.
     *
     * @param string|null the class route name
     * @return string|object|array
     */
    public function get($route = null)
    {
        //argument 1 must be a string or null
        Argument::i()->test(1, 'string', 'null');
		
		//if route is null
		if(is_null($route)) {
			//return all routes
			return $this->route;
		}
		
		//if valid route
        if($this->valid($route)) {
			//return route
            return $this->route[strtolower($route)];
        }
		
		//at this point it is not a route
		//return the same thing
        return $route;
    }

    /**
     * Checks to see if a name is a route
     *
     * @param *string
     * @return bool
     */
    public function valid($route)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        return isset($this->route[strtolower($route)]);
    }

    /**
     * Unsets the route
     *
     * @param *string the class route name
     * @return Eden\Core\Route
     */
    public function release($route)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        if($this->valid($route)) {
            unset($this->route[strtolower($route)]);
        }

        return $this;
    }

    /**
     * Routes a class
     *
     * @param *string the class route name
     * @param *string the name of the class to route to
     * @return Eden\Core\Route
     */
    public function set($source, $destination)
    {
		//argument test
        Argument::i()
			//argument 1 must be a string or object
            ->test(1, 'string', 'object')    
			//argument 2 must be a string or object
            ->test(2, 'string', 'object');    
		
		//if source is an object
        if(is_object($source)) {
			//transform it into string class
            $source = get_class($source);
        }

		//if it is a string
        if(is_string($destination)) {
			//we need to consider if this is a vitual class
            $destination = $this->get($destination);
        }
		
		//now let's route it
        $this->route[strtolower($source)] = $destination;
        
		return $this;
    }
}
