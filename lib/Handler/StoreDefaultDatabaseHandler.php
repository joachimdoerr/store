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
 * Class StoreDatabaseHandler
 */
class StoreDefaultDatabaseHandler
{
    /**
     * @param array $fieldset
     * @param $table
     * @author Joachim Doerr
     * @return array
     */
    private static function handleFieldset($fieldset, $table)
    {
        $columns = StoreColumnHelper::getColumns($table);
        $create = array();
        $update = array();
        $select = array();

        foreach ($fieldset as $field) {
            $result = self::handleDatabaseField($field, $columns);
            if (array_key_exists('create', $result))
                $create[] = $result['create'];

            if (array_key_exists('update', $result))
                $update[] = $result['update'];

            if (array_key_exists('select', $result))
                $select[] = $result['select'];
        }
        return array('update'=>$update, 'create'=>$create, 'select'=>$select);
    }

    /**
     * @param $field
     * @param $columns
     * @return array
     * @author Joachim Doerr
     */
    private static function handleDatabaseField($field, $columns)
    {
        if (array_key_exists('type', $field) && array_key_exists('name', $field)) {
            // for now we check only the type
            if ($column = StoreColumnHelper::isInColumnList($columns, $field['name'])) {
                switch ($field['type']) {
                    case 'bool':
                        $type = 'tinyint';
                        break;
                    default:
                        $type = $field['type'];
                }

                if (array_key_exists('no_db', $field) && $field['no_db'] == 1)
                    return array('false');

                if (strpos(strtolower($column['Type']), strtolower($type)) !== false)
                    return array('select' => $field['name']);
                else
                    return array(
                        'update' => array('Field' => $field['name'], 'Type' => self::switchColumnType($field['type'])),
                        'select' => $field['name']
                    );

            } else
                return array('create'=>array('Field' => $field['name'], 'Type' => self::switchColumnType($field['type'])));
        }
        return array('false');
    }

    /**
     * @param array $fieldset
     * @param $table
     * @author Joachim Doerr
     * @return array
     */
    public static function handleDatabaseFieldset(array $fieldset, $table)
    {
        $newFieldset = StoreYManagerHandler::handleDatabaseFieldset($fieldset, $table);
        return self::handleFieldset($newFieldset, $table);
    }

    /**
     * @param array $fieldset
     * @param $table
     * @author Joachim Doerr
     * @return array
     */
    public static function handleLangDatabaseFieldset(array $fieldset, $table)
    {
        $newFieldset = StoreYManagerHandler::handleLangDatabaseFieldset($fieldset, $table);
        return self::handleFieldset($newFieldset, $table);
    }

    /**
     * @param array $relations
     * @param $table
     * @return array
     * @author Joachim Doerr
     */
    public static function handleDatabaseRelations(array $relations, $table)
    {
        // TODO ...
        // return self::handleFieldset($fieldset, $table);
    }

    /**
     * @param $type
     * @return string
     * @author Joachim Doerr
     */
    private static function switchColumnType($type) {
        switch ($type) {
            case 'varchar':
                return 'VARCHAR(255) NULL';
                break;
            case 'bool':
                return 'BOOL NOT NULL DEFAULT 0';
                break;
            case 'id':
                return 'INT(11) unsigned NOT NULL auto_increment';
                break;
            case 'datetime':
            case 'date':
                return 'DATETIME NULL';
            case 'number':
            case 'int':
            case 'prio':
                return 'INT(11) NULL';
                break;
            case 'float':
                return 'FLOAT(11) NULL';
                break;
            case 'text area':
            case 'textarea':
            case 'markup':
            case 'select':
            case 'text':
            default:
                return 'TEXT NULL';
                break;
        }
    }
}