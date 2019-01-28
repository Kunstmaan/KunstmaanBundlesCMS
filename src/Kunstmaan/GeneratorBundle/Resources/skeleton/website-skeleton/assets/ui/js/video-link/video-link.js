import { sliceArray } from '../helpers/sliceArray';
import { CLASSES } from './video-link.config';

export function videoLink() {
    const HOOKS = sliceArray(document.querySelectorAll(CLASSES.HOOK));

    handlePlaceholders();

    HOOKS.forEach((el) => {
        el.addEventListener('click', clickHandler, false);
    });
}

function clickHandler(e) {
    e.preventDefault();

    const el = e.currentTarget;

    const provider = el.getAttribute('data-video-provider');
    const id = el.getAttribute('data-video-id');
    const videoContainer = el.parentNode.querySelector(CLASSES.CONTAINER);
    const template = createTemplate(provider, id);

    el.classList.add(CLASSES.MODIFIER);
    videoContainer.appendChild(template);
}

function createTemplate(provider, id) {
    let template;
    let url;

    switch (provider) {
        case 'youtube':
            url = `//www.youtube.com/embed/${id}?title=0&amp;byline=0&amp;portrait=0;&amp;badge=0&amp;autoplay=1`;
            break;

        case 'vimeo':
            url = `//player.vimeo.com/video/${id}?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autoplay=1`;
            break;

        case 'dailymotion':
            url = `//www.dailymotion.com/embed/video/${id}?autoplay=1`;
            break;

        default:
            url = '';
            break;
    }

    if (url !== '') {
        template = document.createElement('iframe');
        template.setAttribute('src', url);
        template.setAttribute('webkitallowfullscreen', 'true');
        template.setAttribute('mozallowfullscreen', 'true');
        template.setAttribute('allowfullscreen', 'true');
        template.frameBorder = 0;
        template.style.width = `${100}%`;
        template.style.height = `${100}%`;
    } else {
        template = '<p>Sorry, this provider is not supported yet.</p>';
    }

    return template;
}

function handlePlaceholders() {
    const PLACEHOLDERS = sliceArray(document.querySelectorAll(CLASSES.PLACEHOLDER));

    PLACEHOLDERS.forEach((el) => {
        const provider = el.parentNode.getAttribute('data-video-provider');
        const id = el.parentNode.getAttribute('data-video-id');

        setVideoPlaceholder(provider, id, el);
    });
}

function setVideoPlaceholder(provider, id, el) {
    const placeHolder = el;

    if (provider === 'youtube') {
        placeHolder.src = `//img.youtube.com/vi/${id}/maxresdefault.jpg`;
    } else {
        const xhr = new XMLHttpRequest();
        let url;
        let json;
        let imgUrl;

        if (provider === 'vimeo') {
            url = `http://vimeo.com/api/v2/video/${id}.json`;
        } else if (provider === 'dailymotion') {
            url = `https://api.dailymotion.com/video/${id}?fields=thumbnail_720_url`;
        }

        xhr.open('GET', url, true);
        xhr.send();

        xhr.onreadystatechange = () => {
            const finished = xhr.readyState === 4;
            const ok = xhr.status === 200;

            if (finished && ok) {
                json = JSON.parse(xhr.responseText);

                if (provider === 'vimeo') {
                    imgUrl = json[0].thumbnail_large;
                } else if (provider === 'dailymotion') {
                    imgUrl = json.thumbnail_720_url;
                }

                placeHolder.src = imgUrl;
            }
        };
    }
}
