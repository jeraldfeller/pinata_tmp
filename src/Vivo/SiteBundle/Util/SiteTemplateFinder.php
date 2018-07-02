<?php

namespace Vivo\SiteBundle\Util;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Model\SiteInterface;
use Vivo\SiteBundle\Templating\Loader;

class SiteTemplateFinder implements SiteTemplateFinderInterface
{
    /**
     * @var SiteFactory
     */
    private $siteFactory;

    /**
     * @var \Twig_Environment
     */
    private $originalTwig;

    /**
     * @var \Twig_Environment
     */
    private $clonedTwig;

    /**
     * @var \Twig_Environment[]
     */
    private $twigEnvironments = [];

    /**
     * @var \Symfony\Component\Templating\Loader\LoaderInterface[]
     */
    private $loaders = [];

    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var TemplateNameParserInterface
     */
    private $parser;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var array
     */
    private $bundles;

    /**
     * @var \Mobile_Detect
     */
    private $mobileDetect;

    /**
     * @var bool
     */
    private $mobileSupport;

    /**
     * @var bool
     */
    private $tabletSupport;

    /**
     * Constructor.
     *
     * @param SiteFactory                 $siteFactory
     * @param \Twig_Environment           $twig
     * @param array                       $loaders
     * @param FileLocatorInterface        $locator
     * @param TemplateNameParserInterface $parser
     * @param $kernelRootDir
     * @param array $bundles
     */
    public function __construct(
        SiteFactory $siteFactory,
        \Twig_Environment $twig,
        array $loaders = array(),
        FileLocatorInterface $locator,
        TemplateNameParserInterface $parser,
        $kernelRootDir,
        array $bundles,
        \Mobile_Detect $mobileDetect = null,
        $mobileSupport = false,
        $tabletSupport = false
    ) {
        $this->siteFactory = $siteFactory;
        $this->originalTwig = $twig;
        $this->clonedTwig = clone $twig;
        $this->loaders = $loaders;
        $this->locator = $locator;
        $this->parser = $parser;
        $this->kernelRootDir = $kernelRootDir;
        $this->bundles = $bundles;
        $this->mobileDetect = $mobileDetect;
        $this->mobileSupport = $mobileSupport;
        $this->tabletSupport = $tabletSupport;
    }

    /**
     * {@inheritdoc}
     */
    public function render(SiteInterface $site, $name, array $context = array())
    {
        return $this->getTwigEnvironment($site)->render($name, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function loadTemplate(SiteInterface $site, $name, $index = null)
    {
        return $this->getTwigEnvironment($site)->loadTemplate($name, $index);
    }

    private function getTwigEnvironment(SiteInterface $site)
    {
        if ($site === $this->siteFactory->get()) {
            return $this->originalTwig;
        }

        if (!array_key_exists($site->getId(), $this->twigEnvironments)) {
            $loader = new Loader(
                $this->locator,
                $this->parser,
                $this->kernelRootDir,
                $this->bundles,
                $this->siteFactory,
                $this->mobileDetect,
                $this->mobileSupport,
                $this->tabletSupport
            );

            $chainLoader = new \Twig_Loader_Chain(array($loader));
            foreach ($this->loaders as $loader) {
                $chainLoader->addLoader($loader);
            }

            $twig = clone $this->clonedTwig;
            $twig->setLoader($chainLoader);
            $this->twigEnvironments[$site->getId()] = $twig;
            unset($chainLoader);
        }

        return $this->twigEnvironments[$site->getId()];
    }
}
