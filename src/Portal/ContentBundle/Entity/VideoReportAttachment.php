<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoReport
 *
 * @ORM\Table(name="video_report_attachment")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\VideoReportAttachmentRepository")
 */
class VideoReportAttachment extends Attachment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="VideoReport", inversedBy="attachment")
     * @ORM\JoinColumn(name="video_report_id", referencedColumnName="id")
     */
    private $videoReport;

    public function getId()
    {
        return parent::getId();
    }

    /**
     * @return VideoReport
     */
    public function getVideoReport()
    {
        return $this->videoReport;
    }

    /**
     * @param VideoReport $videoReport
     */
    public function setVideoReport($videoReport)
    {
        $this->videoReport = $videoReport;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title ?? '';
    }
}
