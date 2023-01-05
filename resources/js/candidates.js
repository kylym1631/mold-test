'use strict';

$(function () {
    if (
        $('#statuses-count').length
        || $('#statuses-count-koordinator').length
        || $('#statuses-count-recruiter').length
    ) {
        $('body').on('change', 'select[class*="changeActivation"]', function () {
            setTimeout(() => {
                getStatusesCount();
            }, 2000);
        });

        getStatusesCount();
    }

    if (
        $('#statistics-bar').length
        || $('#statistics-bar-koordinator').length
        || $('#statistics-bar-recruiter').length
    ) {
        statisticsBar();
    }

    if ($('#recruiters-rating').length) {
        ratingBar();
    }

    $('body').on('click', '.delete-candidate-btn', function () {
        var id = $(this).attr('data-id');

        Swal.fire({
            html: 'Удалить кандидата?',
            icon: "question",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Да!",
            cancelButtonText: 'Нет, отмена!',
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var data = {
                    _token: $('input[name=_token]').val(),
                    candidate_id: id,
                };

                $.ajax({
                    url: '/candidates/remove',
                    method: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.error) {
                            toastr.error(response.error);
                        } else {
                            toastr.success('Успешно');
                        }

                        if (window.dTables) {
                            dTables.forEach(function (tbl) {
                                tbl.draw();
                            });
                        }
                    }
                });
            }
        });

        return false;
    });
});

window.getStatusesCount = function () {
    if (
        !$('#statuses-count').length
        && !$('#statuses-count-koordinator').length
        && !$('#statuses-count-recruiter').length
    ) {
        return;
    }

    $.ajax({
        url: '/candidates/statuses/get-count-json',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                toastr.error(response.error);
            } else {
                const res = response.data;
                const html = [];

                if ($('#statuses-count').length) {
                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                    Всего: <span><b>${res.total ? res.total.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                    Новый кандидат: <span><b>${res.s1 ? res.s1.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                        Оформлен: <span><b>${res.s4 ? res.s4.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                        Подтвердил выезд: <span><b>${res.s6 ? res.s6.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                        В пути: <span><b>${res.s19 ? res.s19.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                        Трудоустроен: <span><b>${res.s8 ? res.s8.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                        Приступил к работе: <span><b>${res.s9 ? res.s9.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    $('#statuses-count').html(html);
                }

                if ($('#statuses-count-koordinator').length) {
                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                    Всего: <span><b>${res.total ? res.total.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Приступил к работе: <span><b>${res.s9 ? res.s9.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Отработал 7 дней: <span><b>${res.worked ? res.worked.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Уволен: <span><b>${res.s11 ? res.s11.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    $('#statuses-count-koordinator').html(html);
                }

                if ($('#statuses-count-recruiter').length) {
                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                    Всего: <span><b>${res.total ? res.total.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Новый кандидат: <span><b>${res.s1 ? res.s1.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Оформлен: <span><b>${res.s4 ? res.s4.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Подтвердил Выезд: <span><b>${res.s6 ? res.s6.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    html.push(`<div class="col flex-grow-0">
                                    <div class="card mb-10">
                                        <div class="card-body pt-5 pb-5 text-nowrap">
                                            Трудоустроен: <span><b>${res.s8 ? res.s8.count : 0}</b></span>
                                        </div>
                                    </div>
                                </div>`);

                    html.push(`<div class="col flex-grow-0">
                                <div class="card mb-10">
                                    <div class="card-body pt-5 pb-5 text-nowrap">
                                        Отработал 7 дней: <span><b>${res.worked ? res.worked.count : 0}</b></span>
                                    </div>
                                </div>
                            </div>`);

                    $('#statuses-count-recruiter').html(html);
                }

            }
        }
    });
}

function ratingBar() {
    $.ajax({
        url: '/users/recruiters-rating',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                const html = [];
                const resData = res.sort((a, b) => b.oform - a.oform);
                let me = null;
                let i = 1;
                let imAtTop = false;
                let myInd;

                resData.forEach((u, j) => {
                    if (j < 3) {
                        html.push(`<div class="col">
                            <div class="card mb-4 ${u.is_me ? 'is-me' : ''}">
                                <div class="card-body pt-3 pb-3 text-nowrap">
                                    ${i}. ${u.firstName.toUpperCase()} ${u.lastName.toUpperCase()} 
                                    <span><b>${u.oform}</b></span> оформлено
                                </div>
                            </div>
                        </div>`);

                        if (u.is_me) {
                            imAtTop = true;
                        }
                    }

                    if (u.is_me) {
                        me = u;
                        myInd = i;
                    }

                    i++;
                });

                if (!imAtTop) {
                    html.push(`<div class="col">
                        <div class="card mb-4 is-me">
                            <div class="card-body pt-3 pb-3 text-nowrap">
                                ${myInd}. ${me.firstName.toUpperCase()} ${me.lastName.toUpperCase()} 
                                <span><b>${me.oform}</b></span> оформлено
                            </div>
                        </div>
                    </div>`);
                }

                $('#recruiters-rating').html(html);
            }
        }
    });
}

// window.statisticsBar = function statisticsBar(period) {
//     if (
//         $('#statistics-bar').length
//         || $('#statistics-bar-koordinator').length
//         || $('#statistics-bar-recruiter').length
//     ) {
//         $.ajax({
//             url: '/statistics/employment',
//             // data: period ? 'is_filter=1&period[from]=' + period.from + '&period[to]=' + period.to : '',
//             type: 'GET',
//             dataType: 'json',
//             success: function (res) {
//                 if (res.error) {
//                     toastr.error(res.error);
//                 } else {
//                     const html = [];

//                     if ($('#statistics-bar-recruiter').length) {

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Новый кандидат: <span><b>${res.sum_data.table['in_work']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Оформлен: <span><b>${res.sum_data.table['4']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Подтвердил Выезд: <span><b>${res.sum_data.table['6']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Трудоустроен: <span><b>${res.sum_data.table['8']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                 <div class="card mb-10">
//                                     <div class="card-body pt-5 pb-5 text-nowrap">
//                                         Отработал 7 дней: <span><b>${res.sum_data.table['worked']}</b></span>
//                                     </div>
//                                 </div>
//                             </div>`);

//                         $('#statistics-bar-recruiter').html(html);

//                     } else if ($('#statistics-bar-koordinator').length) {
//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Всего: <span><b>${res.sum_data.table['total']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Приступило к работе: <span><b>${res.sum_data.table['9']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Отработал 7 дней: <span><b>${res.sum_data.table['worked']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Уволен: <span><b>${res.sum_data.table['11']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         $('#statistics-bar-koordinator').html(html);

//                     } else {
//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Всего: <span><b>${res.sum_data.table['total']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Оформлен: <span><b>${res.sum_data.table['4']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Подтвердил выезд: <span><b>${res.sum_data.table['6']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             В пути: <span><b>${res.sum_data.table['19']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Трудоустроен: <span><b>${res.sum_data.table['8']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         html.push(`<div class="col flex-grow-0">
//                                     <div class="card mb-10">
//                                         <div class="card-body pt-5 pb-5 text-nowrap">
//                                             Приступил к работе: <span><b>${res.sum_data.table['9']}</b></span>
//                                         </div>
//                                     </div>
//                                 </div>`);

//                         $('#statistics-bar').html(html);
//                     }
//                 }
//             }
//         });
//     }
// }