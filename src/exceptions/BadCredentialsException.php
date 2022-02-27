<?php

namespace app\exceptions;

class BadCredentialsException extends \Exception
{
	protected $code = 500;
	protected $message = 'Credentials is bad';
}
