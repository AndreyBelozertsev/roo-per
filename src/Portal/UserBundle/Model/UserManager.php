<?php

namespace Portal\UserBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\UserBundle\Entity\User;
use Portal\HelperBundle\Helper\PortalHelper;

class UserManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUserRepo()
    {
        return $this->em->getRepository('PortalUserBundle:User');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRoleToUserRepo()
    {
        return $this->em->getRepository('PortalUserBundle:RoleToUser');
    }
    
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ObjectManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param integer $id
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findOneById($id)
    {
        return $this->getUserRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function find($id)
    {
        if ($id) {
            return $this->getUserRepo()->find($id);
        } else {
            return $this->getUserRepo()->findAll();
        }
    }

    /**
     * @return \Portal\UserBundle\Entity\User[]
     */
    public function findAll()
    {
        return $this->getUserRepo()->findAll();
    }
    
    /**
     * @param integer $array
     *
     * @return \Portal\UserBundle\Entity\User
     */
    public function findOneBy($array)
    {
        return $this->getUserRepo()->findOneBy($array);
    }

    /**
     *
     * Create user
     *
     * @param array $properties
     * @return \Portal\UserBundle\Entity\User|null
     */
    public function createUser($properties)
    {
        $user = new User();

        $user->setPassword('1');
        $user->fill($properties);

        // TODO: Remove entity manager's things, native SQL only
        $this->em->persist($user);
        $this->em->flush();

        return $user->getId();
    }

    /**
     * Returns paged user list
     *
     * @param int $page
     * @param int $per_page
     * @return array
     */
    public function getUsers($page, $per_page)
    {
        return $this->getUserRepo()->getUsers($page, $per_page, $this->container->get('database_connection'));
    }
    
    /**
     * Delete User
     *
     * @param integer $userId
     * 
     * @return boolean
     */
    public function deleteUser($userId)
    {
        return $this->getUserRepo()->deleteUser($userId, $this->container->get('database_connection'));
    }

    /**
     * Returns number of pages
     *
     * @param int $per_page
     *
     * @return int
     */
    public function getUserPages($per_page)
    {
        return $this->getUserRepo()->getUserPages($per_page, $this->container->get('database_connection'));
    }
    
    /**
     *  Returns user count
     *
     * @return integer
     */
    public function getUsersCount()
    {
        return $this->getUserRepo()->getUsersCount($this->container->get('database_connection'));
    }
    
    /**
     *  Returns user count
     *
     * @return integer
     */
    public function getUsersInfo()
    {
        return $this->getUserRepo()->getUsersInfo();
    }

    /**
     * @param string $userRole
     *
     * @return mixed
     */
    public function getUsersByRole($userRole)
    {
        return $this->getUserRepo()->getUsersByRole($userRole);
    }

    /**
     * Is Granted
     * @param string $permissionCode
     * @param string $instanceCode
     * @param int $userId
     *
     * @return mixed
     */
    public function isGranted(string $permissionCode, string $instanceCode, int $userId)
    {
        return $this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
            ? 1
            : $this->getRoleToUserRepo()->isGranted($permissionCode, $instanceCode, $userId);
    }
}
