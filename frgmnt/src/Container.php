<?php
declare(strict_types=1);

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt;

/**
 * Simple Dependency Injection container storing factories and instances.
 */
class Container
{
    private array $factories = [];
    private array $instances = [];

    /**
     * Register a factory for a service.
     *
     * @param string   $name    Identifier key for the service
     * @param callable $factory Closure returning the service instance
     * @return void
     */
    public function set(string $name, callable $factory): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * Get a service instance, creating it via factory if needed.
     *
     * @param string $name Identifier key for the service
     * @return mixed
     * @throws \RuntimeException if service is not registered
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