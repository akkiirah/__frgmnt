<?php

namespace Frgmnt\Model;

class Page
{
    public ?int $id = null;
    public ?int $parent_id = null;
    public string $title = '';
    public string $content = '';
    public string $created_at = '';
    public string $updated_at = '';
}
