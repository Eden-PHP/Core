<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2012-2013 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

require_once __DIR__.'/../Loader.php';
Eden\Core\Loader::i()->addRoot(true, 'Eden\\Core')->register()->load('Controller');