<?php

use App\Http\Controllers\AccountantsController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FreelancersController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateArrivalsController;
use App\Http\Controllers\HandbookController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FieldsMutationController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\LeadsSettingsController;
use App\Http\Controllers\PingsController;
use App\Http\Controllers\StatusesController;
use App\Http\Controllers\HousingsController;
use App\Http\Controllers\WorkLogsController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\AccountantsDepartmentController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\LeadStatusInformsController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\OswiadczeniesController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\TransportationsController;
use App\Http\Controllers\LegalisationsController;
use App\Services\UserOptionsService;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [AuthController::class, 'getLogin'])->name("login");
Route::post('login', [AuthController::class, 'postLogin']);
Route::get('logout', [AuthController::class, 'getLogout']);
Route::get('/recruiter/invite/{id}', [RecruiterController::class, 'getInvite']);
Route::post('/recruiter/portal/add', [RecruiterController::class, 'getInviteAdd']);

Route::get('/manager/invite/{id}', [ManagerController::class, 'getInvite']);
Route::post('/manager/portal/add', [ManagerController::class, 'getInviteAdd']);

Route::get('/user/invite/{id}', [FreelancersController::class, 'getInvite']);
Route::post('/freelancer/portal/add', [FreelancersController::class, 'getInviteAdd']);

