<?php namespace Wiz\Blog\Classes;

class AssetVimeo extends Asset {

    public static function extractIdFromUrl($url)
    {
        return (int) substr(parse_url($url, PHP_URL_PATH), 1);
    }

    public static function embedCode($id, $playerParams = [], $frameParams = [])
    {
        $defaultPlayerParams = [
            'autoplay' => 0,
            'autopause' => 1,
            'title' => 0,
            'loop' => 'es-cl',
            'badge' => 0,
            'byline' => 0,
            'portrait' => 0,
        ];

        $playerOptions = array_merge($defaultPlayerParams, $playerParams);

        $defaultFrameParams = [
            'type' => 'text/html',
            'width' => 203,
            'height' => 115,
            'frameborder' => 0,
        ];

        $frameOptions = array_merge($defaultFrameParams, $frameParams);

        $htmlAttributes = '';

        foreach($frameOptions as $attr => $value)
            $htmlAttributes .= $attr . '="' . $value . '" ';

        return self::generateIframe(
            'vm-' . $id,
            'https://player.vimeo.com/video/' . $id . '?'  . http_build_query($playerOptions),
            $htmlAttributes);
    }

    public static function get($url, $playerParams = [], $frameParams = [])
    {
        return self::embedCode(
            self::extractIdFromUrl($url),
            $playerParams,
            $frameParams
        );
    }

    public static function preview($url, $htmlAttrs = [], $urlOnly = false)
    {
        $defaultHtmlAttrs = [
            'width' => 480,
            'alt' => '',
        ];

        $imgAttrs = array_merge($defaultHtmlAttrs, $htmlAttrs);

        $imageAttributes = '';

        foreach($imgAttrs as $attr => $value)
            $imageAttributes .= $attr . '="' . $value . '" ';

        $hash = unserialize(file_get_contents(sprintf('http://vimeo.com/api/v2/video/%s.php', self::extractIdFromUrl($url))));

        if($urlOnly)
            return $hash[0]['thumbnail_large'];

        return sprintf('<img %s src="%s">',
            $imageAttributes,
            $hash[0]['thumbnail_medium']
        );
    }
}