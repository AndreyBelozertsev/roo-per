<?php
namespace Portal\AdminBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SyncManager
{

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;


    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function getArticle()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT string_agg(CAST(old_id as VARCHAR), ' ') as old FROM article WHERE old_id IS NOT NULL";
        $result = $dc->fetchAll($sql);
        $dc->close();

        return $result ? $result[0]['old'] : '';
    }

    public function getMenuNode()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT string_agg(CAST(old_id as VARCHAR), ' ') as old FROM menu_node WHERE old_id IS NOT NULL";
        $result = $dc->fetchAll($sql);
        $dc->close();

        return $result ? $result[0]['old'] : '';
    }

    public function getEvent()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT string_agg(CAST(old_id as VARCHAR), ' ') as old FROM event WHERE old_id IS NOT NULL";
        $result = $dc->fetchAll($sql);
        $dc->close();

        return $result ? $result[0]['old'] : '';
    }

    public function getPhotoReport()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT string_agg(CAST(report_old_id as VARCHAR), ' ') as old FROM photo_report WHERE report_old_id IS NOT NULL";
        $result = $dc->fetchAll($sql);
        $dc->close();

        return $result ? $result[0]['old'] : '';
    }

    public function getVideoReport()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT string_agg(CAST(old_id as VARCHAR), ' ') as old FROM video_report WHERE old_id IS NOT NULL";
        $result = $dc->fetchAll($sql);
        $dc->close();

        return $result ? $result[0]['old'] : '';
    }

    public function getDocument()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT string_agg(CAST(old_id as VARCHAR), ' ') as old FROM document WHERE old_id IS NOT NULL";
        $result = $dc->fetchAll($sql);
        $dc->close();

        return $result ? $result[0]['old'] : '';
    }

    public function cleanEmptyAttachments()
    {
        $dc = $this->em->getConnection();
        $sql = "DELETE FROM attachment WHERE id IN(SELECT id FROM attachment WHERE preview_file_url = '' AND original_file_name = '')";
        $dc->exec($sql);
        $dc->close();
    }

    public function getCrashedAttachments()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT * FROM attachment WHERE preview_file_url = '' AND original_file_name != ''";
        $result = $dc->fetchAll($sql);
        return $result ? $result : [];
    }

    public function getAllFiles()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT id, preview, original_file_name, preview_file_url FROM attachment";
        $result = $dc->fetchAll($sql);
        return $result ? $result : [];
    }

    public function getAllFilesWithHome()
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT id, preview, original_file_name, preview_file_url FROM attachment WHERE preview_file_url LIKE '%web/%'";
        $result = $dc->fetchAll($sql);
        return $result ? $result : [];
    }

    public function getHeaderMenuNodeId($old_id)
    {
        $dc = $this->em->getConnection();
        $sql = "SELECT id FROM menu_node WHERE old_id = ".(int)$old_id;
        $result = $dc->fetchAll($sql);
        return $result ? $result[0]['id'] : false;
    }


}
