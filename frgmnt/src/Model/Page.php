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

class Page extends BaseModel
{
    protected ?int $parentId = null;
    protected string $title = '';
    protected string $slug = '';
    protected array $contentElements = [];
    protected array $children = [];

    public function getContentElements(): array
    {
        return $this->contentElements;
    }

    public function setContentElements($contentElements): void
    {
        $this->contentElements = $contentElements;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function clearChildren(): void
    {
        $this->children = [];
    }

    public function addChild(Page $child): void
    {
        $this->children[] = $child;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId($parentId): void
    {
        $this->parentId = $parentId;
    }
}
