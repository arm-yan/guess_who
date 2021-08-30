<?php

namespace App\Exceptions;

use Exception;

class UnavailableApiException extends Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}
