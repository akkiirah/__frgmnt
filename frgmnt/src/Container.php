<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */

namespace Frgmnt;

class Container
{
    private array $factories = [];
    private array $instances = [];

    /**
     * Register a factory for key `$name`.
     */
    public function set(string $name, callable $factory): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * Get the singleton instance for key `$name`.
     */
    public function get(string $name)
    {
        if (!isset($this->instances[$name])) {
            if (!isset($this->factories[$name])) {
                throw new \RuntimeException("Service '$name' not registered");
            }
            $this->instances[$name] = ($this->factories[$name])();
        }
        return $this->instances[$name];
    }
}