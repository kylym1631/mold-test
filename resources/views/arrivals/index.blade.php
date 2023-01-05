<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <title>Приезды</title>
    
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

                                <li class="breadcrumb-item text-dark">Приезды</li>
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
                                        <input type="text" class="js-search-input form-control form-control-solid w-100 mw-200px ps-14" placeholder="Поиск">
                                    </div>
                                    <!--end::Search-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">

                                    <div>
                                        <select class="js-filter form-select form-select form-select-solid" name="recruiter" data-placeholder="Рекрутер" multiple>
                                            @foreach ($filter_vars['recruiters'] as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <select class="js-filter form-select form-select form-select-solid" name="country" data-placeholder="Гражданство" multiple>
                                            @foreach ($filter_vars['countries'] as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <select class="js-filter form-select form-select form-select-solid" name="vacancy" data-placeholder="Вакансия" multiple>
                                            @foreach ($filter_vars['vacancies'] as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <select class="js-filter form-select form-select form-select-solid" name="place" data-placeholder="Место прибытия" multiple>
                                            @foreach ($filter_vars['places'] as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-250px">
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor"/>
                                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor"/>
                                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor"/>
                                                    </svg>
                                                    </span>
                                            <!--end::Svg Icon-->
                                            <input class="js-filter form-control form-control-solid ps-14" placeholder="Период" name="period">
                                        </div>
                                    </div>

                                    <div class="w-150px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" data-placeholder="Статус" name="status" multiple>
                                        @if(Auth::user()->isFreelancer())
                                            <option value="1">Новый кандидат</option>
                                            <option value="2">Лид</option>
                                            <option value="3">Отказ</option>
                                            <option value="4">Оформлен</option>
                                            <option value="5">Архив</option>
                                            <option value="10">Отработал 7 дней</option>
                                        @elseif(Auth::user()->isRecruiter())
                                            <option value="1">Новый кандидат</option>
                                            <option value="2">Лид</option>
                                            <option value="3">Отказ</option>
                                            <option value="5">Архив</option>
                                            <option value="14">Перезвонить</option>
                                        @elseif(Auth::user()->isLogist())
                                            <option value="4">Оформлен</option>
                                            <option value="6">Подтвердил Выезд</option>
                                            <option value="19">В пути</option>
                                            <option value="20">Не доехал</option>
                                            <option value="21">Перезвонить</option>
                                        @elseif(Auth::user()->isKoordinator())
                                            <option value="3">Оформлен</option>
                                            <option value="7">Готов к Работе</option>
                                            <option value="8">Трудоустроен</option>
                                            <option value="9">Приступил к Работе</option>
                                            <option value="10">Отработал 7 дней</option>
                                            <option value="11">Уволен</option>
                                        @elseif(Auth::user()->isTrud())
                                            <option value="3">Оформлен</option>
                                            <option value="6">Подтвердил Выезд</option>
                                            <option value="8">Трудоустроен</option>
                                        @else
                                            <option value="1">Новый кандидат</option>
                                            <option value="2">Лид</option>
                                            <option value="3">Отказ</option>
                                            <option value="4">Оформлен</option>
                                            <option value="5">Архив</option>
                                            <option value="6">Подтвердил Выезд</option>
                                            <option value="7">Готов к Работе</option>
                                            <option value="8">Трудоустроен</option>
                                            <option value="9">Приступил к Работе</option>
                                            <option value="10">Отработал 7 дней</option>
                                            <option value="11">Уволен</option>
                                            <option value="12">Приехал</option>
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
                                    <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('candidates.arrivals.all.json')}}">
                                        <!--begin::Table head-->
                                        <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                            <th class="max-w-55px sorting_disabled">Id приезда</th>
                                            <th class="sorting_disabled">Имя</th>
                                            <th class="sorting_disabled">Фамилия</th>
                                            <th class="sorting_disabled">Рекрутер</th>
                                            <th class="sorting_disabled">Телефон</th>
                                            <th class="sorting_disabled">Viber</th>
                                            <th class="max-w-85px sorting_disabled">Гражданство</th>
                                            <th class="max-w-85px sorting_disabled">Вакансия</th>
                                            <th class="max-w-85px sorting_disabled">Место приезда</th>
                                            <th class="max-w-85px sorting_disabled">Вид транспорта</th>
                                            <th class="max-w-85px sorting_disabled">Планируемая дата приезда</th>
                                            <th class="max-w-85px sorting_disabled">Время приезда</th>
                                            <th class="max-w-85px sorting_disabled">Билет</th>
                                            <th class="max-w-85px sorting_disabled">Комментарий</th>
                                            <th class="min-w-100px sorting_disabled">Статус</th>
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
@include('includes.status-manage')
@include('includes.docs-modal')

@if(Auth::user()->isRecruiter() || Auth::user()->isLogist() || Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector())
@include('includes.arrivals-manage')
@endif

<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Page Vendors Javascript-->

<script src="{{ mix('/js/datatables.js') }}"></script>


</body>
<!--end::Body-->
</html>
