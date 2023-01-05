<div class="card mb-5 mb-xl-10 js-data-table-wrap" id="kt_profile_details_view">

    <div class="card-header mb-5">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Легализация</h3>
        </div>

        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <button type="button" data-candidate-id="{{ request()->get('id') }}"            
                data-modal-tpl="legalisation"
                data-modal-title="Добавить документ" data-store-endpoint="{{route('legalisation.store')}}"
                data-refresh-table="LegalisationTable" class="js-creator-create-btn btn btn-primary btn-sm">Добавить
                документ</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="LegalisationTable" class="js-data-table table align-middle table-row-dashed fs-6 gy-3"
                data-route="{{route('legalisation.index', ['candidate_id' => request()->get('id')])}}"
                data-tpl="Legalisation">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                        <th class="max-w-55px sorting_disabled">Id</th>
                        <th class="max-w-85px sorting_disabled">Название документа</th>
                        <th class="max-w-85px sorting_disabled">Кем выдан</th>
                        <th class="max-w-85px sorting_disabled">Дата выдачи</th>
                        <th class="max-w-85px sorting_disabled">Номер документа</th>
                        <th class="max-w-85px sorting_disabled">Дата, от</th>
                        <th class="max-w-85px sorting_disabled">Дата, до</th>
                        <th class="max-w-85px sorting_disabled">Тип</th>
                        <th class="max-w-85px sorting_disabled">Файл</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">

                </tbody>
                <!--end::Table body-->
            </table>
        </div>
    </div>
</div>
