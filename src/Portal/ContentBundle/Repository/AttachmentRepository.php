<?php

namespace Portal\ContentBundle\Repository;
use Portal\ContentBundle\Entity\DocumentAttachment;

/**
 * AttachmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttachmentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAttachmentsByIds(array $attIds)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('att')
            ->from($this->getEntityName(), 'att')
            ->where('att.id IN (:attIds)')
            ->setParameter('attIds', $attIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get attachments exclude document
     *
     * @param $params
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllAttachments($params)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql =
            "SELECT id, preview_file_url, preview, original_file_name, original_file_name,
                file_type, file_description_uk, file_description_ru, file_description_en, file_size, file_update_at
             FROM attachment 
             WHERE
              type != '".DocumentAttachment::TABLE_NAME."'
              {extension}
              {type}
             {sort} LIMIT :limit OFFSET :offset
            ";
        $transGroup = [
            '{sort}' => $params['sort'] == 'asc'? 'ORDER BY file_update_at' : 'ORDER BY file_update_at DESC',
            '{extension}' => !empty($params['extension']) ? "AND file_type = '".$params['extension']."'" : '',
            '{type}' => !empty($params['type']) ? "AND file_type IN (".$params['type'].")" : ''
        ];
        $sql = strtr($sql, $transGroup);

        $statement = $dc->prepare($sql);
        $statement->execute(array(':limit' => $params['limit'],
            ':offset' => $params['offset'],

        ));

        return $statement->fetchAll();
    }

    /**
     * Get attachments of document
     *
     * @param $params
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDocumetnAttachments($params)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql =
            " SELECT id, preview_file_url, preview, original_file_name, original_file_name, "
            ." file_type, file_description_uk, file_description_ru, file_description_en, file_size, file_update_at "
            ." FROM attachment "
            ." WHERE "
            ."  type = '".DocumentAttachment::TABLE_NAME."' "
            ."  {extension} "
            ."  {type} "
            ."  {sort} LIMIT :limit OFFSET :offset ";

        $transGroup = [
            '{sort}' => $params['sort'] == 'asc'? 'ORDER BY file_update_at' : 'ORDER BY file_update_at DESC',
            '{extension}' => !empty($params['extension']) ? "AND file_type = '".$params['extension']."'" : '',
            '{type}' => !empty($params['type']) ? "AND file_type IN (".$params['type'].")" : ''
        ];
        $sql = strtr($sql, $transGroup);

        $statement = $dc->prepare($sql);
        $statement->execute(array(':limit' => $params['limit'],
            ':offset' => $params['offset'],

        ));

        return $statement->fetchAll();
    }

    /**
     * Count files exclude documents files
     *
     * @param array
     * @return array
     */
    public function getCountFiles($params)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql =
            "SELECT COUNT(id)
             FROM attachment 
             WHERE
              type != '". DocumentAttachment::TABLE_NAME. "'
              {extension}
              {extension_type}
            ";

        $transGroup = [
            '{extension}' => isset($params['extension']) ? "AND file_type = '".$params['extension']."'" : '',
            '{extension_type}' => isset($params['type']) ? "AND file_type IN (".$params['type'].")" : ''
        ];
        $sql = strtr($sql, $transGroup);

        $statement = $dc->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();

        return $result['count'];
    }

    /**
     * Count files of documents
     *
     * @param array
     * @return array
     */
    public function getCountDocumentFiles($params=[])
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql =
            " SELECT COUNT(id) "
            ." FROM attachment "
            ." WHERE "
            ."  type = '". DocumentAttachment::TABLE_NAME. "' "
            ."  {extension} "
            ."  {extension_type} ";

        $transGroup = [
            '{extension}' => isset($params['extension']) ? "AND file_type = '".$params['extension']."'" : '',
            '{extension_type}' => isset($params['type']) ? "AND file_type IN (".$params['type'].")" : ''
        ];
        $sql = strtr($sql, $transGroup);

        $statement = $dc->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();

        return $result['count'];
    }
    /**
     * Delete filesgetDocumetnAttachments
     * @param array
     * @return integer
     */
    public function deleteFilesByIds($ids)
    {
        if(is_array($ids)) {
            $strId = implode(',', $ids);
        }

        $dc = $this->getEntityManager()->getConnection();
        $sql = "DELETE FROM attachment WHERE id IN ($strId)";
        $statement = $dc->prepare($sql);
        $statement->execute();

        return $statement->rowCount();
    }
}
