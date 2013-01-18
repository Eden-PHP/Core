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
 * Loops through returned result sets
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Loop extends Base
{
    const INSTANCE = 1;

    protected $scope = null;
    protected $callback = null;

    /**
     * Performs the scope's call and then iterates
     * through the results calling the callback.
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

        //if the scope is null
        if(is_null($this->scope)) {
            //just call the parent
            return parent::__call($name, $args);
        }

        //get the results from the method call
        $results = $this->getResults($name, $args);

        //lets make sure this is loopable
        $loopable = is_scalar($results) ? array($results) : $results;

        //at this point we should loop through the results
        foreach($loopable as $key => $value) {
            if(call_user_func($this->callback, $key, $value) === false) {
                break;
            }
        }

        //and return the results
        return $results;
    }

    /**
     * Hijacks the class and loop through the results
     *
     * @param *object
     * @param *callable
     * @return Eden\Core\Loop
     */
    public function iterate($scope, $callback)
    {
        Argument::i()
            ->test(1, 'object')
            ->test(2, 'callable');

        $this->scope     = $scope;
        $this->callback    = $callback;

        return $this;
    }

    /**
     * Virtually calls the scope's method considering routes
     *
     * @param *string
     * @param *array
     * @return mixed
     */
    protected function getResults($name, $args)
    {
        if(method_exists($this->scope, $name)) {
            return call_user_func_array(array($this->scope, $name), $args);
        }

        return $this->scope->call($name, $args);
    }
}