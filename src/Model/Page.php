<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Model;

class Page
{
    protected ?int $id = null;
    protected ?int $parent_id = null;
    protected string $title = '';
    protected string $slug = '';
    protected array $content_elements = [];
    protected string $created_at = '';
    protected string $updated_at = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getParent_id(): ?int
    {
        return $this->parent_id;
    }

    public function setParent_id($parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getContentElements(): array
    {
        return $this->content_elements;
    }

    public function setContentElements($content_elements): void
    {
        $this->content_elements = $content_elements;
    }

    public function getCreated_at(): string
    {
        return $this->created_at;
    }

    public function setCreated_at($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdated_at(): string
    {
        return $this->updated_at;
    }

    public function setUpdated_at($updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
