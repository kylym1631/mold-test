<script>
import axios from "axios";
import { VueEditor } from "vue3-editor";

export default {
    name: 'TemplateEditor',
    components: {
        VueEditor,
    },
    props: ['tplId', 'editMode', 'viewMode', 'previewMode', 'docHtml'],
    data() {
        return {
            tplHead: '<img alt="Logo" src="data:image/png;base64,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" style="width: 150px" />',
            tplBody: '',
            tplFoot: '',
            title: '',
            description: '',
            chooseTagsOpened: false,
            selectedTag: '',
            tagBtn: null,
            tags: {
                'FULL_NAME': 'Имя Фамилия',
                'POSITION': 'Должность',
                'CLIENT': 'Клиент',
            },
        };
    },
    mounted() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.tpl-editor-btn');

            if (btn) {
                e.preventDefault();
                this.chooseTag(btn);
            }
        });

        if (this.tplId) {
            axios.get('/templates/' + this.tplId + '/json')
                .then(({ data }) => {
                    if (data.error) {
                        toastr.error(data.error);
                        return;
                    }

                    this.title = data.title;
                    this.description = data.description;
                    this.tplHead = JSON.parse(data.tpl_head);
                    this.tplBody = JSON.parse(data.tpl_body);
                    this.tplFoot = JSON.parse(data.tpl_foot);

                });
        }
    },
    watch: {
        selectedTag(n) {
            if (this.tagBtn) {
                this.tagBtn.innerHTML = '{' + n + '}';
            }

            this.tagBtn = null;
            this.chooseTagsOpened = false;
            this.selectedTag = '';
        }
    },
    methods: {
        bodyTextInput() {
            // let text = this.tplBody;

            // text = text.replace(/\{\}/gi, (match, offset, input) => {
            //     console.log(match, offset, input);
            //     return '<button class="tpl-editor-btn" contenteditable="false">{*****}</button>&nbsp;';
            // });

            // this.tplBody = text;

            // console.log(this.tplBody);
        },
        chooseTag(btn) {
            // this.tagBtn = btn;
            // this.chooseTagsOpened = true;


        },
        saveTemplate(e) {
            const btn = e.target;

            btn.disabled = true;

            axios.post('/templates', {
                id: this.tplId,
                title: this.title,
                description: this.description,
                tpl_head: this.tplHead,
                tpl_body: this.tplBody,
                tpl_foot: this.tplFoot,
            }).then(({ data }) => {
                if (data.error) {
                    toastr.error(data.error);
                } else {
                    toastr.success('Успешно');
                    window.location.replace('/templates');
                }

                btn.disabled = false;
            });

        }
    }
}
</script>

