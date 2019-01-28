import { CLASSES } from './audioplayer.config';
import { sliceArray } from '../helpers/sliceArray';

class Audioplayer {
    constructor(element) {
        this.elementNode = element;
        this.sourceNode = this.elementNode.querySelector(`.${CLASSES.SOURCE}`);
        this.progressNode = this.elementNode.querySelector(`.${CLASSES.PROGRESS}`);
        this.playBackNodes = {
            current: this.elementNode.querySelector(`.${CLASSES.CURRENT_TIME}`),
            total: this.elementNode.querySelector(`.${CLASSES.TOTAL_TIME}`),
        };
        this.controlNodes = {
            play: this.elementNode.querySelector(`.${CLASSES.PLAY}`),
            restart: this.elementNode.querySelector(`.${CLASSES.RESTART}`),
        };

        this.isPlaying = false;
        this.playBack = {
            current: this.sourceNode.currentTime,
            total: null,
            percentage: 0,
        };

        this.togglePlay = this.togglePlay.bind(this);
        this.updateTime = this.updateTime.bind(this);
        this.handleFinish = this.handleFinish.bind(this);
        this.rewind = this.rewind.bind(this);
        this.restart = this.restart.bind(this);

        this.addEventListeners();
    }

    addEventListeners() {
        this.controlNodes.play.addEventListener('click', this.togglePlay);
        this.sourceNode.addEventListener('loadedmetadata', this.updateTime);
        this.sourceNode.addEventListener('timeupdate', this.updateTime);
        this.sourceNode.addEventListener('ended', this.handleFinish);
        this.progressNode.addEventListener('click', this.rewind);
        this.controlNodes.restart.addEventListener('click', this.restart);
    }

    togglePlay() {
        if (this.isPlaying) {
            this.sourceNode.pause();
            this.elementNode.classList.remove(CLASSES.MODIFIER);
            this.isPlaying = false;
        } else {
            this.sourceNode.play();
            this.elementNode.classList.add(CLASSES.MODIFIER);
            this.isPlaying = true;
        }
    }

    updateTime() {
        this.playBack.current = formatTime(this.sourceNode.currentTime);
        if (this.playBack.total === null) {
            this.playBack.total = formatTime(this.sourceNode.duration);
        }

        Object.entries(this.playBackNodes).forEach(([key, node]) => {
            const elementNode = node;
            elementNode.setAttribute('datetime', this.playBack[key]);
            elementNode.textContent = this.playBack[key];
        });

        this.playBack.percentage = isPercentageOfTotal(this.sourceNode.currentTime, this.sourceNode.duration);
        this.progressNode.value = this.playBack.percentage;
    }

    rewind(e) {
        if (inRange(e, this.progressNode)) {
            const element = this.progressNode;
            const rect = element.getBoundingClientRect();

            // calculate percentage value of the click in relation with the element
            const coefficient = (e.clientX - rect.left) / e.target.clientWidth;

            this.sourceNode.currentTime = this.sourceNode.duration * coefficient;
        }
    }

    restart() {
        this.sourceNode.currentTime = 0;
    }

    handleFinish() {
        this.updateTime();
        this.togglePlay();
    }
}

function inRange(event, element) {
    const rangeBox = element;
    const min = rangeBox.offsetLeft;
    const max = min + rangeBox.offsetWidth;

    return event.clientX >= min && event.clientX <= max;
}

function formatTime(time) {
    const min = Math.floor(time / 60);
    const sec = Math.floor(time % 60);

    return `${min}:${(sec < 10) ? (`0${sec}`) : sec}`;
}

function isPercentageOfTotal(current, total) {
    return Math.floor((current * 100) / total);
}

function initAudioplayers() {
    const AUDIO_PLAYER_NODES = sliceArray(document.querySelectorAll(`.${CLASSES.ELEMENT}`));
    const AUDIO_PLAYERS = [];

    AUDIO_PLAYER_NODES.forEach((element) => {
        const audioplayer = new Audioplayer(element);
        AUDIO_PLAYERS.push(audioplayer);
    });

    return AUDIO_PLAYERS;
}

export { initAudioplayers };
