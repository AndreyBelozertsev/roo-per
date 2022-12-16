<?php

namespace Portal\HelperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{

    private $em;

    public function getEm()
    {
        if (is_null($this->em)) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }
        
    /**
     * @return \Portal\HelperBundle\Helper\UserHelper
     */
    public function getUserHelper()
    {
        return $this->get('user_helper');
    }
    
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
    
    /**
     * Get article manager
     * 
     * @return ArticleManager
     */
    protected function getArticleManager()
    {
        return $this->get('article_manager');
    }
    
    /**
     * Get article manager
     * 
     * @return ArticleManager
     */
    protected function getInstanceManager()
    {
        return $this->get('instance_manager');
    }
    
    /**
     * Get article manager
     * 
     * @return ArticleManager
     */
    protected function getInstanceCategoryManager()
    {
        return $this->get('instance_category_manager');
    }

    /**
     * @return \Portal\UserBundle\Model\UserRoleManager
     */
    public function getUserRoleManager()
    {
        return $this->get('user_role_manager');
    }

    /**
     * @return \Portal\UserBundle\Model\UserPermissionManager
     */
    public function getUserPermissionManager()
    {
        return $this->get('user_permission_manager');
    }
}