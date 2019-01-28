import 'bootstrap.native/dist/polyfill';
import bsn from 'bootstrap.native';

export default class Scrollspy {

    constructor() {
        // Add scrollspy to subnav
        new bsn.ScrollSpy(document.getElementById('spy-content'), {
            target: document.getElementById('subnav')
        });
    }
}
