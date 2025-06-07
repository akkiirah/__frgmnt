<?php

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
        return $stmt->fetch() ?: null;
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
