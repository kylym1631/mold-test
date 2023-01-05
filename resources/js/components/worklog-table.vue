<script>
import axios from "axios";
import JqFlatpickr from "./jq-flatpickr.vue";
import PgDropzone from "./pg-dropzone.vue";
import flatpickr from "flatpickr";
import MonthSelectPlugin from 'flatpickr/dist/plugins/monthSelect';
import { Russian } from "flatpickr/dist/l10n/ru.js"

export default {
    name: 'WorklogTable',
    components: { JqFlatpickr, PgDropzone },
    props: ['clientIds', 'daysOfWeek', 'userRole'],
    data() {
        return {
            draw: 0,
            length: 100,
            recordsFiltered: 0,
            recordsShown: 0,
            curMonth: null,
            period: null,
            status: [],
            candidates: [],
            days: [],
            modal: false,
            editRate: false,
            editingDay: {},
            timeFormat: '',
            fp: null,
            addModal: {
                open: false,
            },
            addition: {},
            clientsFilter: [],
        };
    },
    watch: {
        length(n, o) {
            if (n != o) {
                this.getData();
            }
        },
        timeFormat(n) {
            if (n == 'decimal') {
                if (this.fp) {
                    this.fp.destroy();
                }
            } else {
                this.fp = flatpickr(this.$refs.worklogHoursInput, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour: 8,
                });
            }
        }
    },
    mounted() {
        this.clientsFilter = JSON.parse(this.clientIds);

        // this.fp = flatpickr(this.$refs.worklogHoursInput, {
        //     enableTime: true,
        //     noCalendar: true,
        //     dateFormat: "H:i",
        //     time_24hr: true,
        //     defaultHour: 8,
        // });

        $(this.$refs.workTimeModal).on('hidden.bs.modal', () => {
            this.getData(this.editingDay.candidate_id);
        });

        $(this.$refs.addDataModal).on('hidden.bs.modal', () => {
            this.addModal.open = false;
            this.addition = {};
        });

        flatpickr(document.getElementById('js-filter-by-month'), {
            altInput: true,
            defaultDate: "today",
            locale: Russian,
            plugins: [new MonthSelectPlugin({
                shorthand: false,
                dateFormat: "Y-m",
                altFormat: "F Y",
            })],
            onClose: () => {
                this.period = { from: document.getElementById('js-filter-by-month').value };
                this.getData();
            },
        });

        this.status = $('#js-filter-by-status').val();

        $('#js-filter-by-status').select2({
            allowClear: true,
            minimumResultsForSearch: -1,
        }).on('change.select2', () => {
            this.status = $('#js-filter-by-status').val();
            this.getData();
        });

        $('#js-filter-by-clients').select2({
            allowClear: true,
            minimumResultsForSearch: -1,

            ajax: {
                url: '/search/client',
                dataType: 'json',
                data: (params) => {
                    return {
                        s: '',
                        f_search: params.term,
                    };
                },
                processResults: (data) => {
                    var results = [];

                    $.each(data, (index, item) => {
                        results.push({
                            id: item.id,
                            text: item.value
                        });
                    });

                    return {
                        results: results
                    };
                }
            }
        }).on('change.select2', () => {
            this.clientsFilter = $('#js-filter-by-clients').val();
            this.getData();
        });

        this.getData();
    },
    methods: {
        getData(candidateId) {
            axios.get('/work-logs/json', {
                params: {
                    period: this.period || '',
                    status: this.status,
                    start: 0,
                    length: this.length,
                    draw: this.draw + 1,
                    clients: this.clientsFilter,
                    candidate_id: candidateId || '',
                }
            }).then(({ data }) => {
                if (data.error) {
                    toastr.error(data.error);
                    return;
                }

                this.draw = +data.draw;

                if (!data.data.length) {
                    this.candidates = [];
                    return;
                }

                if (!candidateId) {
                    this.recordsFiltered = +data.recordsFiltered;

                    if (this.recordsFiltered < this.length) {
                        this.recordsShown = this.recordsFiltered;
                    } else {
                        this.recordsShown = this.length;
                    }
                }

                if (candidateId) {
                    this.candidates.forEach((item, i) => {
                        data.data.forEach((dItem) => {
                            if (item.row_key == dItem.row_key) {
                                this.candidates[i] = dItem;
                                this.candidates[i].is_hidden = item.is_hidden;
                            }
                        });
                    });

                } else {
                    if (this.curMonth !== null) {
                        const month = new Date(data.data[0].period).getMonth();

                        if (month != this.curMonth) {
                            this.buildDates(data.data[0].work_log_days);
                            this.curMonth = month;
                        }

                    } else {
                        this.curMonth = new Date(data.data[0].period).getMonth();
                        this.buildDates(data.data[0].work_log_days);
                    }

                    this.candidates = data.data;

                    this.candidates.forEach((it, i) => {
                        if (it.is_position) {
                            this.candidates[i].is_hidden = true;
                        }
                    })
                }

                // this.timeFormat = data.data[0].work_time_format;
                this.housing = data.data[0].housing;

                // if (this.timeFormat == 'decimal') {
                //     this.fp.destroy();
                // }
            });
        },
        buildDates(days) {
            const daysOfWeek = JSON.parse(this.daysOfWeek);
            this.days = [];

            days.forEach(d => {
                const today = new Date(d.date);
                const dayNum = today.getDay();

                this.days.push({
                    date: today.getDate(),
                    dayNum: dayNum,
                    dayName: daysOfWeek[dayNum],
                });
            });
        },
        editModal(day, i) {
            this.timeFormat = day.work_time_format;
            this.editingDay = day;

            if (!this.editingDay.log_id) {
                this.editingDay.log_id = this.candidates[i].log_id;
            }

            this.editingDay.candidate_id = this.candidates[i].candidate_id;

            $(this.$refs.workTimeModal).modal('show');
        },
        saveDay() {
            const data = {
                _token: $('input[name=_token]').val(),
                log_id: this.editingDay.log_id,
                candidate_id: this.editingDay.candidate_id,
                period: this.editingDay.date,
                log_day_id: this.editingDay.id,
                log_day_date: this.editingDay.date,
                work_time_format: this.timeFormat,
                log_day_hours: String(this.editingDay.work_time).replace(',', '.'),
            };

            axios.post('/work-logs/add', data)
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('Успешно');
                        $(this.$refs.workTimeModal).modal('hide');
                    }
                });
        },
        openAddModal(cdt, type) {
            this.addModal.open = true;

            this.addition.type = type;
            this.addition.candidate_id = cdt.candidate_id;
            this.addition.log_id = cdt.log_id;
            this.addition.period = cdt.period;

            const titles = {
                fine: 'Штраф',
                bhp_form: 'БХП',
                stay_cards_cost: 'Карта побыта',
                premium: 'Премия',
                recommendation: 'Рекомендация',
                transport: 'Транспорт',
                work_permits: 'Разрешение на работу',
                prepayment: 'Аванс',
            }

            this.addModal.title = titles[type];

            setTimeout(() => {
                $(this.$refs.addDataModal).modal('show');
            }, 100);
        },
        saveAddition() {
            const fD = new FormData;

            fD.append('type', this.addition.type);
            fD.append('candidate_id', this.addition.candidate_id);
            fD.append('period', this.addition.period);
            fD.append('work_log_id', this.addition.log_id || '');
            fD.append('date', this.addition.date || '');
            fD.append('comment', this.addition.comment || '');
            fD.append('amount', this.addition.amount || '');

            this.addition.files?.files.forEach(f => {
                if (f.type) {
                    fD.append('file[]', f, f.name);
                }
            });

            this.addition.files?.toRemove.forEach(fileName => {
                fD.append('to_delete_files[]', fileName);
            });

            axios.post('/work-logs/add/additions', fD)
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('Успешно');
                        $(this.$refs.addDataModal).modal('hide');
                        this.getData(this.addition.candidate_id);
                    }
                });
        },
        completeLog(cdt, e) {
            if (cdt.completed && this.userRole !== '1') {
                return;
            }

            if (cdt.completed && this.userRole === '1') {
                axios.post('/work-logs/complete', {
                    id: cdt.log_id,
                    candidate_id: cdt.candidate_id,
                    period: cdt.period,
                    uncomplete: 1,
                }).then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('Успешно');
                        this.getData(this.addition.candidate_id);
                    }
                });
            }

            if (e.target.checked) {
                Swal.fire({
                    html: 'Рассчитать?',
                    icon: "question",
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: "Да!",
                    cancelButtonText: 'Нет, отмена!',
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((r) => {
                    if (r.isConfirmed) {
                        axios.post('/work-logs/complete', {
                            id: cdt.log_id,
                            candidate_id: cdt.candidate_id,
                            period: cdt.period,
                        }).then(({ data }) => {
                            if (data.error) {
                                cdt.completed = false;
                                toastr.error(data.error);
                            } else {
                                toastr.success('Успешно');
                                this.getData(this.addition.candidate_id);
                            }
                        });
                    } else {
                        e.target.checked = false;
                    }
                });
            }
        },
        showPositions(id) {
            this.candidates.forEach((cnd, i) => {
                if (cnd.is_position && cnd.candidate_id == id) {
                    this.candidates[i].is_hidden = !cnd.is_hidden;
                }
            });
        }
    }
}
</script>

