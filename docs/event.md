# Event Driven

Events in Eden are for executing sub routines not originally part of an application scope. A basic usage can be found in `Figure 1`.

**Figure 1. Basic Usage**

	eden()->listen('to-something', function($instance, $action) {
		//do something
	})
	
	->trigger('to-something');

In `Figure 1`, there are two parts to an event. The first one is the listener. You can have many listeners to a single event. The order that it will be called is in a first come first serve order. 

**Figure 2. Importance**

	eden()->listen('to-something', function($instance, $action) {
		//do something
	}, true);

Adding `true` as the last argument in the listener method will serve up that callback first, pending if it was the last method to set it to `true`. You can also pass extra variables to a listener whenever you trigger an event. `Figure 3` explains how this can be done.

**Figure 3. Passing Arguments**

	eden()->listen('to-something', function($instance, $action, $extra, $extraMore) {
		echo $extra.' '.$extraMore; //--> something extra
	})
	
	->trigger('to-something', 'something', 'extra');

Emailing when a comment as been made on your post or logging when an error has been thrown would be valid implementations for an event driven design. With *Eden*, you can easily design your applications to be plugin ready. The example below shows a skinned down version of an order processing function.

**Figure 4. Processing Orders**

	function processOrder($email, $price) {
		if($price < 1) {
			return false;
		}
		 
		//insert into database
		 
		return true;
	}
	 
	if(processOrder('info@openovate.com', 56.99)) {
		echo 'Success!';
	}

When given a set of functional requirements we would think to add it in `processOrder()` linearly. In event driven design, a function should only perform the main process stated on the function name. `processOrder()`, for example should just insert the order into the database and nothing more. After it is done, we should trigger that this action has been performed.

**Figure 5. Adding a Trigger**

	function processOrder($email, $price) {
		if($price < 1) {
			return false;
		}
		 
		//insert into database
		 
		eden()->trigger('success', $email, 'Success!', 'We triggered a success.');
		 
		return true;
	}
	 
	if(processOrder('info@openovate.com', 56.99)) {
		echo 'Success!';
	}

In `Figure 5`, we add a trigger passing all possible variables to whatever other method is listening to that. This is the only requirement to make your application event driven. The example below represents how to build a plugin that will listen for a success event to occur.

**Figure 6. Listen**

	eden()
	->listen('success', function(
		$event, 
		$email, 
		$subject, 
		$message
	) {
		mail($email, $subject, $message);
	});

> **Note:** Eden\Core\Event, when called will be a singleton. This makes it a global event handler. You can create a separate set of events by simply extending this class.

In the example above, we created a callback that will listen to a success event. When a success event is triggered the callback method will be called passing all the arguments as specified by the trigger.