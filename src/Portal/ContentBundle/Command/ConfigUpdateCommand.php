<?php

namespace Portal\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Entity\Instance;
use Portal\ContentBundle\Entity\Attachment;
use Symfony\Component\Yaml\Yaml;

class ConfigUpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('config:update')
            ->setDescription('Update all subdomain config folders');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (posix_getpwuid(posix_geteuid())['name'] !== 'www-data') {
            $output->writeln('This script should be run from the user \'www-data\' (sudo -u www-data ...)');
            exit;
        }

        $container = $this->getContainer();
        $instanceArray = $container->get('instance_manager')->getInstanceCodeList();
        if (!empty($instanceArray)) {
            $rootDir = $container->get('kernel')->getRootDir();
            // clear dir
            $this->clearDir($rootDir . '/sites');
            $this->clearDir($rootDir . '/../web/fc');
//            $this->clearDir($rootDir . '/../web/uploads', $instanceArray);

            // update configs for all instances
            foreach ($instanceArray as $instance) {
                if ($instance !== 'main') {

                    // feature site folders
                    $siteDir = $rootDir . '/sites/' . $instance;
                    $configDir = $siteDir . '/config';
                    mkdir($configDir, 0777, true);
                    echo 'created site ' . $instance . ': ';
                    
                    // make site config links
                    $ResourcesDir = $siteDir . '/Resources';
                    mkdir($ResourcesDir, 0777, true);
                    PortalHelper::makeSymlinksFolderContents($rootDir . '/Resources', $ResourcesDir);
                    echo 'Resources links';
                    
                    // make site config links
                    PortalHelper::makeSymlinksFolderContents($rootDir . '/config', $configDir);
                    echo 'config links';
                    
                    // make site kernel link
                    $kernelFile = $siteDir . '/' . ucfirst($instance) . 'Kernel.php';
                    copy($rootDir . '/AppKernelSub.php', $kernelFile);
                    $kernelContent = file_get_contents($kernelFile);
                    $kernelContent = str_replace('AppKernelSub', ucfirst($instance) . 'Kernel', $kernelContent);
                    file_put_contents($kernelFile, $kernelContent);
                    echo ', kernel link';
                    // make site parameters.yml link
                    $parametersYml = Yaml::parse(file_get_contents($rootDir . '/config/parameters.yml'));
                    $parametersYml['parameters']['database_name2'] = Instance::PREFIX_DATABASE_DEFAULT . $instance;
                    $parametersYml['parameters']['instance_code'] = $instance;
                    file_put_contents($configDir . '/parameters.yml', Yaml::dump($parametersYml));
                    echo ', parameters.yml link';

                    // feature fc folders
                    $dirSource = $rootDir . '/../web';
                    $fcDir = $dirSource . '/fc/' . $instance;
                    mkdir($fcDir, 0777);
                    echo ', fc folder';
                    // make site app file
                    copy($dirSource . '/app_sub.php', $fcDir . '/app.php');
                    $appContent = file_get_contents($fcDir . '/app.php');
                    $appContent = str_replace('subdomainfolder', $instance, $appContent);
                    $appContent = str_replace('subdomainkernel', ucfirst($instance) . 'Kernel', $appContent);
                    file_put_contents($fcDir . '/app.php', $appContent);
                    echo ', app.php';
                    // make site app_dev file
                    copy($dirSource . '/app_dev_sub.php', $fcDir . '/app_dev.php');
                    $appDevContent = file_get_contents($fcDir . '/app_dev.php');
                    $appDevContent = str_replace('subdomainfolder', $instance, $appDevContent);
                    $appDevContent = str_replace('subdomainkernel', ucfirst($instance) . 'Kernel', $appDevContent);
                    file_put_contents($fcDir . '/app_dev.php', $appDevContent);
                    echo ', app_dev.php';
                    // make symlinks
                    symlink($dirSource . '/bundles', $fcDir . '/bundles');
                    symlink($dirSource . '/uploads', $fcDir . '/uploads');
                    symlink($dirSource . '/themes', $fcDir . '/themes');
                    symlink($dirSource . '/robots.txt', $fcDir . '/robots.txt');
                    symlink($dirSource . '/file', $fcDir . '/file');
                    mkdir($fcDir . '/rus', 0777);
                    symlink($dirSource . '/file', $fcDir . '/rus/file');
                }

                // feature upload folder
                $uploadsDir = $rootDir . '/../web/uploads/' . $instance . '/';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777);
                    foreach (Attachment::$PATH_LIST as $path) {
                        mkdir($uploadsDir . Attachment::ATTACHMENTS_DIR . $path, 0777, true);
                    }
                    echo ', upload folder';
                }
                echo "\n";
            }
        }
    }

    /**
     * @param string $path
     * @param array $skipArray
     */
    protected function clearDir($path, $skipArray = []): void
    {
        if (is_dir($path)) {
            $dir = opendir($path);
            while (($file = readdir($dir)) !== false) {
                if ($file === '.' || $file === '..')
                    continue;

                if (in_array($file, $skipArray))
                    continue;

                $current = $path . '/' . $file;
                if (is_file($current) || is_link($current)) {
                    unlink($current);
                    echo 'removed file: ' . $current . "\n";
                }
                if (is_dir($current)) {
                    PortalHelper::removeFolderWithContents($current);
                    echo 'removed dir: ' . $current . "\n";
                }
            }
            closedir($dir);
        } else {
            mkdir($path, 0777);
            echo 'make dir: ' . $path;
        }
    }
}
