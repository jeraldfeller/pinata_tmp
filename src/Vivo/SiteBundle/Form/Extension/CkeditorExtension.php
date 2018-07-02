<?php

namespace Vivo\SiteBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Vivo\SiteBundle\Factory\SiteFactory;

class CkeditorExtension extends AbstractTypeExtension
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var string
     */
    protected $webRoot;

    /**
     * @param SiteFactory $siteFactory
     * @param string      $webRoot
     */
    public function __construct(SiteFactory $siteFactory, $webRoot)
    {
        $this->siteFactory = $siteFactory;
        $this->webRoot = realpath($webRoot).'/';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $site = $this->siteFactory->get();

        if (null === $view->vars['custom_config']) {
            if ($site && $site->getTheme() && $this->fileExists($file = 'javascript/ckeditor/config_'.$site->getTheme().'.js')) {
                $view->vars['custom_config'] = $file;
            } elseif ($this->fileExists($file = 'javascript/ckeditor/config.js')) {
                $view->vars['custom_config'] = $file;
            }
        }

        if (empty($view->vars['contents_css'])) {
            if ($site && $site->getTheme() && $this->fileExists($file = 'javascript/ckeditor/contents_'.$site->getTheme().'.css')) {
                $view->vars['contents_css'][] = $file;
            } elseif ($this->fileExists($file = 'javascript/ckeditor/contents.css')) {
                $view->vars['contents_css'][] = $file;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'Trsteel\CkeditorBundle\Form\Type\CkeditorType';
    }

    /**
     * Checks if a file exists in the web root.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function fileExists($path)
    {
        if (!is_file($toCheck = realpath($this->webRoot.$path))) {
            return false;
        }

        // check if file is contained in web/ directory (prevents ../ in paths)
        if (strncmp($this->webRoot, $toCheck, strlen($this->webRoot)) !== 0) {
            return false;
        }

        return true;
    }
}
