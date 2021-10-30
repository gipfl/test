<?php

namespace gipfl\Test;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

trait AsyncTestUtils
{
    protected $loop;

    protected function loop()
    {
        if ($this->loop === null) {
            $this->loop = Factory::create();
        }

        return $this->loop;
    }

    protected function failAfterSeconds($seconds, LoopInterface $loop)
    {
        $loop->addTimer($seconds, function () use ($seconds) {
            throw new \RuntimeException("Timed out after $seconds seconds");
        });
    }

    protected function collectErrorsForNotices(&$errors)
    {
        \set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$errors) {
            if (\error_reporting() === 0) { // @-operator in use
                return false;
            }
            $errors[] = new \ErrorException($errstr, 0, $errno, $errfile, $errline);

            return false; // Always continue with normal error processing
        }, E_ALL | E_STRICT);

        \error_reporting(E_ALL | E_STRICT);
    }

    protected function throwEventualErrors(array $errors)
    {
        foreach ($errors as $error) {
            throw $error;
        }
    }
}
