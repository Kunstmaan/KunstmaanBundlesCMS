<b>Description:</b>
<br>
<br>
<b>Acceptable use:</b>

```html
@example
<div class="audio-player js-audio-player">
    <audio src="/public/frontend/styleguide/assets/audio/Jolene at 33rpm.mp3" class="audio-player__source js-audio-player-source" type="audio/mpeg" controls></audio>

    <button class="js-audio-player-play audio-player__toggle-play icon-btn btn--eye-catcher icon-btn--lg">
        <svg class="icon-btn__icon audio-player__toggle-play__play"><use xlink:href="../icons/symbol-defs.svg#icon--play"></use></svg>
        <svg class="icon-btn__icon audio-player__toggle-play__pause"><use xlink:href="../icons/symbol-defs.svg#icon--pause"></use></svg>
    </button>
    <div class="audio-player__data">
        <h5 class="audio-player__title">Title audioitem</h5>
        <div class="audio-player__progress-wrapper">
            <progress class="audio-player__progress js-audio-player-progress" value="0" max="100"></progress>
            <div class="audio-player__playback">
                <time class="js-audio-player-time-current" datetime="00:00"></time>
                <time class="js-audio-player-time-total" datetime="00:00"></time>
            </div>
        </div>
    </div>
    <button class="audio-player__restart js-audio-player-restart">
        <svg><use xlink:href="../icons/symbol-defs.svg#icon--rewind"></use></svg>
    </button>
</div>
```