<template>
    <div class="table-responsive worklog-table">
        <div
            id="WorkLogsTable_wrapper"
            class="dataTables_wrapper dt-bootstrap4 no-footer"
        >
            <div
                id="WorkLogsTable_processing"
                class="dataTables_processing"
                style="display: none;"
            >Загрузка...</div>
            <table class="table align-middle table-row-dashed fs-6 gy-3 dataTable no-footer">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                        <th>Id</th>
                        <th>Имя</th>
                        <th>Фамилия</th>
                        <th class="prev-day-cell">Должность</th>
                        <th
                            v-for="day in days"
                            :key="day.date"
                            :class="'day-cell day-cell_' + day.dayNum"
                        >{{day.date}}<br>{{day.dayName}}</th>
                        <th>Часы</th>
                        <th>Ставка</th>
                        <th>Зарплата</th>
                        <th>Жилье</th>
                        <th>Штрафы</th>
                        <th>БХП - форма</th>
                        <th>Карты по быта (стоимоcть)</th>
                        <th>Премия</th>
                        <th>Рекоменации</th>
                        <th>Транспорт</th>
                        <th>Разрешения на работу</th>
                        <th>Аванс</th>
                        <th>К выплате</th>
                        <th>Рассчитан</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    <tr
                        v-for="(cdt, cdtInd) in candidates"
                        :key="cdt.candidate_id"
                        :style="{display: cdt.is_hidden ? 'none' : ''}"
                    >
                        <td>
                            <a
                                v-if="!cdt.is_position"
                                :href="'/candidate/view?id=' + cdt.candidate_id"
                                class="main-color"
                            >{{cdt.candidate_id}}</a>
                        </td>
                        <td>{{cdt.firstName?.toUpperCase()}}</td>
                        <td>{{cdt.lastName?.toUpperCase()}}</td>
                        <td class="prev-day-cell">
                            <a
                                v-if="cdt.positions?.length > 1 && !cdt.is_position"
                                href="#"
                                @click.prevent="showPositions(cdt.candidate_id)"
                                style="white-space: nowrap"
                            ><span class="svg-icon svg-icon-primary svg-icon-2x"><svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path
                                                d="M8.2928955,3.20710089 C7.90237121,2.8165766 7.90237121,2.18341162 8.2928955,1.79288733 C8.6834198,1.40236304 9.31658478,1.40236304 9.70710907,1.79288733 L15.7071091,7.79288733 C16.085688,8.17146626 16.0989336,8.7810527 15.7371564,9.17571874 L10.2371564,15.1757187 C9.86396402,15.5828377 9.23139665,15.6103407 8.82427766,15.2371482 C8.41715867,14.8639558 8.38965574,14.2313885 8.76284815,13.8242695 L13.6158645,8.53006986 L8.2928955,3.20710089 Z"
                                                fill="currentColor"
                                                fill-rule="nonzero"
                                                transform="translate(12.000003, 8.499997) scale(-1, -1) rotate(-90.000000) translate(-12.000003, -8.499997) "
                                            />
                                            <path
                                                d="M6.70710678,19.2071045 C6.31658249,19.5976288 5.68341751,19.5976288 5.29289322,19.2071045 C4.90236893,18.8165802 4.90236893,18.1834152 5.29289322,17.7928909 L11.2928932,11.7928909 C11.6714722,11.414312 12.2810586,11.4010664 12.6757246,11.7628436 L18.6757246,17.2628436 C19.0828436,17.636036 19.1103465,18.2686034 18.7371541,18.6757223 C18.3639617,19.0828413 17.7313944,19.1103443 17.3242754,18.7371519 L12.0300757,13.8841355 L6.70710678,19.2071045 Z"
                                                fill="currentColor"
                                                fill-rule="nonzero"
                                                opacity="0.3"
                                                transform="translate(12.000003, 15.499997) scale(-1, -1) rotate(-360.000000) translate(-12.000003, -15.499997) "
                                            />
                                        </g>
                                    </svg>
                                </span>
                                должности
                            </a>
                            <span v-if="cdt.positions?.length < 2 || cdt.is_position">
                                {{cdt.current_position}}
                            </span>
                        </td>
                        <td
                            v-for="(day,i) in cdt.work_log_days"
                            :key="i"
                            class="day-cell"
                        >{{day.position_id ? day.is_client == false ? 'X' : day.work_time : '--'}}
                            <button
                                v-if="day.position_id && day.is_client"
                                @click="editModal(day, cdtInd)"
                                class="day-cell__btn btn btn-icon"
                            >
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            />
                                            <path
                                                d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                fill="currentColor"
                                                fill-rule="nonzero"
                                                transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "
                                            />
                                            <path
                                                d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                fill="currentColor"
                                                fill-rule="nonzero"
                                                opacity="0.3"
                                            />
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td>{{cdt.work_time_sum}}</td>
                        <td>{{cdt.rate}}</td>
                        <td>{{cdt.salary}}</td>
                        <td>{{cdt.housing_sum}}</td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'fine')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.fine}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'bhp_form')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.bhp_form}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'stay_cards_cost')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.stay_cards_cost}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'premium')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.premium}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'recommendation')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.recommendation}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'transport')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.transport}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'work_permits')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.work_permits}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td class="cell">
                            <button
                                v-if="!cdt.is_position"
                                @click="openAddModal(cdt, 'prepayment')"
                                class="cell__add-btn btn btn-icon"
                            >
                                {{cdt.prepayment}}
                                <span class="svg-icon svg-icon-primary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        version="1.1"
                                    >
                                        <g
                                            stroke="none"
                                            stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd"
                                        >
                                            <rect
                                                x="0"
                                                y="0"
                                                width="24"
                                                height="24"
                                            ></rect>
                                            <circle
                                                fill="currentColor"
                                                opacity="0.3"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path
                                                d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z"
                                                fill="currentColor"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                        <td :class="{'main-color': cdt.payoff < 0}">{{cdt.payoff}}</td>
                        <td>
                            <div
                                v-if="!cdt.is_position"
                                class="form-check form-check-sm form-check-custom form-check-solid justify-content-center"
                            >
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    @change="completeLog(cdt, $event)"
                                    :checked="cdt.completed"
                                    :disabled="cdt.completed && userRole !== '1'"
                                >
                            </div>
                        </td>
                    </tr>

                </tbody>
                <!--end::Table body-->
            </table>
            <div class="dataTable_bottom">
                <div
                    class="dataTables_length"
                    id="WorkLogsTable_length"
                ><label><select
                            name="WorkLogsTable_length"
                            aria-controls="WorkLogsTable"
                            class="form-select form-select-sm form-select-solid"
                            v-model="length"
                        >
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select></label></div>
                <div
                    class="dataTables_info"
                    id="WorkLogsTable_info"
                    role="status"
                    aria-live="polite"
                >Показано {{recordsShown}} из {{recordsFiltered}} записей</div>
                <div
                    class="dataTables_paginate paging_numbers"
                    id="WorkLogsTable_paginate"
                    style="display: none;"
                >
                    <ul class="pagination">
                        <li class="paginate_button page-item active"><a
                                href="#"
                                aria-controls="WorkLogsTable"
                                data-dt-idx="0"
                                tabindex="0"
                                class="page-link"
                            >1</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div
        ref="workTimeModal"
        class="modal fade worklog-modal"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Время</h3>

                    <!--begin::Close-->
                    <div
                        class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="required fs-5 fw-bold mb-2">Отработаное время</label>
                        <input
                            class="form-control form-control-sm form-control-solid"
                            ref="worklogHoursInput"
                            type="text"
                            v-model="editingDay.work_time"
                        >
                    </div>

                    <div class="mt-10">
                        <div
                            class="no-edit-block mt-10"
                            :class="{edit: editRate}"
                        >
                            <div class="mt-5">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="form-label">Должность</label>
                                    {{editingDay.position_name}}
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="form-label">Ставка</label>
                                        {{editingDay.rate}}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="form-label">Жилье</label>
                                        {{editingDay.housing}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light btn-sm"
                        data-bs-dismiss="modal"
                    >Отмена</button>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="saveDay"
                    >Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
        ref="addDataModal"
        class="modal fade worklog-modal"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{addModal.title}}</h3>

                    <!--begin::Close-->
                    <div
                        class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>
                <!--begin::Modal body-->
                <div
                    v-if="addModal.open"
                    class="modal-body"
                >
                    <div class="row">
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="required fs-5 fw-bold mb-2">Дата</label>
                                <JqFlatpickr @change-date="(d) => addition.date = d" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="required fs-5 fw-bold mb-2">Сумма</label>
                                <input
                                    class="form-control form-control-sm form-control-solid"
                                    type="text"
                                    v-model="addition.amount"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-0 fv-row mt-5">
                        <label class="fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea
                            class="form-control form-control-sm form-control-solid"
                            cols="30"
                            v-model="addition.comment"
                        ></textarea>
                    </div>

                    <div class="mt-10">
                        <PgDropzone
                            filescount="5"
                            @uploaded="(d) => addition.files = d"
                        />
                    </div>

                </div>
                <!--end::Modal body-->
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light btn-sm"
                        data-bs-dismiss="modal"
                    >Отмена</button>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="saveAddition"
                    >Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.worklog-table {
    .day-cell {
        border-right: 1px solid #ddd;
        text-align: center;
        position: relative;
        min-width: 3.6rem;
        &_0,
        &_6 {
            color: #ff5612;
        }
        &__btn {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            background: #fff;
            opacity: 0;
            &:hover {
                opacity: 1;
            }
            svg {
                width: 1.5rem;
                height: 1.5rem;
            }
        }
    }
    .cell {
        position: relative;
        &__add-btn {
            color: #7e8299;
            width: 100%;
            .svg-icon {
                opacity: 0;
                transition: 0.21s;
                padding-left: 3px;
            }
            &:hover {
                .svg-icon {
                    opacity: 1;
                }
            }
            svg {
                width: 1.7rem;
                height: 1.7rem;
                transform: rotate(45deg);
            }
        }
    }
    .prev-day-cell {
        border-right: 1px solid #ddd;
    }
    table {
        border-top: 1px solid #ddd !important;
    }
    tbody,
    tr {
        border-bottom: 1px solid #ddd !important;
    }
    .form-check-input:disabled {
        opacity: 1;
    }
}

.worklog-modal {
    display: block;
    visibility: hidden;
    opacity: 0;
    transition: 0.21s;
    background: rgba(0, 0, 0, 0.3);
    &.show {
        visibility: visible;
        opacity: 1;
    }
    .no-edit-block {
        pointer-events: none;
        opacity: 0.5;
        &.edit {
            pointer-events: all;
            opacity: 1;
        }
    }
}
</style>