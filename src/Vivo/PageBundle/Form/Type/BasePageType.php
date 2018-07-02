<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Vivo\PageBundle\Form\EventListener\DisabledAtListener;
use Vivo\PageBundle\Model\PageInterface;

class BasePageType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authChecker;

    /**
     * @var string
     */
    protected $pageClass;

    /**
     * @var string
     */
    protected $slugClass;

    /**
     * @param AuthorizationCheckerInterface $authChecker
     * @param string                        $pageClass
     * @param string                        $slugClass
     */
    public function __construct(AuthorizationCheckerInterface $authChecker, $pageClass, $slugClass)
    {
        $this->authChecker = $authChecker;
        $this->pageClass = $pageClass;
        $this->slugClass = $slugClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $slugReflection = new \ReflectionClass($this->slugClass);

        $builder
            ->add('disabledAt', 'Vivo\UtilBundle\Form\Type\CheckboxToDateTimeType', array(
                'inverted' => true,
                'label' => 'admin.form.page.disabledAt.label',
                'required' => false,
                'help_inline' => 'admin.form.page.disabledAt.help',
            ))
            ->add('primarySlug', 'Vivo\SlugBundle\Form\Type\SlugType', array(
                'slug_entity' => $slugReflection->newInstance(),
                'allow_slashes' => true,
                'label' => 'admin.form.page.slug',
            ))
            ->add('pageTitle', null, array(
                'label' => 'admin.form.page.pagePitle',
            ))
            ->add('metaTitle', null, array(
                'required' => false,
                'label' => 'admin.form.page.metaTitle',
            ))
            ->add('metaDescription', null, array(
                'required' => false,
                'label' => 'admin.form.page.metaDescription',
            ))
            ->add('socialTitle', null, array(
                'required' => false,
                'label' => 'admin.form.page.socialTitle',
            ))
            ->add('socialDescription', null, array(
                'required' => false,
                'label' => 'admin.form.page.socialDescription',
            ))
            ->add('robotsNoIndex', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.page.noIndex',
            ))
            ->add('robotsNoFollow', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.page.noFollow',
            ))
            ->add('excludedFromSitemap', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.page.excludedFromSitemap',
            ))
            ->add('menuNodes', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'Vivo\PageBundle\Form\Type\MenuNodeType',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => array(
                    new Valid(),
                ),
            ))
            ->add('content', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_type' => 'Vivo\PageBundle\Form\Type\ContentType',
                'entry_options' => array(
                    'page_type' => $options['page_type'],
                ),
                'constraints' => array(
                    new Valid(),
                ),
            ))
            ->add('assetGroups', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_type' => 'Vivo\PageBundle\Form\Type\AssetGroupType',
                'entry_options' => array(
                    'page_type' => $options['page_type'],
                ),
                'constraints' => array(
                    new Valid(),
                ),
            ))
        ;

        if ($this->authChecker->isGranted('ROLE_DEVELOPER')) {
            $builder->add('alias', null, array(
                'label' => 'admin.form.page.alias',
            ));
        }

        $builder->addEventSubscriber(new DisabledAtListener());

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            if (null === $data = $event->getData()) {
                return;
            }

            $form = $event->getForm();

            if (!$form->has('pageTitle')) {
                return;
            }

            if ($data instanceof PageInterface) {
                foreach ($data->getMenuNodes() as $node) {
                    if (null === $node->getTitle()) {
                        $node->setTitle($data->getPageTitle());
                    }
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'page_type' => null,
            'data_class' => $this->pageClass,
            'translation_domain' => 'VivoPageBundle',
        ));

        $resolver->setRequired(array(
            'page_type',
        ));

        $resolver->setAllowedTypes('page_type', array('Vivo\PageBundle\PageType\Type\PageTypeInterface'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_base_page_type';
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
