<?php

namespace Vivo\BlogBundle\Form\Choice;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryChoice extends AbstractType
{
    /**
     * @var string
     */
    protected $categoryClass;

    /**
     * Constructor.
     *
     * @param string $categoryClass
     */
    public function __construct($categoryClass)
    {
        $this->categoryClass = $categoryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => $this->categoryClass,
            'choice_label' => 'title',
            'multiple' => true,
            'expanded' => true,
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('category');
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Bridge\Doctrine\Form\Type\EntityType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_blog_category_choice';
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
