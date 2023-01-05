<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <title>Добавить жилье</title>

    @include('includes.global_styles')
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body"
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
    style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

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

                                    <li class="breadcrumb-item text-dark">Добавить жилье</li>
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

                                <div class="card-body pt-15">
                                    <form id="housing-form" action="{{route('housing.create')}}" method="POST"
                                        enctype="multipart/form-data">

                                        @csrf

                                        @if(isset($housing))
                                        <input type="hidden" id="id" name="id" value="{{ request()->get('id') }}">
                                        @endif

                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Название</label>
                                                    <input @if(isset($housing)) value="{{$housing->title}}" @endif
                                                        id="title"
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="title" readonly 
                                                        placeholder="{ID}">
                                                </div>

                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Адрес</label>
                                                    <textarea id="address" name="address"
                                                        class="form-control form-control-sm form-control-solid">@if(isset($housing)){{$housing->address}}@endif</textarea>
                                                </div>

                                                <div class="row mb-5">
                                                    <div class="col-6">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label class="required fs-5 fw-bold mb-2">Индекс</label>
                                                            <input @if(isset($housing)) value="{{$housing->zip_code}}"
                                                                @endif id="zip_code"
                                                                class="form-control form-control-sm form-control-solid"
                                                                type="text" name="zip_code">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label class="required fs-5 fw-bold mb-2">Место работы</label>
                                                            <select id="clients_add_select"
                                                                class="form-select form-select-sm form-select-solid"
                                                                name="clients[]" multiple>
                                                                @if(isset($housing) && $housing->clients != null)
                                                                @foreach ($housing->clients as $client)
                                                                <option value="{{$client->id}}" selected>
                                                                    {{$client->name}}</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-10">
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label class="required fs-5 fw-bold mb-2">Количество
                                                                мест</label>
                                                            <input @if(isset($housing))
                                                                value="{{$housing->places_count}}" @endif
                                                                id="places_count"
                                                                class="form-control form-control-sm form-control-solid"
                                                                type="text" name="places_count">
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label class="required fs-5 fw-bold mb-2">Стоимость
                                                                аренды</label>
                                                            <input @if(isset($housing)) value="{{$housing->cost}}"
                                                                @endif id="cost"
                                                                class="form-control form-control-sm form-control-solid"
                                                                type="text" name="cost">
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label class="required fs-5 fw-bold mb-2">Стоимость за
                                                                сутки</label>
                                                            <input @if(isset($housing))
                                                                value="{{$housing->cost_per_day}}" @endif
                                                                id="cost_per_day"
                                                                class="form-control form-control-sm form-control-solid"
                                                                type="text" name="cost_per_day">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-10 mb-5">
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column mb-0 fv-row">
                                                            <label class="required fs-5 fw-bold mb-2">Статус</label>
                                                            <select name="active" class="form-select  form-select-sm form-select-solid">
                                                                <option value="1" 
                                                                @selected(!isset($housing) || $housing->active == 1)>
                                                                    Активный
                                                                </option>
                                                                <option value="0" 
                                                                @selected(isset($housing) && $housing->active == 0)>
                                                                    Не активный
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>  
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <h3 class="fw-bolder m-0">Контакты</h3>

                                                <div id="husing-contacts-container" class="mt-5">
                                                    @if($housing && $housing->contacts)
                                                    @foreach($housing->contacts as $contact)
                                                    <div class="housing-contact">
                                                        <div class="row mb-5">
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label
                                                                        class="required fs-5 fw-bold mb-2">Имя</label>
                                                                    <input value="{{$contact->firstName}}"
                                                                        class="form-control form-control-sm form-control-solid"
                                                                        type="text" name="firstName[]" />
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label
                                                                        class="required fs-5 fw-bold mb-2">Фамилия</label>
                                                                    <input value="{{$contact->lastName}}"
                                                                        class="form-control form-control-sm form-control-solid"
                                                                        type="text" name="lastName[]" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-5">
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label
                                                                        class="required fs-5 fw-bold mb-2">Email</label>
                                                                    <input value="{{$contact->email}}"
                                                                        class="form-control form-control-sm form-control-solid"
                                                                        type="text" name="email[]" />
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="d-flex flex-column mb-0 fv-row">
                                                                    <label
                                                                        class="required fs-5 fw-bold mb-2">Телефон</label>
                                                                    <input value="{{$contact->phone}}"
                                                                        class="form-control form-control-sm form-control-solid"
                                                                        type="text" name="phone[]" />
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <button style="margin-top: 28px;" type="button"
                                                                    class="js-delete-housing-contact btn btn-light  btn-sm delete_contact">Удалить</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>

                                                <div class="row mt-5">
                                                    <div class="col">
                                                        <button type="button" id="add-housing-contacts"
                                                            class="btn btn-warning btn-sm">Добавить</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-6">
                                                <div class="fv-row">
                                                    <!--begin::Dropzone-->
                                                    <div class="dropzone dz-clickable housing-gallery" id="gallery">
                                                        <!--begin::Message-->
                                                        <div class="dz-message needsclick">
                                                            <!--begin::Icon-->
                                                            <i
                                                                class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                                            <!--end::Icon-->

                                                            <!--begin::Info-->
                                                            <div class="ms-4">
                                                                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Загрузить
                                                                    фотографии</h3>
                                                                <span class="fs-7 fw-bold text-gray-400">Перетащите
                                                                    фотографии сюда. Максимально 25 файлов.<br>
                                                                    Максимальный размер 5Мб. Формат файла: jpeg, jpg,
                                                                    png</span>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                    </div>
                                                    <!--end::Dropzone-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-15">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <span class="indicator-label">Сохранить</span>
                                                <span class="indicator-progress">
                                                    Сохранение...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                    </form>

                                    @if ($housing)
                                    <hr class="mt-10">

                                    <div class="card-header mb-5" style="padding: 0">
                                        <div class="card-title m-0">
                                            <h3 class="fw-bolder m-0">Комнаты</h3>
                                        </div>
                                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                            <button type="button" data-housing-id="{{$housing->id}}"
                                                class="js-add-room btn btn-primary btn-sm">Добавить</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3"
                                            data-route="{{route('housing.rooms.json', $housing->id)}}"
                                            data-tpl="HousingRooms">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                                    <th class="max-w-65px sorting_disabled">Номер комнаты</th>
                                                    <th class="max-w-65px sorting_disabled">Кол-во спальных мест</th>
                                                    <th class="max-w-85px sorting_disabled">Заселено</th>
                                                    <th class="max-w-85px sorting_disabled">Свободно</th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <tbody class="text-gray-600 fw-bold"></tbody>
                                            <!--end::Table body-->
                                        </table>
                                    </div>    
                                    @endif
                                    
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

    @include('housing.includes.add-room')

    @include('includes.global_scripts')

    <!--begin::Page Vendors Javascript(used by this page)-->
    <script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Page Vendors Javascript-->

    <script src="{{ mix('/js/datatables.js') }}"></script>

    <script>
        var existingHousingFiles = @json($housing ? $housing['gallery_files'] : []);
    </script>

    <script src="{{ mix('/js/add-housing.js') }}"></script>

    <script type="text/template" id="contacts-template">
        <div class="housing-contact">
            <div class="row mb-5">
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="required fs-5 fw-bold mb-2">Имя</label>
                        <input
                            class="form-control form-control-sm form-control-solid"
                            type="text" name="firstName[]" />
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="required fs-5 fw-bold mb-2">Фамилия</label>
                        <input
                            class="form-control form-control-sm form-control-solid"
                            type="text" name="lastName[]" />
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="required fs-5 fw-bold mb-2">Email</label>
                        <input
                            class="form-control form-control-sm form-control-solid"
                            type="text" name="email[]" />
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="required fs-5 fw-bold mb-2">Телефон</label>
                        <input
                            class="form-control form-control-sm form-control-solid"
                            type="text" name="phone[]" />
                    </div>
                </div>
                <div class="col">
                    <button style="margin-top: 28px;" type="button" class="js-delete-housing-contact btn btn-light  btn-sm delete_contact">Удалить</button>
                </div>
            </div>
        </div>
    </script>

</body>
<!--end::Body-->

</html>
