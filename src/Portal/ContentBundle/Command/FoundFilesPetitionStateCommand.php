<?php

namespace Portal\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Portal\ProfileBundle\Entity\Department;

class FoundFilesPetitionStateCommand extends ContainerAwareCommand
{
    const SAVE_FILE_DIR = '/../web/uploads/profile/';
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;
    public $defaultPhotoReportTypeId = 16;
    public $io;
    public $instances;
    public $instanceCode;
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('found:files:petition_state')
            ->setDescription('Find files of petition states');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $kernel = $this->container->get('kernel');

        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->kernelDir = $this->container->get('kernel')->getRootDir();
        $this->projectDir = $this->container->get('kernel')->getProjectDir();
        $this->rootDir = str_replace('/app', '/', $this->kernelDir);

        $kernelContainer = $this->container;
        $petitionStateList = $kernelContainer->get('petition_state_manager')->findBy([], ['departmentId' => 'ASC']);

        foreach ($petitionStateList as $petitionState) {
            $this->exportXlsAction($petitionState);
        }
        $kernel->shutdown();
    }

    public function exportXlsAction($petitionState)
    {
        $attachments = $petitionState->getAttachments();
        $stateDepartment = $petitionState->getDepartmentId();
        if ($stateDepartment instanceof Department) {
            $stateDepartmentLabel = $stateDepartment->getName();
        } else {
            $stateDepartmentLabel = 'нет организации для данного уведомления';
        }
        
        $fileXls = $this->projectDir . '/profile_files.xls';
        $rowIndex = 1;
        $isNewFile = true;
        if (file_exists($fileXls)) {
            $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($fileXls);
            $highestRow = $phpExcelObject->setActiveSheetIndex(0)->getHighestRow();
            $rowIndex = $highestRow;
            $isNewFile = false;
        } else {
            $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();
        }
        $phpExcelObject->getProperties()
            ->setCategory("Not exists files");

        try {
            $cellIndex = 0;
            if (!empty($attachments)) {
                // make table head
                if ($isNewFile) {
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $rowIndex, 'Id прикрепленного файла');
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $rowIndex, 'Url прикрепленного файла');
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $rowIndex, 'Оригинальное имя файла');
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $rowIndex, 'Id уведомления, к которому прикреплен файл');
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $rowIndex, 'Внутренний номер обращения');
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $rowIndex, 'Организация отправившая уведомление');
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $rowIndex, 'Id обращения');
                }
                
                // write filenames to console
                $this->io->section('Not exists files for petition state '. $petitionState->getId());
                foreach ($attachments as $attachment) {
                    if (!file_exists($this->projectDir . '/web' . $attachment->getPreviewFileUrl())) {
                        $rowIndex++;
                        $this->io->text($attachment->getPreviewFileUrl());
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $rowIndex, $attachment->getId());
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $rowIndex, $attachment->getPreviewFileUrl() . ' (' . $this->projectDir . '/web' . $attachment->getPreviewFileUrl() . ')');
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $rowIndex, $attachment->getOriginalFileName());
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $rowIndex, $petitionState->getId());
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $rowIndex, $petitionState->getRegistrationNumber());
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $rowIndex, $stateDepartmentLabel);
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $rowIndex, $petitionState->getPetition()->getId());
                    }
                }
            }
            $phpExcelObject->getActiveSheet()->setTitle('Simple');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);
        } catch (\PHPExcel_Exception $e) {
            //
        }
        
        // create the writer
        $writer = $this->container->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $writer->save($fileXls);

    }
}
