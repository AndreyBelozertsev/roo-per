<?php

namespace Portal\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    
    /**
     * @return \Portal\UserBundle\Model\UserManager
     */
    public function getUserManager()
    {
        return $this->get('user_manager');
    }
    
    /**
     * Get user manager
     * 
     * @return UserManager
     */
    protected function getFOSUserManager()
    {
        return $this->get('fos_user.user_manager');
    }
}
