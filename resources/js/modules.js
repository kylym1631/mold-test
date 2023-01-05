'use strict';
/* 
var timer = new Timer({
    elemId: 'timer', // Str element id,
    format: 'extended', // default - false
    stopwatch: true, // default - false
    continue: false // default - false
});

timer.onStop = function () {
	
}

timer.start(Int interval in seconds);
*/

window.numToWord = function (num, wordsArr) {
    num %= 100;

    if (num > 20) {
        num %= 10;
    }

    switch (num) {
        case 1:
            return wordsArr[0];

        case 2:
        case 3:
        case 4:
            return wordsArr[1];

        default:
            return wordsArr[2];
    }
}

window.Timer = function (options) {
    options = options || {};

    options.continue = (options.continue !== undefined) ? options.continue : false;

    this.opt = options;

    this.elem = document.getElementById(options.elemId);

    this.tickSubscribers = [];

    this.setCookie = function () {
        document.cookie = 'lastTimestampValue-' + options.elemId + '=' + Date.now() + '; expires=' + new Date(Date.now() + 259200000).toUTCString();
    }

    this.onTick = function (fun) {
        if (typeof fun === 'function') {
            this.tickSubscribers.push(fun);
        }
    }

    this.stop = function () {
        clearInterval(this.interval);

        if (this.onStop) {
            setTimeout(this.onStop);
        }
    }

    this.pause = function () {
        clearInterval(this.interval);
    }
}

window.Timer.prototype.output = function (time) {
    let day = time > 86400 ? Math.floor(time / 86400) : 0,
        hour = time > 3600 ? Math.floor(time / 3600) : 0,
        min = time > 60 ? Math.floor(time / 60) : 0,
        sec = time > 60 ? Math.round(time % 60) : time;

    if (hour > 24) {
        hour = hour % 24;
    }

    if (min > 60) {
        min = min % 60;
    }

    let timerOut;

    if (this.opt.format == 'extended') {
        var minTxt = numToWord(min, ['минуту', 'минуты', 'минут']),
            secTxt = numToWord(sec, ['секунду', 'секунды', 'секунд']);

        var minOut = (min != 0) ? min + ' ' + minTxt : '',
            secNum = (sec < 10) ? '0' + sec : sec;

        timerOut = ((min) ? min + ' ' + minTxt + ' ' : '') + '' + sec + ' ' + secTxt;

    } else {
        var minNum = (min < 10) ? '0' + min : min,
            secNum = (sec < 10) ? '0' + sec : sec;

        timerOut = minNum + ':' + secNum;
    }

    if (this.elem) {
        this.elem.innerHTML = timerOut;
    }

    if (this.tickSubscribers.length) {
        this.tickSubscribers.forEach(function (item) {
            item(time, { day, hour, min, sec });
        });
    }
}

window.Timer.prototype.start = function (startTime) {
    this.time = +startTime || 0;

    var lastTimestampValue = ((cookie) => {
        if (this.opt.continue) {
            return false;
        }

        if (cookie) {
            var reg = new RegExp('lastTimestampValue-' + this.opt.elemId + '=(\\d+)', 'i'),
                matchArr = cookie.match(reg);

            return matchArr ? matchArr[1] : null;
        }
    })(document.cookie);

    if (lastTimestampValue) {
        var delta = Math.round((Date.now() - lastTimestampValue) / 1000);

        if (this.opt.stopwatch) {
            this.time += delta;
        } else {
            if (this.time > delta) {
                this.time -= delta;
            } else {
                this.setCookie();
            }
        }

    } else if (this.opt.continue) {
        this.setCookie();
    }

    this.output(this.time);

    if (this.interval !== undefined) {
        clearInterval(this.interval);
    }

    this.interval = setInterval(() => {
        if (this.opt.stopwatch) {
            this.time++;

            this.output(this.time);
        } else {
            this.time--;

            if (this.time <= 0) {
                this.stop();
            } else {
                this.output(this.time);
            }
        }
    }, 1000);
}

/*
const tt = new ToolTip({
    btnSelector: '.js-tooltip',
    notHide: true, // def: false
    clickEvent: true, // def: false
    tipElClass: 'some-class', // def: null
    positionX: 'left' | 'right', // def: 'center'
    positionY: 'bottom', // def: 'top'
    fadeSpeed: 1500 // def: 1000
});

tt.beforeShow = function(btnEl, tooltipDivEl) {
    # code...
}

tt.onShow = function(btnEl, tooltipDivEl) {
    # code...
}

tt.onHide = function() {
    # code...
}
*/

