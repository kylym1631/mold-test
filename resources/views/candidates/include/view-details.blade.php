<div class="card mb-5 mb-xl-10" id="kt_profile_details_view">

    <div class="card-header cursor-pointer">

        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Детали</h3>
        </div>
        @if (Auth::user()->isAdmin() 
            || (Auth::user()->hasPermission(['candidate.edit', 'employee.edit']) 
                && Auth::user()->hasPermission(['candidate.edit.status.'. $candidate->active, 'employee.edit.status.'. $candidate->active])))
            <a href="/candidate/add?id={{$candidate->id}}" class="btn btn-primary btn-sm align-self-center">Edit
                Profile</a>
            @endif
    </div>

    <div class="card-body p-9 pb-0">

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Дата рождения</label>
            <div class="col-lg-8">
                <span
                    class="fw-bolder fs-6 text-gray-800">{{\Carbon\Carbon::parse($candidate->dateOfBirth)->format('d.m.Y')}}</span>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Пол</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if ($candidate->gender == 'm')
                    Мужской
                    @endif

                    @if ($candidate->gender == 'f')
                    Женский
                    @endif
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Гражданство</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if($candidate->Citizenship != null)
                    {{ $candidate->Citizenship->name }}
                    @endif
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Специальность</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if($candidate->Speciality != null)
                    {{ $candidate->Speciality->name }}
                    @endif
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Страна
                прибывания</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if($candidate->Country != null)
                    {{ $candidate->Country->name }}
                    @endif
                </span>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Документ</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if($candidate->Type_doc != null)
                    {{ $candidate->Type_doc->name }}
                    @endif
                </span>
            </div>
        </div>

        @if(Auth::user()->isAdmin() ||
        Auth::user()->isRecruitmentDirector())

        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Рекрутер</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if($recruter != null)
                    {{ $recruter->firstName }} {{ $recruter->lastName }}
                    @endif
                </span>
            </div>
        </div>
        @endif
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">ИНН</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800 text-uppercase">
                    {{$candidate->inn}}
                </span>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Вакансия</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    @if($candidate->Vacancy != null)
                    {{ $candidate->Vacancy->title }}
                    @endif
                </span>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Комментарий</label>
            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    {{$candidate->comment}}
                </span>
            </div>
        </div>

        @if(Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector() ||
        Auth::user()->isKoordinator())
        @if ($candidate->Housing)
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Жилье</label>
            <div class="col-lg-4">
                <span class="fw-bolder fs-6 text-gray-800">
                    {{$candidate->Housing->title}} {{$candidate->Housing->address}}
                </span>
            </div>
        </div>
        @endif
        @endif

        @if(Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector() ||
        Auth::user()->isKoordinator())
        @if ($candidate->date_start_work)
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Первый рабочий день</label>

            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    {{$candidate->date_start_work}}
                </span>
            </div>
        </div>
        @endif
        @endif

        @if(Auth::user()->isAdmin() || Auth::user()->isRecruitmentDirector() ||
        Auth::user()->isKoordinator())
        @if ($candidate->date_start_work)
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Отработал 7 дней</label>

            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    {{$candidate->worked ? 'Да' : 'Нет'}}
                </span>
            </div>
        </div>
        @endif
        @endif

        @if (isset($candidate->legal_days_left))
        <div class="row mb-3">
            <label class="col-lg-4 fw-bold text-muted">Осталось легальных дней</label>

            <div class="col-lg-8">
                <span class="fw-bolder fs-6 text-gray-800">
                    {{$candidate->legal_days_left}}
                </span>
            </div>
        </div>
        @endif
    </div>


    @if(Auth::user()->isRecruiter() || Auth::user()->isLogist() || Auth::user()->isAdmin()
    ||
    Auth::user()->isRecruitmentDirector())
    <div style="padding: 0 2.25rem">
        @include('candidates.include.add-arrivals')
    </div>
    @endif

    @if(
        Auth::user()->isLegalizationManager() 
        || Auth::user()->isTrud() 
        ||Auth::user()->isAdmin()
    )
    <div class="p-9">
        @include('candidates.include.oswiadczenie')
    </div>
    @endif
</div>
