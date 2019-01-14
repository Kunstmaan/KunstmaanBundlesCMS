function videolink() {
    Array.prototype.slice.call(document.querySelectorAll('.js-videolink-placeholder')).forEach((placeholder) => {
        const provider = placeholder.parentNode.getAttribute('data-video-provider');
        const id = placeholder.parentNode.getAttribute('data-video-id');

        if (provider && id) {
            setVideoPlaceholder(provider, id, placeholder);
        }
    });

    Array.prototype.slice.call(document.querySelectorAll('.js-videolink-play-link')).forEach((video) => {
        video.addEventListener('click', (event) => {
            event.preventDefault();

            const provider = video.getAttribute('data-video-provider');
            const id = video.getAttribute('data-video-id');
            const videoContainer = video.parentNode.getElementsByClassName('js-videolink-container')[0];
            const template = createTemplate(provider, id);

            // Append the iframe to the video container
            video.parentNode.querySelector('.videolink__video-link')
                .classList.add('videolink__video-link--hidden');
            videoContainer.appendChild(template);
        }, false);
    });
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
        template.style.width = '100%';
        template.style.height = '100%';
    } else {
        template = '<p>Sorry, this provider is not supported yet.</p>';
    }

    return template;
}

function setVideoPlaceholder(provider, id, element) {
    if (provider === 'youtube') {
        // eslint-disable-next-line no-param-reassign
        element.src = `//img.youtube.com/vi/${id}/maxresdefault.jpg`;
    } else {
        const request = new XMLHttpRequest();
        let requestUrl;
        let response;
        let imgUrl;

        if (provider === 'vimeo') {
            requestUrl = `http://vimeo.com/api/v2/video/${id}.json`;
        } else if (provider === 'dailymotion') {
            requestUrl = `https://api.dailymotion.com/video/${id}?fields=thumbnail_720_url`;
        }

        request.open('GET', requestUrl, true);

        request.onload = () => {
            if (request.status >= 200 && request.status < 400) {
                response = JSON.parse(request.responseText);

                if (provider === 'vimeo') {
                    imgUrl = response[0].thumbnail_large;
                } else if (provider === 'dailymotion') {
                    imgUrl = response.thumbnail_720_url;
                }

                // eslint-disable-next-line no-param-reassign
                element.src = imgUrl;
            }
        };

        request.send();
    }
}

export default videolink;
