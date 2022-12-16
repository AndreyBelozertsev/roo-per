<?php

namespace Portal\HelperBundle\Repository;


class AccessLogMessageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Creates log message
     *
     * @param int $userId
     * @param int $timestamp
     * @param string $clientIp
     * @param string $userAgent
     * @param string $uri
     * @param string $controllerName
     * @param string $actionName
     * @param string $requestData
     * @param int $statusCode
     * @param string $responseMessage
     * @param \Doctrine\DBAL\Connection $dc
     * @return bool
     */
    public function createMessage(
        $userId,
        $timestamp,
        $clientIp,
        $userAgent,
        $uri,
        $controllerName,
        $actionName,
        $requestData,
        $statusCode,
        $responseMessage)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'INSERT INTO portal_access_log (user_id, date, ip_address, client, uri, ' .
            'controller, method, request_data, response_code, message) ' .
            'VALUES (' .
            (is_null($userId) ? 'NULL' : $dc->quote($userId, \PDO::PARAM_INT)) . ', ' .
            $dc->quote($timestamp, \PDO::PARAM_STR) . ', ' .
            $dc->quote($clientIp, \PDO::PARAM_STR) . ', ' .
            $dc->quote($userAgent, \PDO::PARAM_STR) . ', ' .
            $dc->quote($uri, \PDO::PARAM_STR) . ', ' .
            $dc->quote($controllerName, \PDO::PARAM_STR) . ', ' .
            $dc->quote($actionName, \PDO::PARAM_STR) . ', ' .
            $dc->quote($requestData, \PDO::PARAM_STR) . ', ' .
            $dc->quote($statusCode, \PDO::PARAM_INT) . ', ' .
            $dc->quote($responseMessage, \PDO::PARAM_STR) . ')';

        $result = $dc->exec($sql);

        return $result > 0;
    }

    /**
     * @param integer $userId
     * @return array
     */
    public function getLastUrls($userId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT uri'
            . ' FROM portal_access_log'
            . ' WHERE user_id = ' . (int)$userId . ' AND response_code = 200 AND controller != \'\''
            . ' ORDER BY id DESC'
            . ' LIMIT 10'
        ;

        return $dc->fetchAll($sql);
    }
}
