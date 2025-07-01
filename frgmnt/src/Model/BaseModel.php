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

namespace Frgmnt\Model;

/**
 * BaseModel provides shared functionality for all models.
 *
 * - Automatic hydration from associative arrays (snake_case to camelCase)
 * - Common properties: id, createdAt, updatedAt
 */
abstract class BaseModel
{
    protected ?int $id = null;
    protected string $createdAt = '';
    protected string $updatedAt = '';

    /**
     * Populate this model from a DB row (associative array).
     * Converts snake_case keys into camelCase properties.
     *
     * @param array $data Associative array of column values
     * @return void
     */
    public function fillFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $prop = $this->snakeToCamel($key);
            if (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
    }

    /**
     * Convert snake_case string to camelCase property name.
     *
     * @param string $string
     * @return string
     */
    protected function snakeToCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($string)))));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
