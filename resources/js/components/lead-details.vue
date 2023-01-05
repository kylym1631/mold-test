<template>
    <div
        v-if="visible"
        class="card lead-details"
    >
        <div>
            <!-- <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <h4 class="m-0">Детали лида</h4>
                </div>
                <button
                    class="btn btn-icon btn-sm btn-active-light-primary ms-2 close-details-btn-icon"
                    @click="visible = false"
                >
                    <span class="svg-icon svg-icon-2x"></span>
                </button>
            </div>
            <div class="card-body pt-0 pb-0">
                <table class="table align-middle table-row-dashed fs-6 gy-3">
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td>Имя</td>
                            <td id="modal_lead_name">{{lead.name}}</td>
                        </tr>
                        <tr>
                            <td>Телефон</td>
                            <td id="modal_lead_phone"><span
                                    class="d-flex"
                                    style="align-items: center"
                                ><a :href="'tel:+' + lead.phone">+{{lead.phone}}</a><button
                                        class="btn js-copy p-0"
                                        style="margin-left: 10px; color: #FF5612;"
                                    > <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                        >
                                            <path
                                                opacity="0.5"
                                                d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z"
                                                fill="currentColor"
                                            ></path>
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z"
                                                fill="currentColor"
                                            ></path>
                                        </svg> </button></span></td>
                        </tr>
                        <tr v-if="lead.viber">
                            <td>Viber</td>
                            <td id="modal_lead_viber"><span
                                    class="d-flex"
                                    style="align-items: center"
                                ><a :href="'viber:+' + lead.viber">+{{lead.viber}}</a><button
                                        class="btn js-copy p-0"
                                        style="margin-left: 10px; color: #FF5612;"
                                    > <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                        >
                                            <path
                                                opacity="0.5"
                                                d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z"
                                                fill="currentColor"
                                            ></path>
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z"
                                                fill="currentColor"
                                            ></path>
                                        </svg> </button></span></td>
                        </tr>
                        <tr>
                            <td>Компания</td>
                            <td id="modal_lead_company">{{lead.company}}</td>
                        </tr>
                    </tbody>
                </table>
            </div> -->

            <div class="card-footer formated-text">
                {{statusInfo}}
            </div>
        </div>

        <!-- <form @submit.prevent="saveDetails">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <h4 class="m-0">Контакты</h4>
                </div>
            </div>
            <div class="card-body pt-0">
                <div
                    class="lead-contact"
                    v-for="(contact, i) in contacts"
                    :key="contact"
                >
                    <div class="row mb-5">
                        <div class="col-3">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Контактное лицо</label>
                                <select
                                    class="form-select form-select-sm form-select-solid"
                                    v-model="contact.type"
                                >
                                    <option value="own">Личный</option>
                                    <option value="agent">Контактное лицо</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Имя</label>
                                <input
                                    v-model="contact.first_name"
                                    class="form-control form-control-sm form-control-solid"
                                    type="text"
                                />
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Фамилия</label>
                                <input
                                    v-model="contact.last_name"
                                    class="form-control form-control-sm form-control-solid"
                                    type="text"
                                />
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Email</label>
                                <input
                                    v-model="contact.email"
                                    class="form-control form-control-sm form-control-solid"
                                    type="text"
                                    name="email[]"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col">
                            <div class="row mb-5 align-items-center">
                                <div class="col-3">
                                    <div
                                        class="d-flex form-check form-check-sm form-check-custom form-check-solid"
                                        style="align-items: center; gap: 10px"
                                    >
                                        <input
                                            :id="'checkbox-viber-' + i"
                                            :checked="number['viber-'+ i]"
                                            class="form-check-input"
                                            type="checkbox"
                                            @change="toggleNumber('viber', i, $event)"
                                        >
                                        <label :for="'checkbox-viber-' + i">Viber</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div
                                        class="d-flex flex-column mb-0 fv-row"
                                        v-if="number['viber-'+ i]"
                                    >
                                        <input
                                            v-model="contact.numbers.viber"
                                            class="form-control form-control-sm form-control-solid"
                                            type="text"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5 align-items-center">
                                <div class="col-3">
                                    <div
                                        class="d-flex form-check form-check-sm form-check-custom form-check-solid"
                                        style="align-items: center; gap: 10px"
                                    >
                                        <input
                                            :id="'checkbox-whatsapp' + i"
                                            :checked="number['whatsapp-'+ i]"
                                            class="form-check-input"
                                            type="checkbox"
                                            @change="toggleNumber('whatsapp', i, $event)"
                                        >
                                        <label :for="'checkbox-whatsapp' + i">Whatsapp</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div
                                        class="d-flex flex-column mb-0 fv-row"
                                        v-if="number['whatsapp-'+ i]"
                                    >
                                        <input
                                            v-model="contact.numbers.whatsapp"
                                            class="form-control form-control-sm form-control-solid"
                                            type="text"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5 align-items-center">
                                <div class="col-3">
                                    <div
                                        class="d-flex form-check form-check-sm form-check-custom form-check-solid"
                                        style="align-items: center; gap: 10px"
                                    >
                                        <input
                                            :id="'checkbox-telegram' + i"
                                            :checked="number['telegram-'+ i]"
                                            class="form-check-input"
                                            type="checkbox"
                                            @change="toggleNumber('telegram', i, $event)"
                                        >
                                        <label :for="'checkbox-telegram' + i">Telegram</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div
                                        class="d-flex flex-column mb-0 fv-row"
                                        v-if="number['telegram-'+ i]"
                                    >
                                        <input
                                            v-model="contact.numbers.telegram"
                                            class="form-control form-control-sm form-control-solid"
                                            type="text"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="col"
                            style="text-align:right"
                        >
                            <button
                                style="margin-top: 28px;"
                                type="button"
                                class="btn btn-light btn-sm"
                                @click="removeContact(i)"
                            >Удалить</button>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col">
                        <button
                            type="button"
                            class="btn btn-warning btn-sm"
                            @click="addNewContact"
                        >Добавить</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Специальность</label>
                            <select
                                class="form-select form-select-sm form-select-solid"
                                v-model="lead.speciality_id"
                            >
                                <option
                                    v-for="spec in speciality"
                                    :key="spec.id"
                                    :value="spec.id"
                                >{{spec.value}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-15">
                    <button
                        type="submit"
                        class="btn btn-primary btn-sm"
                        :disabled="submiting"
                        :data-kt-indicator="submiting ? 'on' : 'off'"
                    >
                        <span class="indicator-label">Сохранить</span>
                        <span class="indicator-progress">
                            Сохранение...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </form> -->
    </div>
</template>

<script>
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
    name: 'LeadDetails',
    props: ['btnselector'],
    mounted() {
        document.addEventListener('click', (e) => {
            const showDetailsBtn = e.target.closest(this.btnselector);

            if (showDetailsBtn) {
                e.preventDefault();

                if (!this.visible) {
                    this.showDetails(showDetailsBtn);
                }
            }
        });
    },
    data() {
        return {
            visible: false,
            contacts: [],
            number: {},
            lead_id: null,
            lead: {},
            speciality: [],
            submiting: false,
            statusInfo: '',
        };
    },
    methods: {
        showDetails(btn) {
            this.contacts = [];
            this.number = {};
            this.lead = {};
            this.statusInfo = '';

            this.lead_id = btn.getAttribute('data-lead-id');

            axios.get('/leads/ajax/id/' + this.lead_id)
                .then(({ data }) => {
                    if (data) {
                        if (data.error) {
                            toastr.error(data.error);
                            return;
                        }

                        if (data.lead) {
                            this.visible = true;
                            this.lead = data.lead;

                            if (data.lead.contacts) {
                                data.lead.contacts.forEach((cItem, i) => {
                                    const numbers = JSON.parse(cItem.numbers);

                                    for (const item of Object.keys(numbers)) {
                                        this.number[item + '-' + i] = true;
                                    }

                                    cItem.numbers = numbers;

                                    this.contacts.push(cItem);
                                });
                            }

                            this.getSpeciality();
                            this.getStatusInfo();
                        }
                    }
                });
        },
        getSpeciality() {
            axios.get('/search/speciality')
                .then(({ data }) => {
                    if (data) {
                        this.speciality = data;
                    }
                });
        },
        getStatusInfo() {
            axios.get('/leads/status-inform/' + (this.lead.status === null ? '0' : this.lead.status))
                .then(({ data }) => {
                    if (data) {
                        this.statusInfo = data.data.info;
                    }
                });
        },
        toggleNumber(sel, i, e) {
            if (this.number[sel + '-' + i] === undefined) {
                this.number[sel + '-' + i] = false;
            }

            this.number[sel + '-' + i] = e.target.checked;

            if (!e.target.checked && this.contacts[i].numbers[sel]) {
                delete this.contacts[i].numbers[sel];
            }
        },
        addNewContact() {
            this.contacts.push({
                first_name: '',
                last_name: '',
                email: '',
                numbers: {},
            });
        },
        removeContact(i) {
            this.contacts.splice(i, 1);
        },
        saveDetails() {
            this.submiting = true;

            axios.post('/leads/details/store', {
                lead_id: this.lead_id,
                contacts: this.contacts,
                speciality_id: this.lead.speciality_id,
            }).then(({ data }) => {
                if (data.error) {
                    toastr.error(data.error);
                    this.submiting = false;
                    return;
                }

                if (data.contacts) {
                    this.contacts = data.contacts;
                }

                setTimeout(() => {
                    toastr.success('Успешно');
                    this.submiting = false;
                }, 500);
            });
        }
    },
}
</script>

<style lang="scss">
.lead-details {
    box-shadow: 0 0 20px 0 rgb(76 87 125 / 21%);
}
.lead-contact {
    margin-top: 2rem;
    & + .lead-contact {
        border-top: 1px solid #eff2f5;
        padding-top: 2rem;
    }
}
.close-details-btn-icon {
    background-color: transparent !important;
    -webkit-appearance: none;
    background: url("/assets/media/icons/duotune/general/gen040.svg") no-repeat;
    background-size: 100%;
    opacity: 0.25;
    transition: 0.21s;

    &:hover {
        opacity: 0.35;
    }
}
.formated-text{
    white-space: pre-wrap;
}
</style>