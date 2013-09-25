# Inspecting

A useful tool when troubleshooting code is Eden's handy inspector feature. At face value you can inspect any kind of variable which would output the value in a readable format

**Figure 1. Output any variable**

	$hello = 'world';
	eden()->inspect($hello);

Executing `Figure 1` would output `world`, which is semi-impressive. Arrays and objects as in `Figure 2` would show nice formatting when used.

**Figure 2. Output array**

	$hello = array(4, 3, 2, 1);
	eden()->inspect($hello);

would show

	Array
	(
		[0] => 4,
		[1] => 3,
		[2] => 2,
		[3] => 1
	)

More than just outputting random variables, The inspector can expose data whether private or protected when used within classes.

**Figure 3. Inspecting inside the class**

	class MyClass extends Eden\Core\Base {
		protected $id = 4;
		
		public function getId() {
			$this->inspect('id');
			
			return $this->id;
		}
	}

For classes like the one in `Figure 3`, you can inspect the next upcoming results like `Figure 4`.

**Figure 4. Inspect getId()**

	eden()->MyClass()->inspect(true)->getId();

Using `Figure 4` would output the results from `getId()`, no need to echo.