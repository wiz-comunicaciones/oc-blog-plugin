<?php namespace Wiz\Blog\Classes;

class ListColumnTypes {

    /**
     * Outputs a link to a file model
     * Column options:
     *      relationName: i.e. avatar
     */
    public static function urlColumnType($value, $column, $record)
    {
        if ($column->config['relationName'] === null || $record->{$column->config['relationName']} === null) {
            return $column->config['default'] != null ? $column->config['default'] : '';
        }

        $file = $record->{$column->config['relationName']};
        $mask = '<a id="system-models-file-id-%s" href="%s" title="%s" target="_blank">%s</a>';
        return sprintf($mask, $file->id, $file->path, $file->title, $file->file_name);
    }

    /**
     * We asume we are using this to display a file that's attached to the model ($attachOne)
     * Normal column definition example:
     * columnName:
     *      type: wiz_blog_image
     *      relationName: asDefinedInAttachOneInModel
     *      width: imageWidth (optional, default:90)
     */
    public static function imageColumnType($value, $column, $record)
    {
        if ($column->config['relationName'] === null || $record->{$column->config['relationName']} === null) {
            return $column->config['default'] != null ? $column->config['default'] : '';
        }

        $mask = '<img src="%s" alt="%s" title="%s" width="%s">';
        $src = $record->{$column->config['relationName']}->getPath();
        $alt = $record->{$column->config['relationName']}->title ? : '';
        $width = ($column->config['width'] != null) ? $column->config['width'] : '90';

        return sprintf( $mask, $src, $alt, $alt, $width );
    }

    /**
     * Outputs strings depending on a given Id. Meant for static id definition (types and things like that)
     * Normal column definition example:
     * columnName:
     *      type: wiz_blog_translate_id
     *      method: modelMethodThatReturnsIds=>Labels()
     */
    public static function translateIdColumnType($value, $column, $record)
    {
        if ($column->config['method'] === null || !method_exists($record, $column->config['method'])) {
            return $column->config['default'] != null ? $column->config['default'] : '';
        }

        $dictionary = call_user_func([$record, $column->config['method']]);

        return isset($dictionary[$value]) ? $dictionary[$value] : $column->config['default'];
    }

    /**
     * Outputs strings with HTML parsed
     */
    public static function htmlColumnType($value, $column, $record)
    {
        return $value;
    }

    /**
     * Outputs attachment count as referenced by the attachOne, attachMany properties.
     * Normal column definition example:
     * columnName:
     *      type: wiz_blog_attachmentcount
     *      relation: attachManyRelationName
     */
    public static function attachmentCountColumnType($value, $column, $record)
    {
        if ($column->config['relation'] === null) {
            return $column->config['default'] != null ? $column->config['default'] : '';
        }
        return $record->{$column->config['relation']}->count();
    }

    /**
     * Number formats the column.
     * Column options:
     *      prefix: i.e. $
     *      digits: i.e. 2 (number of decimals)
     */
    public static function numberColumnType($value, $column, $record)
    {
        $prefix = isset($column->config['prefix']) ? $column->config['prefix'] : '';
        $digits = isset($column->config['digits']) ? $column->config['digits'] : 0;
        return $prefix . number_format($value, $digits, ',', '.');
    }

    /**
     * Outputs strings depending on a given Id. Meant for static id definition (static types and things like that)
     * Normal column definition example:
     * columnName:
     *      method: modelMethodThatReturnsIds=>Labels()
     */
    public static function optionsColumnType($value, $column, $record)
    {
        if ($column->config['method'] === null || !method_exists($record, $column->config['method'])) {
            return $column->config['default'] != null ? $column->config['default'] : '';
        }

        $dictionary = call_user_func([$record, $column->config['method']]);

        return isset($dictionary[$value]) ? $dictionary[$value] : $column->config['default'];
    }
}