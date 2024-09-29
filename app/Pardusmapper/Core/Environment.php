<?php
declare(strict_types=1);

namespace Pardusmapper\Core;


use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

class Environment
{
    public static function load(): void
    {
        try {
            $env = Dotenv::createImmutable(base_path());

            $env->load();
        } catch (InvalidPathException) {
            throw_when(true, sprintf('Failed to load .env file from: %s.env', base_path()));
        }
    }
}