# When

More with chainability is *Eden's* conditional method called *when*. A basic usage of when can be found in `Figure 1`.

**Figure 1. Basic Usage**

	$isTrue = !!rand();
	
    eden()->when($isTrue, function($instance) {
        //do something
    });

The first argument is a conditional that should be set to either `true` or `false`. The second argument in `Figure 1` will be called if argument 1 is `true`. As we can see in the callback *Eden* automatically passes the instance to the callback. We can express the first argument in `Figure 1` also as a condition callback like in `Figure 2`.

**Figure 2. Conditional Callback**

    eden()->when(function($instance) {
		return true;
	}, function($instance) {
        //do something
    });

When argument 1 is a callable function, the conditional callback should return either `true` or `false`. *Eden* automatically passes the instance to the conditional callback as well. A more prime use case for `when` could be a message class.

**Figure 3. Message Handler**

	class Message extends Eden\Core\Base {
		protected $type = null;
		protected $message = null;
		
		public function __construct($message, $type) {
			$this->message = $message;
			$this->type = $type;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function getMessage() {
			return $this->message;
		}
	}
	
	eden()->Message('error', 'There was an error')
		->when(function($instance) {
			return $instance->getType() == 'error';
		}, function($instance) {
			echo '<div class="message-'.$this->getType().'">'.$this->message.'</div>';
		});

Though `Figure 3` is a mouthful the first part of it is merely defining a class extending `Eden\Core\Base`. When we instantiate `Message`, we only want to echo when the message is an error.