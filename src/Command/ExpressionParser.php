<?php

namespace Silly\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Parses the expression that defines a command.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ExpressionParser
{
    public function parse($expression)
    {
        $tokens = explode(' ', $expression);
        $tokens = array_map('trim', $tokens);
        $tokens = array_values(array_filter($tokens));

        if (count($tokens) === 0) {
            throw new InvalidCommandExpression('The expression was empty');
        }

        $name = array_shift($tokens);

        $arguments = [];
        $options = [];

        foreach ($tokens as $token) {
            if ($this->isOption($token)) {
                $options[] = $this->parseOption($token);
            } else {
                $arguments[] = $this->parseArgument($token);
            }
        }

        return [
            'name' => $name,
            'arguments' => $arguments,
            'options' => $options,
        ];
    }

    private function isOption($token)
    {
        return $this->startsWith($token, '-') || $this->startsWith($token, '[-');
    }

    private function parseArgument($token)
    {
        if ($this->endsWith($token, '?')) {
            $mode = InputArgument::OPTIONAL;
            $name = rtrim($token, '?');
        } elseif ($this->endsWith($token, '*')) {
            $mode = InputArgument::IS_ARRAY;
            $name = rtrim($token, '*');
        } elseif ($this->endsWith($token, '+')) {
            $mode = InputArgument::IS_ARRAY | InputArgument::REQUIRED;
            $name = rtrim($token, '+');
        } else {
            $mode = InputArgument::REQUIRED;
            $name = $token;
        }

        return new InputArgument($name, $mode);
    }

    private function parseOption($token)
    {
        // It's an array if it looks like `[--iterations=]*`
        $isArrayValue = false;
        if ($this->startsWith($token, '[-') && $this->endsWith($token, ']*')) {
            $isArrayValue = true;
            $token = substr($token, 2, -2);
        }

        // Shortcut `-y--yell`
        if (strpos($token, '|') !== false) {
            list($shortcut, $token) = explode('|', $token, 2);
            $shortcut = ltrim($shortcut, '-');
        } else {
            $shortcut = null;
        }

        $name = ltrim($token, '-');

        if ($isArrayValue) {
            $mode = InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY;
            $name = rtrim($name, '=');
        } elseif ($this->endsWith($token, '=')) {
            $mode = InputOption::VALUE_REQUIRED;
            $name = rtrim($name, '=');
        } elseif ($this->endsWith($token, '[=]')) {
            $mode = InputOption::VALUE_OPTIONAL;
            $name = substr($name, 0, -3);
        } else {
            $mode = InputOption::VALUE_NONE;
        }

        return new InputOption($name, $shortcut, $mode);
    }

    private function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    private function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}
