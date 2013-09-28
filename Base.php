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
    const ERROR_CALL = 'Both Physical and Virtual method %s->%s() does not exist.';

    const INSTANCE = 0;

    private static $instances = array();
	
	private static $states = array();
	
	private static $invokables = array();

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
			//argument 1 must be a string
            ->test(1, 'string') 
			//argument 2 must be an array
            ->test(2, 'array'); 
		
		//if it's a registered method
		if(isset($this->invokables[$name])) {
			//do them a favor and pass the instance
			//into the last part of the argument
			$args[] = $this;
			//call and return it
			return call_user_func_array($this->invokables[$name], $args);
		}
		
        //if the method name starts with a capital letter
        //most likely they want a class
        if(preg_match("/^[A-Z]/", $name)) {
            //lets first consider that they may just
            //want to load a class so lets try
            try {
                //return the class
                return Route::i()->callArray($name, $args);
            //only if there's a Reflection exception do we want to catch it
            //this is because a class can throw an exception in their construct
            //so if that happens then we do know that the class has actually
            //been called and an exception is suppose to happen
            } catch(\ReflectionException $e) {
                //Bad class name? try namespacing
                $class = '\\'.str_replace('_', '\\', $name);
                //same explanation as the previous try
                try {
                    //return the class
                    return Route::i()->callArray($class, $args);
                //same explanation as the previous catch
                } catch(\ReflectionException $e) {}
            }
        }
		
		//let it fail
		Exception::i()
			->setMessage(self::ERROR_CALL)
			->addVariable(get_class($this))
			->addVariable($name)
			->trigger();
    }

    /**
     * We use __invoke to further make classes extended by Eden
     * access other classes easily.
     *
     * @param *string[,mixed..]
     * @return object
     */
    public function __invoke()
    {
        //if arguments are 0
        if(func_num_args() == 0) {
            //return this
            return Route::i()->callArray('\\Eden\\Core\\Controller');
        }

        //get the arguments
        $args = func_get_args();

        //if the first argument is an array
        if(is_array($args[0])) {
            //make the args that
            $args = $args[0];
        }

        //Fix class name
		$namespace = ucwords(array_shift($args));
        $class = '\\Eden\\'.$namespace.'\\Factory';
		
		//if factory isn't a class
		if(!class_exists($class)) {
			//test reflection of base
			$inspect = new \ReflectionClass('\\Eden\\'.$namespace.'\\Base');
			//if it's not abstract
			if(!$inspect->isAbstract()) {
				//make class to instantiate the base
				$class = '\\Eden\\'.$namespace.'\\Base';
			}
		}
		
        //try to
        try { //load factory
            //instantiate it
            return Route::i()->callArray($class, $args);
        } catch(Exception $e) {
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
     * arguments to be passed as an array
     *
     * @param *string method name
     * @param array arguments
     * @return mixed
     */
    public function callArray($method, array $args = array())
    {
        //argument 1 must be a string
        Argument::i()->test(1,'string');
		
		return call_user_func_array(array($this, $method), $args);
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

        //any other case output variable
        Inspect::i()
            ->output(sprintf(Inspect::INSPECT, 'Variable'))
            ->output($variable);

        return $this;
    }
	
	/**
     * Returns a state that was previously saved
     *
     * @param *string the state name
     * @return Eden\Core\Base
     */
	public function loadState($key) 
	{
		if(self::$states[$key]) {
			return self::$states[$key];
		}
		
		return $this;
	}
	
	/**
     * Loops through returned result sets
     *
     * @param *callable
     * @return Eden\Core\Base
     */
	public function loop($callback, $i = 0) 
	{
		//argument 1 must be callable
        Argument::i()->test(1,'callable');
		
		if(call_user_func($callback, $i, $this) !== false) {
			$this->loop($callback, $i + 1);
		}
		
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
			//argument 1 must be string
            ->test(1, 'string')              
			//argument 2 must be callable or null
            ->test(2, 'callable', 'null')    
			//argument 3 must be boolean
            ->test(3, 'bool');               

        Event::i()->listen($event, $callable, $important);

        return $this;
    }

    /**
     * Creates a class route for this class.
     *
     * @param *string the class route name
	 * @param callable|null
     * @return Eden\Core\Base
     */
    public function alias($source, $destination = null)
    {
        //argument test
        Argument::i()
			//argument 1 must be a string
			->test(1, 'string')				
			//argument 2 must be callable or null
			->test(2, 'callable', 'null');  

        if(is_null($destination)) {
            //when someone calls a class call this instead
            Route::i()->set($source, $this);
            return $this;
        }
		
		//store it
		$this->invokables[$source] = $desination;
		
        return $this;
    }
	
	/**
     * Sets instance state for later usage.
     *
     * @param *string the state name
     * @param mixed
     * @return Eden\Core\Base
     */
	public function saveState($key, $value = null) 
	{
		if(is_null($value)) {
			$value = $this;
		} else if(is_callable($value)) {
			$value = call_user_func($callback, $this);
		}
		
		self::$states[$key] = $value;
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

		call_user_func_array(
			array(Event::i(), 'trigger'),
			func_get_args());

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
			//argument 1 must be a string or null
            ->test(1, 'string', 'null')     
			//argument 2 must be a callable or null
            ->test(2, 'callable', 'null');  

        Event::i()->unlisten($event, $callable);

        return $this;
    }

    /**
     * Invokes Callback if conditional callback is true
     *
     * @param *callable|scalar|null
     * @param *callable
     * @return Eden\Core\Base
     */
    public function when($conditional, $callback)
    {
        Argument::i()
			//argument 1 must be callable, scalar or null
            ->test(1, 'callable', 'scalar', 'null')  
			//argument 2 must be callable
            ->test(2, 'callable');  

        if((is_callable($conditional) && call_user_func($conditional, $this)) 
		|| (!is_callable($conditional) && $conditional)) {
			call_user_func($callback, $this);
		}
		
		return $this;
    }

    /**
     * Returns an instance considering routes. With
     * this method we can instantiate while passing
     * construct arguments as arrays
     *
     * @param *string
     * @return object
     */
    private static function getInstance($class)
    {
		//get the backtrace
        $trace = debug_backtrace();
        $args = array();
		
		//the 2nd line is the caller method
        if(isset($trace[1]['args']) && count($trace[1]['args']) > 1) {
			//get the args
            $args = $trace[1]['args'];
            //shift out the class name
            array_shift($args);
		//then maybe it's the 3rd line?
        } else if(isset($trace[2]['args']) && count($trace[2]['args']) > 0) {
			//get the args
            $args = $trace[2]['args'];
        }
		
		//if there's no args or there's no construct to accept the args
        if(count($args) === 0 || !method_exists($class, '__construct')) {
			//just return the instantiation
            return new $class;
        }
		
		//at this point, we need to vitually call the class
        $reflect = new \ReflectionClass($class);
		
        try { //to return the instantiation
            return $reflect->newInstanceArgs($args);
        } catch(\Reflection_Exception $e) {
			//trigger error
            Exception::i()
                ->setMessage(self::ERROR_REFLECTION_ERROR)
                ->addVariable($class)
                ->addVariable('new')
                ->trigger();
        }
    }

    /**
     * Returns a non-singleton class, while considering routes
     *
     * @param string|null
     * @return object
     */
    protected static function getMultiple($class = null)
    {
		//super magic sauce getting the callers class
        if(is_null($class) && function_exists('get_called_class')) {
            $class = get_called_class();
        }
		
		//get routed class, if any
        $class = Route::i()->get($class);
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
		//super magic sauce getting the callers class
        if(is_null($class) && function_exists('get_called_class')) {
            $class = get_called_class();
        }
		
		//get routed class, if any
        $class = Route::i()->get($class);
		
		//if it's not set
        if(!isset(self::$instances[$class])) {
			//set it
            self::$instances[$class] = self::getInstance($class);
        }
		
		//return the cached version
        return self::$instances[$class];
    }
}