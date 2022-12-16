<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\AdminBundle\Form\InstanceSiteNameFormType;
use Portal\HelperBundle\Controller\Controller as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\AdminBundle\Entity\Instance;
use Portal\AdminBundle\Form\InstanceFormType;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\ContentBundle\Entity\Attachment;

// console
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArgvInput;

use Symfony\Component\Yaml\Yaml;

class InstanceAdminController extends Controller
{
    public function viewAllAction()
    {
        $currentUser = $this->getUserHelper()->getCurrentUser();
        // check for super-admin
        // only super-admin has access to this action
        $authorizationChecker = $this->container->get('security.authorization_checker');
        if ($authorizationChecker->isGranted("ROLE_SUPER_ADMIN")) {
            // TO DO: find All without main instance
            $listInstance = $this->getInstanceManager()->findAll();
            return $this->render('PortalAdminBundle:InstanceAdmin:viewAll.html.twig', array('listInstance' => $listInstance));
        } else {
            throw $this->createNotFoundException('Not exist');
        }
    }

    public function editAction(Request $request, $instanceId)
    {
        $newInstance = false;
        $currentKernel = $this->get('kernel');
//        $currentUser = $this->container->get('user_helper')->getCurrentUser();
        $instance = $this->get('instance_manager')->findOneById($instanceId);
        if (!$instance instanceof Instance) {
            $instance = new Instance();
            $newInstance = true;
            $validation_groups = ['add'];
        } else {
            $validation_groups = ['edit'];
        }

        $authorizationChecker = $this->container->get('security.authorization_checker');
        if ($authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $instanceCategoryList = $this->get('instance_category_manager')->findAll();
        } else {
            $instanceCategoryList = $this->get('instance_category_manager')->findAll();
        };
        // todo: find all without current
        $parentInstanceList = $this->get('instance_manager')->findAll();

        $form = $this->createForm(InstanceFormType::class, $instance, [
            'newInstance' => $newInstance,
            'instanceCategoryList' => $instanceCategoryList,
            'instanceList' => $parentInstanceList,
            'validation_groups' => $validation_groups,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($newInstance) {
                $instanceCode = $instance->getCode();
                $instance->setDomain($instanceCode . '.' . $_SERVER['SERVER_NAME']);

                // make new config dir
                if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/../app/sites/' . $instanceCode)) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/../app/sites/' . $instanceCode, 0777, true);
                }

                $sourceConfig = $_SERVER['DOCUMENT_ROOT'] . '/../app/config';
                $destinationConfig = $_SERVER['DOCUMENT_ROOT'] . '/../app/sites/' . $instanceCode . '/config';
                if (!is_dir($destinationConfig)) {
                    mkdir($destinationConfig, 0777);
                }

                // make symlinks to all config files
                PortalHelper::makeSymlinksFolderContents($sourceConfig, $destinationConfig);

                // make new AppConfig.php file
                $sourceKernel = $currentKernel->getProjectDir() . '/app/AppKernelSub.php';
                $instanceKernelName = ucfirst($instanceCode) . 'Kernel.php';
                $destinationKernel = $currentKernel->getProjectDir() . "/app/sites/{$instanceCode}/$instanceKernelName";
                if (!is_file($destinationKernel)) {
                    copy($sourceKernel, $destinationKernel);
                }
                $kernelContent = file_get_contents($destinationKernel);
                $kernelContent = str_replace('AppKernelSub', ucfirst($instanceCode) . 'Kernel', $kernelContent);
                file_put_contents($destinationKernel, $kernelContent);

                // make new front controllers
                $dirSource = $_SERVER['DOCUMENT_ROOT'];
                $dirDestination = $_SERVER['DOCUMENT_ROOT'] . '/fc/' . $instanceCode;
                if (!is_dir($dirDestination)) {
                    mkdir($dirDestination, 0777, true);
                }

                if (!is_file($dirDestination . '/app.php')) {
                    copy($dirSource . '/app_sub.php', $dirDestination . '/app.php');
                }
                $appContent = file_get_contents($dirDestination . '/app.php');
                $appContent = str_replace('subdomainfolder', $instanceCode, $appContent);
                $appContent = str_replace('subdomainkernel', ucfirst($instanceCode) . 'Kernel', $appContent);
                file_put_contents($dirDestination . '/app.php', $appContent);

                if (!is_file($dirDestination . '/app_dev.php')) {
                    copy($dirSource . '/app_dev_sub.php', $dirDestination . '/app_dev.php');
                }
                $appDevContent = file_get_contents($dirDestination . '/app_dev.php');
                $appDevContent = str_replace('subdomainfolder', $instanceCode, $appDevContent);
                $appDevContent = str_replace('subdomainkernel', ucfirst($instanceCode) . 'Kernel', $appDevContent);
                file_put_contents($dirDestination . '/app_dev.php', $appDevContent);

                if (is_link($dirDestination . '/bundles')) {
                    unlink($dirDestination . '/bundles');
                }
                symlink($dirSource . '/bundles', $dirDestination . '/bundles');
                if (is_link($dirDestination . '/uploads')) {
                    unlink($dirDestination . '/uploads');
                }
                symlink($dirSource . '/uploads', $dirDestination . '/uploads');
                if (is_link($dirDestination . '/themes')) {
                    unlink($dirDestination . '/themes');
                }
                symlink($dirSource . '/themes', $dirDestination . '/themes');
                if (is_link($dirDestination . '/file')) {
                    unlink($dirDestination . '/file');
                }
                symlink($dirSource . '/file', $dirDestination . '/file');
                if (!is_dir($dirDestination . '/rus')) {
                    mkdir($dirDestination . '/rus', 0777);
                }
                if (is_link($dirDestination . '/rus/file')) {
                    unlink($dirDestination . '/rus/file');
                }
                symlink($dirSource . '/file', $dirDestination . '/rus/file');
                if (is_link($dirDestination . '/robots.txt')) {
                    unlink($dirDestination . '/robots.txt');
                }
                symlink($dirSource . '/robots.txt', $dirDestination . '/robots.txt');
//                // make new nginx config files
////                copy("/etc/nginx/sites-enabled/portal", "/app.php");
//
//                $siteName = $this->getParameter('site_name');
//
//
//
//$configText = <<<EOT
//server {
//    listen 80;
//    listen [::]:80;
//
//    root /home/dididim/public_html/portal-symfony/web;
//    index index.php index.html index.htm;
//
//    # Make site accessible from http://localhost/
//    server_name $instanceCode.$siteName;
//
//    # strip app.php/ prefix if it is present
//    rewrite ^/app\.php/?(.*)$ /$1 permanent;
//
//    location / {
//            index /fc/$instanceCode/app.php;
//            try_files \$uri @rewriteapp;
//    }
//
//    location @rewriteapp {
//            rewrite ^(.*)$ /fc/$instanceCode/app.php/$1 last;
//    }
//
//    location ~ ^/(app|app_dev|config)\.php(/|$) {
//            fastcgi_pass unix:/var/run/php7.1-fpm.sock;
//            fastcgi_split_path_info ^(.+\.php)(/.*)$;
//            include fastcgi_params;
//            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
//            fastcgi_param HTTPS off;
//    }
//}
//
//EOT;
//
//                $configInstanceFilename = "/etc/nginx/sites-available/portal-$instanceCode";
//
//                file_put_contents($configInstanceFilename, $configText);

                // create site uploads folder
                $destinationUploads = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $instanceCode . '/';
                if (!is_dir($destinationUploads)) {
                    mkdir($destinationUploads, 0777);
                    // Crutch on local development
                    chmod($destinationUploads, 0777);
                    foreach (Attachment::$PATH_LIST as $pathDir) {
                        mkdir($destinationUploads . Attachment::ATTACHMENTS_DIR . $pathDir, 0777, true);
//                        chmod($destinationUploads . Attachment::ATTACHMENTS_DIR . $pathDir, 0777);
                    }
                }

                // write to a hosts file
//                file_put_contents('/etc/hosts', '127.0.0.1 ' . $instanceCode . '.' . $this->getParameter('site_name') . "\n\r", FILE_APPEND);

                // add params to parameters.yml
                $parametersYml = Yaml::parse(file_get_contents($sourceConfig . '/parameters.yml'));
                $parametersYml['parameters']['database_name2'] = Instance::PREFIX_DATABASE_DEFAULT . $instanceCode;
                $parametersYml['parameters']['instance_code'] = $instanceCode;
                $parametersYml['parameters']['migration_dir'] = $currentKernel->getProjectDir() . '/app/DoctrineMigrations';
                file_put_contents($destinationConfig . '/parameters.yml', Yaml::dump($parametersYml));

                // get new kernel with new config

                require $destinationKernel;
//                require __DIR__. "/../../../../app/sites/{$instanceCode}/{$instanceKernelName}";
//                echo "<pre>";
//                var_dump(__DIR__. "/../../../../app/sites/{$instanceCode}/{$instanceKernelName}", $instanceKernelName);
//                echo "</pre>";
//                die;

                $currentEnvironment = $currentKernel->getEnvironment();
                $instanceKernelClass = ucfirst($instanceCode) . 'Kernel';
//                $kernel = new \AppKernel('prod', true, $instanceCode);
//                $kernel = new $instanceKernelName($currentEnvironment, true);
                $kernel = new $instanceKernelClass($currentEnvironment, true);

                $kernel->boot();
                $application = new Application($kernel);
                $application->setAutoExit(false);

                // create DB
                $input = new ArrayInput([
                    'command' => 'doctrine:database:create',
                    '--connection' => 'customer'
                ]);
                $output = new BufferedOutput();
                $application->run($input, $output);

//                // exclude some migrate version
//                foreach (Instance::$SKIP_MIGRATION_VERSION_LIST as $migrationVersion) {
//                    $application = new Application($kernel);
//                    $application->setAutoExit(false);
//                    $input = new ArrayInput([
//                        'command' => 'doctrine:migrations:version',
//                        'version' => $migrationVersion,
//                        '--add' => true,
//                        '--em' => 'customer'
//                    ]);
//                    $output = new BufferedOutput();
//                    $application->run($input, $output);
//                }

                // migrate
                $input = new ArrayInput([
                    'command' => 'doctrine:migrations:migrate',
                    '--em' => 'customer'
                ]);
                $output = new BufferedOutput();
                $application->run($input, $output);
            }

//            echo "<pre>";
//            var_dump($output->fetch());
//            echo "</pre>";
//            die;

            $em = $this->getDoctrine()->getManager();
            $em->persist($instance);
            $em->flush();

            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

            return $this->redirect($this->generateUrl('admin_admin_instance_edit', [
                'instanceId' => $instance->getId()
            ]));
        }

