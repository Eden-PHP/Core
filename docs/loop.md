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

If you wanted the loop callback to actually manipulate the `$list` array, `Figure 3` can show how this can be acheived.

**Figure 3. Reference Pass**

	$list = array(1, 2, 3, 4);
	
	eden()->loop(function($i) use (&$list) {
		$list[] = $i + 5;
		if($list[$i] == 3) {
			return false;
		}
	});
	
	print_r($list);

Loops also automatically passes the object from where the origin of the loop began.

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
