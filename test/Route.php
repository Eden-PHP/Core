<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
class EdenCoreRouteTest extends \PHPUnit_Framework_TestCase 
{   
    public function testCall() 
    {
        $class = eden('core')->route()->call('Eden\\Core\\Exception', 'Something');
        $this->assertInstanceOf('Eden\\Core\\Exception', $class);
    }
       
    public function testCallArray() 
    {
        $class = eden('core')->route()->callArray('Eden\\Core\\Exception', array('Something'));
        $this->assertInstanceOf('Eden\\Core\\Exception', $class);
    }
    
    public function testSet() 
    {
        //renaming a class example
        $class = eden('core')->route()->set('Test', 'Eden\\Core\\Exception');
        $this->assertInstanceOf('Eden\\Core\\Route', $class);
        
        $string = eden()->Test()->getType();
        $this->assertEquals('LOGIC', $string);
        
        //shortcut example
        eden('core')->route()->set('Event', 'Eden\\Core\\Event');
        $this->assertInstanceOf('Eden\\Core\\Event', eden()->Event());
    }
    
    public function testGet() 
    {
        $route = eden('core')->route();
        $this->assertEquals('Eden\\Core\\Exception', $route->get('test'));
        $this->assertArrayHasKey('test', $route->get());
    }
    
    public function testValid() 
    {
        $route = eden('core')->route();
        $this->assertTrue($route->valid('test'));
        $this->assertFalse($route->valid('another'));
    }
    
    public function testRelease() 
    {
        $route = eden('core')->route()->release('test');
        $this->assertFalse($route->valid('test'));
    }
}