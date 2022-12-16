<?php

namespace Portal\AdminBundle\Command;

use Portal\AdminBundle\Entity\Instance;
use Portal\ContentBundle\Entity\Attachment;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\VarDumper\VarDumper;

class FilesCheckerCommand extends ContainerAwareCommand
{
    public $conn;
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;
    public $input;
    public $io;


    public function __construct()
    {
//        if (!extension_loaded('oci8')) {
//            echo 'OCI8 Extension not enabled! EXIT'."\r\n";
//        }else{
//            $conn = oci_connect('prav', 'N5c4bm52', '10.150.24.131/XE', 'AL32UTF8');
//            if (!$conn) {
//                $e = oci_error();
//                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
//            }else{
//                $this->conn = $conn;
//            }
//        }

        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('fs:check')
            ->setDescription('Check empty origin_file_url for migrated files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->kernelDir = $this->container->get('kernel')->getRootDir();
        $this->rootDir = str_replace('/app', '/', $this->kernelDir);
        $this->webDir = $this->rootDir.'web';

        $startTime = time();
        $this->output->writeln('... check started at ' . date('d-m-Y H:i:s', time()));

        $this->checkFiles();

        $endTime = time();
        $workTime = $endTime - $startTime;

        $hours = floor($workTime/3600);
        $minutes = floor(($workTime - $hours*3600)/60);
        $seconds = $workTime - $hours*3600 - $minutes*60;

        $this->io->writeln('... check started at ' . date('d-m-Y H:i:s', $startTime));
        $this->io->writeln('... check complete at ' . date('d-m-Y H:i:s', $endTime));
        $this->io->writeln('... total time ' . $hours . 'h ' . $minutes . 'min '.$seconds.'sec');
    }

    public function checkFiles()
    {
        $instances = $this->container->get('instance_manager')->findAll();

        foreach($instances as $instance) {

            $instanceCode = $instance->getCode();

            $this->io->success('# PARSING '.mb_strtoupper($instanceCode));

            if($instanceCode !== 'main') {
                $clearInstanceCode = explode('-', $instanceCode);
                $compiledKernelName = '';
                foreach($clearInstanceCode as $code) {
                    $compiledKernelName .= ucfirst($code);
                }
                $newKernel = $compiledKernelName."Kernel";

                require_once $this->kernelDir . "/sites/{$instanceCode}/{$newKernel}.php";

                $kernel = new $newKernel('dev', true);
                $kernel->boot();
            }else{
                $kernel = $this->container->get('kernel');
            }

            $kernelContainer = $kernel->getContainer();
            $kernelContainer->get('sync_manager')->cleanEmptyAttachments();

//            $allFiles = $kernelContainer->get('sync_manager')->getAllFiles();
            $allFilesWithHome = $kernelContainer->get('sync_manager')->getAllFilesWithHome();
            $attachments = $kernelContainer->get('sync_manager')->getCrashedAttachments();
            $sqlContent = [];
            $updateSQL = [];

//            foreach($allFiles as $file) {
//                $updateSQL[] = "UPDATE attachment SET preview = '".urldecode($file['preview'])."', original_file_name = '"
//                    .urldecode($file['original_file_name'])."' WHERE id = ".$file['id'];
//            }

            foreach($allFilesWithHome as $file) {
                $updateSQL[] = "UPDATE attachment SET preview_file_url = '".str_replace('web/', '',$file['preview_file_url'])
                    ."' WHERE id = ".$file['id'];
            }

            $this->writeDb($updateSQL, $kernelContainer);


            foreach($attachments as $attachment) {
                $filePath = false;
                if($attachment['type'] == 'document_attachment') {
                    $this->io->writeln('# find document file to reload. ID = '.$attachment['id']);
                    $filePath = $this->writeDocsToOrg($attachment['original_file_name'], $attachment['old_id']);
                    $this->io->writeln('# new file_path --> '.$filePath);
                    $this->io->writeln('');
                }elseif($attachment['type'] == 'article_attachment'){
                    $this->io->writeln('# find article file to reload. ID = '.$attachment['id']);
                    $filePath = $this->writeImageToNews($attachment['original_file_name'], $attachment['old_id']);
                    $this->io->writeln('# new file_path --> '.$filePath);
                    $this->io->writeln('');
                }else{}
                if($filePath) {
                    $sqlContent[] = "UPDATE attachment SET preview_file_url = '{$filePath}' WHERE id = '{$attachment['id']}'";
                }
            }
            $this->writeDb($sqlContent, $kernelContainer);

            $kernel->shutdown();
        }
    }

    public function writeDocsToOrg($doc, $old_id)
    {
        if($doc == '') {
            return '';
        }

        $path = [
            0 => '/file/pub/',
            1 => '/rus/file/pub/',
            2 => '/file/',
            3 => '/rus/file/'
        ];

        $docMod = urldecode(str_replace("'", "", $doc));
        foreach($path as $dir) {
            $fPath = $this->webDir.$dir.$docMod;
            $this->io->section('trying find file '.$docMod.' in path '.$fPath);
            if(file_exists($fPath)) {
                $fileUrl = $dir.$docMod;
                return $fileUrl;
            }
        }

        $this->io->section('... trying get file from Oracle...');
        $conn = $this->conn;
        $sql = "SELECT TEXT_FILE FROM PUB WHERE ID = ".$old_id;
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $remotePath = [
            0 => 'http://rk.gov.ru/file/pub/',
            1 => 'http://rk.gov.ru/rus/file/pub/',
            2 => 'http://rk.gov.ru/file/',
            3 => 'http://rk.gov.ru/rus/file/'
        ];

        while (($row = oci_fetch_assoc($stid)) != false) {
            foreach($remotePath as $dir) {
                $fPath = $dir.$row['TEXT_FILE'];
                $this->io->writeln('trying find file '.$row['TEXT_FILE'].' in path '.$fPath);
                try{
                    $fileContent = file_get_contents($fPath);
                    file_put_contents($this->webDir.'/file/pub/'.$docMod,$fileContent);
                    return '/file/pub/'.$docMod;
                }catch(\Exception $e) {

                }
            }
        }
        oci_free_statement($stid);

        $this->io->section('... trying get file from Oracle STRUCTURE...');
        $sql = "SELECT REDIRECT_TO FROM STRUCTURE WHERE ID = ".$old_id;
        $bid = oci_parse($conn, $sql);
        oci_execute($bid);

        while (($row = oci_fetch_assoc($bid)) != false) {
            $fPathParts = explode('/', $row["REDIRECT_TO"]);
            unset($fPathParts[0]);
            unset($fPathParts[1]);
            unset($fPathParts[2]);

            $fPath = implode('/', $fPathParts);
            $this->io->writeln(' redirect path ... '.$fPath);
            if(file_exists($this->webDir.'/'.$fPath)) {
                $fileContent = file_get_contents($this->webDir.'/'.$fPath);
                file_put_contents($this->webDir.'/file/pub/'.$docMod,$fileContent);
                return '/file/pub/'.$docMod;
            }
        }

        oci_free_statement($bid);
        oci_close($conn);

        return '';
    }

    public function writeImageToNews($img, $old_id)
    {
        if($img == '') {
            return '';
        }

        $path = [
            0 => '/file/',
            1 => '/file/news/',
            2 => '/rus/file/',
            3 => '/rus/file/news/'
        ];

        foreach($path as $dir) {
            $filePath = $this->webDir. $dir . urldecode($img);
            $this->io->section('trying find file '.$img.' in path '.$filePath);
            if(file_exists($filePath)){
                return $dir . urldecode($img);
            }
        }

        $this->io->section('... trying get file from Oracle...');
        $conn = $this->conn;
        $sql = "SELECT IMAGE_FILE FROM NEWS WHERE ID = ".$old_id;
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $remotePath = [
            0 => 'http://rk.gov.ru/file/news/',
            1 => 'http://rk.gov.ru/rus/file/news/',
            2 => 'http://rk.gov.ru/file/',
            3 => 'http://rk.gov.ru/rus/file/'
        ];

        while (($row = oci_fetch_assoc($stid)) != false) {
            $oracleImageParts = explode('.', $row['IMAGE_FILE']);
            if(!empty($oracleImageParts[1])) {
                $oracleImg = $row['IMAGE_FILE'];
                foreach($remotePath as $dir) {
                    $fPath = $dir.$oracleImg;
                    $this->io->section('trying find file '.$oracleImg.' in path '.$fPath);
                    try{
                        $fileContent = file_get_contents($fPath);
                        file_put_contents($this->webDir.'/file/news/'.$img, $fileContent);
                        return '/file/news/'.$img;
                    }catch(\Exception $e){}
                }
            }


        }


        return '';
    }

    public function writeImageToPhotoReports($img, $dir, $old_id, $code)
    {
        if($img == '') {
            return '';
        }

        if(file_exists($this->webDir.'/file/' . $img)){
            return '/file/' . $img;
        }

        if(file_exists($this->webDir.'/file/photoreport/' . $img)) {
            return '/file/photoreport/' . $img;
        }

        return '';
    }

    public function writeVideo($file)
    {
        if($file == '') {
            return '';
        }

        if(file_exists($this->webDir.'/file/' . $file)){
            return '/file/' . $file;
        }

        if(file_exists($this->webDir.'/file/video/' . $file)) {
            return '/file/video/' . $file;
        }

        return '';
    }

    public function writeDb($sqlContent, $kernelContainer)
    {
        $conn = $kernelContainer->get('doctrine.orm.customer_entity_manager')->getConnection();

        foreach($sqlContent as $query) {
            if($query !== '') {
                $stmt = $conn->prepare($query);
                $stmt->execute();
            }
        }

        $conn->close();
    }

}
