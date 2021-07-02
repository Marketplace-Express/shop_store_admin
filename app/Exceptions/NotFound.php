<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/30
 * Time: 13:50
 */

namespace App\Exceptions;


use Throwable;

class NotFound extends \Exception
{
    /**
     * NotFound constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Entity not found", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}