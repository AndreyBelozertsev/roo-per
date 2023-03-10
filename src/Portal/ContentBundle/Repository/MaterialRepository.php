<?php

namespace Portal\ContentBundle\Repository;

/**
 * MaterialRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MaterialRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        $sql = 'SELECT id, title, author_id
                FROM material
                ORDER BY created_at DESC'
        ;

        return $this->getEntityManager()->getConnection()->fetchAll($sql) ?: [];
    }
}
