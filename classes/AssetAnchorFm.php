<?php namespace Wiz\Blog\Classes;

use Wiz\Blog\Models\Setting;

class AssetAnchorFm extends Asset {

    public static function extractIdFromUrl($url)
    {
        return $url;
    }

    public static function embedCode($id)
    {
        return $id;
    }

    public static function get($url, $htmlAttrs = [])
    {
        $defaultHtmlAttrs = [
            'width' => 400,
            'height' => 102,
            'frameborder' => 0,
            'scrolling' => 'no'
        ];

        $iframeAttrs = array_merge($defaultHtmlAttrs, $htmlAttrs);

        $outputAttributes = '';

        foreach($iframeAttrs as $attr => $value)
            $outputAttributes .= $attr . '="' . $value . '" ';

        $embedUrl = str_replace('/episodes/', '/embed/episodes/', $url);
        $embedUrl = strpos($embedUrl, '/embed') ? $embedUrl : $url . '/embed';

        return sprintf('<iframe src="%s" %s></iframe>',
            $embedUrl,
            $outputAttributes
        );
    }

    public static function preview($url)
    {
        $settings = Setting::instance();

        return $settings->anchorfm_base_image;
    }
}