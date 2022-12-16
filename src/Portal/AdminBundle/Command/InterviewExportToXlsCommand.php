<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InterviewExportToXlsCommand extends ContainerAwareCommand
{
    const SAVE_FILE_DIR = '/../web/uploads/interviews/';
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;
    public $defaultPhotoReportTypeId = 16;
    public $io;
    public $instances;
    public $instanceCode;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('interview:export-to-xls:all')
            ->setDescription('Export interview data to xls file');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $kernel = $this->container->get('kernel');

        $this->instances = $this->container->get('instance_manager')->findAll();
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->kernelDir = $this->container->get('kernel')->getRootDir();
        $this->rootDir = str_replace('/app', '/', $this->kernelDir);

        foreach($this->instances as $instance) {
            $this->instanceCode = $instance->getCode();
            //$instanceCode = $instance->getCode();
            $this->io->section('Interviews for instance '. $this->instanceCode);
            if($this->instanceCode !== 'main') {
               $clearInstanceCode = explode('-', $this->instanceCode);
                $compiledKernelName = '';
                foreach($clearInstanceCode as $code) {
                    $compiledKernelName .= ucfirst($code);
                }
                $newKernel = $compiledKernelName."Kernel";

                require_once $this->kernelDir . "/sites/{$this->instanceCode}/{$newKernel}.php";

                $kernel = new $newKernel('dev', true);
                $kernel->boot();
                $kernelContainer = $kernel->getContainer();
            }else{
                $kernel = $this->container->get('kernel');
                $kernelContainer = $this->container;
            }

            $interviewList = $kernelContainer->get('customer_interview_manager')->findAll();

            foreach ($interviewList as $interview) {
                $this->exportXlsAction($interview->getId(), $kernelContainer);
            }
            $kernel->shutdown();
        }
    }
    public function exportXlsAction($id, $container)
    {
        $interview = $container->get('customer_interview_manager')->findOneById($id);
        $dirName = $this->kernelDir . self::SAVE_FILE_DIR . $this->instanceCode ;
        $file = $dirName . '/' . $interview->getSlug() . '.data';
        $fileXls = $dirName . '/' . $interview->getSlug() . '.xls';
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        if (file_exists($file)) {
            $lastId = (int)file_get_contents($file);
        } else {
            $createFile = fopen($file, "w");
            $lastId = 0;
            fwrite($createFile, $lastId);
            fclose($createFile);
        }
        $container->get('customer_interview_user_answer_manager')->updateDraftUserAnswer($id);
        $questionList = $container->get('customer_interview_question_manager')->getInterviewQuestionList($id);
        $votedList = $container->get('customer_interview_user_answer_manager')->getInterviewVotedList($id, $lastId);
        $rowIndex = 1;
        $isNewFile = true;
        if (file_exists($fileXls)) {
            $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($fileXls);
            $highestRow = $phpExcelObject->setActiveSheetIndex(0)->getHighestRow();
            if ($lastId > 0) {
                $rowIndex = $highestRow;
                $isNewFile = false;
            }
        } else {
            $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();
        }
        $phpExcelObject->getProperties()
            ->setCategory("Interview result file");

        try {
            $cellIndex = 0;
            if (!empty($votedList)) {
                // make table head
                if ($isNewFile) {
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(
                        $cellIndex,
                        $rowIndex,
                        $container->get('translator')->trans('interview_form.label_cell_date')
                    );
                }
                $questionIds[] = 0;
                foreach ($questionList as $question) {
                    $cellIndex++;
                    if ($isNewFile) {
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cellIndex, $rowIndex, $question['content']);
                    }
                    $questionIds[] = $question['id'];
                }
                // make table body
                foreach ($votedList as $voted) {
                    $answerList = $container->get('customer_interview_user_answer_manager')->getVotedAnswerList($voted['unique_id']);
                    $rowIndex++;
                    $cellIndex = 0;
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cellIndex, $rowIndex, $answerList[0]['date_pass']);
                    $cellIndex++;
                    for ($i = 0, $ansCount = count($answerList); $i < $ansCount; $i++) {
                        if ($questionIds[$cellIndex] == $answerList[$i]['interview_question_id']) {
                            $text = $answerList[$i]['answer'];
                        } else {
                            $text = '';
                            $i--;
                        }
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow($cellIndex, $rowIndex, $text);
                        $cellIndex++;
                    }
                    if ((int)$voted['max_id'] > (int)$lastId) {
                        $lastId = $voted['max_id'];
                        file_put_contents($file, $voted['max_id']);
                    }
                }
            } else {
                if ($isNewFile) {
                    $phpExcelObject->setActiveSheetIndex(0)->setCellValueByColumnAndRow(
                        $cellIndex,
                        $rowIndex,
                        $container->get('translator')->trans('interview_form.no_result')
                    );
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
        $this->io->section('Interviews for instance '. $this->instanceCode);

    }
}
