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
 * Allows the ability to listen to events made known by another
 * piece of functionality. Events are items that transpire based
 * on an action. With events you can add extra functionality
 * right after the event has triggered.
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Event extends Base
{
    const INSTANCE = 1;

    protected $observers = array();

    /**
     * Attaches an instance to be notified
     * when an event has been triggered
     *
     * @param *string
     * @param *callable
     * @param bool
     * @return Eden\Core\Event
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

        $id = $this->getId($callable);

        //set up the observer
        $observer = array($event, $id, $callable);

        //if this is important
        if($important) {
            //put the observer on the top of the list
            array_unshift($this->observers, $observer);
            return $this;
        }

        //add the observer
        $this->observers[] = $observer;
        return $this;
    }

    /**
     * Notify all observers of that a specific
     * event has happened
     *
     * @param [string|null[,mixed..]]
     * @return Eden\Core\Event
     */
    public function trigger($event = null)
    {
        //argument 1 must be string
        Argument::i()->test(1, 'string', 'null');

        if(is_null($event)) {
            $trace = debug_backtrace();
            $event = $trace[1]['function'];
            if(isset($trace[1]['class']) && trim($trace[1]['class'])) {
                $event = str_replace('\\', '-', $trace[1]['class']).'-'.$event;
            }
        }

        //get the arguments
        $args = func_get_args();
        //shift out the event
        array_shift($args);

        //as a courtesy lets shift in the object
        array_unshift($args, $this, $event);

        //for each observer
        foreach($this->observers as $observer) {
            //if this is the same event, call the method, if the method returns false
            if($event == $observer[0] && call_user_func_array($observer[2], $args) === false) {
                //break out of the loop
                break;
            }
        }

        return $this;
    }

    /**
     * Stops listening to an event
     *
     * @param string|null
     * @param callable|null
     * @return Eden\Core\Event
     */
    public function unlisten($event = null, $callable = null)
    {
        Argument::i()
			//argument 1 must be string or null
            ->test(1, 'string', 'null')     
			//argument 2 must be callable or null
            ->test(2, 'callable', 'null');  

        //if there is no event and no callable
        if(is_null($event) && is_null($callable)) {
            //it means that they want to remove everything
            $this->observers = array();
            return $this;
        }

        $id = $this->getId($callable);

        //for each observer
        foreach($this->observers as $i => $observer) {
            //if there is an event and is not being listened to
            if(!is_null($event) && $event != $observer[0]) {
                //skip it
                continue;
            }

            if(!is_null($callable) && $id != $observer[1]) {
                continue;
            }

            //unset it
            unset($this->observers[$i]);
        }

        return $this;
    }

    /**
     * Tries to generate an ID for a callable.
     * We need to try in order to properly unlisten
     * to a variable
     *
     * @param *callable
     * @return string|false
     */
    protected function getId($callable)
    {
        if(is_array($callable)) {
            if(is_object($callable[0])) {
                $callable[0] = spl_object_hash($callable[0]);
            }

            return $callable[0].'::'.$callable[1];
        }

        if(is_string($callable)) {
            return $callable;
        }

        return false;
    }

}