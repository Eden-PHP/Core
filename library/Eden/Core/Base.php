<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core;

use Eden\Core\Route\Exception as RouteException;

/**
 * The base class for all classes wishing to integrate with Eden.
 * Extending this class will allow your methods to seemlessly be
 * overloaded and overrided as well as provide some basic class
 * loading patterns.
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Base
{
    const ERROR_REFLECTION = 'Error creating Reflection Class: %s, Method: %s.';

    const INSTANCE = 0;

    private static $instances = array();

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
        if(static::INSTANCE === 1) {
            return self::getSingleton();
        }

        return self::getMultiple();
    }

    /**
     * When a method doesn't exist, it could be because it's a
     * method or class route. This method allows methods to be
     * reverse overrided, without directly changing the original
     * authors code.
     *
     * @param *string
     * @param *array
     * @return mixed
     */
    public function __call($name, $args)
    {
        Argument::i()
            ->test(1, 'string') //argument 1 must be a string
            ->test(2, 'array'); //argument 2 must be an array

        //if the method name starts with a capital letter
        //most likely they want a class
        if(preg_match("/^[A-Z]/", $name)) {
            //lets first consider that they may just
            //want to load a class so lets try
            try {
                //return the class
                return Route::i()->getClass($name, $args);
            //only if there's a route exception do we want to catch it
            //this is because a class can throw an exception in their construct
            //so if that happens then we do know that the class has actually
            //been called and an exception is suppose to happen
            } catch(RouteException $e) {
                //Bad class name? try namespacing
                $class = '\\'.str_replace('_', '\\', $name);
                //same explanation as the previous try
                try {
                    //return the class
                    return Route::i()->getClass($class, $args);
                //same explanation as the previous catch
                } catch(RouteException $e) {
                } catch(\ReflectionException $e) {}
            } catch(\ReflectionException $e) {
                //Bad class name? try namespacing
                $class = '\\'.str_replace('_', '\\', $name);
                //same explanation as the previous try
                try {
                    //return the class
                    return Route::i()->getClass($class, $args);
                //same explanation as the previous catch
                } catch(RouteException $e) {
                } catch(\ReflectionException $e) {}
            }
        }

        //try to
        try {
            //let the router handle this
            return Route::i()->getMethod()->call($this, $name, $args);
        } catch(RouteException $e) {
            //throw the error at this point
            //to get rid of false positives
            Exception::i($e->getMessage())->trigger();
        }
    }

    /**
     * We use __invoke to further make classes extended by Eden
     * access other classes easily.
     *
     * @param string[,mixed..]
     * @return object
     */
    public function __invoke()
    {
        //if arguments are 0
        if(func_num_args() == 0) {
            //return this
            return $this;
        }

        //get the arguments
        $args = func_get_args();

        //if the first argument is an array
        if(is_array($args[0])) {
            //make the args that
            $args = $args[0];
        }

        //Fix class name
        $class = 'Eden\\'.ucwords(array_shift($args)).'\\Factory';

        //try to
        try {
            //instantiate it
            return Route::i()->getClass($class, $args);
        } catch(RouteException $e) {
            //throw the error at this point
            //to get rid of false positives
            Exception::i($e->getMessage())->trigger();
        }
    }

    /**
     * By default echoing a class outputs the class name
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * Calls a method in this class and allows
     * argumetns to be passed as an array
     *
     * @param *string method name
     * @param array arguments
     * @return mixed
     */
    public function call($method, array $args = array())
    {
        //argument 1 must be a string
        Argument::i()->test(1,'string');

        return Route::i()->getMethod()->call($this, $method, $args);
    }

    /**
     * Loops through returned result sets
     *
     * @param *callable
     * @return Eden\Core\Base
     */
    public function each($callback)
    {
        //argument 1 must be callable
        Argument::i()->test(1, 'callable');
        return Loop::i()->iterate($this, $callback);
    }

    /**
     * Force outputs any class property
     *
     * @param mixed
     * @param string|null
     * @return Eden\Core\Base
     */
    public function inspect($variable = null, $next = null)
    {
        //argument 2 must be a string or null
        Argument::i()->test(2, 'string', 'null');

        //we are using tool in all cases
        $class = get_class($this);

        //if variable is null
        if(is_null($variable)) {
            //output the class
            Inspect::i()
                ->output(sprintf(Inspect::INSPECT, $class))
                ->output($this);

            return $this;
        }

        //if variable is true
        if($variable === true) {
            //return whatever the next response is
            //or return the next specified variable
            return Inspect::i()->next($this, $next);
        }

        //if variable is not a string
        if(!is_string($variable)) {
            //output variable
            Inspect::i()
                ->output(sprintf(Inspect::INSPECT, 'Variable'))
                ->output($variable);

            return $this;
        }

        //if variable is set
        if(isset($this->$variable)) {
            //output it
            Inspect::i()
                ->output(sprintf(Inspect::INSPECT, $class.'->'.$variable))
                ->output($this->$variable);

            return $this;
        }

        //could be private
        $private = '_'.$variable;
        //if private variable is set
        if(isset($this->$private)) {
            //output it
            Inspect::i()
                ->output(sprintf(Inspect::INSPECT, $class.'->'.$private))
                ->output($this->$private);

            return $this;
        }

        //any other case output variable
        Inspect::i()
            ->output(sprintf(Inspect::INSPECT, 'Variable'))
            ->output($variable);

        return $this;
    }

    /**
     * Attaches an instance to be notified
     * when an event has been triggered
     *
     * @param *string
     * @param *callable
     * @param bool
     * @return Eden\Core\Base
     */
    public function listen($event, $callable, $important = false)
    {
        Argument::i()
            ->test(1, 'string')              //argument 1 must be string
            ->test(2, 'callable', 'null')    //argument 2 must be callable or null
            ->test(3, 'bool');               //argument 3 must be boolean

        Event::i()->listen($event, $callable, $important);

        return $this;
    }

    /**
     * Creates a class route for this class.
     *
     * @param *string the class route name
     * @return Eden\Core\Base
     */
    public function route($route)
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');

        if(func_num_args() == 1) {
            //when someone calls a class call this instead
            Route::i()->getClass()->route($route, $this);
            return $this;
        }

        //argument 2 must be a string
        Argument::i()->test(2, 'string', 'object');

        $args = func_get_args();

        $source = array_shift($args);
        $class     = array_shift($args);
        $destination = null;

        if(count($args)) {
            $destination = array_shift($args);
        }

        //when someone calls a method here call something ele instead
        Route::i()->getMethod()->route($this, $source, $class, $destination);

        return $this;
    }

    /**
     * Notify all observers of that a specific
     * event has happened
     *
     * @param [string|null[,mixed..]]
     * @return Eden\Core\Base
     */
    public function trigger($event = null)
    {
        //argument 1 must be string
        Argument::i()->test(1, 'string', 'null');

        $args = func_get_args();
        Route::i()->getMethod(Event::i(), 'trigger', $args);

        return $this;
    }

    /**
     * Stops listening to an event
     *
     * @param string|null
     * @param callable|null
     * @return Eden\Core\Base
     */
    public function unlisten($event = null, $callable = null)
    {
        Argument::i()
            ->test(1, 'string', 'null')     //argument 1 must be a string or null
            ->test(2, 'callable', 'null');  //argument 2 must be a callable or null

        Event::i()->unlisten($event, $callable);

        return $this;
    }

    /**
     * Invokes When if conditional is false
     *
     * @param bool
     * @return Eden\Core\Base|Eden\Core\When
     */
    public function when($isTrue, $lines = 0)
    {
        Argument::i()
            ->test(1, 'bool')  //argument 1 must be a boolean
            ->test(2, 'int');  //argument 2 must be an integer

        if($isTrue || $lines == 0) {
            return $this;
        }

        return When::i($this, $lines);
    }

    /**
     * Returns a non-singleton class, while considering routes
     *
     * @param string|null
     * @return object
     */
    protected static function getMultiple($class = null)
    {
        if(is_null($class) && function_exists('get_called_class')) {
            $class = get_called_class();
        }

        $class = Route::i()->getClass()->getRoute($class);
        return self::getInstance($class);
    }

    /**
     * Returns the same instance if instantiated already
     * while considering routes.
     *
     * @param string|null
     * @return object
     */
    protected static function getSingleton($class = null)
    {
        if(is_null($class) && function_exists('get_called_class')) {
            $class = get_called_class();
        }

        $class = Route::i()->getClass()->getRoute($class);

        if(!isset(self::$instances[$class])) {
            self::$instances[$class] = self::getInstance($class);
        }

        return self::$instances[$class];
    }

    /**
     * Returns an instance considering routes. With
     * this method we can instantiate while passing
     * construct arguments as arrays
     *
     * @param string
     * @return object
     */
    private static function getInstance($class)
    {
        $trace = debug_backtrace();
        $args = array();

        if(isset($trace[1]['args']) && count($trace[1]['args']) > 1) {
            $args = $trace[1]['args'];
            //shift out the class name
            array_shift($args);
        } else if(isset($trace[2]['args']) && count($trace[2]['args']) > 0) {
            $args = $trace[2]['args'];
        }

        if(count($args) === 0 || !method_exists($class, '__construct')) {
            return new $class;
        }

        $reflect = new \ReflectionClass($class);

        try {
            return $reflect->newInstanceArgs($args);
        } catch(\Reflection_Exception $e) {
            Argument::i()
                ->setMessage(self::ERROR_REFLECTION_ERROR)
                ->addVariable($class)
                ->addVariable('new')
                ->trigger();
        }
    }
}