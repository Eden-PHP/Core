<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
class EdenCoreEventTest extends PHPUnit_Framework_TestCase 
{
    public function testOn() 
    {
        $test = $this;
        $class = eden('core')->event()->on('some-event', function($foo) use ($test) {
            $test->assertEquals('bar', $foo);
        });
        
        $this->assertInstanceOf('Eden\\Core\\Event', $class);
    }

    public function testTrigger() 
    {
        $class = eden('core')->event()->trigger('some-event', 'bar');
        $this->assertInstanceOf('Eden\\Core\\Event', $class);
    }

    public function testOff() 
    {
        $class = eden('core')->event()->off('some-event');
        $this->assertInstanceOf('Eden\\Core\\Event', $class);
        eden('core')->event()->trigger('some-event', 'bar');
    }
}