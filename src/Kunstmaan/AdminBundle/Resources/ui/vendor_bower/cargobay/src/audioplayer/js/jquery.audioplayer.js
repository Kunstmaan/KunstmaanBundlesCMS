/* ==========================================================================
   jQuery Audio Player

   Based on http://tympanus.net/codrops/2012/12/04/responsive-touch-friendly-audio-player

   Initialize:
   $('audio').audioPlayer({
       classPrefix: 'audioplayer'
   });

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE 9 and up
   ========================================================================== */

;(function($, window, document, undefined) {

    var isTouch = 'ontouchstart' in window,
	eStart = isTouch ? 'touchstart' : 'mousedown',
	eMove = isTouch ? 'touchmove' : 'mousemove',
	eEnd = isTouch ? 'touchend' : 'mouseup',
	eCancel = isTouch ? 'touchcancel' : 'mouseup',
	secondsToTime, canPlayType;

    secondsToTime = function(secs) {
	if(secs != Infinity) {
	    var hours = Math.floor(secs / 3600),
		minutes = Math.floor(secs % 3600 / 60),
		seconds = Math.ceil(secs % 3600 % 60);

	    return (hours === 0 ? '' : hours > 0 && hours.toString().length < 2 ? '0'+hours+':' : hours+':') + (minutes.toString().length < 2 ? '0'+minutes : minutes) + ':' + (seconds.toString().length < 2 ? '0'+seconds : seconds);
	} else {
	    return ('--:--');
	}
    };

    canPlayType = function(type) {
	var audioElement = document.createElement('audio'),
	    supported = !!(audioElement.canPlayType && audioElement.canPlayType(type + ';' ).replace( /no/, '' ));

	return supported;
    };

    $.fn.audioPlayer = function() {
	var classPrefix = 'audioplayer',
	    cssClass = {},
	    cssClassSub = {
		hideDefault:    '__defaultplayer',
		playPause:      '__playpause',
		playing:        '--playing',
		time:           '__time',
		timeCurrent:    '__time--current',
		timeDuration:   '__time--duration',
		bar:            '__bar',
		barLoaded:      '__bar--loaded',
		barPlayed:      '__bar--played',
		volume:         '__volume',
		volumeButton:   '__volume__button',
		volumeAdjust:   '__volume__adjust',
		noVolume:       '--novolume',
		mute:           '--muted',
		mini:           '--mini'
	    };

	for(var subName in cssClassSub) {
	    if(cssClassSub.hasOwnProperty(subName)) {
		cssClass[subName] = classPrefix + cssClassSub[subName];
	    }
	}

	this.each(function() {
	    if($(this).prop('tagName').toLowerCase() != 'audio') {
		return false;
	    }

	    var $this = $(this),
		isAutoPlay = $this.get(0).getAttribute('autoplay'),
		isLoop = $this.get(0).getAttribute('loop'),
		isSupport = false,
		thePlayer, theAudio;

	    isAutoPlay = isAutoPlay === '' || isAutoPlay === 'autoplay' ? true : false;
	    isLoop = isLoop === '' || isLoop === 'loop' ? true : false;

	    $this.find('source').each(function() {
		var audioFile = $(this).attr('src'),
		    audioFileType = $(this).attr('type');

		if(typeof audioFile !== 'undefined' && canPlayType(audioFileType)) {
		    isSupport = true;
		    return false;
		}
	    });

	    if(isSupport) {
		thePlayer = $('<div class="' + classPrefix + '">' + $('<div>').append($this.eq(0).clone()).html() + '<button type="button" class="' + cssClass.playPause + '"><i class="icon--play audioplayer__playpause__icon-play"></i><i class="icon--pause audioplayer__playpause__icon-pause"></i></button></div>');
		theAudio  = thePlayer.find('audio');

		theAudio = theAudio.get(0);

		thePlayer.find('audio').addClass(cssClass.hideDefault);
		thePlayer.append( '<div class="' + cssClass.time + ' ' + cssClass.timeCurrent + '"></div><div class="' + cssClass.bar + '"><div class="' + cssClass.barLoaded + '"></div><div class="' + cssClass.barPlayed + '"></div></div><div class="' + cssClass.time + ' ' + cssClass.timeDuration + '"></div><div class="' + cssClass.volume + '"><button type="button" class="' + cssClass.volumeButton + '"><i class="icon--volume-medium audioplayer__volume__button__icon-volume-up"></i><i class="icon--volume-mute audioplayer__volume__button__icon-volume-off"></i></button><div class="' + cssClass.volumeAdjust + '"><div class="audioplayer__volume__adjust__control"><div class="audioplayer__volume__adjust__control__state"></div></div></div></div>' );

		var theBar = thePlayer.find('.' + cssClass.bar),
		    barPlayed = thePlayer.find('.' + cssClass.barPlayed),
		    barLoaded = thePlayer.find('.' + cssClass.barLoaded),
		    timeCurrent = thePlayer.find('.' + cssClass.timeCurrent),
		    timeDuration = thePlayer.find('.' + cssClass.timeDuration),
		    volumeButton = thePlayer.find('.' + cssClass.volumeButton),
		    volumeAdjuster = thePlayer.find('.' + cssClass.volumeAdjust + ' > div'),
		    volumeDefault = 0,
		    adjustCurrentTime, adjustVolume, updateLoadBar;

		adjustCurrentTime = function(e) {
		    theRealEvent = isTouch ? e.originalEvent.touches[0] : e;
		    theAudio.currentTime = Math.round((theAudio.duration * (theRealEvent.pageX - theBar.offset().left)) / theBar.width());
		};

		adjustVolume = function(e) {
		    theRealEvent = isTouch ? e.originalEvent.touches[0] : e;
		    theAudio.volume = Math.abs((theRealEvent.pageY - (volumeAdjuster.offset().top + volumeAdjuster.height())) / volumeAdjuster.height());
		};

		updateLoadBar = setInterval(function() {
		    if(theAudio.buffered.length > 0) {
			if(theAudio.duration > 0) {
			    barLoaded.width((theAudio.buffered.end(0) / theAudio.duration) * 100 + '%');
			}
			if(theAudio.buffered.end(0) >= theAudio.duration) {
			    clearInterval(updateLoadBar);
			}
		    }
		}, 100);

		var volumeTestDefault = theAudio.volume,
		    volumeTestValue = theAudio.volume = 0.111;

		if( Math.round(theAudio.volume * 1000) / 1000 == volumeTestValue) {
		    theAudio.volume = volumeTestDefault;
		} else {
		    thePlayer.addClass(cssClass.noVolume);
		}

		timeDuration.html('loading');
		timeCurrent.text(secondsToTime(0));

		theAudio.addEventListener('loadedmetadata', function() {
		    timeDuration.text( secondsToTime(theAudio.duration));
		    volumeAdjuster.find('div').height(theAudio.volume * 100 + '%');
		    volumeDefault = theAudio.volume;
		});

		theAudio.addEventListener('timeupdate', function() {
		    timeCurrent.text(secondsToTime(theAudio.currentTime));
		    barPlayed.width((theAudio.currentTime / theAudio.duration) * 100 + '%');
		});

		theAudio.addEventListener('volumechange', function() {
		    volumeAdjuster.find('div').height(theAudio.volume * 100 + '%');
		    if( theAudio.volume > 0 && thePlayer.hasClass(cssClass.mute)) {
			thePlayer.removeClass(cssClass.mute);
		    }
		    if( theAudio.volume <= 0 && !thePlayer.hasClass(cssClass.mute )) {
			thePlayer.addClass( cssClass.mute);
		    }
		});

		theAudio.addEventListener('ended', function() {
		    thePlayer.removeClass( cssClass.playing );
		});

		theBar.on(eStart, function(e) {
		    adjustCurrentTime(e);
		    theBar.on(eMove, function(e) {
			adjustCurrentTime(e);
		    });
		}).on(eCancel, function() {
		    theBar.unbind(eMove);
		});

		volumeButton.on('click', function() {
		    if(thePlayer.hasClass(cssClass.mute)) {
			thePlayer.removeClass(cssClass.mute);
			theAudio.volume = volumeDefault;
		    } else {
			thePlayer.addClass(cssClass.mute);
			volumeDefault = theAudio.volume;
			theAudio.volume = 0;
		    }
		    return false;
		});

		volumeAdjuster.on(eStart, function(e) {
		    adjustVolume(e);
		    volumeAdjuster.on(eMove, function(e) {
			adjustVolume(e);
		    });
		}).on(eCancel, function() {
		    volumeAdjuster.unbind(eMove);
		});
	    } else {
		thePlayer = $('<div class="' + classPrefix + '">' + '<embed src="' + audioFile + '" width="0" height="0" volume="100" autostart="' + isAutoPlay.toString() +'" loop="' + isLoop.toString() + '" />' + '<button type="button" class="' + cssClass.playPause + '"></button></div>');
		theAudio  = thePlayer.find('embed');

		theAudio = theAudio.get(0);

		thePlayer.addClass(cssClass.mini);
	    }

	    if(isAutoPlay) {
		thePlayer.addClass(cssClass.playing);
	    }

	    thePlayer.find('.' + cssClass.playPause).on('click', function() {
		if(thePlayer.hasClass(cssClass.playing )) {
		    thePlayer.removeClass(cssClass.playing);
		    if (isSupport) {
			theAudio.pause();
		    } else {
			theAudio.Stop();
		    }
		} else {
		    thePlayer.addClass(cssClass.playing);
		    if (isSupport) {
			theAudio.play();
		    } else {
			theAudio.Play();
		    }
		}
		return false;
	    });

	    $this.replaceWith(thePlayer);
	});
	return this;
    };
})(jQuery, window, document);
