<script>
import axios from "axios";
import JqFlatpickr from "./jq-flatpickr.vue";
import PgDropzone from "./pg-dropzone.vue";

export default {
    name: 'AdditionWorklog',
    props: ['btnSelector', 'editBtnSelector'],
    components: { JqFlatpickr, PgDropzone },
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
            fp: null,
            addModal: {
                open: false,
            },
            addition: {},
            positions: [],
            positionSelect: null,
        };
    },
    mounted() {
        $(this.$refs.addDataModal).on('hidden.bs.modal', () => {
            if (window.dTables) {
                window.dTables.forEach(tbl => {
                    tbl.draw();
                });
            }

            this.addModal.open = false;
            this.addition = {};
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest(this.btnSelector);
            const editBtn = e.target.closest(this.editBtnSelector);

            if (btn) {
                e.preventDefault();

                if (!this.addModal.open) {
                    const cdt = {
                        candidate_id: btn.getAttribute('data-candidate-id'),
                        id: btn.getAttribute('data-worklog-id'),
                        period: btn.getAttribute('data-period'),
                    };

                    this.openAddModal(cdt, btn.getAttribute('data-type'));
                }
            } else if (editBtn) {
                e.preventDefault();

                const cdt = {
                    addition_id: editBtn.getAttribute('data-id'),
                };

                this.openAddModal(cdt, editBtn.getAttribute('data-type'), true);
            }
        });
    },
    methods: {
        openAddModal(cdt, type, isEdit) {
            this.addition = {};
            
            if (!isEdit) {
                this.addModal.open = true;
            }

            this.addition.type = type;
            this.addition.candidate_id = cdt.candidate_id;
            this.addition.work_log_id = cdt.id;
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
                oswiadczenie: 'Освядчение',
            }

            this.addModal.title = titles[type];

            setTimeout(() => {
                $(this.$refs.addDataModal).modal('show');

                if (isEdit) {
                    axios.get('/work-logs/additions/item/json/' + cdt.addition_id)
                        .then(({ data }) => {
                            this.addition = data;

                            this.addition.extistFiles = data.files?.map(f => {
                                f.name = f.original_name;
                                return f;
                            });

                            this.addition.files = null;
                            this.addModal.open = true;
                        });
                }
            }, 100);
        },
        saveAddition() {
            const fD = new FormData;

            fD.append('type', this.addition.type);
            fD.append('candidate_id', this.addition.candidate_id);
            fD.append('period', this.addition.period);
            fD.append('work_log_id', this.addition.work_log_id || '');
            fD.append('date', this.addition.date || '');
            fD.append('comment', this.addition.comment || '');
            fD.append('amount', this.addition.amount || '');

            if (this.addition.id) {
                fD.append('id', this.addition.id);
            }

            this.addition.files?.files.forEach(f => {
                if (f.type && f.size) {
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
                    }
                });
        },
    }
}
</script>

<template>
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
                                <JqFlatpickr
                                    :date="addition.date"
                                    @change-date="(d) => addition.date = d"
                                />
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
                            :exist-files="addition.extistFiles"
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
}
</style>