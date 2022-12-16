<?php
namespace app\%s;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Doctrine\Bundle\FixturesBundle\Fixture;
%s

class %sFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $finder->in('%s');
        $finder->name('%s.sql');

        foreach( $finder as $file ){
            $content = $file->getContents();
//            $content = str_replace("\r\n", '', $content);
            $queries = explode("\r\n", $content);
            $conn = $this->container->get('doctrine.orm.%sentity_manager')->getConnection();
            foreach($queries as $query) {
                if($query !== '') {
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                }
            }
        }
    }

    %s
}
