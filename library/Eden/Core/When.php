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
 * Trigger when something is false
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class When extends Base implements \ArrayAccess, \Iterator
{
    protected $scope     = null;
    protected $increment = 1;
    protected $lines     = 0;

    /**
     * Sets the scope and the lines to skip
     *
     * @param object
     * @param int
     * @return void
     */
    public function __construct($scope, $lines = 0)
    {
        Argument::i()
            ->test(1, 'object') //argument 1 must be an object
            ->test(2, 'int'); //argument 2 must be an integer

        if($this->lines < 0) {
            $this->lines = 0;
        }

        $this->scope = $scope;
        $this->lines = $lines;
    }

    /**
     * Calls the scopes method and returns the
     * scope when the amount of lines to skip
     * have exceeded, if not will return this class
     *
     * @param string
     * @param array
     * @return object
     */
    public function __call($name, $args)
    {
        Argument::i()
            ->test(1, 'string') //argument 1 must be a string
            ->test(2, 'array'); //argument 2 must be an array

        if($this->increment == $this->lines) {
            return $this->scope;
        }

        $this->increment++;
        return $this;
    }

    /**
     * Returns the current item
     * For Iterator interface
     *
     * @return void
     */
    public function current()
    {
        return $this->scope->current();
    }

    /**
     * Returns th current position
     * For Iterator interface
     *
     * @return void
     */
    public function key()
    {
        return $this->scope->key();
    }

    /**
     * Increases the position
     * For Iterator interface
     *
     * @return void
     */
    public function next()
    {
        $this->scope->next();
    }

    /**
     * isset using the ArrayAccess interface
     *
     * @param scalar|null|bool
     * @return bool
     */
    public function offsetExists($offset)
    {
		//argument 1 must be scalar, null or bool
        Argument::i()->test(1, 'scalar', 'null', 'bool');

        return $this->scope->offsetExists($offset);
    }

    /**
     * returns data using the ArrayAccess interface
     *
     * @param scalar|null|bool
     * @return bool
     */
    public function offsetGet($offset)
    {
		//argument 1 must be scalar, null or bool
        Argument::i()->test(1, 'scalar', 'null', 'bool');
		
        return $this->scope->offsetGet($offset);
    }

    /**
     * Sets data using the ArrayAccess interface
     *
     * @param scalar|null|bool
     * @param mixed
     * @return void
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * unsets using the ArrayAccess interface
     *
     * @param scalar|null|bool
     * @return bool
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Rewinds the position
     * For Iterator interface
     *
     * @return void
     */
    public function rewind()
    {
        $this->scope->rewind();
    }

    /**
     * Validates whether if the index is set
     * For Iterator interface
     *
     * @return void
     */
    public function valid()
    {
        return $this->scope->valid();
    }
}