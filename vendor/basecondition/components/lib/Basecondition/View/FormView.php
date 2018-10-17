<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\View;


use Basecondition\Database\DatabaseDefinitionMergeHandler;
use Basecondition\Definition\DefinitionProvider;
use Basecondition\Utils\FormHelper;
use Basecondition\Utils\MBlockHelper;
use Basecondition\Utils\ViewHelper;
use MBlock;
use mblock_rex_form;
use rex;
use rex_addon;
use rex_clang;
use rex_form;
use rex_form_raw_element;
use rex_request;
use rex_url;

class FormView
{
    const SEARCH_SCHEMA = "*/definitions/default/%s.yml";

    /**
     * @var string
     */
    public $addonKey;

    /**
     * @var array
     */
    public $items;

    /**
     * @var string
     */
    public $tableKey;

    /**
     * @var string
     */
    public $tableBaseName;

    /**
     * @var string
     */
    public $searchFile;

    /**
     * @var rex_form|mblock_rex_form
     */
    public $form;

    /**
     * @var string
     */
    public $namePrefix;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $legend;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var array
     */
    public $urlParameters;

    /**
     * FormView constructor.
     * @param $addonKey
     * @param $searchFile
     * @param string $legend
     * @param int $id
     * @param bool $debug
     * @param array $urlParameters
     * @param bool $initForm
     * @param array $payload
     * @author Joachim Doerr
     */
    public function __construct($addonKey, $searchFile, $legend = '', $id = 0, $debug = false, array $urlParameters = array(), $initForm = true, $payload = array())
    {
        $this->addonKey = $addonKey;
        $this->searchFile = $searchFile;
        $addon = rex_addon::get($addonKey);

        $this->id = $id;
        $this->legend = $legend;
        $this->debug = $debug;
        $this->urlParameters = $urlParameters;
        $this->tableBaseName = $addonKey . '_' . $searchFile;

        $this->tableKey = rex::getTablePrefix() . $addonKey . '_' . $searchFile;

        if (array_key_exists('tableKey', $payload)) {
            $this->tableKey = $payload['tableKey'];
        }

        $definition = DefinitionProvider::load(sprintf(self::SEARCH_SCHEMA, $this->searchFile), $addon->getDataPath('resources'), $addon->getCachePath(), new DatabaseDefinitionMergeHandler, true);
        $this->items = $definition['data'];

        if ($initForm === true)
            $this->initForm();
    }

    /**
     * @author Joachim Doerr
     */
    public function initForm()
    {
        /** @var mblock_rex_form|rex_form $form_class */
        $form_class = (class_exists(mblock_rex_form::class)) ? 'mblock_rex_form' : 'rex_form';

        // init form
        $this->form = $form_class::factory($this->tableKey, $this->legend, 'id = ' . $this->id, 'post', $this->debug);

        if ($this->form instanceof mblock_rex_form) {
            // event?
            // TODO add rex extension point validation
            // $this->form->dispatcher = StoreEvent::dispatcher();
            // $this->form->event = new StoreRexFormValidationEvent($this->form);
        }
        if ($this->id > 0) {
            $this->form->addParam('id', $this->id);
        }

        if (sizeof($this->urlParameters) > 0) {
            foreach ($this->urlParameters as $parameter => $value) {
                if (is_array($value)) {
                    $this->form->addParam($parameter['key'], rex_request::request($parameter['key'], $parameter['type'], $parameter['default']));
                } else {
                    $this->form->addParam($parameter, $value);
                }
            }
        }

        $this->form->setEditMode(($this->id > 0));

        if (!array_key_exists('start', $this->urlParameters)) {
            $this->form->addParam('start', rex_request::request('start', 'int'));
        }
    }

