<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateAllCommand extends ContainerAwareCommand
{
    public $container;
    public $rootDir;
    public $kernelDir;
    public $webDir;
    public $output;
    public $migratable = array();
    public $defaultPhotoReportTypeId = 16;
    public $io;
    public $instances;

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
            ->setName('mm:all')
            ->setDescription('Migrates data from old portal');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $kernel = $this->container->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '--em' => 'customer',
            '--no-interaction' => true
        ));

        $application->run($input, $this->output);

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '--em' => 'log',
            '--no-interaction' => true
        ));

        $application->run($input, $this->output);

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '--em' => 'search',
            '--no-interaction' => true
        ));

        $application->run($input, $this->output);

        $this->instances = $this->container->get('instance_manager')->findAll();
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->kernelDir = $this->container->get('kernel')->getRootDir();
        $this->rootDir = str_replace('/app', '/', $this->kernelDir);

        foreach($this->instances as $instance) {
            $instanceCode = $instance->getCode();
            $this->io->section('Migration for instance '. $instanceCode);
            if($instanceCode !== 'main') {
               $clearInstanceCode = explode('-', $instanceCode);
                $compiledKernelName = '';
                foreach($clearInstanceCode as $code) {
                    $compiledKernelName .= ucfirst($code);
                }
                $newKernel = $compiledKernelName."Kernel";

                require_once $this->kernelDir . "/sites/{$instanceCode}/{$newKernel}.php";

                $kernel = new $newKernel('dev', true);
                $kernel->boot(); 
            }else{
                $kernel = $this->container->get('kernel');
            }
            

            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput(array(
                'command' => 'doctrine:migrations:migrate',
                '--em' => 'customer',
                '--no-interaction' => true
            ));

            $application->run($input, $this->output);

            $kernel->shutdown();
        }
    }
}
