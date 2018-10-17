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
// dispatch install event
rex_extension::registerPoint(new rex_extension_point('STORE_PLUGIN_INSTALL', $this->getName(), array('plugin'=>$this, 'addon'=>$this->getAddon(), 'data_path' => 'resources')));


////////////////////////////////
//// add redactor store product profiles
//if (rex_addon::exists('redactor2') && rex_addon::get('redactor2')->isAvailable()) {
//    if (!redactor2::profileExists('store_product_teaser')) {
//        redactor2::insertProfile('store_product_teaser', 'store product teaser config', 150, 250, 'relative', 'blockquote,bold,italic,underline,deleted,cleaner,fontsize[100%|120%|140%],grouplink[email|external|internal|media]');
//    }
//    if (!redactor2::profileExists('store_product_text')) {
//        redactor2::insertProfile('store_product_text', 'store product text config', 300, 800, 'relative', 'groupheading[2|3|4|5],unorderedlist,alignment,blockquote,bold,italic,underline,deleted,cleaner,fontsize[100%|120%|140%],grouplink[email|external|internal|media],horizontalrule,fullscreen');
//    }
//}
