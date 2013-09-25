# States

A new feature of *Eden*, promoting chainability is the use of handling chain states. A basic usage can be found in `Figure 1`.

**Figure 1. Basic Usage**

	eden()->setState('[YOUR STATE NAME]')->getState('[YOUR STATE NAME]');

`Figure 1` is pretty ambiguous, but let's say you are working on a user registration process. Usually with registration you add the new user to the database and send them an email. We should also mark whether if the email has been sent out or not. We can use states to acheived this process as in `Figure 2`,

**Figure 2. Epic State Chain**

	//1. load up mysql
	eden('mysql', '127.0.0.1', 'app', 'root', '')
		//2. load up model
		->model()
		//3. set user slug
		->setUserSlug('chris')
		//4. set user name
		->setUserName('Chris')
		//5. save model to user table in database
		->save('user')
		//6. save the user model state
		->setState('user')
		
		//7. load up email
		->Eden_Mail()
		//8. load up SMTP
		->smtp(
			'imap.gmail.com', 
			'[YOUR EMAIL]', 
			'[YOUR PASSWORD]', 
			993, 
			true)
		//9. add recipient
		->addTo('cblanquera@openovate.com')
		//10. set the subject
		->setSubject('New Registration')
		//11. set the body
		->setBody('Welcome!')
		//12. send out the email
		->send()
		//13. load up the user model state
		//we saved earlier
		->loadState('user')
		//14. set user validation
		->setUserValidation(1)
		//15. save model to user table in database
		->save('user');
	
`Figure 2` is quite a mouthful, but let's go over the main parts regarding states. The first 5 steps are about initially saving the user data into our MySQL database. Step 6 `6. save the user model state` saves the instance internally to a key called `user` as in `->setState('user')`. Next, step 7 to 12 are about sending an email out using `Eden\Mail\Smtp`. Then we return back to our user state in step 13 using `->loadState('user')` so we can confirm the email has been sent out by updating that row with step 14 `->setUserValidation(1)`.

As we can see we never had to leave the chain to accomplish multiple tasks and we never needed to set a variable as well. We can even set any variables to a state.

> **Note:** Okay almost any variable. Setting a state value to *NULL* will set the state to the instance.

**Figure 3. Setting state to a variable**

	$userId = 123;
	echo eden()->setState('user_id', $userId)->getState('user_id');

In `Figure 3`, we simply created a state and set it to `$userId`. We can now call that state at any time using `->getState('user_id')`.

> **Note:** All states are shared across every class. Because of ths, *Eden* core does not use states, so you are free to use any key name without fear.

States can also be set to an evaluated value using callback as in `Figure 3`.

**Figure 4. Evaluated State**

	echo eden()
		//1. load user state from Figure 2.
		->loadState('user')
		//2. set user id
		->setState('user_id', function($instance) {
			return $instance->getUserId();
		})
		//3. get the user_id state
		->getState('user_id');

The figure above is a continuation of `Figure 2`. We first load the user model state and we set a new state called `user_id`. The second argument step 2 we can use a callback to help evaluate the value that would be set to `user_id`.