Route::group(['middleware' => 'auth'], function () {
    // group middleware

    Route::get('/set-locale/{lang}', function (UserOptionsService $user_opt) {
        $user_opt->setLanguage(auth()->user()->id, request('lang'));
        return Redirect::to(request()->headers->get('referer'));
    });
    Route::get('/', [AuthController::class, 'getMain'])
        ->middleware('auth');

    Route::get('/dashboard', function () {
        // make it cleaner
        Route::get('/dashboard', function () {
            $redirects = [
                1 => 'users',
                2 => '/tasks',
                3 => 'tasks',
                4 => 'tasks',
                5 => '/tasks',
                7 => '/tasks',
                8 => '/tasks',
                9 => 'users',
                11 => 'leads',
                12 => '/tasks',
                13 => '/housing',
                14 => '/vacancies',
            ];

            if (Auth::user()->isCoordinator()) {
                return Redirect::to('tasks');
            }

            if (Auth::user()->group_id > 99) {
                $permissions = [
                    'user.view' => '/users',
                    'vacancy.view' => '/vacancies',
                    'freelancer.view' => '/freelancers',
                    'candidate.view' => '/candidates',
                    'client.view' => '/clients',
                    'lead.view' => '/leads',
                    'statistics.view' => '/statistics',
                    'housing.view' => '/housing',
                    'cars.view' => '/cars',
                    'handbook.view' => '/handbooks',
                    'firm.view' => '/accountant/profile',
                    'templates.view' => '/templates',
                    'transportations.view' => '/transportations',
                ];

                foreach ($permissions as $permission => $url) {
                    if (Auth::user()->hasPermission([$permission])) {
                        return Redirect::to($url);
                    }
                }
            } elseif (isset($redirects[Auth::user()->group_id])) {
                return Redirect::to($redirects[Auth::user()->group_id]);
            }
        });
    });






    Route::get('/a/id', [AuthController::class, 'postAuthById']);

    Route::controller(UsersController::class)->group(function () {
        Route::get('/users/auth_by/{id}', 'authBy')
            ->middleware(['is_admin', 'roles:1|user|user.view']);

        Route::middleware('roles:1|9|user|user.view')->group(function () {
            Route::get('users', 'getIndex');
            Route::get('users/getJson', 'getJson')->name('users.json');

            Route::post('users/add', 'addUser')->name('users.add')
                ->middleware('roles:1|user.create|user.edit');

            Route::post('/files/user/add', 'filesUserAdd')
                ->middleware('roles:1|user.create|user.edit');

            Route::get('/users/activation', 'usersActivation')->middleware('roles:1|user.edit');

            Route::post('/users/activate-lead-settings', 'activateLeadSettings')
                ->middleware('roles:1|user.edit');

            Route::get('users/lead-settings/json', 'withLeadSettingsJson')->name('users.lead-settings.json');
        });

        Route::post('users/fl/add', 'addFlUser')->name('users.fl.add')
            ->middleware('roles:1|2|8|user|user.view|freelancer.edit|freelancer.create');
        Route::get('/users/ajax/id/{id}', 'getUserAjax')->name('users.ajax.id')
            ->middleware('roles:1|2|3|8|9|user|user.view|freelancer.edit');

        Route::get('user/profile', 'getProfile');
        Route::post('user/profile', 'postProfile')->name('users.profile.save');

        Route::get('/users/recruiters-rating', 'getRecruitersRatingJson')
            ->middleware('roles:2');
    });

    // слишком длинный, лучше разделить их
    // Пр. require(app_path() . '/routes/freelancers.php');

    // freelancers
    Route::get('freelancers', [FreelancersController::class, 'getIndex'])
        ->middleware('roles:1|2|8|freelancer|freelancer.view');
    Route::get('freelancers/getJson', [FreelancersController::class, 'getJson'])->name('freelancers.json')
        ->middleware('roles:1|2|8|freelancer|freelancer.view');
    Route::get('freelancers/set_fl_status', [FreelancersController::class, 'setFlStatus'])
        ->middleware('roles:1|2|8|freelancer.edit');

    // clients
    Route::controller(ClientController::class)->group(function () {
        Route::middleware('roles:1|6|7|13|client|client.view')->group(function () {
            Route::get('clients', 'getIndex');
            Route::get('clients/getJson', 'getJson')->name('clients.json');
            Route::get('client/view', 'viewIndex');
        });

        Route::middleware('roles:1|client.create|client.edit')->group(function () {
            Route::get('clients/activation', 'clientsActivation');
            Route::get('client/add', 'getAdd');
            Route::post('client/add', 'postAdd')->name('clients.add');
        });
    });

    // vacancies
    Route::controller(VacancyController::class)->group(function () {
        Route::middleware('roles:1|2|3|9|14|vacancy|vacancy.view')->group(function () {
            Route::get('vacancies', 'getIndex');
            Route::get('vacancy/add', 'getAdd');
            Route::get('vacancy/getJson', 'getJson')->name('vacancy.json');
        });

        Route::post('vacancy/add', 'postAdd')
            ->middleware('roles:1|14|vacancy.create|vacancy.edit')->name('vacancy.add');

        Route::middleware('roles:1|2|3|14|vacancy.create|vacancy.edit')->group(function () {
            Route::post('files/add', 'filesAdd');
            Route::get('vacancy/activation', 'vacancyActivation');
            Route::get('vacancy/changecost', 'vacancyChangecost');
            Route::get('vacancy/change_сost_pay_lead', 'vacancyChangecostpaylead');
            Route::get('vacancy/change_housing_cost', 'vacancyChangehousingcost');
            Route::get('vacancy/change_salary', 'vacancySalary');
            Route::get('vacancy/count_people', 'vacancyCountpeople');
            Route::get('vacancy/count_women', 'vacancyCountwomen');
            Route::get('vacancy/count_men', 'vacancyCountmen');
        });
        // Почему в одних нижний пробел, а в других дефис,
        // если логика такая то ок, а если нет лучше именовать надо одинаково
        Route::get('vacancy/check-filling', 'checkFilling');
        Route::get('vacancy/check-nacionality', 'checkNacionality');
    });

    // candidates
    Route::controller(CandidateController::class)->group(function () {
        Route::middleware('roles:1|2|3|4|5|6|7|9|12|candidate|candidate.view|employee|employee.view')->group(function () {
            Route::get('candidates', 'getIndex');
            Route::get('candidates/getJson', 'getJson')->name('candidates.json');
            Route::get('candidate/set_status', 'setStatus');
            Route::post('candidate/set_status_special', 'setStatusSpecial');
            Route::get('candidate/view', 'viewIndex');
            Route::get('candidate/positions/json', 'positionsJson')->name('candidates.positions.json');
            Route::post('candidate/position/update', 'updatePosition');
            Route::get('candidate/housing/json', 'housingJson')->name('candidates.housing.json');
            Route::post('candidate/housing/update', 'updateHousingPeriod');
            Route::get('candidate/work-logs-history/json', 'workLogsHistoryJson');
            Route::get('candidate/documents/json', 'documentsJson')->name('candidates.documents.json');
        });

        Route::get('/candidate/add', 'addIndex')
            ->middleware('roles:1|2|3|4|5|9|12|candidate.create|candidate.edit|employee.edit');

        Route::post('/candidate/add', 'postAdd')
            ->name('candidate.add')
            ->middleware('roles:1|2|3|4|5|9|12|candidate.create|candidate.edit|employee.edit');

        Route::post('candidate/files/ticket/add', 'filesTicketAdd')
            ->middleware('roles:1|2|3|4|candidate');
        Route::post('candidates/remove', 'remove')
            ->middleware('roles:1|candidate.delete|employee.delete');
        Route::get('/candidates/statuses/get-count-json', 'getStatusesCountJson')
            ->middleware('roles:1|2|6|9|12|candidate|candidate.view');
        Route::post('/candidate/add-vacancy', 'addVacancy')
            ->middleware('roles:5|6|candidate|candidate.view');
        Route::post('/candidate/add-client', 'addClient')
            ->middleware('roles:5|candidate|candidate.view');
        Route::post('/candidate/housing/edit', 'updateHousing')
            ->name('candidate.update.housing')
            ->middleware('roles:1|6|9|candidate|candidate.view');
        Route::post('/candidate/employment/edit', 'updateEmployment')
            ->name('candidate.update.employment')
            ->middleware('roles:1|6|9|candidate|candidate.view');
        Route::post('/candidate/set-gender', 'setGender')
            ->middleware('roles:1|5|6|9|candidate.edit|employee.edit');
    });

    Route::controller(EmployeesController::class)->group(function () {
        Route::middleware('roles:1|employee|employee.view')->group(function () {
            Route::get('employees', 'index');
            Route::get('employees/json', 'listJson')->name('employees.json');
        });
    });

    // arrivals
    Route::controller(CandidateArrivalsController::class)->group(function () {
        Route::get('candidates/arrivals', 'getArrivalsIndex')
            ->middleware('roles:1|4|5|9|arrival.view|arrival.view');
        Route::get('candidates/arrivals/all/getJson', 'getArrivalsAllJson')->name('candidates.arrivals.all.json')
            ->middleware('roles:1|4|5|9|arrival|arrival.view');
        Route::post('candidates/arrivals/getJson', 'getArrivalsJson')->name('candidates.arrivals.json')
            ->middleware('roles:1|2|4|5|9|arrival|arrival.view');
        Route::post('candidates/arrivals/add_ticket', 'addTicketDoc')
            ->middleware('roles:1|2|4|5|arrival|arrival.view');
        Route::post('candidates/arrivals/add', 'postArrivalAdd')
            ->middleware('roles:1|2|4|arrival|arrival.view');
        Route::get('candidates/arrivals/activation', 'postArrivalsActivation')
            ->middleware('roles:1|4|5|9|arrival|arrival.view');
        Route::get('candidates/arrivals/count', 'getArrivalsCount')
            ->middleware('roles:1|2|4|5|9|arrival|arrival.view');
    });

    // Leads
    Route::group([
        'prefix' => 'leads',
        'controller' => LeadsController::class,
    ], function () {
        Route::get('/', 'getIndex')->name('leads')->middleware('roles:1|lead|lead.view');
        Route::get('history', 'getHistoryIndex')->middleware('roles:1|lead|lead.view');
        Route::get('getJson', 'getJson')->name('leads.json')->middleware('roles:1|lead|lead.view');
        Route::get('ajax/id/{id}', 'getLeadAjax')->middleware('roles:1|2|3|4|5|6|9|11|lead|lead.view');
        Route::post('details/store', 'storeDetails')->middleware('roles:1|2|3|4|5|6|9|11|lead|lead.view');

        // здесь тоже надо групировать
        Route::get('force-import', 'forceImport')->middleware('roles:1|lead.import');
        Route::get('reset', 'reset')->middleware('roles:1|lead.import');
        Route::get('import', 'importIndex')->middleware('roles:1|lead.import');
        Route::post('import/upload', 'importUpload')->middleware('roles:1|lead.import');
        Route::post('import/process', 'importProcess')->middleware('roles:1|lead.import');
    });

    Route::group([
        'middleware' => 'roles:1|lead.setting',
        'controller' => LeadsSettingsController::class,
    ], function () {
        Route::get('leads/settings', 'listView');
        Route::get('leads/settings/json', 'listJson')->name('leads.settings.json');
        Route::get('leads/settings/item/json', 'itemJson');
        Route::post('leads/settings/update', 'update');
    });

    Route::get('/leads/status-inform/json', [LeadStatusInformsController::class, 'listJson'])
        ->middleware('roles:1|9|lead|lead.view')->name('leads.status-inform.json');
    Route::resource('leads/status-inform', LeadStatusInformsController::class)
        ->middleware('roles:1|2|9|lead|lead.view');

    // request transaction
    Route::get('requests', [FinanceController::class, 'getIndex'])
        ->middleware('roles:1|3|7');
    Route::post('requests/getJson', [FinanceController::class, 'getJson'])->name('finance.json')
        ->middleware('roles:1|3|7');
    Route::post('requests/add', [FinanceController::class, 'postAdd'])->name('finance.add')
        ->middleware('roles:1|3|7');
    Route::get('requests/change/status', [FinanceController::class, 'postRequestsChangeStatus'])
        ->middleware('roles:1|7');
    Route::get('requests/change/firm', [FinanceController::class, 'postRequestsChangeFirm'])
        ->middleware('roles:1|7');
    Route::post('requests/file/add', [FinanceController::class, 'addSuccessPaymentDoc'])
        ->middleware('roles:1|7');


    // handbooks
    Route::group([
        'prefix' => 'handbooks',
        'controller' => HandbookController::class,
        'middleware' => 'roles:1|handbook|handbook.view',
    ], function () {
        Route::get('/', 'getIndex');
        Route::get('delete', 'deleteHandbook')
            ->middleware('roles:1|handbook.delete');
        Route::get('add', 'addHandbook')
            ->middleware('roles:1|handbook.create');
    });

    //recruiter
    Route::get('/recruiter/dashboard', [RecruiterController::class, 'getIndex'])
        ->middleware('roles:2');
    Route::get('/accountant/profile', [AccountSettingController::class, 'getProfile'])
        ->middleware('roles:1|7|firm|firm.view');
    Route::post('/accountant/profile/save', [AccountSettingController::class, 'postProfileSave'])->name('accountant.profile.save')
        ->middleware('roles:1|7|firm.create|firm.edit');
    Route::get('/accountant/firm/delete', [AccountSettingController::class, 'deleteFirm'])
        ->middleware('roles:1|7|firm.delete');

    Route::get('/manager/dashboard', [ManagerController::class, 'getIndex'])
        ->middleware('roles:8');

    // tasks
    Route::group([
        'prefix' => 'tasks',
        'as' => 'tasks.',
        'controller' => TaskController::class,
    ], function () {
        Route::get('/', 'getIndex');
        Route::get('getJson', 'getJson')->name('json');
        Route::get('all', 'allIndex')->name('all')->middleware('roles:1|task|task.view');
        Route::get('all/json', 'allIndexJson')->name('all.json')->middleware('roles:1|task|task.view');
        Route::get('ajax/id/{id}', 'getTaskAjax');
        Route::post('action', 'doAction');
        Route::get('create', 'create')->name('create')->middleware('roles:1|task.create');
        Route::post('store', 'store')->middleware('roles:1|task.create');

        Route::prefix('templates')->group(function () {
            Route::get('/', 'templatesIndex')
                ->middleware('roles:1|taskTemplate|taskTemplate.view');

            Route::get('json', 'templatesJson')->name('templates.json')
                ->middleware('roles:1|taskTemplate|taskTemplate.view');

            Route::get('create', 'createTemplate')->name('templates.create')
                ->middleware('roles:1|taskTemplate.create');

            Route::post('store', 'storeTemplate')
                ->middleware('roles:1|taskTemplate.create');

            Route::get('{id}/edit', 'editTemplate')->name('templates.edit')
                ->middleware('roles:1|taskTemplate.edit');

            Route::post('update', 'updateTemplate')
                ->middleware('roles:1|taskTemplate.edit');

            Route::get('{id}/json', 'showTemplateJson')
                ->middleware('roles:1|taskTemplate|taskTemplate.view');

            Route::get('{id}', 'showTemplate')
                ->middleware('roles:1|taskTemplate|taskTemplate.view');
        });
    });

    // ajax search
    Route::group([
        'prefix' => 'search',
        'controller' => SearchController::class,
    ], function () {
        Route::get('vacancy/client', 'getAjaxVacancyClients');
        Route::get('vacancy/industry', 'getAjaxVacancyIndustry');
        Route::get('vacancy/nationality', 'getAjaxVacancyNationality');
        Route::get('vacancy/workplace', 'getAjaxVacancyWorkplace');
        Route::get('vacancy/docs', 'getAjaxVacancyDocs');
        Route::get('vacancy', 'getVacancyJson');
        Route::get('client/industry', 'getAjaxClientIndustry');
        Route::get('client/workplace', 'getAjaxClientWorkplace');
        Route::get('client/coordinator', 'getAjaxClientCoordinator');
        Route::get('client/nationality', 'getAjaxClientNationality');
        Route::get('candidate/transport', 'getAjaxClientTransport');
        Route::get('candidate/realstatuswork', 'getAjaxClientRealstatuswork');
        Route::get('candidate/placearrive', 'getAjaxClientPlacearrive');
        Route::get('candidate/typedocs', 'getAjaxClientTypedocs');
        Route::get('candidate/country', 'getAjaxClientCountry');
        Route::get('candidate/citizenship', 'getAjaxClientCitizenship');
        Route::get('candidate/nacionality', 'getAjaxClientNacionality');
        Route::get('candidate/vacancy', 'getAjaxCandidateVacancy');
        Route::get('candidate/recruter', 'getAjaxCandidateRecruter');
        Route::get('leads/recruiters', 'getAjaxLeadsRecruiters');
        Route::get('leads/company', 'getLeadsCompanyJson');
        Route::get('leads/settings', 'getLeadsSettingsJson');
        Route::get('leads', 'getLeadsJson');
        Route::get('candidate/client', 'getAjaxCandidateClient');
        Route::get('candidates/clients', 'getAjaxCandidatesClients');
        Route::get('candidates/koordinators', 'getAjaxCandidatesKoordinators');
        Route::get('candidate/client/position', 'getAjaxCandidateClientPosition');
        Route::get('candidate/coordinators/client', 'getAjaxCandidateCoordinatorsClient');
        Route::get('candidates', 'getAjaxCandidates');
        Route::get('requests/freelacnsers', 'getAjaxCandidateFreelacnsers');
        Route::get('city', 'getAjaxCity');
        Route::get('housing', 'getHousingJson');
        Route::get('housing_room', 'getHousingRoomJson');
        Route::get('client', 'getClientJson');
        Route::get('speciality', 'getSpecialityJson');
        Route::get('users/has-field-mutations/json/{candidate_id}', 'getFieldsMutationUsersJson');
        Route::get('users/has-cars/json', 'getCarsUsersJson');
        Route::get('user/{role}', 'getUserJson');
        Route::get('transportations', 'getTransportationsJson');
        Route::get('car', 'getCarJson');
        Route::get('car', 'getCarJson');
    });

    // fields mutate
    Route::get('fields-mutation', [FieldsMutationController::class, 'getJson'])->name('fields-mutation.json');

    // statistics
    Route::group([
        'prefix' => 'statistics',
        'controller' => StatisticsController::class,
    ], function () {
        Route::get('/', 'getIndex')
            ->middleware('roles:1|9|11|statistics.view|statistics.view');
        Route::get('tasks', 'getTasksJson')
            ->middleware('roles:1|9|statistics|statistics.view')->name('tasks-statistics.json');
        Route::get('employment', 'getEmploymentJson')
            ->middleware('roles:1|2|6|9|statistics|statistics.view')->name('employment-statistics.json');
        Route::get('employment/excel', 'exportEmploymentExcel')
            ->middleware('roles:1|9|statistics|statistics.view')->name('employment-statistics.excel');
        Route::get('leads', 'getLeadsJson')
            ->middleware('roles:1|9|11|statistics|statistics.view')->name('leads-statistics.json');
    });

    // ping
    Route::post('ping', [PingsController::class, 'index'])->name('ping');

    // status
    Route::post('/status-manage', [StatusesController::class, 'index']);

    // Housing
    Route::group([
        'prefix' => 'housing',
        'as' => 'housing.',
        'controller' => HousingsController::class,
        'middleware' => 'roles:1|6|9|13|housing|housing.view',
    ], function () {
        Route::get('/', 'getIndex')->name('index');
        Route::get('json', 'getJson')->name('json');

        Route::get('view', 'viewIndex');

        Route::get('add', 'addIndex')
            ->middleware('roles:housing.edit|housing.create');
        Route::post('add', 'create')->name('create')
            ->middleware('roles:housing.edit|housing.create');

        Route::get('{housing_id}/rooms/json', 'getRoomsJson')->name('rooms.json');
        Route::post('room/add', 'createRoom')->name('room.create')
            ->middleware('roles:housing.edit');
        Route::post('room/remove', 'removeRoom')
            ->middleware('roles:housing.edit');
    });

    // Work Logs
    Route::group([
        'prefix' => 'work-logs',
        'as' => 'work-logs.',
        'controller' => WorkLogsController::class,
        'middleware' => 'roles:1|6|7|9|client|client.view',
    ], function () {
        Route::get('/', 'getView');
        Route::get('json', 'getJson')->name('json');
        Route::get('additions/json/{type}', 'additionsJson')->name('additions.json');
        Route::get('additions/item/json/{id}', 'additionItemJson');
        Route::post('add', 'create')->name('add');
        Route::post('add/additions', 'createAdditions')->name('add.additions');
        Route::post('complete', 'completeLog');
    });

    // Accountants
    Route::group([
        'prefix' => 'accountants',
        'as' => 'accountants.',
        'controller' => AccountantsController::class,
        'middleware' => 'roles:7',
    ], function () {
        Route::get('calculation', 'index')->name('calculation');
        Route::get('calculation/json', 'indexJson')->name('calculation.json');
    });

    // Accountants Department
    Route::group([
        'prefix' => 'accountants-department',
        'as' => 'accountants-department.',
        'controller' => AccountantsDepartmentController::class,
        'middleware' => 'roles:7',
    ], function () {
        Route::get('/', 'listView')->name('view');
        Route::get('json', 'listJson')->name('json');
    });

    // Cars
    Route::group([
        'prefix' => 'cars',
        'as' => 'cars.',
        'controller' => CarsController::class,
        'middleware' => 'roles:1|6|cars|cars.view',
    ], function () {
        Route::get('/', 'getIndex')->name('index');
        Route::get('json', 'getJson')->name('json');

        Route::get('view', 'itemView');
        Route::get('add', 'itemView')
            ->middleware('roles:cars.edit|cars.create');

        Route::post('add', 'create')->name('create')
            ->middleware('roles:cars.edit|cars.create');
    });

    // Roles
    Route::group([
        'prefix' => 'roles',
        'as' => 'roles.',
        'controller' => RolesController::class,
        'middleware' => 'roles:1',
    ], function () {
        Route::get('/', 'listView')->name('view');
        Route::get('json', 'listJson')->name('json');

        Route::get('view', 'itemView')->name('item');
        Route::get('add', 'itemView')->name('add');

        Route::post('create', 'createOrUpdate')->name('create');
        Route::post('update', 'createOrUpdate')->name('update');
    });

    Route::get('/permissions/get/{group_id}', function () {
        $items = \App\Models\UserPermission::getAllowedToRole(request('group_id'));
        return response()->json($items, 200);
    })->middleware('roles:1');

    Route::get('/role-permissions/all/json', function () {
        $items = \App\Models\RolePermission::getAll();

        return response()->json($items, 200);
    })->middleware('roles:1');

    Route::resource('options', OptionsController::class);

    Route::post('oswiadczenie/{id}', [OswiadczeniesController::class, 'update'])
        ->middleware('roles:1|5|12|candidate.edit|employee.edit');
    Route::resource('oswiadczenie', OswiadczeniesController::class)
        ->middleware('roles:1|5|12|candidate|candidate.view');

    // Templates
    Route::group([
        'prefix' => 'templates',
        'as' => 'templates.',
        'controller' => TemplatesController::class,
        'middleware' => 'roles:templates|templates.view',
    ], function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create')
            ->middleware('roles:templates.create');
        Route::get('json', 'listJson')->name('json');
        Route::get('document-preview', 'docPreview')->name('document-preview');
        Route::get('make-pdf', 'makePdf')->name('make-pdf');
        Route::get('{id}/json', 'itemJson')->name('item.json');
        Route::get('{id}/edit', 'edit')->name('edit')->middleware('roles:templates.edit');
        Route::get('{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store')->middleware('roles:templates.create|templates.edit');
    });

    // Transportations
    Route::group([
        'prefix' => 'transportations',
        'as' => 'transportations.',
        'controller' => TransportationsController::class,
        'middleware' => 'roles:1|transportations|transportations.view',
    ], function () {
        Route::get('/', 'index')->name('index');
        Route::get('json', 'indexJson')->name('json');
        Route::get('create', 'create')->name('create')->middleware('roles:1|transportations.create');
        Route::post('store', 'store')->name('store')->middleware('roles:1|transportations.create');
        Route::post('update', 'update')->name('update')->middleware('roles:1|transportations.edit');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('roles:1|transportations.edit');
        Route::get('{id}', 'show')->name('show');

        // Route::get('document-preview', 'docPreview')->name('document-preview');
        // Route::get('make-pdf', 'makePdf')->name('make-pdf');
        // Route::get('{id}/json', 'itemJson')->name('item.json');
        // Route::get('{id}/edit', 'edit')->name('edit')->middleware('roles:templates.edit');
        // Route::get('{id}', 'show')->name('show');
    });

    Route::get('transportation/json/{id}', [TransportationsController::class, 'itemJson']);

    // Route::resource('templates', TemplatesController::class)->middleware('roles:1|templates');

    // Legalisations
    Route::post('legalisation/{id}', [LegalisationsController::class, 'update'])
        ->middleware('roles:1|12');
    Route::resource('legalisation', LegalisationsController::class)
        ->middleware('roles:1|12');

});
