<script>
import axios from 'axios';
import JqFlatpickr from './jq-flatpickr.vue';
import Select2Comp from './select2-comp.vue';

export default {
    name: 'TasksManager',
    props: ['oneTaskMode', 'tasksChainMode', 'userId', 'roles', 'templateId', 'editMode', 'createMode', 'templates'],
    components: {
        JqFlatpickr, Select2Comp,
    },
    data() {
        return {
            title: '',
            description: '',
            items: [{
                title: '',
                end: '',
                to_user_roles: [],
                to_user_ids: [],
                model_name: '',
                model_obj_id: '',
                model_obj_title: '',
                // userOptions: [],
                all_users: '1',
                start_delay: null,
                end_delay: null,
                task_template_id: null,
                step_id: this.userId + '.' + Date.now() + '.' + Math.floor(Math.random() * 100),
            }],
            models: {
                lead: {
                    name: 'Лид',
                    value: 'lead',
                    ajaxEp: '/search/leads',
                },
                candidate: {
                    name: 'Кандидат',
                    value: 'candidate',
                    ajaxEp: '/search/candidates',
                },
                vacancy: {
                    name: 'Вакансия',
                    value: 'vacancy',
                    ajaxEp: '/search/vacancy',
                },
                housing: {
                    name: 'Жилье',
                    value: 'housing',
                    ajaxEp: '/search/housing',
                },
                client: {
                    name: 'Клиент',
                    value: 'client',
                    ajaxEp: '/search/client',
                },
                car: {
                    name: 'Машина',
                    value: 'car',
                    ajaxEp: '/search/car',
                },
            },
            sending: false,
            rolesObj: [],
            usersObj: [],
            taskTemplates: [],
            startOptions: [
                { v: '0', n: 'Сразу после предыдущей' },
                { v: '15', n: 'Через 15 минут' },
                { v: '30', n: 'Через 30 минут' },
                { v: '45', n: 'Через 45 минут' },
                { v: '60', n: 'Через 1 час' },
                { v: '120', n: 'Через 2 часа' },
                { v: '180', n: 'Через 3 часа' },
                { v: '360', n: 'Через 6 часов' },
                { v: '720', n: 'Через 12 часов' },
                { v: '1440', n: 'Через день' },
                { v: '2880', n: 'Через 2 дня' },
                { v: '4320', n: 'Через 3 дня' },
            ],
            endOptions: [
                { v: '15', n: '15 минут' },
                { v: '30', n: '30 минут' },
                { v: '45', n: '45 минут' },
                { v: '60', n: '1 час' },
                { v: '120', n: '2 часа' },
                { v: '180', n: '3 часа' },
                { v: '360', n: '6 часов' },
                { v: '720', n: '12 часов' },
                { v: '1440', n: '1 день' },
                { v: '2880', n: '2 дня' },
                { v: '4320', n: '3 дня' },
            ],
            tab: {},
        };
    },
    mounted() {
        this.rolesObj = JSON.parse(this.roles);
        this.usersObj = this.rolesObj.reduce((acc, curr) => acc.concat(curr.users), []);
        this.taskTemplates = this.templates && JSON.parse(this.templates);

        if (this.templateId) {
            axios.get('/tasks/templates/' + this.templateId + '/json')
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                        return;
                    }

                    this.title = data.title;
                    this.description = data.description;
                    this.items = data.items;
                });
        }
    },
    methods: {
        userRolesChanged(i) {
            // this.items[i].userOptions = [];

            // for (const role of this.rolesObj) {
            //     if (this.items[i].to_user_roles.includes(String(role.id))) {
            //         this.items[i].userOptions = this.items[i].userOptions.concat(role.users);
            //     }
            // }
        },
        addTaskStep(i) {
            this.items.splice(i, 0, {
                title: '',
                end: '',
                to_user_roles: [],
                to_user_ids: [],
                model_name: '',
                model_obj_id: '',
                model_obj_title: '',
                // userOptions: [],
                all_users: '1',
                start_delay: null,
                end_delay: null,
                step_id: this.userId + '.' + Date.now() + '.' + Math.floor(Math.random() * 100),
            });
        },
        moveUp(i) {
            const allItems = JSON.parse(JSON.stringify(this.items));
            const top = this.items[i - 1];
            const bot = this.items[i];

            allItems[i - 1] = bot;
            allItems[i] = top;

            this.items = allItems;
        },
        moveDown(i) {
            const allItems = JSON.parse(JSON.stringify(this.items));
            const top = this.items[i];
            const bot = this.items[i + 1];

            allItems[i] = bot;
            allItems[i + 1] = top;

            this.items = allItems;
        },
        clickRadio(i) {
            if (this.items[i].model_name) {
                this.items[i].model_name = '';
            }
        },
        saveData() {
            this.sending = true;
            let data = {};

            if (this.oneTaskMode) {
                data = this.items[0];
                data._token = document.querySelector('input[name=_token]').value;

                axios.post('/tasks/store', data)
                    .then(({ data }) => {
                        if (data.error) {
                            toastr.error(data.error);
                        } else {
                            toastr.success('Успешно');
                            window.location.replace('/tasks/all');
                        }

                        this.sending = false;

                    });

            } else if (this.tasksChainMode) {
                data = {
                    title: this.title,
                    description: this.description,
                    items: this.items,
                    _token: document.querySelector('input[name=_token]').value,
                };

                let url = '/tasks/templates/store';

                if (this.templateId) {
                    url = '/tasks/templates/update';
                    data.id = this.templateId;
                }

                axios.post(url, data)
                    .then(({ data }) => {
                        if (data.error) {
                            toastr.error(data.error);
                        } else {
                            toastr.success('Успешно');
                            window.location.replace('/tasks/templates');
                        }

                        this.sending = false;

                    });
            }
        }
    },
}
</script>

