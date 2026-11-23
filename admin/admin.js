/**
 * Mloun Side Contact - Admin JavaScript
 *
 * Channel-first builder: clicking a tile clones that channel's template card,
 * then keeps a live preview of the generated link so the URL can never be mistyped.
 *
 * @package Mloun_Side_Contact
 * @version 1.4.39
 */

jQuery( document ).ready( function ( $ ) {

	// Save flash → top-centre toast, auto-dismiss after 4.5s.
	( function () {
		function showFlash() {
			var src = document.querySelector( '.mlsc-flash-source' );
			if ( ! src || src.dataset.toastDone ) {
				return;
			}
			src.dataset.toastDone = '1';
			var toast = document.createElement( 'div' );
			toast.className = 'mlsc-toast';
			toast.setAttribute( 'role', 'status' );
			toast.textContent = ( src.textContent || '' ).trim() || 'Settings saved.';
			document.body.appendChild( toast );
			window.setTimeout( function () {
				toast.style.opacity = '0';
				toast.style.transition = 'opacity .3s ease';
			}, 4200 );
			window.setTimeout( function () { toast.remove(); }, 4600 );
		}
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', showFlash );
		} else {
			showFlash();
		}
	} )();


    'use strict';

    var $list   = $( '#mlsc-items' );
    var $count  = $( '#mlsc-item-count' );
    var $empty  = $( '#mlsc-empty-note' );

    if ( ! $list.length ) {
        return;
    }

    /* ------------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------------ */

    function nextIndex() {
        var max = -1;
        $list.children( '.mlsc-item' ).each( function () {
            var i = parseInt( $( this ).attr( 'data-index' ), 10 );
            if ( ! isNaN( i ) && i > max ) {
                max = i;
            }
        } );
        return max + 1;
    }

    function refreshState() {
        var n = $list.children( '.mlsc-item' ).length;
        $count.text( n );
        $empty.toggle( 0 === n );

        // A tile is "added" when at least one card of that channel exists. Repeatable
        // tiles show how many cards of that channel are in use instead of a tick.
        $( '.mlsc-channel-tile' ).each( function () {
            var $t   = $( this );
            var ch   = $t.attr( 'data-channel' );
            var n    = $list.children( '.mlsc-item[data-channel="' + ch + '"]' ).length;

            $t.toggleClass( 'is-added', n > 0 );

            if ( '1' === $t.attr( 'data-repeatable' ) ) {
                // Show the multiplier only once it matters; the + chip carries the hint.
                var $times = $t.find( '.mlsc-tile-times' );
                $times.text( n > 1 ? '×' + n : '' ).css( 'display', n > 1 ? 'inline-block' : 'none' );
            }
        } );
    }

    function digitsOnly( s ) {
        return ( s || '' ).replace( /\D/g, '' );
    }

    /**
     * Mirrors mlsc_build_link() on the server. Preview only — PHP is authoritative.
     */
    function buildPreview( $card ) {
        var build = $card.attr( 'data-build' ) || '';
        var $val  = $card.find( '.mlsc-value' ).first();
        var value = $val.length ? $.trim( $val.val() || '' ) : '';
        var override = $.trim( $card.find( '.mlsc-link-override' ).val() || '' );

        if ( value && /^https?:\/\//i.test( value ) ) {
            return value;
        }
        if ( ! build || ! value ) {
            return override;
        }

        var isPhone = 'phone' === $card.attr( 'data-type' );
        var digits  = digitsOnly( value );
        var cc      = digitsOnly( $card.find( '.mlsc-cc' ).val() || '' );
        var full    = isPhone ? ( '+' === value.charAt( 0 ) ? digits : cc + digits ) : digits;
        var clean   = value.replace( /^@+/, '' );

        var url = build
            .split( '{value_enc}' ).join( encodeURIComponent( clean ) )
            .split( '{full_enc}' ).join( encodeURIComponent( '+' + full ) )
            .split( '{value}' ).join( clean )
            .split( '{full}' ).join( full );

        if ( '1' === $card.attr( 'data-prefill' ) && url ) {
            var msg = $.trim( $card.find( '.mlsc-prefill' ).val() || '' );
            if ( msg ) {
                url += ( url.indexOf( '?' ) === -1 ? '?' : '&' ) + 'text=' + encodeURIComponent( msg );
            }
        }

        return url || override;
    }

    function updatePreview( $card ) {
        var url = buildPreview( $card );
        $card.find( '.mlsc-url-preview' ).text( url || '' );
        $card.find( '.mlsc-url-preview' ).toggleClass( 'is-empty', ! url );
    }

    function updateBadgeColor( $card ) {
        var color = $card.find( '.mlsc-item-color' ).val();
        if ( color ) {
            $card.find( '.mlsc-item-badge' ).css( 'background', color );
        }
    }

    /* ------------------------------------------------------------------
     * Add a channel
     * ------------------------------------------------------------------ */

    function addChannelCard( $tile ) {
        var channel    = $tile.attr( 'data-channel' );
        var repeatable = '1' === $tile.attr( 'data-repeatable' );
        var $tpl       = $( '#mlsc-tpl-' + channel );

        if ( ! $tpl.length ) {
            return null;
        }

        var idx   = nextIndex();
        var $card = $( $tpl.text().split( '__INDEX__' ).join( idx ) );

        // Copy the channel SVG straight from the tile — keeps the template lean.
        var $badge = $card.find( '.mlsc-item-badge[data-need-icon]' );
        if ( $badge.length ) {
            $badge.html( $tile.find( '.mlsc-tile-icon' ).html() );
            $badge.removeAttr( 'data-need-icon' );
        }

        $list.append( $card );

        if ( repeatable ) {
            var total = $list.children( '.mlsc-item[data-channel="' + channel + '"]' ).length;
            var $name = $card.find( 'input[name$="[name]"]' );
            var base  = $.trim( $tile.attr( 'data-label' ) || '' );

            // Only touch the label while it is still the untouched default.
            if ( total > 1 && $name.length && base && $.trim( $name.val() ) === base ) {
                $name.val( base + ' ' + total );
                // val() does not fire input, so sync the card header here.
                $card.find( '.mlsc-item-title' ).text( base + ' ' + total );
            }
        }

        refreshState();
        updatePreview( $card );

        $( 'html, body' ).animate( { scrollTop: $card.offset().top - 60 }, 250 );
        $card.addClass( 'is-flash' );
        setTimeout( function () { $card.removeClass( 'is-flash' ); }, 1200 );

        $card.find( '.mlsc-value' ).trigger( 'focus' );

        return $card;
    }

    $( document ).on( 'click', '.mlsc-channel-tile', function () {
        var $tile      = $( this );
        var channel    = $tile.attr( 'data-channel' );
        var repeatable = '1' === $tile.attr( 'data-repeatable' );

        // Already added? Jump to the existing card instead of duplicating it — unless
        // the channel is repeatable (Custom Link), where a second click is the whole
        // point: every extra card is another button in the bar.
        var $existing = $list.children( '.mlsc-item[data-channel="' + channel + '"]' ).first();

        if ( ! repeatable && $existing.length ) {
            $( 'html, body' ).animate( { scrollTop: $existing.offset().top - 60 }, 250 );
            $existing.addClass( 'is-flash' );
            setTimeout( function () { $existing.removeClass( 'is-flash' ); }, 1200 );
            return;
        }

        addChannelCard( $tile );
    } );

    // "Add another ..." inside a repeatable card: same path as clicking the tile again.
    $( document ).on( 'click', '.mlsc-add-another', function () {
        var $tile = $( '.mlsc-channel-tile[data-channel="' + $( this ).attr( 'data-channel' ) + '"]' ).first();

        if ( $tile.length ) {
            addChannelCard( $tile );
        }
    } );

    /* ------------------------------------------------------------------
     * Remove a channel
     * ------------------------------------------------------------------ */

    $( document ).on( 'click', '.mlsc-delete-btn', function () {
        var $card = $( this ).closest( '.mlsc-item' );
        $card.slideUp( 160, function () {
            $card.remove();
            refreshState();
        } );
    } );

    /* ------------------------------------------------------------------
     * Live updates
     * ------------------------------------------------------------------ */

    $( document ).on( 'input change', '.mlsc-value, .mlsc-cc, .mlsc-prefill, .mlsc-link-override', function () {
        updatePreview( $( this ).closest( '.mlsc-item' ) );
    } );

    $( document ).on( 'input change', '.mlsc-item-color', function () {
        updateBadgeColor( $( this ).closest( '.mlsc-item' ) );
    } );

    // Several cards can share one channel (Custom Link), so the card header follows the
    // editable name — otherwise the list shows three identical titles.
    $( document ).on( 'input', 'input[name$="[name]"]', function () {
        var $card = $( this ).closest( '.mlsc-item' );
        var label = $.trim( $( this ).val() );

        if ( label ) {
            $card.find( '.mlsc-item-title' ).text( label );
        }
    } );

    /* ------------------------------------------------------------------
     * Drag to reorder
     * ------------------------------------------------------------------ */

    if ( $.fn.sortable ) {
        $list.sortable( {
            handle: '.mlsc-item-header',
            placeholder: 'mlsc-item-placeholder',
            forcePlaceholderSize: true,
            update: refreshState
        } );
    }

    /* ------------------------------------------------------------------
     * Style preset cards (visual active state before saving)
     * ------------------------------------------------------------------ */

    $( document ).on( 'change', '.mlsc-style-card input[type="radio"]', function () {
        $( '.mlsc-style-card' ).removeClass( 'is-active' );
        $( this ).closest( '.mlsc-style-card' ).addClass( 'is-active' );
    } );

    /* ------------------------------------------------------------------
     * Tabs (Select channels  <->  Widget customization)
     * ------------------------------------------------------------------ */

    function goToStep( step ) {
        $( '.mlsc-step' ).removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
        $( '.mlsc-step[data-step="' + step + '"]' ).addClass( 'is-active' ).attr( 'aria-selected', 'true' );

        $( '.mlsc-tab-panel' ).removeClass( 'is-active' );
        $( '.mlsc-tab-panel[data-panel="' + step + '"]' ).addClass( 'is-active' );

        $( 'html, body' ).animate( { scrollTop: $( '.mlsc-steps' ).offset().top - 40 }, 200 );
    }

    $( document ).on( 'click', '.mlsc-step', function () {
        goToStep( $( this ).attr( 'data-step' ) );
    } );

    $( document ).on( 'click', '.mlsc-goto', function () {
        goToStep( $( this ).attr( 'data-step' ) );
    } );

    /* ------------------------------------------------------------------
     * Boot
     * ------------------------------------------------------------------ */

    $list.children( '.mlsc-item' ).each( function () {
        updatePreview( $( this ) );
        updateBadgeColor( $( this ) );
    } );
    refreshState();

} );

