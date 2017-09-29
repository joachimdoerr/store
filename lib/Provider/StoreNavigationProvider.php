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
 * Class StoreNavigationProvider
 *
 * // init store navigation provider
 * $np = new StoreNavigationProvider('store');
 * // add custom navigation
 * $np->addCustomNavigation();
 * // get navigation definition
 * $np->getDefinition();
 */
class StoreNavigationProvider
{
    const SEARCH_SCHEMA = "*/nav/*.yml";

    /**
     * @var string
     */
    private $addonName;

    /**
     * @var string
     */
    private $searchSchema;

    /**
     * @var DefinitionItem[]
     */
    private $definition;

    /**
     * StoreNavigationProvider constructor.
     * @param $addonName
     * @param null $searchSchema
     * @author Joachim Doerr
     */
    public function __construct($addonName, $searchSchema = null)
    {
        $this->addonName = $addonName;
        $this->searchSchema = self::SEARCH_SCHEMA;

        if (!is_null($searchSchema)) {
            $this->searchSchema = $searchSchema;
        }
    }

    public function addCustomNavigation()
    {
        // definition is greater than 0
        if (sizeof($this->getDefinition()) > 0) { // go
            foreach ($this->getDefinition() as $definitionItem) { // set definition
                $definition = $definitionItem->getDefinitions();
                // sort definition by position
                StoreHelper::arraySortByColumn($definition, 'position');

                // sort all pages and subpages
                foreach ($definition as $key => $value)
                    if (is_array($value) && array_key_exists('pages', $value)) {
                        // sort it...
                        StoreHelper::arraySortByColumn($definition[$key]['pages'], 'position');

                        // each all pages
                        foreach ($definition[$key]['pages'] as $pk => $page)
                            foreach (array('subpages', 'subpages_edit', 'subpages_add', 'subpages_form', 'subpages_list') as $val) // sort for all sub of sub page lists
                                if (is_array($page) && array_key_exists($val, $page)) // if sub of sub page exist?
                                    StoreHelper::arraySortByColumn($definition[$key]['pages'][$pk][$val], 'position'); // sort it...
                    }

                // add navigation
                StoreNavigationHandler::addNavigation($definition, $this->addonName);
            }
        }
        return $this;
    }

    /**
     * @return DefinitionItem[]
     * @author Joachim Doerr
     */
    public function getDefinition()
    {
        if (empty($this->definition)) {
            // init manager
            $dm = new StoreDefinitionManager($this->addonName, $this->searchSchema, 'nav');
            // create definition list
            $dm->createDefinition(); // read cache or regenerate...
            $this->definition = $dm->getDefinition();
        }
        return $this->definition;
    }

    /**
     * @return bool
     * @author Joachim Doerr
     */
    public function existDefinitions()
    {
        if (is_array($this->getDefinition()) &&
            is_array($this->getDefinition()[0]->getDefinitions()) &&
            sizeof($this->getDefinition()[0]->getDefinitions()) > 0
        ) {
            return true;
        } else {
            return false;
        }
    }
}