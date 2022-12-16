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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper;

class HeaderMigrateCommand extends ContainerAwareCommand
{
    private $conn;
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;

    public $pravitelstvo = [
        9942 => 'glava',
    ];

    public $ministerstva = [
        9441 => 'minek',
        9444 => 'minfin',
        9445 => 'mtrud',
        9446 => 'msh',
        9447 => 'mkult',
        9448 => 'mtur',
        9449 => 'minfo',
        9450 => 'mzhkh',
        9451 => 'monm',
        9503 => 'mchs',
        9504 => 'mzem',
        9505 => 'must',
        9452 => 'msport',
        9453 => 'mzdrav',
        9454 => 'mprom',
        9455 => 'mtrans',
        9456 => 'mtop',
        9457 => 'mstroy',
        9459 => 'meco'
    ];

    public $goskomitet = [
        9521 => 'gkvet',
        10402 => 'gkdor',
        9463 => 'gkvod',
        9461 => 'gkreg',
        9460 => 'gkmn',
        9462 => 'gkokn',
        9523 => 'gkz'
    ];

    public $vedomstva = [
        9515 => 'gas',
        9513 => 'gizn',
        9512 => 'intsm',
        9511 => 'intrud',
        9517 => 'kkp',
        9516 => 'kpk',
        9509 => 'sgsn',
        9507 => 'sks',
        9722 => 'szfn',
        9508 => 'smpgo',
        9506 => 'set',
        9510 => 'sfn'
    ];

    public $municipals = [
        9776 => 'alushta',
        9781 => 'armyansk',
        10002 => 'bahchisaray',
        9765 => 'bahch',
        9962 => 'belogorsk',
        9767 => 'belogorskiy',
        9741 => 'dzhankoy',
        9783 => 'dzhankoyrn',
        9564 => 'evp',
        9621 => 'kerch',
        9782 => 'kirovskiy',
        9721 => 'krgv',
        9581 => 'krpero',
        9702 => 'krp',
        9764 => 'lenino',
        9565 => 'nijno',
        9602 => 'pervmo',
        9771 => 'razdolnoe',
        9662 => 'sakimo',
        9761 => 'saki',
        9768 => 'simf',
        9843 => 'simfmo',
        9603 => 'sovmo',
        9766 => 'sudakgs',
        9775 => 'feo',
        9661 => 'chero',
        9562 => 'yalta',
        10422 => 'skrym',
        10442 => 'shelkino',
    ];

    public $ombudsman = [
        9601 => 'ombudsman',
        9861 => 'ombudsmanbiz',
        9901 => 'deti',
    ];

    public $another = [
        10122 => 'mil',
        10424 => 'regulation',
    ];

    public $ispRK = [
        10342 => 'cor',
        9801 => 'mfc',
        10082 => 'rfkrmd',
        10062 => 'spas',
        10182 => 'gkupo',
    ];

    public $input;
    public $headerTemplateID;
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
            ->setName('header:migrate')
            ->setDescription('Migrates data from old portal')
            ->addOption('--type', null, InputOption::VALUE_REQUIRED,
                'Migration type of instance(minister, goskom, prav, municipal, vedomstva, ombudsman, another, ispRK');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (posix_getpwuid(posix_geteuid())['name'] !== 'www-data') {
            $output->writeln('This script should be run from the user \'www-data\' (sudo -u www-data ...)');
            exit;
        }
        $this->container = $this->getContainer();
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->kernelDir = $this->container->get('kernel')->getRootDir();
        $this->rootDir = str_replace('/app', '/', $this->kernelDir);
        $this->webDir = $this->rootDir.'web';

        $startTime = time();
        $this->output->writeln('------> Migration started at ' . date('d-m-Y H:i:s', time()));

        $this->migrateAllSQL();

        $endTime = time();
        $workTime = $endTime - $startTime;

        $hours = floor($workTime/3600);
        $minutes = floor(($workTime - $hours*3600)/60);
        $seconds = $workTime - $hours*3600 - $minutes*60;

