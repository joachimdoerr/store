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
 * Class StoreDatabaseManager
 * // init store database manager
 * $dbm = new StoreDatabaseManager("addonname", "definition_folder/path/*.yml");
 * TODO description
 * TODO yform manager handler usage
 */
class StoreDatabaseManager
{
    const SEARCH_SCHEMA = "*/default/*.yml";

    /**
     * @var string
     */
    private $searchSchema;

    /**
     * @var string
     */
    private $addonName;

    /**
     * @var StoreDefinitionManager
     */
    private $definitionManager;

    /**
     * @var string
     */
    private $definitionListType;

    /**
     * StoreDatabaseManager constructor.
     * @param string $addonName
     * @param string $searchSchema
     * @param string $definitionListType
     * @author Joachim Doerr
     */
    public function __construct($addonName, $searchSchema = null, $definitionListType = 'default')
    {
        $this->addonName = $addonName;
        $this->searchSchema = self::SEARCH_SCHEMA;

        if (!is_null($searchSchema)) {
            $this->searchSchema = $searchSchema;
        }

        $this->definitionListType = $definitionListType;
        $this->definitionManager = new StoreDefinitionManager($this->getAddonName(), $this->getSearchSchema(), $definitionListType);

        // create definition list
        $this->getDefinitionManager()->createDefinition(); // read cache or regenerate...
    }

    /**
     * @author Joachim Doerr
     */
    public function executeCustomTablesHandling()
    {
        switch ($this->definitionListType) {
            case 'yform':
                return $this->yformExecution();
            default:
            case 'default':
                return $this->storeExecution();
        }
    }

    /**
     * @author Joachim Doerr
     */
    public function executeCustomTablesLangHandling()
    {
        switch ($this->definitionListType) {
            case 'yform':
                return $this->yformLangExecution();
            default:
            case 'default':
                return $this->storeLangExecution();
        }
    }

    /**
     * @return bool
     * @author Joachim Doerr
     */
    private function storeExecution()
    {
        $list = array();
        foreach ($this->getDefinition() as $definitionItem) {
            // reset arrays
            $create = array();
            $update = array();

            // create arrays for execution
            foreach ($definitionItem->getDefinitions() as $key => $fieldset)
                switch (TRUE) {
                    case (strpos($key, 'lang') !== false):
                        $fields = StoreDefaultDatabaseHandler::handleLangDatabaseFieldset($fieldset, $definitionItem->getPayload('table'));
                        $create = array_merge($create, $fields['create']);
                        $update = array_merge($update, $fields['update']);
                        break;
                    case (strpos($key, 'fields') !== false):
                        $fields = StoreDefaultDatabaseHandler::handleDatabaseFieldset($fieldset, $definitionItem->getPayload('table'));
                        $create = array_merge($create, $fields['create']);
                        $update = array_merge($update, $fields['update']);
                    break;
                }

            // execute arrays
            if (sizeof($create) > 0) {
                // TODO TODO
                // execute ymanager table create
                StoreColumnHelper::addColumnsToTable($create, $definitionItem->getPayload('table'));
            }
            if (sizeof($update) > 0) {
                // TODO TODO
                // execute ymanager table update
                StoreColumnHelper::changeColumnsInTable($update, $definitionItem->getPayload('table'));
            }

            $list[] = array('item' => $definitionItem, 'create' => $create, 'update' => $update);
        }

        return true;
    }

    /**
     * @return bool
     * @author Joachim Doerr
     */
    private function yformExecution()
    {
        foreach ($this->getDefinition() as $definitionItem) {
            rex_yform_manager_table_api::setTable(array('table_name' => $definitionItem->getPayload('table'), 'hidden' => 1, 'status' => 1));

            $fieldSet = array();

            foreach ($definitionItem->getDefinitions() as $key => $definition) {
                switch (TRUE) {
                    case (strpos($key, 'lang') !== false):
                        $fieldSet = array_merge($fieldSet, StoreYManagerHandler::handleLangDatabaseFieldset($definition, $definitionItem->getPayload('table')));
                        break;
                    case (strpos($key, 'fields') !== false):
                        $fieldSet = array_merge($fieldSet, StoreYManagerHandler::handleDatabaseFieldset($definition, $definitionItem->getPayload('table')));
                        break;
                }
            }

            $table = rex_yform_manager_table_api::setTable(array('table_name' => $definitionItem->getPayload('table')), $fieldSet);
        }
        return true;
    }

    /**
     * @return bool
     * @author Joachim Doerr
     */
    private function storeLangExecution()
    {
        foreach ($this->getDefinition() as $definitionItem) {
            StoreColumnHelper::addColumnsToTable(StoreColumnHelper::getAllLangColumns($definitionItem->getPayload('table')), $definitionItem->getPayload('table'));
        }
        return true;
    }

    /**
     * @return bool
     * @author Joachim Doerr
     */
    private function yformLangExecution()
    {
        #foreach ($this->getDefinition() as $definitionItem) {
        #    StoreColumnHelper::addColumnsToTable(StoreColumnHelper::getAllLangColumns($definitionItem->getPayload('table')), $definitionItem->getPayload('table'));
        #}
        return true;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getSearchSchema()
    {
        return $this->searchSchema;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getAddonName()
    {
        return $this->addonName;
    }

    /**
     * @return StoreDefinitionManager
     * @author Joachim Doerr
     */
    public function getDefinitionManager()
    {
        return $this->definitionManager;
    }

    /**
     * @return DefinitionItem[]
     * @author Joachim Doerr
     */
    public function getDefinition()
    {
        return $this->getDefinitionManager()->getDefinition();
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getDefinitionListType()
    {
        return $this->definitionListType;
    }
}