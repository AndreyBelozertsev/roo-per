<?php

namespace Portal\ContentBundle\Repository;

use Portal\ContentBundle\Entity\VideoReport;

/**
 * VideoReportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VideoReportRepository extends \Doctrine\ORM\EntityRepository
{
    public function getVideoReportList($limit, $offset)
    {
        $_LIMIT = $limit ? ' LIMIT ' . $limit . ' OFFSET ' . $offset : '';

        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT vr.id AS video_report_id, vr.title AS video_report_title'
            . ', vr.description AS video_report_description, vr.published_at AS video_report_published_at'
            . ', vr.views_counter'
            . ', ('
                . ' SELECT preview_file_url'
                . ' FROM attachment, video_report_attachment'
                . ' WHERE video_report_attachment.video_report_id = vr.id AND video_report_attachment.id = attachment.id LIMIT 1'
            . ' ) AS video_report_file_url'
            . ', ('
                . ' SELECT file_type'
                . ' FROM attachment, video_report_attachment'
                . ' WHERE video_report_attachment.video_report_id = vr.id AND video_report_attachment.id = attachment.id LIMIT 1'
            . ' ) AS video_report_file_type'
            . ' FROM video_report AS vr'
            . ' WHERE vr.is_published IS true'
            . ' ORDER BY vr.published_at DESC'
            . $_LIMIT
        ;

        return $dc->executeQuery($sql)->fetchAll() ?: false;
    }

    public function getStructureVideoReportList($stuctureId, $offset)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT vr.id AS video_report_id, vr.title AS video_report_title'
            . ', vr.description AS video_report_description, vr.published_at AS video_report_published_at'
            . ', ('
                . ' SELECT preview_file_url'
                . ' FROM attachment, video_report_attachment'
                . ' WHERE video_report_attachment.video_report_id = vr.id AND video_report_attachment.id = attachment.id LIMIT 1'
            . ' ) AS video_report_file_url'
            . ', ('
                . ' SELECT file_type'
                . ' FROM attachment, video_report_attachment'
                . ' WHERE video_report_attachment.video_report_id = vr.id AND video_report_attachment.id = attachment.id LIMIT 1'
            . ' ) AS video_report_file_type'
            . ' FROM video_report AS vr'
            . ' WHERE vr.is_published IS true'
            . ' AND vr.menu_node_id = ' . $stuctureId
            . ' ORDER BY vr.published_at DESC'
            . ' LIMIT ' . VideoReport::VIDEO_REPORT_LIMIT_ON_PAGE . ' OFFSET ' . (int)$offset
        ;

        return $dc->executeQuery($sql)->fetchAll() ?: false;
    }

    public function getVideoReportListCount()
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT count(*)'
            . ' FROM video_report'
            . ' WHERE is_published IS true'
        ;

        return $dc->executeQuery($sql)->fetchColumn() ?: false;
    }

    public function getStructureVideoReportListCount($structureId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT count(*) '
            . ' FROM video_report '
            . ' WHERE is_published IS true '
            . ' AND menu_node_id = ' . $structureId
        ;
        $result = $dc->executeQuery($sql)->fetchColumn();

        return $result ?: 0;
    }
}
