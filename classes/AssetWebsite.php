<?php namespace Wiz\Blog\Classes;

use Wiz\Blog\Models\Setting;

class AssetWebsite extends Asset {

    public static function extractIdFromUrl($url)
    {
        return $url;
    }

    public static function embedCode($id)
    {
        return $id;
    }

    public static function get($url)
    {
        return $url;
    }

    public static function preview($url, $params = [], $htmlAttrs = [], $urlOnly = false)
    {
        $settings = Setting::instance();

        $defaultParams = [
            'width' => 480,
        ];

        $options = array_merge($defaultParams, $params);

        $defaultHtmlAttrs = [
            'width' => 480,
            'alt' => '',
        ];

        $imgAttrs = array_merge($defaultHtmlAttrs, $htmlAttrs);

        $imageAttributes = '';

        foreach($imgAttrs as $attr => $value)
            $imageAttributes .= $attr . '="' . $value . '" ';

        $pic = sprintf('https://api.thumbnail.ws/api/%s/thumbnail/get?url=%s&%s',
            $settings->thumbnailws_key,
            urlencode($url),
            http_build_query($options));

        if($urlOnly)
            return $pic;

        return sprintf('<img %s src="%s">',
            $imageAttributes,
            $pic
        );
    }
}