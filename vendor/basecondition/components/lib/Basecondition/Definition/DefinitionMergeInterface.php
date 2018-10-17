<?php
/**
 * @package components
 * @author Joachim Doerr
 * @copyright (C) hello@basecondition.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecondition\Definition;


interface DefinitionMergeInterface
{
    /**
     * @param array $array
     * @return array
     * @author Joachim Doerr
     */
    public static function merge(array $array);
}