<template>
    <div
        :style="{'pointer-events': (!editMode && !createMode ? 'none' : '')}"
        :class="{'view-task-template': !editMode && !createMode}"
    >
        <div
            class="row mb-5"
            v-if="tasksChainMode"
        >
            <div class="col">
                <div class="d-flex flex-column mb-5 fv-row">
                    <label class="required fs-5 fw-bold mb-2">Название шаблона</label>
                    <input
                        class="form-control form-control-sm form-control-solid"
                        type="text"
                        v-model="title"
                    >
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <label class="fs-5 fw-bold mb-2">Описание</label>
                    <textarea
                        class="form-control form-control-sm form-control-solid"
                        cols="20"
                        rows="3"
                        v-model="description"
                    ></textarea>
                </div>
            </div>
        </div>

        <div
            class="mb-10"
            v-if="tasksChainMode && (editMode || createMode)"
        >
            <button
                type="button"
                class="btn btn-primary btn-sm btn-xs"
                @click="addTaskStep(0)"
            >
                Добавить задачу
            </button>
        </div>

        <div
            v-for="(item, i) in items"
            :key="item.step_id"
            class="mb-10"
        >
            <div
                v-if="tasksChainMode"
                class="task-step"
            >
                <span>{{i + 1}}</span>
                <div class="task-step__title">{{item.title}}</div>
                <hr>
                <button
                    class="task-step__slide-btn"
                    :style="{transform: tab[item.step_id] ? 'rotate(180deg)' : ''}"
                    @click="() => tab[item.step_id] = !tab[item.step_id]"
                >
                    <span class="svg-icon svg-icon-primary svg-icon-2x">
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
                </button>
                <div
                    class="task-step__sort-buttons"
                    v-if="tasksChainMode && (editMode || createMode)"
                >
                    <button
                        class="task-step__top-btn"
                        v-if="i > 0"
                        @click="moveUp(i)"
                    >
                        <span class="svg-icon svg-icon-primary svg-icon-2">
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
                                        d="M9.35321926,16.3736278 L16.3544311,10.3706602 C16.5640654,10.1909158 16.5882961,9.87526197 16.4085517,9.66562759 C16.3922584,9.64662485 16.3745611,9.62887247 16.3556091,9.6125202 L9.35439731,3.57169798 C9.14532254,3.39130299 8.82959492,3.41455255 8.64919993,3.62362732 C8.5708616,3.71442013 8.52776329,3.83034375 8.52776329,3.95026134 L8.52776329,15.9940512 C8.52776329,16.2701936 8.75162092,16.4940512 9.02776329,16.4940512 C9.14714624,16.4940512 9.2625893,16.4513356 9.35321926,16.3736278 Z"
                                        fill="currentColor"
                                        transform="translate(12.398118, 9.870355) rotate(-450.000000) translate(-12.398118, -9.870355) "
                                    />
                                    <rect
                                        fill="currentColor"
                                        opacity="0.3"
                                        transform="translate(12.500000, 17.500000) scale(-1, 1) rotate(-270.000000) translate(-12.500000, -17.500000) "
                                        x="11"
                                        y="11"
                                        width="3"
                                        height="13"
                                        rx="0.5"
                                    />
                                </g>
                            </svg>
                        </span>
                    </button>
                    <button
                        class="task-step__down-btn"
                        v-if="i < items.length - 1"
                        @click="moveDown(i)"
                    >
                        <span class="svg-icon svg-icon-primary svg-icon-2">
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
                                        d="M9.35321926,16.3736278 L16.3544311,10.3706602 C16.5640654,10.1909158 16.5882961,9.87526197 16.4085517,9.66562759 C16.3922584,9.64662485 16.3745611,9.62887247 16.3556091,9.6125202 L9.35439731,3.57169798 C9.14532254,3.39130299 8.82959492,3.41455255 8.64919993,3.62362732 C8.5708616,3.71442013 8.52776329,3.83034375 8.52776329,3.95026134 L8.52776329,15.9940512 C8.52776329,16.2701936 8.75162092,16.4940512 9.02776329,16.4940512 C9.14714624,16.4940512 9.2625893,16.4513356 9.35321926,16.3736278 Z"
                                        fill="currentColor"
                                        transform="translate(12.398118, 9.870355) rotate(-450.000000) translate(-12.398118, -9.870355) "
                                    />
                                    <rect
                                        fill="currentColor"
                                        opacity="0.3"
                                        transform="translate(12.500000, 17.500000) scale(-1, 1) rotate(-270.000000) translate(-12.500000, -17.500000) "
                                        x="11"
                                        y="11"
                                        width="3"
                                        height="13"
                                        rx="0.5"
                                    />
                                </g>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <div v-if="tab[item.step_id] || oneTaskMode">
                <div class="row mb-5">
                    <div :class="oneTaskMode ? 'col-8' : 'col-12'">
                        <div class="d-flex flex-column mb-5 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Описание задачи</label>
                            <input
                                class="form-control form-control-sm form-control-solid"
                                type="text"
                                v-model="item.title"
                            >
                        </div>
                    </div>
                    <div
                        class="col-4"
                        v-if="oneTaskMode"
                    >
                        <div class="d-flex flex-column mb-5 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Дедлайн</label>
                            <JqFlatpickr
                                type="datetime"
                                :date="item.end"
                                @change-date="(d) => item.end = d"
                            />
                        </div>
                    </div>
                </div>

                <div v-if="tasksChainMode">
                    <div class="row mb-10">
                        <div class="col">
                            <div class="d-flex flex-column fv-row">
                                <label class="required fs-5 fw-bold mb-2">Начало выполнения</label>
                                <Select2Comp
                                    placeholder="Начало выполнения"
                                    v-model="item.start_delay"
                                >
                                    <option></option>
                                    <option
                                        v-for="o in startOptions"
                                        :key="o.v"
                                        :value="o.v"
                                    >{{o.n}}</option>
                                </Select2Comp>
                            </div>
                        </div>

                        <div class="col">
                            <div class="d-flex flex-column fv-row">
                                <label class="required fs-5 fw-bold mb-2">Дедлайн</label>
                                <Select2Comp
                                    placeholder="Дедлайн"
                                    v-model="item.end_delay"
                                >
                                    <option></option>
                                    <option
                                        v-for="o in endOptions"
                                        :key="o.v"
                                        :value="o.v"
                                    >{{o.n}}</option>
                                </Select2Comp>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center mb-3">
                    <div class="col flex-grow-0">
                        <label class="required fs-5 fw-bold">Сотрудник</label>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col flex-grow-0">
                                <div class="d-flex align-items-center form-check form-check-custom form-check-sm form-check-solid">
                                    <input
                                        class="form-check-input me-2"
                                        type="radio"
                                        :name="'all_users_' + item.step_id"
                                        :id="'all_users_' + i"
                                        value="1"
                                        v-model="item.all_users"
                                    >
                                    <label
                                        :for="'all_users_' + i"
                                        class="text-nowrap"
                                    >Все сотрудники в ролях</label>
                                </div>
                            </div>
                            <div class="col flex-grow-0">
                                <div class="d-flex align-items-center form-check form-check-custom form-check-sm form-check-solid">
                                    <input
                                        class="form-check-input me-2"
                                        type="radio"
                                        :name="'all_users_' + item.step_id"
                                        :id="'all_users_choose_' + i"
                                        value="0"
                                        v-model="item.all_users"
                                    >
                                    <label
                                        :for="'all_users_choose_' + i"
                                        class="text-nowrap"
                                    >Выбрать сотрудников</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-10">
                    <div class="col">
                        <div
                            class="roles-select-wrap"
                            v-if="item.all_users == '1'"
                        >
                            <Select2Comp
                                multiple
                                placeholder="Роли сотрудников"
                                v-model="item.to_user_roles"
                                @change="userRolesChanged(i)"
                            >
                                <option
                                    v-for="opt in rolesObj"
                                    :key="opt.id"
                                    :value="opt.id"
                                >{{opt.name}}</option>
                            </Select2Comp>

                            <button
                                class="all-btn"
                                @click="() => {item.to_user_roles = item.to_user_roles.length == rolesObj.length ? [] : rolesObj.map(o => String(o.id))}"
                            >{{item.to_user_roles.length == rolesObj.length ? 'Отменить все' :  'Выбрать все'}}</button>
                        </div>

                        <div
                            class="roles-select-wrap"
                            v-if="item.all_users === '0'"
                        >
                            <Select2Comp
                                multiple
                                placeholder="Сотрудники"
                                v-model="item.to_user_ids"
                            >
                                <option
                                    v-for="opt in usersObj"
                                    :key="opt.id"
                                    :value="opt.id"
                                >{{(opt.firstName +' '+ opt.lastName).toUpperCase()}}</option>
                            </Select2Comp>

                            <button
                                class="all-btn"
                                @click="() => {item.to_user_ids = item.to_user_ids.length == usersObj.length ? [] : usersObj.map(o => String(o.id))}"
                            >{{item.to_user_ids.length == usersObj.length ? 'Отменить все' :  'Выбрать все'}}</button>
                        </div>
                    </div>
                </div>

                <div class="row mb-10">
                    <div class="col-4">
                        <label class="fs-5 fw-bold mb-2">Кого касается задача</label>
                        <div
                            class="d-flex align-items-center form-check form-check-custom form-check-sm form-check-solid mb-2"
                            v-for="model in models"
                            :key="model.value"
                        >
                            <input
                                class="form-check-input me-2"
                                type="radio"
                                :name="'model_name_' + i"
                                :id="model.value + i"
                                :value="model.value"
                                v-model="item.model_name"
                                @click="clickRadio(i)"
                            >
                            <label :for="model.value + i">{{model.name}}</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div
                            class="d-flex flex-column fv-row"
                            v-if="item.model_name"
                        >
                            <label class="fs-5 fw-bold mb-2">{{models[item.model_name].name}}</label>
                            <Select2Comp
                                :placeholder="models[item.model_name].name"
                                :ajax-endpoint="models[item.model_name].ajaxEp"
                                v-model="item.model_obj_id"
                            >
                                <option
                                    v-if="item.model_obj_title"
                                    :value="item.model_obj_id"
                                    selected
                                >{{item.model_obj_title}}</option>
                            </Select2Comp>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="oneTaskMode">
                <div class="d-flex flex-column fv-row">
                    <label class="fs-5 fw-bold mb-2">Шаблон задач</label>
                    <Select2Comp
                        placeholder="Шаблон задач"
                        v-model="item.task_template_id"
                    >
                        <option></option>
                        <option
                            v-for="tpl in taskTemplates"
                            :key="tpl.id"
                            :value="tpl.id"
                        >{{tpl.title}}</option>
                    </Select2Comp>
                </div>
            </div>

            <div v-if="tasksChainMode && (editMode || createMode)">
                <button
                    type="button"
                    class="btn btn-primary btn-sm btn-xs"
                    @click="addTaskStep(i + 1)"
                >
                    Добавить задачу
                </button>
            </div>
        </div>

        <div
            class="mt-15"
            v-if="editMode || createMode"
        >
            <button
                type="submit"
                class="btn btn-primary btn-sm"
                :disabled="sending"
                :data-kt-indicator="sending ? 'on' : 'off'"
                @click="saveData"
            >
                <span class="indicator-label">Сохранить</span>
                <span class="indicator-progress">
                    Сохранение...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>
