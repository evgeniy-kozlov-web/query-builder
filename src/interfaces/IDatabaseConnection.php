<?php

namespace app\interfaces;

interface IDatabaseConnection
{
	public function connect();
	public function getConnection();
}
