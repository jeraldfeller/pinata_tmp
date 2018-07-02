<?php

namespace Vivo\TreeBundle\Form\Choice;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\TreeBundle\Form\EventListener\MergeCollectionListener;
use Vivo\TreeBundle\Model\TreeLevel;
use Vivo\UtilBundle\Form\DataTransformer\EntityToIdTransformer;
use Vivo\TreeBundle\Model\TreeInterface;
use Vivo\TreeBundle\Repository\TreeRepositoryInterface;

/**
 * Class TreeChoice.
 */
class TreeChoice extends AbstractType
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choice_data'] = $this->getChoices($options, true);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityToIdTransformer($this->objectManager, $options['class'], $options['multiple'] ? true : false);
        $builder->addModelTransformer($transformer);

        if ($options['multiple']) {
            $builder->addEventSubscriber(new MergeCollectionListener($options['choices']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choice_translation_domain' => false,
            'class' => null,
            'property' => null,
            'property_prefix' => '-',
            'max_levels' => null,
            'required' => false,
            'placeholder' => 'None',
            'choices' => function (Options $options) {
                return $this->getChoices($options);
            },
            'query_builder' => null,
        ));

        $resolver->setRequired(array(
            'class', 'property',
        ));

        $resolver->setAllowedTypes('query_builder', array('null', '\Doctrine\ORM\QueryBuilder', '\Closure'));
        $resolver->setAllowedTypes('max_levels', array('null', 'integer'));
        $resolver->setAllowedTypes('class', array('string'));
        $resolver->setAllowedTypes('property', array('string'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_tree_tree_choice';
    }

    /**
     * @BC for < Symfony 3.0
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param Options|array $options
     * @param Options|array $options
     * @param bool          $asData
     *
     * @return array
     */
    protected function getChoices($options, $asData = false)
    {
        $choices = array();

        $results = $this->getRepository($options['class'])->getFlatArrayChildren(null, $options['max_levels'], $this->getQueryBuilder($options));

        foreach ($results as $result) {
            $result->setLabelProperty($options['property']);

            if ($asData) {
                $choices[$result->getModel()->getId()] = $result;
            } else {
                $choices[$result->getModel()->getId()] = ($options['property_prefix'] ? str_repeat($options['property_prefix'], $result->getLevel()).' ' : '').(string) $result;
            }
        }

        return $choices;
    }

    /**
     * @param TreeInterface $parent
     * @param string        $labelProperty
     * @param int           $level
     *
     * @return \Vivo\TreeBundle\Model\TreeLevelInterface[]
     */
    protected function getChildren(TreeInterface $parent, $labelProperty, $level = 0)
    {
        $results = array(new TreeLevel($parent, $level, $labelProperty));

        $children = $parent->getChildren();

        foreach ($children as $child) {
            $results[] = new TreeLevel($child, $level, $labelProperty);

            foreach ($this->getChildren($child, $labelProperty, ++$level) as $result) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * @param string $class
     *
     * @return TreeRepositoryInterface|\Doctrine\Common\Persistence\ObjectRepository
     *
     * @throws \Exception
     */
    protected function getRepository($class)
    {
        $repository = $this->objectManager->getRepository($class);

        if (!$repository instanceof TreeRepositoryInterface) {
            throw new \Exception(sprintf("'%s' does not implement TreeRepositoryInterface", get_class($repository)));
        }

        return $repository;
    }

    /**
     * @param Options|array $options
     *
     * @return QueryBuilder|null
     *
     * @throws UnexpectedTypeException
     */
    protected function getQueryBuilder($options)
    {
        $queryBuilder = $options['query_builder'];

        if (null == $queryBuilder) {
            return;
        }

        if (!($queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }

        if ($queryBuilder instanceof \Closure) {
            $queryBuilder = $queryBuilder($this->getRepository($options['class']));

            if (!$queryBuilder instanceof QueryBuilder) {
                throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder');
            }
        }

        return $queryBuilder;
    }
}
