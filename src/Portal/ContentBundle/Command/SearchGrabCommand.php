<?php

namespace Portal\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchGrabCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('search:grab')
            ->setDescription('Collect data from multikernels to search database');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $instances = $container->get('instance_manager')->findAll();
        $rootDir = $container->get('kernel')->getRootDir();
        $siteNameMain = $container->getParameter('site_name');

        foreach ($instances as $instance) {
            $code = $instance->getCode();
            $indexedArticlesId = [];
            $indexedDocumentsId = [];
            $indexedPhotoReportsId = [];
            $indexedEventsId = [];
            $indexedMenuNodesId = [];

            $output->writeln('');
            $output->writeln('# ' . mb_strtoupper($code));
            $output->writeln('.... синхронизация');

            $articleSyncLimit = 0;
            $documentsSyncLimit = 0;
            $photoReportSyncLimit = 0;
            $eventSyncLimit = 0;
            $structureSyncLimit = 0;

            $syncLimits = $container->get('search_manager')->getSyncLimits($code);
            if ($syncLimits) {
                foreach ($syncLimits as $limit) {
                    if ($limit['entity_type'] == 'article') {
                        $articleSyncLimit = $limit['max'];
                    } elseif ($limit['entity_type'] == 'document') {
                        $documentsSyncLimit = $limit['max'];
                    } elseif ($limit['entity_type'] == 'photoreport') {
                        $photoReportSyncLimit = $limit['max'];
                    } elseif ($limit['entity_type'] == 'structure') {
                        $structureSyncLimit = $limit['max'];
                    } else {
                        $eventSyncLimit = $limit['max'];
                    }
                }
            }

            if ($code !== 'main') {
                $kernelName = ucfirst($code) . 'Kernel';
                require_once "{$rootDir}/sites/{$code}/{$kernelName}.php";
                $kernel = new $kernelName('dev', true);
                $kernel->boot();
                $kernelContainer = $kernel->getContainer();

                $newSearchArticleData = $kernelContainer->get('customer_article_manager')->getAllArticlesForSearchGrab($articleSyncLimit);
                $newSearchDocumentData = $kernelContainer->get('customer_document_manager')->getAllDocumentsForSearchGrab($documentsSyncLimit);
                $newSearchPhotoreportData = $kernelContainer->get('customer_photo_report_manager')->getAllPhotoReportForSearchGrab($photoReportSyncLimit);
                $newSearchEventData = $kernelContainer->get('customer_event_manager')->getAllEventsForSearchGrab($eventSyncLimit);
                $newSearchStructureData = $kernelContainer->get('customer_menu_node_manager')->getAllStructureForSearchGrab($structureSyncLimit);
                
                $updateSearchArticleData = $kernelContainer->get('customer_article_manager')->getAllArticlesForSearchUpdate($articleSyncLimit);
                $updateSearchDocumentData = $kernelContainer->get('customer_document_manager')->getAllDocumentsForSearchUpdate($documentsSyncLimit);
                $updateSearchPhotoreportData = $kernelContainer->get('customer_photo_report_manager')->getAllPhotoReportForSearchUpdate($photoReportSyncLimit);
                $updateSearchEventData = $kernelContainer->get('customer_event_manager')->getAllEventsForSearchUpdate($eventSyncLimit);
                $updateSearchStructureData = $kernelContainer->get('customer_menu_node_manager')->getAllStructureForSearchUpdate($structureSyncLimit);
                
                $siteName = $code . '.' . $siteNameMain;
            } else {
                $kernelName = 'AppKernel';
                require_once "{$rootDir}/{$kernelName}.php";
                $kernel = new $kernelName('dev', true);
                $kernel->boot();
                $kernelContainer = $kernel->getContainer();

                $newSearchArticleData = $kernelContainer->get('article_manager')->getAllArticlesForSearchGrabMain($articleSyncLimit);
                $newSearchDocumentData = $kernelContainer->get('document_manager')->getAllDocumentsForSearchGrab($documentsSyncLimit);
                $newSearchPhotoreportData = $kernelContainer->get('photo_report_manager')->getAllPhotoReportForSearchGrab($photoReportSyncLimit);
                $newSearchEventData = $kernelContainer->get('event_manager')->getAllEventsForSearchGrab($eventSyncLimit);
                $newSearchStructureData = $kernelContainer->get('menu_node_manager')->getAllStructureForSearchGrab($structureSyncLimit);
                
                $updateSearchArticleData = $kernelContainer->get('article_manager')->getAllArticlesForSearchUpdateMain($articleSyncLimit);
                $updateSearchDocumentData = $kernelContainer->get('document_manager')->getAllDocumentsForSearchUpdate($documentsSyncLimit);
                $updateSearchPhotoreportData = $kernelContainer->get('photo_report_manager')->getAllPhotoReportForSearchUpdate($photoReportSyncLimit);
                $updateSearchEventData = $kernelContainer->get('event_manager')->getAllEventsForSearchUpdate($eventSyncLimit);
                $updateSearchStructureData = $kernelContainer->get('menu_node_manager')->getAllStructureForSearchUpdate($structureSyncLimit);
                
                $siteName = $siteNameMain;
            }

            $searchData = $searchUpdateData = [];

            // article
            if (!empty($newSearchArticleData)) {
                $output->writeln('.... найдено ' . count($newSearchArticleData) . ' новостей');
                foreach ($newSearchArticleData as $article) {

                    if ($article['published_at'] == '') {
                        $article['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($article['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/article/show/{$article['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/article/show/{$article['id']}";
                    }

                    $indexedArticlesId[] = $article['id'];
                    $searchData[] = [
                        'title' => str_replace('ё', 'е', $article['title']),
                        'full_text' => str_replace('ё', 'е', $article['content']),
                        'id' => $article['id'],
                        'published_at' => $article['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'article',
                        'link' => $link
                    ];
                }
            }
            if (!empty($updateSearchArticleData)) {
                $output->writeln('.... найдено ' . count($updateSearchArticleData) . ' новостей  для обновления');
                foreach ($updateSearchArticleData as $article) {

                    if ($article['published_at'] == '') {
                        $article['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($article['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/article/show/{$article['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/article/show/{$article['id']}";
                    }

                    $indexedArticlesId[] = $article['id'];
                    $searchUpdateData[] = [
                        'title' => str_replace('ё', 'е', $article['title']),
                        'full_text' => str_replace('ё', 'е', $article['content']),
                        'id' => $article['id'],
                        'published_at' => $article['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'article',
                        'link' => $link
                    ];
                }
            }

            // document
            if (!empty($newSearchDocumentData)) {
                $output->writeln('.... найдено ' . count($newSearchDocumentData) . ' документов');
                foreach ($newSearchDocumentData as $document) {

                    if ($document['published_at'] == '') {
                        $document['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($document['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$document['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$document['id']}";
                    }

                    $indexedDocumentsId[] = $document['id'];
                    $searchData[] = [
                        'title' => str_replace('ё', 'е', $document['title']),
                        'full_text' => str_replace('ё', 'е', $document['content']),
                        'id' => $document['id'],
                        'published_at' => $document['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'document',
                        'link' => $link
                    ];
                }
            }
            if (!empty($updateSearchDocumentData)) {
                $output->writeln('.... найдено ' . count($updateSearchDocumentData) . ' документов  для обновления');
                foreach ($updateSearchDocumentData as $document) {

                    if ($document['published_at'] == '') {
                        $document['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($document['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$document['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$document['id']}";
                    }

                    $indexedDocumentsId[] = $document['id'];
                    $searchUpdateData[] = [
                        'title' => str_replace('ё', 'е', $document['title']),
                        'full_text' => str_replace('ё', 'е', $document['content']),
                        'id' => $document['id'],
                        'published_at' => $document['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'document',
                        'link' => $link
                    ];
                }
            }
            
            // photoreport
            if (!empty($newSearchPhotoreportData)) {
                $output->writeln('.... найдено ' . count($newSearchPhotoreportData) . ' фоторепортажей');
                foreach ($newSearchPhotoreportData as $photoreport) {

                    if ($photoreport['published_at'] == '') {
                        $photoreport['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$photoreport['id']}";

                    $indexedPhotoReportsId[] = $photoreport['id'];
                    $searchData[] = [
                        'title' => str_replace('ё', 'е', $photoreport['title']),
                        'full_text' => str_replace('ё', 'е', $photoreport['description']),
                        'id' => $photoreport['id'],
                        'published_at' => $photoreport['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'photoreport',
                        'link' => $link
                    ];
                }
            }
            if (!empty($updateSearchPhotoreportData)) {
                $output->writeln('.... найдено ' . count($updateSearchPhotoreportData) . ' фоторепортажей для обновления');
                foreach ($updateSearchPhotoreportData as $photoreport) {

                    if ($photoreport['published_at'] == '') {
                        $photoreport['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$photoreport['id']}";

                    $indexedPhotoReportsId[] = $photoreport['id'];
                    $searchUpdateData[] = [
                        'title' => str_replace('ё', 'е', $photoreport['title']),
                        'full_text' => str_replace('ё', 'е', $photoreport['description']),
                        'id' => $photoreport['id'],
                        'published_at' => $photoreport['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'photoreport',
                        'link' => $link
                    ];
                }
            }

            // event
            if (!empty($newSearchEventData)) {
                $output->writeln('.... найдено ' . count($newSearchEventData) . ' мероприятий');
                foreach ($newSearchEventData as $event) {

                    if ($event['published_at'] == '') {
                        $event['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($event['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/event/show/{$event['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/event/show/{$event['id']}";
                    }

                    $indexedEventsId[] = $event['id'];
                    $searchData[] = [
                        'title' => str_replace('ё', 'е', $event['title']),
                        'full_text' => str_replace('ё', 'е', $event['content']),
                        'id' => $event['id'],
                        'published_at' => $event['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'event',
                        'link' => $link
                    ];
                }
            }
            if (!empty($updateSearchEventData)) {
                $output->writeln('.... найдено ' . count($updateSearchEventData) . ' мероприятий для обновления');
                foreach ($updateSearchEventData as $event) {

                    if ($event['published_at'] == '') {
                        $event['published_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($event['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/event/show/{$event['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/event/show/{$event['id']}";
                    }

                    $indexedEventsId[] = $event['id'];
                    $searchUpdateData[] = [
                        'title' => str_replace('ё', 'е', $event['title']),
                        'full_text' => str_replace('ё', 'е', $event['content']),
                        'id' => $event['id'],
                        'published_at' => $event['published_at'],
                        'instance_code' => $code,
                        'entity_type' => 'event',
                        'link' => $link
                    ];
                }
            }

            // structure (menu_node)
            if (!empty($newSearchStructureData)) {
                $output->writeln('.... найдено ' . count($newSearchStructureData) . ' разделов');
                foreach ($newSearchStructureData as $item) {
                    if ($item['created_at'] == '') {
                        $item['created_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($item['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/structure/{$item['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/structure/{$item['id']}";
                    }

                    if (!empty($item['route'])) {
                        if (mb_stripos($item['route'], 'http') !== false) {
                            $link = $item['route'];
                        } else {
                            $link = $container->getParameter('protocol')."://{$siteName}{$item['route']}";
                        }
                    }

                    $indexedMenuNodesId[] = $item['id'];
                    $content = $item['before_text'] . ' ' . $item['after_text'];
                    $searchData[] = [
                        'title' => str_replace('ё', 'е', $item['title']),
                        'full_text' => str_replace('ё', 'е', $content),
                        'id' => $item['id'],
                        'published_at' => $item['created_at'],
                        'instance_code' => $code,
                        'entity_type' => 'structure',
                        'link' => $link
                    ];
                }
            }
            if (!empty($updateSearchStructureData)) {
                $output->writeln('.... найдено ' . count($updateSearchStructureData) . ' разделов для обновления');
                foreach ($updateSearchStructureData as $item) {
                    if ($item['created_at'] == '') {
                        $item['created_at'] = date('Y-m-d H:i:s', time());
                    }

                    if (!empty($item['slug'])) {
                        $link = $container->getParameter('protocol')."://{$siteName}/structure/{$item['slug']}";
                    } else {
                        $link = $container->getParameter('protocol')."://{$siteName}/structure/{$item['id']}";
                    }

                    if (!empty($item['route'])) {
                        if (mb_stripos($item['route'], 'http') !== false) {
                            $link = $item['route'];
                        } else {
                            $link = $container->getParameter('protocol')."://{$siteName}{$item['route']}";
                        }
                    }

                    $indexedMenuNodesId[] = $item['id'];
                    $content = $item['before_text'] . ' ' . $item['after_text'];
                    $searchUpdateData[] = [
                        'title' => str_replace('ё', 'е', $item['title']),
                        'full_text' => str_replace('ё', 'е', $content),
                        'id' => $item['id'],
                        'published_at' => $item['created_at'],
                        'instance_code' => $code,
                        'entity_type' => 'structure',
                        'link' => $link
                    ];
                }
            }

            if ($container->get('search_manager')->addData($searchData)) {
                $output->writeln('Данные успешно проиндексированы!');
            }
            if ($container->get('search_manager')->updateData($searchUpdateData)) {
                $output->writeln('Данные успешно переиндексированы!');
            }
            
            $kernelContainer->get('customer_article_manager')->updateIsSearchIndexedFlag($indexedArticlesId);
            $kernelContainer->get('customer_document_manager')->updateDocumentsIsSearchIndexedFlag($indexedDocumentsId);
            $kernelContainer->get('customer_photo_report_manager')->updateIsSearchIndexedFlag($indexedPhotoReportsId);
            $kernelContainer->get('customer_event_manager')->updateIsSearchIndexedFlag($indexedEventsId);
            $kernelContainer->get('customer_menu_node_manager')->updateIsSearchIndexedFlag($indexedMenuNodesId);
        }
        

        $output->writeln('Данные успешно мигрированы в поисковую базу данных!');
    }
}
