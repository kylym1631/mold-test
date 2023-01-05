'use strict';
import { Fancybox } from "@fancyapps/ui";

let activity = 0;
let tasksCount = null;

$(function () {
    $('body').on('click', '.js-view-doc, .js-view-doc-wrap a', function () {
        const href = $(this).attr('href');
        const ext = $(this).data('ext') || href.split('.').pop();

        if (ext.includes('pdf')) {
            $('.js-modal-view-doc .iframe').html('<iframe src="' + href + '" frameborder="0"></iframe>');
            $('.js-modal-view-doc').modal('show');
        } else {
            $('.js-modal-view-image .image').html('<img src="' + href + '" alt="img">');
            $('.js-modal-view-image').modal('show');
        }

        return false;
    });

    if ($('a[href*="tasks"]').length) {
        ping(true);

        setInterval(function () {
            ping();
        }, 60000);
    }

    if ($('#login-timer').length) {
        const timer = new Timer({
            elemId: 'login-timer',
        });

        timer.onStop = function () {
            // window.location.replace('/logout');
            console.log('logout!!!!!!!');
        }

        timer.start(3600);

        $(document).on('click', function () {
            activity++;
            timer.start(3600);
        }).on('keypress', function () {
            activity++;
            timer.start(3600);

        });
    }

    // $('body').on('click', '.js-show-comment', function () {
    //     alert($(this).attr('data-content'));
    // });

    new ToolTip({
        btnSelector: '.js-show-comment',
        clickEvent: true,
        positionX: 'left',
        fadeSpeed: 300,
    });

    // add select all to select2
    $("body").on('click', '.js-select-all-for-select2', function () {
        const $sel = $(this).parent().prev();
        if ($sel.attr('data-ajax-opt')) {
            $sel.select2('open');
            setTimeout(() => {
                $sel.find("option").prop("selected", "selected");
                $sel.trigger("change");
            }, 1500);
        } else {
            $sel.find("option").prop("selected", "selected");
            $sel.trigger("change");
        }
    });

    // $('.select2.select2-container').each(function () {
    //     $(this).append('<button type="button" class="js-select-all-for-select2">a</button>');
    // });
});



window.ping = function ping(dontRefreshTasksTbl) {
    var data = {};
    data._token = $('input[name=_token]').val();
    data.activity = activity;

    $.ajax({
        url: routes.ping,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (res) {
            activity = 0;

            if (res.tasksCount > 0) {
                $('#countTask').show().html(res.tasksCount);
            } else {
                $('#countTask').hide();
            }

            if (tasksCount === 0 && res.tasksCount === 0) { } else {
                if (window.tasksOTable && dontRefreshTasksTbl !== true) {
                    tasksOTable.draw();
                }
            }

            tasksCount = res.tasksCount;

            if (res.logout) {
                window.location.replace('/logout');
            }
        }
    });
}

Fancybox.bind('a.js-fancybox', {
    groupAll: true,
});

// Local Storage
window.LocStor = {
    set: function (prop, val) {
        window.localStorage.setItem(prop, val);
    },

    get: function (prop) {
        const val = window.localStorage.getItem(prop);

        return (val !== null) ? val : false;
    }
};

// Check element for hidden
window.elemIsHidden = function (elem, exclude) {
    exclude = exclude || [];

    while (elem) {
        if (!elem) break;

        const compStyle = getComputedStyle(elem);

        if (
            compStyle.display == 'none' ||
            compStyle.visibility == 'hidden' ||
            (!exclude.includes('opacity') && compStyle.opacity == '0')
        ) return true;

        elem = elem.parentElement;
    }

    return false;
}