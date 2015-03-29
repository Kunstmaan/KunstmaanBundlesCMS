## Cargo Bay Audio player

### General
- Responsive audioplayer that uses html5 audio.
- Uses embed as fallback (includes check on type).
- JQuery only.
- Based on [http://tympanus.net/codrops/2012/12/04/responsive-touch-friendly-audio-player](http://tympanus.net/codrops/2012/12/04/responsive-touch-friendly-audio-player)


### Required includes
- **Javascript**
	- jQuery
	- jquery.audioplayer.js
- **Styles**
	- audioplayer.scss


### Dependencies
- jQuery
- Font-awesome (icons)


### Initialise
```javascript
$('.js-audio-player').audioPlayer();
```

### Usage
The class **js-audio-player** is used as javascript hook.
Don't forget to specify the type, as it uses this for the support-check.

```html
<audio controls preload="metadata" class="js-audio-player">
	<source src="test.mp3" type="audio/mp3"/>
    <source src="test.ogg" type="audio/ogg"/>
    <source src="test.wav" type="audio/wav"/>
</audio>
```
### Available scss variables
```scss
// General
$audioplayer-height:                            2.5em!default;
$audioplayer-background:                        #1D3037!default;

$audioplayer-max-width:                         30em!default;
$audioplayer-breakpoint-mobile-version:         30em!default;


// Play-Pause
$audioplayer-playpause-width:                   2.5em!default;
$audioplayer-playpause-height:                  100%!default;
$audioplayer-playpause-color:                   #fff!default;
$audioplayer-playpause-background:              #0bd789!default;

$audioplayer-playpause-playing-background:      #000!default;
$audioplayer-playpause-playing-color:           #fff!default;


// Bar
$audioplayer-bar-background:                    #333!default;
$audioplayer-bar-loaded-background:             #000!default;
$audioplayer-bar-played-background:             #0bd789!default;


// Time
$audioplayer-time-font-size:                    .7em!default;
$audioplayer-time-color:                        #fff!default;
$audioplayer-time-mobile-color:                 #000!default;


// Volume
$audioplayer-volume-width:                      2.5em!default;
$audioplayer-volume-height:                     100%!default;

$audioplayer-volume-button-background:          #0bd789!default;
$audioplayer-volume-button-color:               #fff!default;

$audioplayer-volume-adjust-height:              6.25em!default;
$audioplayer-volume-adjust-background:          #1D3037!default;

$audioplayer-volume-adjust-control-background:        #000!default;
$audioplayer-volume-adjust-control-state-background:  #0bd789!default;
```


### Support

- Latest Chrome
- Latest FireFox
- Latest Safari
- IE 9 and up
