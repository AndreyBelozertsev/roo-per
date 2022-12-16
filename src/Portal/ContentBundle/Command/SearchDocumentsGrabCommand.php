<?php

namespace Portal\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchDocumentsGrabCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('search:documents:grab')
            ->setDescription('Collect documents data from multikernels to search database');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $instances = $container->get('instance_manager')->findAll();
        $rootDir = $container->get('kernel')->getRootDir();
        $siteName = $container->getParameter('site_name');

        foreach( $instances as $instance) {
            $indexedDocumentsId = [];
            $code = $instance->getCode();
            $output->writeln('');
            $output->writeln('# '.mb_strtoupper($code));
            $output->writeln('.... поиск документов');

            $documentsSyncLimit = 0;
            $syncLimits = $container->get('search_manager')->getDocumentsSyncLimits($code);

            if($syncLimits) {
                $documentsSyncLimit = $syncLimits[0]['max'];
            }

            if($code !== 'main') {
                $kernelName = ucfirst($code).'Kernel';
                require_once "{$rootDir}/sites/{$code}/{$kernelName}.php";
                $kernel = new $kernelName('dev', true);
                $kernel->boot();
                $kernelContainer = $kernel->getContainer();

                $newSearchDocumentData = $kernelContainer->get('customer_document_manager')->getAllDocumentsForSearchDocumentGrab($documentsSyncLimit);
                $updateSearchDocumentData = $kernelContainer->get('customer_document_manager')->getAllDocumentsForSearchDocumentUpdate($documentsSyncLimit);

            }else{
                $kernelName = 'AppKernel';
                require_once "{$rootDir}/{$kernelName}.php";
                $kernel = new $kernelName('dev', true);
                $kernel->boot();
                $kernelContainer = $kernel->getContainer();

                $newSearchDocumentData = $kernelContainer->get('document_manager')->getAllDocumentsForSearchDocumentGrab($documentsSyncLimit);
                $updateSearchDocumentData = $kernelContainer->get('document_manager')->getAllDocumentsForSearchDocumentUpdate($documentsSyncLimit);
            }

            $searchData = $searchUpdateData = [];

            if(!empty($newSearchDocumentData)) {
                $output->writeln('.... найдено '. count($newSearchDocumentData) . ' документов для индексирования');
                foreach ($newSearchDocumentData as $document) {

                    $tags = '';
                    $tagsClean = '';

                    if($document['published_at'] == '') {
                        $document['published_at'] = null;
                    }

                    if($document['approval_date'] == '') {
                        $document['approval_date'] = null;
                    }
                    if(!empty($document['tags']) && $document['tags'] !== '') {
                        $tagsClean = '#' . implode(' #', explode('|', $document['tags']));
                        $tags = implode(' ', explode('|', $document['tags']));
                    }

                    if($code !== 'main') {
                        $link = $container->getParameter('protocol')."://{$code}.{$siteName}/document/show/{$document['id']}";
                    }else{
                        $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$document['id']}";

                    }
                    
                    $indexedDocumentsId[] = $document['id'];
                    $searchData[] = [
                        'title' => $document['title'],
                        'full_text' => $document['content'],
                        'entity_id' => $document['id'],
                        'published_at' => $document['published_at'],
                        'approval_date' => $document['approval_date'],
                        'document_number' => $document['document_number'],
                        'document_type' => $document['document_type'] ?? 1,
                        'tags' => $tags,
                        'tags_clean' => $tagsClean,
                        'category_id' => $document['category_id'],
                        'instance_code' => $code,
                        'link' => $link,
                        'is_published' => $document['is_published'],
                        'is_deleted' => $document['is_deleted']
                    ];
                }
            }
            
            if(!empty($updateSearchDocumentData)) {
                $output->writeln('.... найдено '. count($updateSearchDocumentData) . ' документов для обновления индекса');
                foreach ($updateSearchDocumentData as $document) {

                    $tags = '';
                    $tagsClean = '';

                    if($document['published_at'] == '') {
                        $document['published_at'] = null;
                    }

                    if($document['approval_date'] == '') {
                        $document['approval_date'] = null;
                    }
                    if(!empty($document['tags']) && $document['tags'] !== '') {
                        $tagsClean = '#' . implode(' #', explode('|', $document['tags']));
                        $tags = implode(' ', explode('|', $document['tags']));
                    }

                    if($code !== 'main') {
                        $link = $container->getParameter('protocol')."://{$code}.{$siteName}/document/show/{$document['id']}";
                    }else{
                        $link = $container->getParameter('protocol')."://{$siteName}/document/show/{$document['id']}";

                    }

                    $indexedDocumentsId[] = $document['id'];
                    $searchUpdateData[] = [
                        'title' => $document['title'],
                        'full_text' => $document['content'],
                        'entity_id' => $document['id'],
                        'published_at' => $document['published_at'],
                        'approval_date' => $document['approval_date'],
                        'document_number' => $document['document_number'],
                        'document_type' => $document['document_type'] ?? 1,
                        'tags' => $tags,
                        'tags_clean' => $tagsClean,
                        'category_id' => $document['category_id'],
                        'instance_code' => $code,
                        'link' => $link,
                        'is_published' => $document['is_published'],
                        'is_deleted' => $document['is_deleted']
                    ];
                }
            }
            
            if($container->get('search_manager')->addDataDocument($searchData)) {
                $output->writeln('выполнено индексирование новых документов!');
            }
            if($container->get('search_manager')->updateDataDocument($searchUpdateData)) {
                $output->writeln('обновлено индексирование новых документов!');
            }
            $kernelContainer->get('customer_document_manager')->updateDocumentsIsSearchDocumentIndexedFlag($indexedDocumentsId);
        }

        $output->writeln('');
        $output->writeln('###' .mb_strtoupper('Данные успешно мигрированы в поисковую базу данных!'). '###');
    }
}
