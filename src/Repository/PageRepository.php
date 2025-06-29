<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Repository;

use Frgmnt\Service\Database;
use Frgmnt\Model\Page;

class PageRepository
{
    public function fetchAll(): array
    {
        $stmt = Database::getConnection()
            ->query('SELECT * FROM pages ORDER BY parent_id, id');
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function fetchById(int $id): ?Page
    {
        $stmt = Database::getConnection()
            ->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, Page::class);
        $page = $stmt->fetch() ?: null;

        if ($page) {
            $stmt = Database::getConnection()
                ->prepare('SELECT * FROM content_elements WHERE page_id = ? ORDER BY position');
            $stmt->execute([$page->id]);
            $page->content_elements = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $page;
    }


    public function save(Page $page): void
    {
        if ($page->id) {
            $sql = 'UPDATE pages SET title = ?, content = ?, parent_id = ? WHERE id = ?';
            Database::getConnection()->prepare($sql)
                ->execute([$page->title, $page->content, $page->parent_id, $page->id]);
        } else {
            $sql = 'INSERT INTO pages (title, content, parent_id) VALUES (?, ?, ?)';
            Database::getConnection()->prepare($sql)
                ->execute([$page->title, $page->content, $page->parent_id]);
            $page->id = (int) Database::getConnection()->lastInsertId();
        }
    }
}
