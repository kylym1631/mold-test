<script>
import axios from "axios";
import JqFlatpickr from "./jq-flatpickr.vue";
import PgDropzone from "./pg-dropzone.vue";
import Select2Comp from "./select2-comp.vue";

export default {
    name: 'CreateEdit',
    props: ['createBtnSelector', 'editBtnSelector'],
    components: { JqFlatpickr, PgDropzone, Select2Comp },
    data() {
        return {
            modalData: {
                open: false,
                tpl: '',
                title: '',
                storeEndpoint: '',
                updateEndpoint: '',
                getEndpoint: '',
                refreshTable: '',
            },
            formData: {},
            isEditMode: false,
        };
    },
    mounted() {
        $(this.$refs.modalEl).on('hidden.bs.modal', () => {
            if (this.modalData.refreshTable && window.dTables) {
                window.dTables.forEach(tbl => {
                    if (tbl.context[0].sInstance == this.modalData.refreshTable) {
                        tbl.draw();
                    }
                });
            }

            this.modalData = {
                open: false,
                title: '',
                tpl: '',
                storeEndpoint: '',
                updateEndpoint: '',
                getEndpoint: '',
                refreshTable: '',
            };

            this.formData = {};
            this.isEditMode = false;
        });

        document.addEventListener('click', (e) => {
            const createBtn = e.target.closest(this.createBtnSelector);
            const editBtn = e.target.closest(this.editBtnSelector);

            if (createBtn || editBtn) {
                e.preventDefault();
                const btn = createBtn || editBtn;

                if (!this.modalData.open) {
                    const btnData = {
                        modalTpl: btn.getAttribute('data-modal-tpl'),
                        candidateId: btn.getAttribute('data-candidate-id'),
                        modalTitle: btn.getAttribute('data-modal-title'),
                        storeEndpoint: btn.getAttribute('data-store-endpoint'),
                        updateEndpoint: btn.getAttribute('data-update-endpoint'),
                        getEndpoint: btn.getAttribute('data-get-endpoint'),
                        refreshTable: btn.getAttribute('data-refresh-table'),
                    };

                    if (editBtn) {
                        this.isEditMode = true;
                    }

                    this.openModal(btnData);
                }
            }
        });
    },
    methods: {
        openModal(btnData) {
            this.modalData.tpl = btnData.modalTpl;
            this.modalData.title = btnData.modalTitle || '';
            this.modalData.storeEndpoint = btnData.storeEndpoint || '';
            this.modalData.updateEndpoint = btnData.updateEndpoint || '';
            this.modalData.getEndpoint = btnData.getEndpoint || '';
            this.modalData.refreshTable = btnData.refreshTable || '';

            this.formData.candidate_id = btnData.candidateId || '';

            if (this.isEditMode) {
                this.getData(() => {
                    setTimeout(() => {
                        this.modalData.open = true;
                        $(this.$refs.modalEl).modal('show');
                    }, 100);
                });
            } else {
                setTimeout(() => {
                    this.modalData.open = true;
                    $(this.$refs.modalEl).modal('show');
                }, 100);
            }
        },
        getData(callback) {
            axios.get(this.modalData.getEndpoint)
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                        return;
                    }

                    this.formData = data;

                    this.formData.extistFiles = data.files?.map(f => {
                        f.name = f.original_name;
                        return f;
                    });

                    this.formData.files = null;

                    callback();
                });
        },
        saveData() {
            const fD = new FormData;

            fD.append('_token', document.querySelector('input[name="_token"]').value);

            for (const key in this.formData) {
                if (Object.hasOwnProperty.call(this.formData, key)) {
                    const val = this.formData[key];

                    if (key == 'files') {
                        if (val) {
                            val.files?.forEach(f => {
                                if (f.type && f.size) {
                                    fD.append('file[]', f, f.name);
                                }
                            });

                            val.toRemove?.forEach(name => {
                                fD.append('to_delete_files[]', name);
                            });
                        }
                    } else if (val != null) {
                        fD.append(key, val);
                    }
                }
            }

            axios.post(this.isEditMode ? this.modalData.updateEndpoint : this.modalData.storeEndpoint, fD)
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('Успешно');
                        $(this.$refs.modalEl).modal('hide');
                    }
                });
        },
    }
}
</script>

<template>
    <div
        ref="modalEl"
        class="modal fade worklog-modal"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{modalData.title}}</h3>

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

                <div v-if="modalData.open">

                    <div
                        v-if="modalData.tpl == 'oswiadczenie'"
                        class="modal-body"
                    >
                        <div class="row">
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Дата подачи</label>
                                    <JqFlatpickr
                                        :date="formData.date"
                                        @change-date="(d) => formData.date = d"
                                    />
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Стоимость</label>
                                    <input
                                        class="form-control form-control-sm form-control-solid"
                                        type="text"
                                        v-model="formData.cost"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mt-10">
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Минимальное количество часов</label>
                                    <input
                                        class="form-control form-control-sm form-control-solid"
                                        type="text"
                                        v-model="formData.min_hours"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <PgDropzone
                                filescount="5"
                                :exist-files="formData.extistFiles"
                                @uploaded="(f) => formData.files = f"
                            />
                        </div>
                    </div>

                    <div
                        v-if="modalData.tpl == 'legalisation'"
                        class="modal-body"
                    >
                        <div class="d-flex flex-column fv-row mb-5">
                            <label class="required fs-5 fw-bold mb-2">Тип документа</label>
                            <Select2Comp
                                placeholder="Тип документа"
                                ajaxEndpoint="/search/candidate/typedocs"
                                v-model="formData.doc_type_id"
                            >
                                <option
                                    v-if="formData.doc_type_id"
                                    :value="formData.doc_type_id"
                                >
                                    {{formData.type?.name}}
                                </option>
                            </Select2Comp>
                        </div>

                        <div class="row mb-5">
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Название документа</label>
                                    <input
                                        tabindex="-1"
                                        class="form-control form-control-sm form-control-solid"
                                        type="text"
                                        v-model="formData.title"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Кем выдан</label>
                                    <input
                                        tabindex="-1"
                                        class="form-control form-control-sm form-control-solid"
                                        type="text"
                                        v-model="formData.who_issued"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Дата выдачи</label>
                                    <JqFlatpickr
                                        :date="formData.issue_date"
                                        @change-date="(d) => formData.issue_date = d"
                                    />
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Номер документа</label>
                                    <input
                                        tabindex="-1"
                                        class="form-control form-control-sm form-control-solid"
                                        type="text"
                                        v-model="formData.number"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Дата, от</label>
                                    <JqFlatpickr
                                        :date="formData.date_from"
                                        @change-date="(d) => formData.date_from = d"
                                    />
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex flex-column mb-0 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Дата, до</label>
                                    <JqFlatpickr
                                        :date="formData.date_to"
                                        @change-date="(d) => formData.date_to = d"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <PgDropzone
                                title="Загрузить документ"
                                types=".jpeg, .jpg, .png, .pdf"
                                filescount="1"
                                :exist-files="formData.extistFiles"
                                @uploaded="(f) => formData.files = f"
                            />
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light btn-sm"
                        data-bs-dismiss="modal"
                    >Отмена</button>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="saveData"
                    >Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
