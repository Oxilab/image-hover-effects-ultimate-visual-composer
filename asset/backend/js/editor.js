/**
 * Flip box editor: save without reloading, item dialog, delete dialog.
 *
 * Saving posts the very same form to the very same page in the background,
 * so the server runs exactly the code it ran before (same fields, nonce and
 * checks). That response is the freshly rendered editor; the parts that show
 * saved data (preview, its CSS, item templates, reorder list, name) are
 * copied from it. Whatever the user is editing in the settings stays as is.
 * If this file does not load, every form still posts and reloads as before.
 */
jQuery(function ($) {
    'use strict';

    var canAjax = !!(window.fetch && window.FormData && window.DOMParser);
    var $modal = $('#oxi-addons-list-data-modal');
    var itemForm = document.getElementById('oxi-flip-template-modal-form');
    var $toast = $('#oxi-flip-ed-toast').appendTo(document.body);
    var toastTimer = null;

    /* Toast */

    function toast(key, isError) {
        if (!$toast.length) {
            return;
        }
        clearTimeout(toastTimer);
        $toast.toggleClass('is-error', !!isError)
            .find('.oxi-flip-ed-toast-text').text($toast.attr('data-' + key));
        $toast.prop('hidden', false).removeClass('is-in');
        $toast[0].offsetWidth; // restart the slide in
        $toast.addClass('is-in');
        toastTimer = setTimeout(function () {
            $toast.removeClass('is-in');
            toastTimer = setTimeout(function () {
                $toast.prop('hidden', true);
            }, 250);
        }, isError ? 6000 : 2600);
    }

    $toast.on('click', '.oxi-flip-ed-toast-close', function () {
        clearTimeout(toastTimer);
        $toast.removeClass('is-in').prop('hidden', true);
    });

    /* Background save */

    function send(form, name, value) {
        var data = new FormData(form);
        if (name) {
            data.append(name, value);
        }
        return fetch(window.location.href.split('#')[0], {
            method: 'POST',
            body: data,
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.text();
        }).then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            // A failed nonce prints a plain message, an expired login the
            // login page: neither has the editor in it.
            if (!doc.getElementById('oxi-addons-preview-data')) {
                throw new Error('No editor in the response');
            }
            refresh(doc);
            return doc;
        });
    }

    // Bring `from` (on the page) in line with `to` (from the response) in
    // place: identical parts are left alone, changed attributes and text are
    // set on the existing nodes, and only new or removed nodes are added or
    // removed. Replacing the whole preview instead threw away the very
    // elements the browser's inspector was showing, so their code vanished
    // from Elements after every save.
    function morph(from, to) {
        var i, name;
        for (i = from.attributes.length - 1; i >= 0; i--) {
            name = from.attributes[i].name;
            if (!to.hasAttribute(name)) {
                from.removeAttribute(name);
            }
        }
        for (i = 0; i < to.attributes.length; i++) {
            if (from.getAttribute(to.attributes[i].name) !== to.attributes[i].value) {
                from.setAttribute(to.attributes[i].name, to.attributes[i].value);
            }
        }
        var kids = Array.prototype.slice.call(to.childNodes);
        for (i = 0; i < kids.length; i++) {
            var next = kids[i];
            var cur = from.childNodes[i];
            if (!cur) {
                from.appendChild(document.importNode(next, true));
            } else if (cur.isEqualNode(next)) {
                continue;
            } else if (cur.nodeType === next.nodeType && cur.nodeName === next.nodeName && cur.nodeType === 1 && cur.nodeName !== 'SCRIPT') {
                morph(cur, next);
            } else if (cur.nodeType === next.nodeType && (cur.nodeType === 3 || cur.nodeType === 8)) {
                cur.nodeValue = next.nodeValue;
            } else {
                from.replaceChild(document.importNode(next, true), cur);
            }
        }
        while (from.childNodes.length > kids.length) {
            from.removeChild(from.lastChild);
        }
    }

    function refresh(doc) {
        // Preview. Its edit and delete forms use delegated handlers below.
        // Its CSS is already on the page, so the load guard is not needed
        // (and its script, a one-time guard, does nothing a second time).
        morph($('#oxi-addons-preview-data')[0], doc.getElementById('oxi-addons-preview-data'));
        $('#oxi-addons-preview-data .oxi-flip-booting').removeClass('oxi-flip-booting');

        // The preview's CSS (printed with the flip box stylesheet).
        var css = doc.getElementById('flip-box-addons-style-inline-css');
        var current = document.getElementById('flip-box-addons-style-inline-css');
        if (css && current) {
            if (current.textContent !== css.textContent) {
                current.textContent = css.textContent;
            }
        } else if (css) {
            document.head.appendChild(document.importNode(css, true));
        } else if (current) {
            current.textContent = '';
        }

        // Stylesheets the new data needs, such as a newly chosen Google font.
        $(doc).find('link[rel="stylesheet"][id]').each(function () {
            var mine = document.getElementById(this.id);
            if (!mine) {
                document.head.appendChild(document.importNode(this, true));
            } else if (mine.tagName === 'LINK' && mine.href !== this.href) {
                mine.href = this.href;
            }
        });

        // Item edit templates (a new item gets its id here).
        var tpls = doc.querySelector('.oxi-flip-edit-templates');
        if (tpls) {
            $('.oxi-flip-edit-templates').replaceWith(document.importNode(tpls, true));
        }

        // Reorder list.
        var list = doc.getElementById('oxi-addons-modal-rearrange');
        var $list = $('#oxi-addons-modal-rearrange');
        if (list && $list.length) {
            $list[0].innerHTML = list.innerHTML;
            if ($list.data('ui-sortable')) {
                $list.sortable('refresh');
            }
        }

        // Name in the header.
        var name = doc.querySelector('.oxi-flip-ed-title h1');
        if (name) {
            $('.oxi-flip-ed-title h1').text(name.textContent);
        }

        flipTriggerPreview();
    }

    /* Flip Trigger: the preview follows the select before saving too */

    function flipTriggerPreview() {
        var $select = $('#oxilab-flip-trigger');
        if (!$select.length) {
            return;
        }
        var click = $select.val() === 'click';
        var $preview = $('#oxi-addons-preview-data');
        $preview.find('.oxi-addons-container').toggleClass('oxi-flip-trigger-click', click);
        if (!click) {
            $preview.find('.oxilab-flip-box-flip.active').removeClass('active');
        }
        if (click && window.oxiFlipTriggerPrepare) {
            window.oxiFlipTriggerPrepare();
        }
    }

    $(document).on('change', '#oxilab-flip-trigger', flipTriggerPreview);

    function busy($button, on) {
        $button.prop('disabled', on).toggleClass('oxi-flip-ed-saving', on).attr('aria-busy', on ? 'true' : null);
    }

    // The button that submitted, or the form's own save button for Enter.
    function submitter(e, form, fallback) {
        var b = e.originalEvent && e.originalEvent.submitter;
        return b && b.name ? b : form.querySelector(fallback);
    }

    if (canAjax) {
        // Save changes. Delegated, so it runs after the form's own submit
        // handlers (the free version resets its locked options in one).
        $(document).on('submit', '#oxi-addons-form-submit', function (e) {
            var button = submitter(e, this, 'button[name="oxi-addons-flip-templates-submit"]');
            if (!button) {
                return;
            }
            e.preventDefault();
            var $button = $(button);
            if ($button.prop('disabled')) {
                return;
            }
            busy($button, true);
            send(this, button.name, button.value).then(function () {
                toast('saved');
            }, function () {
                toast('error', true);
            }).then(function () {
                busy($button, false);
            });
        });

        // Item dialog Save.
        $(document).on('submit', '#oxi-flip-template-modal-form', function (e) {
            var button = submitter(e, this, '#oxi-flip-template-modal-submit');
            if (!button) {
                return;
            }
            e.preventDefault();
            var $button = $(button);
            if ($button.prop('disabled')) {
                return;
            }
            busy($button, true);
            send(this, button.name, button.value).then(function () {
                $modal.modal('hide');
                toast('item');
            }, function () {
                // The dialog stays open, so nothing typed is lost.
                toast('error', true);
            }).then(function () {
                busy($button, false);
            });
        });

        // Clone on a preview item: the copy is added at the end.
        $(document).on('submit', '.oxilab-style-absulate-clone form', function (e) {
            var button = submitter(e, this, 'button[name="clone"]');
            if (!button) {
                return;
            }
            e.preventDefault();
            var $button = $(button);
            if ($button.prop('disabled')) {
                return;
            }
            busy($button, true);
            send(this, button.name, button.value).then(function () {
                toast('cloned');
            }, function () {
                toast('error', true);
            }).then(function () {
                busy($button, false);
            });
        });

        // Rename.
        $(document).on('submit', '.oxi-addons-shortcode-body form', function (e) {
            var button = submitter(e, this, 'button[name="addonsstylenamechange"]');
            if (!button || button.name !== 'addonsstylenamechange') {
                return;
            }
            e.preventDefault();
            var $button = $(button);
            if ($button.prop('disabled')) {
                return;
            }
            busy($button, true);
            send(this, button.name, button.value).then(function () {
                toast('renamed');
            }, function () {
                toast('error', true);
            }).then(function () {
                busy($button, false);
            });
        });
    }

    /* Delete confirmation dialog (replaces vendor.js's confirm()) */

    var $dialog = $('#oxi-flip-ed-delete-dialog');
    if ($dialog.length) {
        // Out of the editor layout so position: fixed is always the viewport.
        $dialog.appendTo(document.body);

        var $title = $('#oxi-flip-ed-delete-title');
        var $submit = $('#oxi-flip-ed-delete-submit');
        var submitLabel = $submit.text();
        var pending = null;
        var lastFocus = null;

        // The item's front title, read from its edit template when it has one.
        var itemTitle = function (id) {
            var tpl = document.getElementById('oxi-flip-edit-tpl-' + id);
            var field = tpl && tpl.content ? tpl.content.querySelector('[name="flip-box-front-title"]') : null;
            // DOMParser documents are inert: markup in a saved title never runs.
            var text = field ? $.trim(new DOMParser().parseFromString(field.getAttribute('value') || '', 'text/html').body.textContent || '') : '';
            return text.length > 60 ? text.slice(0, 57) + '...' : text;
        };

        var openDialog = function (form, trigger) {
            pending = form;
            lastFocus = trigger || document.activeElement;
            var name = itemTitle(parseInt($(form).find('input[name="item-id"]').val(), 10));
            $title.text(name ? $title.attr('data-template').replace('%s', name) : $title.attr('data-default'));
            $submit.prop('disabled', false).text(submitLabel);
            $dialog.prop('hidden', false);
            $('body').addClass('oxi-flip-ed-dialog-open');
            $dialog.find('.oxi-flip-ed-dialog-btn[data-oxi-flip-ed-close]').trigger('focus');
        };

        var closeDialog = function () {
            if ($submit.prop('disabled')) {
                return;
            }
            pending = null;
            $dialog.prop('hidden', true);
            $('body').removeClass('oxi-flip-ed-dialog-open');
            if (lastFocus && document.body.contains(lastFocus)) {
                lastFocus.focus();
            }
        };

        $('.oxilab-style-absulate-delete-confirmation').off('submit');
        $(document).on('submit', '.oxilab-style-absulate-delete-confirmation', function (e) {
            e.preventDefault();
            openDialog(this, e.originalEvent && e.originalEvent.submitter);
        });

        $dialog.on('click', '[data-oxi-flip-ed-close]', closeDialog);
        $(document).on('keydown', function (e) {
            if ($dialog.prop('hidden')) {
                return;
            }
            if (e.key === 'Escape') {
                closeDialog();
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
            var form = pending;
            $submit.prop('disabled', true).text($submit.attr('data-busy'));
            if (!canAjax) {
                // A native submit skips the button, so send its delete=delete pair.
                $('<input type="hidden" name="delete" value="delete">').appendTo(form);
                HTMLFormElement.prototype.submit.call(form);
                return;
            }
            send(form, 'delete', 'delete').then(function () {
                toast('deleted');
            }, function () {
                toast('error', true);
            }).then(function () {
                $submit.prop('disabled', false);
                lastFocus = null;
                closeDialog();
            });
        });

        // Back button from the next page: show the editor, not a busy dialog.
        $(window).on('pageshow', function (e) {
            if (e.originalEvent && e.originalEvent.persisted) {
                $submit.prop('disabled', false);
                $(pending).find('input[type="hidden"][name="delete"]').remove();
                closeDialog();
            }
        });
    }

    /* Item dialog: open Edit without reloading */

    if (!$modal.length || !itemForm || !('content' in document.createElement('template'))) {
        return;
    }

    function liveFields(name) {
        return $(itemForm.elements).filter(function () {
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

    // Edit on a preview item. Admin_Render::child_edit_templates() renders
    // every item's fields into an inert <template>; an item without one falls
    // back to the old reload flow.
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

    // Add a flip box: vendor.js resets the form, this also clears the values
    // a previous Edit filled in.
    $('#oxi-addons-list-data-modal-open').on('click', function () {
        var tpl = document.getElementById('oxi-flip-edit-tpl-new');
        if (tpl) {
            fill(tpl);
            $('#item-id').val('');
        }
    });

    /* Custom CSS: a code editor on the textarea */

    // WordPress's own CSS editor (CodeMirror, settings from
    // wp_enqueue_code_editor() in Admin_Render::hooks()). The textarea stays
    // the field the form sends, so saved CSS loads into the editor as it is
    // and every save posts exactly what is in the editor. Without the editor
    // (syntax highlighting turned off in the user's profile) nothing changes.
    var cssField = document.getElementById('custom-css');
    if (cssField && window.oxiFlipCssEditor && window.wp && wp.codeEditor) {
        var cssEditor = wp.codeEditor.initialize(cssField, window.oxiFlipCssEditor).codemirror;
        cssEditor.on('change', function () {
            cssEditor.save();
        });
        $(cssField).data('oxiCodeMirror', cssEditor);
        // It starts in a hidden tab: lay it out again once it is shown.
        $('.oxi-addons-tabs-ul li[ref="#oxilab-tabs-id-2"]').on('click', function () {
            setTimeout(function () {
                cssEditor.refresh();
            }, 0);
        });
    }

    /* Copy the shortcode (or the PHP code) next to its field */

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        // Sites without HTTPS have no Clipboard API: fall back to a hidden textarea.
        return new Promise(function (resolve, reject) {
            var $tmp = $('<textarea readonly>').val(text).css({position: 'fixed', top: '-1000px'}).appendTo('body');
            var ok = false;
            $tmp[0].select();
            try {
                ok = document.execCommand('copy');
            } catch (err) {
                ok = false;
            }
            $tmp.remove();
            return ok ? resolve() : reject();
        });
    }

    $(document).on('click', '.oxi-flip-shortcode-copy', function () {
        var $btn = $(this);
        var input = $btn.siblings('input')[0];
        var label = $btn.data('label') || $btn.attr('aria-label');
        var restore = function () {
            $btn.removeClass('is-copied').attr({'aria-label': label, title: label})
                .find('.dashicons').removeClass('dashicons-yes').addClass('dashicons-admin-page');
        };
        if (!input) {
            return;
        }
        $btn.data('label', label);
        clearTimeout($btn.data('timer'));
        copyText(input.value).then(function () {
            $btn.addClass('is-copied').attr({'aria-label': $btn.attr('data-copied'), title: $btn.attr('data-copied')})
                .find('.dashicons').removeClass('dashicons-admin-page').addClass('dashicons-yes');
        }, function () {
            // Copy blocked: leave the text selected so Ctrl+C still works.
            input.focus();
            input.setSelectionRange(0, input.value.length);
            $btn.attr({'aria-label': $btn.attr('data-copy-failed'), title: $btn.attr('data-copy-failed')});
        });
        $btn.data('timer', setTimeout(restore, 1800));
    });
});
