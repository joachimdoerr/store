<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Utils;


use rex_extension_point;
use rex_request;
use rex_sql;

class ActionHelper
{
    /**
     * togglet bool data column
     * @param $table
     * @param $id
     * @param null $column
     * @return boolean
     * @author Joachim Doerr
     */
    public static function toggleBoolData($table, $id, $column = NULL)
    {
        if (!is_null($column)) {
            $sql = rex_sql::factory();
            $sql->setQuery("UPDATE $table SET $column=ABS(1-$column) WHERE id=$id");
            return true;
        } else {
            return false;
        }
    }

    /**
     * clone data
     * @param $table
     * @param $id
     * @return boolean
     * @author Joachim Doerr
     */
    static public function cloneData($table, $id)
    {
        $sql = rex_sql::factory();
        $fields = $sql->getArray('DESCRIBE `' . $table . '`');
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {
                if ($field['Key'] != 'PRI' && $field['Field'] != 'status') {
                    $queryFields[] = $field['Field'];
                }
            }
        }
        $sql->setQuery('INSERT INTO ' . $table . ' (`' . implode('`, `', $queryFields) . '`) SELECT `' . implode('`, `', $queryFields) . '` FROM ' . $table . ' WHERE id =' . $id);
        return true;
    }

    /**
     * delete data
     * @param $table
     * @param $id
     * @return boolean
     * @author Joachim Doerr
     */
    static public function deleteData($table, $id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery("DELETE FROM $table WHERE id=$id");
        return true;
    }

    /**
     * delete data
     * @param $table
     * @param $id
     * @return boolean
     * @author Joachim Doerr
     */
    static public function statusData($table, $id)
    {
        self::toggleBoolData($table, $id, 'status');
        return true;
    }


    /**
     * @param rex_extension_point $params
     * @author Joachim Doerr
     * @return string
     *
     * rex_extension::register('REX_FORM_SAVED', function (rex_extension_point $params) {
        ActionHelper::preSaveStatusChange($params);
        });
     */
    public static function preSaveStatusChange(rex_extension_point $params)
    {
        $param = $params->getParams();
        /** @var rex_form $form */
        $form = $param['form'];
        $post = rex_request::post($form->getName());
        $status = (array_key_exists('status', $post) && array_key_exists(1, $post['status']) && $post['status'][1] == 1) ? 1 : 0;

        // set status
        $sql = rex_sql::factory();
        $sql->setQuery("UPDATE ".$form->getTableName()." SET status = $status WHERE " . $form->getWhereCondition());
        return '';
    }

}