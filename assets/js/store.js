/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(function () {
    checkboxpicker_init();
    store_mblock_init();
    $(document).on('pjax:end', function() {
        checkboxpicker_init();
        store_mblock_init();
    });
});

function checkboxpicker_init() {
    var checkbox = $('.bootstrap-toggle');
    if (checkbox.length) {
        checkbox.each(function () {
            $(this).bootstrapToggle();
            var val = 0;
            if ($(this).prop('checked')) {
                val = 1;
            }

            $(this).parent().parent().append('<input type="hidden" name="' + $(this).attr('name').replace('[1]', '') + '" value="' + val + '"/>');
            $(this).attr('name', '');

            $(this).change(function() {
                var val = 0;
                if ($(this).prop('checked')) {
                    val = 1;
                }
                $(this).parent().parent().find('input').val(val);
            })
        });
    }
}

function store_mblock_init() {
    var af_dropdown = $('.store_mblock .dropdown');
    if (af_dropdown.length) {
        af_dropdown.each(function() {
            var target = $(this).parent().next();
            $(this).find('a').each(function(){
                // ajax
                $(this).unbind().bind('click', function(event){
                    var _li = $(this).parent(),
                        _a = $(this);

                    if (!_li.hasClass('disabled')) {

                        $.ajax({
                            url: $(this).attr('data-link'),
                            success: function (result) {
                                target.append(result);
                                mblock_init();

                                _li.addClass('disabled');
                                _a.find('span').remove();
                                _a.append(' <span class="glyphicon glyphicon-ok">');
                            }
                        });

                    }
                    event.toElement.parentElement.click();
                    return false;
                })
            });
        });
        store_mblock_callback_run(af_dropdown);
    }
}

function store_mblock_callback_run(af_dropdown) {
    mblock_module.registerCallback('reindex_end', function (item) {
        af_dropdown.each(function () {
            var _drop = $(this),
                target = $(this).parent().next();
            target.find('.mblock_wrapper').each(function () {
                if ($(this).find('> div').length == 0) {
                    var type_key = $(this).data('type_key');
                    _drop.find('li').each(function () {
                        if ($(this).data('type_key') == type_key) {
                            $(this).removeClass('disabled');
                            $(this).find('a span').remove();
                        }
                    });
                }
            });
        });
    });
}