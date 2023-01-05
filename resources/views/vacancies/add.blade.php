@if(
    Auth::user()->isAdmin() 
    || Auth::user()->isRecruitmentDirector() 
    || Auth::user()->isHeadOfEmploymentDepartment()
    || (Auth::user()->hasPermission('vacancy.create') && !request()->has('id'))
    || (Auth::user()->hasPermission('vacancy.edit') && request()->has('id'))
)
    @include('vacancies.vacancy.include_add')
@else
    @include('vacancies.vacancy.include_view')
@endif