window.ToolTip = function ToolTip(options) {
    this.opt = options || {};

    this.tooltipDiv = null;
    this.tooltipClass = null;
    this.canBeHidden = false;
    this.position = {};
    this.onShow = null;
    this.mO = null;

    this.opt.notHide = (this.opt.notHide !== undefined) ? this.opt.notHide : false;
    this.opt.evClick = (this.opt.clickEvent !== undefined) ? this.opt.clickEvent : false;
    this.opt.tipElClass = (this.opt.tipElClass !== undefined) ? this.opt.tipElClass : null;
    this.opt.fadeSpeed = (this.opt.fadeSpeed !== undefined) ? this.opt.fadeSpeed : 1000;

    this.position.X = (this.opt.positionX !== undefined) ? this.opt.positionX : 'center';
    this.position.Y = (this.opt.positionY !== undefined) ? this.opt.positionY : 'top';

    let mouseOver = (e) => {
        if (this.canBeHidden) {
            if (!e.target.closest(this.opt.btnSelector) && !e.target.closest('.tooltip')) {
                this.hide();

                this.canBeHidden = false;
            }
        } else {
            const elem = e.target.closest(this.opt.btnSelector);

            if (elem) {
                this.show(elem);
            }
        }
    }

    let mouseClick = (e) => {
        const elem = e.target.closest(this.opt.btnSelector);

        if (elem) {
            e.preventDefault();

            this.hide();

            this.canBeHidden = false;

            this.show(elem);
        }
    }

    if (document.ontouchstart !== undefined || this.opt.evClick) {
        document.addEventListener('click', mouseClick);

    } else {
        document.addEventListener('mouseover', mouseOver);

        document.addEventListener('click', (e) => {
            if (e.target.closest(this.opt.btnSelector)) e.preventDefault();
        });
    }

    this.tooltipDiv = document.createElement('div');
    this.tooltipDiv.className = 'tooltip' + (this.opt.tipElClass ? ' ' + this.opt.tipElClass : '');

    document.body.appendChild(this.tooltipDiv);

    document.addEventListener('click', (e) => {
        const closeBtn = e.target.closest('.tooltip__close');

        if (closeBtn || (this.canBeHidden && !e.target.closest('.tooltip'))) {
            this.hide();
        }
    });
}

ToolTip.prototype.show = function (elem) {
    clearTimeout(this.hideTimeout);

    let html = elem.hasAttribute('data-tooltip') ? elem.getAttribute('data-tooltip').replace(/\[(\/?\w+)\]/gi, '<$1>') : '';

    if (this.opt.evClick) html += '<button type="button" class="tooltip__close"></button>';

    this.tooltipDiv.innerHTML = html;

    if (this.beforeShow) {
        this.beforeShow(elem, this.tooltipDiv);
    }

    this.tooltipClass = elem.getAttribute('data-tooltip-class');

    if (this.tooltipClass) {
        this.tooltipDiv.classList.add(this.tooltipClass);
    }



    const bubleStyle = this.tooltipDiv.style,
        elemRect = elem.getBoundingClientRect();

    let coordX,
        coordY,
        posX = this.position.X,
        posY = this.position.Y;

    if (elem.hasAttribute('data-tip-position-x')) {
        posX = elem.getAttribute('data-tip-position-x');
    }

    if (elem.hasAttribute('data-tip-position-y')) {
        posY = elem.getAttribute('data-tip-position-y');
    }

    if (posX == 'center') {
        coordX = (elemRect.left + ((elemRect.right - elemRect.left) / 2)) - (this.tooltipDiv.offsetWidth / 2);
    } else if (posX == 'left') {
        coordX = elemRect.left - this.tooltipDiv.offsetWidth;
    } else if (posX == 'right') {
        coordX = elemRect.right;
    }

    if (posY == 'top') {
        coordY = elemRect.top + window.pageYOffset - this.tooltipDiv.offsetHeight;

    } else if (posY == 'bottom') {
        coordY = elemRect.bottom + window.pageYOffset;
    }

    bubleStyle.left = coordX + 'px';
    bubleStyle.top = coordY + 'px';

    const tipElRect = this.tooltipDiv.getBoundingClientRect();

    if (tipElRect.top < 0) {
        bubleStyle.top = (coordY - tipElRect.top) + 'px';
    }

    if (this.onShow) {
        this.onShow(elem, this.tooltipDiv);
    }

    this.tooltipDiv.style.transition = 'opacity ' + this.opt.fadeSpeed + 'ms';
    this.tooltipDiv.style.opacity = '1';

    setTimeout(() => {
        this.canBeHidden = true;
    }, 21);

    this.mO = this.mouseOut.bind(this);

    if (document.ontouchstart !== undefined) {
        document.addEventListener('touchstart', this.mO);

    } else if (this.opt.evClick) {
        document.addEventListener('wheel', this.mO);
    }
}

ToolTip.prototype.hide = function () {
    if (this.opt.notHide) {
        return;
    }

    this.tooltipDiv.style.opacity = '0';

    this.hideTimeout = setTimeout(() => {
        this.tooltipDiv.removeAttribute('style');
        this.tooltipDiv.innerHTML = '';

        if (this.tooltipClass) {
            this.tooltipDiv.classList.remove(this.tooltipClass);

            this.tooltipClass = null;
        }

        if (this.onHide) {
            this.onHide();
        }
    }, this.opt.fadeSpeed);
}

ToolTip.prototype.mouseOut = function (e) {
    if (this.canBeHidden && !e.target.closest(this.opt.btnSelector) && !e.target.closest('.tooltip')) {
        this.hide();

        this.canBeHidden = false;

        document.removeEventListener('touchstart', this.mO);
        document.removeEventListener('wheel', this.mO);
    }
}