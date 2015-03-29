/* ==========================================================================
   Videolink

   Initialize:
   cargobay.videolink.init();

   Dependencies:
   jQuery
   Modernizr.csstransitions (check css-transitions)
   fitVids (when you want make the video's fluid)

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE 9 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.videolink = (function($, window, undefined) {

    var init;

    init = function() {
        $('.js-videolink-play-link').on('click', function(e) {
            e.preventDefault();

            var $this = $(this),
                provider = $this.data('video-provider'),
                id = $this.data('video-id'),
                makeFluid = $this.data('make-fluid'),
                $videoContainer = $this.next('.js-videolink-container'),
                template;

            // Construct template
            if(provider === 'youtube') {
                template = '<iframe src="//www.youtube.com/embed/' + id + '?autoplay=1&amp;rel=0&amp;showinfo=0&amp;modestbranding&amp;wmode=transparent" width="560" height="315" frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            } else if (provider === 'vimeo') {
                template = '<iframe src="//player.vimeo.com/video/' + id + '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autoplay=1" width="500" height="290" frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            } else {
                template = '<p>Sorry, this provider is not supported yet.</p>';
            }

            // Append template
            $videoContainer.html(template);

            // Make iframe fluid
            if(makeFluid) {
                $videoContainer.fitVids();
            }

            // Hide link
            if(Modernizr.csstransitions) {
                $this.one('transitionend webkitTransitionEnd', function() {
                    $this.addClass('videolink__video-link--hidden');
                });
                $this.addClass('videolink__video-link--hide');
            } else {
                $this.addClass('videolink__video-link--hidden');
            }
        });
    };

    return {
        init: init
    };

}(jQuery, window));
