<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendMailOfExpirationPetitionCommand extends ContainerAwareCommand
{
    public $container;
    public $output;
    public $input;
    public $io;

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
            ->setName('petition:send-mail-expiration')
            ->setDescription('Send mail operators of expiration petition');
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

        $departmentIdList = $this->container->get('petition_manager')->getDepartmentWithExpirationPetition();
        if ($departmentIdList) {
            foreach ($departmentIdList as $departmentId) {
                $this->messageSendPetitionExpiration($departmentId);
            }
        }
    }

    /**
     * Send message for operators of petition expiration
     *
     * @param $departmentId
     *
     */
    private function messageSendPetitionExpiration($departmentId)
    {
        $toSendList = $this->container->get('petition_user_profile')->getOperatorEmailList($departmentId);

        $condition = " p.id IN  (
            SELECT p.id
            FROM petition_state AS ps
            LEFT JOIN petition_state_status AS pss ON ps.status_id = pss.id
            LEFT JOIN petition_department AS pd ON ps.department_id = pd.id
            LEFT JOIN petition AS p ON ps.petition_id = p.id

            WHERE
               (
                  (
                     pss.code = 'code_for_registration'
                     AND DATE_PART('day', current_date - ps.created_at) BETWEEN (pss.deadline_days - 3) AND pss.deadline_days 
                     AND ps.is_done IS FALSE
                  ) OR (
                     pss.code <> 'code_for_registration'
                     AND DATE_PART('day', current_date - ps.created_at) BETWEEN (pss.deadline_days - 3) AND pss.deadline_days 
                     OR DATE_PART('day', current_date - ps.execution_date) BETWEEN 0 AND 3
                     AND ps.is_done IS FALSE AND pss.is_finished IS FALSE
                  )
               )
               AND p.is_deleted IS FALSE
               AND pd.is_deleted IS FALSE  AND ps.department_id = 3)";
        $sort = "p.created_at DESC";
        $petitionExpiringList = $this->container->get('doctrine.orm.entity_manager')->getRepository('PortalProfileBundle:Petition')
            ->getPetitionByDepartmentSortBy($condition, $sort, $departmentId);

        $body = $this->container->get('templating')->render('PortalProfileBundle:Operator/PetitionState:email_expiration_petition_message.html.twig',
            [
                'list' => $this->container->get('petition_manager')->getStatisticsExpired(['filterDep' => $departmentId]),
                'petitionExpiringList' => $petitionExpiringList,
            ]
        );
        $subject = $this->container->get('translator')->trans('petition.subject_expiration_petition');
        if (!empty($toSendList)) {
            $mailer = $this->container->get('mailer');
            foreach ($toSendList as $toSend) {
                try {
                    $message = \Swift_Message::newInstance()
                        ->setFrom($this->container->getParameter('mailer_user'))
                        ->setTo($toSend)
                        ->setSubject($subject)
                        ->setBody($body, 'text/html')
                    ;
                    $result = $mailer->send($message);
                    $resultFlush = $mailer->getTransport()->getSpool()->flushQueue($this->container->get('swiftmailer.transport.real'));
                } catch (\Exception $e) {
                }
            }
        }
    }
}
