<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// default title
$title = rex_view::title(rex_i18n::msg($this->getName() . '_title'));
// to change title
$storeTitle = rex_extension::registerPoint(new rex_extension_point(strtoupper($this->getName()) . '_TITLE', $title));
// to add anything to title
$storeTitle .= rex_extension::registerPoint(new rex_extension_point(strtoupper($this->getName()) . '_TITLE_SHOW', ''));
//
print $storeTitle;

// TODO add brotkrümel ?

include rex_be_controller::getCurrentPageObject()->getSubPath();