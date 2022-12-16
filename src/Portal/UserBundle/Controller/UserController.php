<?php

namespace Portal\UserBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\UserBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Portal\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Get information about user
     *
     * @param Request $userId
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getUserAction($userId)
    {
        $user = $this->getUserManager()->getUserById($userId);

        $status = ($user) ? true : false;

        return PortalHelper::response($status, ($status) ? $user : $this->get('translator')->trans('no_user', array(), 'messages'));
    }

    /**
     * Create user
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addUserAction(Request $request)
    {
        /** @var User $user */
        $user = new User();
        $errors = array();
        
        // Form
        $form = $this->createForm(new RestAddFormType(), $user);

        // Bind Form
        $form->submit(array(
            "firstName" => $request->request->get('firstName'), // It is firstname in server
            "lastName" => $request->request->get('lastName'),
            "middleName" => $request->request->get('middleName'),
            "email" => $request->request->get('email'),
            "job" => $request->request->get('job'),
            "username" => $request->request->get('username'),
            "plainPassword" => array(
                "first" => $request->request->get('password'),
                "second" => $request->request->get('password')
            ),
            "phone" => $request->request->get('phone'),
            "officeNumber" => $request->request->get('officeNumber')
        ));

        // Manual check
        $isValid = $form->isValid();

        // Init Department
        $department = $this->getDepartmentManager()->find($request->request->get('departmentId'));
        if (is_null($department) || $request->request->get('departmentId') === null ||
                $request->request->get('departmentId') == 0
            || $this->getDepartmentManager()->hasChildren($request->request->get('departmentId'))) {
            $isValid = false;
            $errors['departmentId'] = $this->get('translator')->trans('no_depart', array(), 'messages');
        }
 
        if ($isValid) {
            // Init User From the Form
            $user = $form->getData();
            // Set Enabled
            $user->setEnabled(true);

            $user->setDepartment($department);
            // send confirmation
            $token = sha1(uniqid(mt_rand(), true)); // Or whatever you prefer to generate a token

            $user->setConfirmationToken($token);

            // add user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return PortalHelper::response(true, ['userId' => $user->getId()]);
            
        } else {
            foreach ($form->createView()->children as $key => $childrenErrors) {
                if($key == "plainPassword" &&
                       !empty($childrenErrors->children['first']->vars['errors'] &&
                               isset($childrenErrors->children['first']->vars['errors'][0])) ) {
                    $errors["password"] = $childrenErrors->children['first']->vars['errors'][0]->getMessage();
                } elseif (!empty($childrenErrors->vars['errors'])) {
                    if (isset($childrenErrors->vars['errors'][0])) {
                        $errors[$key] = $childrenErrors->vars['errors'][0]->getMessage();
                    }
                }
            }
            // + all errors
            foreach ($form->getErrors() as $error) {
                $errors[] = "Form: " . $error->getMessage();
            }
       
            return PortalHelper::response(false, $errors, true);
        }
    }

    /**
     * Returns complete list when departmentId is 0
     *
     * @param integer $departmentId
     * 
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getUserListAction($departmentId)
    {
        $userList = $this->getUserManager()->getUserListByDepartmentId(
            ($departmentId > 0) ? $departmentId : null
        );
         
        $status = (!$userList && !empty($userList)) ? false : true;
        
        return PortalHelper::response($status, ($status) ? $userList : $this->get('translator')->trans('error', array(), 'messages'));
    }

    /**
     * Returns paged user list | Show deleted users
     *
     * @param int $page
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getUsersAction($page)
    {
        $users = $this->getUserManager()->getUsers(($page > 0) ? $page : 1, self::PER_PAGE);

        $status = (!$users && !empty($users)) ? false : true;

        return PortalHelper::response($status, ($status) ? $users : $this->get('translator')->trans('error', array(), 'messages'));
    }

    /**
     * Login controller. We should sent username/password in POST
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        // User Manager
        $userManager = $this->getFOSUserManager();
        // Find User By Username
        $user = $userManager->findUserByUsername($username);
        if (!$user) {
            // Find User By Email
            $user = $userManager->findUserByEmail($username);
        }
        if (!$user instanceof User) {
            // Error
            return PortalHelper::response(false, $this->get('translator')->trans('no_user', array(), 'messages'));
        }
        // Check Credentials
        if (!$this->checkUserPassword($user, $password)) {
            // Error
            return PortalHelper::response(false, $this->get('translator')->trans('wrong_pass', array(), 'messages'));
        }
        // Everythign is okay, Auth User.
        $this->loginUser($user);
        // Get wsse token
        $token = $this->createBaseWsse($user, $password);
        
        $arrayResponse = array(
            "token" => $token,
            "userId" => $user->getId()
        );
        if ($this->container->get('security.authorization_checker')->isGranted("ROLE_SUPER_ADMIN")) {
            $arrayResponse['role'] = 'super-admin';
        } elseif ($this->container->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) {
            $arrayResponse['role'] = 'admin';
        } elseif ($this->container->get('security.authorization_checker')->isGranted("ROLE_OPERATOR")) {
            $arrayResponse['role'] = 'operator';
        }
        // Success Response
        return PortalHelper::response(true, $arrayResponse);
    }
    
    /**
     * Check user password
     * 
     * @param Portal\UserBundle\Entity\User $user
     * @param string $password
     * 
     * @return boolean
     */
    protected function checkUserPassword(User $user, $password)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        if (!$encoder) {
            return false;
        }
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }
    
    /**
     * Login User
     * 
     * @param Portal\UserBundle\Entity\User $user
     */
    protected function loginUser(User $user)
    {
        $security = $this->get('security.token_storage');
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        $security->setToken($token);
    }
    
    /**
     * Create token
     * 
     * @param Portal\UserBundle\Entity\User $user
     * @param string $palinPassword
     * @param boolean $truePassword
     * 
     * @return string
     * 
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function createBaseWsse(User $user, $palinPassword, $truePassword = true)
    {
        // Get Username
        $username = $user->getUsername();
        // Plain Password (clear)
        $password = $palinPassword;

        $created = date('c');
        $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
        $nonceHigh = base64_encode($nonce);

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        if ($truePassword) {
            // We need to encode password
            $password = $encoder->encodePassword($password, $user->getSalt());
        }
        
        $passwordDigest = base64_encode(sha1($nonce . $created . $password, true));

        // Completed Token
        $token = "UsernameToken Username=\"{$username}\", " .
                "PasswordDigest=\"{$passwordDigest}\", Nonce=\"{$nonceHigh}\","
                . " Created=\"{$created}\"";

        return $token;
    }
    
    /**
     * Logout 
     * 
     * @Security("has_role('ROLE_OPERATOR')")
     */
    public function logoutAction()
    {
        $this->logoutUser();
        
        // Success Response
        return PortalHelper::response(true, "");
    }
    
    /**
     * Logout current user using random token
     * 
     */
    protected function logoutUser()
    {
        $security = $this->get('security.token_storage');
        $token = new AnonymousToken(null, new User());
        $security->setToken($token);
        $this->get('session')->invalidate();
    }
    
    /**
     * Edit User
     * 
     * @param Request $request
     *
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function editUserAction(Request $request)
    {
        // Collect Data
        $userId = $request->request->get('userId');
        $errors = array();

        $userData = array(
            "username" => $request->request->get('username'),
            "firstName" => $request->request->get('firstName'),
            "lastName" => $request->request->get('lastName'),
            "middleName" => $request->request->get('middleName'),
            "email" => $request->request->get('email'),
            "job" => $request->request->get('job'),
            "phone" => $request->request->get('phone'),
            "officeNumber" => $request->request->get('officeNumber')
        );

        if (is_null($userId)) {
            return PortalHelper::response(false, $this->get('translator')->trans('no_user_id', array(), 'messages'));
        }

        $user = $this->getUserManager()->findOneById($userId);

        if (is_null($user)) {
            return PortalHelper::response(false, $this->get('translator')->trans('no_user', array(), 'messages'));
        }

        $form = $this->createForm(new RestEditFormType(), $user);

        // If Won't change this
        if(is_null($userData['firstName'])) {
            $userData['firstName'] = $user->getFirstName();
        } 
        if(is_null($userData['lastName'])) {
            $userData['lastName'] = $user->getLastName();
        }
        if(is_null($userData['email'])) {
            $userData['email'] = $user->getEmail();
        }
        if(is_null($userData['phone'])) {
            $userData['phone'] = $user->getPhone();
        }
        // username can be changed too
        if(is_null($userData['username'])) {
            $userData['username'] = $user->getUsername();
        }

        // If we wont to change Password
        if(!is_null($request->request->get('password'))) {
            $userData['newPassword']['first'] = $request->request->get('password');
            $userData['newPassword']['second'] = $request->request->get('password');
        }

        $form->submit($userData);

        // Manual check
        $isValid = $form->isValid();

        // Init Department
        $departmentId = $request->request->get('departmentId');
        if (is_null($departmentId) || $departmentId == "" || $departmentId == 0) {
            $isValid = false;
            $errors['departmentId'] = $this->get('translator')->trans('select_dep', array(), 'messages');
        }
   
        if(!is_null($request->request->get('password'))) {
            if ((is_null($userData['newPassword']['first']) || is_null($userData['newPassword']['second']))) {
                $isValid = false;
                $errors['password'] = $this->get('translator')->trans('input_pass', array(), 'messages');
            }

            if ($userData['newPassword']['first'] !== $userData['newPassword']['second']) {
                $isValid = false;
                $errors['password'] = $this->get('translator')->trans('pass_not_match', array(), 'messages');
            }
        }

        if ($isValid) {

            // Update user
            $newUser = $form->getData();

            // Init Department
            $department = $this->getDepartmentManager()->find($departmentId);  
            if (is_null($department) || $this->getDepartmentManager()->hasChildren($departmentId)) {
                return PortalHelper::response(false, $this->get('translator')->trans('no_depart', array(), 'messages'), true);
            }
            // Change Department
            $newUser->setDepartment($department);
            
            // Enabled/Disabled             
            if(!is_null($request->request->get('enabled'))) {
                $newUser->setEnabled($request->request->get('enabled'));
            }

            if (!is_null($newUser->newPassword) && 
                    $newUser->newPassword != "") {
                // Change Password
                $user->setPlainPassword($newUser->newPassword);
            }

            // Update User
            $this->getFOSUserManager()->updateUser($newUser);

            // Complete response
            $response = array(
                "userId" => $newUser->getId()
            );
            // If we changed Password, we should update Token
            if (!is_null($request->request->get('password')) && 
                    $request->request->get('password') != "") {
                // Get wsse token
                $token = $this->createBaseWsse($newUser, $request->request->get('password'));
                $response['token'] = $token;
            }
            return PortalHelper::response(true, $response);

        } else {
            foreach ($form->createView()->children as $key => $childrenErrors) {
                if($key == "newPassword" &&
                       !empty($childrenErrors->children['first']->vars['errors']) &&
                        isset($childrenErrors->children['first']->vars['errors'][0])) {
                    $errors["password"] = $childrenErrors->children['first']->vars['errors'][0]->getMessage();
                } elseif (!empty($childrenErrors->vars['errors'])) {
                    if (isset($childrenErrors->vars['errors'][0])) {
                        $errors[$key] = $childrenErrors->vars['errors'][0]->getMessage();
                    }
                }
            }
            // + all errors
            foreach ($form->getErrors() as $error) {
                $errors[] = "Form: " . $error->getMessage();
            }

            return PortalHelper::response(false, $errors, true);
        }
    }
    
    /**
     * Delete User
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteUserAction(Request $request)
    {
        // Collect Data
        $userId = $request->request->get('userId');
        
        if(is_null($userId)) {
            return PortalHelper::response(false, $this->get('translator')->trans('wrong_data', array(), 'messages'));
        }

        try {
            $this->getUserManager()->deleteUser($userId);
        } catch(\Exception $e) {
            return PortalHelper::response(false, $this->get('translator')->trans('cant_del_user', array(), 'messages'));
        }

        return PortalHelper::response(true, []);
    }

    /**
     * Returns number of pages
     *
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function getUserPagesAction()
    {
        $pages = $this->getUserManager()->getUserPages(self::PER_PAGE);

        return PortalHelper::response(true, ['pages' => $pages]);
    }

    /**
     * Searches for users
     *
     *  @param Request $request
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function lookForUsersAction(Request $request)
    {
        $queryString = $request->get('term');
        $departmentId = $request->get('departmentId');

        if(is_null($queryString))
            return PortalHelper::response(false, $this->get('translator')->trans('empty_query_string', array(), 'messages'));

        $users = $this->getUserManager()->lookForUsers($queryString, $departmentId);

        return new JsonResponse($users, Response::HTTP_OK, ['Access-Control-Allow-Origin' => '*']);
    }

    /**
     * 
     * Autocomplete Search By Name (For View All Users)
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function autocompleteViewAllUsersSearchAction(Request $request)
    {
        $status = true;
        $q = $request->get('term');
        if (is_null($q) || $q == "") {
            $status = false;
        }
        $results = array();
        $userList = $this->getUserManager()->searchByFullName($q);
        if (is_null($userList)) {
            $status = false;
        }
        if ($status) {
            // Collect Autocomplete Response
            foreach ($userList as $user) {
                $userObj = array(
                    "id" => $user['id'],
                    "value" => $user['fullname'],
                    "label" => $user['fullname']
                );
                $results[] = $userObj;
            }
        }
        
        return new JsonResponse($results, Response::HTTP_OK, ['Access-Control-Allow-Origin' => '*']);
    }
}
