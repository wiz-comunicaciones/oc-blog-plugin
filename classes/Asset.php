<?php namespace Wiz\Blog\Classes;

abstract class Asset {

    abstract public static function extractIdFromUrl($url);

    abstract public static function embedCode($id);

    abstract public static function get($url);

    abstract public static function preview($url);

    public static function generateIframe($id, $src, $frameParams)
    {
        return sprintf('<iframe id="%s" %s src="%s"></iframe>', $id, $frameParams, $src);
    }

    public static function generateDiv($id, $attributes, $content)
    {
        return sprintf('<div id="%s" %s>%s</div>', $id, $attributes, $content);
    }
}