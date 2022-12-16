<?php

namespace Portal\UserBundle\Entity\Repository;

/**
 * RoleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoleRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param array $params
     * @return array
     */
    public function getRoleList($params = [])
    {
        $queryBuilder = $this->createQueryBuilder('roleObj');
        $queryBuilder
            ->add('orderBy', 'roleObj.label ASC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}