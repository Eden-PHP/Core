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
 * Used to inspect classes and result sets
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Inspect extends Base
{
    const INSTANCE = 1;

    const INSPECT = 'INSPECTING %s:';

    protected $scope = null;
    protected $name  = null;

    /**
     * Call a method of the scope and output it
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

        //if the scope is null
        if(is_null($this->scope)) {
            //just call the parent
            return parent::__call($name, $args);
        }

        //get the results from the method call
        $results = $this->getResults($name, $args);

        //set temp variables
        $name = $this->name;
        $scope = $this->scope;

        //reset globals
        $this->name = null;
        $this->scope = null;

        //if there's a property name
        if($name) {
            //output that
            $scope->inspect($name);
            //and return the results
            return $results;
        }

        //at this point we should output the results
        $class = get_class($scope);

        $this->output(sprintf(self::INSPECT, $class.'->'.$name))->output($results);

        //and return the results
        return $results;
    }

    /**
     * Hijacks the class and reports the results of the next
     * method call
     *
     * @param *object
     * @param string
     * @return Eden\Core\Inspect
     */
    public function next($scope, $name = null)
    {
        Argument::i()
            ->test(1, 'object')          //argument 1 must be an object
            ->test(2, 'string', 'null'); //argument 2 must be a string or null

        $this->scope = $scope;
        $this->name = $name;

        return $this;
    }

    /**
     * Outputs anything
     *
     * @param *mixed any data
     * @return Eden\Core\Inspect
     */
    public function output($variable)
    {
        if($variable === true) {
            $variable = '*TRUE*';
        } else if($variable === false) {
            $variable = '*FALSE*';
        } else if(is_null($variable)) {
            $variable = '*null*';
        }

        echo '<pre>'.print_r($variable, true).'</pre>';
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