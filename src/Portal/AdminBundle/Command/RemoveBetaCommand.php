<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveBetaCommand extends ContainerAwareCommand
{
    public $container;
    public $output;
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
            ->setName('beta:remove')
            ->setDescription('Migrates data from old portal');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        
        $this->instances = $this->container->get('instance_manager')->findAll();
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);
        
        $command = 'php bin/console doctrine:query:sql --connection=search --instance=main "SELECT * FROM search_view"';
        $this->output->writeln( "*********************search************************" );
        $this->output->writeln( shell_exec($command) );
        $this->output->writeln( "*********************/search************************" );
        $this->output->writeln( "" );
        $this->output->writeln( "" );
        $this->output->writeln( "" );
        $this->output->writeln( "" );
        
        $command = 'php bin/console doctrine:query:sql --connection=log --instance=main "SELECT * FROM search_view"';
        $this->output->writeln( "*********************log************************" );
        $this->output->writeln( shell_exec($command) );
        $this->output->writeln( "*********************/log************************" );
        $this->output->writeln( "" );
        $this->output->writeln( "" );
        $this->output->writeln( "" );
        $this->output->writeln( "" );
        
        foreach($this->instances as $instance) {
            $instanceCode = $instance->getCode();
            $this->output->writeln( "*********************$instanceCode************************" );
            $command = 'php bin/console doctrine:query:sql --instance='.$instanceCode.' "SELECT * FROM search_view"';
            $this->output->writeln( shell_exec($command) );
            $this->output->writeln( "************************/$instanceCode*********************" );
            $this->output->writeln( "" );
            $this->output->writeln( "" );
            $this->output->writeln( "" );
            $this->output->writeln( "" );
        }
    }
}
