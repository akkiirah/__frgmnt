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


namespace Frgmnt\Repository;

use Frgmnt\Model\Page;
use Frgmnt\Model\ContentElement;
use PDO;

/**
 * PageRepository handles all database operations for Page entities,
 * including fetching, saving, and building hierarchical routes.
 */
class PageRepository
{
    private PDO $db;

    /**
     * @param PDO $db PDO database connection injected via the container
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all pages ordered by parent_id and id.
     *
     * @return Page[] Array of Page objects
     */
    public function fetchAll(): array
    {
        $rows = $this->db
            ->query('SELECT * FROM pages ORDER BY parent_id, id')
            ->fetchAll(PDO::FETCH_ASSOC);

        $pages = [];
        foreach ($rows as $row) {
            $page = new Page();
            $page->fillFromArray($row);
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     * Fetch a single page by its ID, including its content elements.
     *
     * @param int $id Page identifier
     * @return Page|null The Page object or null if not found
     */
    public function fetchById(int $id): ?Page
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $page = new Page();
        $page->fillFromArray($row);

        // Load content elements
        $stmt2 = $this->db->prepare(
            'SELECT * FROM content_elements WHERE page_id = ? ORDER BY position'
        );
        $stmt2->execute([$page->getId()]);
        $elements = [];
        foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $ce = new ContentElement();
            $ce->fillFromArray($r);
            $elements[] = $ce;
        }
        $page->setContentElements($elements);

        return $page;
    }

    /**
     * Fetch a page by a full slug path (e.g. 'about-us/team').
     * Automatically hydrates content elements.
     *
     * @param string $path Slash-separated slug path
     * @return Page|null The Page object or null if not found
     */
    public function fetchBySlugPath(string $path): ?Page
    {
        $cleanPath = trim($path, '/');
        $slugs = $cleanPath === '' ? [] : explode('/', $cleanPath);

        // Load the root page (parent_id IS NULL)
        $row = $this->db
            ->query('SELECT * FROM pages WHERE parent_id IS NULL LIMIT 1')
            ->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $root = new Page();
        $root->fillFromArray($row);

        // Skip the root slug if present
        if (isset($slugs[0]) && $slugs[0] === $root->getSlug()) {
            array_shift($slugs);
        }

        // If no more segments, return root
        if (count($slugs) === 0) {
            $this->loadContentElements($root);
            return $root;
        }

        // Traverse child slugs
        $parentId = $root->getId();
        $page = null;
        foreach ($slugs as $slug) {
            $stmt = $this->db
                ->prepare('SELECT * FROM pages WHERE slug = ? AND parent_id = ?');
            $stmt->execute([$slug, $parentId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }
            $page = new Page();
            $page->fillFromArray($row);
            $parentId = $page->getId();
        }

        $this->loadContentElements($page);
        return $page;
    }

    /**
     * Fetch all child pages of a given parent.
     *
     * @param int $parentId Parent page ID
     * @return Page[] Array of child pages
     */
    public function fetchChildren(int $parentId): array
    {
        $stmt = $this->db
            ->prepare('SELECT * FROM pages WHERE parent_id = ? ORDER BY id');
        $stmt->execute([$parentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $children = [];
        foreach ($rows as $row) {
            $p = new Page();
            $p->fillFromArray($row);
            $children[] = $p;
        }
        return $children;
    }

    /**
     * Load and attach content elements to a Page object.
     *
     * @param Page $page The Page to populate
     * @return void
     */
    public function loadContentElements(Page $page): void
    {
        $stmt = $this->db
            ->prepare('SELECT * FROM content_elements WHERE page_id = ? ORDER BY position');
        $stmt->execute([$page->getId()]);
        $elements = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ce = new ContentElement();
            $ce->fillFromArray($row);
            $elements[] = $ce;
        }
        $page->setContentElements($elements);
    }
}