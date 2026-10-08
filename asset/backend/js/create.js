jQuery.noConflict();
(function ($) {
    $(function () {
        var $root = $('.oxi-flip-create');
        if (!$root.length) {
            return;
        }

        function request(functionname, rawdata) {
            return $.ajax({
                url: oxi_flip_box_editor.ajaxurl,
                type: 'post',
                data: {
                    action: 'oxi_flip_box_data',
                    _wpnonce: oxi_flip_box_editor.nonce,
                    functionname: functionname,
                    styleid: '',
                    childid: '',
                    rawdata: rawdata
                }
            });
        }

        // Create dialog.
        var $dialog = $('#oxi-flip-create-dialog');
        var $name = $('#oxi-flip-create-name');
        var $status = $dialog.find('.oxi-flip-set-dialog-status');
        var lastFocus = null;
        var busy = false;

        function setStatus(state) {
            $status.attr('data-state', state).text(state ? $status.attr('data-' + state) : '');
        }

        function openDialog(trigger) {
            lastFocus = trigger;
            $('#oxi-flip-create-source').val($(trigger).attr('data-source'));
            $dialog.find('.oxi-flip-create-design').text($(trigger).attr('data-label'));
            $name.val('');
            setStatus('');
            $dialog.prop('hidden', false);
            $('body').addClass('oxi-flip-set-dialog-open');
            setTimeout(function () {
                $name.trigger('focus');
            }, 50);
        }

        function closeDialog() {
            if (busy) {
                return;
            }
            $dialog.prop('hidden', true);
            $('body').removeClass('oxi-flip-set-dialog-open');
            if (lastFocus) {
                lastFocus.focus();
            }
        }

        $root.on('click', '.oxi-flip-tpl-use', function () {
            openDialog(this);
        });
        $dialog.on('click', '[data-oxi-flip-close]', closeDialog);
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && !$dialog.prop('hidden')) {
                closeDialog();
            }
        });

        // Same payload as before: the chosen design's JSON from its textarea.
        $('#oxi-flip-create-form').on('submit', function (e) {
            e.preventDefault();
            var name = $.trim($name.val());
            var source = $('#oxi-flip-create-source').val();
            if (!name || !source || busy) {
                return;
            }
            var style;
            try {
                style = JSON.parse($('#' + source).val());
            } catch (err) {
                setStatus('error');
                return;
            }
            busy = true;
            setStatus('saving');
            $dialog.find('button').prop('disabled', true);

            function failed() {
                busy = false;
                $dialog.find('button').prop('disabled', false);
                setStatus('error');
            }

            request('create_flip', JSON.stringify({name: name, style: style})).done(function (result) {
                var url = $.trim(String(result));
                if (url.indexOf('http') === 0) {
                    document.location.href = url;
                    return;
                }
                failed();
            }).fail(failed);
        });

        // Remove a template from this list (it can be added back from Import Templates).
        $root.on('submit', '.oxi-flip-tpl-remove', function (e) {
            e.preventDefault();
            var $form = $(this);
            var $card = $form.closest('.oxi-flip-tpl');
            var $text = $form.find('.oxi-flip-tpl-remove-text');
            var original = $text.text();
            if ($form.data('busy')) {
                return;
            }
            $form.data('busy', true);
            $form.find('button').prop('disabled', true);
            $text.text($root.attr('data-removing'));
            request('shortcode_deactive', $form.serialize()).done(function (result) {
                if ($.trim(String(result)) === 'done') {
                    $card.addClass('is-removing');
                    setTimeout(function () {
                        $card.remove();
                    }, 300);
                    return;
                }
                $form.data('busy', false);
                $form.find('button').prop('disabled', false);
                $text.text($root.attr('data-remove-error'));
                setTimeout(function () {
                    $text.text(original);
                }, 2500);
            }).fail(function () {
                $form.data('busy', false);
                $form.find('button').prop('disabled', false);
                $text.text($root.attr('data-remove-error'));
                setTimeout(function () {
                    $text.text(original);
                }, 2500);
            });
        });
    });
})(jQuery);
