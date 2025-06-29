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
    public ?int $id = null;
    public ?int $parent_id = null;
    public string $title = '';
    public string $content = '';
    public string $created_at = '';
    public string $updated_at = '';
}
