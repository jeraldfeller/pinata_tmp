<?php

namespace Vivo\UtilBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\UtilBundle\Form\DataTransformer\SecureEntityToIdTransformer;
use Vivo\UtilBundle\Util\EntitySignerUtil;

class SecureHiddenEntityType extends AbstractType
{
    /**
     * @var EntitySignerUtil
     */
    protected $entitySigner;

    /**
     * SecureHiddenEntityType constructor.
     *
     * @param EntitySignerUtil $entitySigner
     */
    public function __construct(EntitySignerUtil $entitySigner)
    {
        $this->entitySigner = $entitySigner;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SecureEntityToIdTransformer($this->entitySigner, $options['class'], false));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The entity does not exist.',
        ));

        $resolver->setRequired(array(
            'class',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\HiddenType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_util_secure_hidden_entity';
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
