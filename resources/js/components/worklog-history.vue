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
                        <th class="prev-day-cell">Должность</th>
                        <th
                            v-for="day in calendar"
                            :key="day.date"
                            :class="'day-cell day-cell_' + day.dayNum"
                        >{{day.date}}<br>{{day.dayName}}</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    <tr
                        v-for="(logItem, li) in worklogData"
                        :key="li"
                    >
                        <td class="prev-day-cell">{{logItem.position.title}}</td>
                        <td
                            v-for="(dayItem, di) in logItem.work_log_days"
                            :key="di"
                            class="day-cell"
                        >{{dayItem.work_time || '--'}}
                            <button
                                v-if="dayItem.work_time != ''"
                                @click="openInfoModal(dayItem)"
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
                                            <polygon points="0 0 24 0 24 24 0 24" />
                                            <path
                                                d="M3.52270623,14.028695 C2.82576459,13.3275941 2.82576459,12.19529 3.52270623,11.4941891 L11.6127629,3.54050571 C11.9489429,3.20999263 12.401513,3.0247814 12.8729533,3.0247814 L19.3274172,3.0247814 C20.3201611,3.0247814 21.124939,3.82955935 21.124939,4.82230326 L21.124939,11.2583059 C21.124939,11.7406659 20.9310733,12.2027862 20.5869271,12.5407722 L12.5103155,20.4728108 C12.1731575,20.8103442 11.7156477,21 11.2385688,21 C10.7614899,21 10.3039801,20.8103442 9.9668221,20.4728108 L3.52270623,14.028695 Z M16.9307214,9.01652093 C17.9234653,9.01652093 18.7282432,8.21174298 18.7282432,7.21899907 C18.7282432,6.22625516 17.9234653,5.42147721 16.9307214,5.42147721 C15.9379775,5.42147721 15.1331995,6.22625516 15.1331995,7.21899907 C15.1331995,8.21174298 15.9379775,9.01652093 16.9307214,9.01652093 Z"
                                                fill="currentColor"
                                                fill-rule="nonzero"
                                                opacity="0.7"
                                            />
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </td>
                    </tr>

                </tbody>
                <!--end::Table body-->
            </table>
        </div>
    </div>

    <div
        ref="infoModal"
        class="modal fade worklog-modal"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{infoData.dateFormated}}</h3>

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
                    <div class="row">
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Отработаное время</label>
                                {{infoData.work_time}}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Ставка</label>
                                {{infoData.rate}}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Жилье за сутки</label>
                                {{infoData.housing}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: 'WorklogHistory',
    props: ['candidateId', 'daysOfWeek'],
    components: {},
    data() {
        return {
            worklogData: [],
            calendar: [],
            infoData: {},
        };
    },
    watch: {

    },
    mounted() {
        this.getWorklog();

        document.querySelector('input[data-js-filter="work_log_history"]')
            .addEventListener('change', (e) => {
                this.getWorklog(e.target.value);
            });
    },
    methods: {
        getWorklog(period = '') {
            axios.get('/candidate/work-logs-history/json', {
                params: {
                    candidate_id: this.candidateId,
                    period,
                }
            }).then(({ data }) => {
                if (data.error) {
                    toastr.error(data.error);
                    return;
                }

                this.worklogData = data.positions;

                this.buildCalendar(data.work_log_days);
            });
        },
        buildCalendar(days) {
            const daysOfWeek = JSON.parse(this.daysOfWeek);
            this.calendar = [];

            days.forEach(d => {
                const today = new Date(d.date);
                const dayNum = today.getDay();

                this.calendar.push({
                    date: today.getDate(),
                    dayNum: dayNum,
                    dayName: daysOfWeek[dayNum],
                });
            });
        },
        openInfoModal(infoData) {
            this.infoData = infoData;
            $(this.$refs.infoModal).modal('show');
        }
    }
}
</script>

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