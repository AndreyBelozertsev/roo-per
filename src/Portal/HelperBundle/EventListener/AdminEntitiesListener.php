<?php
// src/Portal/HelperBundle/EventListener/AdminEntitiesListener.php

namespace Portal\HelperBundle\EventListener;

use Portal\ContentBundle\Entity\FeedbackForm;
use Portal\ContentBundle\Entity\MenuNode;
use Portal\ContentBundle\Entity\PhotoReport;
use Portal\ContentBundle\Entity\VideoReport;
use Portal\ContentBundle\Entity\WidgetToPanel;
use Portal\HelperBundle\Entity\AccessLogMessage;
use Portal\HelperBundle\Helper\PortalEntityLogger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//use Doctrine\ORM\Event\LifecycleEventArgs;
// for Doctrine 2.4
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Portal\HelperBundle\Helper\PortalLogger;
use Doctrine\Common\EventSubscriber;

// log entity
use Portal\HelperBundle\Entity\Log;

// entities
use Portal\ContentBundle\Entity\Article;

use FOS\UserBundle\Model\User as FOSUser;

class AdminEntitiesListener implements EventSubscriber
{
    /**
     *
     * @var PortalEntityLogger
     */
    private $entityLogger;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @param PortalEntityLogger $entityLogger
     * @param ContainerInterface $container
     */
    public function __construct(PortalEntityLogger $entityLogger, ContainerInterface $container)
    {
        $this->entityLogger = $entityLogger;
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'postDelete',
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $eventText = "добавлен";
        $this->index($args, $eventText, Log::ACTION_TYPE_CREATE);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $eventText = "обновлен";
        $this->index($args, $eventText, Log::ACTION_TYPE_EDIT);
    }

    public function postDelete(LifecycleEventArgs $args)
    {
        $eventText = "удален";
        $this->index($args, $eventText, Log::ACTION_TYPE_DELETE);
    }

    public function index(LifecycleEventArgs $args, $eventText, $actionType)
    {
        $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($args->getObject());
        $entity = $args->getEntity();
        if (!$entity instanceof Log || !$entity instanceof AccessLogMessage) {
            // TODO: review request -- createFromGlobals
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);

            $token = $this->container->get('security.token_storage')->getToken();
            if ($token !== null) {
                $user = $token->getUser();
                if (is_object($user) && $user instanceof FOSUser) {
                    $userName = $user ? $user->getFullUserName().' ('.$user->getUsername().')' : '';
                    $userId = $user ? $user->getId() : 0;
                    $entityName = '';
                    $suffix = '';
                    $isEntityAction = false;
                    $entityId = null;
                    $entityActionType = null;
                    $entityType = null;
                    $instanceCode = $this->container->getParameter('instance_code');
                    if ($entity instanceof Article) {
                        $entityName = "Новость";
                        $suffix = 'а';
                        $entityType = Log::ENTITY_TYPE_ARTICLE;
                        $entityId = $entity->getId();
                        if ($entity->getOriginalInstanceCode()) {
                            $instanceCode = "main";
                        }
                        $isEntityAction = true;
                    } elseif ($entity instanceof PhotoReport) {
                        $entityName = "Фоторепортаж";
                        $entityType = Log::ENTITY_TYPE_PHOTO_REPORT;
                        $entityId = $entity->getId();
                        $isEntityAction = true;
                    } elseif ($entity instanceof VideoReport) {
                        $entityName = "Видеорепортаж";
                        $entityType = Log::ENTITY_TYPE_VIDEO_REPORT;
                        $entityId = $entity->getId();
                        $isEntityAction = true;
                     } elseif ($entity instanceof FeedbackForm) {
                        $entityName = "Форма обратной связи";
                        $suffix = 'а';
                        $entityType = Log::ENTITY_TYPE_FEEDBACK_FORM;
                        $entityId = $entity->getId();
                        $isEntityAction = true;
                    } elseif ($entity instanceof MenuNode) {
                        $entityName = "Раздел";
                        $entityType = Log::ENTITY_TYPE_STRUCTURE;
                        $entityId = $entity->getId();
                        $isEntityAction = true;
                    } elseif ($entity instanceof  WidgetToPanel) {
                        $entityName = "Виджет";
                        $entityType = Log::ENTITY_TYPE_WIDGET;
                        $entityId = $entity->getId();
                        $isEntityAction = true;
                    }

                    if ($isEntityAction) {
                        $message = "$entityName успешно $eventText$suffix пользователем $userName id: $userId";
                        $this->entityLogger->log($entityType, $entityId, $actionType, $instanceCode, $message, $changes);
                    }
                }
            }
        }
    }
}
