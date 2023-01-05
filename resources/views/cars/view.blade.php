<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <title>Машины</title>

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

                                    <li class="breadcrumb-item text-dark">Машины</li>
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

                                <div class="card-body">
                                    <div class="card-header mb-15" style="padding: 0">
                                        <div class="card-title m-0">
                                            <h3 class="fw-bolder m-0">Детали</h3>
                                        </div>
                                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                            @if (Auth::user()->hasPermission('cars.edit'))
                                            <a href="/cars/add?id={{$item->id}}" class="btn btn-primary btn-sm">Редактировать</a>    
                                            @endif
                                        </div>
                                    </div>

                                        @csrf
                                        
                                        <div class="row mb-5">
                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Марка</label>
                                                    {{$item->brand}}
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Модель</label>
                                                    {{$item->model}}
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Номер</label>
                                                    {{$item->number}}
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div class="d-flex flex-column mb-5 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Год</label>
                                                    {{$item->year}}
                                                </div>
                                            </div>
                                        </div>

                                        @if ($item->is_rent)
                                        <div class="row mt-10">
                                            <div class="col-3">
                                                <div>
                                                    <div class="d-flex flex-column mb-5 fv-row" >
                                                        <label class=" fs-5 fw-bold mb-2">Стоимость аренды</label>
                                                        {{$item->rent_cost}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-3">
                                                <div>
                                                    <div class="d-flex flex-column mb-5 fv-row">
                                                        <label class=" fs-5 fw-bold mb-2">Арендодатель</label>
                                                        {{$item->landlord}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="mt-15 pb-2">
                                            <h4 class="fw-bolder m-0">Документ</h4>
                                        </div>

                                        <div class="row mt-10 housing-gallery">
                                            @foreach ($item->doc_files as $fItem)
                                            <div class="col-1">
                                                <div class="housing-gallery__img">
                                                    <a href="{{$fItem['path']}}" class="js-fancybox">
                                                        @if ($fItem['ext'] == 'pdf')
                                                        <img src="/assets/media/svg/files/pdf.svg" alt="img">
                                                        @else
                                                        <img src="{{$fItem['path']}}" alt="img">
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>    
                                            @endforeach
                                        </div>

                                        <div class="mt-15 pb-2">
                                            <h4 class="fw-bolder m-0">Галерея</h4>
                                        </div>

                                        <div class="row mt-10 housing-gallery">
                                            @foreach ($item->gallery_files as $fItem)
                                            <div class="col-1">
                                                <div class="housing-gallery__img">
                                                    <a href="{{$fItem['path']}}" class="js-fancybox">
                                                        <img src="{{$fItem['path']}}" alt="img">
                                                    </a>
                                                </div>
                                            </div>    
                                            @endforeach
                                        </div>


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
    var existingGalleryFiles = @json($item['gallery_files']);
    var is_rent = '';
</script>

<script src="{{ mix('/js/add-cars.js') }}"></script>

</body>
<!--end::Body-->

</html>
