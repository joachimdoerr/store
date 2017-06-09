<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class StoreYManagerHandler
 * TODO description
 */
class StoreYManagerHandler
{
    /**
     * @param $fieldSet
     * @param $table
     * @return array
     * @author Joachim Doerr
     */
    public static function handleDatabaseFieldset($fieldSet, $table)
    {
        $newFieldSet = array();
        if (is_array($fieldSet))
            foreach ($fieldSet as $key => $field)
                if (is_array($field) && array_key_exists('name', $field))
                    $newFieldSet[] = self::setDefaults($field, $table);

        return $newFieldSet;
    }

    /**
     * @param array $fieldSet
     * @param $table
     * @return array
     * @author Joachim Doerr
     */
    public static function handleLangDatabaseFieldset($fieldSet, $table)
    {
        $newFieldSet = array();
        if (is_array($fieldSet))
            foreach ($fieldSet as $key => $field)
                switch (TRUE) {
                    case (strpos($key, 'fields') !== false):
                        foreach ($field as $value)
                            foreach (rex_clang::getAll() as $clang)
                                if (is_array($value) && array_key_exists('name', $value)) {
                                    $newField = $value;
                                    $newField['name'] = $value['name'] . '_' . $clang->getId();
                                    $newFieldSet[] = self::setDefaults($newField, $table);
                                }
                        break;
                    case (strpos($key, 'panel') !== false):
                        $newFieldSet = array_merge($newFieldSet, self::handleLangDatabaseFieldset($field, $table));
                        break;
                }

        return $newFieldSet;
    }

    /**
     * @param array $relations
     * @param $table
     * @return array
     * @author Joachim Doerr
     */
    public static function handleDatabaseRelations(array $relations, $table)
    {
        //$fieldSet = array();
        //foreach ($relations as $item) {
        //    $field = new stdClass();
        //    $field['name'] = $item['name'];
        //    $field['type'] = 'varchar';
        //    $fieldSet[] = $field;
        //}
    }

    /**
     * @param array $fieldSet
     * @param $table
     * @return array
     * @author Joachim Doerr
     */
    private static function setDefaults(array $fieldSet, $table)
    {
        $fieldSet['not_required'] = (!array_key_exists('not_required', $fieldSet)) ? 1 : $fieldSet['not_required'];
        $fieldSet['table_name'] = $table;
        return array_merge($fieldSet , self::switchColumnType($fieldSet));
    }

    /**
     * @param array $fieldSet
     * @return array
     * @author Joachim Doerr
     */
    private static function switchColumnType(array $fieldSet)
    {
        if (is_array($fieldSet) && array_key_exists('type', $fieldSet))
            switch ($fieldSet['type']) {
                case 'datetime':
                    return array(
                        "type_id" => "value",
                        "type_name" => "datetime"
                    );
                case 'number':
                case 'int':
                case 'prio':
                    return array(
                        "type_id" => "value",
                        "type_name" => "integer"
                    );
                case 'float':
                    return array(
                        "type_id" => "value",
                        "type_name" => "float"
                    );
                case 'bool':
                case 'varchar':
                case 'text area':
                case 'text_area':
                case 'textarea':
                case 'markup':
                case 'select':
                case 'text':
                default:
                    return array(
                        "type_id" => "value",
                        "type_name" => "text"
                    );
            }
        return array();
    }

}