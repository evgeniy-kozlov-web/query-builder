<?php

namespace app\exceptions;

class InvalidDMLTypeException extends \Exception
{
	protected $code = 422;
	protected $message = 'DML Type is invalid';
}
