<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


//////////////////////////////
// boot tools
if (rex::isBackend() && rex::getUser()) {
    // add toggle plugin
    // http://www.bootstraptoggle.com
    rex_view::addJsFile($this->getAssetsUrl('bootstrap-toggle/js/bootstrap-toggle.min.js'));
    rex_view::addCssFile($this->getAssetsUrl('bootstrap-toggle/css/bootstrap-toggle.css'));

    // add multiselect
    rex_view::addJsFile($this->getAssetsUrl('bootstrap-multiselect/dist/js/bootstrap-multiselect.js'));
    rex_view::addCssFile($this->getAssetsUrl('bootstrap-multiselect/dist/css/bootstrap-multiselect.css'));

    // general
    rex_view::addJsFile($this->getAssetsUrl('js/tools.js'));
    rex_view::addCssFile($this->getAssetsUrl('css/tools.css'));
}
