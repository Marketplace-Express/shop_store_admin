<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/30
 * Time: 13:57
 */

namespace App\Exceptions;


use Throwable;

class OperationNotPermitted extends \Exception
{
    public function __construct($message = "Operation not permitted", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}