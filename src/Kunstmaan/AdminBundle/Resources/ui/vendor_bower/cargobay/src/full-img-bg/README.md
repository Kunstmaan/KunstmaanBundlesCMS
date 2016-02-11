## Cargo Bay Full Image Background

### General
CSS3 Full Image background with a fallback ([jquery-backstretch](https://github.com/srobbin/jquery-backstretch, 'jquery-backstretch')) for older browsers.
The image is placed inline for content-input of the image trough the CMS.

Default, `background-size:cover` will be used. For browsers that don't support this feature, Modernizr will deliver the fallback scripts.



### Dependencies
- [jQuery-backstretch](https://github.com/srobbin/jquery-backstretch)
- [Modernizr.js](http://modernizr.com/)
- [jQuery](http://jquery.com/)



### Usage
The class **.full-img-bg** is used as a javascript hook.
Via the **data-backstretch-img** attribute we set the background image for the fallback.

Make sure that when you build your Modernizr you have the *Modernizr.load()* property enabled. This is used to load the fallback javascript files.



#### On the Body tag
```html
    <body class="full-img-bg" style="background-image: url(imgUrl);" data-backstretch-img="imgUrl">
	...
    </body>
```
#### On a block level element
```html
    <div class="full-img-bg" style="background-image: url(imgUrl);" data-backstretch-img="imgUrl">
	...
    </div>
```



#### Fallback
##### Initialise
```javascript
Modernizr.load({
    test: Modernizr.backgroundsize,
    nope: ['http://cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.4/jquery.backstretch.min.js', '../js/full-img-bg.js'],
    callback: function(url, result, key) {
	if(key == 1) {
	    cargobay.backstretch.init();
	}
    }
});
```
