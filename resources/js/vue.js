import { createApp } from 'vue';
import LeadDetails from './components/lead-details.vue';
import WorklogTable from './components/worklog-table.vue';
import WorklogHistory from './components/worklog-history.vue';
import AdditionWorklog from './components/addition-worklog.vue';
import JqFlatpickr from './components/jq-flatpickr.vue';
import PgDropzone from './components/pg-dropzone.vue';
import CreateEdit from './components/create-edit.vue';
import TemplateEditor from './components/template-editor.vue';
import RolesPermissions from './components/roles-permissions.vue';
import Select2Comp from './components/select2-comp.vue';
import TasksManager from './components/tasks-manager.vue';

const vueApp = createApp();
vueApp.component('lead-details', LeadDetails);
vueApp.component('worklog-table', WorklogTable);
vueApp.component('worklog-history', WorklogHistory);
vueApp.component('addition-worklog', AdditionWorklog);
vueApp.component('jq-flatpickr', JqFlatpickr);
vueApp.component('pg-dropzone', PgDropzone);
vueApp.component('template-editor', TemplateEditor);
vueApp.component('roles-permissions', RolesPermissions);
vueApp.component('select2-comp', Select2Comp);
vueApp.component('tasks-manager', TasksManager);
vueApp.mount('#vue-app');

const fooVueApp = createApp();
fooVueApp.component('create-edit', CreateEdit);
fooVueApp.mount('#foo-vue-app');