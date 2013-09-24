<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Core_Tests_Core_EventTest extends \PHPUnit_Framework_TestCase
{
    public function testListen()
	{
		$test = $this;
		$class = eden('core')->event()->listen('some-event', function($event, $action) use ($test) {
			$test->assertEquals('some-event', $action);
		});
		
		$this->assertInstanceOf('Eden\\Core\\Event', $class);
	}

    public function testTrigger()
    {
		$class = eden('core')->event()->trigger('some-event');
		$this->assertInstanceOf('Eden\\Core\\Event', $class);
    }

    public function testUnlisten()
    {
		$class = eden('core')->event()->unlisten('some-event');
		$this->assertInstanceOf('Eden\\Core\\Event', $class);
		eden('core')->event()->trigger('some-event');
    }
}