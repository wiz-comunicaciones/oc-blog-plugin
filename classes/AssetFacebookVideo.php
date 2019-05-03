<?php namespace Wiz\Blog\Classes;

class AssetFacebookVideo extends Asset {

    public static function extractIdFromUrl($url)
    {
        if(preg_match("#(\d+)/$#", $url, $matches)){
            return $matches[1];
        }
        return false;
    }

    public static function embedCode($url, $divParams = [])
    {
        $defaultDivParams = [
            'class' => 'fb-video',
            'data-allowfullscreen' => 'false',
            'data-autoplay' => 'false',
            'data-width' => 'auto',
            'data-show-text' => 'false',
            'data-show-captions' => 'false',
        ];

        $divOptions = array_merge(
            ['data-href' => $url],
            $defaultDivParams,
            $divParams
        );

        $htmlAttributes = '';

        foreach($divOptions as $attr => $value)
            $htmlAttributes .= $attr . '="' . $value . '" ';

        return self::generateDiv(
            'fb-' . self::extractIdFromUrl($url),
            $htmlAttributes,
            ''
        );
    }

    public static function get($url)
    {
        return self::embedCode($url);
    }

    public static function preview($url)
    {
        $id = self::extractIdFromUrl($url);

        return 'https://graph.facebook.com/' . $id . '/picture';
    }
}