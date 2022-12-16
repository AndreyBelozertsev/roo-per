<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RewriteLinksCommand extends ContainerAwareCommand
{
    private $conn;
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;
    public $defaultPhotoReportTypeId = 16;
    public $defaultVideoReportTypeId = 15;

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

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('rwrtlc')
            ->setDescription('Rewrite Links for menu before text and article content...');
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
        $this->rootDir = $this->kernelDir . '/../';
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

    public function getSites()
    {
        $sites['main'] = 
            [
                'title'     => 'Правительство Республики Крым',
                'typeId'    => 1
            ];
        return $sites;
    }

    public function preMigrate()
    {
        //pre defined for structure
        $sqlContent = "ALTER TABLE menu_node ADD COLUMN old_id INTEGER DEFAULT NULL;"."\r\n";
        $sqlContent .= "CREATE UNIQUE INDEX UNIQ_old_id ON menu_node (old_id);"."\r\n";
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


        file_put_contents($this->webDir . "/oracle/preMigrate.sql", $sqlContent);
    }

    public function postMigrate()
    {
        $sqlContent = '';
        // postUp for structure
        $sqlContent .= "UPDATE menu_node AS mn SET parent_id = (SELECT id FROM menu_node WHERE old_id = mn.old_parent_id LIMIT 1);"."\r\n";
        // postUp for documents
        $sqlContent .= "UPDATE document AS doc SET menu_node_id = (SELECT id FROM menu_node WHERE old_id = doc.structure_id LIMIT 1);"."\r\n";
        $sqlContent .= "UPDATE article SET menu_node_id = (SELECT id FROM menu_node WHERE old_id = 1488);"."\r\n";

        $sqlContent .= "INSERT INTO document_attachment(id, document_id) SELECT a.id, doc.id FROM document as doc LEFT JOIN attachment as a ON a.old_id = doc.old_id WHERE a.type = 'document_attachment';"."\r\n";

        $sqlContent .= "INSERT INTO article_attachment(id, article_id) SELECT a.id, art.id FROM attachment as a
            LEFT JOIN article as art ON art.old_id = a.old_id WHERE a.type = 'article_attachment';"."\r\n";

        $sqlContent .= "INSERT INTO photo_report_attachment(id, photo_report_id) SELECT a.id, ph.id FROM attachment as a
            LEFT JOIN photo_report as ph ON ph.report_old_id = a.old_id WHERE a.type = 'photo_report_attachment';"."\r\n";

        $sqlContent .= "INSERT INTO video_report_attachment(id, video_report_id) SELECT a.id, v.id FROM attachment as a
            LEFT JOIN video_report as v ON v.old_id = a.old_id WHERE a.type = 'video_report_attachment';"."\r\n";

        $sqlContent .= "UPDATE event as e SET photo_report_id = (SELECT id FROM photo_report WHERE report_old_id = e.old_id LIMIT 1);"."\r\n";
        $sqlContent .= "UPDATE photo_report SET menu_node_id = (SELECT id FROM menu_node WHERE old_type_id = 16 LIMIT 1);"."\r\n";
        $sqlContent .= "UPDATE video_report SET menu_node_id = (SELECT id FROM menu_node WHERE old_type_id = 15 LIMIT 1);"."\r\n";

        $sqlContent .= "UPDATE menu_node SET is_hidden = CAST( hide AS BOOLEAN );"."\r\n";

        file_put_contents($this->rootDir . "web/oracle/postMigrate.sql", $sqlContent);
    }

    public function migrateAllSQL()
    {
        $code = 'main';
        $this->newsRoot = 1;
        $this->eventsRoot = 41;
        $this->structureRoot = 2236;

        //remove temporary directories
        if (is_dir($this->rootDir . "web/oracle/{$code}")) {
            $this->delTree($this->rootDir . "web/oracle/{$code}");
            $this->io->success('Sync deleted!');

        }
        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);

        // drop DB
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => true,
            '--connection' => 'customer'
        ));

        $application->run($input, $this->output);
        $this->io->success('Database deleted!');

        //create temporary directories
        if (!is_dir($this->rootDir . "web/oracle/{$code}/migrations/sql/")) {
            mkdir($this->rootDir . "web/oracle/{$code}/migrations/sql/", 0777, true);
            chmod($this->rootDir . "web/oracle/{$code}/migrations/sql/", 0777);
        }
        if (!is_dir($this->rootDir . "web/oracle/{$code}/sync/")) {
            mkdir($this->rootDir . "web/oracle/{$code}/sync/", 0777, true);
            chmod($this->rootDir . "web/oracle/{$code}/sync/", 0777);
        }
        $this->output->writeln('filesystem created...');

        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);

        // create DB
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create',
            '--if-not-exists' => true,
            '--connection' => 'customer'
        ));

        $application->run($input, $this->output);

        $this->io->success('Database created!');

        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '--em' => 'customer',
            '--no-interaction' => true
        ));

        $application->run($input, $this->output);

        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'fos:user:create',
            'username' => 'admin',
            'email' => 'admin@mail.ru',
            'password' => '123123',
            '--super-admin' => true
        ));
        $application->run($input, $this->output);

        $this->preMigrate();
        $this->postMigrate();
        
        $this->io->section('Collecting of Structure Data');
        $structure = $this->getStructure($code);

        $this->io->section('Collecting of News Data');
        $news = $this->getNews($code);

        $this->io->section('Collecting of Documents Data');
        $docs = $this->getDocs($code);

        $this->io->section('Collecting of Events Data');
        $events = $this->getEvents($code);

        $this->writeDb($code);
        $this->io->success('------> Migration complete for Main Site!!!');
    }

    public function getDocs($code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $docCategories = [];
        $syncedBefore = [];
        $structureId = $this->structureRoot;
        if($structureId > 0) {
            $sql = "SELECT ID, ORIG_ID FROM STRUCTURE  WHERE ORIG_ID > 0 START WITH ID = " . $structureId . " CONNECT BY PRIOR ID = PARENT_ID";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);

            while (($row = oci_fetch_assoc($stid)) != false) {
                $docCategories[$row['ID']] = $row['ORIG_ID'];
            }
            oci_free_statement($stid);

            if(file_exists($this->rootDir . "web/oracle/{$code}/sync/docs.txt")) {
                $syncedBefore = trim(file_get_contents($this->rootDir . "web/oracle/{$code}/sync/docs.txt"));
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

    public function getStructure($code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $syncedBefore = [];
        $writeMigration = false;
        $beforeText = '';

        $this->structureID = $this->container->get('customer_menu_manager')->findOneBy(['code' => 'structure_menu'])->getId();
        $this->documentTemplateID = $this->container->get('customer_structure_template_manager')->findOneBy(['code' => 'document'])->getId();
        $this->simpleTemplateID = $this->container->get('customer_structure_template_manager')->findOneBy(['code' => 'simple'])->getId();
        $this->newsTemplateID = $this->container->get('customer_structure_template_manager')->findOneBy(['code' => 'article'])->getId();
        $this->photoReportTemplateID = $this->container->get('customer_structure_template_manager')->findOneBy(['code' => 'photo_report'])->getId();
        $this->videoReportTemplateID = $this->container->get('customer_structure_template_manager')->findOneBy(['code' => 'video_report'])->getId();

        $sqlContent = "INSERT INTO menu_node(menu_id, title, node_order, structure_template_id, old_id) 
            VALUES(".$this->structureID.", 'Новости', 0, 2, 1488);"."\r\n";
        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);

        if(file_exists($this->rootDir . "web/oracle/{$code}/sync/structure.txt")) {
            $syncedBefore = trim(file_get_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt"));
            if($syncedBefore !== '') {
                $syncedBefore = explode(' ', $syncedBefore);
            }
        }

        $structureId = $this->structureRoot;
        if($structureId > 0) {
            $sql = "SELECT ID, PARENT_ID, REDIRECT_TO, TITLE, TYPE, NAME, ORIG_ID, HIDE, DRAFT, ORDI FROM STRUCTURE START WITH ID = ".$structureId." CONNECT BY PRIOR ID = PARENT_ID";
            $stid = oci_parse($conn, $sql);
            oci_execute($stid);
            while (($row = oci_fetch_assoc($stid)) != false) {
                $beforeText = '';
                if((int)$row['ID'] !== $structureId && !in_array($row['ID'], $syncedBefore)) {
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

                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
                            VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
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

                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
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

                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 5) {
                        if(!empty($row['TITLE']) && $row['TITLE'] !== '' && !is_null($row['TITLE']) && $row['TITLE']->size()) {
                            $beforeText = $this->cleanString($row['TITLE']->read($row['TITLE']->size()));
                        }
                        $template = $this->simpleTemplateID;

                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 16) {
                        $beforeText = '';
                        $template = $this->photoReportTemplateID;
                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft, old_type_id)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].", 16) ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 15) {
                        $beforeText = '';
                        $template = $this->videoReportTemplateID;
                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft, old_type_id)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].", 16) ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }elseif($row['TYPE'] == 6) {
                        $template = $this->simpleTemplateID;
                        $beforeText = '';
                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
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

                            $sqlContent .= "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($tbl['dep'])."', '".$beforeText."', ".$template.", ".(int)$tbl['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['ID'].", 0, 0) ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                            file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                            file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $tbl['ID'].' ', FILE_APPEND);
                            $writeMigration = true;
                        }
                        oci_free_statement($podved);

                    }else{
                        $template = $this->simpleTemplateID;

                        $sqlContent = "INSERT INTO menu_node(menu_id, node_order, title, before_text, structure_template_id, old_id, orig_id, old_parent_id, hide, draft)
    VALUES(".$this->structureID.", ".(int)$row['ORDI'].", '".$this->cleanString($row['NAME'])."', '".$beforeText."', ".$template.", ".(int)$row['ID'].", ".(int)$row['ORIG_ID'].", ".(int)$row['PARENT_ID'].", ".(int)$row['HIDE'].", ".(int)$row['DRAFT'].") ON CONFLICT(old_id) DO NOTHING;"."\r\n";
                        file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                        file_put_contents($this->rootDir . "web/oracle/{$code}/sync/structure.txt", $row['ID'].' ', FILE_APPEND);
                        $writeMigration = true;
                    }

                }
                $syncedBefore[] = $row['ID'];
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

    public function getNews($code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        //create temporary directories
        $article_dir = $this->rootDir . "web/uploads/{$code}/attachments/articles/";
        $syncedBefore = [];

        //migrate news
        if(file_exists($this->rootDir . "web/oracle/{$code}/sync/news.txt")) {
            $syncedBefore = trim(file_get_contents($this->rootDir . "web/oracle/{$code}/sync/news.txt"));
            if($syncedBefore !== '') {
                $syncedBefore = explode(' ', $syncedBefore);
            }
        }

        $sql = "SELECT ID, TITLE, TEXT, NEWS_DATE, IMAGE_FILE, RATING FROM NEWS WHERE NEWS_CATEGORY_ID = ".$this->newsRoot;
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

    public function getEvents($code)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $syncedBefore = [];

        if(file_exists($this->rootDir . "web/oracle/{$code}/sync/events.txt")) {
            $syncedBefore = trim(file_get_contents($this->rootDir . "web/oracle/{$code}/sync/events.txt"));
            if($syncedBefore !== '') {
                $syncedBefore = explode(' ', $syncedBefore);
            }
        }

        $sql = "SELECT m.ID, m.NAME as measure_name, m.MEASURE_DATE, m.ACC_END, m.PLACE
                FROM MEASURE_CATEGORY mc
                LEFT JOIN MEASURE m ON m.MEASURE_CATEGORY_ID = mc.ID WHERE mc.ID = ".$this->eventsRoot;
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        while (($row = oci_fetch_assoc($stid)) != false) {
            if(!in_array($row['ID'], $syncedBefore)) {
                $sqlContent = "INSERT INTO event(author_id, title, slug, old_id, created_at, published_at, start_date, end_date, place) 
VALUES(1,'".$this->cleanString($row['MEASURE_NAME'])."', '', ".(int)$row['ID'].", '".$this->convertDate($row['MEASURE_DATE'])."', '".$this->convertDate($row['MEASURE_DATE'])."', '".$this->convertDate($row['MEASURE_DATE'])."', '".$this->convertDate($row['ACC_END'])."','".$this->cleanString($row['PLACE'])."');"."\r\n";
                file_put_contents($this->rootDir . "web/oracle/{$code}/migrations/sql/migration.sql", $sqlContent, FILE_APPEND);
                file_put_contents($this->rootDir . "web/oracle/{$code}/sync/events.txt", $row['ID'].' ', FILE_APPEND);
                $syncedBefore[] = $row['ID'];

                $photoReports = $this->getPhotoreports($row['ID'], $code, $row['MEASURE_DATE']);
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

    public function getPhotoreports($measureId, $code, $measureDate)
    {
        $conn = $this->conn;

        $sqlContent = '';
        $photoreport_dir = $this->rootDir . "web/uploads/{$code}/attachments/photoreport/";
        $syncedBefore = [];
        $photoReportCreated = false;

        if(file_exists($this->rootDir . "web/oracle/{$code}/sync/photoreports.txt")) {
            $syncedBefore = trim(file_get_contents($this->rootDir . "web/oracle/{$code}/sync/photoreports.txt"));
            if($syncedBefore !== '') {
                $syncedBefore = explode(' ', $syncedBefore);
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

        if(file_exists($this->rootDir . "web/oracle/{$code}/sync/videoreports.txt")) {
            $syncedBefore = trim(file_get_contents($this->rootDir . "web/oracle/{$code}/sync/videoreports.txt"));
            if($syncedBefore !== '') {
                $syncedBefore = explode(' ', $syncedBefore);
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

    public function convertDate($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

    public function writeDb($code)
    {
        $conn = $this->container->get('doctrine.orm.entity_manager')->getConnection();
        $files[] = $this->webDir . '/oracle/preMigrate.sql';
        $files[] = $this->webDir . '/oracle/' . $code .'/migrations/sql/migration.sql';
        $files[] = $this->webDir . '/oracle/postMigrate.sql';

        foreach($files as $file) {
            $content = file_get_contents($file);
            $queries = explode("\r\n", $content);
            
            foreach($queries as $query) {
                if($query !== '') {
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                }
            }
        }

        $conn->close();
    }

}
