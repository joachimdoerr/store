<?php
/**
 * User: joachimdoerr
 * Date: 23.09.18
 * Time: 16:13
 */

namespace Basecondition\Database;


use rex_yform_manager_table_api;

class DatabaseYManagerSchemaCreator
{
    public static function addColumnsToTable(array $column, $table)
    {
        $fields = array();

        foreach ($column as $key => $value) {
            $fields[$key] = array(
                "id" => $key,
                "table_name" => $table,
                "prio" => "1",
                "type_id" => "value",
                "type_name" => $value['payload']['type'],
                "db_type" => $value['payload']['type'],
                "list_hidden" => "0",
                "search" => "0",
                "name" => $value['Field'],
                "label" => (isset($value['payload']['label']) && !empty($value['payload']['label'])) ? $value['payload']['label'] : $value['Field'],
                "not_required" => "",
                "default" => "",
                "no_db" => "0",
                "max" => "",
                "message" => "",
                "type" => ""
            );
        }

        $schema = array(
            $table => array(
                'table' => array(
                    "status" => "1",
                    "table_name" => $table,
                    "name" => $table,
                    "list_amount" => "50",
                    "list_sortfield" => "id",
                    "list_sortorder" => "ASC",
                    "schema_overwrite" => "1",
                ),
                'fields' => $fields,
            )
        );

//        echo '<pre>';
//        print_r(json_encode($schema));die;

        rex_yform_manager_table_api::importTablesets(json_encode($schema));

    }
}