/**
 * Mloun Side Contact - Frontend JavaScript
 *
 * @package Mloun_Side_Contact
 * @version 1.4.38
 */

(function () {
    'use strict';

    var SCROLL_THRESHOLD = 0.2; // show Back to Top after 20% of the scrollable height
    var HYSTERESIS = 0.03;      // dead band around the threshold so it cannot flutter
    var MOBILE_QUERY = '(max-width: 768px)';

    function isMobile() {
        return !!(window.matchMedia && window.matchMedia(MOBILE_QUERY).matches);
    }

    /**
     * True when `bar` is the phone bottom-bar layout: no `.mlsc-mobile-side`
     * class while the viewport is phone-width.
     *
     * That layout is a flat full-width row pinned to the bottom edge, so its
     * rules are the flat ones: the Fold / Unfold setting does not apply, and
     * Back to Top stays put instead of following the desktop scroll threshold.
     * The side rail keeps the desktop behaviour. Both places share this test so
     * the two layouts cannot drift apart again.
     */
    function isPhoneBottomBar(bar) {
        return !!bar && isMobile() && !bar.classList.contains('mlsc-mobile-side');
    }

    function currentY() {
        return window.pageYOffset || document.documentElement.scrollTop || 0;
    }

    function each(list, fn) {
        Array.prototype.forEach.call(list, fn);
    }

    /**
     * Swap the background colour on hover (per-button override).
     */
    function initHoverColors() {
        each(document.querySelectorAll('.mlsc .item-inner'), function (el) {
            if ('1' === el.getAttribute('data-mlsc-hover-bound')) {
                return;
            }
            el.setAttribute('data-mlsc-hover-bound', '1');

            var normal = el.style.background;
            var hover = el.getAttribute('data-hover');

            if (!hover || hover === normal) {
                return;
            }

            el.addEventListener('mouseenter', function () {
                el.style.background = hover;
            });

            el.addEventListener('mouseleave', function () {
                el.style.background = normal;
            });
        });
    }

    /**
     * On phones the bar is pinned to the bottom edge, so the end of the page would
     * sit underneath it. Reserve exactly the bar's height instead of covering the
     * footer. The site's own inline padding is remembered and restored on desktop.
     */
    var bodyPaddingBefore = null;

    function reserveBottomSpace() {
        var body = document.body;

        if (!body) {
            return;
        }

        if (null === bodyPaddingBefore) {
            bodyPaddingBefore = body.style.paddingBottom || '';
        }

        var bar = document.querySelector('.mlsc');
        var height = 0;

        // Only the full-width BOTTOM BAR needs the page padded underneath it.
        // A side rail — including "Same as desktop side rail" on a phone — hugs a
        // screen edge and must not add empty space at the end of the page.
        if (isPhoneBottomBar(bar) && 'none' !== window.getComputedStyle(bar).display) {
            height = bar.getBoundingClientRect().height;
        }

        body.style.paddingBottom = height > 0
            ? (Math.round(height) + 8) + 'px'
            : bodyPaddingBefore;
    }

    /**
     * Fold / unfold: one button that reveals or hides the whole channel list.
     */
    function initFold() {
        each(document.querySelectorAll('.mlsc [data-mlsc-toggle]'), function (btn) {
            if ('1' === btn.getAttribute('data-mlsc-fold-bound')) {
                return;
            }
            btn.setAttribute('data-mlsc-fold-bound', '1');

            btn.addEventListener('click', function (event) {
                event.preventDefault();

                var bar = btn.closest('.mlsc');
                if (!bar) {
                    return;
                }

                var isOpen = bar.classList.contains('mlsc-open');
                bar.classList.toggle('mlsc-open', !isOpen);
                bar.classList.toggle('mlsc-collapsed', isOpen);
                btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });
        });
    }

    /**
     * Back to Top. On click it locks itself out of the DOM flow until the page is
     * back at the top, so no scroll event, resize or second initialisation can
     * flash it on again.
     */
    function initBackToTop() {
        var bars = [];

        each(document.querySelectorAll('.mlsc'), function (bar) {
            if (!bar.querySelector('li.mlsc-totop-item')) {
                return;
            }
            if ('1' === bar.getAttribute('data-mlsc-totop-init')) {
                return;
            }
            bar.setAttribute('data-mlsc-totop-init', '1');
            bars.push(bar);
        });

        if (!bars.length) {
            return;
        }

        var items = [];
        each(bars, function (bar) {
            each(bar.querySelectorAll('li.mlsc-totop-item'), function (li) {
                items.push(li);
            });
        });

        var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var hiding = false;
        var shown = false;
        var lastY = currentY();

        // Every bar renders one layout, so "any bar is the phone bottom bar" is
        // the answer for the whole instance.
        function phoneBottomBar() {
            var pinned = false;
            each(bars, function (bar) {
                if (isPhoneBottomBar(bar)) {
                    pinned = true;
                }
            });
            return pinned;
        }

        function setLocked(locked) {
            each(bars, function (bar) {
                if (locked) {
                    bar.classList.add('mlsc-totop-locked');
                } else {
                    bar.classList.remove('mlsc-totop-locked');
                }
            });
        }

        // Never write the class twice in a row: a redundant add restarts the
        // fade-in animation and reads as a flicker.
        function setVisible(want) {
            if (want === shown) {
                return;
            }
            shown = want;
            each(items, function (li) {
                if (want) {
                    li.classList.add('is-visible');
                } else {
                    li.classList.remove('is-visible');
                }
            });
        }

        function scrollableHeight() {
            var doc = document.documentElement;
            var full = Math.max(
                doc.scrollHeight || 0,
                document.body ? document.body.scrollHeight : 0
            );
            return Math.max(0, full - window.innerHeight);
        }

        function update() {
            var y = currentY();

            // Phone bottom bar: Back to Top belongs to the flat row and is always
            // shown, so it never follows the scroll threshold. Any lock a previous
            // tap left behind is cleared here too, otherwise the button could stay
            // hidden for good on a control that is meant to be permanent.
            if (phoneBottomBar()) {
                hiding = false;
                setLocked(false);
                setVisible(true);
                lastY = y;
                return;
            }

            // The side rail - on phones and on desktop alike - runs the shared
            // threshold calculation below.

            if (hiding) {
                var movedDown = y > lastY + 8;

                if (y <= 2 || movedDown) {
                    hiding = false;
                    setLocked(false);
                } else {
                    // Locked: touch nothing at all.
                    lastY = y;
                    return;
                }
            }

            var max = scrollableHeight();
            var ratio = max > 0 ? (y / max) : 0;

            // Appear exactly at the threshold; only the hiding side gets the dead band,
            // so the button still cannot flutter but shows up the moment you cross 20%.
            var want = shown
                ? (ratio > (SCROLL_THRESHOLD - HYSTERESIS))
                : (ratio >= SCROLL_THRESHOLD);

            setVisible(want);
            lastY = y;
        }

        function scrollToTop() {
            if (reduced || !('scrollBehavior' in document.documentElement.style)) {
                window.scrollTo(0, 0);
                return;
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        each(items, function (li) {
            var trigger = li.querySelector('[data-mlsc-totop]');
            if (!trigger || '1' === trigger.getAttribute('data-mlsc-top-bound')) {
                return;
            }
            trigger.setAttribute('data-mlsc-top-bound', '1');

            trigger.addEventListener('click', function (event) {
                event.preventDefault();

                // The phone bottom bar keeps Back to Top visible, so hiding it for
                // the duration of the scroll would only read as a flicker on a
                // control that is meant to be permanent.
                if (phoneBottomBar()) {
                    scrollToTop();
                    return;
                }

                // Side rail: lock the button out until the page is back at the top,
                // so no scroll race can flash it on again.
                hiding = true;
                lastY = currentY();
                setLocked(true);
                setVisible(false);

                scrollToTop();
            });

            trigger.addEventListener('keydown', function (event) {
                if ('Enter' === event.key || ' ' === event.key) {
                    event.preventDefault();
                    trigger.click();
                }
            });
        });

        var passive = false;
        try {
            window.addEventListener('test-passive', null, Object.defineProperty({}, 'passive', {
                get: function () {
                    passive = true;
                    return true;
                }
            }));
        } catch (err) {
            passive = false;
        }

        window.addEventListener('scroll', update, passive ? { passive: true } : false);
        window.addEventListener('resize', update);
        update();
    }

    function boot() {
        initHoverColors();
        initFold();
        initBackToTop();
        reserveBottomSpace();

        window.addEventListener('resize', reserveBottomSpace);

        // Fonts and images settle after load and can change the bar height.
        window.setTimeout(reserveBottomSpace, 300);
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // A second copy of this file (or a theme re-running scripts) must not create a
    // second controller for the same elements — re-run harmlessly, guards decide.
    document.addEventListener('mlsc-boot', boot);
})();
