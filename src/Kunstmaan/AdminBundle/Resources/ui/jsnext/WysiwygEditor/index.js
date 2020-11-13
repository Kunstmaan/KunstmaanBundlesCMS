import '@ckeditor/ckeditor5-build-classic/build/translations/nl'; // ?
import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import { defaultConfig } from './config';
import { wordCountConfig } from './wordCount';

const SELECTOR = '.js-wysiwyg-editor';
const editors = new Map();

export const initWysiwygEditors = ({ container = window.document } = {}) => {
    if (container) {
        const editorElements = [...container.querySelectorAll(SELECTOR)];

        editorElements.forEach((editorElement) => {
            const editor = editors.get(editorElement);
            // Don't create if already created
            if (!editor) {
                const configName = editorElement.getAttribute('data-editor-mode');

                const onWindowLoadedHandler = () => {
                    if (isConfigDefined(configName)) {
                        createEditor({
                            elementToReplace: editorElement,
                            config: window.kunstmaanbundles.CKEDITOR_CONFIGS[configName],
                        });
                    } else {
                        createEditor({ elementToReplace: editorElement });
                        console.warn(`No CKEditor config found with name "${configName}". The default config is used.`);
                    }
                    window.removeEventListener('load', onWindowLoadedHandler);
                };

                if (!configName) {
                    createEditor({ elementToReplace: editorElement });
                } else if (isConfigDefined(configName)) {
                    createEditor({
                        elementToReplace: editorElement,
                        config: window.kunstmaanbundles.CKEDITOR_CONFIGS[configName],
                    });
                } else {
                    // If the custom ckeditor config isn't find initially, wait for
                    // the window (all js resources) to be loaded and check again.
                    window.addEventListener('load', onWindowLoadedHandler);
                }
            }
        });
    }
};

function isConfigDefined(configName) {
    return window.kunstmaanbundles.CKEDITOR_CONFIGS && window.kunstmaanbundles.CKEDITOR_CONFIGS[configName];
}

function createEditor({ elementToReplace, config = defaultConfig } = {}) {
    const editorConfig = { ...config };
    const maxLength = elementToReplace.getAttribute('maxlength')
        && parseInt(elementToReplace.getAttribute('maxlength'), 10);

    if (maxLength) {
        editorConfig.wordCount = wordCountConfig({ elementId: elementToReplace.id, maxCharacters: maxLength });
    }

    ClassicEditor.create(elementToReplace, editorConfig).then((editor) => {
        editors.set(elementToReplace, editor);
    }).catch((error) => console.error(error.stack));
}

const reInit = initWysiwygEditors;

// Make it available in the old js
window.kunstmaanbundles = {
    ...window.kunstmaanbundles,
    wysiwygEditor: { reInit },
};
