<?php

namespace app\exceptions;

class BadCredentialsException extends \Exception
{
	protected $code = 422;
	protected $message = 'Credentials is bad';
}
