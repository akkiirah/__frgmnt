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
use Frgmnt\Model\ContentElement;

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

    public function fetchBySlugPath(string $path): ?Page
    {
        // 1) Path trimmen und zerlegen
        $cleanPath = trim($path, '/');
        $slugs = $cleanPath === '' ? [] : explode('/', $cleanPath);

        // 2) Root-Page (home) holen: das einzige Record mit parent_id IS NULL
        $stmt = Database::getConnection()
            ->query('SELECT * FROM pages WHERE parent_id IS NULL LIMIT 1');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, Page::class);
        $root = $stmt->fetch();
        if (!$root) {
            return null; // keine Root-Page definiert
        }

        // 3) Wenn erstes Segment gleich dem Root-Slug ist, überspringen
        if (isset($slugs[0]) && $slugs[0] === $root->getSlug()) {
            array_shift($slugs);
        }

        // 4) Wenn danach kein Segment mehr übrig ist, geben wir die Root-Page zurück
        if (count($slugs) === 0) {
            // Content-Elemente der Root laden, falls nötig
            $this->loadContentElements($root);
            return $root;
        }

        // 5) Nun rekursiv durch die Slugs gehen, immer innerhalb des aktuellen parent_id
        $parentId = $root->getId();
        $page = null;
        $db = Database::getConnection();

        foreach ($slugs as $slug) {
            $sql = 'SELECT * FROM pages WHERE slug = ? AND parent_id = ?';
            $stmt = $db->prepare($sql);
            $stmt->execute([$slug, $parentId]);
            $stmt->setFetchMode(\PDO::FETCH_CLASS, Page::class);
            $page = $stmt->fetch();

            if (!$page) {
                return null; // Segment nicht gefunden → 404
            }
            $parentId = $page->getId();
        }

        // 6) Content-Elemente laden und zurückgeben
        $this->loadContentElements($page);
        return $page;
    }

    public function loadContentElements(Page $page): void
    {
        $stmt = Database::getConnection()
            ->prepare('SELECT * FROM content_elements WHERE page_id = ? ORDER BY position');
        $stmt->execute([$page->getId()]);

        $elements = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $element = new ContentElement();
            $element->fillFromArray($row);
            $elements[] = $element;
        }

        $page->setContentElements($elements);
    }

    public function fetchChildren(int $parentId): array
    {
        $stmt = Database::getConnection()
            ->prepare('SELECT * FROM pages WHERE parent_id = ? ORDER BY id');
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }
}
