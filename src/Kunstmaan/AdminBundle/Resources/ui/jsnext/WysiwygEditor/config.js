import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';

export const defaultConfig = {
    plugins: [Essentials, Paragraph, Bold, Italic, Link, List, BlockQuote],
    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
};

window.kunstmaanbundles = {
    ...window.kunstmaanbundles,
    CKEDITOR_CONFIGS: {
        ...window.kunstmaanbundles.CKEDITOR_CONFIGS,
        default: defaultConfig,
    },
};
