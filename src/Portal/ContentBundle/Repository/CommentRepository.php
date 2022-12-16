<?php

namespace Portal\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CommentRepository
 * @package Portal\ContentBundle\Repository
 */
class CommentRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getComments()
    {
        $sql = 'SELECT
                   c.id, c.name, c.text, c.ispublished,
                   (CASE WHEN p.title_uk IS NULL THEN a.title_uk ELSE p.title_uk END) AS title
                FROM comment AS c
                   LEFT JOIN post AS p ON c.post_id = p.id
                   LEFT JOIN article a ON c.article_id = a.id
                ORDER BY c.id DESC'
        ;

        return $this->getEntityManager()->getConnection()->fetchAll($sql) ?: [];
    }
}
