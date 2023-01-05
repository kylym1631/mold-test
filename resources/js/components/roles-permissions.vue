<script>
import axios from "axios";
import Select2Comp from './select2-comp.vue';

export default {
    name: 'RolesPermissions',
    props: ['roleItem'],
    components: {
        Select2Comp,
    },
    data() {
        return {
            allPermissions: {},
            currPermissions: [],
            staticPermissions: [],
            permissions: [],
            select2Perm: {},
            name: '',
            id: '',
            sending: false,
        };
    },
    mounted() {

        this.name = this.roleItem && JSON.parse(this.roleItem)['name'];
        this.id = this.roleItem && JSON.parse(this.roleItem)['id'];

        axios.get('/role-permissions/all/json')
            .then(({ data }) => {
                this.allPermissions = data;

                if (this.roleItem) {
                    const roleItem = JSON.parse(this.roleItem);

                    this.currPermissions = roleItem.cur_permissions || [];
                    this.staticPermissions = roleItem.static_permissions || [];
                }
            });
    },
    watch: {
        currPermissions(permArr) {
            this.allPermissions.forEach(apIt => {
                apIt.children.forEach(chIt => {
                    if (chIt.select) {
                        this.select2Perm[chIt.comp_key] = [];

                        chIt.select.options.forEach(opt => {
                            if (permArr.includes(opt.comp_key)) {
                                this.select2Perm[chIt.comp_key].push(opt.comp_key);
                            }
                        });
                    }
                });
            });

            let sel2Pm = [];

            for (const key in this.select2Perm) {
                if (Object.hasOwnProperty.call(this.select2Perm, key)) {
                    sel2Pm = sel2Pm.concat(this.select2Perm[key]);
                }
            }

            permArr.forEach(cP => {
                if (!sel2Pm.includes(cP)) {
                    this.permissions.push(cP);
                }
            });
        },
        permissions(permArr) {
            for (const key in this.select2Perm) {
                if (Object.hasOwnProperty.call(this.select2Perm, key)) {
                    if (!permArr.includes(key)) {
                        this.select2Perm[key] = [];
                    }
                }
            }
        },
    },
    methods: {
        selectChanged() {
            // for (const key in this.select2Perm) {
            //     if (Object.hasOwnProperty.call(this.select2Perm, key)) {
            //         const sel = this.select2Perm[key];
            //         sel.forEach(pm => {
            //             this.permissions.push(pm);
            //         });
            //     }
            // }
        },
        saveData() {
            let sel2Pm = [];

            for (const key in this.select2Perm) {
                if (Object.hasOwnProperty.call(this.select2Perm, key)) {
                    const sel = this.select2Perm[key];

                    if (this.permissions.includes(key) && !sel.length) {
                        toastr.error('Выберите дополнительные параметры (статусы, роли)');
                        return;
                    }

                    sel2Pm = sel2Pm.concat(sel);
                }
            }

            this.sending = true;

            const savePermissions = this.permissions.concat(sel2Pm)
                .filter(pm => {
                    if (!this.staticPermissions.includes(pm)) {
                        return pm;
                    }
                });

            const data = {
                _token: document.querySelector('input[name=_token]').value,
                permission: savePermissions,
                name: this.name,
            };

            if (this.id) {
                data.id = this.id;
            }

            axios.post('/roles/create', data)
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('Успешно');
                        window.location.replace('/roles');
                    }

                    this.sending = false;
                });
        },
    },
}
</script>

<template>
    <div class="row mb-5">
        <div class="col-6">
            <div class="d-flex flex-column mb-5 fv-row">
                <label class="required fs-5 fw-bold mb-2">Имя роли</label>
                <input
                    class="form-control form-control-sm form-control-solid"
                    type="text"
                    v-model="name"
                    :readonly="!!id && id < 99"
                >
            </div>
        </div>
    </div>

    <div
        class="mb-10 pb-10 border-bottom"
        v-for="item in allPermissions"
        :key="item.key"
    >
        <h5 class="mb-5">{{item.name}}</h5>

        <div class="row">
            <div
                class="col-3"
                v-for="child in item.children"
                :key="child.comp_key"
            >
                <div class="d-flex align-items-center form-check form-check-custom form-check-sm form-check-solid">
                    <input
                        class="form-check-input me-2"
                        type="checkbox"
                        :id="child.comp_key"
                        :value="child.comp_key"
                        :disabled="staticPermissions.includes(child.comp_key)"
                        v-model="permissions"
                    >
                    <label :for="child.comp_key">{{child.name}}</label>
                </div>

                <div
                    class="mt-5 roles-select-wrap"
                    v-if="child.select && permissions.includes(child.comp_key)"
                >
                    <Select2Comp
                        multiple
                        :disabled="staticPermissions.includes(child.comp_key)"
                        :placeholder="child.select.name"
                        v-model="select2Perm[child.comp_key]"
                        @change="selectChanged"
                    >
                        <option
                            v-for="opt in child.select.options"
                            :key="opt.comp_key"
                            :value="opt.comp_key"
                        >{{opt.name}}</option>
                    </Select2Comp>

                    <button
                        class="all-btn"
                        v-if="!staticPermissions.includes(child.comp_key)"
                        @click="() => {select2Perm[child.comp_key] = select2Perm[child.comp_key].length == child.select.options.length ? [] : child.select.options.map(o => o.comp_key)}"
                    >{{select2Perm[child.comp_key].length == child.select.options.length ? 'Отменить все' :  'Выбрать все'}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-15">
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
</style>