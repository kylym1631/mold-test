<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <title>Пакеты настроек по лидам</title>

    @include('includes.global_styles')
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body"
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
    style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
    @csrf
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">

            @include('includes.aside')
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Header-->
                <div id="kt_header" style="" class="header align-items-stretch">
                    <!--begin::Container-->
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <!--begin::Aside mobile toggle-->
                        <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
                            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                                id="kt_aside_mobile_toggle">
                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                            fill="currentColor" />
                                        <path opacity="0.3"
                                            d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </div>
                        </div>
                        <!--end::Aside mobile toggle-->
                        <!--begin::Mobile logo-->
                        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                            <a href="{{url('/')}}/" class="d-lg-none">
                                <img style="margin-top: 8px;" alt="Logo" src="{{url('/')}}/assets/media/logos/g10.png"
                                    class="h-30px" />
                            </a>
                        </div>
                        <!--end::Mobile logo-->
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                            @include('includes.Navbar')
                            @include('includes.Toolbar')
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Header-->
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Toolbar-->
                    <div class="toolbar" id="kt_toolbar">
                        <!--begin::Container-->
                        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                            <!--begin::Page title-->
                            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                <!--begin::Title-->

                                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item text-muted">
                                        <a href="{{url('/')}}/dashboard"
                                            class="text-muted text-hover-primary">Главная</a>
                                    </li>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item">
                                        <span class="bullet bg-gray-300 w-5px h-2px"></span>
                                    </li>

                                    <li class="breadcrumb-item text-dark">Пакеты настроек по лидам</li>
                                    <!--end::Item-->
                                </ul>
                                <!--end::Title-->
                            </div>
                            <!--end::Page title-->

                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Toolbar-->

                    <!--begin::Post-->
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <!--begin::Container-->
                        <div id="kt_content_container" class="container-fluid">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-line-tabs fs-6">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab"
                                                href="#kt_tab_pane_1">Пакеты</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Рекрутеры</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                        <div class="card js-data-table-wrap">
                                            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                                <!--begin::Card title-->
                                                <div class="card-title">
                                                    Пакеты настроек по лидам
                                                </div>
                                                <!--end::Card title-->
                                                <!--begin::Card toolbar-->
                                                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                                    <button id="add-settings-package"
                                                        class="btn btn-primary">Добавить</button>
                                                </div>
                                                <!--end::Card toolbar-->
                                            </div>
                                            <!--begin::Card body-->
                                            <div class="card-body pt-0">
                                                <!--begin::Table-->
                                                <div class="table-responsive">
                                                    <table
                                                        class="js-data-table table align-middle table-row-dashed fs-6 gy-3"
                                                        data-route="{{route('leads.settings.json')}}"
                                                        data-tpl="LeadSettings">
                                                        <!--begin::Table head-->
                                                        <thead>
                                                            <!--begin::Table row-->
                                                            <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                                                <th class="max-w-65px sorting_disabled">Id</th>
                                                                <th class="max-w-85px sorting_disabled">Название</th>
                                                                <th class="max-w-85px sorting_disabled">Кампании</th>
                                                                <th class="max-w-65px sorting_disabled">Статусы</th>
                                                                <th class="max-w-65px sorting_disabled">Специальности
                                                                </th>
                                                                <th class="max-w-65px sorting_disabled">Время жизни
                                                                    лида, дней</th>
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
                                    </div>
                                    <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                        <div class="card js-data-table-wrap">
                                            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                                <!--begin::Card title-->
                                                <div class="card-title">
                                                    Рекрутеры
                                                </div>
                                                <!--end::Card title-->
                                                <!--begin::Card toolbar-->
                                                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">

                                                </div>
                                                <!--end::Card toolbar-->
                                            </div>
                                            <!--begin::Card body-->
                                            <div class="card-body pt-0">
                                                <!--begin::Table-->
                                                <div class="table-responsive">
                                                    <table
                                                        class="js-data-table table align-middle table-row-dashed fs-6 gy-3"
                                                        data-route="{{route('users.lead-settings.json')}}"
                                                        data-tpl="UsersLeadSettings" id="UsersLeadSettingsTable">
                                                        <!--begin::Table head-->
                                                        <thead>
                                                            <!--begin::Table row-->
                                                            <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                                                <th class="max-w-55px sorting_disabled">Id</th>
                                                                <th class="max-w-85px">Имя</th>
                                                                <th class="max-w-85px">Фамилия</th>

                                                                @foreach ($all_packages as $item)
                                                                <th class="max-w-85px text-center">{{$item->name}}</th>
                                                                @endforeach
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
                                    </div>
                                </div>




                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Post-->
                </div>
                <!--end::Content-->

                @include('includes.Footer')
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->

    <div class="modal fade" tabindex="-1" id="modal-lead-settings">
        <div class="modal-dialog modal-dialog_wide">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Добавление настроек</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="row mb-5">
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="required fs-5 fw-bold mb-2">Название</label>
                                        <input id="modal-lead-settings-input-name"
                                            class="form-control form-control-sm form-control-solid" type="text" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col">
                                    <label class="fs-5 fw-bold">Компании</label>

                                    <div id="modal_leads_sources_container">
                                        <!-- sources -->
                                    </div>
                                    {{-- <div class="d-flex flex-column mb-0 fv-row">
                                <label class="fs-5 fw-bold mb-2">Компании</label>
                                
                                <select id="modal_leads_companies_select" class="form-select form-select-sm form-select-solid" multiple></select>
                            </div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="row mb-5">
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="fs-5 fw-bold mb-2">Статусы</label>

                                        <select id="modal_leads_statuses_select"
                                            class="form-select form-select-sm form-select-solid" multiple></select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="fs-5 fw-bold mb-2">Специальность</label>

                                        <select id="modal_leads_speciality_select"
                                            class="form-select form-select-sm form-select-solid" multiple></select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="fs-5 fw-bold mb-2">Время жизни лида, дней</label>
                                        <input id="modal-lead-settings-input-lifetime"
                                            class="form-control form-control-sm form-control-solid" type="text" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-10" style="display: none !important">
                        <div class="col">
                            <div class="row mb-5">
                                <div class="col">
                                    <h3 class="fs-5 fw-bold">Промежутки между недозвонами</h3>

                                    <div class="d-flex flex-column mb-0 fv-row mb-3">
                                        <label class="form-label">1 недозвон</label>

                                        <select id="modal_leads_failed_call_1_delay_select"
                                            class="form-select form-select-sm form-select-solid">
                                            <option value="0">Без задержки</option>
                                            <option value="15">15 минут</option>
                                            <option value="30">30 минут</option>
                                            <option value="45">45 минут</option>
                                            <option value="60">1 час</option>
                                            <option value="120">2 часа</option>
                                            <option value="180">3 часа</option>
                                            <option value="360">6 часов</option>
                                            <option value="720">12 часов</option>
                                            <option value="1440">24 часа</option>
                                            <option value="2880">2 дня</option>
                                            <option value="4320">3 дня</option>
                                        </select>
                                    </div>

                                    <div class="d-flex flex-column mb-0 fv-row mb-3">
                                        <label class="form-label">2 недозвона</label>

                                        <select id="modal_leads_failed_call_2_delay_select"
                                            class="form-select form-select-sm form-select-solid">
                                            <option value="0">Без задержки</option>
                                            <option value="15">15 минут</option>
                                            <option value="30">30 минут</option>
                                            <option value="45">45 минут</option>
                                            <option value="60">1 час</option>
                                            <option value="120">2 часа</option>
                                            <option value="180">3 часа</option>
                                            <option value="360">6 часов</option>
                                            <option value="720">12 часов</option>
                                            <option value="1440">24 часа</option>
                                            <option value="2880">2 дня</option>
                                            <option value="4320">3 дня</option>
                                        </select>
                                    </div>

                                    <div class="d-flex flex-column mb-0 fv-row">
                                        <label class="form-label">3 недозвона</label>

                                        <select id="modal_leads_failed_call_3_delay_select"
                                            class="form-select form-select-sm form-select-solid">
                                            <option value="0">Без задержки</option>
                                            <option value="15">15 минут</option>
                                            <option value="30">30 минут</option>
                                            <option value="45">45 минут</option>
                                            <option value="60">1 час</option>
                                            <option value="120">2 часа</option>
                                            <option value="180">3 часа</option>
                                            <option value="360">6 часов</option>
                                            <option value="720">12 часов</option>
                                            <option value="1440">24 часа</option>
                                            <option value="2880">2 дня</option>
                                            <option value="4320">3 дня</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row mb-5">
                                <div class="col">
                                    <div class="d-flex flex-column mb-0 fv-row mb-5">
                                        <label class="fs-5 fw-bold">Промежуток между "Не заинтересован"</label>

                                        <select id="modal_leads_not_interested_delay_select"
                                            class="form-select form-select-sm form-select-solid">
                                            <option value="0">Без задержки</option>
                                            <option value="15">15 минут</option>
                                            <option value="30">30 минут</option>
                                            <option value="45">45 минут</option>
                                            <option value="60">1 час</option>
                                            <option value="120">2 часа</option>
                                            <option value="180">3 часа</option>
                                            <option value="360">6 часов</option>
                                            <option value="720">12 часов</option>
                                            <option value="1440">24 часа</option>
                                            <option value="2880">2 дня</option>
                                            <option value="4320">3 дня</option>
                                        </select>
                                    </div>

                                    <div class="d-flex flex-column mb-0 fv-row mb-5">
                                        <label class="fs-5 fw-bold">Промежуток между "Не оставлял заявку"</label>

                                        <select id="modal_leads_not_liquidity_delay_select"
                                            class="form-select form-select-sm form-select-solid">
                                            <option value="0">Без задержки</option>
                                            <option value="15">15 минут</option>
                                            <option value="30">30 минут</option>
                                            <option value="45">45 минут</option>
                                            <option value="60">1 час</option>
                                            <option value="120">2 часа</option>
                                            <option value="180">3 часа</option>
                                            <option value="360">6 часов</option>
                                            <option value="720">12 часов</option>
                                            <option value="1440">24 часа</option>
                                            <option value="2880">2 дня</option>
                                            <option value="4320">3 дня</option>
                                        </select>
                                    </div>

                                    <div class="d-flex flex-column mb-0 fv-row mb-3">
                                        <label class="fs-5 fw-bold">Промежуток между "Горячий"</label>

                                        <select id="modal_leads_liquidity_delay_select"
                                            class="form-select form-select-sm form-select-solid">
                                            <option value="0">Без задержки</option>
                                            <option value="15">15 минут</option>
                                            <option value="30">30 минут</option>
                                            <option value="45">45 минут</option>
                                            <option value="60">1 час</option>
                                            <option value="120">2 часа</option>
                                            <option value="180">3 часа</option>
                                            <option value="360">6 часов</option>
                                            <option value="720">12 часов</option>
                                            <option value="1440">24 часа</option>
                                            <option value="2880">2 дня</option>
                                            <option value="4320">3 дня</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end::Modal body-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                    <button id="modal-lead-settings-save-btn" type="button" class="btn btn-primary btn-sm">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var all_packages = '@json($all_packages)';

    </script>

    @include('includes.global_scripts')
    <!--begin::Page Vendors Javascript(used by this page)-->
    <script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Page Vendors Javascript-->

    <script src="{{ mix('/js/datatables.js') }}"></script>

    <script src="{{ mix('/js/leads-settings.js') }}"></script>

</body>
<!--end::Body-->

</html>
