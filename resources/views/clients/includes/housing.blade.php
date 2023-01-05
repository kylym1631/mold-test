<div class="card js-data-table-wrap">
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
                <input type="text" class="js-search-input form-control form-control-solid w-250px ps-14" placeholder="Поиск">
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <input type="hidden" class="js-filter" name="client[]" value="{{request()->get('id')}}">
            <div class="w-175px">
                <!--begin::Select2-->
                <select class="js-filter form-select form-select-solid" name="status" data-placeholder="Статус">
                    <option value="1" selected>Активный</option>
                    <option value="0">Не активный</option>
                </select>
                <!--end::Select2-->
            </div>
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <div class="table-responsive">
            <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('housing.json')}}" data-tpl="ClientHousing">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                        <th class="max-w-65px sorting_disabled">Id</th>
                        <th class="max-w-65px sorting_disabled">Название</th>
                        <th class="max-w-85px sorting_disabled">Адрес</th>
                        <th class="max-w-85px sorting_disabled">Индекс</th>
                        <th class="max-w-45px sorting_disabled">Количество мест</th>
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