/* ==========================================================================
   Media library picker + live preview
   Independent of the channel builder above, which bails out when the list is
   missing, so this runs on any settings screen.
   ========================================================================== */

jQuery( function ( $ ) {

    'use strict';

    var cfg = window.MLSC_ADMIN || {};
    var i18n = cfg.i18n || {};

    /* ------------------------------------------------------------------
     * Media library picker
     * ------------------------------------------------------------------ */

    $( document ).on( 'click', '.mlsc-media-pick', function ( event ) {

        event.preventDefault();

        var $field = $( this ).closest( '.mlsc-media' );
        var $url   = $field.find( '.mlsc-media-url' );
        var $thumb = $field.find( '.mlsc-media-thumb' );

        if ( ! window.wp || ! window.wp.media ) {
            // No media library on this screen: fall back to the URL field.
            $url.trigger( 'focus' );
            return;
        }

        var frame = window.wp.media( {
            title: i18n.chooseImage || 'Choose image',
            button: { text: i18n.useImage || 'Use this image' },
            library: { type: 'image' },
            multiple: false
        } );

        frame.on( 'select', function () {
            var attachment = frame.state().get( 'selection' ).first().toJSON();
            var url = attachment.sizes && attachment.sizes.thumbnail
                ? attachment.sizes.thumbnail.url
                : attachment.url;

            // Keep the full-size URL in the field, show the thumbnail in the preview.
            $url.val( attachment.url ).trigger( 'change' );
            $thumb.removeClass( 'is-empty' ).find( 'img' ).attr( 'src', url );
        } );

        frame.open();
    } );

    $( document ).on( 'click', '.mlsc-media-clear', function ( event ) {

        event.preventDefault();

        var $field = $( this ).closest( '.mlsc-media' );

        $field.find( '.mlsc-media-url' ).val( '' ).trigger( 'change' );
        $field.find( '.mlsc-media-thumb' ).addClass( 'is-empty' ).find( 'img' ).attr( 'src', '' );
    } );

    // Typing a URL by hand keeps the thumbnail honest.
    $( document ).on( 'input', '.mlsc-media-url', function () {

        var $field = $( this ).closest( '.mlsc-media' );
        var $thumb = $field.find( '.mlsc-media-thumb' );
        var value  = $.trim( $( this ).val() );

        $thumb.toggleClass( 'is-empty', value === '' ).find( 'img' ).attr( 'src', value );
    } );

    /* ------------------------------------------------------------------
     * Live preview
     * ------------------------------------------------------------------ */

    var $preview = $( '#mlsc-preview' );
    var $iframe  = $( '#mlsc-preview-frame' );
    var $status  = $( '#mlsc-preview-status' );

    if ( ! $preview.length || ! $iframe.length ) {
        return;
    }

    var timer    = null;
    var inflight = null;

    var lastHtml  = '';
    var lastCss   = '';
    var baseCss   = null;   // frontend.css source, fetched once so it can be rewritten
    var cssAsked  = false;

    /**
     * Repaint from the last response. Needed because switching device changes the
     * stylesheet variant, not just the iframe size.
     */
    function paint() {

        if ( null === baseCss ) {
            return; // wait for the stylesheet; the fail path falls back to <link>
        }

        $iframe.attr( 'srcdoc', buildDocument( lastHtml, lastCss, device ) );
        fitFrame();

        // On a phone canvas the widget can legitimately be hidden by "Hide on
        // mobile". Say so instead of leaving an empty canvas that looks broken.
        if ( ! $status.hasClass( 'is-error' ) ) {
            var hiddenOnPhone = ( 'mobile' === device && lastHtml && -1 !== lastHtml.indexOf( 'mlsc-mobile-hide' ) );
            setStatus( hiddenOnPhone ? ( i18n.hiddenOnMobile || 'Hidden on phones' ) : '' );
        }
    }

    function loadBaseCss() {

        if ( null !== baseCss || cssAsked || ! cfg.cssUrl ) {
            return;
        }

        cssAsked = true;

        $.ajax( { url: cfg.cssUrl, dataType: 'text', cache: true } )
            .done( function ( text ) {
                baseCss = typeof text === 'string' ? text : '';
                paint();
            } )
            .fail( function () {
                baseCss = ''; // keep using the <link> fallback
            } );
    }

    /**
     * The preview canvas is always narrower than the frontend's 768px phone
     * breakpoint, so a "max-width: 768px" rule would fire inside the preview and
     * misrepresent a desktop visit. Neutralise that breakpoint in EVERY sheet we
     * inject — frontend.css *and* the per-instance CSS the endpoint returns.
     *
     * Twelve versions of the preview only handled frontend.css, so the dynamic
     * sheet's "Hide on mobile" rule (".mlsc { display: none }" under 768px) killed
     * the whole widget inside the desktop preview on every site that had that
     * option switched on — the canvas looked empty and nobody could tell why.
     */
    function neutraliseBreakpoint( css, mode ) {

        if ( ! css ) {
            return '';
        }

        if ( 'desktop' !== mode ) {
            return css; // mobile mode must show the real phone layout
        }

        return css.replace( /@media[^{]*max-width:\s*768px[^{]*\{/g, '@media (max-width:0px){' );
    }

    function buildDocument( html, css, mode ) {

        var sheet;

        if ( null !== baseCss && '' !== baseCss ) {
            sheet = '<style>' + neutraliseBreakpoint( baseCss, mode ) + '</style>';
        } else {
            sheet = '<link rel="stylesheet" href="' + ( cfg.cssUrl || '' ) + '">';
        }

        return '<!doctype html><html><head><meta charset="utf-8">'
            + '<meta name="viewport" content="width=device-width,initial-scale=1">'
            + sheet
            + '<style>' + neutraliseBreakpoint( css, mode ) + '</style>'
            + '<style>'
            + 'html,body{margin:0;height:100%;background:#fff;font-family:Arial,Helvetica,sans-serif}'
            + '.mlsc{position:absolute !important}'
            // A neutral page skeleton: without it the preview is a blank sheet and you
            // cannot judge where the widget sits. It is inert, purely decorative.
            + '.mlsc-demo{height:100%;box-sizing:border-box;padding:18px 22px;overflow:hidden}'
            + '.mlsc-demo i{display:block;background:#eef0f4;border-radius:5px;margin-bottom:11px}'
            + '.mlsc-demo i.h{height:26px;width:42%;background:#e3e7ee}'
            + '.mlsc-demo i.s{height:11px}'
            + '.mlsc-demo i.s1{width:88%}.mlsc-demo i.s2{width:79%}.mlsc-demo i.s3{width:84%}'
            + '.mlsc-demo i.b{height:120px;width:100%;background:#f4f6f9;border-radius:9px;margin:18px 0}'
            + '.mlsc-demo i.s4{width:66%}.mlsc-demo i.s5{width:74%}'
            + '.mlsc-demo i.b2{height:150px;background:#f4f6f9;border-radius:9px;margin-top:18px}'
            + '</style>'
            + '</head><body>'
            + '<div class="mlsc-demo" aria-hidden="true"><i class="h"></i><i class="s s1"></i><i class="s s2"></i>'
            + '<i class="s s3"></i><i class="b"></i><i class="s s4"></i><i class="s s5"></i><i class="b2"></i></div>'
            + ( html || '' ) + '</body></html>';
    }

    function warn() {
        if ( window.console && window.console.warn ) {
            var args = [ '[Mloun Side Contact]' ];
            for ( var i = 0; i < arguments.length; i++ ) { args.push( arguments[ i ] ); }
            window.console.warn.apply( window.console, args );
        }
    }

    function setStatus( text, state ) {

        $status.removeClass( 'is-busy is-error' ).text( text || '' );

        if ( state ) {
            $status.addClass( state );
        }
    }

    function request() {

        var form = $( 'form[action="options.php"]' ).first();

        if ( ! form.length ) {
            return;
        }

        if ( inflight ) {
            inflight.abort();
        }

        setStatus( '', 'is-busy' );

        inflight = $.ajax( {
            url: cfg.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            timeout: 25000,
            cache: false,
            data: {
                action: 'mlsc_preview',
                nonce: cfg.nonce,
                form: form.serialize()
            }
        } ).done( function ( response ) {

            if ( ! response || ! response.success ) {
                var msg = ( response && response.data && response.data.message )
                    ? response.data.message
                    : ( i18n.previewFail || 'Preview failed' );
                setStatus( msg, 'is-error' );
                warn( 'unexpected response', response );
                return;
            }

            var data = response.data || {};

            if ( 0 === data.count ) {
                setStatus( i18n.noChannels || 'Add a channel', 'is-error' );
            } else if ( ! data.html ) {
                // Channels were accepted but nothing came back to draw. Always show
                // this instead of a silent skeleton, otherwise a server-side problem
                // looks like "the preview is broken" with no clue why.
                setStatus( i18n.previewEmpty || 'Preview returned no markup', 'is-error' );
                warn( 'empty markup', data );
            } else {
                setStatus( '' );
            }

            lastHtml = data.html || '';
            lastCss  = data.css || '';
            paint();

        } ).fail( function ( xhr, textStatus, errorThrown ) {

            if ( 'abort' === textStatus ) {
                return;
            }

            var hint = 'timeout' === textStatus
                ? 'timeout'
                : ( xhr && xhr.status ? 'HTTP ' + xhr.status : textStatus );

            setStatus( ( i18n.previewFail || 'Preview failed' ) + ' · ' + hint, 'is-error' );
            warn( 'request failed', textStatus, errorThrown, ( xhr && xhr.responseText || '' ).slice( 0, 400 ) );

        } ).always( function () {
            inflight = null;
        } );
    }

    function schedule() {

        window.clearTimeout( timer );
        timer = window.setTimeout( request, 400 );
    }

    // Any settings change re-renders.
    $( document ).on( 'input change', 'form[action="options.php"] :input', schedule );

    // Channels added, removed or reordered by the builder.
    var list = document.getElementById( 'mlsc-items' );

    if ( list && window.MutationObserver ) {
        new window.MutationObserver( schedule ).observe( list, {
            childList: true,
            subtree: true
        } );
    }

    /* ---- device switch + scaled canvas ----
       The iframe is rendered at a REAL viewport size (1024px desktop / 390px mobile)
       and shrunk with a CSS transform. Sizing the iframe to the panel width instead
       would put it under the frontend's 768px breakpoint and every preview would
       come out as the mobile layout, on desktop too. */

    // The canvas IS the preview panel — no scaling at all, so the widget renders at
    // exactly the size and proportions visitors see. Desktop layout on a narrow panel
    // comes from neutralising the phone breakpoint inside buildDocument(), not from
    // shrinking a wider canvas.
    var CANVAS_H = 760;
    var MOBILE_W = 390;

    var device = 'desktop';
    var $stage = $( '#mlsc-preview-stage' );

    function fitFrame() {

        var stageW = $stage.length ? $stage[0].clientWidth : 0;

        if ( ! stageW ) {
            return;
        }

        var w = 'mobile' === device ? MOBILE_W : stageW;

        $iframe.css( {
            width: w + 'px',
            height: CANVAS_H + 'px',
            transform: 'none',
            left: Math.max( 0, Math.round( ( stageW - w ) / 2 ) ) + 'px'
        } );

        $stage.css( 'height', CANVAS_H + 'px' );
    }

    $( document ).on( 'click', '.mlsc-device', function () {

        device = $( this ).attr( 'data-device' ) || 'desktop';

        $( '.mlsc-device' ).removeClass( 'is-active' ).attr( 'aria-pressed', 'false' );
        $( this ).addClass( 'is-active' ).attr( 'aria-pressed', 'true' );

        $preview.toggleClass( 'is-mobile', 'mobile' === device );
        paint();
    } );

    var resizeTimer = null;

    $( window ).on( 'resize', function () {
        window.clearTimeout( resizeTimer );
        resizeTimer = window.setTimeout( fitFrame, 150 );
    } );

    // Initial paint.
    fitFrame();
    loadBaseCss();
    request();
} );
