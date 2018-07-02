<?php

namespace Vivo\PageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\Form\Model\PageModel;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;

class UniquePageTypeValidator extends ConstraintValidator
{
    /**
     * @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface
     */
    protected $pageTypeManager;

    /**
     * @var \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @param PageTypeManagerInterface $pageTypeManager
     * @param PageRepositoryInterface  $pageRepository
     */
    public function __construct(PageTypeManagerInterface $pageTypeManager, PageRepositoryInterface $pageRepository)
    {
        $this->pageTypeManager = $pageTypeManager;
        $this->pageRepository = $pageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if ($value instanceof PageModel) {
            $value = $value->getPage();
        }

        if (!$value instanceof PageInterface) {
            throw new UnexpectedTypeException($value, 'Vivo\\PageBundle\\Entity\\PageInterface');
        }

        $pageType = $value->getPageTypeInstance();

        if (!$pageType || !$pageType->isUnique()) {
            return true;
        }

        $page = $this->pageRepository->findOnePageByPageTypeAlias($value->getPageTypeInstance()->getAlias());

        if (null !== $page && $page !== $value) {
            $this->context->addViolationAt('pageTypeInstance', $constraint->message);
        }

        return true;
    }
}
