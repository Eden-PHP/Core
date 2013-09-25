# Instantiating Classes

In Eden, there are many ways to instantiate a class because one of Eden's methodologies is that Eden does not tell you how to build an application. Eden simply provides tools for you to build it faster. First off any class ie. `class Sample\Class {}` can be called as in `Figure 1`.

**Figure 1. Standard Instantiation**

	new Sample\Class();

Like `Figure 1` you can instantiate classes in the normal PHP way using `new`. The problem with `new` is that it also requires you to use classes in at least 2 lines of code. In Eden, we have the availability to call classes using the controller as in `Figure 2`.

**Figure 2. Use the Controller**

	eden()->Sample_Class();

As long as you start with `eden()`, you can easily instantiate classes and call methods, possible in one line. Any class you define that extends `Eden\Core\Base` can instantiate classes within the definition as in `Figure 3`.

**Figure 3. Inside the Class**

	class MyClass extends Eden\Core\Base 
	{
		public function call() {
			$this->Sample_Class();
		}
	}

Extending `Eden\Core\Base` transforms your `$this` into something as powerful as `eden()`. Much like how Eden calls classes you can instantiate classes with `$this` as in `$this('core')->inspect()` or `$this()->setTimezone('GMT')`. Also any Eden Class as well as any class you define that extends `Eden\Core\Base` can be instantiated as in `Figure 4`.

**Figure 4. The i() Method**

	MyClass::i();

Passing arguments to a constructor using any of the above figures can be achieved by simply adding it between the parameters.

**Figure 5. Passing Arguments**

	new Sample\Class('argument 1', array());

	eden()->Sample_Class('argument 1', array());

	$this->Sample_Class('argument 1', array());

	MyClass::i('argument 1', array());