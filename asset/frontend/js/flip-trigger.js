/**
 * Flipbox: Flip Trigger "On Click".
 *
 * Only flip boxes saved with Flip Trigger "On Click" carry the
 * oxi-flip-trigger-click class and load this file. Hover flip boxes are never
 * touched. Clicking toggles the "active" class that style.css already flips on.
 *
 * - First click flips, even when the whole box is a link.
 * - On the flipped side links open as usual; a click anywhere else flips back.
 * - Keyboard: the box is focusable and Enter or Space flips it.
 */
(function () {
    'use strict';

    if (window.oxiFlipTriggerReady) {
        return;
    }
    window.oxiFlipTriggerReady = true;

    var BOX = '.oxi-flip-trigger-click .oxilab-flip-box-flip';

    function setFlipped(box, on) {
        box.classList.toggle('active', on);
        if (box.hasAttribute('aria-pressed')) {
            box.setAttribute('aria-pressed', on ? 'true' : 'false');
        }
    }

    // Make boxes reachable by keyboard (not when the box sits inside a link,
    // which is focusable already).
    function prepare() {
        var boxes = document.querySelectorAll(BOX);
        for (var i = 0; i < boxes.length; i++) {
            var box = boxes[i];
            if (box.hasAttribute('data-oxi-flip-ready')) {
                continue;
            }
            box.setAttribute('data-oxi-flip-ready', '');
            if (!box.hasAttribute('tabindex') && !box.closest('a')) {
                box.setAttribute('tabindex', '0');
                box.setAttribute('role', 'button');
                box.setAttribute('aria-pressed', box.classList.contains('active') ? 'true' : 'false');
            }
        }
    }

    document.addEventListener('click', function (e) {
        var target = e.target && e.target.closest ? e.target : null;
        var box = target ? target.closest(BOX) : null;
        if (!box) {
            return;
        }
        var link = target.closest('a[href]');
        if (!box.classList.contains('active')) {
            if (link) {
                e.preventDefault();
            }
            setFlipped(box, true);
            return;
        }
        if (link) {
            return;
        }
        setFlipped(box, false);
    });

    document.addEventListener('keydown', function (e) {
        var box = e.target;
        if ((e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar') || !box || !box.matches || !box.matches(BOX)) {
            return;
        }
        e.preventDefault();
        setFlipped(box, !box.classList.contains('active'));
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', prepare);
    } else {
        prepare();
    }
    // Boxes added later (page builders, the editor preview after a save).
    window.oxiFlipTriggerPrepare = prepare;
    document.addEventListener('pointerover', function (e) {
        var target = e.target && e.target.closest ? e.target : null;
        var box = target ? target.closest(BOX) : null;
        if (box && !box.hasAttribute('data-oxi-flip-ready')) {
            prepare();
        }
    });
})();
