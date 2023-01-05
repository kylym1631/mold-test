'use strict';

$(() => {

    $('body').on('change', '.js-choose-template', function () {
        let link = '/templates/document-preview' + window.location.search;
        const tplIds = [];

        $('.js-choose-template:checked').each(function () {
            const val = $(this).val();
            tplIds.push(val);
        });

        link += '&' + tplIds.map(id => {
            return 'tpl_id[]=' + id;
        }).join('&');

        if (tplIds.length) {
            $('.js-generate-docs-link').show().attr('href', encodeURI(link));
        } else {
            $('.js-generate-docs-link').hide();
        }
    });

});