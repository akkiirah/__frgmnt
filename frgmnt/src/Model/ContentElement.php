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

class ContentElement
{
    protected ?int $id = null;
    protected ?int $pageId = null;
    protected ?int $position = null;
    protected ?string $internalName = null;
    protected ?string $type = null;
    protected ?string $headline = null;
    protected ?string $subheadline = null;
    protected ?string $body = null;
    protected ?string $imagePath = null;
    protected ?string $specialDataJson = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of internalName
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Set the value of internalName
     *
     * @return  self
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * Get the value of position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @return  self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of pageId
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set the value of pageId
     *
     * @return  self
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of headline
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * Set the value of headline
     *
     * @return  self
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * Get the value of subheadline
     */
    public function getSubheadline()
    {
        return $this->subheadline;
    }

    /**
     * Set the value of subheadline
     *
     * @return  self
     */
    public function setSubheadline($subheadline)
    {
        $this->subheadline = $subheadline;

        return $this;
    }

    /**
     * Get the value of body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @return  self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of imagePath
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * Set the value of imagePath
     *
     * @return  self
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
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

    // Setter: nimmt Array entgegen, speichert intern als JSON-String
    public function setSpecialDataJson(array $data): void
    {
        $json = json_encode($data);
        if ($json === false) {
            throw new \RuntimeException('JSON encoding failed: ' . json_last_error_msg());
        }
        $this->specialDataJson = $json;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function fillFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $property = $this->snakeToCamel($key);
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    protected function snakeToCamel(string $string): string
    {
        $string = strtolower($string);
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }
}