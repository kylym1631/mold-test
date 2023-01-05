'use strict';
import flatpickr from "flatpickr";
import MonthSelectPlugin from 'flatpickr/dist/plugins/monthSelect';
import { Russian } from "flatpickr/dist/l10n/ru.js"

window.dTables = [];

let userPermissions = [];

try {
    userPermissions = JSON.parse(permissions);
} catch (error) {
    console.log(new Error(error));
}

$(function () {
    init();

    $('body').on('click', '[data-bs-toggle="tab"]', function () {
        setTimeout(() => {
            init();
        }, 1000);
    });

    $('body').on('click', '.js-show-positions-details', function () {
        const cId = $(this).attr('data-candidate-id');

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');

            $('[data-candidate-position="' + cId + '"]').each(function () {
                $(this).closest('tr').hide();
            });

        } else {
            $(this).addClass('active');

            $('[data-candidate-position="' + cId + '"]').each(function () {
                $(this).closest('tr').show();
            });
        }

        return false;
    });
});

function init() {
    const tables = [];
    const $tables = [];

    $('.js-data-table:not(.dataTable)').each(function () {
        if ($(this).closest('.tab-pane').length) {
            if ($(this).closest('.tab-pane').hasClass('active')) {
                $tables.push($(this));
            }
        } else {
            $tables.push($(this));
        }
    });

    $tables.forEach(function ($tbl) {
        const $filters = $tbl.closest('.js-data-table-wrap').find('.js-filter');

        const tbl = new InitDataTable($tbl, {
            $filters,
            route: $tbl.attr('data-route'),
        });

        $filters.each(function () {
            if ($(this).hasClass('js-filter_joint')) {
                tables.push(tbl);

                new InitFilter($(this), tbl, tables);
            } else {
                new InitFilter($(this), tbl);
            }
        });

        $tbl.closest('.js-data-table-wrap').find('.js-search-input')
            .keyup(function () {
                tbl.search = $(this).val();
                tbl.table.draw();
            });

        window.dTables.push(tbl.table);
    });
}

class InitFilter {
    constructor($el, tblObj, tables) {
        this.$el = $el;
        this.tblObj = tblObj;
        this.tables = tables;

        if ($el.attr('type') != 'hidden' && !this.$el.hasClass('js-filter_initialized')) {
            this.$el.addClass('js-filter_initialized');

            if (
                $el.attr('name') == 'period'
                || $el.attr('name') == 'work_log_period'
                || $el.attr('name') == 'last_action_at'
            ) {
                if ($el.attr('data-format') == 'month') {
                    this.monthPicker();
                } else {
                    this.datePicker();
                }
            } else {
                this.multiSelect();
            }
        }
    }

    datePicker() {
        this.$el.flatpickr({
            altInput: true,
            altFormat: "d.m.Y",
            dateFormat: "Y-m-d",
            mode: "range",
            locale: {
                firstDayOfWeek: 1
            },
            onClose: () => {
                this.refreshTable();
            }
        });
    }

    monthPicker() {
        flatpickr(this.$el[0], {
            altInput: true,
            defaultDate: "today",
            locale: Russian,
            plugins: [new MonthSelectPlugin({
                shorthand: false,
                dateFormat: "Y-m",
                altFormat: "F Y",
            })],
            onClose: (date, dateFormat, obj) => {
                $('.js-add-addition-worklog').attr('data-period', dateFormat + '-01');

                this.refreshTable();
            },
        });
    }

