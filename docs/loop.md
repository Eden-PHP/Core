# Looping

Looping is possible with Eden without leaving the chain. The following figure shows a basic example of how this can be acheived.

**Figure 1. Basic Example**

	eden()->loop(function($i) {
		echo "Hello World<br />\n";
		
		if($i > 10) {
			return false;
		}
	});

It's important that your loop callback eventually returns false to tell Eden that you are done your iteration and to continue to the next chain process. Also in PHP 5.3 you can pass variables into the loop callback manually as in `Figure 2` and `Figure 3`.

**Figure 2. Passing variables**

	$list = array(1, 2, 3, 4);
	
	eden()->loop(function($i) use ($list) {
		if($list[$i] == 3) {
			return false;
		}
	});

In `Figure 2`, we created an array of numbers called `$list` and then passed `$list` into our loop callback using PHP 5.3 `use ($list)`. Sometimes you may want the loop callback to actually manipulate the `$list` array, `Figure 3` can show how this can be acheived.

**Figure 3. Reference Pass**

	$list = array(1, 2, 3, 4);
	
	eden()->loop(function($i) use (&$list) {
		$list[] = $i + 5;
		if($list[$i] == 3) {
			return false;
		}
	});
	
	print_r($list);

`Figure 3` looks similar like `Figure 2` with the exception of `use (&$list)`, which allows our loop callback to manipulate the array on a global level. Loops also automatically passes the object from where the origin of the loop began.

**Figure 4. Using Instances**

	class User extends Eden\Core\Base {
		public $friends = array('Sarah', 'John', 'Cole');
	}
	
	eden()->User()->loop(function($i, $instance) {
		if(!isset($instance->friends[$i])) {
			return false;
		}
		
		echo $instance->friends[$i].' ';
	});

The first thing we did in `Figure 4` was create a class called *User* which extends `Eden\Core\Base`. From here we can instantiate that class and call the `loop()` method which passes our instance of *User* to be used inside of the scope.