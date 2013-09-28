<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Core_Tests_Core_BaseTest extends \PHPUnit_Framework_TestCase
{
    public function test__Call()
    {
		$class = eden()->Eden_Core_Event();
		$this->assertInstanceOf('Eden\\Core\\Event', $class);
    }
	
	public function test__Invoke()
    {
		//eden('core')->event();		means	Eden\Core\Factory 		-> Eden\Core\Event
		//eden('core')->loader();		means	Eden\Core\Factory 		-> Eden\Core\Loader
		//eden('core')->route(); 		means 	Eden\Core\Factory 		-> Eden\Core\Route
		//eden('facebook')->graph();	means	Eden\Facebook\Factory 	-> Eden\Facebook\Graph
		//eden('utility')->session();	means	Eden\Utility\Factory 	-> Eden\Utility\Session
		$class = eden('core');
		$this->assertInstanceOf('Eden\\Core\\Factory', $class);
    }
	
	public function test__ToString()
    {
		$this->assertEquals('Eden\\Core\\Controller', (string) eden());
    }
	
	public function testCallArray()
    {
		$class = eden()->callArray('getActiveApp');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
    }
	
	public function testLoop() {
		$self = $this;
		eden()->loop(function($i, $instance) use ($self) {
			$self->assertInstanceOf('Eden\\Core\\Controller', $instance);
			
			if($i == 2) {
				return false;
			}
		});
	}
	
	public function testInspect()
    {
		ob_start();
		eden('core')->event()->inspect('Something');
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('<pre>INSPECTING Variable:</pre><pre>Something</pre>', $contents);
    }
	
	public function testListen()
	{
		$test = $this;
		$class = eden()->listen('some-event', function($event, $action) use ($test) {
			$test->assertEquals('some-event', $action);
		});
		
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
	}
	
    public function testRoute()
    {
		eden('core')->event()->alias('E');
		$this->assertInstanceOf('Eden\\Core\\Event', eden()->E());
    }

    public function testTrigger()
    {
		$class = eden()->trigger('some-event');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
    }

    public function testUnlisten()
    {
		$class = eden()->unlisten('some-event');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
		eden()->trigger('some-event');
    }
	
    public function testWhen()
    {
		$test = 'Good';
		eden()->when(function() {
			return false;
		}, function() use (&$test) {
			$test = 'Bad';
		});
		
		$this->assertSame('Good', $test);
		
		$test = 'Good';
		eden()->when(null, function() use (&$test) {
			$test = 'Bad';
		});
		
		$this->assertSame('Good', $test);
		
		$test = 'Good';
		eden()->when(false, function() use (&$test) {
			$test = 'Bad';
		});
		
		$this->assertSame('Good', $test);
		
		
		$test = 'Good';
		eden()->when(function() {
			return true;
		}, function() use (&$test) {
			$test = 'Bad';
		});
		
		$this->assertSame('Bad', $test);
		
		$test = 'Good';
		eden()->when('hi', function() use (&$test) {
			$test = 'Bad';
		});
		
		$this->assertSame('Bad', $test);
		
		$test = 'Good';
		eden()->when(true, function() use (&$test) {
			$test = 'Bad';
		});
		
		$this->assertSame('Bad', $test);
    }
}
