## Cargo Bay Videolink

### General
- Support for Youtube and Vimeo
- Replaces image with video (lazy-load video's)



### Required includes
- **Javascript**
    - jquery.videolink.js
- **Styles**
    - videolink.scss



### Dependencies
- jQuery
- Modernizr.csstransitions (check css-transitions)
- FitVids.js (if you want make the video's fluid - [fitvidsjs.com](http://fitvidsjs.com/))



### Initialise
```javascript
cargobay.videolink.init();
```



### Usage
The class **.js-videolink-play-link** is used as javascript hook to identify a video link.

With the **data-video-provider** attribute, you can identify where the video is hosted, and what kind of embed code should be used.
Available values are " *youtube* " and " *vimeo* "

The **data-video-id** attribute is used by the javascript to load the requested video.

With the **data-make-fluid** attribute, you can make the embedded videos responsive.
Available values are " *true* " or " *false* "

**Don't forget to include fitVids if you want to use this feature! [fitvidsjs.com](http://fitvidsjs.com/)**

```html
<div class="videolink">
    <a href="//www.youtube.com/embed/C9OfBcjyxKY" target="_blank" class="js-videolink-play-link videolink__video-link" data-video-provider="youtube" data-video-id="C9OfBcjyxKY" data-make-fluid="true">
	<img src="img/videolink.jpg" alt="videolink-image" class="videolink__video-link__image" />
    </a>
    <div class="js-videolink-container videolink__video-container"></div>
</div>
```



### Available scss variables
```scss
$videolink-background:          #000;
```



### Support
#### JQuery version
- Latest Chrome
- Latest FireFox
- Latest Safari
- IE 9 and up
