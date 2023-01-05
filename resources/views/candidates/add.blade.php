<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <title>Добавить кандидата</title>

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
                    <!--begin::Container-->
                    <div id="kt_content_container" class="container-fluid">
                        <!--begin::Card-->
                        <div class="card">

                            <div class="card-body pt-15">
                                <form id="candidate-form" action="{{route('candidate.add')}}" method="POST" enctype="multipart/form-data">

                                @csrf
                                <input type="hidden" id="id" name="id" value="{{ request()->get('id') }}">

                                <div class="row">
                                    <div class="col">
                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Фамилия</label>
                                                    <input
                                                        @if($candidate != null) value="{{$candidate->lastName}}" @endif
                                                    id="lastName" style="text-transform: uppercase"
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="lastName">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Имя</label>
                                                    <input style="text-transform: uppercase"
                                                           @if($candidate != null) value="{{$candidate->firstName}}"
                                                           @endif
                                                           id="firstName"
                                                           class="form-control form-control-sm form-control-solid"
                                                           type="text" name="firstName">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Дата рождения</label>
                                                    <input
                                                        @if($candidate != null) value="{{\Carbon\Carbon::parse($candidate->dateOfBirth)->format('d.m.Y')}}"
                                                        @endif
                                                        id="dateOfBirth"
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="dateOfBirth">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Телефон</label>
                                                    <input
                                                        @if($candidate != null) value="{{$candidate->phone}}" @endif
                                                    id="phone"
                                                        placeholder="Писать в междунаробном формате, например Украина +380664252585"
                                                        class="form-control form-control-sm form-control-solid"
                                                        title="Писать в междунаробном формате, например Украина +380664252585"
                                                        type="text" name="phone">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Номер Viber</label>
                                                    <input id="viber"
                                                           @if($candidate != null) value="{{$candidate->viber}}" @endif
                                                           class="form-control form-control-sm form-control-solid"
                                                           type="text" name="viber">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Дополнительный
                                                        контакт</label>
                                                    <input
                                                        @if($candidate != null) value="{{$candidate->phone_parent}}"
                                                        @endif
                                                        id="phone_parent"
                                                        class="form-control form-control-sm form-control-solid"
                                                        type="text" name="phone_parent">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-6 mb-5">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Гражданство</label>
                                                    <select
                                                        name="citizenship_id"
                                                        id="citizenship_id"
                                                        class="form-select  form-select-sm form-select-solid">
                                                        @if ($candidate && $candidate->Citizenship)
                                                        <option value="{{ $candidate->Citizenship->id }}" selected>{{ $candidate->Citizenship->name }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6 mb-5">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Специальность</label>
                                                    <select
                                                        name="speciality_id"
                                                        id="speciality_id"
                                                        class="form-select  form-select-sm form-select-solid">
                                                        @if ($candidate && $candidate->Speciality)
                                                        <option value="{{ $candidate->Speciality->id }}" selected>{{ $candidate->Speciality->name }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- @if (Auth::user()->isAdmin() || Auth::user()->isTrud())
                                            <div class="col-6 mb-5">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Национальность</label>
                                                    <select
                                                        name="nacionality_id"
                                                        id="nacionality_id"
                                                        class="form-select  form-select-sm form-select-solid">
                                                        @if ($candidate && $candidate->Nacionality)
                                                        <option value="{{ $candidate->Nacionality->id }}" selected>{{ $candidate->Nacionality->name }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>    
                                            @endif --}}
                                            
                                            <div class="col-6 mb-5">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Страна прибывания</label>
                                                    <select id="country_id" name="country_id"
                                                            class="form-select  form-select-sm form-select-solid">
                                                            @if ($candidate && $candidate->Country)
                                                                <option value="{{ $candidate->Country->id }}" selected>{{ $candidate->Country->name }}</option>
                                                            @endif
                                                        </select>
                                                </div>
                                            </div>

                                            <div class="col-6 mb-5">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Документ</label>
                                                    <select id="type_doc_id"
                                                        name="type_doc_id"
                                                            class="form-select  form-select-sm form-select-solid">
                                                            @if ($candidate && $candidate->Type_doc)
                                                                <option value="{{ $candidate->Type_doc->id }}" selected>{{ $candidate->Type_doc->name }}</option>
                                                            @endif
                                                        </select>
                                                </div>
                                            </div>
                                        
                                            <div class="col-6 mb-5">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="required fs-5 fw-bold mb-2">Пол</label>
                                                    <select name="gender" class="form-select  form-select-sm form-select-solid">
                                                        <option></option>
                                                        <option value="m" 
                                                        @selected($candidate && $candidate->gender == 'm')>
                                                            Мужской
                                                        </option>
                                                        <option value="f" 
                                                        @selected($candidate && $candidate->gender == 'f')>
                                                            Женский
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>

                                    <div class="col">
                                        @if(
                                            Auth::user()->isAdmin() 
                                            || Auth::user()->isSupportManager() 
                                            || Auth::user()->isTrud()
                                        )
                                        <div class="row mb-5">
                                            
                                            @if(Auth::user()->isAdmin() || Auth::user()->isSupportManager())
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Рекрутер</label>

                                                    <select id="recruiter_id"
                                                            name="recruiter_id"
                                                            class="form-select  form-select-sm form-select-solid">
                                                            @if ($candidate && $recruter)
                                                            <option value="{{ $recruter->id }}" selected>{{ $recruter->firstName .' '. $recruter->lastName}}</option>
                                                            @endif
                                                        </select>
                                                </div>
                                            </div>
                                            @endif

                                            @if (Auth::user()->isAdmin() || Auth::user()->isTrud())
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">ИНН</label>
                                                    <input id="inn"
                                                            name="inn"
                                                           @if($candidate != null) value="{{$candidate->inn}}" @endif
                                                           class="form-control form-control-sm form-control-solid"
                                                           type="text"/>
                                                </div>
                                            </div>
                                            @endif
                                            
                                        </div>
                                        @endif

                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class="fs-5 fw-bold mb-2">Вакансия</label>
                                                    <select id="real_vacancy_id"
                                                            name="real_vacancy_id"
                                                            class="form-select  form-select-sm form-select-solid">
                                                            @if ($candidate && $candidate->Vacancy)
                                                                <option value="{{ $candidate->Vacancy->id }}" selected>{{ $candidate->Vacancy->title }}</option>
                                                            @elseif($vacancy)
                                                                <option value="{{ $vacancy->id }}" selected>{{ $vacancy->title }}</option>
                                                            @endif
                                                        </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-6 add-dropzone">
                                                <div class="d-flex flex-column mb-0 fv-row dropzone">
                                                    <a id="kt_file_doc" href="#">Загрузить паспорт</a>
                                                    <a id="kt_file_doc1" href="#">Загрузить карту побыту (вместе с децизией)</a>
                                                    <a id="kt_file_doc2" href="#">Загрузить водительское
                                                        удостоверение</a>
                                                    <a id="kt_file_doc3" href="#">Загрузить диплом(сертификаты)</a>
                                                    <a id="kt_file_doc4" href="#">Загрузить легитимацию из универа</a>
                                                    <a id="kt_file_doc5" href="#">Загрузить прочий документ</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    <label class=" fs-5 fw-bold mb-2">Комментарий</label>
                                                    <textarea id="comment"
                                                            name="comment"
                                                              class="form-control form-control-sm form-control-solid"
                                                              cols="20"
                                                              rows="6"> @if($candidate != null){{$candidate->comment}}@endif</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        @if($candidate != null)
                                        <div class="row mb-5">
                                            <div class="col">
                                                <div class="d-flex flex-column mb-0 fv-row">
                                                    {!! $select_active !!}
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="mt-10 pb-10" style="text-align: right">
                                            <button type="submit" class="btn btn-primary btn-sm"> 
                                                <span class="indicator-label">Сохранить</span> 
                                                <span class="indicator-progress">
                                                    Сохранение...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span> 
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                @if(Auth::user()->isRecruiter() || Auth::user()->isLogist() || Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector())
                                @include('candidates.include.add-arrivals')
                                @endif

                                @if(Auth::user()->group_id == 5 || Auth::user()->group_id == 1)
                                @include('candidates.include.add-employment')
                                @endif

                                @if((Auth::user()->isCoordinator() || Auth::user()->group_id == 9 || Auth::user()->group_id == 1))
                                @include('candidates.include.add-housing', ['view' => 'add'])
                                @endif
                                
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
@include('includes.status-manage')

@if(Auth::user()->isRecruiter() || Auth::user()->isLogist() || Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector())
@include('includes.arrivals-manage')
@endif

<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{url('/')}}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Page Vendors Javascript-->

