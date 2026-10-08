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
