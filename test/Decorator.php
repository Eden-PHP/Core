<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
class Eden_Core_Decorator_Test extends PHPUnit_Framework_TestCase 
{
    public function testDecor() 
    {
		$class = Eden::call()->Core_Event();
        $this->assertInstanceOf('Eden\\Core\\Event', $class);
    }
}
