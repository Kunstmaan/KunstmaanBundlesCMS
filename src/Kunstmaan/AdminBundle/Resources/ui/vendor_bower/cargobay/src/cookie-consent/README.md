## Cargo Bay Cookie Consent

### General
- Cookiebar
- jQuery and vanilla-js version

### Required includes
- **Javascript**
    - jquery.cookie-consent.js or vanilla.cookie-consent.js
- **Styles**
    - cookie-consent.scss



### Dependencies
- jQuery (for jQuery version)



### Initialise
```javascript
cargobay.cookieconsent.init();
```



###  Basic html structure
```html
    <div id="cookie-bar" class="cookie-bar">
	<p>
	    Deze site gebruikt cookies om uw surfervaring op deze website gemakkelijker te maken. Indien u meer informatie wenst kan u <a href="#" class="cookie-bar__policy">hier ons cookiebeleid lezen</a>.
	    <button type="button" id="cookie-bar__consent-btn" class="btn btn-warning cookie-bar__btn">Doorgaan</button>
	</p>
    </div>
```



### Available scss variables
```scss
//General
$cookie-bar-position-top:                   auto!default;
$cookie-bar-position-right:                 auto!default;
$cookie-bar-position-bottom:                0!default;
$cookie-bar-position-left:                  0!default;
$cookie-bar-color:                          rgba(86,86,86,0.95)!default;
$cookie-bar-text-align:                     center!default;
$cookie-bar-width:                          100%!default;
$cookie-bar-padding:                        5px 10px 0px 10px!default;
$cookie-bar-font-color:                     #fff!default;
$cookie-bar-font-family:                    Helvetica, Arial, sans-serif!default;
$cookie-bar-font-size:                      13px!default;
$cookie-bar-line-height:                    1.7!default;

//Policy link
$cookie-bar-policy-link-color:              #fff!default;
$cookie-bar-policy-link-text-decoration:    underline!default;

//Button
$cookie-bar-button-margin:                  0 0 0 5px!default;
```



### Support
- Latest Chrome
- Latest FireFox
- Latest Safari
- IE9 (IE10 for vanilla version) and up
