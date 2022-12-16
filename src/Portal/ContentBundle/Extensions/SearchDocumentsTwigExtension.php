<?php

namespace Portal\ContentBundle\Extensions;
use Gedmo\Translator\Translation;
use Portal\ContentBundle\Entity\Document;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SearchDocumentsTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class SearchDocumentsTwigExtension extends \Twig_Extension
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('searchForm', array($this, 'searchForm'))
        ];
    }

    public function searchForm($data = array())
    {
        $params['tags'] = $this->container->get('tag_manager')->getAllDocumentsTags();
        $params['data'] = $data;

        foreach(Document::DOC_TYPES as $key => $value) {
            $params['document_types'][$key] = $this->container->get('translator')->trans($value, [], 'messages');
        }

        return $this->container->get('templating')->render('PortalContentBundle:Search:searchForm.html.twig', $params);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'search_documents';
    }
}
