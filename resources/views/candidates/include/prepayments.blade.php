<hr>
<div class="card-header mb-5" style="padding: 0">
    <div class="card-title m-0">
        <h4 class="fw-bolder m-0">Авансы</h4>
    </div>

    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
        <button type="button" data-type="prepayment" data-candidate-id="{{ request()->get('id') }}" data-period="" data-worklog-id="" class="js-add-addition-worklog btn btn-primary btn-sm">Добавить</button>
    </div>
</div>

<div class="table-responsive">
    <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('work-logs.additions.json', ['type' => 'prepayment'])}}" data-tpl="WorklogAddition">
        <!--begin::Table head-->
        <thead>
            <!--begin::Table row-->
            <tr class="text-start text-muted fw-bolder fs-7 gs-0">

                <th class="max-w-55px sorting_disabled">Id</th>
                <th class="max-w-85px sorting_disabled">Дата</th>
                <th class="max-w-85px sorting_disabled">Сумма</th>
                <th class="max-w-85px sorting_disabled">Комментарий</th>
                <th class="max-w-85px sorting_disabled">Файлы</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-bold">

        </tbody>
        <!--end::Table body-->
    </table>
</div>