    /**
     * @author Joachim Doerr
     */
    public function addFieldElements()
    {
        if (is_array($this->items)) {

            $tab = false;
            $tabWrapper = false;
            $tabNav = array();

            // tab wrapper and tab nav?
            foreach ($this->items as $key => $item) {
                // set tab
                if (strpos($key, 'tabs') !== false) {
                    if (array_key_exists('tab_row', $item) && $item['tab_row'] == 'close') {
                        continue; // not add close item to tab navigation
                    }

                    $uId = uniqid();
                    $this->items[$key]['tab_unique_id'] = $uId;
                    $tabNav[$uId] = ViewHelper::getLabel($item, 'label', $this->tableBaseName);
                }
            }

            // add navigation an tab wrapper open
            if (sizeof($tabNav) > 0) {
                FormHelper::addTabs($this->form, 'wrapper', key($tabNav), null, $tabNav);
                $tabWrapper = true;
            }

            foreach ($this->items as $key => $item) {
                // add form elements
                switch (TRUE) {
                    case (strpos($key, 'tabs') !== false):
                        // latest tab is open
                        if ($tab === true) {
                            FormHelper::closeTabs($this->form, 'close_inner_wrapper'); // close tab
                        }
                        if (array_key_exists('tab_row', $item) && $item['tab_row'] == 'close') {
                            FormHelper::closeTabs($this->form, 'close_wrapper'); // close tab wrapper
                            $tab = false;
                            $tabWrapper = false;
                            break;
                        }
                        FormHelper::addTabs($this->form, 'inner_wrapper', $item['tab_unique_id'], key($tabNav), rex_clang::getCurrentId()); // open tab new tab
                        $tab = true; // set tab info tab is open
                        break;
                    case (strpos($key, 'lang') !== false):
                        $this->addLangFieldset($item); // it is a langfieldset
                        break;
                    case (strpos($key, 'fields') !== false):
                        $this->addDefaultFieldset($item); // it is a fieldset
                        break;
                    case (strpos($key, 'panel') !== false):
                        $this->addPanelFieldset($item); // it is a fieldset
                        break;
                }
            }

            // foreach is closed
            // latest tab is open
            if ($tab === true) {
                FormHelper::closeTabs($this->form, 'close_inner_wrapper'); // close tab
            }
            if ($tabWrapper === true) {
                FormHelper::closeTabs($this->form, 'close_wrapper'); // close tab wrapper
            }
        }
    }

    /**
     * @param array $fieldset
     * @author Joachim Doerr
     */
    public function addLangFieldset(array $fieldset)
    {
        // start lang tabs
        FormHelper::addLangTabs($this->form, 'wrapper', 1);

        // add inner wrapper -> tabs content
        foreach (rex_clang::getAll() as $key => $clang) {
            // open inner wrapper
            FormHelper::addLangTabs($this->form, 'inner_wrapper', $clang->getId(), rex_clang::getCurrentId());

            // add fieldsets to tab content field
            foreach ($fieldset as $value) {
                // is the value a array
                if (is_array($value)) { // yes go on
                    if (array_key_exists('panel_name', $value) && array_key_exists('fields', $value)) { // foreach the fieldsets form langfieldset
                        $this->addPanelFieldset($value, $clang->getId()); // panel field
                    } else {
                        $this->addDefaultFieldset($value, $clang->getId()); // default lang fields
                    }
                }
            }
            // close inner wrapper
            FormHelper::closeLangTabs($this->form, 'close_inner_wrapper');
        }
        // close lang tabs
        FormHelper::closelangTabs($this->form, 'close_wrapper');
    }

    /**
     * @param array $panel
     * @param null $clang
     * @author Joachim Doerr
     * @throws \rex_exception
     */
    public function addPanelFieldset(array $panel, $clang = null)
    {
        if (array_key_exists('panel_name', $panel) && array_key_exists('fields', $panel)) {
            // ADD PANEL
            FormHelper::addCollapsePanel($this->form, 'wrapper');
            FormHelper::addCollapsePanel($this->form, 'inner_wrapper', ViewHelper::getLabel($panel, 'label', $this->tableBaseName));

            // panel lang fields
            $this->addDefaultFieldset($panel['fields'], $clang);

            // close
            FormHelper::closeCollapsePanel($this->form, 'close_inner_wrapper');
            FormHelper::closeCollapsePanel($this->form, 'close_wrapper');
        }
    }

