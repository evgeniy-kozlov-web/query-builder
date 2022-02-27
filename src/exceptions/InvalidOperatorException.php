<?php

namespace app\exceptions;

class InvalidOperatorException extends \Exception
{
	protected $code = 422;
	protected $message = 'Operator is invalid';
}
