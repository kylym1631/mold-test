<hr>
<div class="card-header mb-5" style="padding: 0">
    @if (Auth::user()->isRecruiter())
    <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Приезды</h3>
    </div>    
    @else
    <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Логист</h3>
    </div>    
    @endif
    
    @if (!$is_add_page)
    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
        <button type="button" data-candidate-id="{{ request()->get('id') }}" class="js-add-arrival btn btn-primary btn-sm">Добавить</button>
    </div>
    @endif
</div>

@if ($is_add_page)
<div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
    <div class="row mb-5">
        <div class="col-6">
            <div class="d-flex flex-column mb-0 fv-row">
                <label class=" fs-5 fw-bold mb-2">Вид транспорта</label>
                <select id="transport_id" name="transport_id" class="js-transport-id form-select form-select-sm form-select-solid"></select>
            </div>

            <div class="js-transportations-field mt-5" style="display: none">
                <div class="d-flex flex-column mb-0 fv-row">
                    <label class="form-label">Регулярные перевозки</label>
                    <select id="transportation_id" name="transportation_id" class="js-transportions-id form-select form-select-sm form-select-solid"></select>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="d-flex flex-column mb-0 fv-row">
                <label class="fs-5 fw-bold mb-2">Дата и время</label>
                <input id="date_arrive" name="date_arrive" class="js-date-arrive form-control form-control-sm form-control-solid" type="text"/>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-6">
            <div class="d-flex flex-column mb-0 fv-row">
                <label class="fs-5 fw-bold mb-2">Место приезда</label>
                <select id="place_arrive_id" name="place_arrive_id" class="form-select form-select-sm form-select-solid"></select>
            </div>
        </div>
        <div class="col-6">
            <div class="d-flex flex-column mb-0 fv-row">
                <label for="arrival-manage-textarea-1" class="fs-5 fw-bold mb-2">Комментарий</label>
                <textarea id="arrival-manage-textarea-1" class="form-control form-control-lg form-control-solid" cols="60" name="comment_arrive"></textarea>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-6">
            <!--begin::Dropzone-->
            <div class="add-dropzone">
                <div class="dropzone mt-5" id="kt_file_ticket">
                    <!--begin::Message-->
                    <div class="dz-message needsclick">
                        <!--begin::Icon-->
                        <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                        <!--end::Icon-->

                        <!--begin::Info-->
                        <div class="ms-4">
                            <h3 class="fs-5 fw-bolder text-gray-900 mb-1">
                                Загрузить билет</h3>
                            <span class="fs-7 fw-bold text-gray-400">Перетащите документ сюда</span>
                        </div>
                        <!--end::Info-->
                    </div>
                </div>
                <!--end::Dropzone-->
            </div>
        </div>
    </div>
</div>

@else

<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-3" id="users">
        <!--begin::Table head-->
        <thead>
        <!--begin::Table row-->
        <tr class="text-start text-muted fw-bolder fs-7 gs-0">

            <th class="max-w-55px sorting_disabled">Id</th>
            <th class="max-w-85px sorting_disabled">Комментарий</th>
            <th class="max-w-85px sorting_disabled">Место приезда</th>
            <th class="max-w-85px sorting_disabled">Планируемая дата
                приезда
            </th>
            <th class="max-w-85px sorting_disabled">Время приезда</th>
            <th class="max-w-85px sorting_disabled">Вид транспорта</th>
            <th class="min-w-100px sorting_disabled">Билет</th>
            <th class="min-w-100px sorting_disabled">Статус</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-bold">

        </tbody>
        <!--end::Table body-->
    </table>
</div>
@endif
