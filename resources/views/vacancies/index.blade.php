<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <title>Вакансии</title>

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

                                <li class="breadcrumb-item text-dark">Вакансии</li>
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
                                    <div class="w-100 mw-150px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" name="industry" data-placeholder="Отрасль" multiple>
                                            @foreach($industries as $industry)
                                                <option value="{{$industry->id}}">{{$industry->name}}</option>
                                            @endforeach
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                    <div
                                        @if(Auth::user()->isFreelancer() || Auth::user()->isRecruiter() || Auth::user()->isRecruitmentDirector()) style="display:none;"
                                        @endif class="w-100 mw-150px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" name="status" data-placeholder="Статус" multiple>
                                            <option value="1">Активный</option>
                                            <option value="2">Пауза</option>
                                            <option value="3">Завершена</option>
                                            <option value="4">Удалена</option>
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                    <div class="w-100 mw-150px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" name="city" data-placeholder="Город" multiple>
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                    <div class="w-100 mw-150px">
                                        <!--begin::Select2-->
                                        <select class="js-filter form-select form-select-solid" name="genre" data-placeholder="Пол" multiple>
                                            <option value="1">Мужчины</option>
                                            <option value="2">Женщины</option>
                                            <option value="3">Любой</option>
                                        </select>
                                        <!--end::Select2-->
                                    </div>

                                    @if(
                                        Auth::user()->group_id == 1 
                                        || Auth::user()->hasPermission('vacancy.create')
                                    )
                                        <a href="{{url('/')}}/vacancy/add" class="btn btn-primary">Добавить</a>
                                    @endif
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Table-->
                                <div class="table-responsive">
                                    <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3" data-route="{{route('vacancy.json')}}">
                                        <!--begin::Table head-->
                                        <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                                            @if(
                                                Auth::user()->group_id == 3 
                                                || Auth::user()->group_id == 2 
                                                || Auth::user()->isRecruitmentDirector()
                                                || Auth::user()->isHeadOfEmploymentDepartment()
                                            )
                                                <th class="max-w-55px sorting_disabled">Id</th>
                                                <th class="max-w-55px" data-name="title">Название</th>
                                                <th class="max-w-85px">Отрасль</th>
                                                <th data-name="deadline_to">Дедлайн</th>
                                                <th class="max-w-25px" data-name="count_men">М</th>
                                                <th class="max-w-25px" data-name="count_women">Ж</th>
                                                <th class="max-w-25px" data-name="count_people">Н</th>
                                                <th data-name="salary">Ставка</th>
                                                <th class="sorting_disabled">Описание</th>
                                                <th data-name="housing_cost">Жилье</th>
                                                @if (!Auth::user()->isRecruitmentDirector() && !Auth::user()->isHeadOfEmploymentDepartment())
                                                <th class="sorting_disabled">Добавить</th>
                                                @endif
                                            @else
                                                <th class="max-w-55px sorting_disabled">Id</th>
                                                <th class="max-w-55px" data-name="title">Название</th>
                                                <th class="max-w-85px">Отрасль</th>
                                                <th data-name="deadline_to">Дедлайн</th>
                                                <th class="max-w-25px" data-name="count_men">М</th>
                                                <th class="max-w-25px" data-name="count_women">Ж</th>
                                                <th class="max-w-25px" data-name="count_people">Н</th>
                                                <th data-name="salary">Ставка</th>
                                                <th class="sorting_disabled">Описание</th>
                                                <th data-name="housing_cost">Жилье</th>
                                                <th class="sorting_disabled">Стоимость Лид</th>
                                                <th class="sorting_disabled">Стоимость кандидат</th>
                                                <th class="w-125px sorting_disabled">Статус</th>
                                            @endif
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
<!--begin::Modal-->

@include('includes.global_scripts')
<script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ mix('/js/datatables.js') }}"></script>

<script>
    function changeActivation(id) {
        var changeActivation = $('.changeActivation' + id).val();
        $.get('{{url('/')}}/vacancy/activation?s=' + changeActivation + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    function changeCost(id) {
        var changeCost = $('.changeCost' + id).val();
        $.get('{{url('/')}}/vacancy/changecost?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }
    function changeCost_pay_lead(id) {
        var changeCost = $('.changeCost_pay_lead' + id).val();
        $.get('{{url('/')}}/vacancy/change_сost_pay_lead?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    function change_housing_cost(id) {
        var changeCost = $('.change_housing_cost' + id).val();
        $.get('{{url('/')}}/vacancy/change_housing_cost?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    function change_salary(id) {
        var changeCost = $('.change_salary' + id).val();
        $.get('{{url('/')}}/vacancy/change_salary?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    function change_count_people(id) {
        var changeCost = $('.change_count_people' + id).val();
        $.get('{{url('/')}}/vacancy/count_people?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    function change_count_women(id) {
        var changeCost = $('.change_count_women' + id).val();
        $.get('{{url('/')}}/vacancy/count_women?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    function change_count_men(id) {
        var changeCost = $('.change_count_men' + id).val();
        $.get('{{url('/')}}/vacancy/count_men?s=' + changeCost + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }
</script>
</body>
<!--end::Body-->
</html>


