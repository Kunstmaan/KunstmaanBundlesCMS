// This can be used to import extra polyfills that aren't in babel-polyfill already.

(function() {
    if (!Object.entries) {

        Object.entries = function( obj ){
          var ownProps = Object.keys( obj ),
              i = ownProps.length,
              resArray = new Array(i); // preallocate the Array
          while (i--)
            resArray[i] = [ownProps[i], obj[ownProps[i]]];

          return resArray;
        };
      }
})();

(function () {

    if ( typeof window.CustomEvent === "function" ) return false;

    function CustomEvent ( event, params ) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();
