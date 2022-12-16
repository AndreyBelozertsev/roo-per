<?php

namespace Portal\HelperBundle\Model;

/**
 * Class AbstractReportFile
 */
abstract class AbstractReportFile
{
    /**
     * @var bool
     */
    protected $processStatus = false;

    /**
     * Temp path
     *
     * @var string
     */
    protected $filePath;

    /**
     * Which file name we will get in download
     *
     * @var string
     */
    protected $fileName;

    /**
     * @return string|bool
     */
    public function getFilePath()
    {
        if($this->processStatus)
            return $this->filePath;

        return false;
    }

    /**
     * @return bool|string
     */
    public function getFileName()
    {
        if($this->processStatus)
            return $this->fileName;

        return false;
    }

    protected function assignFile($fileName) { $this->fileName = $fileName; }

    public abstract function process();
}