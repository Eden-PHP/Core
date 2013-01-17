<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Tests_Core_BaseTest extends \PHPUnit_Framework_TestCase
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
	
	public function testCall()
    {
		$class = eden()->call('getActiveApp');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
    }
	
	public function testEach() {
		$final = null;
		$lastIndex = 0;
		
		eden('core')
			->route()
			->each(function($index, $string) use (&$final, &$lastIndex) {
				$final = $string;
				$lastIndex = $index;
			})->getFunction('trim', array('  Something  '));
		
		$this->assertEquals('Something', $final);
		$this->assertEquals(0, $lastIndex);
	}
	
	public function testInspect()
    {
		ob_start();
		eden('core')
			->route()->inspect(true)
			->getFunction('trim', array('  Something  '));
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('<pre>INSPECTING Eden\Core\Route->:</pre><pre>Something</pre>', $contents);
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
		eden('core')->event()->route('E');
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
		$class = eden()->when(true, 1)->setTimezone('GMT');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
		
		$class = eden()->when(false, 1)->setTimezone('GMT');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
		
		$class = eden()
			->when(true, 1)
			->setTimezone('GMT')
			->when(false, 1)
			->setTimezone('GMT');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
		
		$class = eden()
			->when(false, 1)
			->setTimezone('GMT')
			->when(true, 1)
			->setTimezone('GMT');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
		
		$class = eden()
			->when(false, 2)
			->setTimezone('GMT');
			
		$class = $class->setTimezone('GMT');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
    }
}