        $this->io->success('------> Migration started at ' . date('d-m-Y H:i:s', $startTime));
        $this->io->success('------> Migration complete at ' . date('d-m-Y H:i:s', $endTime));
        $this->io->success('------> Total migration time ' . $hours . 'h ' . $minutes . 'min '.$seconds.'sec');
    }

    public function migrateAllSQL()
    {
        $total = [];
        if($this->input->getOption('type') == 'vedomstva' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->vedomstva;
        }

        if($this->input->getOption('type') == 'minister' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->ministerstva;
        }

        if($this->input->getOption('type') == 'prav' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->pravitelstvo;
        }

        if($this->input->getOption('type') == 'goskom' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->goskomitet;
        }

        if($this->input->getOption('type') == 'municipal' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->municipals;
        }

        if($this->input->getOption('type') == 'another' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->another;
        }

        if($this->input->getOption('type') == 'ombudsman' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->ombudsman;
        }

        if($this->input->getOption('type') == 'ispRK' || is_null($this->input->getOption('type'))) {
            $total = $total + $this->ispRK;
        }

        foreach($total as $key=>$value) {
            $this->io->section('Trying to override headers');
            $this->getHeaders($key, $value);

            $this->io->success('------> Migration complete for '.$value.' subdomain');
        }
    }

    public function getHeaders($key, $code)
    {
        $conn = $this->conn;

        $sqlContent = [];

        $clearInstanceCode = explode('-', $code);
        $compiledKernelName = '';
        foreach($clearInstanceCode as $code) {
            $compiledKernelName .= ucfirst($code);
        }
        $newKernel = $compiledKernelName."Kernel";

        require_once $this->kernelDir . "/sites/{$code}/{$newKernel}.php";

        $kernel = new $newKernel('dev', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->headerTemplateID = $container->get('customer_structure_template_manager')->findOneBy(['code' => 'head'])->getId();

        $sql = "SELECT ID, ORIG_ID, PARENT_ID FROM STRUCTURE WHERE PARENT_ID IN (SELECT ID FROM STRUCTURE WHERE TYPE = 4 AND rownum = 1"
            . " START WITH ID = (SELECT STRUCTURE_PAGE_ID FROM ORGANIZATION WHERE ID = ".$key.")"
            . " CONNECT BY PRIOR ID = PARENT_ID) AND TYPE = 2";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $headerNewID = false;

        while (($row = oci_fetch_assoc($stid)) != false) {
            if(!$headerNewID){
                $headerNewID = $container->get('sync_manager')->getHeaderMenuNodeId($row['PARENT_ID']);
                $sqlContent[] = "UPDATE menu_node SET structure_template_id = ".$this->headerTemplateID." WHERE id = ".$headerNewID;
            }

            $innerSql = "SELECT p.FIO, p.PICTURE, p.BIOGRAPHY, po.NAME, pd.ADDRESS, pd.PHONE, pd.EMAIL FROM 
PERSON_DEPARTMENT pd LEFT JOIN POST po ON po.ID = pd.POST_ID LEFT JOIN PERSON p ON p.ID = pd.PERSON_ID WHERE pd.ID = " . $row['ORIG_ID'];
            $personResults = oci_parse($conn, $innerSql);
            oci_execute($personResults);
            $innerHTML = '';
            while (($tbl = oci_fetch_assoc($personResults)) != false) {
                $this->io->writeln('... processing '.$tbl['FIO']);
                $picture = '';
                if (!empty($tbl['PICTURE'])) {
                    $picture = $this->loadEditorPictures($tbl['PICTURE'], $row['ID'], $code);
                }
                if (!is_null($tbl['BIOGRAPHY']) && $tbl['BIOGRAPHY']->size()) {
                    $innerHTML = $this->cleanString($tbl['BIOGRAPHY']->read($tbl['BIOGRAPHY']->size()));
                }

                $contacts = '';
                if(!empty($tbl['ADDRESS'])) {
                    $contacts .= '<p>Адрес: '.str_replace('<br>', ' ',$tbl['ADDRESS']).'</p>';
                }
                if(!empty($tbl['PHONE'])) {
                    $contacts .= '<p>Телефон: '.strip_tags(str_replace('<br>', ', ',$tbl['PHONE']), '<a>').'</p>';
                }
                if(!empty($tbl['EMAIL'])) {
                    $contacts .= '<p>E-mail: '.$tbl['EMAIL'].'</p>';
                }

                $fio = explode(' ', $tbl['FIO']);

//                $sqlContent[] = "DELETE FROM attachment WHERE type='head_attachment'";
                $sqlContent[] = "DELETE FROM head WHERE old_id = {$row['ID']}";
//                $sqlContent[] = "TRUNCATE head";
                $sqlContent[] = "INSERT INTO head(old_id, slug, lastname, firstname, middlename, position, menu_node_id, contact_information, biography, author_id)".
                    "VALUES({$row['ID']},'', '{$fio[0]}', '{$fio[1]}', '{$fio[2]}', '{$tbl['NAME']}', {$headerNewID}, '{$contacts}', '{$innerHTML}', 1)";
                $sqlContent[] = "INSERT INTO attachment(type, old_id, original_file_name, preview, preview_file_url)"
                    ." VALUES('head_attachment', {$row['ID']}, '{$tbl['PICTURE']}', '{$tbl['PICTURE']}', '{$picture}')";
                $sqlContent[] = "DELETE FROM menu_node WHERE old_id = ".$row['ID']." AND parent_id = ".$headerNewID;
                $sqlContent[] = "UPDATE menu_node SET before_text = '' WHERE id = ".$headerNewID;
                $sqlContent[] = "DELETE FROM menu_node WHERE parent_id = ".$headerNewID;

            }

            oci_free_statement($personResults);
        }

        $sqlContent[] = "CREATE UNIQUE INDEX IF NOT EXISTS uniq_head_old ON head(old_id)";

        oci_free_statement($stid);
        oci_close($conn);
        $this->writeToDb($container, $sqlContent);
        $this->updateAttach($container);
        $this->io->success('... headers collected');
        $kernel->shutdown();
    }

    public function loadEditorPictures($file, $old_id, $code)
    {
        if($file == '') {
            return '';
        }

        $file = urldecode($file);

        if(file_exists($this->webDir.'/file/'.$file)) {
            return '/file/'.$file;
        }

        if(file_exists($this->webDir.'/rus/file/'.$file)) {
            return '/rus/file/'.$file;
        }

        return '';
    }

    public function cleanString($string)
    {
        $str = trim(strip_tags($string, '<a><img><br><table><tr><td><th><thead><tbody><p><div><ul><li><ol><strong><i><h1><h2><h3><h4><h5><h6><center>'));
        $str = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $str)));
        $str = preg_replace("/(style=\".+?\"|onclick=\".+?\"|id=\".+?\"|class=\".+?\"|align=\".+?\")/","", $str);
        $str = str_replace("'", '"', $str);
        return $str;
    }

    public function writeToDb($container, $sql = [])
    {
        $conn = $container->get('doctrine.orm.customer_entity_manager')->getConnection();
        $q = "DELETE FROM attachment WHERE type='head_attachment'";
        $conn->exec($q);
        $q = "TRUNCATE head CASCADE; ALTER TABLE head ALTER COLUMN id SET DEFAULT nextval('head_id_seq'); ALTER TABLE head ADD COLUMN IF NOT EXISTS old_id INTEGER DEFAULT NULL; ALTER TABLE head ALTER COLUMN education DROP NOT NULL; ALTER TABLE head ALTER COLUMN created_at DROP NOT NULL;
ALTER TABLE head ALTER COLUMN created_at SET DEFAULT NULL::timestamp without time zone;";
        $conn->exec($q);

        foreach($sql as $query) {
            if($query !== '') {
                $stmt = $conn->prepare($query);
                $stmt->execute();
            }
        }

        $conn->close();
    }

    public function updateAttach($container)
    {
        $conn = $container->get('doctrine.orm.customer_entity_manager')->getConnection();
        $q = "INSERT INTO head_attachment(id, head_id) "
            ." SELECT a.id,  h.id FROM head h LEFT JOIN attachment a ON a.old_id = h.old_id AND a.type='head_attachment'"
            ." WHERE h.old_id IS NOT NULL ON CONFLICT(head_id) DO NOTHING";
        $conn->exec($q);

        $conn->close();
    }

}
