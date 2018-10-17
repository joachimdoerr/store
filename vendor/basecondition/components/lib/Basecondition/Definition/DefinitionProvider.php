<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Definition;


use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\Yaml\Parser;

class DefinitionProvider
{
    const CACHE_TTL = 345600; // 48h
    const CACHE_PATH = '%s/.definition';

    /**
     * @param string|array $searchSchemes
     * @param string $searchPath
     * @param null $cachePath
     * @param null $mergeHandler
     * @param bool $isCached
     * @param int $cacheTTL
     * @return false|mixed
     * @author Joachim Doerr
     */
    public static function load($searchSchemes, $searchPath, $cachePath = null, $mergeHandler = null, $isCached = false, $cacheTTL = self::CACHE_TTL)
    {
        if (is_string($searchSchemes)) $searchSchemes = [$searchSchemes];
        $searchPath = str_replace('//', '/', $searchPath . '/');

        # dump($searchSchemes);

        if (is_null($cachePath)) $cachePath = $searchPath;
        $cachePath = str_replace('//', '/',sprintf(self::CACHE_PATH, $cachePath)) . '/';
        $cache = new FilesystemCache($cachePath);

        $ymlFiles = array();

        // find all files by schema
        foreach ($searchSchemes as $searchScheme)
            $ymlFiles = array_merge($ymlFiles, glob($searchPath . $searchScheme)); // find all files

        // use last modification date for cache key
        $lastModifications = array_map(function ($f) {
            return filemtime($f);
        }, $ymlFiles);
        // set cache keys
        $cacheKey = md5(sprintf("%s:%s", __CLASS__, implode('.',$searchSchemes))) . '.' . md5(implode('.', $lastModifications));
        // load from cache
        if ($definition = $cache->fetch($cacheKey)) {
            if ($isCached) {
                return array(
                    'search_schemas' => $searchSchemes,
                    'search_schema' => $searchSchemes[0],
                    'cached' => true,
                    'cache_key' => $cacheKey,
                    'data' => $definition
                );
            }
            return $definition;
        }
        // parse yml
        $parser = new Parser();
        $parsedContents = array_map(function ($f) use ($parser) {
            return $parser->parse(file_get_contents($f));
        }, $ymlFiles);
        // merge definitions by parsed contents
        $definition = self::mergeParsedContents($parsedContents, $mergeHandler);
        // save cache
        $cache->save($cacheKey, $definition, $cacheTTL);
        if ($isCached) {
            return array(
                'search_schemas' => $searchSchemes,
                'search_schema' => $searchSchemes[0],
                'cached' => false,
                'cache_key' => $cacheKey,
                'data' => $definition
            );
        }
        return $definition;
    }

    /**
     * @param array $parsedContents
     * @param DefinitionMergeInterface|null $mergeHandler
     * @return mixed
     * @author Joachim Doerr
     */
    public static function mergeParsedContents(array $parsedContents, $mergeHandler = null)
    {
        if ($mergeHandler instanceof DefinitionMergeInterface) {
            return $mergeHandler::merge($parsedContents);
        }
        return $parsedContents;
    }
}