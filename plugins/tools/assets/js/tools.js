/**
 * @package alfred
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(function () {
    tools_init();
    $(document).on('pjax:end', function() {
        tools_init();
    });
});

function tools_init() {
    var multi_select = $('.bootstrap-multi-select');
    if (multi_select.length) {
        multi_select.each(function () {
            $(this).multiselect({
                numberDisplayed: 8
            });
        });
    }
}