<?php

namespace App\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CkeditorBasicTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($content)
    {
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($content)
    {
        if (strlen($content) < 1) {
            return;
        }

        return $this->cleanContent($content);
    }

    /**
     * Strip unwanted tags.
     *
     * @param string $content
     *
     * @return string
     */
    protected function cleanContent($content)
    {
        return strip_tags($content, '<p><br><a><strong><em><u><s><sub><sup>');
    }
}
