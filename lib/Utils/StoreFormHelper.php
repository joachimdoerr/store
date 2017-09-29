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
 * Class StoreFormHelper
 * TODO description
 */
class StoreFormHelper
{
    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|null|rex_form_element|rex_form_widget_media_element|rex_form_widget_medialist_element|rex_form_prio_element
     * @author Joachim Doerr
     */
    public static function addFormElementByField(rex_form $form, array $item, $id = null)
    {
        if (array_key_exists('form_hidden', $item) && $item['form_hidden'] == 1) {
            return null;
        }

        if (
            (array_key_exists('type', $item) or array_key_exists('form_type', $item))
            && (array_key_exists('name', $item) or array_key_exists('lang_name', $item))
        ) {
            $name = (array_key_exists('lang_name', $item)) ? $item['lang_name'] : $item['name'];

            if (array_key_exists('form_callable', $item)) {
                return call_user_func_array($item['form_callable'], array($form, $item, $id));
            }

            $type = $item['type'];

            if (array_key_exists('form_type', $item)) {
                $type = $item['form_type'];
            }

            switch ($type) {
                case 'media':
                    return $form->addMediaField($name);

                case 'medialist':
                    return $form->addMedialistField($name);

                case 'varchar':
                case 'text':
                    return $form->addTextField($name);

                case 'textarea':
                    return $form->addTextAreaField($name);

                case 'number':
                case 'float':
                case 'int':
                    $element = $form->addTextField($name);
                    $element->setAttribute('type', 'number');
                    return $element;

                case 'prio':
                    $element = $form->addPrioField($name);
                    $element->setLabelField($item['form_prio_label']);
                    $element->setAttribute('class', 'selectpicker form-control');
                    return $element;

                default:
                    // add exception
                    break;
            }
        }

        return null;
    }

    /**
     * @param mixed|null|rex_form_element|rex_form_widget_media_element|rex_form_widget_medialist_element $element
     * @param array $item
     * @return mixed|null|rex_form_element|rex_form_widget_media_element|rex_form_widget_medialist_element
     * @author Joachim Doerr
     */
    public static function setElementProperties($element, array $item)
    {
        // add label
        if (is_object($element) && empty($element->getLabel())) {
            $element->setLabel(StoreHelper::getLabel($item));
        }
        // add class
        if (is_object($element) && array_key_exists('form_class', $item)) {
            $element->setAttribute('class', $element->getAttribute('class') . ' ' . $item['form_class']);
        }
        // add style
        if (is_object($element) && array_key_exists('form_style', $item)) {
            $element->setAttribute('style', $item['form_style']);
        }

        return $element;
    }

    /**
     * @param rex_form $form
     * @param $type
     * @param array $item
     * @return bool
     * @author Joachim Doerr
     */
    public static function addColumns(rex_form $form, $type, array $item = array())
    {
        switch ($type) {
            case 'wrapper':
                $form->addRawField('<div class="row">');
                return true;

            case 'column':
                $class = $item['field_column'];
                if (array_key_exists('field_class', $item)) $class .= $item['field_class'];
                $form->addRawField("<div class=\"$class\">");
                return true;

            case 'close_column':
            case 'close_wrapper':
                $form->addRawField('</div>');
                return false;
        }
    }

    /**
     * @param rex_form $form
     * @param string $type
     * @param null $key
     * @param null $curClang
     * @author Joachim Doerr
     */
    public static function addLangTabs(rex_form $form, $type, $key = null, $curClang = null)
    {
        if (rex_clang::count() > 1) {
            switch ($type) {
                case 'wrapper':
                    $form->addRawField('<div class="store-clangtabs"><ul class="nav nav-tabs" role="tablist">');
                    foreach (rex_clang::getAll() as $clang) {
                        $active = '';
                        if ($key == $clang->getId()) {
                            $active = ' active';
                        }
                        $form->addRawField("<li role=\"presentation\" class=\"$active\"><a href=\"#lang{$clang->getId()}\" aria-controls=\"home\" role=\"tab\" data-toggle=\"tab\">{$clang->getName()}</a></li>");
                    }
                    $form->addRawField('</ul><div class="tab-content store-tabform">');
                    break;

                case 'close_wrapper':
                    $form->addRawField('</div></div>');
                    break;

                case 'inner_wrapper':
                    $active = '';
                    if ($key == $curClang) {
                        $active = ' active';
                    }
                    $form->addRawField("\n\n\n<div id=\"lang$key\" role=\"tabpanel\" class=\"tab-pane $active\">\n");
                    break;

                case 'close_inner_wrapper':
                    $form->addRawField('</div>');
                    break;
            }
        }
    }

    /**
     * @param rex_form $form
     * @param $type
     * @author Joachim Doerr
     */
    public static function closeLangTabs(rex_form $form, $type)
    {
        // close lang tabs
        StoreFormHelper::addLangTabs($form, $type);
    }

