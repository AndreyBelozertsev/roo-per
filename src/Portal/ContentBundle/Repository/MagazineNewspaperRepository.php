<?php

namespace Portal\ContentBundle\Repository;

use Doctrine\DBAL\DBALException;
use Portal\ContentBundle\Entity\MagazineNewspaper;

/**
 * MagazineNewspaperRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MagazineNewspaperRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param int $page
     * @param string $type
     * @return array
     */
    public function getPaginatedList(int $page, string $type)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT m_n.id, attachment.preview_file_url,
                    m_n.title_uk,  m_n.title_ru, m_n.title_en,
                    m_n.created_at, document_attachment.preview_file_url as file
                FROM magazine_newspaper AS m_n
                INNER JOIN magazine_newspaper_attachment m_n_a ON m_n_a.magazine_newspaper_id = m_n.id
                INNER JOIN attachment ON attachment.id = m_n_a.id
                INNER JOIN magazine_newspaper_document_attachment m_n_d_a ON m_n_d_a.magazine_newspaper_id = m_n.id
                INNER JOIN attachment document_attachment ON document_attachment.id = m_n_d_a.id
                WHERE m_n.type_of = :type AND m_n.is_deleted IS FALSE AND m_n.is_published IS TRUE
                ORDER BY m_n.created_at DESC
                LIMIT ' . MagazineNewspaper::PAGE_PAGINATION_LIMIT . '
                OFFSET ' . (int)$page * MagazineNewspaper::PAGE_PAGINATION_LIMIT . '
        ';

        return $dc->fetchAll($sql,['type' => $type]) ?: [];
    }


    /**
     * @param string $type
     * @return int|mixed
     */
    public function getCount(string $type )
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT count(*)
                FROM magazine_newspaper AS m_n
                WHERE m_n.type_of = :type AND m_n.is_deleted IS FALSE AND m_n.is_published IS TRUE
        ';
        try {
            $count = $dc->executeQuery($sql,['type' => $type])->fetch(\PDO::FETCH_COLUMN);
        } catch (DBALException $e) {
        }

        return $count ?? false;
    }
    
    public function getLastMagazine()
    {
        /**
         * @return array
         */
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT m_n.id, att.preview_file_url,
                    m_n.title_uk,  m_n.title_ru, m_n.title_en,
                    m_n.created_at, doc.preview_file_url as file
                FROM magazine_newspaper AS m_n
                    INNER JOIN magazine_newspaper_attachment AS m_n_a ON m_n_a.magazine_newspaper_id = m_n.id
                    INNER JOIN attachment AS att ON att.id = m_n_a.id
                    INNER JOIN magazine_newspaper_document_attachment AS m_n_d_a ON m_n_d_a.magazine_newspaper_id = m_n.id
                    INNER JOIN attachment AS doc ON doc.id = m_n_d_a.id
                WHERE type_of = :type_of AND m_n.is_deleted IS FALSE AND m_n.is_published IS TRUE 
                ORDER BY m_n.created_at DESC
                LIMIT 1
        ';

        return $dc->fetchAll($sql,['type_of' => 'magazine'])[0] ?: null;
    }
}
