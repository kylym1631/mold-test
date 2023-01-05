<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <title>Сотрудники</title>

    @include('includes.global_styles')
    <style>

        .sorting_disabled.sorting_asc:after {
            display: none !important;
        }

        .sorting_disabled.sorting_desc:after {
            display: none !important;
        }
    </style>

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
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none">
											<path
                                                d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                                fill="currentColor"/>
											<path opacity="0.3"
                                                  d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                                  fill="currentColor"/>
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
                                 class="h-30px"/>
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
                                    <a href="{{url('/')}}/dashboard" class="text-muted text-hover-primary">Главная</a>
                                </li>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-300 w-5px h-2px"></span>
                                </li>

                                <li class="breadcrumb-item text-dark">Сотрудники</li>
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
                        <!--begin::Card-->
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
                                        <input type="text" class="js-search-input form-control form-control-solid w-250px ps-14" placeholder="Поиск ">
                                    </div>
                                    <!--end::Search-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                    @if (
                                        !isset($controller_name) 
                                        || $controller_name != 'AccountantsDepartmentController'
                                    )
                                    <div class="w-200px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" name="group" data-placeholder="Роли" multiple>
                                        @foreach ($roles as $item)
                                            @if (Auth::user()->isAdmin()
                                            || Auth::user()->hasPermission('user.view.role.'. $item->id))
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                    @endif

                                    <div class="w-100 mw-150px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" name="status" data-allow-clear="false">
                                            <option value="1" selected>Активный</option>
                                            <option value="2">Не активный</option>
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                    
                                    @if (
                                        (!isset($controller_name) 
                                        || $controller_name != 'AccountantsDepartmentController')
                                        && Auth::user()->hasPermission('user.create')
                                    )
                                    <!--begin::Add product-->
                                    <a id="users__add_btn" href="javascript:;" class="btn btn-primary">Добавить</a>
                                    <!--end::Add product-->
                                    @endif
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Table-->

                                @if (
                                    isset($controller_name) 
                                    && $controller_name == 'AccountantsDepartmentController'
                                )
                                <div class="table-responsive">
                                    <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('accountants-department.json')}}">
                                        <!--begin::Table head-->
                                        <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                            <th class="max-w-55px sorting_disabled">Id</th>
                                            <th class="max-w-85px" data-name="firstName">Имя</th>
                                            <th class="max-w-85px" data-name="lastName">Фамилия</th>
                                            <th class="max-w-65px" data-name="phone">Телефон</th>
                                            <th class="max-w-65px" data-name="email">Email</th>
                                        </tr>
                                        <!--end::Table row-->
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                        <!--end::Table body-->
                                    </table>
                                </div>

                                @else

                                <div class="table-responsive">
                                    <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('users.json')}}">
                                        <!--begin::Table head-->
                                        <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                            <th class="max-w-55px sorting_disabled">Id</th>
                                            <th class="max-w-85px" data-name="firstName">Имя</th>
                                            <th class="max-w-85px" data-name="lastName">Фамилия</th>
                                            <th class="min-w-125px" data-name="group_id">Роль</th>
                                            <th class="max-w-65px" data-name="phone">Телефон</th>
                                            <th class="max-w-65px" data-name="email">Email</th>
                                            <th class="w-35px sorting_disabled">Файл</th>
                                            <th class="min-w-100px sorting_disabled">Статус</th>
                                        </tr>
                                        <!--end::Table row-->
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold"></tbody>
                                        <!--end::Table body-->
                                    </table>
                                </div>
                                @endif
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
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

<div class="modal fade" tabindex="-1" id="modal_users_add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Добавление пользователя</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal body-->
            <div class="modal-body">

                <input type="hidden" id="modal_users_add__id">

                <div class="row mb-5">
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Фамилия</label>
                            <input id="modal_users_add__lastName"
                                   class="form-control form-control-sm form-control-solid" type="text"/>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Имя</label>
                            <input id="modal_users_add__firstName"
                                   class="form-control form-control-sm form-control-solid" type="text"/>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Телефон</label>
                            <input id="modal_users_add__phone" class="form-control form-control-sm form-control-solid"
                                   type="text"/>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Email</label>
                            <input id="modal_users_add__email" class="form-control form-control-sm form-control-solid"
                                   type="email"/>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Роль</label>

                            @if (Auth::user()->isAdmin())
                                <select id="modal_users_add__group_id" class="form-select form-select-sm form-select-solid">
                                @if ($roles)
                                    @foreach ($roles as $role)
                                        <option value="{{$role['id']}}">{{$role['name']}}</option>    
                                    @endforeach
                                @endif
                                </select>
                            @else
                                <select id="modal_users_add__group_id" class="form-select form-select-sm form-select-solid">
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="col-6 mb-5">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Статус</label>
                            <select id="modal_users_add__activation"
                                    class="form-select form-select-sm form-select-solid">
                                <option value="1">Активный</option>
                                <option value="2">Не активный</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-6 mb-5">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Язык интерфейса</label>
                            <select id="modal_users_add__lang"
                                    class="form-select form-select-sm form-select-solid">
                                <option value="ru">Русский</option>
                                <option value="pl">Polski</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: none;"  class="row mb-5">
                    <div  сlass="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Cчет</label>
                            <input id="modal_users_add__account" class="form-control form-control-sm form-control-solid"
                                   type="text"/>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->isAdmin())
                <div id="modal_users_RecruitmentDirector_field" class="row mb-5 active">
                    <div class="col-12">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Руководитель</label>
                            
                            <select id="modal_users_RecruitmentDirector_id" name="modal_users_RecruitmentDirector_name" class="form-select form-select-sm form-select-solid">
                            </select>
                        </div>
                    </div>
                </div>
                @endif
                
                @if (Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector())
                <div id="modal_users_leads_companies_field" class="row mb-5" style="display: none">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2">Пакеты настроек лидов</label>
                            <select id="modal_users_leads_companies_select" class="form-select form-select-sm form-select-solid" multiple></select>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="required fs-5 fw-bold mb-2">Пароль</label>

                            <div class="input-group input-group-solid mb-5">
                                <span onclick="generatePassword();" style="cursor: pointer" class="input-group-text"
                                ><i class="far fa-keyboard fs-6"></i></span>
                                <input id="modal_users_add__password"
                                       class="form-control form-control-sm form-control-solid"
                                       type="text"/>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="fv-row">
                            <!--begin::Dropzone-->
                            <div class="dropzone" id="kt_ecommerce_add_product_media">
                                <!--begin::Message-->
                                <div class="dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Загрузить документ</h3>
                                        <span class="fs-7 fw-bold text-gray-400">Перетащите документ сюда</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                    </div>
                </div>

                {{-- @if (Auth::user()->isAdmin())
                <div class="row mt-10">
                    <div class="col">
                        <label class="fs-5 fw-bold">Разрешения</label>

                        <div id="modal_users_add__permission">
                            <!-- fields -->
                        </div>
                    </div>
                </div>
                @endif --}}

            </div>
            <!--end::Modal body-->
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button id="modal_users_add__save" type="button" class="btn btn-primary btn-sm">Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

@include('includes.global_scripts')
<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Page Vendors Javascript-->

<script>
    var allRoles = @json($roles);
</script>

<script src="{{ mix('/js/datatables.js') }}"></script>
<script src="{{ mix('/js/users.js') }}"></script>

</body>
<!--end::Body-->
</html>
