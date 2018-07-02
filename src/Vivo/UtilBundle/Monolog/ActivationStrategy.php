<?php

namespace Vivo\UtilBundle\Monolog;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Monolog\Logger;

class ActivationStrategy extends ErrorLevelActivationStrategy
{
    public function __construct($actionLevel = Logger::CRITICAL)
    {
        parent::__construct($actionLevel);
    }

    public function isHandlerActivated(array $record)
    {
        if (isset($record['context']['exception'])) {
            $exception = $record['context']['exception'];
            if ($exception instanceof HttpException) {
                if (503 === $exception->getStatusCode()) {
                    return false;
                }
            }
        }

        return parent::isHandlerActivated($record);
    }
}