</template>

<style lang="scss">
.roles-select-wrap {
    position: relative;
    .all-btn {
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
    }
    .select2-selection.form-select {
        padding-right: 9rem;
        background-position: right 0.4rem center;
    }
}
.all-btn {
    border: 1px solid #ccc;
    border-radius: 0.4rem;
    padding: 0.15rem 0.4rem;
    font-size: 0.8rem;
    color: #7e8299;
    background: #e4e6ef;
}
.task-step {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    position: relative;
    gap: 1rem;
    & > span {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        border: 1px solid rgba(255, 85, 18, 0.5);
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        font-family: "Roboto", sans-serif;
        padding-top: 1px;
    }
    hr {
        border: none;
        border-bottom: 1px solid rgba(255, 85, 18, 0.5);
        flex: 1 0 auto;
        opacity: 1;
        background: none;
    }
    &__title {
        flex: 0 0 auto;
        font-size: 1.2rem;
    }
    &__sort-buttons {
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        height: 4rem;
        width: 2rem;
        button {
            position: absolute;
            left: 0;
            display: block;
            height: 50%;
            width: 100%;
            background: none;
            border: none;
            padding: 0;
            transition: 0.21s;
            &:hover {
                opacity: 0.5;
            }
        }
    }
    &__top-btn {
        top: 0;
    }
    &__down-btn {
        transform: rotate(180deg);
        bottom: 0;
    }
    &__slide-btn {
        display: block;
        height: 100%;
        width: 2rem;
        background: none;
        border: none;
        padding: 0;
        transition: 0.21s;
        pointer-events: all;
        &:hover {
            opacity: 0.5;
        }
    }
}
.view-task-template {
    label.required {
        &::after {
            display: none;
        }
    }
}
</style>