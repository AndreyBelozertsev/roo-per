<?php //

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

class DataMigrationCommand extends ContainerAwareCommand
{
    private $conn;
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;
    public $migratable = array();
    public $defaultPhotoReportTypeId = 16;

    public $pravitelstvo = [
        9942 => 'glava',
        // 2236 => 'prav'
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
//        9776 => 'alushta',
//        9781 => 'armyansk',
//        10002 => 'bahchisaray',
//        9765 => 'bahch',
//        9962 => 'belogorsk',
//        9767 => 'belogorskiy',
//        9741 => 'dzhankoy',
//        9783 => 'dzhankoyrn',
//        9564 => 'evp',
//        9621 => 'kerch',
//        9782 => 'kirovskiy',
//        9721 => 'krgv',
//        9581 => 'krpero',
//        9702 => 'krp',
//        9764 => 'lenino',
//        9565 => 'nijno',
//        9602 => 'pervmo',
//        9771 => 'razdolnoe',
//        9662 => 'sakimo',
//        9761 => 'saki',
//        9768 => 'simf',
//        9843 => 'simfmo',
//        9603 => 'sovmo',
//        9766 => 'sudakgs',
//        9775 => 'feo',
//        9661 => 'chero',
//        9562 => 'yalta',
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

    public $structureID = 32;
    public $input;
    public $documentTemplateID;
    public $simpleTemplateID;
    public $newsTemplateID;
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
//
//        $this->migratable = $this->ministerstva;

        parent::__construct();

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('data:migrate')
            ->setDescription('Migrates data from old portal')
            ->addOption('--cl', null, InputOption::VALUE_NONE, 'If need clean all prev sync data')
            ->addOption('--type', null, InputOption::VALUE_REQUIRED,
                'Migration type of instance(minister, goskom, prav, municipal, vedomstva')
            ->addOption('--lfs', null, InputOption::VALUE_NONE,
                'Use local filesystem for check files exists')
            ->addOption('--no-sync', null, InputOption::VALUE_NONE,
        'Use local filesystem for check files exists');
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

//        try{
            $this->instancesSQL();
            $this->migrateAllSQL();
//        }catch(\Exception $e){
//            print_r($e);
//        }


        $endTime = time();
        $workTime = $endTime - $startTime;

        $hours = floor($workTime/3600);
        $minutes = floor(($workTime - $hours*3600)/60);
        $seconds = $workTime - $hours*3600 - $minutes*60;

        $this->io->success('------> Migration started at ' . date('d-m-Y H:i:s', $startTime));
        $this->io->success('------> Migration complete at ' . date('d-m-Y H:i:s', $endTime));
        $this->io->success('------> Total migration time ' . $hours . 'h ' . $minutes . 'min '.$seconds.'sec');
    }

