<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    protected $instance;

    /**
     * @return string
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param string $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Constructor.
     *
     * @param string $environment The environment
     * @param bool   $debug       Whether to enable debugging or not
     * @param string $instance
     */
    public function __construct($environment, $debug, $instance = null)
    {
        if (!is_null($instance) && $instance !== 'main') {
            $this->instance = $instance;
        }
        $this->environment = $environment;
        $this->debug = (bool) $debug;
        $this->rootDir = $this->getRootDir();
        $this->name = $this->getName();

        if ($this->debug) {
            $this->startTime = microtime(true);
        }

//        $defClass = new \ReflectionMethod($this, 'init');
//
//        if (__CLASS__ !== $defClass) {
//            @trigger_error(sprintf('Calling the %s::init() method is deprecated since version 2.3 and will be removed in 3.0. Move your logic to the constructor method instead.', $defClass), E_USER_DEPRECATED);
//            $this->init();
//        }
//        $defClass = $defClass->getDeclaringClass()->name;
    }

    public function getCharset()
    {
        return 'UTF-8';
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Pagination
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),

            // FOS 
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            // Migrations
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Portal\UserBundle\PortalUserBundle(),
            new Portal\HelperBundle\PortalHelperBundle(),

            // These are the other bundles the SonataAdminBundle relies on
//            new Sonata\CoreBundle\SonataCoreBundle(),
//            new Sonata\BlockBundle\SonataBlockBundle(),
//            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

//            // And finally, the storage and SonataAdminBundle
//            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
//            new Sonata\AdminBundle\SonataAdminBundle(),

            // Usefull
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),

            // Project Bundles
            new Portal\ContentBundle\PortalContentBundle(),
            new Portal\AdminBundle\PortalAdminBundle(),

            // Fixtures
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

            // JS Translator
            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),

            //Doctrine Extensions
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            // Captcha
//            new Captcha\Bundle\CaptchaBundle\CaptchaBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Portal\PdfJSBundle\PortalPdfJSBundle(),

            // Imagine crop, resize and thumbnails
//            new Imagine\Gd\Imagine(),

            // Excel
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            // Excel Twig
            new MewesK\TwigExcelBundle\MewesKTwigExcelBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }
    public function getCacheDir()
    {
        if ($this->getInstance()) {
//            return $this->getRootDir() . '/var/cache/' . $this->getInstance() . '/' . $this->getEnvironment();
            return dirname(__DIR__) . '/var/cache/' . $this->getInstance() . '/' . $this->getEnvironment();
        } else {
//            return '/var/' . parent::getCacheDir();
            return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
//            return $this->getRootDir() . '/var/cache/'.$this->getEnvironment();
        }
    }
    
//    public function getRootDir()
//    {
//        return __DIR__;
//    }
    
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
//        return $this->getRootDir().'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (is_null($this->getInstance())) {
            // Default main configuration
            $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
        } else {
            // Website configs
            $loader->load($this->getRootDir().'/sites/' . $this->getInstance() . '/config/config_'.$this->getEnvironment().'.yml');
        }
    }
}
