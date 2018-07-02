<?php

namespace App\CoreBundle\Form\Extension;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class PageExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('introduction', 'textarea', array(
                'required' => false,
            ))
            ->add('promoGroup', 'entity', array(
                'required' => false,
                'empty_value' => 'None',
                'class' => 'AppCoreBundle:PromoBlockGroup',
                'property' => 'name',
                'label' => 'Promo Blocks',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('b')
                            ->orderBy('b.name', 'ASC')
                            ->addOrderBy('b.id', 'ASC');
                    },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'vivo_page_base_page_type';
    }
}
