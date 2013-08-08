/**
 * Google Analytics namespace / Object.
 *
 * @type {Object}.
 */
var _ga = _ga || {};

/**
 * Google Analytics Queue.
 *
 * @type {Array}
 */
var _gaq = _gaq || [];

jQuery(function() {

window.googleAnalyticsApi = (function($, gaq) {
    /**
     * Returns the normalized tracker name configuration parameter.
     *
     * @param {string} opt_trackerName An optional name for the tracker object.
     * @return {string} If opt_trackerName is set, then the value appended with a.
     *      Otherwise an empty string.
     * @private
     */
    var _buildTrackerName = function (opt_trackerName) {
        return opt_trackerName ? opt_trackerName + '.' : '';
    };

    /**
     * Extracts a query parameter value from a URI.
     *
     * @param {string} uri The URI from which to extract the parameter.
     * @param {string} paramName The name of the query paramater to extract.
     * @return {string} The un-encoded value of the query paramater. underfined
     *      if there is no URI parameter.
     * @private
     */
    var _extractParamFromUri = function (uri, paramName) {
        if (!uri) {
            return;
        }
        var regex = new RegExp('[\\?&#]' + paramName + '=([^&#]*)');
        var params = regex.exec(uri);
        if (params != null) {
            return unescape(params[1]);
        }
        return;
    };

    var clickHandler = function () {
        var $a = $(this);

        if ($a.is('input[type=button]')) {
            gaq.push(['_trackEvent', 'Button', 'Click']);
            return;
        }

        var href = $a.attr('href');

        if ((href == null) || (href === '#')) {
            return;
        }

        var category, event;

        if ($a.hasClass("btn")) {
            category = 'Button';
            event = 'Click';
            gaq.push(['_trackEvent', category, event, href]);
        }

        if ((href.match(/^http/i)) && (!href.match(document.domain))) {
            category = 'Outgoing';
            event = 'Link';
            gaq.push(['_trackEvent', category, event, href]);
        }

        var ext = href.match(/\.(doc|pdf|xls|ppt|zip|txt|vsd|vxd|js|css|rar|exe|wma|mov|avi|wmv|mp3)$/i)
        if (ext) {
            category = 'Download';
            event = ext[0];
            gaq.push(['_trackEvent', category, event, href]);
        }

        if (href.match(/^mailto:/i)) {
            category = 'Outgoing';
            event = 'Email';
            gaq.push(['_trackEvent', category, event, href]);
        }
    };

    /**
     * Tracks everytime a user clicks on a tweet button from Twitter.
     * This subscribes to the Twitter JS API event mechanism to listen for
     * clicks coming from this page. Details here:
     * http://dev.twitter.com/pages/intents-events#click
     * This method should be called once the twitter API has loaded.
     * @param {string} opt_pageUrl An optional URL to associate the social
     *     tracking with a particular page.
     * @param {string} opt_trackerName An optional name for the tracker object.
     */
    var trackTwitter = function (opt_pageUrl, opt_trackerName) {
        var trackerName = _buildTrackerName(opt_trackerName);
        try {
            if (twttr && twttr.events && twttr.events.bind) {
                twttr.events.bind('tweet', function (event) {
                    if (event) {
                        var targetUrl; // Default value is undefined.
                        if (event.target && event.target.nodeName == 'IFRAME') {
                            targetUrl = _extractParamFromUri(event.target.src, 'url');
                        }
                        gaq.push([trackerName + '_trackSocial', 'twitter', 'tweet',
                            targetUrl, opt_pageUrl]);
                    }
                });
            }
        } catch (e) {
        }
    };

    /**
     * Tracks Facebook likes, unlikes and sends by suscribing to the Facebook
     * JSAPI event model. Note: This will not track facebook buttons using the
     * iFrame method.
     *
     * @param {string} opt_pageUrl An optional URL to associate the social
     *     tracking with a particular page.
     * @param {string} opt_trackerName An optional name for the tracker object.
     */
    var trackFacebook = function (opt_pageUrl, opt_trackerName) {
        var trackerName = _buildTrackerName(opt_trackerName);
        try {
            if (FB && FB.Event && FB.Event.subscribe) {
                FB.Event.subscribe('edge.create', function (targetUrl) {
                    gaq.push([trackerName + '_trackSocial', 'facebook', 'like',
                        targetUrl, opt_pageUrl]);
                });
                FB.Event.subscribe('edge.remove', function (targetUrl) {
                    gaq.push([trackerName + '_trackSocial', 'facebook', 'unlike',
                        targetUrl, opt_pageUrl]);
                });
                FB.Event.subscribe('message.send', function (targetUrl) {
                    gaq.push([trackerName + '_trackSocial', 'facebook', 'send',
                        targetUrl, opt_pageUrl]);
                });
            }
        } catch (e) {
        }
    };

    /**
     * Helper method to track social features. This assumes all the social
     * scripts / apis are loaded synchronously. If they are loaded async,
     * you might need to add the network specific tracking call to the
     * callback once the network's script has loaded.
     * @param {string} opt_pageUrl An optional URL to associate the social
     *     tracking with a particular page.
     * @param {string} opt_trackerName An optional name for the tracker object.
     */
    var trackSocial = function (opt_pageUrl, opt_trackerName) {
        trackFacebook(opt_pageUrl, opt_trackerName);
        trackTwitter(opt_pageUrl, opt_trackerName);
    };

    var init = function(doDebug) {
        if (doDebug) {
            var debugEvent = function() {
                if (console) {
                    console.log('Google Analytics debug:', arguments[0]);
                }
            }
            // Replace the push function with a console.log.
            _gaq.push = debugEvent;

            // Dump everything with that's already present.
            for (var key in _gaq) {
                if (_gaq.hasOwnProperty(key) && typeof(_gaq[key]) != "function" ) {
                    debugEvent(_gaq[key]);
                }
            }
        } else {
            (function (d,t) {
                var ga = d.createElement(t);
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
                var s = d.getElementsByTagName(t)[0];
                s.parentNode.insertBefore(ga, s);
            })(document, 'script');
        }
    }

    // Track clicks
    $('a, input[type=button]').on('click', clickHandler);

    return {
        'clickHandler': clickHandler,
        'trackFacebook': trackFacebook,
        'trackTwitter': trackTwitter,
        'trackSocial': trackSocial,
        'init': init
    }
})(jQuery, _gaq);

});