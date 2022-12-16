<?php

namespace Portal\ContentBundle\Repository;

/**
 * InterviewQuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InterviewQuestionRepository extends \Doctrine\ORM\EntityRepository
{
    public function getInterviewQuestionList($interviewId)
    {
        $sql = 'SELECT id, content
                FROM interview_question
                WHERE interview_id = ' . (int)$interviewId . '
                ORDER BY id ASC'
        ;
        $dc = $this->getEntityManager()->getConnection();

        return $dc->executeQuery($sql)->fetchAll() ?: [];
    }

    public function getCountQuestion($interviewId)
    {
        $sql = 'SELECT count(iq.id)
                FROM interview_question AS iq
                WHERE iq.interview_id = ' . (int)$interviewId
        ;
        $dc = $this->getEntityManager()->getConnection();

        return $dc->executeQuery($sql)->fetch(\PDO::FETCH_COLUMN) ?: false;
    }
}
