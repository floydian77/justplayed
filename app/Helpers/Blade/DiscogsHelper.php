<?php


class DiscogsHelper
{
    public static function mergeArtists($artists)
    {
        $_artists = array();
        foreach ($artists as $artist) {
            $name = $artist->name;
            if (strrpos($name, ' (')) {
                $name = trim(substr($name, 0, strrpos($name, ' (')));
            }
            array_push($_artists, $name);
        }

        return implode(', ', $_artists);
    }

    public static function mergeFormats($formats)
    {
        $_formats = array();
        foreach ($formats as $format) {
            array_push($_formats, sprintf(
                "%s [%s]",
                $format->name,
                $format->qty
            ));
        }

        return implode(', ', $_formats);
    }

    public static function mergeLabels($labels)
    {
        $_labels = array();
        foreach ($labels as $label) {
            array_push($_labels, sprintf(
                "%s - %s",
                $label->name,
                $label->catno
            ));
        }

        return implode(', ', $_labels);
    }
}