<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\Repository\MenuNodeRepositoryInterface;

class PlaceholderPageType extends AbstractType
{
    /**
     * @var MenuNodeRepositoryInterface
     */
    private $menuNodeRepository;

    /**
     * @param MenuNodeRepositoryInterface $menuNodeRepository
     */
    public function __construct(MenuNodeRepositoryInterface $menuNodeRepository)
    {
        $this->menuNodeRepository = $menuNodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('primarySlug')
            ->remove('metaTitle')
            ->remove('metaDescription')
            ->remove('socialTitle')
            ->remove('socialDescription')
            ->remove('robotsNoIndex')
            ->remove('robotsNoFollow')
            ->remove('excludedFromSitemap')
            ->remove('alias')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if ($data instanceof PageInterface) {
                if (count($data->getMenuNodes()) < 1) {
                    $data->addMenuNode($this->menuNodeRepository->createMenuNode());

                    $event->setData($data);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Vivo\PageBundle\Form\Type\BasePageType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_placeholder_page_type';
    }

    /**
     * BC - Add alias if Symfony < 3.0.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
