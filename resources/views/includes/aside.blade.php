<div id="kt_aside" class="aside aside-light aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{URL::to('/')}}/dashboard">
            <img alt="Logo" src="{{URL::to('/')}}/assets/media/logos/g10.png" class="h-40px logo" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.5"
                        d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z"
                        fill="currentColor" />
                    <path
                        d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z"
                        fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
            data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">

                @if (
                    Auth::user()->isRecruiter()
                    || Auth::user()->isFreelancer()
                    || Auth::user()->isLogist()
                    || Auth::user()->isTrud()
                    || Auth::user()->isCoordinator()
                    || Auth::user()->isAccountant()
                    || Auth::user()->isSupportManager()
                    || Auth::user()->isLegalizationManager()
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*tasks*') ) active @endif" href="{{url('/')}}/tasks">
                        <span class="menu-icon">
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
                        <span class="menu-title">{{__('Задачи')}}</span>
                        <span style="display: none;" id="countTask" class="badge badge-circle badge-warning"></span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission(['task', 'task.view'])
                )
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion @if (Request::is('*tasks*') ) show @endif">
                    <!--begin:Menu link-->
                    <span class="menu-link @if (Request::is('*tasks*') ) active @endif">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Задачи')}}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <!--end:Menu link-->

                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion menu-active-bg @if (Request::is('tasks') ) show @endif">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('tasks/all') ) active @endif" 
                            href="/tasks/all">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Все задачи')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        
                        @if (
                            Auth::user()->isAdmin()
                            || Auth::user()->hasPermission(['taskTemplate', 'taskTemplate.view'])
                        )
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('*tasks/templates') ) active @endif" 
                            href="/tasks/templates">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Шаблоны задач')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        @endif
                        
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                @endif

                {{-- @if (Auth::user()->isAccountant())
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*accountants-department*')) active @endif"
                        href="{{url('/')}}/accountants-department">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z"
                                    fill="currentColor"></path>
                                <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="currentColor"></rect>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">{{__('Администрация')}}</span>
                    </a>
                </div>    
                @endif --}}

                @if(Auth::user()->isSupportManager())
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*manager/dashboard*') ) active @endif"
                        href="{{url('/')}}/manager/dashboard">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M11 2.375L2 9.575V20.575C2 21.175 2.4 21.575 3 21.575H9C9.6 21.575 10 21.175 10 20.575V14.575C10 13.975 10.4 13.575 11 13.575H13C13.6 13.575 14 13.975 14 14.575V20.575C14 21.175 14.4 21.575 15 21.575H21C21.6 21.575 22 21.175 22 20.575V9.575L13 2.375C12.4 1.875 11.6 1.875 11 2.375Z"
                                    fill="currentColor"></path>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Главная')}}</span>
                    </a>
                </div>
                @endif

                @if(
                    Auth::user()->isAccountant()
                    || Auth::user()->isFreelancer()
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*requests*') ) active @endif" href="{{url('/')}}/requests">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M22 7H2V11H22V7Z" fill="currentColor"></path>
                                <path opacity="0.3"
                                    d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19ZM14 14C14 13.4 13.6 13 13 13H5C4.4 13 4 13.4 4 14C4 14.6 4.4 15 5 15H13C13.6 15 14 14.6 14 14ZM16 15.5C16 16.3 16.7 17 17.5 17H18.5C19.3 17 20 16.3 20 15.5C20 14.7 19.3 14 18.5 14H17.5C16.7 14 16 14.7 16 15.5Z"
                                    fill="currentColor"></path>
                            </svg>

                        </span>
                        <span class="menu-title">{{__('История запросов')}}</span>
                    </a>
                </div>
                @endif
                
                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->isRecruitmentDirector()
                    || Auth::user()->hasPermission(['user', 'user.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*users*')) active @endif"
                        href="{{url('/')}}/users">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z"
                                    fill="currentColor"></path>
                                <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="currentColor"></rect>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">{{__('Сотрудники')}}</span>
                    </a>
                </div>    
                @endif
                
                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->isRecruiter()
                    || Auth::user()->isFreelancer()
                    || Auth::user()->isRecruitmentDirector()
                    || Auth::user()->isHeadOfEmploymentDepartment()
                    || Auth::user()->hasPermission(['vacancy', 'vacancy.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*vacancies*') || Request::is('*vacancy*')) active @endif"
                        href="{{url('/')}}/vacancies">
                        <span class="menu-icon">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path opacity="0.3"
                                    d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                                    fill="currentColor"></path>
                            </svg>

                        </span>
                        <span class="menu-title">{{__('Вакансии')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->isSupportManager()
                    || Auth::user()->hasPermission(['freelancer', 'freelancer.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*freelancers*') ) active @endif"
                        href="{{url('/')}}/freelancers">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path opacity="0.3"
                                    d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z"
                                    fill="currentColor" />
                                <path
                                    d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Фрилансеры')}}</span>
                    </a>
                </div>
                @endif
                

                @if (Auth::user()->isAdmin() || Auth::user()->hasPermission(['client', 'client.view']))
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*client*') ) active @endif" href="{{url('/')}}/clients">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: assets/media/icons/duotune/communication/com014.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M16.0173 9H15.3945C14.2833 9 13.263 9.61425 12.7431 10.5963L12.154 11.7091C12.0645 11.8781 12.1072 12.0868 12.2559 12.2071L12.6402 12.5183C13.2631 13.0225 13.7556 13.6691 14.0764 14.4035L14.2321 14.7601C14.2957 14.9058 14.4396 15 14.5987 15H18.6747C19.7297 15 20.4057 13.8774 19.912 12.945L18.6686 10.5963C18.1487 9.61425 17.1285 9 16.0173 9Z"
                                    fill="currentColor" />
                                <rect opacity="0.3" x="14" y="4" width="4" height="4" rx="2" fill="currentColor" />
                                <path
                                    d="M4.65486 14.8559C5.40389 13.1224 7.11161 12 9 12C10.8884 12 12.5961 13.1224 13.3451 14.8559L14.793 18.2067C15.3636 19.5271 14.3955 21 12.9571 21H5.04292C3.60453 21 2.63644 19.5271 3.20698 18.2067L4.65486 14.8559Z"
                                    fill="currentColor" />
                                <rect opacity="0.3" x="6" y="5" width="6" height="6" rx="3" fill="currentColor" />
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">{{__('Клиенты')}}</span>
                    </a>
                </div>
                @endif
                
                @if (
                    (Auth::user()->isAdmin()
                    || Auth::user()->isRecruiter()
                    || Auth::user()->isFreelancer()
                    || Auth::user()->isLogist()
                    || Auth::user()->isTrud()
                    || Auth::user()->isRecruitmentDirector()
                    || Auth::user()->isLegalizationManager()
                    || Auth::user()->hasPermission(['candidate', 'candidate.view']))
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*candidates') ) active @endif"
                        href="{{url('/')}}/candidates">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z"
                                    fill="currentColor" />
                            </svg>

                        </span>

                        <span class="menu-title">{{__('Кандидаты')}}</span>
                    </a>
                </div>
                @endif

                @if (Auth::user()->isAdmin() || Auth::user()->hasPermission(['employee', 'employee.view']))
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*employees') ) active @endif"
                        href="{{url('/')}}/employees">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z"
                                    fill="currentColor" />
                            </svg>

                        </span>
                        
                        <span class="menu-title">{{__('Работники')}}</span>
                    </a>
                </div>
                @endif

                @if (Auth::user()->isAdmin() || Auth::user()->isCoordinator())
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*work-logs') ) active @endif"
                        href="{{url('/')}}/work-logs">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M12,22 C7.02943725,22 3,17.9705627 3,13 C3,8.02943725 7.02943725,4 12,4 C16.9705627,4 21,8.02943725 21,13 C21,17.9705627 16.9705627,22 12,22 Z" fill="currentColor" opacity="0.3"/>
                                    <path d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="currentColor"/>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Часы работы')}}</span>
                    </a>
                </div>
                @endif

                @if (Auth::user()->isAccountant())
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*calculation') ) active @endif"
                        href="{{route('accountants.calculation')}}">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z"
                                    fill="currentColor" />
                            </svg>

                        </span>
                        <span class="menu-title">{{__('Часы работы')}}</span>
                    </a>
                </div>
                @endif

                @if(
                    Auth::user()->isAdmin() 
                    || Auth::user()->isAccountant()
                    || Auth::user()->hasPermission(['firm', 'firm.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*accountant/profile') ) active @endif"
                        href="{{url('/')}}/accountant/profile">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path opacity="0.3"
                                    d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z"
                                    fill="currentColor"></path>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Фирмы')}}</span>
                    </a>
                </div>
                @endif
                
                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission(['lead', 'lead.view'])
                )
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion @if (Request::is('*leads*') ) show @endif">
                    <!--begin:Menu link-->
                    <span class="menu-link @if (Request::is('*leads*') ) active @endif">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Лиды')}}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion menu-active-bg @if (Request::is('*leads*') ) show @endif">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('*leads') ) active @endif" href="{{url('/')}}/leads">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Все лиды')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        {{-- <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('*leads/sources') ) active @endif" href="{{url('/')}}/leads/sources">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Источники')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div> --}}
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        @if (
                            Auth::user()->isAdmin()
                            || Auth::user()->hasPermission('lead.setting')
                        )
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('*leads/settings') ) active @endif" href="{{url('/')}}/leads/settings">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Пакеты')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('*leads/status-inform') ) active @endif" href="{{url('/')}}/leads/status-inform">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Статусы')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        @endif

                        @if (
                            Auth::user()->isAdmin()
                            || Auth::user()->hasPermission('lead.import')
                        )
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link @if (Request::is('leads/import') ) active @endif" 
                                href="/leads/import">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{__('Импорт')}}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        @endif

                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                @endif
                
                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->isLogist()
                    || Auth::user()->isTrud()
                    || Auth::user()->isRecruitmentDirector()
                    || Auth::user()->hasPermission(['arrival', 'arrival.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*candidates/arrivals') ) active @endif"
                        href="{{url('/')}}/candidates/arrivals">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M8.39961 20.5073C7.29961 20.5073 6.39961 19.6073 6.39961 18.5073C6.39961 17.4073 7.29961 16.5073 8.39961 16.5073H9.89961C11.7996 16.5073 13.3996 14.9073 13.3996 13.0073C13.3996 11.1073 11.7996 9.50732 9.89961 9.50732H8.09961L6.59961 11.2073C6.49961 11.3073 6.29961 11.4073 6.09961 11.5073C6.19961 11.5073 6.19961 11.5073 6.29961 11.5073H9.79961C10.5996 11.5073 11.2996 12.2073 11.2996 13.0073C11.2996 13.8073 10.5996 14.5073 9.79961 14.5073H8.39961C6.19961 14.5073 4.39961 16.3073 4.39961 18.5073C4.39961 20.7073 6.19961 22.5073 8.39961 22.5073H15.3996V20.5073H8.39961Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M8.89961 8.7073L6.69961 11.2073C6.29961 11.6073 5.59961 11.6073 5.19961 11.2073L2.99961 8.7073C2.19961 7.8073 1.7996 6.50732 2.0996 5.10732C2.3996 3.60732 3.5996 2.40732 5.0996 2.10732C7.6996 1.50732 9.99961 3.50734 9.99961 6.00734C9.89961 7.00734 9.49961 8.0073 8.89961 8.7073Z"
                                    fill="currentColor" />
                                <path
                                    d="M5.89961 7.50732C6.72804 7.50732 7.39961 6.83575 7.39961 6.00732C7.39961 5.1789 6.72804 4.50732 5.89961 4.50732C5.07119 4.50732 4.39961 5.1789 4.39961 6.00732C4.39961 6.83575 5.07119 7.50732 5.89961 7.50732Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17.3996 22.5073H15.3996V13.5073C15.3996 12.9073 15.7996 12.5073 16.3996 12.5073C16.9996 12.5073 17.3996 12.9073 17.3996 13.5073V22.5073Z"
                                    fill="currentColor" />
                                <path
                                    d="M21.3996 18.5073H15.3996V13.5073H21.3996C22.1996 13.5073 22.5996 14.4073 22.0996 15.0073L21.2996 16.0073L22.0996 17.0073C22.6996 17.6073 22.1996 18.5073 21.3996 18.5073Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Приезды')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission(['transportations', 'transportations.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*transportations') ) active @endif"
                        href="{{url('/')}}/transportations">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M8.39961 20.5073C7.29961 20.5073 6.39961 19.6073 6.39961 18.5073C6.39961 17.4073 7.29961 16.5073 8.39961 16.5073H9.89961C11.7996 16.5073 13.3996 14.9073 13.3996 13.0073C13.3996 11.1073 11.7996 9.50732 9.89961 9.50732H8.09961L6.59961 11.2073C6.49961 11.3073 6.29961 11.4073 6.09961 11.5073C6.19961 11.5073 6.19961 11.5073 6.29961 11.5073H9.79961C10.5996 11.5073 11.2996 12.2073 11.2996 13.0073C11.2996 13.8073 10.5996 14.5073 9.79961 14.5073H8.39961C6.19961 14.5073 4.39961 16.3073 4.39961 18.5073C4.39961 20.7073 6.19961 22.5073 8.39961 22.5073H15.3996V20.5073H8.39961Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M8.89961 8.7073L6.69961 11.2073C6.29961 11.6073 5.59961 11.6073 5.19961 11.2073L2.99961 8.7073C2.19961 7.8073 1.7996 6.50732 2.0996 5.10732C2.3996 3.60732 3.5996 2.40732 5.0996 2.10732C7.6996 1.50732 9.99961 3.50734 9.99961 6.00734C9.89961 7.00734 9.49961 8.0073 8.89961 8.7073Z"
                                    fill="currentColor" />
                                <path
                                    d="M5.89961 7.50732C6.72804 7.50732 7.39961 6.83575 7.39961 6.00732C7.39961 5.1789 6.72804 4.50732 5.89961 4.50732C5.07119 4.50732 4.39961 5.1789 4.39961 6.00732C4.39961 6.83575 5.07119 7.50732 5.89961 7.50732Z"
                                    fill="currentColor" />
                                <path opacity="0.3"
                                    d="M17.3996 22.5073H15.3996V13.5073C15.3996 12.9073 15.7996 12.5073 16.3996 12.5073C16.9996 12.5073 17.3996 12.9073 17.3996 13.5073V22.5073Z"
                                    fill="currentColor" />
                                <path
                                    d="M21.3996 18.5073H15.3996V13.5073H21.3996C22.1996 13.5073 22.5996 14.4073 22.0996 15.0073L21.2996 16.0073L22.0996 17.0073C22.6996 17.6073 22.1996 18.5073 21.3996 18.5073Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Регулярные перевозки')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->isRecruitmentDirector()
                    || Auth::user()->isMarketer()
                    || Auth::user()->hasPermission(['statistics', 'statistics.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*statistics*')) active @endif"
                        href="{{url('/')}}/statistics">
                        <span class="menu-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="9" width="3" height="10" rx="1.5" fill="currentColor"></rect>
                                <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="currentColor"></rect>
                                <rect x="18" y="11" width="3" height="8" rx="1.5" fill="currentColor"></rect>
                                <rect x="3" y="13" width="3" height="6" rx="1.5" fill="currentColor"></rect>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Статистика')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->isRecruitmentDirector()
                    || Auth::user()->isRealEstateManager()
                    || Auth::user()->hasPermission(['housing', 'housing.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*housing*') ) active @endif"
                        href="{{url('/')}}/housing">
                        <span class="menu-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 2.375L2 9.575V20.575C2 21.175 2.4 21.575 3 21.575H9C9.6 21.575 10 21.175 10 20.575V14.575C10 13.975 10.4 13.575 11 13.575H13C13.6 13.575 14 13.975 14 14.575V20.575C14 21.175 14.4 21.575 15 21.575H21C21.6 21.575 22 21.175 22 20.575V9.575L13 2.375C12.4 1.875 11.6 1.875 11 2.375Z" fill="currentColor"/>
                            </svg>                                
                        </span>
                        <span class="menu-title">{{__('Жилье')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission(['cars', 'cars.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*cars*') ) active @endif"
                        href="{{url('/')}}/cars">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M9,15 L7.5,15 C6.67157288,15 6,15.6715729 6,16.5 C6,17.3284271 6.67157288,18 7.5,18 C8.32842712,18 9,17.3284271 9,16.5 L9,15 Z M9,15 L9,9 L15,9 L15,15 L9,15 Z M15,16.5 C15,17.3284271 15.6715729,18 16.5,18 C17.3284271,18 18,17.3284271 18,16.5 C18,15.6715729 17.3284271,15 16.5,15 L15,15 L15,16.5 Z M16.5,9 C17.3284271,9 18,8.32842712 18,7.5 C18,6.67157288 17.3284271,6 16.5,6 C15.6715729,6 15,6.67157288 15,7.5 L15,9 L16.5,9 Z M9,7.5 C9,6.67157288 8.32842712,6 7.5,6 C6.67157288,6 6,6.67157288 6,7.5 C6,8.32842712 6.67157288,9 7.5,9 L9,9 L9,7.5 Z M11,13 L13,13 L13,11 L11,11 L11,13 Z M13,11 L13,7.5 C13,5.56700338 14.5670034,4 16.5,4 C18.4329966,4 20,5.56700338 20,7.5 C20,9.43299662 18.4329966,11 16.5,11 L13,11 Z M16.5,13 C18.4329966,13 20,14.5670034 20,16.5 C20,18.4329966 18.4329966,20 16.5,20 C14.5670034,20 13,18.4329966 13,16.5 L13,13 L16.5,13 Z M11,16.5 C11,18.4329966 9.43299662,20 7.5,20 C5.56700338,20 4,18.4329966 4,16.5 C4,14.5670034 5.56700338,13 7.5,13 L11,13 L11,16.5 Z M7.5,11 C5.56700338,11 4,9.43299662 4,7.5 C4,5.56700338 5.56700338,4 7.5,4 C9.43299662,4 11,5.56700338 11,7.5 L11,11 L7.5,11 Z" fill="currentColor" fill-rule="nonzero"/>
                                </g>
                            </svg>                         
                        </span>
                        <span class="menu-title">{{__('Машины')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission(['handbook', 'handbook.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*handbooks*') ) active @endif" href="{{url('/')}}/handbooks">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path opacity="0.3"
                                    d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z"
                                    fill="currentColor"></path>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Настройки')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('*roles*') ) active @endif" href="{{url('/')}}/roles">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="currentColor"/>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Роли')}}</span>
                    </a>
                </div>
                @endif

                @if (
                    Auth::user()->isAdmin()
                    || Auth::user()->hasPermission(['templates', 'templates.view'])
                )
                <div class="menu-item">
                    <a class="menu-link  @if (Request::is('templates*') ) active @endif" href="{{url('/')}}/templates">
                        <span class="menu-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <rect fill="currentColor" x="4" y="5" width="16" height="3" rx="1.5"/>
                                    <path d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L12.5,10 C13.3284271,10 14,10.6715729 14,11.5 C14,12.3284271 13.3284271,13 12.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z" fill="currentColor" opacity="0.3"/>
                                </g>
                            </svg>
                        </span>
                        <span class="menu-title">{{__('Шаблоны документов')}}</span>
                    </a>
                </div>
                @endif

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->

</div>