<script src="{{ mix('/js/add-candidate.js') }}"></script>

@if(Auth::user()->isAdmin() || Auth::user()->isLogist() || Auth::user()->isRecruiter())
<script>
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


    function changeArrivalActivation(id) {
        var changeArrivalActivation = $('.changeArrivalActivation' + id).val();
        if (changeArrivalActivation == '') {
            return;
        }

        $.get('{{url('/')}}/candidates/arrivals/activation?s=' + changeArrivalActivation + '&id=' + id, function (res) {
            if (res.error) {
                toastr.error(res.error);
            } else {
                toastr.success('Успешно');
            }
        });
    }

    $(document).on('click', '.edit_arrival', function () {
        $('#modal_add_arrivals_id').val($(this).data('id'))

        $('#modal_place_arrive_id').append(new Option($(this).data('place_arrive_name'), $(this).data('place_arrive_id'), true, true)).trigger('change');
        $('#modal_transport_id').append(new Option($(this).data('transport_name'), $(this).data('transport_id'), true, true)).trigger('change');


        $('#modal__comment').val($(this).data('comment'))
        $('#modal__date_arrive').val($(this).data('date_arrive'))
        $('#modal_add_arrivals').modal('show');
    });

    oTable = $('#users').DataTable({
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
        fnDrawCallback: function(oSettings) {
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
                url: '{{ route('candidates.arrivals.json') }}',
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

    $('#modal_add_arrivals__save').click(function (e) {
        e.preventDefault();
        let self = $(this);

        self.prop('disabled', true);

        var data = {
            candidate_id: $('#id').val(),
            place_arrive_id: $('#modal_place_arrive_id').val(),
            transport_id: $('#modal_transport_id').val(),
            date_arrive: $('#modal__date_arrive').val(),
            comment: $('#modal__comment').val(),
            _token: $('input[name=_token]').val(),
        };

        if (data.candidate_id === '') {
            toastr.error('Создайте кандидата');
            return '';
        }


        let id = $('#modal_add_arrivals_id').val();
        if (id !== '') {
            data.id = id;
        }

        $.ajax({
            url: "{{url('/')}}/candidates/arrivals/add",
            method: 'post',
            data: data,
            success: function (response, status, xhr, $form) {
                if (response.error) {
                    toastr.error(response.error);
                } else {
                    $('#modal_add_arrivals').modal('hide');
                    oTable.draw();
                }
                self.prop('disabled', false);
            }
        });
    })
</script>
@endif

</body>
<!--end::Body-->
</html>