    multiSelect() {
        let ajaxOpt = null;

        if (this.$el.attr('data-ajax-opt')) {
            ajaxOpt = {
                url: this.$el.attr('data-ajax-opt'),
                dataType: 'json',
                data: (params) => {
                    return {
                        s: this.$el.attr('data-ajax-s') || '',
                        f_search: params.term,
                        view: 'filter'
                    };
                },
                processResults: (data) => {
                    var results = [];

                    $.each(data, (index, item) => {
                        results.push({
                            id: item.id,
                            text: item.value
                        });

                        if (!this.$el.find("option[value='" + item.id + "']").length) {
                            this.$el.append(new Option(item.value, item.id));
                        }
                    });

                    this.$el.attr('data-loaded', 'true');

                    return {
                        results: results
                    };
                }
            };
        }

        this.$el.select2({
            allowClear: this.$el.attr('data-allow-clear') == 'false' ? false : true,
            minimumResultsForSearch: 5,
            ajax: ajaxOpt,
        }).on('change.select2', () => {
            this.refreshTable();

            LocStor.set('filter-' + this.$el.attr('name'), this.$el.val());
        });

        if (this.$el.prop('multiple')) {
            this.$el.next().append('<button type="button" class="js-select-all-for-select2" title="Выбрать всё">A</button>');
        }

        const filterCache = LocStor.get('filter-' + this.$el.attr('name'));

        if (filterCache) {
            if (this.$el.attr('data-ajax-opt')) {
                this.$el.attr('data-loaded', 'false');
                this.$el.select2('open');

                const _this = this;

                setTimeout(function loop() {
                    if (_this.$el.attr('data-loaded') == 'false') {
                        setTimeout(loop, 100);
                    } else if (_this.$el.attr('data-loaded') == 'true') {
                        filterCache.split(',').forEach(v => {
                            _this.$el.find('option[value="' + v + '"]').prop('selected', true);
                        });

                        _this.$el.trigger("change");
                        _this.$el.select2('close');
                        _this.$el.attr('data-loaded', 'none');
                    }
                }, 100);

                return;
            }

            filterCache.split(',').forEach(v => {
                this.$el.find('option[value="' + v + '"]').prop('selected', true);
            });

            this.$el.trigger("change");
        }
    }

    refreshTable() {
        if (this.tables) {
            this.tables.forEach(tbl => tbl.table.draw());
        } else {
            if (this.tblObj) {
                this.tblObj.table.draw();
            }
        }

        // if (window.statisticsBar) {
        //     const $dateFilter = $('.js-filter[name="period"]');

        //     if ($dateFilter.length) {
        //         const date = $dateFilter.val();
        //         let period = null;

        //         if (date) {
        //             const dateSpl = date.split('to');

        //             if (dateSpl.length > 1) {
        //                 period = {
        //                     from: dateSpl[0].trim(),
        //                     to: dateSpl[1].trim(),
        //                 };
        //             } else {
        //                 period = {
        //                     from: dateSpl[0].trim(),
        //                     to: dateSpl[0].trim(),
        //                 };
        //             }
        //         }

        //         statisticsBar(period);
        //     }
        // }
    }
}

