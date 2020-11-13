import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import LinkUI from '@ckeditor/ckeditor5-link/src/linkui';

const POPUP_OPTIONS = `location=no,titlebar=no,menubar=no,toolbar=no,dependent=yes,
    minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes`;

export default class Link extends Plugin {
    init() {
        const { editor } = this;
        const linkUI = editor.plugins.get(LinkUI);

        this.fileBrowseUrl = document.body && document.body.getAttribute('data-file-browse-url');
        this.linkFormView = linkUI.formView;
        this.button = this.createButton();

        if (this.fileBrowseUrl) {
            this.linkFormView.once('render', () => {
                // Render button's tamplate.
                this.button.render();

                // Register the button under the link form view, it will handle its destruction.
                this.linkFormView.registerChild(this.button);

                // Create wrapper div for styling purposes
                const newDiv = document.createElement('div');
                newDiv.classList.add('ck', 'ck-list');
                newDiv.appendChild(this.button.element);
                // Inject the element into DOM.
                this.linkFormView.element.insertBefore(newDiv, this.linkFormView.saveButtonView.element);
            });
        }
    }

    createButton() {
        const { editor } = this;
        const button = new ButtonView(this.locale);
        const linkCommand = editor.commands.get('link');
        let popup = null;

        button.set({
            class: 'internal-link-btn',
            label: 'Internal link',
            withText: true,
            tooltip: true,
        });

        // Probably this button should be also disabled when the link command is disabled.
        button.bind('isEnabled').to(linkCommand);

        button.on('execute', () => {
            const { linkFormView } = this;

            if (popup === null || popup.closed) {
                popup = window.open(
                    `${this.fileBrowseUrl}?CKEditorFuncNum=1`,
                    null,
                    getPopupOptions(),
                    true,
                );

                window.addEventListener('message', onMessageEvent, false);
                popup.onbeforeunload = () => {
                    // onbeforeunload also fires when change page, so we need to check the closed property
                    // on on each occurance before we remove the eventListener
                    setTimeout(() => {
                        if (popup.closed) {
                            console.log('remove event listener');
                            window.removeEventListener('message', onMessageEvent);
                        }
                    }, 500);
                };
            } else {
                // the window reference must exist and the window is not closed;
                // therefore, we can bring it back on top of any other window
                // with the focus() method. There would be no need to re-create
                // the window or to reload the referenced resource.
                popup.focus();
            }

            function onMessageEvent(event) {
                // Check event.origin to verify the targetOrigin matches this window's domain
                if (event.origin !== window.location.origin) return;

                if (event.data && event.data.itemUrl) {
                    linkFormView.urlInputView.fieldView.element.value = event.data.itemUrl;
                    linkFormView.fire('submit');
                }

                popup.close();
            }
        });

        return button;
    }
}

function getPopupOptions() {
    let width = 970;
    let height = parseInt((window.screen.height * parseInt('70%', 10)) / 100, 10);

    if (width < 640) width = 640;

    if (height < 420) height = 420;

    const top = parseInt((window.screen.height - height) / 2, 10);
    const left = parseInt((window.screen.width - width) / 2, 10);

    return `${POPUP_OPTIONS},width=${width},height=${height},top=${top},left=${left}`;
}
