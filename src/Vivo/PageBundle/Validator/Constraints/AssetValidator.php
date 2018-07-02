<?php

namespace Vivo\PageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vivo\PageBundle\Model\AssetInterface;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;

class AssetValidator extends ConstraintValidator
{
    /**
     * @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface
     */
    protected $pageTypeManager;

    /**
     * @param PageTypeManagerInterface $pageTypeManager
     */
    public function __construct(PageTypeManagerInterface $pageTypeManager)
    {
        $this->pageTypeManager = $pageTypeManager;
    }

    /**
     * @param \Vivo\PageBundle\Model\AssetInterface $value
     * @param Constraint                            $constraint
     *
     * @return bool|void
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return true;
        }

        if (!$value instanceof AssetInterface) {
            throw new UnexpectedTypeException($value, '\Vivo\\PageBundle\\Entity\\AssetInterface');
        }

        $assetGroup = $value->getAssetGroup();

        $assetGroupBlock = $assetGroup->getPage()->getPageTypeInstance()->getAssetGroupBlockByAlias($assetGroup->getAlias());
        $options = $assetGroupBlock->getOptions();
        $allowedMimeTypes = isset($options['mime_types']) ? $options['mime_types'] : array();
        $success = true;

        if (is_array($allowedMimeTypes) && count($allowedMimeTypes) > 0) {
            $validMime = false;
            $fileMime = explode('/', $value->getFile()->getMimeType(), 2);

            foreach ($allowedMimeTypes as $mimeType) {
                $allowedMime = explode('/', $mimeType, 2);

                if (('*' === $allowedMime[1] && $allowedMime[0] === $fileMime[0]) || ($allowedMime[0] === $fileMime[0] && $allowedMime[1] === $fileMime[1])) {
                    $validMime = true;

                    break;
                }
            }

            if (!$validMime) {
                $this->context->addViolationAt('file', $constraint->message, array('{{ filename }}' => $value->getFilename()));
            }
        }

        return $success;
    }
}
