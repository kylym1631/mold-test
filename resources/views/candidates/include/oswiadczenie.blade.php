<hr>
<div class="card-header mb-5" style="padding: 0">
    <div class="card-title m-0">
        <h4 class="fw-bolder m-0">Освядчение</h4>
    </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
        <button type="button" 
        data-candidate-id="{{ request()->get('id') }}" 
        data-modal-tpl="oswiadczenie" 
        data-modal-title="Добавить освядчение" 
        data-store-endpoint="{{route('oswiadczenie.store')}}" 
        data-refresh-table="OswiadczenieTable" 
        class="js-creator-create-btn btn btn-primary btn-sm">Добавить</button>
    </div>
</div>

<div class="table-responsive">
    <table id="OswiadczenieTable" class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('oswiadczenie.index', ['candidate_id' => request()->get('id')])}}" data-tpl="Oswiadczenie">
        <!--begin::Table head-->
        <thead>
            <!--begin::Table row-->
            <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                <th class="max-w-55px sorting_disabled">Id</th>
                <th class="max-w-85px sorting_disabled">Дата подачи</th>
                <th class="max-w-85px sorting_disabled">Стоимость</th>
                <th class="max-w-85px sorting_disabled">Минимальное количество часов</th>
                <th class="max-w-85px sorting_disabled">Файлы</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-bold">

        </tbody>
        <!--end::Table body-->
    </table>
</div>

{{-- <div class="mt-10">
    <a href="/templates/?candidate_id={{ request()->get('id') }}&find_tpl=1" class="btn btn-primary btn-sm">Сгенерировать договор</a>
</div> --}}