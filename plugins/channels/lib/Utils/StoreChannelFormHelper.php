<?php

use Basecondition\Utils\ViewHelper;

/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreChannelFormHelper
{
    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @author Joachim Doerr
     */
    public static function addCategoryElement(rex_form $form, array $item, $id = null)
    {
        if (rex_request::get('func', 'string', '') == 'edit') { // is edit
            $sql = rex_sql::factory();
            $sql->setQuery("SELECT * FROM ".StoreChannelsActions::CHANNELS_TABLE." AS sh WHERE id = $id");
            $channel = $sql->getRow();

            if (!empty($channel['sh.category'])) { // category is not given
                // add delete button
                $formElements = array(
                    array(
                        'label' => ViewHelper::getLabel($item),
                        'field' => '
                            <a class="btn btn-delete" data-confirm="' . rex_i18n::msg('store_confirm_delete') . '" href="' . $form->getUrl(array('sub_func' => 'delete_cat')) . '">
                                <i class="rex-icon fa-trash-o"></i> ' . rex_i18n::msg('store_category_removecat') . '
                            </a>
                            <a class="btn btn-info" href="' . $form->getUrl(array('func' => 'setcat')) . '">
                                <i class="rex-icon fa-folder"></i> ' . rex_i18n::msg('store_category_setcat_goto') . '
                            </a>',
                        # 'note' => ''
                    )
                );
            } else {
                $formElements = array(
                    array(
                        'label' => ViewHelper::getLabel($item),
                        'field' => '
                            <a class="btn btn-apply" href="' . $form->getUrl(array('sub_func' => 'add_cat')) . '">
                                <i class="rex-icon fa-folder-o"></i> ' . rex_i18n::msg('store_category_setcat') . '
                            </a>',
                        # 'note' => ''
                    )
                );

            }
            $fragment = new rex_fragment();
            $fragment->setVar('elements', $formElements, false);
            $form->addRawField($fragment->parse('core/form/form.php'));
        }
    }

    /**
     * @param rex_extension_point $params
     * @return mixed
     * @author Joachim Doerr
     */
    public static function removeDeleteButton(rex_extension_point $params)
    {
        $id = rex_request::get('id', 'int', 0);
        $subject = $params->getSubject();
        if ($id > 0) { // check is category set?
            $sql = rex_sql::factory();
            $sql->setQuery("SELECT * FROM ".StoreChannelsActions::CHANNELS_TABLE." AS sh WHERE id = $id");
            $channel = $sql->getRow();
            if (!empty($channel['sh.category'])) { // yes it is
                $subject['delete'] = ''; // remove delete button
            }
        }
        return $subject;
    }
}