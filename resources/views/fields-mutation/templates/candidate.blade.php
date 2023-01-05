<!--begin::Card-->
<div class="card">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                        height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                        fill="currentColor"></rect>
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="currentColor"></path>
                            </svg>
                        </span>
                <!--end::Svg Icon-->
                <input type="text"
                        class="form-control form-control-solid w-250px ps-14"
                        id="f__search"
                        placeholder="Поиск кандидата">
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->

        <!--begin::Card toolbar-->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <div class="w-200px">
                <select id="filter__period" class="form-select form-select form-select-solid">
                    <option value=""></option>
                    <option value="today">Сегодня</option>
                    <option value="yesterday">Вчера</option>
                    <option value="lastWeek">7 дней</option>
                    <option value="lastMonth">30 дней</option>
                </select>
            </div>

            <div class="w-200px">
                <select id="filter__users" class="form-select form-select form-select-solid">
                    <option value=""></option>
                    {{-- @foreach ($users as $user)
                    <option value="{{$user['id']}}">{{$user['firstName']}} {{$user['lastName']}}</option>
                    @endforeach --}}
                </select>
            </div>

            <div class="w-200px">
                <select id="filter__roles" class="form-select form-select form-select-solid">
                    <option value=""></option>
                    {{-- @foreach ($roles_ids as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach --}}
                </select>
            </div>
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-3" id="mutated-fields">
                <!--begin::Table head-->
                <thead>
                <!--begin::Table row-->
                <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                    <th class="max-w-55px sorting_disabled">Дата</th>
                    <th class="max-w-85px sorting_disabled">Автор</th>
                    <th class="max-w-55px sorting_disabled">Кандидат</th>
                    {{-- <th class="max-w-55px sorting_disabled">Model Name</th>
                    <th class="max-w-55px sorting_disabled">Model Id</th> --}}
                    <th class="max-w-85px sorting_disabled">Поле</th>
                    <th class="max-w-85px sorting_disabled">Изменения</th>
                </tr>
                <!--end::Table row-->
                </thead>
                <tbody class="text-gray-600 fw-bold"></tbody>
                <!--end::Table body-->
            </table>
        </div>

        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

<script>
    var groupColumn = 0;
    oTable = $('#mutated-fields').DataTable({
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
        language: {
            emptyTable: "нет данных",
            zeroRecords: "нет данных",
            sSearch: "Поиск"
        },
        aoColumnDefs: [{
            bSortable: false,
            aTargets: ['sorting_disabled']
        }],
        ajax: function (data, callback, settings) {
            data._token = $('input[name="_token"]').val();
            data.period = $('#filter__period').val();
            data.roles = $('#filter__roles').val();
            data.user_id = $('#filter__users').val();
            // data.candidate_id = $('#filter__candidates').val();
            data.search = $('#f__search').val().trim();
            
            $.ajax({
                url: '{{ route("fields-mutation.json") }}',
                type: 'POST',
                data: data,
                success: function (res) {
                    if (res.error) {
                        toastr.error(res.error);
                    } else {
                        res.data = !!res.data && res.data.map(item => {
                            return [
                                item.date_time,
                                item.user_name +', '+ item.user_role_title,
                                '<a href="/candidate/add?id='+ item.model_id +'">' + item.model_data + '</a>',
                                (item.model_name == 'CandidateArrival' ? 'Логистика, ' : '') + item.field || item.field_name,
                                (item.prev_value ? item.prev_value : '') + ' -> ' + item.current_value,
                            ];
                        });

                        callback(res);
                    }
                }
            });
        },
    });

    $('#f__search').keyup(function () {
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

    $('#filter__candidates').select2({
        placeholder: 'Кандидаты',
        allowClear: true,
    }).on('select2:select', function (e) {
        oTable.draw();
    }).on('select2:clear', function (e) {
        oTable.draw();
    });

    $('#filter__users').select2({
        placeholder: 'Авторы',
        allowClear: true,
    }).on('select2:select', function (e) {
        oTable.draw();
    }).on('select2:clear', function (e) {
        oTable.draw();
    });

    $('#filter__roles').select2({
        placeholder: 'Роли авторов',
        allowClear: true,
    }).on('select2:select', function (e) {
        oTable.draw();
    }).on('select2:clear', function (e) {
        oTable.draw();
    });
</script>

</body>
<!--end::Body-->
</html>
