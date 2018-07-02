<?php

namespace Vivo\UtilBundle\Twig;

class LinkExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('vivo_util_link', array($this, 'link'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('vivo_util_link_twitter', array($this, 'linkTwitter'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('vivo_util_link_facebook', array($this, 'linkFacebook'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $string
     * @param array  $attr
     *
     * @return mixed
     */
    public function link($string, array $attr = array())
    {
        $extension = $this;
        $attrString = $this->getAttrString($attr, array('target' => '_blank'));

        // Replace :// links
        $string = preg_replace_callback("/(^|[\n ])([\w]+?:\/\/[\w]+[^ \"\n\r\t< ]*)/", function ($matches) use ($extension, $attrString) {
            $value = $this->normalize($matches[2]);

            return $matches[1].'<a href="'.$value.'"'.$attrString.'>'.$value.'</a>';
        }, $string);

        // Replace www. and ftp. links
        $string = preg_replace_callback("/(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)/", function ($matches) use ($extension, $attrString) {
            $value = $this->normalize($matches[2]);

            return $matches[1].'<a href="http://'.$value.'"'.$attrString.'>'.$value.'</a>';
        }, $string);

        // Replace email addresses
        $string = preg_replace_callback("/([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.(\w+))(?![^<]*<\/a>)/", function ($matches) use ($extension, $attr) {
            $value = $this->normalize($matches[1]);

            // mailto doesn't need a _blank target
            $attrString = $this->getAttrString($attr);

            return '<a href="mailto:'.$value.'"'.$attrString.'>'.$value.'</a>';
        }, $string);

        return $string;
    }

    /**
     * @param string|array $stringOrTweetArray
     * @param array        $attr
     *
     * @return string
     */
    public function linkTwitter($stringOrTweetArray, array $attr = array())
    {
        $extension = $this;
        $attrString = $this->getAttrString($attr, array('target' => '_blank'));

        if (is_array($stringOrTweetArray)) {
            $string = $this->tweetArrayToString($stringOrTweetArray);
        } else {
            $string = $stringOrTweetArray;
        }

        $string = preg_replace_callback("/@(\w+)(?![^<]*<\/a>)/", function ($matches) use ($extension, $attrString) {
            return '<a href="https://www.twitter.com/'.$this->normalize($matches[1]).'"'.$attrString.'>'.$this->normalize($matches[0]).'</a>';
        }, $string);

        $string = preg_replace_callback("/#(\w+)(?![^<]*<\/a>)/", function ($matches) use ($extension, $attrString) {
            return '<a href="https://twitter.com/search?q=%23'.$this->normalize($matches[1]).'"'.$attrString.'>'.$this->normalize($matches[0]).'</a>';
        }, $string);

        return $string;
    }

    /**
     * @param string $string
     * @param array  $attr
     *
     * @return mixed
     */
    public function linkFacebook($string, array $attr = array())
    {
        $extension = $this;
        $attrString = $this->getAttrString($attr, array('target' => '_blank'));

        $string = preg_replace_callback("/@(\w+)(?![^<]*<\/a>)/", function ($matches) use ($extension, $attrString) {
            return '<a href="https://www.facebook.com/'.$this->normalize($matches[1]).'"'.$attrString.'>'.$this->normalize($matches[0]).'</a>';
        }, $string);

        $string = preg_replace_callback("/#(\w+)(?![^<]*<\/a>)/", function ($matches) use ($extension, $attrString) {
            return '<a href="https://www.facebook.com/hashtag/'.$this->normalize($matches[1]).'"'.$attrString.'>'.$this->normalize($matches[0]).'</a>';
        }, $string);

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_util_link';
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function normalize($string)
    {
        return htmlentities(strip_tags($string), ENT_COMPAT, 'utf-8');
    }

    /**
     * @param array $attr
     * @param array $defaults
     *
     * @return string
     */
    private function getAttrString(array $attr, array $defaults = array())
    {
        $mergedAttr = array_merge($defaults, $attr);
        $mergedAttr = array_filter($mergedAttr);

        $result = '';

        foreach ($mergedAttr as $key => $value) {
            $result .= $key.'="'.$value.'" ';
        }

        $result = trim($result);

        return strlen($result) > 0 ? ' '.$result : '';
    }

    /**
     * @param array $tweetArray
     *
     * @return string
     */
    private function tweetArrayToString($tweetArray)
    {
        $isRetweet = false;

        if (isset($tweetArray['retweeted_status']['text'])) {
            $isRetweet = true;
            $tweetData = $tweetArray['retweeted_status'];
        } else {
            if (!isset($tweetArray['text'])) {
                return '';
            }

            $tweetData = $tweetArray;
        }

        $tweet = $tweetData['text'];

        $urlEntities = isset($tweetData['entities']['urls']) ? $tweetData['entities']['urls'] : array();

        if (count($urlEntities) < 1) {
            return $tweet;
        }

        $entities = array();

        foreach ($urlEntities as $url) {
            $entities[] = array(
                'start' => $url['indices'][0],
                'end' => $url['indices'][1],
                'text' => $url['url'],
            );
        }

        usort($entities, function ($first, $second) {
            return $second['start'] - $first['start'];
        });

        foreach ($entities as $item) {
            $tweet = $this->mb_substr_replace($tweet, $item['text'], $item['start'], $item['end'] - $item['start']);
        }

        if ($isRetweet && isset($tweetArray['retweeted_status']['user']['screen_name'])) {
            return 'RT @'.$tweetArray['retweeted_status']['user']['screen_name'].': '.$tweet;
        }

        return $tweet;
    }

    /**
     * @param string   $string
     * @param string   $replacement
     * @param int      $start
     * @param int|null $length
     * @param null     $encoding
     *
     * @return mixed|string
     */
    private function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = 'UTF8')
    {
        if (extension_loaded('mbstring') === true) {
            $string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);

            if ($start < 0) {
                $start = max(0, $string_length + $start);
            } elseif ($start > $string_length) {
                $start = $string_length;
            }

            if ($length < 0) {
                $length = max(0, $string_length - $start + $length);
            } elseif ((is_null($length) === true) || ($length > $string_length)) {
                $length = $string_length;
            }

            if (($start + $length) > $string_length) {
                $length = $string_length - $start;
            }

            if (is_null($encoding) === true) {
                return mb_substr($string, 0, $start).$replacement.mb_substr($string, $start + $length, $string_length - $start - $length);
            }

            return mb_substr($string, 0, $start, $encoding).$replacement.mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
        }

        return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);
    }
}