    /**
     * @param array $fieldset
     * @param null $clang
     * @author Joachim Doerr
     * @throws \rex_exception
     */
    public function addDefaultFieldset(array $fieldset, $clang = null)
    {
        // field row
        $fieldRow = false;
        $fieldColumn = false;

        foreach ($fieldset as $item) {
            if (is_array($item) && array_key_exists('field_row', $item) && $item['field_row'] == 'open') {
                FormHelper::addColumns($this->form, 'wrapper', $item);
            }
            if (is_array($item) && array_key_exists('field_row', $item) && $item['field_row'] == 'close') {
                $fieldRow = true;
            }
            if (is_array($item) && array_key_exists('field_column', $item)) {
                $fieldColumn = FormHelper::addColumns($this->form, 'column', $item);
            }
        }

        foreach ($fieldset as $item) {
            // break is not a array
            if (!is_array($item)) {
                continue;
            }
            if ($this->namePrefix) {
                $item['name'] = $this->namePrefix . $item['name'];
            }
            if (!isset($item['mblock_callable']) && isset($item['mblock_definition_table'])) {
                $item['mblock_callable'] = '\Basecondition\Utils\MBlockHelper::getMBlockDefinitions';
            }

            if (isset($item['mblock_callable'])) {
                $result = call_user_func_array($item['mblock_callable'], array($this, $item));

                if (isset($result['continue']) && $result['continue'] === true) {
                    continue;
                }
                if (isset($result['item'])) {
                    $item = $result['item'];
                }
            }

            // fix definition for mblock area
            if (isset($item['form_type']) && $item['form_type'] == 'mblock' or isset($item['mblock_definition'])) {

                if (is_array($item['mblock_definition'])) {
                    // more definitions by table or files
                    $this->addMBlockSetFieldset($item);
                    continue;
                }
                // one definition file...
                $this->addMBlockFieldset($item);
                continue;
            }

            // is field a lang field
            if (!is_null($clang) && is_array($item) && array_key_exists('name', $item)) {
                $item['lang_name'] = $item['name'] . '_' . $clang; // add lang name
            }

            // set element for add more...
            $element = FormHelper::addFormElementByField($this->form, $item, $this->id, $this->tableBaseName);

//            // TODO validation...
//            if ($element instanceof rex_form_element) {
//                $validator = $element->getValidator();
//                $validator->add('notempty', 'darf nicht');
//            }


            // set element properties by item array
            FormHelper::setElementProperties($element, $item, $this->tableBaseName);
        }

        if ($fieldColumn) {
            FormHelper::addColumns($this->form, 'close_column');
        }
        if ($fieldRow) {
            FormHelper::addColumns($this->form, 'close_wrapper');
        }
    }

    /**
     * @param array $item
     * @author Joachim Doerr
     */
    private function addMBlockSetFieldset(array $item)
    {
        $active = array();
        if ($this->id > 0) { // is edit?
            $row = json_decode($this->form->getSql()->getRow()[$this->form->getTableName() .'.'. $item['name']], true);
            if (is_array($row) && sizeof($row) > 0) {
                foreach ($row as $key => $value) {
                    $active[] = $key;
                }
            }
        }

        $uid = uniqid();
        // create navigation
        MBlockHelper::addMBlockSetNavigation($this->form, $item, $this->urlParameters, $this->id, $active, $uid, 'base', $this->tableBaseName);
        // create mblock fieldsets
        MBlockHelper::addMBlockSetFieldset($this, $item, $active, $uid);
    }

    /**
     * @param array $item
     * @param null $type
     * @param array $settings
     * @return rex_form_raw_element
     * @author Joachim Doerr
     */
    public function addMBlockFieldset(array $item, $type = null, $settings = array())
    {
        $settings = array_merge($settings, MBlockHelper::getSettings($item, $type));
        return $this->form->addRawField(self::createMBlockFieldset($item, $type, $settings));
    }

    /**
     * @param array $item
     * @param null $type
     * @param array $settings
     * @return mixed
     * @author Joachim Doerr
     */
    public function createMBlockFieldset(array $item, $type = null, $settings = array())
    {
        $table = $this->tableKey . '::' . $item['name'];

        if (!is_null($type)) {
            $table = $table . '::' . $type;
            $type = '[' . $type . ']';
        }

        $mblockView = new FormView($this->addonKey, $item['mblock_definition'], '', $this->id, $this->debug, $this->urlParameters, false, array('tableKey'=>$this->tableKey));
        $mblockView->namePrefix = $item['name'] . ']' . $type . '[0][';
        $mblockView->initForm();
        $mblockView->addFieldElements(); // add field elements by defaults

        return MBlock::show($table, $mblockView->showElements(), array_merge(array('min'=>0), $settings));
    }

    /**
     * @return rex_form
     * @author Joachim Doerr
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function show()
    {
        return $this->form->get();
    }

    /**
     * @param string $legend
     * @return string
     * @author Joachim Doerr
     */
    public function showElements($legend = '')
    {
        return $this->form->getElements($legend);
    }
}