class InitDataTable {
    constructor($tbl, { $filters, route }) {
        this.$tbl = $tbl;
        this.search = '';
        this.dataCache = null;

        const columns = [];

        this.$tbl.find('thead tr th').each(function () {
            if (!!$(this).attr('data-name')) {
                $(this).html('<span>' + $(this).text() + '<span></span></span>');
            }

            columns.push({ name: $(this).attr('data-name'), orderable: !!$(this).attr('data-name') });
        });

        this.table = $tbl.DataTable({
            dom: 'rt<"dataTable_bottom"lip>',
            columns,
            paging: true,
            searching: false,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            pageLength: 100,
            lengthMenu: [25, 50, 100, 250],
            order: ['0', ''],
            infoCallback: function (settings, start, end, max, total, pre) {
                return 'Показано ' + (end - start + 1) + ' из ' + total + ' записей';
            },
            fnDrawCallback: function (oSettings) {
                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                } else {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }

                $('.js-hours-input').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    onClose: function () {
                        if (window.saveHours) {
                            saveHours.call(this.element);
                        }
                    }
                });
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
                // delete (data.columns);

                data._token = $('input[name=_token]').val();
                data.search = this.search.trim();

                if (settings.sInstance == 'WorkLogsTable') {
                    data.candidate_id = $('#' + settings.sInstance).attr('data-candidate-id');
                }

                $filters.each(function () {
                    if ($(this).attr('name') == 'period') {
                        const date = $(this).val();

                        if (date) {
                            const dateSpl = date.split('to');

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

                    } else if ($(this).attr('name') == 'work_log_period') {
                        const date = $(this).val();

                        if (date) {
                            const dateSpl = date.split('to');

                            if (dateSpl.length > 1) {
                                data.work_log_period = {
                                    from: dateSpl[0].trim(),
                                    to: dateSpl[1].trim(),
                                };
                            } else {
                                data.work_log_period = {
                                    from: dateSpl[0].trim(),
                                    to: dateSpl[0].trim(),
                                };
                            }
                        }

                    } else if ($(this).attr('name') == 'last_action_at') {
                        const date = $(this).val();

                        if (date) {
                            const dateSpl = date.split('to');

                            if (dateSpl.length > 1) {
                                data.last_action_at = {
                                    from: dateSpl[0].trim(),
                                    to: dateSpl[1].trim(),
                                };
                            } else {
                                data.last_action_at = {
                                    from: dateSpl[0].trim(),
                                    to: dateSpl[0].trim(),
                                };
                            }
                        }

                    } else {
                        data[$(this).attr('name')] = $(this).val();
                    }
                });

                $.ajax({
                    url: route,
                    type: 'GET',
                    data: data,
                    success: (res) => {
                        if (res.error) {
                            toastr.error(res.error);
                        } else {
                            if ($tbl.attr('data-tpl')) {
                                if (this['tpl' + $tbl.attr('data-tpl')]) {
                                    callback(this['tpl' + $tbl.attr('data-tpl')](res));
                                } else {
                                    console.log(new Error('Make template method: tpl' + $tbl.attr('data-tpl')));
                                }
                            } else {
                                callback(res);
                            }

                            if ($tbl.attr('data-accontant-table') == 'true') {
                                $tbl.find('[data-candidate-position]').each(function () {
                                    $(this).closest('tr').hide();
                                });
                            }
                        }
                    }
                });
            },
        });
    }

    tplLeads(res) {
        res.data = !!res.data && res.data.map(item => {
            if (group == 1 || group == 9 || group == 11) {
                return [
                    '<a href="/leads/history?id=' + item.id + '">' + item.id + '</a>',
                    item.date,
                    item.source,
                    item.company,
                    item.name,
                    item.phone,
                    item.viber,
                    item.speciality_name,
                    item.requiter_name,
                    item.status,
                    item.last_action_at,
                    '',
                    '',
                    (settings => {
                        let res = '<ul>';
                        settings.forEach(li => {
                            res += '<li>' + li + '</li>';
                        })
                        res += '</ul>';
                        return res;
                    })(item.settings),
                ];
            }

            return [
                item.date,
                item.source,
                item.company,
                item.name,
                item.phone,
                item.viber,
                item.speciality_name,
            ];
        });

        return res;
    }

    tplLeadFieldMutation(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                item.date_time,
                item.user_name + ', ' + item.user_role_title,
                item.field,
                (item.prev_value ? item.prev_value : '') +
                ' -> ' + item.current_value,
            ];
        });

        return res;
    }

    tplLeadSettings(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="${item.id}" data-id="${item.id}" class="js-show-lead-settings">${item.id}</a>`,
                item.name,
                item.sources && item.sources.join(', '),
                item.statuses && item.statuses.join(', '),
                item.speciality && item.speciality.join(', '),
                item.lifetime,
            ];
        });

        return res;
    }

    tplLeadsStatusInform(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="${item.system_id}" data-id="${item.system_id}" class="js-show-lead-status-info">${item.system_id}</a>`,
                item.name,
                item.info,
            ];
        });

        return res;
    }

    tplHousing(res) {
        res.data = !!res.data && res.data.map(item => {
            let clients = '<ul>';

            item.clients.forEach((cl) => {
                if (cl) {
                    clients += '<li>' + cl.name + '</li>';
                }
            });

            clients += '</ul>';

            return [
                `<a href="/housing/view?id=${item.id}">${item.id}</a>`,
                item.title,
                item.address,
                item.zip_code,
                clients,
                item.places_count,
                // item.cost,
                // item.cost_per_day,
            ];
        });

        return res;
    }

    tplClientHousing(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="/housing/view?id=${item.id}">${item.id}</a>`,
                item.title,
                item.address,
                item.zip_code,
                item.places_count,
            ];
        });

        return res;
    }

    tplHousingRooms(res) {
        res.data = !!res.data && res.data.map(item => {
            let delBtn = '';

            if (userPermissions.includes('housing.edit')) {
                delBtn = '<button type="button" class="btn btn-sm btn-icon js-delete-room-btn" data-id="' + item.id + '"><span class="svg-icon svg-icon-primary svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <rect x="0" y="0" width="24" height="24"/> <circle fill="currentColor" opacity="0.3" cx="12" cy="12" r="10"/> <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="currentColor"/> </g> </svg></span></button>';
            }

            return [
                `<a href="#" class="js-edit-room"
                    data-id="${item.id}"
                    data-housing-id="${item.housing_id}"
                    data-number="${item.number}"
                    data-places-count="${item.places_count}"
                    data-filled-count="${item.filled_count}"
                >${item.number}</a>`,
                item.places_count,
                item.filled_count,
                item.free_count,
                delBtn,
            ];
        });

        return res;
    }

    tplWorkLogs(res) {
        let data = [];

        if (res.updateCandidate) {
            data = this.dataCache.map(item => {
                if (item.candidate_id == res.updateCandidate) {
                    return res.data[0];
                } else {
                    return item;
                }
            });

            this.dataCache = JSON.parse(JSON.stringify(data));

        } else if (res.data) {
            data = res.data;
            this.dataCache = JSON.parse(JSON.stringify(data));
        }

        res.data = !!data.length && data.map(m => {
            let clientOptions = ['<option selected></option>'];

            m.clients.forEach(c => {
                if (c.id == m.client_id) {
                    clientOptions.push('<option value="' + c.id + '" selected>' + c.name + '</option>');
                } else {
                    clientOptions.push('<option value="' + c.id + '">' + c.name + '</option>');
                }
            });

            // let clientSelect = '<select class="js-select-client form-select form-select-sm form-select-solid" data-log-id="' + m.id + '" data-date="' + m.period + '" data-candidate-id="' + m.candidate_id + '"> ' + clientOptions.join('') + ' </select>';

            const arr = [
                m.candidate_id,
                m.firstName,
                m.lastName,
                m.position,
                // clientSelect,
            ];

            m.work_log_days.forEach(d => {
                arr.push('<input type="text" value="' + d.work_time + '" data-id="' + d.id + '" data-log-id="' + d.work_log_id + '" data-date="' + d.date + '" data-candidate-id="' + m.candidate_id + '" data-rate="' + m.rate + '" data-housing="' + m.housing + '" title="Ставка за день: ' + d.rate + '" data-format="' + m.work_time_format + '" class="' + (m.work_time_format == 'natural' ? 'js-hours-input' : 'js-worklog-hours-input') + '">');
            });

            const fine = '<input type="text" value="' + m.fine + '" data-date="' + m.period + '" data-candidate-id="' + m.candidate_id + '" data-rate="' + m.rate + '" data-housing="' + m.housing + '" data-log-id="' + m.id + '" class="js-fine-input">';

            const premium = '<input type="text" value="' + m.premium + '" data-date="' + m.period + '" data-candidate-id="' + m.candidate_id + '" data-rate="' + m.rate + '" data-housing="' + m.housing + '" data-log-id="' + m.id + '" class="js-premium-input">';

            const bhp_form = '<input type="text" value="' + m.bhp_form + '" data-date="' + m.period + '" data-candidate-id="' + m.candidate_id + '" data-rate="' + m.rate + '" data-housing="' + m.housing + '" data-log-id="' + m.id + '" class="js-bhp-form-input">';

            arr.push(
                m.work_time_sum,
                m.rate,
                m.salary,
                m.housing,
                fine,
                bhp_form,
                m.stay_cards_cost,
                premium,
                m.recommendation,
                m.transport,
                m.work_permits,
                m.Coordinator
            );

            return arr;
        });

        return res;
    }

    tplCars(res) {
        res.data = !!res.data && res.data.map(item => {
            let doc = '';

            if (item.doc_files.length) {
                doc += '<a href="' + item.doc_files[0].path + '" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="' + item.doc_files[0].ext + '"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path> <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path> </svg> </a>';
            }

            return [
                `<a href="/cars/view?id=${item.id}">${item.id}</a>`,
                item.brand,
                item.model,
                item.number,
                item.year,
                item.is_rent ? item.rent_cost : '-',
                item.is_rent ? item.landlord : '-',
                doc,
                item.user_name,
            ];
        });

        return res;
    }

    tplTransportations(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="/transportations/${item.id}">${item.id}</a>`,
                item.title,
                item.departure_date,
                item.departure_place,
                item.arrival_date,
                item.arrival_place,
                item.drivers_phone,
                item.number,
                item.places,
                item.status_name,
                item.comment,
            ];
        });

        return res;
    }

    tplRoles(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                ((id) => {
                    if (item.id == '1') {
                        return '<span>' + id + '</span>';
                    }

                    return `<a href="/roles/add?id=${id}">${id}</a>`;

                })(item.id),
                item.name,
                ((id) => {
                    if (id == '1') {
                        return '<span class="d-block text-center">Администратор системы</span>';
                    }

                    return `<span class="d-block text-center"><a href="/roles/add?id=${id}" class="btn btn-icon" title="Задать разрешения"> 
                    <span class="svg-icon svg-icon-2x svg-icon-primary"> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" > <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" > <rect x="0" y="0" width="24" height="24" /> <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="currentColor" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) " /> <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="currentColor" fill-rule="nonzero" opacity="0.3" /> </g> </svg> </span> 
                    </a></span>`;
                })(item.id),
            ];
        });

        return res;
    }

    tplWorklogAddition(res) {
        if (res.data && res.data[0]) {
            $('.js-add-addition-worklog').attr('data-worklog-id', res.data[0].work_log_id);
        }

        res.data = !!res.data && res.data.map(item => {
            let filesRes = '';

            if (item.files) {
                filesRes = item.files.map(fItem => {
                    return `<a href="${fItem.path}" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="${fItem.ext}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                        </svg>
                    </a>`;
                }).join('');
            }

            return [
                `<a href="#"
                    data-id="${item.id}"
                    data-type="${item.type}"
                    class="js-edit-addition-worklog">
                    ${item.id}
                </a>`,
                item.date,
                item.amount,
                item.comment,
                filesRes,
            ];
        });

        return res;
    }

    tplCandidatePositions(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="#"
                    data-id="${item.id}"
                    data-start="${item.start}"
                    data-end="${item.end}"
                    data-current="${item.is_current}"
                    data-candidate-id="${item.candidate_id}"
                    class="js-edit-position">
                    ${item.id}
                </a>`,
                item.position,
                item.client,
                item.start,
                item.end,
            ];
        });

        return res;
    }

    tplCandidateHousing(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="#"
                    data-id="${item.id}"
                    data-start="${item.start}"
                    data-end="${item.end}"
                    data-current="${item.is_current}"
                    data-candidate-id="${item.candidate_id}"
                    class="js-edit-housing-period">
                    ${item.id}
                </a>`,
                item.housing,
                item.housing_room,
                item.cost_per_day,
                item.start,
                item.end,
            ];
        });

        return res;
    }

    tplTemlpates(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a 
                href="/templates/${item.id + (userPermissions.includes('templates.edit') ? '/edit' : '')}" class="js-edit-housing-period">
                    ${item.id}
                </a>`,
                item.title,
                item.description,
            ];
        });

        return res;
    }

    tplTemlpatesFind(res) {
        res.data = !!res.data && res.data.map(item => {
            const cndId = window.location.search.match(/candidate_id\=(\d+)/i);

            return [
                item.id,
                item.title,
                item.description,
                `<div class="d-flex justify-content-center form-check form-check-custom form-check-sm form-check-solid">
                    <input
                        class="form-check-input js-choose-template"
                        type="checkbox"
                        name="tpl_id[]"
                        value="${item.id}"
                    >
                </div>`
            ];
        });

        return res;
    }

    tplOswiadczenie(res) {
        res.data = !!res.data && res.data.map(item => {
            let filesRes = '';

            if (item.files) {
                filesRes = item.files.map(fItem => {
                    return `<a href="${fItem.path}" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="${fItem.ext}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                        </svg>
                    </a>`;
                }).join('');
            }

            return [
                `<a href="#"
                data-modal-tpl="oswiadczenie" 
                data-modal-title="Редактировать освядчение" 
                data-update-endpoint="/oswiadczenie/${item.id}" 
                data-get-endpoint="/oswiadczenie/${item.id}" 
                data-refresh-table="OswiadczenieTable" 
                class="js-editor-edit-btn">
                    ${item.id}
                </a>`,
                item.date,
                item.cost,
                item.min_hours,
                filesRes,
            ];
        });

        return res;
    }

    tplFieldsMutation(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                item.date_time,
                item.user_name + ', ' + item.user_role_title,
                (item.model_name == 'CandidateArrival' ?
                    'Логистика, ' : '') + item.field || item
                    .field_name,
                (item.prev_value ? item.prev_value : '') +
                ' -> ' + item.current_value,
            ];
        });

        return res;
    }

    tplCandidateDocuments(res) {
        res.data = !!res.data && res.data.map(item => {
            const previewLink = '<span class="d-block text-center"><a href="/templates/document-preview?doc_id=' + item.id + '" class="btn btn-icon" title="Предпросмотр и скачивание"><span class="svg-icon svg-icon-2x svg-icon-primary"> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <polygon points="0 0 24 0 24 24 0 24"/> <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="currentColor" fill-rule="nonzero" opacity="0.3"/> <rect fill="currentColor" x="6" y="11" width="9" height="2" rx="1"/> <rect fill="currentColor" x="6" y="15" width="5" height="2" rx="1"/> </g> </svg> </span></a></span>';

            return [
                item.id,
                item.title,
                item.date,
                previewLink,
            ];
        });

        return res;
    }

    tplAllTasks(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                item.id,
                '<span title="'+ item.created_updated +'">' + item.date + '</span>',
                item.title,
                '<span title="'+ item.cur_status +'">' + item.person + '</span>',
                item.user_full_name,
                item.status,
            ];
        });

        return res;
    }

    tplTaskTemplates(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="/tasks/templates/${item.id}" class="js-edit-housing-period">
                    ${item.id}
                </a>`,
                item.title,
                item.description,
            ];
        });

        return res;
    }

    tplUsersLeadSettings(res) {
        res.data = !!res.data && res.data.map(item => {
            const rtn = [
                item.id,
                item.firstName,
                item.lastName,
            ];

            JSON.parse(all_packages).forEach(pcg => {
                rtn.push(`<div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                            <input class="js-activate-lead-setting form-check-input" type="checkbox"
                            data-user-id="${item.id}" 
                            value="${pcg.id}" ${item.leads_settings.includes(pcg.id) ? 'checked' : ''}>
                        </div>`);
            });

            return rtn;
        });

        return res;
    }

    tplLegalisation(res) {
        res.data = !!res.data && res.data.map(item => {
            return [
                `<a href="#"
                data-modal-tpl="legalisation" 
                data-modal-title="Редактировать документ" 
                data-update-endpoint="/legalisation/${item.id}" 
                data-get-endpoint="/legalisation/${item.id}" 
                data-refresh-table="LegalisationTable" 
                class="js-editor-edit-btn">
                    ${item.id}
                </a>`,
                item.title,
                item.who_issued,
                item.issue_date,
                item.number,
                item.date_from,
                item.date_to,
                item.type ? item.type.name : '',
                item.files[0]
                    ? `<a href="${item.files[0].path}" style="cursor: pointer;" class="js-view-doc svg-icon svg-icon-2x svg-icon-primary me-4" data-ext="${item.files[0].ext}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="currentColor"></path>
                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor"></path>
                        </svg>
                    </a>`
                    : '',
            ];
        });

        return res;
    }
}