<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Database;


use Basecondition\Definition\DefinitionProvider;
use rex;
use rex_addon;

class DatabaseManager
{
    const SEARCH_SCHEMA = '*/definitions/default/%s.yml';

    /**
     * @param $addonName
     * @param $searchPath
     * @param string $searchSchema
     * @author Joachim Doerr
     */
    public static function provideSchema($addonName, $searchPath, $searchSchema = self::SEARCH_SCHEMA)
    {
        $definitions = self::getDefinitions($addonName, $searchPath, $searchSchema);

        if (sizeof($definitions) > 0)
            foreach ($definitions as $definition) {

                if ($definition['cached'] === true)
                    continue;

                if (array_key_exists('data', $definition)) {
                    // table name
                    $tableName = rex::getTablePrefix() . $addonName . '_' . pathinfo($definition['search_schema'], PATHINFO_FILENAME);

                    // reset arrays
                    $create = array();
                    $update = array();

                    // create arrays for execution
                    foreach ($definition['data'] as $key => $fieldset) {
                        switch (TRUE) {
                            case (strpos($key, 'lang') !== false):
                                $fields = DatabaseFieldsetHandler::handleLangDatabaseFieldset($fieldset, $tableName);
                                $create = array_merge($create, $fields['create']);
                                $update = array_merge($update, $fields['update']);
                                break;
                            case (strpos($key, 'fields') !== false):
                                $fields = DatabaseFieldsetHandler::handleDatabaseFieldset($fieldset, $tableName);
                                $create = array_merge($create, $fields['create']);
                                $update = array_merge($update, $fields['update']);
                                break;
                        }
                    }

                    // execute arrays
                    if (sizeof($create) > 0)
                        DatabaseSchemaCreator::addColumnsToTable($create, $tableName);

                    if (sizeof($update) > 0)
                        DatabaseSchemaCreator::changeColumnsInTable($update, $tableName);
                }
            }
    }

    /**
     * @param $addonName
     * @param $searchPath
     * @param string $searchSchema
     * @author Joachim Doerr
     */
    public static function provideLangSchema($addonName, $searchPath, $searchSchema = self::SEARCH_SCHEMA)
    {
        $definitions = self::getDefinitions($addonName, $searchPath, $searchSchema);

        if (sizeof($definitions) > 0)
            foreach ($definitions as $definition)
                if (array_key_exists('data', $definition)) {
                    // table name
                    $tableName = rex::getTablePrefix() . $addonName . '_' . pathinfo($definition['search_schema'], PATHINFO_FILENAME);
                    DatabaseSchemaCreator::addColumnsToTable(DatabaseSchemaCreator::getAllLangColumns($tableName), $tableName);
                }
    }

    /**
     * @param $addonName
     * @param $searchPath
     * @param $searchSchema
     * @return array
     * @author Joachim Doerr
     */
    private static function getDefinitions($addonName, $searchPath, $searchSchema)
    {
        $addon = rex_addon::get($addonName);
        $searchPath = str_replace('//', '/', $searchPath . '/');
        $searchFiles = glob($searchPath . sprintf($searchSchema, '*')); // find all files
        $definitions = [];

        if (is_array($searchFiles) && sizeof($searchFiles) > 0)
            foreach ($searchFiles as $searchFile) {
                $s_files = array(sprintf($searchSchema, pathinfo($searchFile, PATHINFO_FILENAME)));
                $definitions[] = DefinitionProvider::load($s_files, $searchPath, $addon->getCachePath(), new DatabaseDefinitionMergeHandler, true);
            }

        return $definitions;
    }
}