    /**
     * @param rex_form $form
     * @param string $type
     * @param null $key
     * @param null $curKey
     * @param null $nav
     * @author Joachim Doerr
     */
    public static function addTabs(rex_form $form, $type, $key = null, $curKey = null, $nav = null)
    {
        if (rex_clang::count() > 1) {
            switch ($type) {
                case 'wrapper':
                    $form->addRawField('<div class="store-tabs"><ul class="nav nav-tabs" role="tablist">');
                    if (is_array($nav)) {
                        foreach ($nav as $nKey => $value) {
                            $active = '';
                            if ($key == $nKey) {
                                $active = ' active';
                            }
                            $form->addRawField("<li role=\"presentation\" class=\"$active\"><a href=\"#pnl{$nKey}\" aria-controls=\"home\" role=\"tab\" data-toggle=\"tab\">{$value}</a></li>");
                        }
                    }
                    $form->addRawField('</ul><div class="tab-content store-tabform">');
                    break;
                case 'navigation':
                    break;
                case 'close_wrapper':
                    $form->addRawField('</div></div>');
                    break;

                case 'inner_wrapper':
                    $active = '';
                    if ($key == $curKey) {
                        $active = ' active';
                    }
                    $form->addRawField("\n\n\n<div id=\"pnl$key\" role=\"tabpanel\" class=\"tab-pane $active\">\n");
                    break;

                case 'close_inner_wrapper':
                    $form->addRawField('</div>');
                    break;
            }
        }
    }

    /**
     * @param rex_form $form
     * @param $type
     * @author Joachim Doerr
     */
    public static function closeTabs(rex_form $form, $type)
    {
        // close lang tabs
        StoreFormHelper::addLangTabs($form, $type);
    }

    /**
     * @param rex_form $form
     * @param $type
     * @param string $name
     * @author Joachim Doerr
     */
    public static function addCollapsePanel(rex_form $form, $type, $name = '')
    {
        $in = '';
        switch ($type) {
            case 'wrapper':
                $keya = uniqid('a');
                $form->addRawField("<div class=\"panel-group store-panel\" id=\"$keya\">");
                break;

            case 'close_wrapper':
                $form->addRawField('</div>');
                break;

            case 'inner_wrapper_open':
                $in = 'in';

            case 'inner_wrapper':
                $keyp = uniqid('p');
                $keyc = uniqid('c');
                $form->addRawField("<div class=\"panel panel-default\" id=\"$keyp\"><div class=\"panel-heading\"><h4 class=\"panel-title\"><a data-toggle=\"collapse\" data-target=\"#$keyc\" href=\"#$keyc\" class=\"collapsed\">$name</a></h4></div> <div id=\"$keyc\" class=\"panel-collapse collapse$in\"><div class=\"panel-body\">");
                break;

            case 'close_inner_wrapper':
                $form->addRawField('</div></div></div>');
                break;
        }
    }

    /**
     * @param rex_form $form
     * @param $type
     * @author Joachim Doerr
     */
    public static function closeCollapsePanel(rex_form $form, $type)
    {
        StoreFormHelper::addCollapsePanel($form, $type);
    }

    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @return mixed|rex_form_select_element
     * @author Joachim Doerr
     */
    public static function addStatusElement(rex_form $form, array $item, $id = null)
    {
        if (rex_plugin::get('store', 'tools')->isAvailable()) {
            // add toggle button online offline
            $element = $form->addCheckboxField($item['name']);

            if (array_key_exists('label', $item)) {
                $element->setLabel(StoreHelper::getLabel($item));
            }

            $element->addOption('', 1);
            $element->setAttribute('data-toggle', 'toggle');
            $element->setAttribute('data-on', '<i class=\'rex-icon rex-icon-online\'> ' . rex_i18n::msg('store_online'));
            $element->setAttribute('data-off', '<i class=\'rex-icon rex-icon-offline\'> ' . rex_i18n::msg('store_offline'));
            $element->setAttribute('data-width', 160);
            $element->setAttribute('data-onstyle', 'info');
            $element->setAttribute('class', 'bootstrap-toggle');
        } else {
            // fallback
            $element = $form->addSelectField('status');
            $select = $element->getSelect();
            $select->addOptions(array(1=>'online', 0=>'offline'));
            $element->setLabel(StoreHelper::getLabel($item));

            if (array_key_exists('style', $item)) {
                $element->setAttribute('style', $item['style']);
            }
        }

        return $element;
    }

    /**
     * @param rex_form $form
     * @param array $item
     * @return mixed|rex_form_select_element
     * @author Joachim Doerr
     */
    public static function addSelectField(rex_form $form, array $item)
    {
        $sql = rex_sql::factory();
        $sql->setQuery($item['query']);

        $element = $form->addSelectField($item['name']);
        $select = $element->getSelect();

        if (isset($item['multiple']) && $item['multiple']) {
            $select->setMultiple(true);
        }

        while($sql->hasNext()) {
            $select->addOption($sql->getValue('name'), $sql->getValue('id'), $sql->getValue('id'));
            $sql->next();
        }

        if ($form->isEditMode()) {
            $select->setSelected(explode(',', str_replace(array('[',']', '"'), '', $element->getValue())));
        }

        $element->setLabel(StoreHelper::getLabel($item));

        return $element;

    }

    /**
     * @param $message
     * @param rex_form|StoreFormView $form
     * @return string
     * @author Joachim Doerr
     */
    public static function wrapForm($message, $form)
    {
        return '<div class="store-form">' . $message . $form->show() . '</div>';
    }
}