<?php

namespace Vivo\SiteBundle\Templating;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Vivo\SiteBundle\Factory\SiteFactory;

class Loader extends FilesystemLoader
{
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var array
     */
    private $bundles;

    /**
     * @var SiteFactory
     */
    private $siteFactory;

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
     * @var bool
     */
    private $themePathsAdded = false;

    /**
     * @param FileLocatorInterface        $locator
     * @param TemplateNameParserInterface $parser
     * @param $kernelRootDir
     * @param array          $bundles
     * @param SiteFactory    $siteFactory
     * @param \Mobile_Detect $mobileDetect
     * @param bool           $mobileSupport
     * @param bool           $tabletSupport
     */
    public function __construct(
        FileLocatorInterface $locator,
        TemplateNameParserInterface $parser,
        $kernelRootDir,
        array $bundles,
        SiteFactory $siteFactory,
        \Mobile_Detect $mobileDetect = null,
        $mobileSupport = false,
        $tabletSupport = false
    ) {
        parent::__construct($locator, $parser);

        $this->kernelRootDir = $kernelRootDir;
        $this->bundles = $bundles;
        $this->siteFactory = $siteFactory;
        $this->mobileDetect = $mobileDetect;
        $this->mobileSupport = $mobileSupport;
        $this->tabletSupport = $tabletSupport;
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate($template, $throw = true)
    {
        if (true !== $this->themePathsAdded) {
            $site = $this->siteFactory->get();

            if (null !== $site) {
                if (is_dir($dir = $this->kernelRootDir.'/Resources/views/themes/'.$site->getTheme().'/')) {
                    $this->prependPath($dir);
                }

                if (null !== $this->mobileDetect) {
                    $isMobile = $this->mobileDetect->isMobile();
                    $isTablet = $this->mobileDetect->isTablet();

                    if ($this->tabletSupport && ($isTablet || $isMobile)) {
                        if (is_dir($dir = $this->kernelRootDir.'/Resources/views/themes/'.$site->getTheme().'/tablet/')) {
                            $this->prependPath($dir);
                        }
                    }

                    if ($this->mobileSupport && $isMobile) {
                        if (is_dir($dir = $this->kernelRootDir.'/Resources/views/themes/'.$site->getTheme().'/mobile/')) {
                            $this->prependPath($dir);
                        }
                    }
                }

                foreach ($this->bundles as $bundle => $class) {
                    $name = $bundle;
                    if ('Bundle' === substr($name, -6)) {
                        $name = substr($name, 0, -6);
                    }

                    if (is_dir($dir = $this->kernelRootDir.'/Resources/views/themes/'.$site->getTheme().'/'.$bundle.'/views/')) {
                        $this->prependPath($dir, $name);
                    }
                }

                $this->themePathsAdded = true;
            }
        }

        return parent::findTemplate($template, $throw);
    }
}
