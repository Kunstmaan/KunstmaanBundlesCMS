import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import WordCount from '@ckeditor/ckeditor5-word-count/src/wordcount';

export const defaultConfig = {
    plugins: [Essentials, Paragraph, Bold, Italic, Heading, Link, List, BlockQuote, WordCount],
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        ],
    },
    wordCount: {
        onUpdate: (stats) => {
            console.log(`Characters: ${stats.characters}\nWords: ${stats.words}`);
        },
    },
};

window.kunstmaanbundles = {
    ...window.kunstmaanbundles,
    CKEDITOR_CONFIGS: {
        ...window.kunstmaanbundles.CKEDITOR_CONFIGS,
        default: defaultConfig,
    },
};
