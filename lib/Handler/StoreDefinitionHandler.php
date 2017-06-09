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
 * Class StoreDefinitionHandler
 *
 * the definition list handler load all definition files by provided search schema and addon key
 *
 * // init definition list handler
 * $handler = new DefinitionListHandler("definition_folder/path/*.yml", "addonname");
 * // create definition list
 * $handler->createNavigationDefinitionList();
 * // use the list
 * $handler->getDefinitionList();
 */
class StoreDefinitionHandler
{
    /**
     * @var DefinitionItem[]
     */
    private $definition = array();

    /**
     * @var string
     */
    private $definitionType;

    /**
     * @var string
     */
    private $addonName;

    /**
     * @var string
     */
    private $searchSchema;

    /**
     * DefinitionListHandler constructor.
     * @param string $searchSchema
     * @param string $addon
     * @param string $definitionListType
     * @throws Exception
     * @author Joachim Doerr
     */
    public function __construct($searchSchema, $addon, $definitionListType = 'default')
    {
        $this->searchSchema = $searchSchema;
        $this->definitionType = $definitionListType;
        // set addon name
        if (rex_addon::exists($addon))
            $this->addonName = $addon;
        else
            throw new \Exception("The addon $addon don't exists.");
    }

    /**
     * @return DefinitionItem[]
     * @author Joachim Doerr
     */
    public function createDefaultDefinition()
    {
        // read addon paths
        $addonFiles = $this->getAddonFiles();
        // read plugin paths
        $pluginsFiles = $this->getPluginsFiles();
        // addon list
        $list = array();

        // create addon list
        if (sizeof($addonFiles) > 0)
            foreach ($addonFiles as $file)
                if (array_key_exists(pathinfo($file, PATHINFO_FILENAME), $list)) {
                    if (strpos($file, '/data/') !== false)
                        $list[pathinfo($file, PATHINFO_FILENAME)]['paths'][$this->getAddonName() . '_data'] = $file;
                } else {
                    $list[pathinfo($file, PATHINFO_FILENAME)] = array(
                        'name' => pathinfo($file, PATHINFO_FILENAME),
                        'paths' => array($this->getAddonName() => $file),
                        'table' => rex::getTablePrefix() . $this->addonName . '_' . pathinfo($file, PATHINFO_FILENAME),
                        'path_base' => str_replace(rex_path::addon($this->addonName), '', pathinfo($file, PATHINFO_DIRNAME)),
                        'data_file' => str_replace(array(rex_path::addon($this->addonName), '.yml'), array(rex_path::addonData($this->addonName), '.json'), $file)
                    );
                }

        // merge addon and plugin list
        if (sizeof($pluginsFiles) > 0)
            foreach ($pluginsFiles as $key => $pluginFiles)
                foreach ($pluginFiles as $file)
                    if (array_key_exists(pathinfo($file, PATHINFO_FILENAME), $list)) {
                        if (strpos($file, '/data/') !== false)
                            $list[pathinfo($file, PATHINFO_FILENAME)]['paths'][$key . '_data'] = $file;
                        else
                            $list[pathinfo($file, PATHINFO_FILENAME)]['paths'][$key] = $file;
                    } else {
                        $pluginPath = explode('/', str_replace(rex_path::addon($this->addonName), '', pathinfo($file, PATHINFO_DIRNAME)));
                        unset($pluginPath[0], $pluginPath[1]);
                        $pluginPath = implode('/', $pluginPath) . '/';
                        $list[pathinfo($file, PATHINFO_FILENAME)] = array(
                            'name' => pathinfo($file, PATHINFO_FILENAME),
                            'paths' => array($key => $file),
                            'table' => rex::getTablePrefix() . $this->addonName . '_' . pathinfo($file, PATHINFO_FILENAME),
                            'path_base' => $pluginPath,
                            'data_file' => rex_path::addonData($this->addonName, $pluginPath) . pathinfo($file, PATHINFO_FILENAME) . '.json'
                        );
                    }

        // add list item to definition lists
        if (sizeof($addonFiles) > 0 || sizeof($pluginsFiles) > 0)
            foreach ($list as $listItemName => $listItem) {
                // init object
                $item = new DefinitionItem();
                // map array to object
                $item->setName($listItemName)
                    ->setType($this->getDefinitionType())
                    ->setYmlFiles($listItem['paths'])
                    ->setBasePath($listItem['path_base'])
                    ->setDataFilePath($listItem['data_file'])
                    ->addPayload('path_base', $listItem['path_base'])
                    ->addPayload('data_path', $listItem['data_file'])
                    ->addPayload('table', $listItem['table'])
                    ->addPayload('table_key', StoreHelper::getTableKey($listItemName));

                // add to definition lists
                $this->definition[] = $item;
            }

        return $this->getDefinition();
    }

    /**
     * @return array
     * @author Joachim Doerr
     */
    public function createNavigationDefinition()
    {
        // first create definition lists
        $this->createDefaultDefinition();
        // unset table and table key
        foreach ($this->definition as $item) {
            $payload = $item->getPayload();
            unset($payload['table'], $payload['table_key']);
            $item->setPayload($payload);
        }
        return $this->getDefinition();
    }

    /**
     * @return array
     * @author Joachim Doerr
     */
    private function getAddonFiles()
    {
        // path files
        $addonFiles = array();
        // addon paths
        $addonSearchPath = array(
            rex_path::addon($this->getAddonName()),
            rex_path::addonData($this->getAddonName())
        );
        // get addon files
        foreach ($addonSearchPath as $path)
            $addonFiles = array_merge($addonFiles, glob($path . $this->getSearchSchema())); // merge all files

        return $addonFiles;
    }

    /**
     * @return array
     * @author Joachim Doerr
     */
    private function getPluginsFiles()
    {
        // path files
        $pluginFiles = array();
        // plugin paths
        $pluginSearchPaths = array();
        // only form the installed plugins
        foreach(rex_plugin::getInstalledPlugins($this->addonName) as $plugin) {
            $pluginSearchPaths[$plugin->getName()][] = $plugin->getPath();
            $pluginSearchPaths[$plugin->getName()][] = $plugin->getDataPath();
        }
        // get plugin files
        foreach ($pluginSearchPaths as $plugin => $pluginPaths) {
            $files = array();
            foreach ($pluginPaths as $path) // merge all files
                $files = array_merge($files, glob($path . $this->getSearchSchema()));

            // add plugin files
            $pluginFiles[$plugin] = $files;
        }
        return $pluginFiles;
    }

    /**
     * @return DefinitionItem[]
     * @author Joachim Doerr
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return mixed
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
    public function getSearchSchema()
    {
        return $this->searchSchema;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getDefinitionType()
    {
        return $this->definitionType;
    }
}