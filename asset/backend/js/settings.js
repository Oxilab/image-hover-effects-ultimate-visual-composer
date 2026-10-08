jQuery.noConflict();
(function ($) {
    $(document).ready(function () {
        var $root = $('.oxi-flip-settings');
        if (!$root.length) {
            return;
        }
        var timers = {};

        function setStatus(name, state) {
            var $status = $root.find('[data-status-for="' + name + '"]');
            clearTimeout(timers[name]);
            $status.attr('data-state', state).text(state ? $root.attr('data-' + state) : '');
            if (state === 'saved') {
                timers[name] = setTimeout(function () {
                    $status.attr('data-state', '').text('');
                }, 2500);
            }
        }

        // Each option has its own method in Classes/Admin_Ajax.php, which
        // echoes an oxi-confirmation-success span when it saved.
        function save(name, data, onError) {
            setStatus(name, 'saving');
            $.ajax({
                url: oxi_flip_box_settings.ajaxurl,
                type: 'post',
                data: {
                    action: 'oxi_flip_box_data',
                    _wpnonce: oxi_flip_box_settings.nonce,
                    functionname: name,
                    styleid: '',
                    childid: '',
                    rawdata: JSON.stringify(data)
                }
            }).done(function (result) {
                if (String(result).indexOf('oxi-confirmation-success') !== -1) {
                    setStatus(name, 'saved');
                } else {
                    setStatus(name, 'error');
                    if (onError) {
                        onError();
                    }
                }
            }).fail(function () {
                setStatus(name, 'error');
                if (onError) {
                    onError();
                }
            });
        }

        $root.on('change', '.oxi-flip-set-switch input', function () {
            var input = this;
            var value = input.checked ? input.getAttribute('data-on') : input.getAttribute('data-off');
            save(input.name, {value: value}, function () {
                input.checked = !input.checked;
            });
        });

        $root.on('change', '#oxi_addons_user_permission', function () {
            save(this.name, {value: $(this).val()});
        });

        // Danger zone: "Delete all data" asks for DELETE to be typed first.
        var $dialog = $('#oxi-flip-delete-dialog');
        var $confirm = $('#oxi-flip-delete-confirm');
        var $submit = $('#oxi-flip-delete-submit');
        var $dialogStatus = $dialog.find('.oxi-flip-set-dialog-status');
        var lastFocus = null;
        var deleting = false;

        function openDialog(trigger) {
            lastFocus = trigger;
            $confirm.val('');
            $submit.prop('disabled', true);
            $dialogStatus.attr('data-state', '').text('');
            $dialog.prop('hidden', false);
            $('body').addClass('oxi-flip-set-dialog-open');
            setTimeout(function () {
                $confirm.trigger('focus');
            }, 50);
        }

        function closeDialog() {
            if (deleting) {
                return;
            }
            $dialog.prop('hidden', true);
            $('body').removeClass('oxi-flip-set-dialog-open');
            if (lastFocus) {
                lastFocus.focus();
            }
        }

        $root.on('click', '[data-oxi-flip-open]', function () {
            openDialog(this);
        });

        $dialog.on('click', '[data-oxi-flip-close]', closeDialog);

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && !$dialog.prop('hidden')) {
                closeDialog();
            }
        });

        $confirm.on('input', function () {
            $submit.prop('disabled', $confirm.val().trim() !== 'DELETE');
        });

        $confirm.on('keydown', function (e) {
            if (e.key === 'Enter' && !$submit.prop('disabled')) {
                $submit.trigger('click');
            }
        });

        $submit.on('click', function () {
            if ($confirm.val().trim() !== 'DELETE' || deleting) {
                return;
            }
            deleting = true;
            $submit.prop('disabled', true);
            $confirm.prop('disabled', true);
            $dialogStatus.attr('data-state', 'saving').text($dialogStatus.attr('data-deleting'));

            function failed() {
                deleting = false;
                $confirm.prop('disabled', false);
                $submit.prop('disabled', false);
                $dialogStatus.attr('data-state', 'error').text($dialogStatus.attr('data-error'));
            }

            $.ajax({
                url: oxi_flip_box_settings.ajaxurl,
                type: 'post',
                data: {
                    action: 'oxi_flip_box_data',
                    _wpnonce: oxi_flip_box_settings.nonce,
                    functionname: 'oxi_flipbox_delete_all_data',
                    styleid: '',
                    childid: '',
                    rawdata: JSON.stringify({confirm: 'DELETE'})
                }
            }).done(function (result) {
                if (String(result).indexOf('oxi-confirmation-success') === -1) {
                    failed();
                    return;
                }
                $dialogStatus.attr('data-state', 'saved').text($dialogStatus.attr('data-done'));
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            }).fail(failed);
        });
    });
})(jQuery);
