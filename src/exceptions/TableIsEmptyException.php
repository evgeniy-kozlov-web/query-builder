<?php

namespace app\exceptions;

class TableIsEmptyException extends \Exception
{
	protected $code = 422;
	protected $message = 'Table is empty';
}
