'use strict';

const tbTpl = {
    def: item => {
        const tplArr = [
            ((act) => {
                if (act.action == 'setLeadStatus') {
                    return item.taskId;
                } else {
                    return `<a href="#" class="js-show-task" data-id="${item.taskId}" data-model-status="${item.model_obj_status}">${item.taskId}</a>`;
                }
            })(item.action),
            item.taskType > 99 ? item.end : item.start,
            item.title,
            item.recruiter,
            item.person,
            (act => {
                if (item.taskStatus == 2) {
                    return '<div class="row flex-nowrap status-actions">' + item.model_obj_status_title + item.info_btn + '</div>';
                }

                const options = ['<option disabled selected></option>'];

                for (const key in act.options) {
                    if (Object.hasOwnProperty.call(act.options, key)) {
                        const tit = act.options[key];

                        options.push('<option value="' + key + '">' + tit + '</option>');
                    }
                }

                let modelObjId = '';

                if (act.action == 'setCandidateStatus') {
                    modelObjId = 'data-candidate-id="' + item.model_obj_id + '" data-candidate-gender="' + item.model_obj_gender + '"';
                } else if (act.action == 'setLeadStatus') {
                    modelObjId = 'data-lead-id="' + item.model_obj_id + '"';
                }

                let resHtml = '<div class="' + (act.add_actions && (act.add_actions.includes('createArrival') || act.add_actions.includes('createCandidate')) ? 'row flex-nowrap status-actions' : 'status-actions') + '">';

                resHtml += `<select 
                    class="js-select-status form-select form-select-sm form-select-solid"
                    data-task-id="${item.taskId}"
                    ${modelObjId}
                    data-action="${act.action}">
                    ${options.join('')}
                </select>`;

                if (act.add_actions && act.add_actions.includes('createArrival')) {
                    resHtml += `<button type="button" class="js-add-arrival btn btn-sm btn-icon create-arrival-btn" data-candidate-id="${item.model_obj_id}" title="Добавить приезд">
                    <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Scale.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M10,14 L5,14 C4.33333333,13.8856181 4,13.5522847 4,13 C4,12.4477153 4.33333333,12.1143819 5,12 L12,12 L12,19 C12,19.6666667 11.6666667,20 11,20 C10.3333333,20 10,19.6666667 10,19 L10,14 Z M15,9 L20,9 C20.6666667,9.11438192 21,9.44771525 21,10 C21,10.5522847 20.6666667,10.8856181 20,11 L13,11 L13,4 C13,3.33333333 13.3333333,3 14,3 C14.6666667,3 15,3.33333333 15,4 L15,9 Z" fill="#000000" fill-rule="nonzero"/>
                        <path d="M3.87867966,18.7071068 L6.70710678,15.8786797 C7.09763107,15.4881554 7.73079605,15.4881554 8.12132034,15.8786797 C8.51184464,16.2692039 8.51184464,16.9023689 8.12132034,17.2928932 L5.29289322,20.1213203 C4.90236893,20.5118446 4.26920395,20.5118446 3.87867966,20.1213203 C3.48815536,19.7307961 3.48815536,19.0976311 3.87867966,18.7071068 Z M16.8786797,5.70710678 L19.7071068,2.87867966 C20.0976311,2.48815536 20.7307961,2.48815536 21.1213203,2.87867966 C21.5118446,3.26920395 21.5118446,3.90236893 21.1213203,4.29289322 L18.2928932,7.12132034 C17.9023689,7.51184464 17.2692039,7.51184464 16.8786797,7.12132034 C16.4881554,6.73079605 16.4881554,6.09763107 16.8786797,5.70710678 Z" fill="#000000" opacity="0.3"/>
                    </g>
                    </svg><!--end::Svg Icon--></span>
                    </button>`;
                }

                if (act.add_actions && act.add_actions.includes('createCandidate')) {
                    resHtml += `<button type="button"
                                    data-task-id="${item.taskId}"
                                    ${modelObjId}
                                    class="js-create-candidate-from-lead btn btn-sm btn-icon create-candidate-btn">
                                        <i class="fas fa-user-plus"></i>
                                </button>`;
                }

                if (act.action == 'setCandidateStatus' && item.count_failed_call && +item.count_failed_call > 0) {
                    resHtml += `<button class="js-call-fail-btn" 
                    data-action="${act.action}" 
                    data-task-id="${item.taskId}" 
                    ${modelObjId} 
                    data-status="${item.model_obj_status}">
                    Недозвон ${+item.count_failed_call + 1}
                    </button>`;
                }

                resHtml += '</div>';

                return resHtml;
            })(item.action),
        ];

        if (group == 2) {
            tplArr.splice(5, 0, item.taskType > 99 ? '' : item.file);
        }

        if (group == 5) {
            const vacancySelect = `<select class="js-vacancy-task-select form-select form-select-solid form-select-sm" data-candidate-id="${item.model_obj_id}">
                    <option value="${item.vacancy_id}">${item.vacancy}</option>
                </select>`;

            const clientSelect = `<select class="js-client-task-select form-select form-select-solid form-select-sm" data-candidate-id="${item.model_obj_id}">
                    <option>${item.client}</option>
                </select>`;


            tplArr.splice(4, 0, item.taskType > 99 ? '' : vacancySelect, item.taskType > 99 ? '' : clientSelect);
        }

        if (group == 6 || group == 103) {
            const vacancySelect = `<select class="js-vacancy-task-select form-select form-select-solid form-select-sm" data-candidate-id="${item.model_obj_id}">
                    <option value="${item.vacancy_id}">${item.vacancy}</option>
                </select>`;

            const clientSelect = '<span data-client-id="' + item.client_id + '">' + item.client + '</span>';


            tplArr.splice(4, 0, item.taskType > 99 ? '' : vacancySelect, item.taskType > 99 ? '' : clientSelect);
        }

        return tplArr;
    },

    recruiter: (item) => {
        let phone = item.person_phone && (item.person_phone).indexOf('+') == -1 ? '+' + item.person_phone : item.person_phone;
        let viber = item.person_viber && (item.person_viber).indexOf('+') == -1 ? '+' + item.person_viber : item.person_viber;

        if (phone) {
            phone = '<span class="d-flex" style="align-items: center"><a href="tel:' + phone + '">' + phone + '</a><button class="btn js-copy p-0" style="margin-left: 10px; color: #FF5612;"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="currentColor"></path> </svg> </button></span>';
        }

        if (viber) {
            viber = '<span class="d-flex" style="align-items: center"><a href="viber:' + viber + '">' + viber + '</a><button class="btn js-copy p-0" style="margin-left: 10px; color: #FF5612;"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="currentColor"></path> </svg> </button></span>';
        }

        const tplArr = [
            ((act) => {
                if (act.action == 'setLeadStatus') {
                    return item.taskId;
                } else {
                    return `<a href="#" class="js-show-task" data-id="${item.taskId}" data-model-status="${item.model_obj_status}">${item.taskId}</a>`;
                }
            })(item.action),
            item.taskType > 99 ? item.end : item.start,
            item.title,
            item.person,
            phone,
            viber,
            item.lead_company,
            (act => {
                if (item.taskStatus == 2) {
                    return '<div class="row flex-nowrap status-actions">' + item.model_obj_status_title + item.info_btn + '</div>';
                }

                const options = ['<option disabled selected></option>'];

                for (const key in act.options) {
                    if (Object.hasOwnProperty.call(act.options, key)) {
                        const tit = act.options[key];

                        options.push('<option value="' + key + '">' + tit + '</option>');
                    }
                }

                let modelObjId = '';

                if (act.action == 'setCandidateStatus') {
                    modelObjId = 'data-candidate-id="' + item.model_obj_id + '" data-candidate-gender="' + item.model_obj_gender + '"';
                } else if (act.action == 'setLeadStatus') {
                    modelObjId = 'data-lead-id="' + item.model_obj_id + '"';
                }

                let resHtml = '<div class="' + (act.add_actions && (act.add_actions.includes('createArrival') || act.add_actions.includes('createCandidate')) ? 'row flex-nowrap status-actions' : 'status-actions') + '">';

                resHtml += `<select 
                    class="js-select-status form-select form-select-sm form-select-solid"
                    data-task-id="${item.taskId}"
                    ${modelObjId}
                    data-action="${act.action}">
                    ${options.join('')}
                </select>`;

                if (act.add_actions && act.add_actions.includes('createArrival')) {
                    resHtml += `<button type="button" class="js-add-arrival btn btn-sm btn-icon create-arrival-btn" data-candidate-id="${item.model_obj_id}" title="Добавить приезд">
                    <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Scale.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M10,14 L5,14 C4.33333333,13.8856181 4,13.5522847 4,13 C4,12.4477153 4.33333333,12.1143819 5,12 L12,12 L12,19 C12,19.6666667 11.6666667,20 11,20 C10.3333333,20 10,19.6666667 10,19 L10,14 Z M15,9 L20,9 C20.6666667,9.11438192 21,9.44771525 21,10 C21,10.5522847 20.6666667,10.8856181 20,11 L13,11 L13,4 C13,3.33333333 13.3333333,3 14,3 C14.6666667,3 15,3.33333333 15,4 L15,9 Z" fill="#000000" fill-rule="nonzero"/>
                        <path d="M3.87867966,18.7071068 L6.70710678,15.8786797 C7.09763107,15.4881554 7.73079605,15.4881554 8.12132034,15.8786797 C8.51184464,16.2692039 8.51184464,16.9023689 8.12132034,17.2928932 L5.29289322,20.1213203 C4.90236893,20.5118446 4.26920395,20.5118446 3.87867966,20.1213203 C3.48815536,19.7307961 3.48815536,19.0976311 3.87867966,18.7071068 Z M16.8786797,5.70710678 L19.7071068,2.87867966 C20.0976311,2.48815536 20.7307961,2.48815536 21.1213203,2.87867966 C21.5118446,3.26920395 21.5118446,3.90236893 21.1213203,4.29289322 L18.2928932,7.12132034 C17.9023689,7.51184464 17.2692039,7.51184464 16.8786797,7.12132034 C16.4881554,6.73079605 16.4881554,6.09763107 16.8786797,5.70710678 Z" fill="#000000" opacity="0.3"/>
                    </g>
                    </svg><!--end::Svg Icon--></span>
                    </button>`;
                }

                if (act.add_actions && act.add_actions.includes('createCandidate')) {
                    resHtml += `<button type="button"
                                    data-task-id="${item.taskId}"
                                    ${modelObjId}
                                    class="js-create-candidate-from-lead btn btn-sm btn-icon create-candidate-btn">
                                        <i class="fas fa-user-plus"></i>
                                </button>`;
                }

                if (act.action == 'setCandidateStatus' && item.count_failed_call && +item.count_failed_call > 0) {
                    resHtml += `<button class="js-call-fail-btn" 
                    data-action="${act.action}" 
                    data-task-id="${item.taskId}" 
                    ${modelObjId} 
                    data-status="${item.model_obj_status}">
                    Недозвон ${+item.count_failed_call + 1}
                    </button>`;
                }

                resHtml += '</div>';

                return resHtml;
            })(item.action),
        ];

        return tplArr;
    },

    logist: item => {
        return [
            `<a href="#" class="js-show-task" data-id="${item.taskId}" data-model-status="${item.model_obj_status}">${item.taskId}</a>`,
            item.taskType > 99 ? item.end : item.start,
            item.title,
            item.person ? item.person : item.firstName,
            item.lastName,
            item.recruiter,
            item.phone,
            item.viber,
            item.citizenship,
            item.vacancy,
            item.place_arrive,
            item.transport,
            item.date_link,
            item.date_arrive_time,
            item.file,
            item.comment,
            (act => {
                if (item.taskStatus == 2) {
                    return '<div class="row flex-nowrap status-actions">' + item.model_obj_status_title + item.info_btn + '</div>';
                }

                const options = ['<option disabled selected></option>'];

                for (const key in act.options) {
                    if (Object.hasOwnProperty.call(act.options, key)) {
                        const tit = act.options[key];

                        options.push('<option value="' + key + '">' + tit + '</option>');
                    }
                }

                let resHtml = '<div class="' + (act.add_actions && act.add_actions.includes('createArrival') ? 'row flex-nowrap status-actions' : 'status-actions') + '">';

                resHtml += `<select 
                    class="js-select-status form-select form-select-sm form-select-solid"
                    data-task-id="${item.taskId}"
                    data-candidate-id="${item.model_obj_id}"
                    data-action="${act.action}">
                    ${options.join('')}
                </select>`;

                if (act.add_actions && act.add_actions.includes('createArrival')) {
                    resHtml += `<button type="button" class="js-add-arrival btn btn-sm btn-icon create-arrival-btn" data-candidate-id="${item.model_obj_id}" title="Добавить приезд">
                    <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Scale.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M10,14 L5,14 C4.33333333,13.8856181 4,13.5522847 4,13 C4,12.4477153 4.33333333,12.1143819 5,12 L12,12 L12,19 C12,19.6666667 11.6666667,20 11,20 C10.3333333,20 10,19.6666667 10,19 L10,14 Z M15,9 L20,9 C20.6666667,9.11438192 21,9.44771525 21,10 C21,10.5522847 20.6666667,10.8856181 20,11 L13,11 L13,4 C13,3.33333333 13.3333333,3 14,3 C14.6666667,3 15,3.33333333 15,4 L15,9 Z" fill="#000000" fill-rule="nonzero"/>
                        <path d="M3.87867966,18.7071068 L6.70710678,15.8786797 C7.09763107,15.4881554 7.73079605,15.4881554 8.12132034,15.8786797 C8.51184464,16.2692039 8.51184464,16.9023689 8.12132034,17.2928932 L5.29289322,20.1213203 C4.90236893,20.5118446 4.26920395,20.5118446 3.87867966,20.1213203 C3.48815536,19.7307961 3.48815536,19.0976311 3.87867966,18.7071068 Z M16.8786797,5.70710678 L19.7071068,2.87867966 C20.0976311,2.48815536 20.7307961,2.48815536 21.1213203,2.87867966 C21.5118446,3.26920395 21.5118446,3.90236893 21.1213203,4.29289322 L18.2928932,7.12132034 C17.9023689,7.51184464 17.2692039,7.51184464 16.8786797,7.12132034 C16.4881554,6.73079605 16.4881554,6.09763107 16.8786797,5.70710678 Z" fill="#000000" opacity="0.3"/>
                    </g>
                    </svg><!--end::Svg Icon--></span>
                    </button>`;
                }

                resHtml += '</div>';

                return resHtml;
            })(item.action),
        ];
    },

    legalise: item => {
        return [
            `<a href="#" class="js-show-task" data-id="${item.taskId}" data-model-status="${item.model_obj_status}">${item.taskId}</a>`,
            item.taskType > 99 ? item.end : item.start,
            item.title,
            item.person ? item.person : item.firstName,
            item.file,
            (act => {
                const options = ['<option disabled selected></option>'];

                for (const key in act.options) {
                    if (Object.hasOwnProperty.call(act.options, key)) {
                        const tit = act.options[key];

                        options.push('<option value="' + key + '">' + tit + '</option>');
                    }
                }

                let modelObjId = 'data-candidate-id="' + item.model_obj_id + '"';

                let resHtml = `<select 
                    class="js-select-status form-select form-select-sm form-select-solid"
                    data-task-id="${item.taskId}"
                    ${modelObjId}
                    data-action="${act.action}">
                    ${options.join('')}
                </select>`;

                return resHtml;
            })(item.action),
        ];
    },
};

