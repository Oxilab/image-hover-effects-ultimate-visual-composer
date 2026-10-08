jQuery.noConflict();
(function ($) {
    $(function () {
        var $root = $('.oxi-flip-import');
        if (!$root.length) {
            return;
        }

        // Add a template to the Create New list. shortcode_active answers with
        // the Create New link for that template, which we then open.
        $root.on('submit', '.oxi-flip-tpl-add', function (e) {
            e.preventDefault();
            var $form = $(this);
            var $button = $form.find('button');
            var $text = $form.find('.oxi-flip-tpl-add-text');
            var original = $text.text();
            if ($form.data('busy')) {
                return;
            }
            $form.data('busy', true);
            $button.prop('disabled', true);
            $text.text($root.attr('data-adding'));

            function failed() {
                $form.data('busy', false);
                $button.prop('disabled', false);
                $text.text($root.attr('data-add-error'));
                setTimeout(function () {
                    $text.text(original);
                }, 2500);
            }

            $.ajax({
                url: oxi_flip_box_editor.ajaxurl,
                type: 'post',
                data: {
                    action: 'oxi_flip_box_data',
                    _wpnonce: oxi_flip_box_editor.nonce,
                    functionname: 'shortcode_active',
                    styleid: '',
                    childid: '',
                    rawdata: $form.serialize()
                }
            }).done(function (result) {
                var url = $.trim(String(result));
                if (url.indexOf('http') === 0) {
                    document.location.href = url;
                    return;
                }
                failed();
            }).fail(failed);
        });
    });
})(jQuery);
