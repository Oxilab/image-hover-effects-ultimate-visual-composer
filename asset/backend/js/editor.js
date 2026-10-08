/**
 * Flip box editor: delete confirmation dialog.
 *
 * Replaces the browser confirm() that vendor.js puts on each preview item's
 * Delete form. The form still posts exactly as before once confirmed. If this
 * file does not load, vendor.js's confirm() stays in place.
 */
jQuery(function ($) {
    'use strict';

    var $dialog = $('#oxi-flip-ed-delete-dialog');
    if (!$dialog.length) {
        return;
    }
    // Out of the editor layout so position: fixed is always the viewport.
    $dialog.appendTo(document.body);

    var $title = $('#oxi-flip-ed-delete-title');
    var $submit = $('#oxi-flip-ed-delete-submit');
    var submitLabel = $submit.text();
    var pending = null;
    var lastFocus = null;

    // The item's front title, read from its edit template when it has one.
    function itemTitle(id) {
        var tpl = document.getElementById('oxi-flip-edit-tpl-' + id);
        var field = tpl && tpl.content ? tpl.content.querySelector('[name="flip-box-front-title"]') : null;
        // DOMParser documents are inert: markup in a saved title never runs.
        var text = field ? $.trim(new DOMParser().parseFromString(field.getAttribute('value') || '', 'text/html').body.textContent || '') : '';
        return text.length > 60 ? text.slice(0, 57) + '...' : text;
    }

    function open(form, trigger) {
        pending = form;
        lastFocus = trigger || document.activeElement;
        var name = itemTitle(parseInt($(form).find('input[name="item-id"]').val(), 10));
        $title.text(name ? $title.attr('data-template').replace('%s', name) : $title.attr('data-default'));
        $submit.prop('disabled', false).text(submitLabel);
        $dialog.prop('hidden', false);
        $('body').addClass('oxi-flip-ed-dialog-open');
        $dialog.find('[data-oxi-flip-ed-close].oxi-flip-ed-dialog-btn').trigger('focus');
    }

    function close() {
        if ($submit.prop('disabled')) {
            return;
        }
        pending = null;
        $dialog.prop('hidden', true);
        $('body').removeClass('oxi-flip-ed-dialog-open');
        if (lastFocus && document.body.contains(lastFocus)) {
            lastFocus.focus();
        }
    }

    // Drop vendor.js's confirm() and ask with the dialog instead.
    $('.oxilab-style-absulate-delete-confirmation').off('submit');
    $(document).on('submit', '.oxilab-style-absulate-delete-confirmation', function (e) {
        e.preventDefault();
        open(this, e.originalEvent && e.originalEvent.submitter);
    });

    $dialog.on('click', '[data-oxi-flip-ed-close]', close);
    $(document).on('keydown', function (e) {
        if ($dialog.prop('hidden')) {
            return;
        }
        if (e.key === 'Escape') {
            close();
        } else if (e.key === 'Tab') {
            // Keep focus inside the dialog.
            var $buttons = $dialog.find('button:enabled');
            var first = $buttons.first()[0], last = $buttons.last()[0];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });

    $submit.on('click', function () {
        if (!pending) {
            return;
        }
        $submit.prop('disabled', true).text($submit.attr('data-busy'));
        // A native submit skips the button, so send its delete=delete pair.
        $('<input type="hidden" name="delete" value="delete">').appendTo(pending);
        HTMLFormElement.prototype.submit.call(pending);
    });

    // Back button from the next page: show the editor, not a busy dialog.
    $(window).on('pageshow', function (e) {
        if (e.originalEvent && e.originalEvent.persisted) {
            $submit.prop('disabled', false);
            $(pending).find('input[type="hidden"][name="delete"]').remove();
            close();
        }
    });
});

/**
 * Flip box editor: open the item dialog without reloading the page.
 *
 * Admin_Render::child_edit_templates() renders every item's fields into an
 * inert <template>. Edit copies those values into the dialog that is already
 * on the page (so the icon picker and image upload keep working) and opens
 * it. An item without a template falls back to the old reload flow.
 */
(function ($) {
    'use strict';

    var $modal = $('#oxi-addons-list-data-modal');
    var form = document.getElementById('oxi-flip-template-modal-form');
    if (!$modal.length || !form || !('content' in document.createElement('template'))) {
        return;
    }

    function liveFields(name) {
        return $(form.elements).filter(function () {
            return this.name === name;
        });
    }

    // The item dialogs only hold text, textarea, icon and image fields.
    function fill(tpl) {
        $(tpl.content.querySelectorAll('input[name], textarea[name]')).each(function () {
            var value = this.tagName.toLowerCase() === 'textarea' ? this.textContent : (this.getAttribute('value') || '');
            liveFields(this.name).first().val(value);
        });
    }

    // Edit on a preview item.
    $(document).on('submit', '.oxilab-style-absulate-edit form', function (e) {
        var id = parseInt($(this).find('input[name="item-id"]').val(), 10);
        var tpl = document.getElementById('oxi-flip-edit-tpl-' + id);
        if (!id || !tpl) {
            return;
        }
        e.preventDefault();
        fill(tpl);
        $('#item-id').val(id);
        $modal.modal('show');
    });

    // Add a flip box: vendor.js resets the form, this also resets the pickers.
    $('#oxi-addons-list-data-modal-open').on('click', function () {
        var tpl = document.getElementById('oxi-flip-edit-tpl-new');
        if (tpl) {
            fill(tpl);
            $('#item-id').val('');
        }
    });
})(jQuery);