let tableTemplate = tbTpl.def;

if (group == 4) {
    tableTemplate = tbTpl.logist;
} else if (group == 2) {
    tableTemplate = tbTpl.recruiter;
} else if (group == 12) {
    tableTemplate = tbTpl.legalise;
}

$(function () {

    let oTable = null;

    $('body').on('click', '.js-copy', function () {
        const copyEl = $(this)[0].previousElementSibling;

        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(copyEl);
            range.select().createTextRange();
            document.execCommand("Copy");
            $(this).addClass('copied');
        } else if (window.getSelection) {
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNode(copyEl);
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand("Copy");
            $(this).addClass('copied');
        }

        return false;
    });

    $('body').on('click', '.js-show-task', function () {
        const id = $(this).attr('data-id');

        $.get('/tasks/ajax/id/' + id, function (res) {
            $('#modal_users_add__id').val(id);

            if (res.task.output_end) {
                $('#modal_users_add__output_start').html(res.task.output_start + ' - ' + res.task.output_end);
            } else {
                $('#modal_users_add__output_start').html(res.task.output_start);
            }

            $('#modal_users_add__Candidate_title').html('Кандидат');
            $('#modal_users_add__Candidate').html(res.task.Candidate);
            $('#modal_users_add__Autor').html(res.task.Autor);
            $('#modal_users_add__title').html(res.task.title);
            $('#modal_users_add__status').html(res.task.status);
            $('#modal_users_add__create_date').html(res.task.createdAt);
            $('#modal_task_lead_company_tr').css('display', 'none');

            if (res.task.Candidate) {
                $('#modal_users_add__Candidate_title').html('Кандидат');
                $('#modal_users_add__Candidate').html(res.task.Candidate);
            } else if (res.task.Lead) {
                $('#modal_users_add__Candidate_title').html('Лид');
                $('#modal_users_add__Candidate').html(res.task.Lead);
                $('#modal_task_lead_company_tr').css('display', '');
                $('#modal_task_lead_company').html(res.task.LeadCompany);
            }

            $('#modal_users_add').modal('show');
        });

        return false;

    }).on('click', '.js-show-lead', function () {
        const id = $(this).attr('data-id');

        $.get('/leads/ajax/id/' + id, function (res) {
            const phone = res.lead.phone && (res.lead.phone).indexOf('+') == -1 ? '+' + res.lead.phone : res.lead.phone;
            const viber = res.lead.viber && (res.lead.viber).indexOf('+') == -1 ? '+' + res.lead.viber : res.lead.viber;

            $('#modal_lead_name').html(res.lead.name.toUpperCase());

            $('#modal_lead_phone').html('<span class="d-flex" style="align-items: center"><a href="tel:' + phone + '">' + phone + '</a><button class="btn js-copy p-0" style="margin-left: 10px; color: #FF5612;"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="currentColor"></path> </svg> </button></span>');

            $('#modal_lead_viber').html('');

            if (res.lead.viber) {
                $('#modal_lead_viber').html('<span class="d-flex" style="align-items: center"><a href="viber:' + viber + '">' + viber + '</a><button class="btn js-copy p-0" style="margin-left: 10px; color: #FF5612;"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="currentColor"></path> </svg> </button></span>');
            } else {
                $('#modal_lead_viber').html('');
            }

            $('#modal_lead_company').html(res.lead.company);

            $('#modal_users_add').modal('hide');
            $('#modal_lead').modal('show');
        });

        return false;
    });

    oTable = $('#tasks-table').DataTable({
        dom: 'rt<"dataTable_bottom"lip>',
        paging: true,
        searching: false,
        pagingType: "numbers",
        processing: true,
        serverSide: true,
        pageLength: 100,
        lengthMenu: [25, 50, 100, 250],
        infoCallback: function (settings, start, end, max, total, pre) {
            return 'Показано ' + (end - start + 1) + ' из ' + total + ' записей';
        },
        fnDrawCallback: function (oSettings) {
            if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
            } else {
                $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
            }

            if (oSettings.fnRecordsDisplay() < 2) {
                $(oSettings.nTableWrapper).find('.dataTables_length').hide();
                $(oSettings.nTableWrapper).find('.dataTables_info').hide();
            } else {
                $(oSettings.nTableWrapper).find('.dataTables_length').show();
                $(oSettings.nTableWrapper).find('.dataTables_info').show();
            }
        },
        language: {
            emptyTable: "нет данных",
            zeroRecords: "нет данных",
            sSearch: "Поиск",
            processing: 'Загрузка...'
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': ['sorting_disabled']
        }],
        ajax: function (data, callback, settings) {
            delete data.columns;

            data._token = $('input[name=_token]').val();
            data.search = $('#f__search').val().trim();
            data.status = $('#filter__status').val();

            if ($("#kt_datepicker_7").length) {
                var date = $("#kt_datepicker_7").val();
                if (date) {
                    var dateSpl = date.split('to');
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
            }

            $.ajax({
                url: routes.tasksJson,
                type: 'GET',
                data,
                success: function (res) {
                    if (res.error) {
                        toastr.error(res.error);
                    } else {
                        res.data = !!res.data && res.data.map(item => tableTemplate(item));

                        callback(res);

                        if (group == 2) {
                            setTimeout(() => {
                                if ($('.js-show-lead-details').length == 1) {
                                    $('.js-show-lead-details')[0].click();
                                }
                            }, 100);
                        }

                        if (window.ping) {
                            ping(true);
                        }
                    }
                }
            });
        },
    });

    $('#f__search').keyup(function () {
        oTable.draw();
    });

    $('#filter__group').change(function () {
        oTable.draw();
    });

    $('#filter__status').select2({
        placeholder: 'Статус',
        allowClear: true,
        minimumResultsForSearch: -1
    }).on('change.select2', function (e) {
        oTable.draw();
    });

    $('#filter__period').select2({
        placeholder: 'Период',
        allowClear: true,
        minimumResultsForSearch: -1
    }).on('select2:select', function (e) {
        oTable.draw();
    }).on('select2:clear', function (e) {
        oTable.draw();
    });

    $("#kt_datepicker_7").flatpickr({
        altInput: true,
        altFormat: "d.m.Y",
        dateFormat: "Y-m-d",
        mode: "range",
        locale: {
            firstDayOfWeek: 1
        },
        onClose: function (selectedDates) {
            oTable.draw();
        }
    });

    if (group == 5 || group == 6 || group == 103) {
        oTable.on('draw', () => {
            $('.js-vacancy-task-select').select2({
                placeholder: 'Вакансия',
                ajax: {
                    url: "/search/candidate/vacancy",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            f_search: params.term,
                            gender: $(this).closest('tr').find('[data-candidate-gender]').attr('data-candidate-gender'),
                            view: 'tasks',
                            client_id: $(this).closest('tr').find('[data-client-id]').attr('data-client-id'),
                        };
                    },
                    processResults: function (data) {
                        if (data.error) {
                            toastr.error(data.error);
                            if (data.code == 'GENDER_NONE') {
                                $('#candidate-set-gender input[name="candidate_id"]').val($(this)[0].$element.attr('data-candidate-id'));
                                $('#candidate-set-gender [name="gender"]').val('');
                                $('#candidate-set-gender').modal('show');
                            }
                            return;
                        }

                        var results = [];
                        $.each(data, function (index, item) {
                            results.push({
                                id: item.id,
                                text: item.value,
                                disabled: item.filled,
                            });
                        });
                        return {
                            results: results
                        };
                    }
                },
            }).on('select2:select', function () {
                const data = {
                    _token: $('input[name=_token]').val(),
                    candidateId: $(this).attr('data-candidate-id'),
                    vacancyId: $(this).val(),
                };

                $.ajax({
                    url: '/candidate/add-vacancy',
                    type: 'POST',
                    dataType: 'json',
                    data,
                    success: res => {
                        if (res.error) {
                            toastr.error(res.error);
                        } else {
                            toastr.success('Успешно');

                            if (group == 5) {
                                $(this).closest('tr').find('select.js-client-task-select').html('')
                            }
                        }
                    }
                });
            });

            $('body').on('click', '.select2-results__option--disabled', function () {
                toastr.error('Вакансия уже заполнена');
            });

            $('.js-client-task-select').select2({
                placeholder: 'Клиент',
                ajax: {
                    url: "/search/candidate/client",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            f_search: params.term,
                            vacancy_id: $(this).closest('tr').find('.js-vacancy-task-select').val(),
                        };
                    },
                    processResults: function (data) {
                        var results = [];
                        $.each(data, function (index, item) {
                            results.push({
                                id: item.id,
                                text: item.value
                            });
                        });
                        return {
                            results: results
                        };
                    }
                },
            }).on('select2:open', function () {
                if (!$(this).closest('tr').find('.js-vacancy-task-select').val()) {
                    toastr.error('Выберите вакансию');
                }
            }).on('select2:select', function () {
                const data = {
                    _token: $('input[name=_token]').val(),
                    candidateId: $(this).attr('data-candidate-id'),
                    clientId: $(this).val(),
                };

                $.ajax({
                    url: '/candidate/add-client',
                    type: 'POST',
                    dataType: 'json',
                    data,
                    success: function (res) {
                        if (res.error) {
                            toastr.error(res.error);
                        } else {
                            toastr.success('Успешно');
                        }
                    }
                });
            });
        });

        $('body').on('click', '.js-candidate-gender-save-btn', function () {
            const data = {
                _token: $('input[name=_token]').val(),
                candidateId: $('#candidate-set-gender [name="candidate_id"]').val(),
                gender: $('#candidate-set-gender [name="gender"]').val(),
            };

            $.ajax({
                url: '/candidate/set-gender',
                type: 'POST',
                dataType: 'json',
                data,
                success: function (res) {
                    if (res.error) {
                        toastr.error(res.error);
                    } else {
                        toastr.success('Успешно');
                        $('#candidate-set-gender').modal('hide');
                        oTable.draw();
                    }
                }
            });
        });
    }

    window.tasksOTable = oTable;
});