        return $this->render('PortalAdminBundle:InstanceAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'add' => $newInstance,
            'instanceId' => $instanceId
        ]);
    }

    /**
     * remove instance
     *
     * @param Request $request
     * @param integer $instanceId
     * @return JsonResponse
     */
    public function removeAction(Request $request, $instanceId)
    {
        // Response
        $response = [
            'status' => false,
            'message' => '',
            'redirectUrl' => $request->headers->get('referer')
        ];

        $authChecker = $this->container->get('security.authorization_checker');
        if (!$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $response['status'] = false;
            $response['message'] = $this->get('translator')->trans('instance_form.not_granted');

            return new JsonResponse($response);
        }

        $instance = $this->get('instance_manager')->findOneById($instanceId);
        if (!$instance instanceof Instance) {
            // check for exists
            $resultResponse['message'] = $this->get('translator')->trans('instance_form.already_deleted');
        } else {
            try {
                // Drop DB
                $kernel = new \AppKernel('prod', true, $instance->getCode());
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $input = new ArrayInput([
                    'command' => 'doctrine:database:drop',
                    '--connection' => 'customer',
                    '--force' => true
                ]);
                $output = new BufferedOutput();
                $application->run($input, $output);

                $em = $this->getDoctrine()->getManager();
                $em->remove($instance);
                $em->flush();

                // remove config dir
                $siteConfigDir = $_SERVER['DOCUMENT_ROOT'] . '/../app/sites/' . $instance->getCode();
                PortalHelper::removeFolderWithContents($siteConfigDir);
                // remove uploads dir
                $siteUploadsDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $instance->getCode();
                PortalHelper::removeFolderWithContents($siteUploadsDir);
                // remove fc dir
                $siteFCDir = $_SERVER['DOCUMENT_ROOT'] . '/fc/' . $instance->getCode();
                PortalHelper::removeFolderWithContents($siteFCDir);

//                // remove site from hosts file
//                $sample = $instance->getCode().'.'.$this->getParameter('site_name');
//                $arr = file('/etc/hosts');
//                $handle = @fopen("/etc/hosts", "w+");
//                if ($handle) {
//                    foreach ($arr as $string) {
//                        if (strpos($string, $sample) === false) {
//                            fwrite($handle, $string);
//                        }
//                    }
//                    fclose($handle);
//                }

                $response['message'] = $this->get('translator')->trans('instance_form.successfully_delete');
                $response['status'] = true;
            } catch (DBALException $e) {
                $response['message'] = $this->get('translator')->trans('instance_form.error_remove');
            }
        }

        return new JsonResponse($response);
    }

    public function migrateAction()
    {
        // текущее время
        echo date('h:i:s') . "\n<br>";

        $respMessage = '';

        $instances = $this->container->get('instance_manager')->findAll();
        $instances = ['minfo', 'mstroy'];
//        $instances = ['minfo'];
        $code = '';
        $mainKernel = $this->get('kernel');
        $mainApplication = new Application($mainKernel);
        $mainApplication->setAutoExit(false);

        // main cache clear
//        $input = new ArrayInput([
//            'command' => 'cache:clear',
//            '--no-warmup' => true,
//            '--env' => 'dev',
//        ]);
//        $output = new BufferedOutput();
//        $mainApplication->run($input, $output);
//        sleep(10);
//        echo $output->fetch();
//        $input = new ArrayInput([
//            'command' => 'cache:clear',
//            '--no-warmup' => true,
//            '--env' => 'prod',
//        ]);
//        $output = new ConsoleOutput();
//        $mainApplication->run($input, $output);
//        sleep(10);

        if (is_array($instances)) {
            foreach ($instances as $instance) {

                // get instance code
//                $instanceCode = trim($instance->getCode());
                $instanceCode = $instance;

                // get new kernel with new config
//                if ($instanceCode == Instance::MAIN_SITE_INSTANCE_CODE) {
//                    $kernel = new \AppKernel('dev', true);
//                } else {
//                    $kernel = new \AppKernel('dev', true, $instanceCode);
//                }
                if ($instanceCode == 'minfo') {
                    $kernel = new \AppKernelMinfo('dev', true, $instanceCode);
                } elseif ($instanceCode == 'mstroy') {
                    $kernel = new \AppKernelMstroy('dev', true, $instanceCode);
                }
//                var_dump($instanceCode);


                // clear previuos application cache
//                $input = new ArrayInput([
//                    'command' => 'cache:clear',
//                    '--no-warmup' => true,
//                    '--env' => $instanceCode,
//                ]);
//                $output = new ConsoleOutput();
//                $mainApplication->run($input, $output);
//                sleep(10);

//                die ('sfthatesj');
//                if ($code) {
//                    $input = new ArrayInput([
//                        'command' => 'cache:clear',
//                        '--env' => $code,
//    //                    '--no-debug' => true,
//                        '--no-warmup' => true,
//                    ]);
//                    $output = new BufferedOutput();
//                    $mainApplication->run($input, $output);
//                } else {
//                }

                // do nothing
//                echo "<pre>";
//                \Doctrine\Common\Util\Debug::dump($kernel->getCacheDir());
//                echo "</pre>";
//                die;
                $application = new Application($kernel);
//                echo "<pre>";
//                \Doctrine\Common\Util\Debug::dump($kernel->getCacheDir());
//                echo "</pre>";
//                die;
                $application->setAutoExit(false);

                // unset
//                echo $output->wtireln('***' . $instanceCode . '***');
//                echo ('***' . $instanceCode . '***');
//                $kernel->shutdown();
//                $kernel->boot();
//                $input = new ArrayInput([
//                    'command' => 'cache:clear',
//                    '--no-warmup' => true,
//                    '--env' => $instanceCode,
//                    '--instance' => $instanceCode,
//                ]);

//                $input = new ArrayInput([
//                    'command' => 'doctrine:migrations:migrate',
//                    '--em' => 'customer',
//                    '--no-interaction' => true,
//                    '--no-warmup' => true,
//                    '--instance' => $instanceCode,
//                ]);


//                $output = new BufferedOutput();
//                $mainApplication->run($input, $output);
//                $respMessage .= $output->fetch();
//                sleep(10);
//                echo date('h:i:s') . "\n";


//                unset($application);
//                unset($kernel);
//                $code = $instanceCode;
//                echo $code ;

            }
        }
        // текущее время
//        echo date('h:i:s') . "\n<hr>";
        return $this->render('PortalAdminBundle:InstanceAdmin:migrate.html.twig',
            ['message' => $respMessage]);
    }

    public function editSiteNameAction(Request $request, $instanceCode)
    {
        $instance = $this->get('instance_manager')->findOneBy(['code' => $instanceCode]);
        if (!$instance instanceof Instance) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $hasGranted = $this->get('user_manager')->isGranted(
            'edit_site_name',
            $instanceCode,
            $currentUserId
        );
        if (!$hasGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $form = $this->createForm(InstanceSiteNameFormType::class, $instance, [
            'validation_groups' => ['edit_site_name'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($instance);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                    return $this->redirectToRoute('admin_instance_site_name_edit', [
                        'instanceCode' => $instanceCode
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:InstanceAdmin:edit_site_name.html.twig', [
            'form' => $form->createView(),
            'instanceCode' => $instanceCode,
            'hasGranted' => $hasGranted,
        ]);

    }
}
