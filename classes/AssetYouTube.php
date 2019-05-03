<?php namespace Wiz\Blog\Classes;

class AssetYouTube extends Asset {

    public static function extractIdFromUrl($url)
    {
        if(preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches)){
            return $matches[1];
        }
        return false;
    }

    public static function embedCode($id, $playerParams = [], $frameParams = [])
    {
        $defaultPlayerParams = [
            'autoplay' => 0,
            'controls' => 1,
            'enablejsapi' => 0,
            'hl' => 'es-cl',
            'modestbranding' => 0,
            'rel' => 0,
            'showinfo' => 0
        ];
        $playerOptions = array_merge($defaultPlayerParams, $playerParams);

        $defaultFrameParams = [
            'type' => 'text/html',
            'class' => 'yt-video-frame',
            'width' => 203,
            'height' => 115,
            'frameborder' => 0,
        ];
        $frameOptions = array_merge($defaultFrameParams, $frameParams);

        $htmlAttributes = '';

        foreach($frameOptions as $attr => $value)
            $htmlAttributes .= $attr . '="' . $value . '" ';

        return self::generateIframe(
            'yt-' . $id,
            'https://www.youtube.com/embed/' . $id . '?' . http_build_query($playerOptions),
            $htmlAttributes
        );
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

        $pic = sprintf('https://img.youtube.com/vi/%s/maxresdefault.jpg', self::extractIdFromUrl($url));

        if($urlOnly)
            return $pic;

        return sprintf('<img %s src="%s">',
            $imageAttributes,
            $pic
        );
    }
}