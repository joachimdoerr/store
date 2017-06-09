<?php

/**
 * User: joachimdoerr
 * Date: 08.04.17
 * Time: 10:56
 */
class StoreActionView
{
    const SEARCH_SCHEMA = "*/default/%s.yml";

    /**
     * @var string
     */
    public $addonKey;

    /**
     * @var string
     */
    public $func;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $searchFile;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var array
     */
    public $urlParameters = array();

    /**
     * @var StoreDefinitionManager
     */
    private $definitionManager;

    /**
     * StoreActionView constructor.
     * @param string $addonKey
     * @param string $func
     * @param int $id
     * @param string $searchFile
     * @param bool $debug
     * @param array $urlParameters
     * @author Joachim Doerr
     */
    public function __construct($addonKey, $func, $id, $searchFile, $debug = false, array $urlParameters = array())
    {
        $this->addonKey = $addonKey;
        $this->searchFile = $searchFile;
        $this->definitionManager = new StoreDefinitionManager($this->addonKey, sprintf(self::SEARCH_SCHEMA, $this->searchFile));
        $this->definitionManager->createDefinition();

        $this->id = $id;
        $this->func = $func;
        $this->debug = $debug;
        $this->urlParameters = $urlParameters;


    }


    /*        $params['addon']->getName(),
            $params['search_file'],
            $func,
            $params['id'],
            $params['debug'],
            $params['url_parameters']
    */



    public function getFunc()
    {
        return '';
    }
}