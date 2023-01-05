<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <title>Просмотр кандидата {{$candidate->lastName}} {{$candidate->firstName}}</title>

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

                                    <li class="breadcrumb-item text-dark">Добавить кандидата</li>
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
                        <div id="kt_content_container" class="container-xxl">
                            <!--begin::Navbar-->
                            <div class="card mb-5 mb-xxl-8">
                                <div class="card-body pt-9 pb-0">
                                    <!--begin::Details-->
                                    <div class="d-flex flex-wrap flex-sm-nowrap">

                                        <!--begin::Info-->
                                        <div class="flex-grow-1">
                                            <!--begin::Title-->
                                            <div
                                                class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                                <!--begin::User-->
                                                <div class="d-flex flex-column">
                                                    <!--begin::Name-->
                                                    <div class="d-flex align-items-center ">
                                                        <span href="#"
                                                            class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1 text-uppercase">
                                                            {{$candidate->lastName}}
                                                            {{$candidate->firstName}}</span>
                                                    </div>
                                                    <div class=" mb-2">
                                                        {{$candidate->getCurrentStatus()}}
                                                    </div>
                                                    <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                                        <a href="#"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-phone-alt"></i></span>телефон:
                                                            {{$candidate->phone}}
                                                        </a>
                                                        @if($candidate->viber != '')
                                                        <a href="#"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-phone-alt"></i></span> viber:
                                                            {{$candidate->viber}}
                                                        </a>
                                                        @endif
                                                        @if($candidate->phone_parent != '')
                                                        <a href="#"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fas fa-phone-alt"></i></span>доп телефон
                                                            {{$candidate->phone_parent}}
                                                        </a>
                                                        @endif

                                                    </div>
                                                    <!--end::Info-->

                                                    <!--begin::Info-->
                                                    <div
                                                        class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2 js-view-doc-wrap">
                                                        @if($candidate->getPasportLink() != '')
                                                        <a href="{{$candidate->getPasportLink()}}"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-passport"></i></span> Паспорт(ID
                                                            card)
                                                        </a>
                                                        @endif

                                                        @if($candidate->getKartapobytu() != '')
                                                        <a href="{{$candidate->getKartapobytu()}}"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-file-contract"></i></span> Карта по
                                                            быту(вместе с
                                                            децизией)
                                                        </a>
                                                        @endif
                                                        @if($candidate->getDriverLicense() != '')
                                                        <a href="{{$candidate->getDriverLicense()}}"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-id-card"></i></span> Водительское
                                                            удостоверение
                                                        </a>
                                                        @endif

                                                        @if($candidate->getDiplom() != '')
                                                        <a href="{{$candidate->getDiplom()}}"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-certificate"></i></span>
                                                            Диплом(сертификаты)
                                                        </a>
                                                        @endif

                                                        @if($candidate->getLegitim() != '')
                                                        <a href="{{$candidate->getLegitim()}}"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-scroll"></i></span> Легитимация из
                                                            Универа
                                                        </a>
                                                        @endif

                                                        @if($candidate->getElsefile() != '')
                                                        <a href="{{$candidate->getElsefile()}}"
                                                            class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                                            <span class="svg-icon svg-icon-4 me-1">
                                                                <i class="fa fa-file"></i></span> Прочий документ
                                                        </a>
                                                        @endif


                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::User-->
                                            </div>
                                            <!--end::Title-->

                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Details-->

                                    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab"
                                                href="#kt_tab_pane_1">Общее</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">История</a>
                                        </li>

                                        @if (Auth::user()->isKoordinator() || Auth::user()->isAdmin())
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3">Часы
                                                работы</a>
                                        </li>
                                        @endif

                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab"
                                                href="#kt_tab_pane_4">Трудоустройство</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Жилье</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Документы</a>
                                        </li>
                                        
                                        @if (Auth::user()->isAdmin()
                                        || Auth::user()->isLegalizationManager())
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_7">Легализация</a>
                                        </li>
                                        @endif
                                        
                                    </ul>

                                    <div class="tab-content" id="myTabContent">

                                        <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                            @include('candidates.include.view-details')
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                            @include('candidates.include.history')
                                        </div>

                                        @if (Auth::user()->isKoordinator() || Auth::user()->isAdmin())
                                        <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
                                            <div class="card mb-5 mb-xl-10 js-data-table-wrap"
                                                id="kt_profile_details_view">

                                                <div class="card-header cursor-pointer">
                                                    <div class="card-title m-0">
                                                        <h3 class="fw-bolder m-0">Часы работы</h3>
                                                    </div>

                                                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                                        <input type="hidden" name="candidate_id"
                                                            value="{{ request()->get('id') }}" class="js-filter">

                                                        <div class="w-250px">
                                                            <div
                                                                class="d-flex align-items-center position-relative my-1">
                                                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                                                <span
                                                                    class="svg-icon svg-icon-1 position-absolute ms-4">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3"
                                                                            d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                                            fill="currentColor" />
                                                                        <path
                                                                            d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                                            fill="currentColor" />
                                                                        <path
                                                                            d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                                <input type="text"
                                                                    class="js-filter js-filter_joint form-control form-control-solid ps-14 flatpickr-input"
                                                                    name="period" data-format="month"
                                                                    data-js-filter="work_log_history">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="vue-app" class="card-body p-9 pb-0">
                                                    @php
                                                    $days = json_encode(['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                                                    JSON_UNESCAPED_UNICODE);
                                                    @endphp

                                                    <worklog-history candidate-id="{{ $candidate->id }}"
                                                        days-of-week="{{$days}}">
                                                    </worklog-history>

                                                    @include('candidates.include.fines')
                                                    @include('candidates.include.bhp-forms')
                                                    @include('candidates.include.stay-cards')
                                                    @include('candidates.include.premiums')
                                                    @include('candidates.include.recommendations')
                                                    @include('candidates.include.transport')
                                                    @include('candidates.include.work-permits')
                                                    @include('candidates.include.prepayments')

                                                    <addition-worklog
                                                        btn-selector=".js-add-addition-worklog"
                                                        edit-btn-selector=".js-edit-addition-worklog"
                                                    ></addition-worklog>
                                                </div>

                                            </div>
                                        </div>
                                        @endif

                                        <div class="tab-pane fade" id="kt_tab_pane_4" role="tabpanel">
                                            <div class="card mb-5 mb-xl-10 js-data-table-wrap"
                                                id="kt_profile_details_view">

                                                <div class="card-header cursor-pointer">
                                                    <div class="card-title m-0">
                                                        <h3 class="fw-bolder m-0">Трудоустройство</h3>
                                                    </div>

                                                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                                        <input type="hidden" name="candidate_id"
                                                            value="{{ request()->get('id') }}" class="js-filter">
                                                    </div>
                                                </div>

                                                <div class="card-body p-9 pb-0">
                                                    @include('candidates.include.positions')
                                                </div>

                                                @if(Auth::user()->group_id == 5
                                                || Auth::user()->group_id == 1
                                                ||Auth::user()->isRecruitmentDirector())
                                                <div class="card-header cursor-pointer mt-10">
                                                    <div class="card-title m-0">
                                                        <h4 class="fw-bolder m-0">
                                                            Настройки трудоустройство
                                                        </h4>
                                                    </div>
                                                </div>

                                                <div class="card-body p-9 pb-0">

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Статус
                                                            трудоустройства</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate->Real_status_work != null)
                                                                {{ $candidate->Real_status_work->name }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Клиент</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate->Client != null)
                                                                {{ $candidate->Client->name }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Должность</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate->Client_position != null)
                                                                {{ $candidate->Client_position->title }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">PESEL</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->pesel }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Номер банковского
                                                            счета</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->account_number }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Имя матери</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->mothers_name }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Имя отца</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->fathers_name }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Адрес</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->address }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Индекс</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->zip }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label class="col-lg-4 fw-bold text-muted">Город</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">
                                                                @if($candidate != null)
                                                                {{ $candidate->city }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>
                                                @endif

                                                @if(Auth::user()->isKoordinator())
                                                <div class="p-9">
                                                    <form id="candidate-form-simple"
                                                        action="{{route('candidate.update.employment')}}" method="POST">
                                                        @csrf

                                                        <input type="hidden" id="id" name="id"
                                                            value="{{$candidate->id}}">

                                                        <input type="hidden" id="client_id" name="client_id"
                                                            value="{{$candidate->client_id}}">

                                                        @include('candidates.include.add-employment')

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
                                                @endif

                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel">
                                            <div class="card mb-5 mb-xl-10 js-data-table-wrap"
                                                id="kt_profile_details_view">

                                                <div class="card-header cursor-pointer">
                                                    <div class="card-title m-0">
                                                        <h3 class="fw-bolder m-0">Жилье</h3>
                                                    </div>

                                                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                                        <input type="hidden" name="candidate_id"
                                                            value="{{ request()->get('id') }}" class="js-filter">
                                                    </div>
                                                </div>

                                                <div class="card-body p-9 pb-0">
                                                    @include('candidates.include.housing')
                                                </div>


                                                @if(Auth::user()->isCoordinator()
                                                || Auth::user()->group_id == 9 
                                                || Auth::user()->group_id == 1)
                                                <div class="p-9">
                                                    <form id="candidate-form"
                                                        action="{{route('candidate.update.housing')}}" method="POST">
                                                        @csrf

                                                        <input type="hidden" id="id" name="id"
                                                            value="{{$candidate->id}}">

                                                        <input type="hidden" name="edit_housing" value="1">

                                                        @include('candidates.include.add-housing', ['view' => 'tabs'])

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
                                                @endif

                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_6" role="tabpanel">
                                            @include('candidates.include.documents')
                                        </div>

                                        @if (Auth::user()->isAdmin()
                                        || Auth::user()->isLegalizationManager())
                                        <div class="tab-pane fade" id="kt_tab_pane_7" role="tabpanel">
                                            @include('candidates.include.legalisation')
                                        </div>
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                @include('includes.Footer')
            </div>
        </div>
    </div>

    <div id="foo-vue-app">
        <create-edit 
            create-btn-selector=".js-creator-create-btn" 
            edit-btn-selector=".js-editor-edit-btn"
        ></create-edit>
    </div>

    @include('includes.global_scripts')
    @include('includes.status-manage')
    @include('includes.docs-modal')

    @if(Auth::user()->isRecruiter() || Auth::user()->isLogist() || Auth::user()->isAdmin() ||
    Auth::user()->isRecruitmentDirector())
    @include('includes.arrivals-manage')
    @endif

    @include('candidates.include.positions-manage')
    @include('candidates.include.housing-manage')

    @if(Auth::user()->isCoordinator() || Auth::user()->group_id == 9 || Auth::user()->group_id == 1)
    <script src="{{ mix('/js/add-candidate.js') }}"></script>
    @endif

    <script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>

    @if(Auth::user()->isAdmin() || Auth::user()->isRecruiter() || Auth::user()->isLogist() ||
    Auth::user()->isRecruitmentDirector())
    <script>
        var oTable = $('#users').DataTable({
            dom: 'rt<"dataTable_bottom"lip>',
            paging: true,
            searching: false,
            pagingType: "numbers",
            processing: true,
            serverSide: true,
            pageLength: 100,
            lengthMenu: [25, 50, 100, 250],
            ordering: false,
            infoCallback: function (settings, start, end, max, total, pre) {
                return 'Показано ' + (end - start + 1) + ' из ' + total + ' записей';
            },
            fnDrawCallback: function (oSettings) {
                reInitDropzone();

                if (oSettings._iDisplayLength >= oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                } else {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').show();
                }
            },
            language: {
                emptyTable: "нет данных",
                zeroRecords: "нет данных",
                sSearch: "Поиск",
                processing: 'Загрузка...'
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': ['sorting_disabled']
            }],
            ajax: function (data, callback, settings) {
                data._token = $('input[name=_token]').val();

                @if($candidate != null)
                data.canddaite_id = '{{$candidate->id}}';
                @endif

                $.ajax({
                    url: '{{ route("candidates.arrivals.json") }}',
                    type: 'POST',
                    data: data,
                    success: function (data) {
                        if (data.error) {
                            toastr.error(data.error);
                        } else {
                            callback(data);
                        }
                    }
                });
            }
        });

        window.arrivalsTable = oTable;

        function reInitDropzone() {
            $('.add_file').each(function () {

                let id = $(this).data('id');
                new Dropzone('#' + $(this).attr('id'), {
                    url: "{{url('/')}}/candidates/arrivals/add_ticket", // Set the url for your upload script location
                    paramName: "file",
                    maxFiles: 1,
                    maxFilesize: 5,
                    acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
                    addRemoveLinks: true,
                    sending: function (file, xhr, formData) {
                        formData.append('_token', $('input[name=_token]').val());
                        formData.append('id', id);
                    },
                    success: function (file, done) {
                        oTable.draw();
                    },
                    accept: function (file, done) {
                        done();
                    }
                });
            });
        }

    </script>
    @endif

    <script src="{{ mix('/js/datatables.js') }}"></script>
    <script src="{{ mix('/js/vue.js') }}"></script>

</body>

</html>
