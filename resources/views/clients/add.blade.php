<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <title>Клиенты</title>

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

                                <li class="breadcrumb-item text-muted">
                                    <a href="{{url('/')}}/clients" class="text-muted text-hover-primary">Клиенты</a>
                                </li>

                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-300 w-5px h-2px"></span>
                                </li>


                                <li class="breadcrumb-item text-dark">Добавить клиента</li>
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
                        <div class="card">

                            <!--begin::Card body-->
                            <div class="card-body pt-10">
                                @if(request()->has('id'))
                                <input type="hidden" id="id" value="{{request('id')}}">
                                @endif

                                <div class="row mb-5">
                                    <div class="col-6">
                                        <div class="row mb-5">
                                            <div class="col">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Название</label>
                                                    <input id="name"
                                                           @if($client != null) value="{{$client->name}}" @endif
                                                           class="form-control form-control-sm form-control-solid"
                                                           type="text"/>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="coordinator_id"
                                                       class="required form-label">Координатор</label>
                                                <select id="coordinator_id"
                                                        class="form-select form-select-sm form-select-solid"></select>
                                            </div>

                                        </div>
                                        <div class="row mb-5">
                                            <div class="col">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label for="address"
                                                           class="required fs-5 fw-bold mb-2">Адрес</label>
                                                    <input id="address"
                                                           @if($client != null) value="{{$client->address}}" @endif
                                                           class="form-control form-control-sm form-control-solid"
                                                           type="text"/>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mb-5">
                                            <div class="col">
                                                <label for="industry_id" class="required form-label">Отрасль</label>
                                                <select id="industry_id" multiple="multiple"
                                                        class="form-select form-select-sm form-select-solid"></select>
                                            </div>
                                            <div class="col">
                                                <label for="work_place_id" class="required form-label">Место
                                                    работы</label>
                                                <select id="work_place_id" multiple="multiple"
                                                        class="form-select form-select-sm form-select-solid"></select>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col">
                                                <label for="nationality_id" class="required form-label">Национальность</label>
                                                <select id="nationality_id" multiple="multiple"
                                                        class="form-select form-select-sm form-select-solid"></select>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col">
                                                <label for="housing_id" class="form-label">Жилье</label>
                                                <select id="housing_id" multiple="multiple"
                                                        class="form-select form-select-sm form-select-solid"></select>
                                            </div>
                                        </div>

                                        <div class="mt-10">
                                            <h3>Часы работы</h3>

                                            <div class="row mb-5">
                                                <div class="col">
                                                    <label for="work_time_format" class="form-label">Формат</label>
                                                    <select id="work_time_format"
                                                            class="form-select form-select-sm form-select-solid">
                                                        <option value="natural" 
                                                        @selected($client != null
                                                        && (!$client->work_time_format 
                                                        || $client->work_time_format == 'natural'))>
                                                        ЧЧ:ММ - часы</option>
                                                        <option value="decimal"
                                                        @selected($client != null && $client->work_time_format == 'decimal')>
                                                        ЧЧ,ММ - десятичные</option>
                                                    </select>
                                                </div>

                                                <div class="col">
                                                    <div class="d-flex flex-column mb-0 fv-row">
                                                        <label class="required form-label">Мин. кол-во часов</label>
                                                        <input id="min_work_time"
                                                               @if($client != null) 
                                                               value="{{$client->min_work_time_dec}}" 
                                                               @endif
                                                               class="form-control form-control-sm form-control-solid"
                                                               type="text"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-10 mb-5">
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required form-label fs-5 fw-bold">Статус</label>
                                                    <select id="active" class="form-select  form-select-sm form-select-solid">
                                                        <option value="1" 
                                                        @selected($client == null || $client->active == 1)>
                                                            Активный
                                                        </option>
                                                        <option value="0" 
                                                        @selected($client != null && $client->active == 0)>
                                                            Не активный
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>  
                                        </div>

                                        @include('clients.includes.add-position')

                                        <div class="row mt-10 mb-5">
                                            <div class="col">
                                                <button id="save_vacancies" type="button"
                                                        class="btn btn-warning btn-sm">Сохранить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h3>Контакты</h3>
                                        <div class="contacts">
                                            @if($client != null)
                                                @foreach($client->contacts as $contact)
                                                    <div class="contact">
                                                        <div class="row mb-5">
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label for="salary"
                                                                           class="required fs-5 fw-bold mb-2">Имя</label>
                                                                    <input
                                                                        value="{{$contact->firstName}}"
                                                                        class="form-control form-control-sm form-control-solid cfirstName"
                                                                        type="text"/>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label for="salary"
                                                                           class="required fs-5 fw-bold mb-2">Фамилия</label>
                                                                    <input
                                                                        value="{{$contact->lastName}}"
                                                                        class="form-control form-control-sm form-control-solid clastName"
                                                                        type="text"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-5">
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label for="salary"
                                                                           class="required fs-5 fw-bold mb-2">Должность</label>
                                                                    <input
                                                                        value="{{$contact->position}}"
                                                                        class="form-control form-control-sm form-control-solid cposition"
                                                                        type="text"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-5">
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label for="salary"
                                                                           class="required fs-5 fw-bold mb-2">Email</label>
                                                                    <input
                                                                        value="{{$contact->email}}"
                                                                        class="form-control form-control-sm form-control-solid cemail"
                                                                        type="text"/>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label for="salary"
                                                                           class="required fs-5 fw-bold mb-2">Телефон</label>
                                                                    <input
                                                                        value="{{$contact->phone}}"
                                                                        class="form-control form-control-sm form-control-solid cphone"
                                                                        type="text"/>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <button style="margin-top: 28px;" type="button"
                                                                        class="btn btn-light  btn-sm delete_contact">
                                                                    Удалить
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>


                                        <div class="row mb-5">
                                            <div class="col">
                                                <button id="add_contact" type="button"
                                                        class="btn btn-warning btn-sm">Добавить
                                                </button>
                                            </div>
                                            <div class="col">
                                            </div>
                                        </div>

                                        <div style="display:none;" id="template_add">
                                            <div class="contact">
                                                <div class="row mb-5">
                                                    <div class="col">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label for="salary"
                                                                   class="required fs-5 fw-bold mb-2">Имя</label>
                                                            <input
                                                                class="form-control form-control-sm form-control-solid cfirstName"
                                                                type="text"/>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label for="salary"
                                                                   class="required fs-5 fw-bold mb-2">Фамилия</label>
                                                            <input
                                                                class="form-control form-control-sm form-control-solid clastName"
                                                                type="text"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <div class="col">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label for="salary"
                                                                   class="required fs-5 fw-bold mb-2">Должность</label>
                                                            <input
                                                                class="form-control form-control-sm form-control-solid cposition"
                                                                type="text"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-5">
                                                    <div class="col">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label for="salary"
                                                                   class="required fs-5 fw-bold mb-2">Email</label>
                                                            <input
                                                                class="form-control form-control-sm form-control-solid cemail"
                                                                type="text"/>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label for="salary"
                                                                   class="required fs-5 fw-bold mb-2">Телефон</label>
                                                            <input
                                                                class="form-control form-control-sm form-control-solid cphone"
                                                                type="text"/>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <button style="margin-top: 28px;" type="button"
                                                                class="btn btn-light  btn-sm delete_contact">
                                                            Удалить
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        @if($client != null)
                        <div class="card mt-10">

                            <div class="card-body">
                                <ul class="nav nav-tabs nav-line-tabs fs-6">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab"
                                            href="#kt_tab_pane_1">Кандидаты</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Жилье</a>
                                    </li>
                                </ul>
                            </div>


                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                    <div class="card shadow-none js-data-table-wrap">
                                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                            <!--begin::Card title-->
                                            <div class="card-title">
                                                <!--begin::Search-->
                                                <div class="d-flex align-items-center position-relative my-1">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none">
                                                            <rect opacity="0.5" x="17.0365" y="15.1223"
                                                                width="8.15546" height="2" rx="1"
                                                                transform="rotate(45 17.0365 15.1223)"
                                                                fill="currentColor"></rect>
                                                            <path
                                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                                fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <input type="text"
                                                        class="js-search-input form-control form-control-solid w-250px ps-14"
                                                        placeholder="Поиск">
                                                </div>
                                                <!--end::Search-->
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                                <input type="hidden" name="view" value="clients.view"
                                                    class="js-filter">
                                                <input type="hidden" name="clients[]"
                                                    value="{{request()->get('id')}}" class="js-filter">

                                                <div class="w-200px">
                                                    <select
                                                        class="js-filter form-select form-select form-select-solid"
                                                        data-ajax-opt="/search/candidate/vacancy" name="vacancies"
                                                        data-placeholder="Вакансия" multiple></select> </div>

                                                <div class="w-150px">
                                                    <!--begin::Select2-->
                                                    <select class="js-filter form-select form-select-solid"
                                                        data-placeholder="Статус" name="status" multiple>

                                                        @if(Auth::user()->isKoordinator())
                                                        <option value="8" selected>Трудоустроен</option>
                                                        <option value="7" selected>Заселен</option>
                                                        <option value="9" selected>Приступил к Работе</option>
                                                        <option value="11">Уволен</option>
                                                        @else
                                                        <option value="1">Новый кандидат</option>
                                                        <option value="2">Лид</option>
                                                        <option value="3">Отказ</option>
                                                        <option value="4">Оформлен</option>
                                                        <option value="5">Архив</option>
                                                        <option value="6">Подтвердил Выезд</option>
                                                        <option value="7">Заселен</option>
                                                        <option value="8">Трудоустроен</option>
                                                        <option value="9">Приступил к Работе</option>
                                                        <option value="11">Уволен</option>
                                                        <option value="12">Приехал</option>
                                                        <option value="14">Перезвонить (Рекрутер)</option>
                                                        <option value="15">Недозвон</option>
                                                        <option value="16">Оформление</option>
                                                        <option value="19">В пути</option>
                                                        <option value="20">Не доехал</option>
                                                        <option value="21">Перезвонить (Логист)</option>
                                                        <option value="22">Не рекрутируем</option>
                                                        @endif

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
                                                <table
                                                    class="js-data-table table align-middle table-row-dashed fs-6 gy-3"
                                                    data-route="{{route('candidates.json')}}">
                                                    <!--begin::Table head-->
                                                    <thead>
                                                        <!--begin::Table row-->
                                                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                                            <th class="sorting_disabled">Id</th>
                                                            <th class="sorting_disabled">Имя</th>
                                                            <th class="sorting_disabled">Фамилия</th>
                                                            <th class="sorting_disabled">Вакансия</th>
                                                            <th class="sorting_disabled">Жилье</th>
                                                            <th class="sorting_disabled">Телефон</th>
                                                            <th class="sorting_disabled">Статус</th>
                                                        </tr>
                                                        <!--end::Table row-->
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-bold"></tbody>
                                                    <!--end::Table body-->
                                                </table>
                                            </div>

                                            <!--end::Table-->
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                    @include('clients.includes.housing')
                                </div>
                            </div>

                            <!--end::Card body-->
                        </div>
                        @endif

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
<!--begin::Modal-->


@include('includes.global_scripts')

<script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ mix('/js/datatables.js') }}"></script>

<script>
    var clientOpt = {
        id: '{{request("id")}}',
        Coordinator: @json($Coordinator),
        h_v_industry: @json($h_v_industry),
        h_v_city: @json($h_v_city),
        h_v_nationality: @json($h_v_nationality),
        h_v_housing: @json($h_v_housing),
    };
</script>

<script src="{{ mix('/js/add-client.js') }}"></script>

</body>
<!--end::Body-->
</html>
