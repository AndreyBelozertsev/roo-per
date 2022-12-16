<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewsletterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('portal:newsletter')
            ->setDescription('Newsletter latest news');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        // get emails grouped by
        $emailGroup = $container->get('article_subscribe_manager')->getGroupedEmails();

        // get data from all used instances (id, title)
        $instArr = implode(',', array_column($emailGroup, 'instances'));
        $instanceIds = array_unique(explode(',', $instArr));
        $instances = [];
        foreach ($container->get('instance_manager')->getInstanceDataByIds($instanceIds) as $i) {
            $instances[$i['id']] = $i['title'];
        }

        // get today articles grouped by instances
        $articles = [];
        $todayArticles = $container->get('search_manager')->getTodayArticless();
        foreach ($todayArticles as $a) {
            $instance = $container->get('instance_manager')->findOneBy(['code' => $a['instanceCode']]);
            if ($instance instanceof \Portal\AdminBundle\Entity\Instance) {
                $instanceId = $instance->getId();
                $articles[$instanceId][] = $a;
            }
        }

        // for each subscriber
        foreach ($emailGroup as $e) {
            // choose the necessary groups of articles
            $usedArticles = array_intersect_key($articles, array_flip(explode(',', $e['instances'])));
            if (count($usedArticles) !== 0) {
                // choose the necessary instances
                $usedInstances = array_intersect_key($instances, array_flip(explode(',', $e['instances'])));
                $body = $container->get('templating')->render('PortalContentBundle:Newsletter:newsletter.html.twig', [
                    'articles' => $usedArticles,
                    'instances' => $usedInstances,
                    'hash' => $e['uid'],
                    'site_name' => $container->getParameter('site_name')
                ]);

                $message = \Swift_Message::newInstance()
                    ->setSubject($container->get('translator')->trans('subscribe.subject'))
                    ->setFrom($container->getParameter('mailer_user'))
                    ->setTo(explode(',', $e['email']))
                    ->setBody($body, 'text/html');
                $container->get('mailer')->send($message);

                $output->writeln('mail sent to:' . $e['email']);
            } else {
                $output->writeln('no articles for:' . $e['email']);
            }
        }
    }
}
