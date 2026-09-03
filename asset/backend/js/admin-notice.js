jQuery.noConflict();
(function ($) {
    "use strict";

    var REVIEW_NOTICE = '.oxilab-flipbox-review-notice',
        UPGRADE_NOTICE = '.oxilab-flipbox-upgrade-notice',
        PLUGIN_MENU = '.oxilab-new-admin-menu';

    /**
     * WordPress core moves admin notices to the top of the page body, which
     * lands them above the plugin's own menu bar. On the plugin's screens,
     * drop them just below that bar instead.
     */
    function placeBelowPluginMenu() {
        var $menu = $(PLUGIN_MENU).first();

        if (!$menu.length) {
            return;
        }

        $([REVIEW_NOTICE, UPGRADE_NOTICE].join(',')).each(function () {
            var $notice = $(this);

            if ($notice.prev().is($menu)) {
                return; // Already in place.
            }
            $notice.addClass('is-below-plugin-menu').insertAfter($menu);
        });
    }

    $(function () {
        placeBelowPluginMenu();
        // Core's common.js relocates notices on ready too; run once more after
        // it has had its turn. Idempotent, so a double run is harmless.
        window.setTimeout(placeBelowPluginMenu, 0);
    });

    /**
     * Shared dismissal. `action` is the AJAX endpoint and `parent` the notice
     * the clicked control belongs to.
     */
    function dismiss($button, action, parent) {
        var $notice = $button.closest(parent);

        if ($button.data('oxiBusy')) {
            return;
        }
        $button.data('oxiBusy', true);

        $.ajax({
            url: oxilab_flip_notice_dissmiss.ajaxurl,
            method: 'POST',
            data: {
                _wpnonce: oxilab_flip_notice_dissmiss.nonce,
                action: action,
                notice: $button.attr('sup-data')
            }
        }).always(function () {
            $notice.addClass('is-dismissing');
            window.setTimeout(function () {
                $notice.remove();
            }, 300);
        });
    }

    /**
     * Review notice. The "Sure, you deserve it!" link is deliberately not wired
     * up here: it opens the review form and leaves the notice in place, so the
     * reader can come back and confirm with "I already did".
     */
    $(document).on("click", ".oxi-flip-support-reviews", function (e) {
        e.preventDefault();
        dismiss($(this), 'oxilab_flip_notice_dissmiss', REVIEW_NOTICE);
    });

    /**
     * Upgrade notice. "See Pro plans" is likewise left alone so the pricing
     * page opens without the notice vanishing behind it.
     */
    $(document).on("click", ".oxi-flip-upgrade-dismiss", function (e) {
        e.preventDefault();
        dismiss($(this), 'oxilab_flip_upgrade_dissmiss', UPGRADE_NOTICE);
    });

})(jQuery);
