$(function () {
    'use strict';

    ['#tasks-table', '#employment-table', '#leads-table'].forEach(function (id) {
        if ($(id).length) {
            new DataProcess(id);
        }
    });

    function DataProcess(contId) {
        const columns = [];

        $(contId + ' .table').find('thead tr th').each(function () {
            if (!!$(this).attr('data-name')) {
                $(this).html('<span>'+ $(this).text() +'<span></span></span>');
            }
            
            columns.push({ name: $(this).attr('data-name'), orderable: !!$(this).attr('data-name')});
        });

        this.oTable = $(contId + ' .table').DataTable({
            dom: 'rt<"dataTable_bottom"lip>',
            columns,
            paging: true,
            searching: false,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            pageLength: 100,
            lengthMenu: [25, 50, 100, 250],
            infoCallback: function (settings, start, end, max, total, pre) {
                return 'Показано ' + (end - start + 1) + ' из ' + total + ' записей';
            },
            fnDrawCallback: function (oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                } else {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
            },
            language: {
                emptyTable: "нет данных",
                zeroRecords: "нет данных",
                sSearch: "Поиск",
                processing: 'Загрузка...'
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': ['sorting_disabled']
            }],
            ajax: (data, callback, settings) => {
                data._token = $('input[name=_token]').val();

                if ($(contId + ' .filter__group').length) {
                    data.group_id = $(contId + ' .filter__group').val();
                }

                if ($(contId + ' .filter__activation').length) {
                    data.filter__activation = $(contId + ' .filter__activation').val();
                }

                if ($(contId + ' .js-date-oform').length) {
                    const date = $(contId + ' .js-date-oform').val();
                    const dateSpl = !!date && date.split('to');

                    if (dateSpl) {
                        if (dateSpl.length > 1) {
                            data.period_oform = {
                                from: dateSpl[0].trim(),
                                to: dateSpl[1].trim(),
                            };
                        } else {
                            data.period_oform = {
                                from: dateSpl[0].trim(),
                                to: dateSpl[0].trim(),
                            };
                        }
                    }
                }

                if ($(contId + ' .js-date-created').length) {
                    const date = $(contId + ' .js-date-created').val();
                    const dateSpl = !!date && date.split('to');

                    if (dateSpl) {
                        if (dateSpl.length > 1) {
                            data.period_created = {
                                from: dateSpl[0].trim(),
                                to: dateSpl[1].trim(),
                            };
                        } else {
                            data.period_created = {
                                from: dateSpl[0].trim(),
                                to: dateSpl[0].trim(),
                            };
                        }
                    }
                }

                const date = $(contId + ' .kt_datepicker_7').val();
                const dateSpl = !!date && date.split('to');

                if (dateSpl) {
                    if (dateSpl.length > 1) {
                        data.period = {
                            from: dateSpl[0].trim(),
                            to: dateSpl[1].trim(),
                        };
                    } else {
                        data.period = {
                            from: dateSpl[0].trim(),
                            to: dateSpl[0].trim(),
                        };
                    }
                }

                $.ajax({
                    url: $(contId).data('route'),
                    type: 'GET',
                    data: data,
                    success: (res) => {
                        if (res.error) {
                            toastr.error(res.error);
                        } else {
                            res.data = !!res.data && res.data.map(item => {
                                if (contId == '#tasks-table') {
                                    return this.tplTasks(item);
                                } else if (contId == '#employment-table') {
                                    return this.tplEmloy(item);
                                } else {
                                    return this.tplLeads(item);
                                }
                            });

                            if (res.data && contId == '#employment-table') {
                                res.data.unshift(this.tplEmloy(res.sum_data));
                            }

                            callback(res);
                        }
                    }
                });
            },
        });

        this.tplTasks = function (item) {
            return [
                `<div class="d-flex align-items-center">
                    <div class="d-flex justify-content-start flex-column">
                        <a href="#" class="text-dark fw-bold text-hover-primary fs-6 text-uppercase">
                            ${item.firstName} ${item.lastName}
                        </a>
                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                            ${item.groupName}
                        </span>
                    </div>
                </div>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                    ${item.todoCount}
                </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                    ${item.overdueCount}
                </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                    ${item.performedCount}
                </span>`,
                `<div class="d-flex flex-column w-100 me-2">
                    <div class="d-flex flex-stack mb-2">
                        <span class="text-muted me-2 fs-7 fw-bold">${item.barWidth}%</span>
                    </div>
                    <div class="progress h-6px w-100">
                        <div class="progress-bar bg-warning" role="progressbar" 
                        style="width: ${item.barWidth}%" aria-valuenow="${item.barWidth}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>`,
            ];
        }

        this.tplEmloy = function (item) {
            return [
                `<div class="d-flex align-items-center">
                        <div class="d-flex justify-content-start flex-column">
                            <span class="text-dark fw-bold text-hover-primary fs-6 ${item.firstName == 'Всего' ? '' : 'text-uppercase'}">
                                ${item.firstName} ${item.lastName}
                            </span>
                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                ${item.groupName}
                            </span>
                        </div>
                    </div>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table.total}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table.in_work}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['4']}
                    </span>`,    
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                    ${item.table['3']}
                </span>`,
                `<div class="d-flex flex-column w-100 me-2">
                    <div class="d-flex flex-stack mb-2">
                        <span class="text-muted me-2 fs-7 fw-bold">${item.table.c_3}%</span>
                    </div>
                </div>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['6']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['logist_3']}
                    </span>`,
                // `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                //         ${item.table['21']}
                //     </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['19']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['12']}
                    </span>`,
                `<div class="d-flex flex-column w-100 me-2">
                    <div class="d-flex flex-stack mb-2">
                        <span class="text-muted me-2 fs-7 fw-bold">${item.table.c_12}%</span>
                    </div>
                </div>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['20']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['trud_3']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['22']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['8']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['7']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table['9']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                        ${item.table.worked}
                    </span>`,
                `<div class="d-flex flex-column w-100 me-2">
                        <div class="d-flex flex-stack mb-2">
                            <span class="text-muted me-2 fs-7 fw-bold">${item.table.c_worked}%</span>
                        </div>
                    </div>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                    ${item.table['11']}
                    </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                    ${item.table['5']}
                    </span>`,
            ];
        }

        this.tplLeads = function (item) {
            return [
                `<div class="d-flex align-items-center">
                    <div class="d-flex justify-content-start flex-column">
                        <a href="#" class="text-dark fw-bold text-hover-primary fs-6 text-uppercase">
                            ${item.company}
                        </a>
                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                            ${item.name}
                        </span>
                    </div>
                </div>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.total}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.col_new}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.col_2}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.col_3}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.col_4}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.col_1}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.candidates.total}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.candidates['14']}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.candidates['4']}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.candidates['8']}
                        </span>`,
                `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
                            ${item.candidates.worked}
                        </span>`,
            ];
            // return [
            //     `<div class="d-flex align-items-center">
            //                 <div class="d-flex justify-content-start flex-column">
            //                     <a href="#" class="text-dark fw-bold text-hover-primary fs-6 text-uppercase">
            //                         ${item.name}
            //                     </a>
            //                     <span class="text-muted fw-semibold text-muted d-block fs-7">
            //                         ${item.company}
            //                     </span>
            //                 </div>
            //             </div>`,
            //     `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
            //                 ${item.total}
            //             </span>`,
            //     `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
            //                 ${item.col_1}
            //             </span>`,
            //     `<span class="text-dark fw-bold text-hover-primary d-block fs-6">
            //                 ${item.col_2}
            //             </span>`,
            //     `<div class="d-flex flex-column w-100 me-2">
            //         <div class="d-flex flex-stack mb-2">
            //             <span class="text-muted me-2 fs-7 fw-bold">${item.barWidth}%</span>
            //         </div>
            //         <div class="progress h-6px w-100">
            //             <div class="progress-bar bg-warning" role="progressbar" 
            //             style="width: ${item.barWidth}%" aria-valuenow="${item.barWidth}" aria-valuemin="0" aria-valuemax="100"></div>
            //         </div>
            //     </div>`,
            // ];
        }

        $(contId + ' .filter__group').select2({
            placeholder: 'Роль',
            allowClear: true,
            minimumResultsForSearch: -1
        }).on('select2:select', (e) => {
            this.oTable.draw();
        }).on('select2:clear', (e) => {
            this.oTable.draw();
        });

        $(contId + ' .filter__activation').select2({
            placeholder: 'Статус',
            allowClear: true,
            minimumResultsForSearch: -1
        }).on('select2:select', (e) => {
            this.oTable.draw();
        }).on('select2:clear', (e) => {
            this.oTable.draw();
        });

        $(contId + " .kt_datepicker_flatpickr").flatpickr({
            altInput: true,
            altFormat: "d.m.Y",
            dateFormat: "Y-m-d",
            mode: "range",
            locale: {
                firstDayOfWeek: 1
            },
            onClose: () => {
                this.oTable.draw();
            }
        });
    }
});