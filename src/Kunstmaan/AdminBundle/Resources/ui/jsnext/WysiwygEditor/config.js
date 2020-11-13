import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import WordCount from '@ckeditor/ckeditor5-word-count/src/wordcount';

import BundlesLink from './Plugins/Link';

export const defaultConfig = {
    plugins: [
        Essentials, Paragraph, Bold, Italic, Underline, Link, BundlesLink, List, BlockQuote, Alignment, WordCount,
    ],
    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
    link: {
        decorators: {
            qfefqopenInNewTab: {
                mode: 'manual',
                label: 'Open in a new tab',
                defaultValue: false,
                attributes: {
                    target: '_blank',
                    rel: 'noopener noreferrer',
                },
            },
        },
    },
    language: {
        // The UI will be English.
        ui: 'nl',

        // But the content will be edited in Arabic.
        content: 'nl',
    },
};

window.kunstmaanbundles = {
    ...window.kunstmaanbundles,
    CKEDITOR_CONFIGS: {
        ...window.kunstmaanbundles.CKEDITOR_CONFIGS,
        default: defaultConfig,
    },
};
