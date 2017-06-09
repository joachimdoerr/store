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
 * Class StoreDefinitionManager
 *
 * // init manager
 * $dm = new StoreDefinitionManager("addonname", "definition_folder/path/*.yml", "nav");
 * // create definition list
 * $dm->createDefinition(); // read cache or regenerate...
 * // get the definition list
 * $dm->getDefinition();
 */
class StoreDefinitionManager
{
    /**
     * @var string
     */
    private $searchSchema;

    /**
     * @var string
     */
    private $addonName;

    /**
     * @var string
     */
    private $definitionListType;

    /**
     * @var StoreDefinitionHandler
     */
    private $definitionListHandler;

    /**
     * @var StoreDefinitionCacheHandler
     */
    private $definitionCacheHandler;

    /**
     * DefinitionManager constructor.
     * @param string $addonName
     * @param string $searchSchema
     * @param string $definitionListType
     * @throws Exception
     * @author Joachim Doerr
     */
    public function __construct($addonName, $searchSchema, $definitionListType = 'default')
    {
        if (!is_null($addonName))
            if (rex_addon::exists($addonName))
                $this->addonName = $addonName;
            else
                throw new \Exception("The addon $addonName don't exists.");

        $this->searchSchema = $searchSchema;
        $this->definitionListType = $definitionListType;
        $this->definitionListHandler = new StoreDefinitionHandler($this->getSearchSchema(), $this->getAddonName(), $this->getDefinitionListType());

        // create definition list
        $this->createList();

        $this->definitionCacheHandler = new StoreDefinitionCacheHandler($this->getDefinition(), $this->getSearchSchema(), $this->getAddonName(), null, $this->getDefinitionListType());
    }

    /**
     * @return DefinitionItem[]
     * @throws Exception
     * @author Joachim Doerr
     */
    public function createDefinition()
    {
        // we must create?
        $create = false;

        // is cache exist?
         if (!$this->getDefinitionCacheHandler()->isCacheFileExist()) {
             // write cache file
             $this->getDefinitionCacheHandler()->writeCacheFile();
             $create = true;
         }

        // check definition time...
        foreach ($this->getDefinition() as $definitionItem) {
            foreach ($definitionItem->getYmlFiles() as $fileKey => $file) // check for all files
                try { // is file newer as our cache file?
                    if ($this->getDefinitionCacheHandler()->isCacheModifiedTimesLessThanFile($file)) // yes
                        $create = true; // we must recreate
                } catch (\Exception $e) {
                    $create = true;
                }

            if (!file_exists($definitionItem->getPayload('data_path'))) // it don't exist
                $create = true; // we must create...
        }

        // so we must create or recreate...
        if ($create == true) {
            // create definitions
            $this->createDefinitions();
            // write definitions files
            $this->writeDefinitionJson();
            // final write cache file
            $this->getDefinitionCacheHandler()->writeCacheFile();
        } else
            $this->readDefinitionJson(true); // load definitions list

        // that's it.
        return $this->getDefinition();
    }

    /**
     * @return $this
     * @author Joachim Doerr
     */
    private function createList()
    {
        // create definition list for type
        switch ($this->getDefinitionListType()) {
            case 'nav':
                $this->getDefinitionListHandler()->createNavigationDefinition();
                break;
            default:
                $this->getDefinitionListHandler()->createDefaultDefinition();
        }
        return $this;
    }

    /**
     * @return $this
     * @author Joachim Doerr
     */
    private function createDefinitions()
    {
        if (sizeof($this->getDefinition()) > 0) // create definitions
            foreach ($this->getDefinition() as $definitionItem)
                StoreDefinitionFileHandler::createDefinition($definitionItem);

        return $this;
    }

    /**
     * @return $this
     * @author Joachim Doerr
     */
    private function writeDefinitionJson()
    {
        if (sizeof($this->getDefinition()) > 0)
            foreach ($this->getDefinition() as $definitionItem) // write definitions
                StoreDefinitionFileHandler::writeDefinitionJson($definitionItem);

        return $this;
    }

    /**
     * @param bool $assoc
     * @return $this
     * @author Joachim Doerr
     */
    private function readDefinitionJson($assoc = false)
    {
        if (sizeof($this->getDefinition()) > 0)
            foreach ($this->getDefinition() as $definitionItem) // read definitions
                $definitionItem->setDefinitions(StoreDefinitionFileHandler::readDefinitionJson($definitionItem, $assoc));

        return $this;
    }

    /**
     * @return StoreDefinitionHandler
     * @author Joachim Doerr
     */
    public function getDefinitionListHandler()
    {
        return $this->definitionListHandler;
    }

    /**
     * @return StoreDefinitionCacheHandler
     * @author Joachim Doerr
     */
    public function getDefinitionCacheHandler()
    {
        return $this->definitionCacheHandler;
    }

    /**
     * @return DefinitionItem[]
     * @author Joachim Doerr
     */
    public function getDefinition()
    {
        return $this->getDefinitionListHandler()->getDefinition();
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
     * @return string
     * @author Joachim Doerr
     */
    public function getDefinitionListType()
    {
        return $this->definitionListType;
    }
}