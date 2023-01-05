<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <title>Добавить Машины</title>

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

                                    <li class="breadcrumb-item text-dark">Добавить машину</li>
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
                                    <form id="item-form" action="{{route('cars.create')}}" method="POST"
                                        enctype="multipart/form-data">

                                        @csrf

                                        @if(isset($item))
                                        <input type="hidden" id="id" name="id" value="{{ request()->get('id') }}">
                                        @endif

                                        <div class="row mb-5">
                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Марка</label>
                                                    <input @if(isset($item)) value="{{$item->brand}}" @endif
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="brand">
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Модель</label>
                                                    <input @if(isset($item)) value="{{$item->model}}" @endif
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="model">
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Номер</label>
                                                    <input @if(isset($item)) value="{{$item->number}}" @endif
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="number">
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Год</label>
                                                    
                                                    <select name="year"
                                                        class="form-select  form-select-sm form-select-solid">
                                                        @if (!isset($item))
                                                        <option></option>
                                                        @endif
                                                        
                                                        @for ($y = 1991; $y <= date("Y"); $y++)
                                                        <option value="{{$y}}" 
                                                        @selected(isset($item) && $item->year == $y)
                                                        >{{$y}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            @if(Auth::user()->isAdmin() || Auth::user()->hasPermission(['cars', 'cars.view']))
                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Ответственный за машину</label>

                                                    <select id="coordinator_id"
                                                        name="user_id"
                                                        class="form-select  form-select-sm form-select-solid">
                                                        @if (isset($item) && $item->user)
                                                        <option value="{{ $item->user->id }}" selected>{{ $item->user->firstName .' '. $item->user->lastName}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Статус</label>

                                                    <select name="active"
                                                        class="form-select  form-select-sm form-select-solid">
                                                        <option value="1" @selected(isset($item) && $item->active == 1)>Активная</option>
                                                        <option value="0" @selected(isset($item) && $item->active == 0)>Не активная</option>
                                                    </select>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="row mb-5">
                                            <div class="col-3">
                                                <div class="d-flex form-check form-check-sm form-check-custom form-check-solid mt-10" style="align-items: center; gap: 10px"> 
                                                    <input id="is-rent" class="form-check-input" type="checkbox" name="is_rent" value="1"> 
                                                    <label for="is-rent" class="fs-5 fw-bold">Аренда</label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div id="rent-cost-block" style="display: none">
                                                    <div class="d-flex flex-column mb-5 fv-row" >
                                                        <label class="required fs-5 fw-bold mb-2">Стоимость аренды</label>
                                                        <input @if(isset($item)) value="{{$item->rent_cost}}" @endif
                                                            class="form-control form-control-sm form-control-solid"
                                                            type="text" name="rent_cost">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div id="landlord-block" style="display: none">
                                                    <div class="d-flex flex-column mb-5 fv-row">
                                                        <label class="required fs-5 fw-bold mb-2">Арендодатель</label>
                                                        <input @if(isset($item)) value="{{$item->landlord}}" @endif
                                                            class="form-control form-control-sm form-control-solid"
                                                            type="text" name="landlord">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-3">
                                                <div class="fv-row">
                                                    <!--begin::Dropzone-->
                                                    <div class="dropzone dz-clickable" id="document">
                                                        <!--begin::Message-->
                                                        <div class="dz-message needsclick">
                                                            <!--begin::Icon-->
                                                            <i
                                                                class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                                            <!--end::Icon-->

                                                            <!--begin::Info-->
                                                            <div class="ms-4">
                                                                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Загрузить
                                                                    документ</h3>
                                                                <span class="fs-7 fw-bold text-gray-400">Перетащите документ сюда.<br> Максимальный размер 5Мб. Формат файла: jpeg, jpg, png, pdf</span>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                    </div>
                                                    <!--end::Dropzone-->
                                                </div> 
                                            </div>
                                            <div class="col-6">
                                                <div class="fv-row">
                                                    <!--begin::Dropzone-->
                                                    <div class="dropzone dz-clickable cars-gallery" id="gallery">
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

    @include('includes.global_scripts')

    <!--begin::Page Vendors Javascript(used by this page)-->
    <script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Page Vendors Javascript-->

    <script src="{{ mix('/js/datatables.js') }}"></script>

    <script>
        var existingGalleryFiles = @json($item ? $item['gallery_files'] : []);
        var existingDocumentFiles = @json($item ? $item['doc_files'] : []);
        var is_rent = "{{$item ? $item['is_rent'] : null}}";
    </script>

    <script src="{{ mix('/js/add-cars.js') }}"></script>
</body>
<!--end::Body-->

</html>
