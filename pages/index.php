<?php

// TODO cool headlines
$title = rex_view::title(rex_i18n::msg($this->getName() . '_title'));
// to change title
$shopTitle = rex_extension::registerPoint(new rex_extension_point(strtoupper($this->getName()) . '_TITLE', $title));
// to add anything to title
$shopTitle .= rex_extension::registerPoint(new rex_extension_point(strtoupper($this->getName()) . '_TITLE_SHOW', ''));

print $shopTitle;

// TODO add brotkrümel

// exchange
include rex_be_controller::getCurrentPageObject()->getSubPath();
