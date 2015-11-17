<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace 
{
	
	require_once __DIR__.'/../../../autoload.php';
	
	Eden::DECORATOR;
	
 	class Bar_Foo_Zoo extends Eden\Core\Base
    {
        protected $foobar = 4;
        
        public function setFoobar($foobar)
        {
            $this->foobar = $foobar;
            return $this;
        }
        
        public function getFoobar()
        {
            return $this->foobar;
        }
    }
    
    class Bar_Foo_Index extends Bar_Foo_Zoo
    {
    }
}

namespace Foo\Bar {
    class Zoo extends \Eden\Core\Base
    {
        protected $foobar = 4;
        
        public function setFoobar($foobar)
        {
            $this->foobar = $foobar;
            return $this;
        }
        
        public function getFoobar()
        {
            return $this->foobar;
        }
    }
    
    class Foo_Zoo extends Zoo
    {
    }
    
    class Index extends Zoo
    {
    }
}