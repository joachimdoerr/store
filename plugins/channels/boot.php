<?php
/**
 * @author Joachim Doerr
 * @support www.dev51.com
 * @package redaxo5 / shop.dev51
 */


if (rex::isBackend() && rex::getUser()) {
    // add assets
    rex_view::addCssFile($this->getAssetsUrl('css/' . $this->getAddon()->getName() . '_channels.css'));
}