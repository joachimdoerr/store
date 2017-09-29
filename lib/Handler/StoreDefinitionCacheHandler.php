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
 * Class StoreDefinitionCacheHandler
 *
 * // init manager
 * $dm = new StoreDefinitionManager("addonname", "definition_folder/path/*.yml", "nav");
 * // create definition list
 * $dm->createDefinition(); // read cache or regenerate...
 *
 * // init definition cache folder
 * $dch = new StoreDefinitionCacheHandler($dm->getDefinition(), "definition_folder/path/*.yml", "addonname");
 *
 * // cache file exist?
 * if ($dch->isCacheFileExist()) {
 *      // create files
 *      ...
 *      // create cache file
 *      $dch->writeCacheFile();
 * }
 * // compare with file
 * try {
 *      if ($dch->isCacheModifiedTimesLessThanFile($file)) { // yes
 *          $create = true;
 *      }
 * } catch (\Exception $e) {
 *      $create = true;
 * }
 */
class StoreDefinitionCacheHandler
{
    /**
     * @var string
     */
    private $cacheName;

    /**
     * @var string
     */
    private $cacheFile;

    /**
     * @var null|string
     */
    private $cacheFileSchema = 'cache/definitions/%s.json';

    /**
     * @var DefinitionItem[]
     */
    private $definition;

    /**
     * @var string
     */
    private $definitionListType;

    /**
     * DefinitionCacheHandler constructor.
     * @param DefinitionItem[] $definition
     * @param string $searchSchema
     * @param string $addonName
     * @param null|string $cacheFileSchema
     * @param string $definitionListType
     * @author Joachim Doerr
     */
    public function __construct($definition, $searchSchema, $addonName, $cacheFileSchema = null, $definitionListType = 'default')
    {
        if (!is_null($cacheFileSchema)) {
            $this->cacheFileSchema = $cacheFileSchema;
        }
        $this->definition = $definition;
        $this->definitionListType = $definitionListType;
        $this->cacheName = $definitionListType . '.' . md5(json_encode($definition) . $searchSchema . $addonName . $definitionListType);
        $this->cacheFile = rex_path::addonData($addonName, sprintf($this->cacheFileSchema, $this->cacheName));
    }

    /**
     * @return bool
     * @author Joachim Doerr
     */
    public function isCacheFileExist()
    {
        return file_exists($this->cacheFile);
    }

    /**
     * @return int
     * @author Joachim Doerr
     */
    public function getModifiedTime()
    {
        return filemtime($this->cacheFile);
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     * @author Joachim Doerr
     */
    public function isCacheModifiedTimesLessThanFile($file)
    {
        if (!$this->isCacheFileExist()) {
            return true;
        }
        if (!file_exists($file)) {
            throw new \Exception("The file $file to compare modified times is not exists.");
        }
        return (filemtime($file) >= $this->getModifiedTime());
    }

    /**
     * @return $this
     * @author Joachim Doerr
     */
    public function writeCacheFile()
    {
        // delete old cache files
        array_map('unlink', glob(pathinfo($this->cacheFile, PATHINFO_DIRNAME) . '/' . $this->definitionListType . '*.' . pathinfo($this->cacheFile, PATHINFO_EXTENSION)));

        // create path
        if (!is_dir(pathinfo($this->cacheFile, PATHINFO_DIRNAME))) {
            mkdir(pathinfo($this->cacheFile, PATHINFO_DIRNAME), 0777, true);
        }
        // put file
        file_put_contents($this->cacheFile, json_encode(array('name'=>$this->cacheName)));
        return $this;
    }
}