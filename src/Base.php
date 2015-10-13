<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Core;

/**
 * The base class for all classes wishing to integrate with Eden.
 * Extending this class will allow your methods to seemlessly be
 * overloaded and overrided as well as provide some basic class
 * loading patterns.
 *
 * @package  Eden
 * @category Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Base
{
    /**
     * @const string ERROR_REFLECTION Error template
     */
    const ERROR_REFLECTION = 'Error creating Reflection Class: %s, Method: %s.';
    
    /**
     * @const string ERROR_CALL Error template
     */
    const ERROR_CALL = 'Both Physical and Virtual method %s->%s() does not exist.';
    
    /**
     * @const int INSTANCE Flag that designates multiton when using ::i()
     */
    const INSTANCE = 0;
    
    /**
     * @var array $states memory cache of instances saved
     */
    private static $states = array();
    
    /**
     * @var array $instances captured sungleton instances
     */
    private static $instances = array();
    
    /**
     * @var array $invokables cache of invokable callbacks
     */
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
     * @param mixed[,mixed..] $args Arguments to pass to the constructor
     *
     * @return object
     */
    public static function i()
    {
        if (static::INSTANCE === 1) {
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
     * @param *string $name name of method
     * @param *array  $args arguments to pass
     *
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
        if (isset(self::$invokables[$name])) {
            //call and return it
            return call_user_func_array(self::$invokables[$name], $args);
        }
        
        //if the method name starts with a capital letter
        //most likely they want a class
        if (preg_match("/^[A-Z]/", $name)) {
            //lets first consider that they may just
            //want to load a class so lets try
            try {
                //return the class
                return Route::i()->callArray($name, $args);
                //only if there's a Reflection exception do we want to catch it
                //this is because a class can throw an exception in their construct
                //so if that happens then we do know that the class has actually
                //been called and an exception is suppose to happen
            } catch (\ReflectionException $e) {
                //Bad class name? try namespacing
                $class = '\\'.str_replace('_', '\\', $name);
                //same explanation as the previous try
                try {
                    //return the class
                    return Route::i()->callArray($class, $args);
                    //same explanation as the previous catch
                } catch (\ReflectionException $e) {
                    //ok... try invoking
                    array_unshift($args, $name);
                    return call_user_func_array(array($this, '__invoke'), $args);
                }
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
     * @param string           $class name of the class
     * @param mixed[, mixed..] $args  arguments to be passed
     *
     * @return object
     */
    public function __invoke()
    {
        //if arguments are 0
        if (func_num_args() == 0) {
            //return this
            return $this;
        }

        //get the arguments
        $args = func_get_args();

        //if the first argument is an array
        if (is_array($args[0])) {
            //make the args that
            $args = $args[0];
        }
        
        //Fix class name
        $class = $name = array_shift($args);
        
        $class = str_replace('_', ' ', $class);
        $class = str_replace(' ', '_', ucwords($class));
        $class = str_replace('\\', ' ', $class);
        $class = str_replace(' ', '\\', ucwords($class));
        
        $name = $class;
        
        //try to see if it's not an eden class
        if (!class_exists($class)) {
            //try adding on Index
            if (strpos($class, '\\') !== false) {
                $class .= '\\Index';
            } else {
                $class .= '_Index';
            }
        }
        
        if (!class_exists($class)) {
            //reset
            $class = $name;
            //try to convert _ to \\
            $class = str_replace('_', '\\', $class);
        }
        
        if (!class_exists($class)) {
            $class .= '\\Index';
        }
        
        //I give up. It must be an eden class
        if (!class_exists($class)) {
            //reset
            $class = $name;
            
            //if there is no _ ie. Facebook
            if (strpos($class, '_') === false) {
                //make it into Eden_Facebook_Index
                $class = 'Eden\\' . $class . '\\Index';
            } else {
                $class = str_replace('_', '\\', $class);
            }
            
            //if this class does not start with Eden ie. Facebook_Graph
            if (strpos($class, 'Eden\\') !== 0) {
                //make it into Eden_Facebook_Graph
                $class = 'Eden\\' . $class;
            }
        }
        
        //if factory isn't a class
        if (!class_exists($class)) {
            $class = $name;
            
            //Logic for Legacy
            //if there is no _ ie. Facebook
            if (strpos($class, '_') === false) {
                //make it into Eden_Facebook_Index
                $class = 'Eden_' . $class . '_Index';
            }
            
            //if this class does not start with Eden ie. Facebook_Graph
            if (strpos($class, 'Eden_') !== 0) {
                //make it into Eden_Facebook_Graph
                $class = 'Eden_' . $class;
            }
            
            //if factory isn't a class
            if (!class_exists($class)) {
                $class = $name;
            }
        }
        
        //try to
        try { //load factory
            //instantiate it
            return Route::i()->callArray($class, $args);
        } catch (\Exception $e) {
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
     * Creates a class route for this class.
     *
     * @param *string       $source     the class route name
     * @param callable|null $desination callback handler
     *
     * @return Eden\Core\Base
     */
    public function addMethod($source, $destination = null)
    {
        //argument test
         Argument::i()
            //argument 1 must be a string
            ->test(1, 'string')
            //argument 2 must be callable or null
            ->test(2, 'callable', 'null');

        if (is_null($destination)) {
            //when someone calls a class call this instead
            Route::i()->set($source, $this);
            return $this;
        }
        
        //if it's an array they meant to bind the callback
        if (!is_array($destination)) {
            //so there's no scope
            $destination = $destination->bindTo($this, get_class($this));
        }
        
        //store it
        self::$invokables[$source] = $destination;
        
        return $this;
    }
    
    /**
     * Calls a method in this class and allows
     * arguments to be passed as an array
     *
     * @param *string $method method name
     * @param array   $args   arguments
     *
     * @return mixed
     */
    public function callArray($method, array $args = array())
    {
        //argument 1 must be a string
        Argument::i()->test(1, 'string');
        
        return call_user_func_array(array($this, $method), $args);
    }

    /**
     * Force outputs any class property
     *
     * @param mixed       $variable name or value to be inspected
     * @param string|null $next     the name of the next available variable
     *
     * @return Eden\Core\Base
     */
    public function inspect($variable = null, $next = null)
    {
        //argument 2 must be a string or null
        Argument::i()->test(2, 'string', 'null');

        //we are using tool in all cases
        $class = get_class($this);

        //if variable is null
        if (is_null($variable)) {
            //output the class
            Inspect::i()
                ->output(sprintf(Inspect::INSPECT, $class))
                ->output($this);

            return $this;
        }

        //if variable is true
        if ($variable === true) {
            //return whatever the next response is
            //or return the next specified variable
            return Inspect::i()->next($this, $next);
        }

        //if variable is not a string
        if (!is_string($variable)) {
            //output variable
            Inspect::i()
                ->output(sprintf(Inspect::INSPECT, 'Variable'))
                ->output($variable);

            return $this;
        }

        //if variable is set
        if (isset($this->$variable)) {
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
     * @param *string $key the state name
     *
     * @return Eden\Core\Base|null
     */
    public function loadState($key)
    {
        if (isset(self::$states[$key])) {
            return self::$states[$key];
        }
        
        return null;
    }
    
    /**
     * Loops through returned result sets
     *
     * @param *callable $callback the callback method
     * @param int       $i        the incrementor
     *
     * @return Eden\Core\Base
     */
    public function loop($callback, $i = 0)
    {
        //argument 1 must be callable
        Argument::i()->test(1, 'callable');
        
        $bound = $callback->bindTo($this, get_class($this));
        
        if (call_user_func($bound, $i) !== false) {
            $this->loop($callback, $i + 1);
        }
        
        return $this;
    }

    /**
     * Stops listening to an event
     *
     * @param string|null   $event    name of the event
     * @param callable|null $callable callback handler
     *
     * @return Eden\Core\Base
     */
    public function off($event = null, $callable = null)
    {
         Argument::i()
            //argument 1 must be a string or null
            ->test(1, 'string', 'null')
            //argument 2 must be a callable or null
            ->test(2, 'callable', 'null');

        Event::i()->off($event, $callable);

        return $this;
    }

    /**
     * Attaches an instance to be notified
     * when an event has been triggered
     *
     * @param *string   $event     the name of the event
     * @param *callable $callback  the event handler
     * @param bool      $important if true will be prepended in order
     *
     * @return Eden\Core\Base
     */
    public function on($event, $callback, $important = false)
    {
         Argument::i()
            //argument 1 must be string
            ->test(1, 'string')
            //argument 2 must be callable or null
            ->test(2, 'callable', 'null')
            //argument 3 must be boolean
            ->test(3, 'bool');
        
        //if it's an array they meant to bind the callback
        if (!is_array($callback)) {
            //so there's no scope
            $callback = $callback->bindTo($this, get_class($this));
        }
        
        Event::i()->on($event, $callback, $important);

        return $this;
    }
    
    /**
     * Sets instance state for later usage.
     *
     * @param *string $key   the state name
     * @param mixed   $value the instance to save
     *
     * @return Eden\Core\Base
     */
    public function saveState($key, $value = null)
    {
        if (is_null($value)) {
            $value = $this;
        } else if (is_callable($value)) {
            $value = call_user_func($callback, $this);
        }
        
        self::$states[$key] = $value;
        return $this;
    }
    
    /**
     * Notify all observers of that a specific
     * event has happened
     *
     * @param string|null      $event the event to trigger
     * @param mixed[, mixed..] $arg   the arguments to pass to the handler
     *
     * @return Eden\Core\Base
     */
    public function trigger($event = null)
    {
        //argument 1 must be string
        Argument::i()->test(1, 'string', 'null');

        call_user_func_array(
            array(Event::i(), 'trigger'),
            func_get_args()
        );

        return $this;
    }

    /**
     * Invokes Callback if conditional callback is true
     *
     * @param *callable|scalar|null $conditional should evaluate to true
     * @param *callable             $success     called when conditional is true
     * @param callable              $fail        called when conditional is false
     *
     * @return Eden\Core\Base
     */
    public function when($conditional, $success, $fail = null)
    {
        Argument::i()
            //argument 1 must be callable, scalar or null
            ->test(1, 'callable', 'scalar', 'null')
            //argument 2 must be callable
            ->test(2, 'callable')
            //argument 3 must be callable or null
            ->test(3, 'callable', 'null');
        
        //bind conditional if it's not bound
        if (is_callable($conditional) && !is_array($conditional)) {
            $conditional = $conditional->bindTo($this, get_class($this));
        }
        
        //bind success if it's not bound
        if (is_callable($success) && !is_array($success)) {
            $success = $success->bindTo($this, get_class($this));
        }
        
        //bind fail if it's not bound
        if (is_callable($fail) && !is_array($fail)) {
            $fail = $fail->bindTo($this, get_class($this));
        }
        
        //default results is null
        $results = null;
        
        //if condition is true
        if ((is_callable($conditional) && call_user_func($conditional))
            || (!is_callable($conditional) && $conditional)
        ) {
            //call success
            $results = call_user_func($success);
        } else if (is_callable($fail)) {
            //call fail
            $results = call_user_func($fail);
        }
        
        //do we have results ?
        if ($results !== null) {
            //then return it
            return $results;
        }
        
        //otherwise return this
        return $this;
    }
    
    /**
     * Returns a non-singleton class, while considering routes
     *
     * @param string|null $class name of the class
     *
     * @return object
     */
    protected static function getMultiple($class = null)
    {
        //super magic sauce getting the callers class
        if (is_null($class) && function_exists('get_called_class')) {
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
     * @param string|null $class name of the class
     *
     * @return object
     */
    protected static function getSingleton($class = null)
    {
        //super magic sauce getting the callers class
        if (is_null($class) && function_exists('get_called_class')) {
            $class = get_called_class();
        }
        
        //get routed class, if any
        $class = Route::i()->get($class);
        
        //if it's not set
        if (!isset(self::$instances[$class])) {
            //set it
            self::$instances[$class] = self::getInstance($class);
        }
        
        //return the cached version
        return self::$instances[$class];
    }
    
    /**
     * Returns an instance considering routes. With
     * this method we can instantiate while passing
     * construct arguments as arrays
     *
     * @param *string $class name of the class
     *
     * @return object
     */
    private static function getInstance($class)
    {
        //get the backtrace
        $trace = debug_backtrace();
        $args = array();
        
        //the 2nd line is the caller method
        if (isset($trace[1]['args']) && count($trace[1]['args']) > 1) {
            //get the args
            $args = $trace[1]['args'];
            //shift out the class name
            array_shift($args);
            //then maybe it's the 3rd line?
        } else if (isset($trace[2]['args']) && count($trace[2]['args']) > 0) {
            //get the args
            $args = $trace[2]['args'];
        }
        
        //if there's no args or there's no construct to accept the args
        if (count($args) === 0 || !method_exists($class, '__construct')) {
            //just return the instantiation
            return new $class;
        }
        
        //at this point, we need to vitually call the class
        $reflect = new \ReflectionClass($class);
        
        try { //to return the instantiation
            return $reflect->newInstanceArgs($args);
        } catch (\ReflectionException $e) {
            //trigger error
            Exception::i()
                ->setMessage(self::ERROR_REFLECTION)
                ->addVariable($class)
                ->addVariable('new')
                ->trigger();
        }
    }
}
