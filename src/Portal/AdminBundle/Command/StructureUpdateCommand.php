<?php

namespace Portal\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StructureUpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('structure:order:update')
            ->setDescription('Update structure order tree')
            ->addArgument('instanceCode', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $kernelDir = $container->get('kernel')->getRootDir();
        // get list of instances
        $inst = strtolower($input->getArgument('instanceCode'));
        if ($inst === 'all') {
            $instances = $container->get('instance_manager')->getInstanceCodeList();
        } else {
            $instances[] = $inst;
        }

        // walk instance array
        foreach ($instances as $inst) {
            echo 'Sort order for: ' . $inst;
            if($inst !== 'main') {
                $clearInstanceCode = explode('-', $inst);
                $compiledKernelName = '';
                foreach($clearInstanceCode as $code) {
                    $compiledKernelName .= ucfirst($code);
                }
                $newKernel = $compiledKernelName."Kernel";
                require_once $kernelDir . "/sites/{$inst}/{$newKernel}.php";
                $kernel = new $newKernel('dev', true);
                $kernel->boot();
            }else{
                $kernel = $container->get('kernel');
            }
            $kernelContainer = $kernel->getContainer();
            $structMenu = $kernelContainer->get('customer_menu_node_manager')->getStructureMenu();
            $params = [];
            // walk menu_node
            foreach ($structMenu as $k => $v) {
                $parent = (int)$v['parent_id'];
                $params[$parent]['parent'] = $parent;
                $params[$parent]['tree'][] = $v['id'];
            }
            // update db table
            if (!empty($params)) {
                $container->get('customer_menu_node_manager')->moveCategory($params, $kernelContainer);
            }
            echo " - OK\n";
        }
    }
}
