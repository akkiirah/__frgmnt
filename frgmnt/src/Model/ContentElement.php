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
 * Represents a content element associated with a page.
 */
class ContentElement extends BaseModel
{
    protected ?int $pageId = null;
    protected ?int $position = null;
    protected ?string $internalName = null;
    protected ?string $type = null;
    protected ?string $headline = null;
    protected ?string $subheadline = null;
    protected ?string $body = null;
    protected ?string $imagePath = null;
    protected ?string $specialDataJson = null;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getInternalName()
    {
        return $this->internalName;
    }

    public function setInternalName($internalName): void
    {
        $this->internalName = $internalName;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition($position): void
    {
        $this->position = $position;
    }

    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    public function setPageId($pageId): void
    {
        $this->pageId = $pageId;
    }

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    public function setHeadline($headline): void
    {
        $this->headline = $headline;
    }

    public function getSubheadline(): ?string
    {
        return $this->subheadline;
    }

    public function setSubheadline($subheadline): void
    {
        $this->subheadline = $subheadline;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody($body): void
    {
        $this->body = $body;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath($imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function getSpecialDataJson(): ?array
    {
        if (!$this->specialDataJson) {
            return null;
        }

        $data = json_decode($this->specialDataJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('JSON decoding failed: ' . json_last_error_msg());
        }
        return $data;
    }

    public function setSpecialDataJson(array $data): void
    {
        $json = json_encode($data);
        if ($json === false) {
            throw new \RuntimeException('JSON encoding failed: ' . json_last_error_msg());
        }
        $this->specialDataJson = $json;
    }
}