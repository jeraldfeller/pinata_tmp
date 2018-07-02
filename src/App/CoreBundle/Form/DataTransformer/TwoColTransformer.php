<?php

namespace App\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class TwoColTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($content)
    {
        if (strlen($content) < 1) {
            return $content;
        }

        $crawler = HtmlPageCrawler::create($content);
        $equalCols = $crawler->filter('div.equal-cols');

        if (count($equalCols) < 1) {
            return $content;
        }

        $equalCols->each(function (HtmlPageCrawler $equalCol) {
            $equalCol->attr('contenteditable', 'false');
            $equalCol->attr('unselectable', 'on');

            $cols = $equalCol->filter('div.col');
            $colCount = count($cols);

            if ($colCount < 2) {
                // Missing col - Add it

                for ($i = $colCount; $i < 2; ++$i) {
                    $equalCol->append('<div class="col"><div class="inner-content"></div></div>');
                }

                $cols = $equalCol->filter('div.col');
            }

            $cols->each(function (HtmlPageCrawler $col) {
                $col->attr('contenteditable', 'false');
                $col->attr('unselectable', 'on');

                $col->filter('div.inner-content')->each(function (HtmlPageCrawler $content) {
                    $content->attr('contenteditable', 'true');
                    $content->attr('unselectable', 'off');
                });
            });
        });

        return (string) $crawler;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($content)
    {
        if (strlen($content) < 1) {
            return;
        }

        return $content;
    }
}