    public function getSites()
    {
        $conn = $this->conn;

        // CREATE SITES MIGRATION
        $sites = [];
        $orgIds = [];

        if($this->input->getOption('type') == 'prav' || is_null($this->input->getOption('type'))) {
            foreach($this->pravitelstvo as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME, NEWS_CATEGORY_ID FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->pravitelstvo[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 1
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'municipal' || is_null($this->input->getOption('type'))) {
            foreach($this->municipals as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME, NEWS_CATEGORY_ID FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->municipals[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 2
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'vedomstva' || is_null($this->input->getOption('type'))) {
            foreach($this->vedomstva as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->vedomstva[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 3
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'goskom' || is_null($this->input->getOption('type'))) {
            foreach($this->goskomitet as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->goskomitet[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 4
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'minister' || is_null($this->input->getOption('type'))) {
            foreach($this->ministerstva as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->ministerstva[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 5
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'ombudsman' || is_null($this->input->getOption('type'))) {
            foreach($this->ombudsman as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME, NEWS_CATEGORY_ID FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->ombudsman[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 6
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'another' || is_null($this->input->getOption('type'))) {
            foreach($this->another as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME, NEWS_CATEGORY_ID FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->another[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 8
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        if($this->input->getOption('type') == 'ispRK' || is_null($this->input->getOption('type'))) {
            foreach($this->ispRK as $key=>$value) {
                $orgIds[] = $key;
            }
            $orgIdsStr = implode(',', $orgIds);
            //take all organisations
            $sql = 'SELECT ID,NAME, NEWS_CATEGORY_ID FROM ORGANIZATION WHERE ID IN('.$orgIdsStr.')';
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $sites[] = [
                    'code'      => $this->ispRK[$row['ID']],
                    'id'        => $row['ID'],
                    'title'     => $row['NAME'],
                    'typeId'    => 7
                ];
            }
            oci_free_statement($stid);
            $orgIds = [];
        }

        oci_close($conn);

        return $sites;
    }

    public function preMigrate()
    {
        $sqlContent = '';
        //pre defined for structure
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "CREATE UNIQUE INDEX uniq_old_id ON menu_node(old_id);"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN orig_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN old_parent_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN old_type_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN hide INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN draft INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ALTER COLUMN title TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ALTER COLUMN route DROP NOT NULL;"."\r\n";

        //pre defined for docs
        $sqlContent .= "ALTER TABLE document ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE document_attachment ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE document ADD COLUMN orig_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE document ADD COLUMN structure_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE document ALTER COLUMN title TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE document ALTER COLUMN created_at DROP NOT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE document ALTER COLUMN content DROP NOT NULL;"."\r\n";

        $sqlContent .= "ALTER TABLE attachment ALTER COLUMN preview_file_url TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE attachment ALTER COLUMN preview TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE attachment ALTER COLUMN original_file_name TYPE VARCHAR(2048);"."\r\n";

        //pre defined for news
        $sqlContent .= "ALTER TABLE article ALTER COLUMN title TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE article ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE attachment ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE article ALTER COLUMN category_id DROP NOT NULL;"."\r\n";

        //pre defined for events
        $sqlContent .= "ALTER TABLE event ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE event ALTER COLUMN title TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE event ALTER COLUMN place TYPE VARCHAR(2048);"."\r\n";

        //pre defined for photoreports
        $sqlContent .= "ALTER TABLE photo_report ADD COLUMN report_old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE photo_report ALTER COLUMN title TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE photo_report_attachment ADD COLUMN report_old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE attachment ALTER COLUMN id SET DEFAULT nextval('attachment_id_seq');"."\r\n";
        $sqlContent .= "ALTER TABLE article ALTER COLUMN id SET DEFAULT nextval('article_id_seq');"."\r\n";
        $sqlContent .= "ALTER TABLE photo_report ALTER COLUMN id SET DEFAULT nextval('photo_report_id_seq');"."\r\n";

        $sqlContent .= "ALTER TABLE video_report ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE video_report ADD COLUMN event_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE video_report ALTER COLUMN title TYPE VARCHAR(2048);"."\r\n";
        $sqlContent .= "ALTER TABLE video_report_attachment ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE video_report ALTER COLUMN id SET DEFAULT nextval('video_report_id_seq');"."\r\n";

        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN old_url VARCHAR(2000) DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN old_domain VARCHAR(2000) DEFAULT NULL;"."\r\n";
        $sqlContent .= "ALTER TABLE menu_node ADD COLUMN old_redirect VARCHAR(2000) DEFAULT NULL;"."\r\n";
        
        $sqlContent .= "DROP INDEX uniq_4d30180b989d9b62;"."\r\n";

        file_put_contents($this->rootDir . "web/oracle/preMigrate.sql", $sqlContent);
    }

    public function postMigrate()
    {
        $sqlContent = '';
        // postUp for structure
        $sqlContent .= "UPDATE menu_node AS mn SET parent_id = (SELECT id FROM menu_node WHERE old_id = mn.old_parent_id LIMIT 1);"."\r\n";
        // postUp for documents
        $sqlContent .= "UPDATE document AS doc SET menu_node_id = (SELECT id FROM menu_node WHERE old_id = doc.structure_id LIMIT 1);"."\r\n";
        $sqlContent .= "UPDATE article SET menu_node_id = (SELECT id FROM menu_node WHERE old_id = 1488);"."\r\n";

        $sqlContent .= "INSERT INTO document_attachment(id, document_id) SELECT a.id, doc.id FROM document as doc
 LEFT JOIN attachment as a ON a.old_id = doc.old_id WHERE a.type = 'document_attachment' ON CONFLICT(id) DO NOTHING;"."\r\n";

        $sqlContent .= "INSERT INTO article_attachment(id, article_id) SELECT a.id, art.id FROM attachment as a
            LEFT JOIN article as art ON art.old_id = a.old_id WHERE a.type = 'article_attachment' ON CONFLICT(id) DO NOTHING;"."\r\n";

        $sqlContent .= "INSERT INTO photo_report_attachment(id, photo_report_id) SELECT a.id, ph.id FROM attachment as a
            LEFT JOIN photo_report as ph ON ph.report_old_id = a.old_id WHERE a.type = 'photo_report_attachment' ON CONFLICT(id) DO NOTHING;"."\r\n";

        $sqlContent .= "INSERT INTO video_report_attachment(id, video_report_id) SELECT a.id, v.id FROM attachment as a
            LEFT JOIN video_report as v ON v.old_id = a.old_id WHERE a.type = 'video_report_attachment' ON CONFLICT(id) DO NOTHING;"."\r\n";

        $sqlContent .= "UPDATE event as e SET photo_report_id = (SELECT id FROM photo_report WHERE report_old_id = e.old_id LIMIT 1);"."\r\n";
        $sqlContent .= "UPDATE photo_report SET menu_node_id = (SELECT id FROM menu_node WHERE old_type_id = 16 LIMIT 1);"."\r\n";
        $sqlContent .= "UPDATE video_report SET menu_node_id = (SELECT id FROM menu_node WHERE old_type_id = 15 LIMIT 1);"."\r\n";

        $sqlContent .= "UPDATE menu_node SET is_hidden = CAST( hide AS BOOLEAN );"."\r\n";
        $sqlContent .= "UPDATE menu_node SET slug = NULL WHERE slug = '';"."\r\n";
        $sqlContent .= "CREATE UNIQUE INDEX uniq_4d30180b989d9b62 ON menu_node(slug);"."\r\n";

        $sqlContent .= "DELETE FROM attachment WHERE id IN (SELECT id FROM article_attachment WHERE article_id IN ("
            . "SELECT id FROM article WHERE id IN (SELECT id FROM article WHERE old_id IN("
            . "select old_id from article where old_id in (select old_id from article group by old_id having count(*) > 1)"
		    . ")) AND id NOT IN(SELECT MIN(id) FROM article WHERE old_id IN ("
            . "select old_id from article where old_id in (select old_id from article group by old_id having count(*) > 1)"
		    . ") GROUP BY old_id))) AND type = 'article_attachment';"."\r\n";
        $sqlContent .= "DELETE FROM article WHERE id IN (SELECT id FROM article WHERE old_id IN("
            . "select old_id from article where old_id in (select old_id from article group by old_id having count(*) > 1)"
            . ")) AND id NOT IN(SELECT MIN(id) FROM article WHERE old_id IN ("
            . "select old_id from article where old_id in (select old_id from article group by old_id having count(*) > 1)"
            . ") GROUP BY old_id);"."\r\n";
        $sqlContent .= "DELETE FROM attachment WHERE id IN (SELECT id FROM document_attachment WHERE document_id IN ("
            . "SELECT id FROM document WHERE id IN (SELECT id FROM document WHERE old_id IN("
            . "select old_id from document where old_id in (select old_id from document group by old_id having count(*) > 1)"
            . ")) AND id NOT IN(SELECT MIN(id) FROM document WHERE old_id IN ("
            . "select old_id from document where old_id in (select old_id from document group by old_id having count(*) > 1)"
            . ") GROUP BY old_id))) AND type = 'document_attachment';"."\r\n";
        $sqlContent .= "DELETE FROM document WHERE id IN (SELECT id FROM document WHERE old_id IN("
            . " select old_id from document where old_id in (select old_id from document"
            . " WHERE old_id IS NOT NULL group by old_id having count(*) > 1))) AND id NOT IN(SELECT MIN(id)"
            . " FROM document WHERE old_id IN (select old_id from document where old_id in ("
            . " select old_id from document WHERE old_id IS NOT NULL group by old_id having count(*) > 1))"
            . " GROUP BY old_id);"."\r\n";

        file_put_contents($this->rootDir . "web/oracle/postMigrate.sql", $sqlContent);
    }

    public function instancesSQL()
    {
        $sites = $this->getSites();
        $sqlContent = '';
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(1, 'pravitelʹstvo', 'Правительство') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(2, 'municipalʹnye-obrazovania', 'Муниципальные образования') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(3, 'vedomstva', 'Ведомства') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(4, 'goskomitety', 'Госкомитеты') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(5, 'ministerstva', 'Министерства') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(6, 'upolnomochnie', 'Уполномоченные') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(7, 'isp_rk', 'Организации при исполнительных советах государственной власти РК') ON CONFLICT(id) DO NOTHING;"."\r\n";
        $sqlContent .= "INSERT INTO instance_category (id, slug, title) VALUES(8, 'another', 'Иные организации, предприятия, сайты') ON CONFLICT(id) DO NOTHING;"."\r\n";

        foreach($sites as $site) {

            if($this->input->getOption('cl')) {
                $this->cleanFs($site['code']);
            }

            $this->createFs($site['code']);
            $this->createConfigsForSite($site['code']);

            $sqlContent .= "INSERT INTO instance(code, title, category_id) VALUES ('{$site['code']}', '{$site['title']}' , {$site['typeId']})
ON CONFLICT(code) DO NOTHING;"."\r\n";
        }


        if (!is_dir($this->rootDir . 'web/oracle/main/migrations/sql/')) {
            mkdir($this->rootDir . 'web/oracle/main/migrations/sql/', 0777, true);
            chmod($this->rootDir . 'web/oracle/main/migrations/sql/', 0777);
        }

        file_put_contents($this->rootDir . "web/oracle/main/migrations/sql/instances.sql", $sqlContent);

        if (!is_dir($this->kernelDir . '/fixtures/')) {
            mkdir($this->kernelDir . '/fixtures/', 0777, true);
            chmod($this->kernelDir . '/fixtures/', 0777);
        }

        $this->createFixture('fixtures', 'Instance', $this->rootDir . "web/oracle/main/migrations/sql", 'instances');
        $this->loadFixtures("main");
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

        $this->preMigrate();
        $this->postMigrate();

        foreach($total as $key=>$value) {
            $this->io->section('Collecting of Structure Data');
            $structure = $this->getStructureForOrganisationSQL($key, $value);

            $this->io->section('Collecting of News Data');
            $news = $this->getNewsForOrganisationSQL($key, $value);

            $this->io->section('Collecting of Documents Data');
            $docs = $this->getDocsForOrganisationSQL($key, $value);

            $this->io->section('Collecting of Events Data');
            $events = $this->getEventsForOrganisationSQL($key, $value);

            $clearInstanceCode = explode('-', $value);
            $compiledKernelName = '';
            foreach($clearInstanceCode as $code) {
                $compiledKernelName .= ucfirst($code);
            }

            $this->createFixture(
                'sites\\'.$value.'\\fixtures',
                'Start',
                $this->rootDir . "web/oracle",
                'preMigrate',
                'customer_');
            $this->createFixture(
                'sites\\'.$value.'\\fixtures',
                'Main',
                $this->rootDir . "web/oracle/{$value}/migrations/sql",
                'migration',
                'customer_',
                'StartFixture::class');
            $this->createFixture(
                'sites\\'.$value.'\\fixtures',
                'Post',
                $this->rootDir . "web/oracle",
                'postMigrate',
                'customer_',
                'StartFixture::class, MainFixture::class');
            $this->loadFixtures($value);

            $this->io->success('------> Migration complete for '.$value.' subdomain');
        }
    }

    public function getDocsForOrganisationSQL($key, $code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $docCategories = [];
        $syncedBefore = [];

        $sql = "SELECT STRUCTURE_PAGE_ID FROM ORGANIZATION WHERE ID = ".$key;
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $row = oci_fetch_assoc($stid);
        $structureId = $row['STRUCTURE_PAGE_ID'];
        oci_free_statement($stid);
        if($structureId > 0) {
            $sql = "SELECT ID, ORIG_ID FROM STRUCTURE  WHERE ORIG_ID > 0 START WITH ID = " . $structureId . " CONNECT BY PRIOR ID = PARENT_ID";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);

            while (($row = oci_fetch_assoc($stid)) != false) {
                $docCategories[$row['ID']] = $row['ORIG_ID'];
            }
            oci_free_statement($stid);

            $syncFile = $this->rootDir . "web/oracle/{$code}/sync/docs.txt";
            if(file_exists($syncFile)) {
//                $this->checkSync($syncFile, $code, 'Document');
                $syncedBefore = trim(file_get_contents($syncFile));
                if($syncedBefore !== '') {
                    $syncedBefore = explode(' ', $syncedBefore);
                }
            }

            foreach($docCategories as $key=>$value) {
                $sql = "SELECT ID FROM PUB_CATEGORY WHERE ID = ".$value;
                $stid = oci_parse($conn, $sql);
                oci_execute($stid);
                $row = oci_fetch_assoc($stid);
                $docCategoryId = $row['ID'];
                oci_free_statement($stid);
                if($docCategoryId > 0) {
                    $sql = "SELECT ID, FULL_TITLE, TEXT_FILE, PUB_DATE, NR FROM PUB WHERE PUB_CATEGORY_ID = ".$docCategoryId;
                    $stid = oci_parse($conn, $sql);
                    oci_execute($stid);
                    while (($row = oci_fetch_assoc($stid)) != false) {
                        if(!in_array($row['ID'], $syncedBefore)) {
                            //GRABB FILES, REMOVE TO DIRS
                            $docsDirectory = $this->rootDir . "web/uploads/{$code}/attachments/documents/";
                            $writeDocs = $this->writeDocsToOrg($row['TEXT_FILE'], $docsDirectory, 'file/pub/', $row['ID'], $code);

                            //WRITE MIGRATIONS

                            $sqlContent = "INSERT INTO document(author_id, title, slug, content, published_at, created_at, document_number, document_type, old_id, orig_id, structure_id) 
                        VALUES(1, '".$this->cleanString($row['FULL_TITLE'])."', '', '', '".$this->convertDate($row['PUB_DATE'])."', '".$this->convertDate($row['PUB_DATE'])."', ".(int)$row['NR'].", 1, ".(int)$row['ID'].", ".(int)$value.", ".(int)$key.");"."\r\n";

                            //LINK DOCUMENT ATTACHMENTS TO DOCUMENTS
                            $fileType = '';
                            if(mb_stripos($row['TEXT_FILE'], 'pdf') !== false) {
                                $fileType = 'application/pdf';
                            }
                            $sqlContent .= "INSERT INTO attachment(type, file_type, preview_file_url, preview, original_file_name, old_id)
                  VALUES('document_attachment', '".$fileType."', '".$writeDocs."', '".$row['TEXT_FILE']."', '".$row['TEXT_FILE']."', ".(int)$row['ID'].");"."\r\n";

                            file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                            file_put_contents($this->rootDir . "web/oracle/{$code}/sync/docs.txt", $row['ID'].' ', FILE_APPEND);
                        }
                        $syncedBefore[] = $row['ID'];
                    }
                    oci_free_statement($stid);
                }
            }
        }

        oci_close($conn);
        $this->io->success('documents collected');
    }

    public function getStructureForOrganisationSQL($key, $code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $syncedBefore = [];
        $writeMigration = false;
        $beforeText = '';

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


        $this->structureID = $container->get('customer_menu_manager')->findOneBy(['code' => 'structure_menu'])->getId();
        $this->documentTemplateID = $container->get('customer_structure_template_manager')->findOneBy(['code' => 'document'])->getId();
        $this->simpleTemplateID = $container->get('customer_structure_template_manager')->findOneBy(['code' => 'simple'])->getId();
        $this->newsTemplateID = $container->get('customer_structure_template_manager')->findOneBy(['code' => 'article'])->getId();
        $this->photoReportTemplateID = $container->get('customer_structure_template_manager')->findOneBy(['code' => 'photo_report'])->getId();
        $this->videoReportTemplateID = $container->get('customer_structure_template_manager')->findOneBy(['code' => 'video_report'])->getId();

        $sqlContent = "INSERT INTO menu_node(menu_id, title, node_order, structure_template_id, old_id) VALUES(".$this->structureID.", 'Новости', 0, 2, 1488);"."\r\n";
        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);

        $syncFile = $this->rootDir . "web/oracle/{$code}/sync/structure.txt";
        if(file_exists($syncFile)) {
//            $this->checkSync($syncFile, $code, 'MenuNode');
            $syncData = trim(file_get_contents($syncFile));
            if($syncData !== '') {
                $syncedBefore = explode(' ', $syncData);
            }
        }

        $sql = "SELECT STRUCTURE_PAGE_ID FROM ORGANIZATION WHERE ID = ".$key;
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $row = oci_fetch_assoc($stid);
        $structureId = $row['STRUCTURE_PAGE_ID'];
        oci_free_statement($stid);
        if($structureId > 0) {
            $sql = "SELECT ID, PARENT_ID, REDIRECT_TO, URL, DOMAIN, TITLE, TYPE,"
                    . " NAME, ORIG_ID, HIDE, DRAFT FROM STRUCTURE "
                    . "START WITH ID = ".$structureId." "
                    . "CONNECT BY PRIOR ID = PARENT_ID";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            
            $slugs = [];
            while (($row = oci_fetch_assoc($stid)) != false) {
                $beforeText = '';

                if($row['ID'] !== $structureId && !in_array($row['ID'], $syncedBefore)) {
                    $slug = '';
                    if(mb_stripos($row['URL'], 'http://') === false) {
                        $cleanFirstSlash = $row['URL'];
                        if($row['URL'][0] == '/') {
                            $cleanFirstSlash = substr($row['URL'], 1);
                        }
                        $urlRewrited = str_replace('/', '_', str_replace('.htm', '', $cleanFirstSlash));
                        if(!in_array($urlRewrited, $slugs)) {
                            $slug = $urlRewrited;
                            $slugs[] = $slug;
                        }
                    }
                    
                    if($row['TYPE'] == 3) {
                        $template = $this->documentTemplateID;
                        $beforeText = '';
                        if(!empty($row['REDIRECT_TO']) && $row['REDIRECT_TO'] !== '') {
                            //create document from link
                            $fileNameParts = explode('/', $row['REDIRECT_TO']);
                            $fileName = str_replace("'", "", array_pop($fileNameParts));
                            if(mb_stripos($fileName, 'pdf') !== false
                                || mb_stripos($fileName, 'doc') !== false
                                || mb_stripos($fileName, 'docx') !== false
                                || mb_stripos($fileName, 'xls') !== false
                                || mb_stripos($fileName, 'xlsx') !== false
                                || mb_stripos($fileName, 'rtf') !== false
                                || mb_stripos($fileName, 'ppt') !== false
                                || mb_stripos($fileName, 'zip') !== false
                                || mb_stripos($fileName, 'rar') !== false
                                || mb_stripos($fileName, 'csv') !== false
                                || mb_stripos($fileName, 'txt') !== false
                            ) {
                                if(mb_stripos($row['REDIRECT_TO'], 'rk.gov.ru') !== false) {
                                    //GRABB FILES, REMOVE TO DIRS
                                    $docsDirectory = $this->rootDir . "web/uploads/{$code}/attachments/documents/";
                                    $writeDocs = $this->writeDocsToOrgFromLink($fileName, $docsDirectory, $row['REDIRECT_TO'], $row['ID'], $code);
                                    //insert new document attachment
                                    $fileType = '';
                                    if(mb_stripos($fileName, 'pdf') !== false) {
                                        $fileType = 'application/pdf';
                                    }
                                    $sqlContent .= "INSERT INTO attachment(type, file_type, preview_file_url, preview, original_file_name, old_id)
                      VALUES('document_attachment', '".$fileType."', '".$writeDocs."', '".$fileName."', '".$fileName."', ".(int)$row['ID'].");"."\r\n";

                                    //WRITE MIGRATIONS
                                    $sqlContent .= "INSERT INTO document(author_id, title, slug, document_type, old_id, orig_id, structure_id)
                        VALUES(1, '".$this->cleanString($row['NAME'])."', '', 1, ".(int)$row['ID'].", ".(int)$row['ID'].", ".(int)$row['ID'].");"."\r\n";

                                    file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                                    file_put_contents($this->rootDir . "web/oracle/{$code}/sync/docs.txt", $row['ID'].' ', FILE_APPEND);
                                }
                                
                            }
                        }else{
                            if($row['ORIG_ID'] > 0) {
                                $sql = "SELECT HTML FROM HTML WHERE ID = ".$row['ORIG_ID']." AND rownum = 1";
                                $html = oci_parse($conn, $sql);
                                oci_execute($html);
                                $htmlRow = oci_fetch_assoc($html);
                                oci_free_statement($html);
                                if(!is_null($htmlRow['HTML']) && $htmlRow['HTML']->size()) {
                                    $beforeText = $this->cleanString($htmlRow['HTML']->read($htmlRow['HTML']->size()));
                                }
                            }

                        }

                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 29) {
                        $innerSql = "SELECT p.FIO, p.PICTURE, po.NAME, pd.ADDRESS, pd.PHONE, pd.EMAIL FROM PHONEBOOK pb LEFT JOIN
PERSON_DEPARTMENT pd ON pd.DEPARTMENT_ID = pb.ROOT_DEPARTMENT_ID LEFT JOIN POST po ON po.ID = pd.POST_ID LEFT JOIN
PERSON p ON p.ID = pd.PERSON_ID WHERE pb.ID = ".$row['ORIG_ID'];
                        $phonebookResults = oci_parse($conn, $innerSql);
                        oci_execute($phonebookResults);

                        $template = $this->simpleTemplateID;
                        $tableRowsHtml = '';
                        while (($tbl = oci_fetch_assoc($phonebookResults)) != false) {
                            $picture = '';
                            if(!empty($tbl['PICTURE'])) {
                                //write file to ckeditor files directory
                                $picture = $this->loadEditorPictures($tbl['PICTURE'], $row['ID'], $code);
                            }
                            $tableRowsHtml .= "<tr><td><image src=\"".$picture."\" width=\"75px\" alt=\"".$tbl['FIO']."\" /></td><td><p><strong>".$tbl['FIO'];
                            $tableRowsHtml .= "</strong></p><p>".$tbl['NAME'];
                            $tableRowsHtml .= "</p></td><td>".$tbl['PHONE']."</td><td><p>".$tbl['EMAIL']."</p><p>".$tbl['ADDRESS']."</p></td></tr>";
                        }

                        oci_free_statement($phonebookResults);
                        $beforeText = '<table class=\"table table-condensed\"><tr><td></td><td>Сотрудник</td><td>Телефон,факс</td><td>Дополнительная информация</td></tr>'
                            .$tableRowsHtml.'</table>';

                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 2) {
                        $innerSql = "SELECT p.FIO, p.PICTURE, p.BIOGRAPHY, po.NAME, pd.ADDRESS, pd.PHONE, pd.EMAIL FROM 
PERSON_DEPARTMENT pd LEFT JOIN POST po ON po.ID = pd.POST_ID LEFT JOIN PERSON p ON p.ID = pd.PERSON_ID WHERE pd.ID = ".$row['ORIG_ID'];
                        $personResults = oci_parse($conn, $innerSql);
                        oci_execute($personResults);
                        $template = $this->simpleTemplateID;
                        $beforeText = '';
                        $innerHTML = false;
                        while (($tbl = oci_fetch_assoc($personResults)) != false) {
                            $picture = '';
                            if(!empty($tbl['PICTURE'])) {
                                //write file to ckeditor files directory
                                $picture = $this->loadEditorPictures($tbl['PICTURE'], $row['ID'], $code);
                            }
                            $innerHTML .= "<div><image src=\"".$picture."\" width=\"150px\" alt=\"".$tbl['FIO']."\" /><p><strong>".$tbl['FIO'];
                            $innerHTML .= "</strong></p><p><strong>Контактная информация</strong></p>";
                            $innerHTML .= "<p>E-mail: ".$tbl['EMAIL']."</p>";
                            $innerHTML .= "<p>Адрес: ".$tbl['ADDRESS']."</p>";
                            $innerHTML .= "</div>";
                            $innerHTML .= "<div><p><strong>Автобиография</strong></p>";
                            if(!is_null($tbl['BIOGRAPHY']) && $tbl['BIOGRAPHY']->size()) {
                                $innerHTML .= "<div>".$this->cleanString($tbl['BIOGRAPHY']->read($tbl['BIOGRAPHY']->size()))."</div>";
                            }
                            $innerHTML .= "</div>";
                        }

                        oci_free_statement($personResults);

                        if($innerHTML) {
                            $beforeText .= "<div>". $innerHTML ."</div>";
                        }

                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 5) {
                        if(!empty($row['TITLE']) && $row['TITLE'] !== '' && !is_null($row['TITLE']) && $row['TITLE']->size()) {
                            $beforeText = $this->cleanString($row['TITLE']->read($row['TITLE']->size()));
                        }
                        $template = $this->simpleTemplateID;

                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 16) {
                        $beforeText = '';
                        $template = $this->photoReportTemplateID;
                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft, old_type_id)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].", 16) ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 15) {
                        $beforeText = '';
                        $template = $this->videoReportTemplateID;
                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft, old_type_id)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].", 16) ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 6) {
                        $template = $this->simpleTemplateID;
                        $beforeText = '';
                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;

                        $innerSql = 'SELECT d.ID,d.NAME AS "dep", d.FUNCTIONS, p.FIO, p.PICTURE, po.NAME, pd.PHONE FROM DEPARTMENT d LEFT JOIN
PERSON_DEPARTMENT pd ON pd.DEPARTMENT_ID = d.ID LEFT JOIN POST po ON po.ID = pd.POST_ID LEFT JOIN
PERSON p ON p.ID = pd.PERSON_ID WHERE d.PARENT_ID = '.$row['ORIG_ID'];
                        $podved = oci_parse($conn, $innerSql);
                        oci_execute($podved);

                        while (($tbl = oci_fetch_assoc($podved)) != false) {
                            $picture = '';
                            $beforeText = '';
                            if(!empty($tbl['FUNCTIONS']) && $tbl['FUNCTIONS'] !== '' && !is_null($tbl['FUNCTIONS']) && $tbl['FUNCTIONS']->size()) {
                                $beforeText .= "<div class=\"podved-block\">".$this->cleanString($tbl['FUNCTIONS']->read($tbl['FUNCTIONS']->size()))."</div>";
                            }
                            if(!empty($tbl['PICTURE'])) {
                                //write file to ckeditor files directory
                                $picture = $this->loadEditorPictures($tbl['PICTURE'], $row['ID'], $code);
                            }

                            $beforeText .= "<div class=\"podved-person-block\">";
                            $beforeText .= "<div class=\"person-image\"><image src=\"".$picture."\" width=\"75px\" alt=\"".$tbl['FIO']."\" /></div>";
                            $beforeText .= "<p><strong>".$tbl['FIO']."</strong></p>";
                            $beforeText .= "<p>".$tbl['NAME']."</p>";
                            $beforeText .= "<p>Телефон: ".$tbl['PHONE']."</p>";
                            $beforeText .= "</div>";

                            $sqlContent .= "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($tbl['dep'])."', '".$beforeText."', ".$template.", ".(int)$tbl['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['ID'].", 0, 0) ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                            file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                            file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $tbl['ID'].' ', FILE_APPEND);
                            $writeMigration = true;
                        }
                        oci_free_statement($podved);

                    }else{
                        $template = $this->simpleTemplateID;

                        $sqlContent = "INSERT INTO menu_node(slug, old_url, old_domain, old_redirect, menu_id, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES('".$slug."', '".$this->cleanUrl($row['URL'])."', '".$row['DOMAIN']."', '".$this->cleanUrl($row['REDIRECT_TO'])."', ".$this->structureID.", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }

                }
                $syncedBefore[] = $row['ID'];

                if($row['ID'] !== $structureId) {
                    if(!empty($row['REDIRECT_TO']) && $row['REDIRECT_TO'] !== '') {
                        //create document from link
                        $fileNameParts = explode('/', $row['REDIRECT_TO']);
                        $fileName = str_replace("'", "", array_pop($fileNameParts));
                        if (mb_stripos($fileName, 'pdf') === false
                            && mb_stripos($fileName, 'doc') === false
                            && mb_stripos($fileName, 'docx') === false
                            && mb_stripos($fileName, 'xls') === false
                            && mb_stripos($fileName, 'xlsx') === false
                            && mb_stripos($fileName, 'rtf') === false
                            && mb_stripos($fileName, 'ppt') === false
                            && mb_stripos($fileName, 'zip') === false
                            && mb_stripos($fileName, 'rar') === false
                            && mb_stripos($fileName, 'csv') === false
                            && mb_stripos($fileName, 'txt') === false
                        ) {
                            $sqlContent = "UPDATE menu_node SET route = '" . $row['REDIRECT_TO'] . "' WHERE old_id = " . $row['ID'] . ";" . "\r\n";
                            file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        }
                    }
                }
            }
            oci_free_statement($stid);
            oci_close($conn);
        }
        $this->io->success('structure collected');
    }

    public function loadEditorPictures($file, $old_id, $code)
    {
        if($file == '') {
            return '';
        }

        if(file_exists($this->webDir.'file/'.$file)) {
            return '/file/'.$file;
        }

        try {
            $newFile = file_get_contents('http://rk.gov.ru/file/' . $file);
            $fileUrl = '/file/' . $file;
            return $fileUrl;
        } catch (\Exception $e) {}

        return '';
    }

    public function writeDocsToOrgFromLink($fileName, $dir, $linkDownload, $entityId, $code)
    {
        if($fileName == '') {
            return '';
        }

        $urlParts = explode('/', $linkDownload);
        for($i = 0; $i < 3; $i++) {
            array_shift($urlParts);
        }
        $fileUrl = str_replace("'", "", '/'.implode('/', $urlParts));

        if(file_exists($this->webDir.$fileUrl)){
            return $fileUrl;
        }

        return '';
    }


    public function writeDocsToOrg($doc, $dir, $path, $old_id, $code)
    {
        if($doc == '') {
            return '';
        }

        $docMod = str_replace("'", "", $doc);
        if(file_exists($this->webDir.'/'.$path.$docMod)){
            $fileUrl = "/{$path}{$docMod}";
            return $fileUrl;
        }
        return '';
    }

    public function getNewsForOrganisationSQL($key, $code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        //create temporary directories
        $article_dir = $this->rootDir . "web/uploads/{$code}/attachments/articles/";
        $syncedBefore = [];

        //migrate news
        $syncFile = $this->rootDir . "web/oracle/{$code}/sync/news.txt";
        if(file_exists($syncFile)) {
//            $this->checkSync($syncFile, $code, 'Article');
            $syncData = trim(file_get_contents($syncFile));
            if($syncData !== '') {
                $syncedBefore = explode(' ', $syncData);
            }
        }

        $sql = "SELECT ID, TITLE, TEXT, NEWS_DATE, IMAGE_FILE, RATING FROM NEWS WHERE NEWS_CATEGORY_ID IN (
                SELECT NEWS_CATEGORY_ID FROM ORGANIZATION WHERE ID = ".$key.")";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
            if(!in_array($row['ID'], $syncedBefore)) {
                $sqlContent = "INSERT INTO attachment(type, preview_file_url, preview, original_file_name, old_id) 
                  VALUES('article_attachment', '".$this->writeImageToNews($row["IMAGE_FILE"], $article_dir, $row['ID'], $code)."', '".$row['IMAGE_FILE']."', '".$row['IMAGE_FILE']."', ".(int)$row['ID'].");"."\r\n";

                $sqlContent .= "INSERT INTO article(slug, title, content, published_at, created_at, views_counter, author_id, old_id)
                    VALUES ('','".$this->cleanString($row['TITLE'])."', '".$this->cleanString($row['TEXT']->read($row['TEXT']->size()))."' , '".$this->convertDate($row['NEWS_DATE'])."', '".$this->convertDate($row['NEWS_DATE'])."', ".(int)$row['RATING'].", 1, ".(int)$row['ID'].");"."\r\n";
                file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                file_put_contents($this->rootDir . "web/oracle/{$code}/sync/news.txt", $row['ID'].' ', FILE_APPEND);

                $syncedBefore[] = $row['ID'];
            }
            $syncedBefore[] = $row['ID'];
        }
        oci_free_statement($stid);
        oci_close($conn);

        $this->io->success('news collected');

    }

    public function writeImageToNews($img, $dir, $old_id, $code)
    {
        if($img == '') {
            return '';
        }

        $filePath = $this->webDir.'/file/' . $img;
        if(file_exists($filePath)){
            return '/file/' . $img;
        }

        $filePath = $this->webDir.'/rus/file/news/' . $img;
        if(file_exists($filePath)) {
            return '/rus/file/news/' . $img;
        }
        return '';
    }

    public function getEventsForOrganisationSQL($key, $code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $syncedBefore = [];
        $photoReports = false;
        $videoReports = false;

        $syncFile = $this->rootDir . "web/oracle/{$code}/sync/events.txt";
        if(file_exists($syncFile)) {
//            $this->checkSync($syncFile, $code, 'Event');
            $syncData = trim(file_get_contents($syncFile));
            if($syncData !== '') {
                $syncedBefore = explode(' ', $syncData);
            }
        }

        $sql = "SELECT m.ID,org.NAME, m.NAME as measure_name, m.MEASURE_DATE, m.ACC_END, m.PLACE
                FROM ORGANIZATION org LEFT JOIN MEASURE_CATEGORY mc ON mc.PLAN_CATEGORY_ID = org.PLAN_CATEGORY_ID
                LEFT JOIN MEASURE m ON m.MEASURE_CATEGORY_ID = mc.ID WHERE org.ID = ".$key;
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) {
            if(!in_array($row['ID'], $syncedBefore)) {
                $sqlContent = "INSERT INTO event(author_id, title, slug, old_id, created_at, published_at, start_date, end_date, place) 
VALUES(1,'".$this->cleanString($row['MEASURE_NAME'])."', '', ".(int)$row['ID'].", '".$this->convertDate($row['MEASURE_DATE'])."', '".$this->convertDate($row['MEASURE_DATE'])."', '".$this->convertDate($row['MEASURE_DATE'])."', '".$this->convertDate($row['ACC_END'])."','".$this->cleanString($row['PLACE'])."');"."\r\n";
                file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                file_put_contents($this->rootDir . "web/oracle/{$code}/sync/events.txt", $row['ID'].' ', FILE_APPEND);
                $syncedBefore[] = $row['ID'];

                $photoReports = $this->getPhotoreportsForOrganisationSQL($row['ID'], $code, $row['MEASURE_DATE']);
                $videoReports = $this->getVideoReports($row['ID'], $code, $row['MEASURE_DATE']);
            }
        }

        oci_free_statement($stid);
        oci_close($conn);
        $this->io->success('events collected');

        if($photoReports){
            $this->io->success('photoReports collected');
        }

        if($videoReports){
            $this->io->success('videoReports collected');
        }

    }

    public function getPhotoreportsForOrganisationSQL($measureId, $code, $measureDate)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $photoreport_dir = $this->rootDir . "web/uploads/{$code}/attachments/photoreport/";
        $syncedBefore = [];
        $photoReportCreated = false;

        $syncFile = $this->rootDir . "web/oracle/{$code}/sync/photoreports.txt";
        if(file_exists($syncFile)) {
//            $this->checkSync($syncFile, $code, 'PhotoReport');
            $syncData = trim(file_get_contents($syncFile));
            if($syncData !== '') {
                $syncedBefore = explode(' ', $syncData);
            }
        }

        $sql = "SELECT ph.ID, ph.IMG_PRINT, ph.MEASURE_ID, m.NAME FROM PHOTOREPORT ph 
                LEFT JOIN MEASURE m ON m.ID = ph.MEASURE_ID WHERE MEASURE_ID = ".$measureId;
        try{
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);

            while (($row = oci_fetch_assoc($stid)) != false) {
                if(!in_array($row['ID'], $syncedBefore)) {
                    $sqlContent = "INSERT INTO attachment(type, preview_file_url, preview, original_file_name, old_id) 
              VALUES('photo_report_attachment', '".$this->writeImageToPhotoReports($row["IMG_PRINT"], $photoreport_dir, $row['ID'], $code)."', '".$row['IMG_PRINT']."', '".$row['IMG_PRINT']."', ".(int)$row['MEASURE_ID'].");"."\r\n";

                    if(!$photoReportCreated) {
                        $sqlContent .= "INSERT INTO photo_report(title, report_old_id, published_at) VALUES('".$this->cleanString($row['NAME'])."', ".(int)$row['MEASURE_ID'].", '".$this->convertDate($measureDate)."');"."\r\n";
                        $photoReportCreated = true;
                    }

                    file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                    file_put_contents($this->rootDir . "web/oracle/{$code}/sync/photoreports.txt", $row['ID'].' ', FILE_APPEND);
                }
            }

            oci_free_statement($stid);
        }catch(\Exception $e){}

        oci_close($conn);

        return true;
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

    public function getVideoReports($measureId, $code, $measureDate)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $syncedBefore = [];
        $photoReportCreated = false;

        $syncFile = $this->rootDir . "web/oracle/{$code}/sync/videoreports.txt";
        if(file_exists($syncFile)) {
//            $this->checkSync($syncFile, $code, 'VideoReport');
            $syncData = trim(file_get_contents($syncFile));
            if($syncData !== '') {
                $syncedBefore = explode(' ', $syncData);
            }
        }

        $sql = "SELECT ID, HI, LO, TITLE, INFO, HI_LINK, LO_LINK FROM VIDEO WHERE MEASURE_ID = ".$measureId;
        try{
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);

            while (($row = oci_fetch_assoc($stid)) != false) {
                if(!in_array($row['ID'], $syncedBefore)) {
                    $findLocal = '';
                    if(!empty($row['HI'])) {
                        $fileName = $row['HI'];
                        $findLocal = $this->writeVideo($row['HI']);
                    }elseif(!empty($row['LO'])) {
                        $fileName = $row['LO'];
                        $findLocal = $this->writeVideo($row['LO']);
                    }elseif(!empty($row['HI_LINK'])){
                        $fileName = $row['HI_LINK'];
                        $findLocal = $row['HI_LINK'];
                    }elseif(!empty($row['LO_LINK'])){
                        $fileName = $row["LO_LINK"];
                        $findLocal = $row['LO_LINK'];
                    }else{
                        $fileName = '';
                    }

                    $sqlContent = "INSERT INTO video_report(title, description, old_id, published_at, event_id, is_published) 
                        VALUES('".$this->cleanString($row['TITLE'])."', '".$this->cleanString($row['INFO'])."', ".(int)$row['ID'].", '".$this->convertDate($measureDate)."', ".$measureId.", TRUE);"."\r\n";
                    $sqlContent .= "INSERT INTO attachment(type, file_type, preview_file_url, preview, original_file_name, old_id) 
              VALUES('video_report_attachment', 'video/mp4','".$findLocal."', '".$fileName."', '".$fileName."', ".(int)$row['ID'].");"."\r\n";

                    file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                    file_put_contents($this->rootDir . "web/oracle/{$code}/sync/videoreports.txt", $row['ID'].' ', FILE_APPEND);
                }
            }

            oci_free_statement($stid);
        }catch(\Exception $e){}

        oci_close($conn);

        return true;
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

    public function cleanString($string)
    {
        $str = trim(strip_tags($string, '<a><img><br><table><tr><td><th><thead><tbody><p><div><ul><li><ol><strong><i><h1><h2><h3><h4><h5><h6><center>'));
        $str = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $str)));
        $str = preg_replace("/(style=\".+?\"|onclick=\".+?\"|id=\".+?\"|class=\".+?\"|align=\".+?\")/","", $str);
        $str = str_replace("'", '"', $str);
        return $str;
    }

    public function cleanUrl($string)
    {
        $str = str_replace("'", '', $string);
        return $str;
    }

    public function convertDate($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function createFs($value)
    {
        //create temporary directories
        if (!is_dir($this->rootDir . "web/oracle/{$value}/migrations/doctrine/")) {
            mkdir($this->rootDir . "web/oracle/{$value}/migrations/doctrine/", 0777, true);
            chmod($this->rootDir . "web/oracle/{$value}/migrations/doctrine/", 0777);
        }
        if (!is_dir($this->rootDir . "web/oracle/{$value}/migrations/sql/")) {
            mkdir($this->rootDir . "web/oracle/{$value}/migrations/sql/", 0777, true);
            chmod($this->rootDir . "web/oracle/{$value}/migrations/sql/", 0777);
        }
        if (!is_dir($this->rootDir . "web/oracle/{$value}/sync/")) {
            mkdir($this->rootDir . "web/oracle/{$value}/sync/", 0777, true);
            chmod($this->rootDir . "web/oracle/{$value}/sync/", 0777);
        }

        //create directories for subdomains attachments
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_BANNERS)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_BANNERS, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_BANNERS, 0777);
        }
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_PAGES)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_PAGES, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_PAGES, 0777);
        }
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_MEDIA)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_MEDIA, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_MEDIA, 0777);
        }
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_ARTICLES)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_ARTICLES, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_ARTICLES, 0777);
        }
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_DOCUMENTS)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_DOCUMENTS, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_DOCUMENTS, 0777);
        }
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_EVENTS)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_EVENTS, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_EVENTS, 0777);
        }
        if (!is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_PHOTOREPORT)) {
            mkdir($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_PHOTOREPORT, 0777, true);
            chmod($this->rootDir . "web/" . Attachment::FILE_DIR.$value.'/'.Attachment::ATTACHMENTS_DIR.Attachment::PATH_PHOTOREPORT, 0777);
        }

        $this->output->writeln('filesystem created...');
    }

    public function cleanFs($value)
    {
        //remove temporary directories
        if (is_dir($this->rootDir . "web/oracle/{$value}/migrations")) {
            $this->delTree($this->rootDir . "web/oracle/{$value}/migrations");
            $this->io->success('Migrations deleted!');

        }

//        if($this->input->getOption('no-sync')) {
            if (is_dir($this->rootDir . "web/oracle/{$value}/sync")) {
                $this->delTree($this->rootDir . "web/oracle/{$value}/sync");
                $this->io->success('Sync deleted!');
            }

            $deleteFC = PortalHelper::removeFolderWithContents($this->rootDir . "web/fc/{$value}");

            //remove directories for subdomains attachments
            if (is_dir($this->rootDir . "web/" . Attachment::FILE_DIR.$value)) {
                $this->delTree($this->rootDir . "web/" . Attachment::FILE_DIR.$value);
                $this->io->success('Uploads deleted!');
            }

            $clearInstanceCode = explode('-', $value);
            $compiledKernelName = '';
            foreach($clearInstanceCode as $code) {
                $compiledKernelName .= ucfirst($code);
            }
            $newKernel = $compiledKernelName."Kernel";

            if(file_exists($this->rootDir . "app/sites/{$value}/{$newKernel}.php")) {
                require_once $this->rootDir . "app/sites/{$value}/{$newKernel}.php";

                $kernel = new $newKernel('dev', true);
                $kernel->boot();

                $application = new Application($kernel);
                $application->setAutoExit(false);

                // create DB
                $input = new ArrayInput(array(
                    'command' => 'doctrine:database:drop',
                    '--force' => true,
                    '--connection' => 'customer'
                ));

                $application->run($input, $this->output);
                $this->io->success('Database deleted!');
            }

            if (is_dir($this->rootDir . "app/sites/{$value}")) {
                $this->delTree($this->rootDir . "app/sites/{$value}");
                $this->io->success('Kernel deleted!');
            }
//        }


    }

    public function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

    public function createConfigsForSite($instanceCode)
    {
        // make new config dir
        if (!is_dir($this->kernelDir . '/sites/'.$instanceCode)) {
            mkdir($this->kernelDir . '/sites/'.$instanceCode, 0777, true);
        }
        //fixtures dir
        if (!is_dir($this->kernelDir . '/sites/'.$instanceCode.'/fixtures/')) {
            mkdir($this->kernelDir . '/sites/'.$instanceCode.'/fixtures/', 0777, true);
        }

        $sourceConfig = $this->kernelDir . '/config';
        $destinationConfig = $this->kernelDir . '/sites/'.$instanceCode.'/config';
        if (!is_dir($destinationConfig)) {
            mkdir($destinationConfig, 0775);
        }
        // copy all config files
        PortalHelper::copyFolderContents($sourceConfig, $destinationConfig);

        // make new front controllers
        $dirFrontController = $this->rootDir . 'web/fc/';
        if (!is_dir($dirFrontController.$instanceCode)) {
            mkdir($dirFrontController.$instanceCode, 0775, true);
        }
        $dirSource = $this->rootDir . 'web/';

        $clearInstanceCode = explode('-', $instanceCode);
        $compiledKernelName = '';
        foreach($clearInstanceCode as $code) {
            $compiledKernelName .= ucfirst($code);
        }
        $newKernel = $compiledKernelName."Kernel";

        $appPhp = file_get_contents($this->rootDir . 'web/app_sub.php');
        $appPhp = str_replace('subdomainfolder', $instanceCode, $appPhp);
        $appPhp = str_replace('subdomainkernel', $newKernel, $appPhp);
        file_put_contents($dirFrontController . $instanceCode . '/app.php', $appPhp);

        $appDevPhp = file_get_contents($this->rootDir . 'web/app_dev_sub.php');
        $appDevPhp = str_replace('subdomainfolder', $instanceCode, $appDevPhp);
        $appDevPhp = str_replace('subdomainkernel', $newKernel, $appDevPhp);
        file_put_contents($dirFrontController . $instanceCode . '/app_dev.php', $appDevPhp);

        $appKernel = file_get_contents($this->kernelDir . '/AppKernelSub.php');
        $appKernel = str_replace('AppKernelSub', $newKernel, $appKernel);
        file_put_contents($this->kernelDir . "/sites/{$instanceCode}/{$newKernel}.php", $appKernel);

        // create site uploads folder
        $destinationUploads = $this->rootDir . 'web/uploads/'.$instanceCode.'/';
        if (!is_dir($destinationUploads)) {
            mkdir($destinationUploads, 0777);
            // Crutch on local development
            chmod($destinationUploads, 0777);
            foreach (Attachment::$PATH_LIST as $pathDir) {
                if(!is_dir($destinationUploads . Attachment::ATTACHMENTS_DIR . $pathDir)) {
                    mkdir($destinationUploads . Attachment::ATTACHMENTS_DIR . $pathDir, 0777, true);
                    chmod($destinationUploads . Attachment::ATTACHMENTS_DIR . $pathDir, 0777);
                }
            }
        }

        // make symlinks
        if(!is_dir($dirFrontController . $instanceCode .'/bundles')) {
            symlink($this->rootDir . 'web/bundles', $dirFrontController . $instanceCode .'/bundles');
        }
        if(!is_dir($dirFrontController . $instanceCode .'/uploads')) {
            symlink($this->rootDir . 'web/uploads', $dirFrontController . $instanceCode .'/uploads');
        }
        if(!is_dir($dirFrontController . $instanceCode .'/themes')) {
            symlink($this->rootDir . 'web/themes', $dirFrontController . $instanceCode .'/themes');
        }

        if(!is_dir($dirFrontController . $instanceCode .'/file')) {
            symlink($this->rootDir . 'web/file', $dirFrontController . $instanceCode .'/file');
        }

        if(!is_link($dirFrontController . $instanceCode .'/robots.txt')) {
            symlink($this->rootDir . 'web/robots.txt', $dirFrontController . $instanceCode .'/robots.txt');
        }

        if(!is_dir($dirFrontController . $instanceCode . '/rus')) {
            mkdir($dirFrontController . $instanceCode . '/rus', 0777);
        }
        if(!is_link($dirFrontController . $instanceCode .'/rus/file')) {
            symlink($this->rootDir . 'web/file', $dirFrontController . $instanceCode . '/rus/file');
        }

        // add params to parameters.yml
        $arr = file($destinationConfig.'/parameters.yml');
        $handle = @fopen($destinationConfig.'/parameters.yml', "w+");

        if ($handle) {
            $str = '';
            foreach ($arr as $string) {
                if (stripos($string, 'database_name2') !== false) {
                    $str .= "    database_name2: ".(string)Instance::PREFIX_DATABASE_DEFAULT.$instanceCode."\n";
                }elseif (strpos($string, 'migration_dir') !== false) {
                    $str .= "    migration_dir: " . $this->kernelDir . "/DoctrineMigrations"."\n";
                }elseif (strpos($string, 'instance_code') !== false) {
                    $str .= "    instance_code: " . $instanceCode . "\n";
                }else{
                    $str .= $string;
                }
            }

            fwrite($handle, $str);
            fclose($handle);
        }

        require_once $this->kernelDir . "/sites/{$instanceCode}/{$newKernel}.php";

        $kernel = new $newKernel('dev', true);
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        // create DB
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create',
            '--if-not-exists' => true,
            '--connection' => 'customer'
        ));

        $application->run($input, $this->output);

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '--em' => 'customer',
            '--no-interaction' => true
        ));

        $application->run($input, $this->output);

        $kernel->shutdown();
    }

    public function createFixture($namespaceStr, $classStr, $path, $filename, $emPrefix = '', $dependencies = '')
    {
        $pathToWrite = str_replace('\\', '/', $namespaceStr);
        $fixture = file_get_contents($this->rootDir . "web/Fixture_dist.php");

        $useStatements = '';
        $dependenciesPhp = '';
        if($dependencies !== '') {
            $uses = explode(',', $dependencies);
            foreach($uses as $useState) {
                $parts = explode('::', $useState);
                $classTitle = trim($parts[0]);
                $useStatements .= "use app\\{$namespaceStr}\\{$classTitle}".";"."\r\n";
            }
            $dependenciesPhp = sprintf(file_get_contents($this->rootDir . "web/FixtureDependencies_dist.php"), $dependencies);
        }
        file_put_contents(
            $this->kernelDir . "/{$pathToWrite}/{$classStr}Fixture.php",
            sprintf($fixture, $namespaceStr, $useStatements, $classStr, $path, $filename, $emPrefix, $dependenciesPhp)
        );

        $this->output->writeln('fixtures created...');

    }

    public function loadFixtures($instanceCode)
    {
        $instanceCode = strtolower($instanceCode);
        if($instanceCode == 'main') {
            $kernel = $this->container->get('kernel');

            $input = new ArrayInput(array(
                'command' => 'doctrine:fixtures:load',
                '--em' => 'customer',
                '--fixtures' => $this->kernelDir . "/fixtures",
                '--append' => true
            ));
        }else{
            $clearInstanceCode = explode('-', $instanceCode);
            $compiledKernelName = '';
            foreach($clearInstanceCode as $code) {
                $compiledKernelName .= ucfirst($code);
            }
            $newKernel = $compiledKernelName."Kernel";

            require_once $this->kernelDir . "/sites/{$instanceCode}/{$newKernel}.php";

            $kernel = new $newKernel('dev', true);

            $input = new ArrayInput(array(
                'command' => 'doctrine:fixtures:load',
                '--append' => true,
                '--fixtures' => $this->kernelDir . "/sites/{$instanceCode}/fixtures",
                '--em' => 'customer'
            ));
        }

        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->run($input, $this->output);

        $this->output->writeln('fixtures loaded...');
    }


    public function checkSync($syncFile, $instanceCode, $tableName)
    {
        if($instanceCode == 'main') {
            $kernel = $this->container->get('kernel');
        }else{
            $clearInstanceCode = explode('-', $instanceCode);
            $compiledKernelName = '';
            foreach($clearInstanceCode as $code) {
                $compiledKernelName .= ucfirst($code);
            }
            $newKernel = $compiledKernelName."Kernel";

            require_once $this->kernelDir . "/sites/{$instanceCode}/{$newKernel}.php";

            $kernel = new $newKernel('dev', true);
        }

        $kernel->boot();

        $kernelContainer = $kernel->getContainer();
        $syncArray = [];
        $fileContent = trim(file_get_contents($syncFile));

        if($fileContent !== '' && !empty($fileContent)){
            $syncArray = explode(' ', $fileContent);
        }

        $methodName = 'get' . $tableName;

        $dbString = $kernelContainer->get('sync_manager')->$methodName();
        $dbArray = explode(' ', $dbString);
//        if(count($syncArray) > 0) {
//            $resultArray = array_intersect($dbArray, $syncArray);
//            if(count($resultArray) > 0) {
//                file_put_contents($syncFile, implode(' ', $resultArray));
                file_put_contents($syncFile, implode(' ', $dbArray));
//            }
//        }
    }

}