<template>
    <div
        v-if="previewMode"
        class="tpl-preview-mode"
    >
        <div v-html="JSON.parse(docHtml)"></div>
    </div>

    <div
        v-if="editMode"
        class="row mb-5"
    >
        <div class="col-4">
            <div class="d-flex flex-column mb-5 fv-row">
                <label class="required fs-5 fw-bold mb-2">Название</label>
                <input
                    class="form-control form-control-sm form-control-solid"
                    type="text"
                    v-model="title"
                >
            </div>
        </div>

        <div class="col-4">
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
        v-if="viewMode"
        class="row mb-5"
    >
        <div class="col-4">
            <div class="d-flex flex-column mb-5 fv-row">
                <label class="fs-5 fw-bold mb-2">Название</label>
                {{title}}
            </div>
        </div>
    </div>

    <div
        v-if="editMode || viewMode"
        class="row tpl-edit-mode"
    >
        <div class="col-8">
            <div class="tpl-editor">
                <div class="tpl-editor-field">
                    <div
                        class="tpl-editor-field__head"
                        v-html="tplHead"
                    >
                    </div>
                    <div class="tpl-editor-field__body">
                        <div
                            v-if="viewMode"
                            class="ql-container ql-snow"
                        >
                            <div
                                class="ql-editor"
                                v-html="tplBody"
                            ></div>
                        </div>

                        <VueEditor
                            v-if="editMode"
                            v-model="tplBody"
                        />

                    </div>
                </div>
            </div>
        </div>
        <div
            class="col-4"
            v-if="editMode"
        >
            <div class="tpl-options">
                <div class="row">
                    <div class="col">
                        <h4 class="mb-5">Теги</h4>
                        <p>
                            {TPL_ID} - Id шаблона
                        </p>
                        <p>
                            {DATE} - Текущая дата
                        </p>
                        <p>
                            {DATE_ADD_3_M} - Текущая дата + 3 месяца
                        </p>
                        <p class="tpl-options-brd">
                            {ID} - Id кандидата
                        </p>
                        <p>
                            {FULL_NAME} - Имя Фамилия
                        </p>
                        <p>
                            {BIRTH_DATE} - Дата рождения
                        </p>
                        <p>
                            {CITIZENSHIP} - Гражданство
                        </p>
                        <p class="tpl-options-brd">
                            {DOC_NUMBER} - Номер документа
                        </p>
                        <p>
                            {DOC_ISSUE_DATE} - Дата выдачи документа
                        </p>
                        <p>
                            {DOC_WHO_ISSUED} - Кто выдал документ
                        </p>
                        <p>
                            {DOC_EXPIRATION} - Дата действия документа
                        </p>

                        <p class="tpl-options-brd">
                            {PESEL} - PESEL
                        </p>
                        <p>
                            {ACCOUNT_NUMBER} - Номер банковского счета
                        </p>
                        <p>
                            {MOTHERS_NAME} - Имя матери
                        </p>
                        <p>
                            {FATHERS_NAME} - Имя отца
                        </p>
                        <p>
                            {ADDRESS} - Адрес
                        </p>
                        <p>
                            {ZIP} - Индекс
                        </p>
                        <p>
                            {CITY} - Город
                        </p>

                        <p class="tpl-options-brd">
                            {CLIENT} - Клиент
                        </p>
                        <p>
                            {CLIENT_PLACES} - Места работы клиента
                        </p>
                        <p>
                            {POSITION} - Должность
                        </p>
                        <p>
                            {POSITION_RATE} - Ставка
                        </p>

                        <div v-if="chooseTagsOpened">
                            <div class="d-flex flex-column fv-row">
                                <label class="required form-label">Тег</label>
                                <select
                                    v-model="selectedTag"
                                    class="form-select  form-select-sm form-select-solid"
                                >
                                    <option></option>
                                    <option
                                        v-for="(val, key) in tags"
                                        :key="key"
                                        :value="key"
                                    >{{val}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        v-if="editMode"
        class="mt-15"
    >
        <button
            type="submit"
            class="btn btn-primary btn-sm"
            @click="saveTemplate"
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
.tpl-options {
    padding-left: 1rem;
    &-brd {
        border-top: 1px solid #c9c9c9;
        padding-top: 1rem;
    }
}

.tpl-editor {
    font-size: 14px;
    font-family: "DejaVu Serif";
    line-height: 1.5;
    position: relative;
    width: 80em;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.21);
    border: 1px solid #c9c9c9;
    max-width: 100%;
    // &::before {
    //     display: block;
    //     content: "";
    //     padding-bottom: 141%;
    // }
    &-field {
        // position: absolute;
        // left: 0;
        // top: 0;
        width: 100%;
        min-height: 1000px;
        .tpl-edit-mode & {
            display: flex;
            flex-direction: column;
        }

        &__head {
            flex: 0 0 auto;
            padding: 2rem 3.5em 1rem;
        }
        &__body {
            padding: 1em 3.5em;
            .tpl-edit-mode & {
                flex: 1 0 auto;
                display: flex;
                flex-direction: column;
            }
            .tpl-text {
                width: 100%;
            }
        }
        &__foot {
            flex: 0 0 auto;
        }
    }
    .ql-container {
        font-family: "DejaVu Serif" !important;
    }
}

.tpl-text {
    outline: none;
    border: 1px dashed #c9c9c9;
    flex: 1 0 auto;
}

.tpl-editor-btn {
    outline: none;
    color: #ff5612;
    padding: 0;
    border: none;
    background: none;
}

.tpl-preview-mode {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    .ql-container.ql-snow {
        border: none !important;
    }
}
</style>