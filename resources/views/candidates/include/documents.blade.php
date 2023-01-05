<div class="card mb-5 mb-xl-10 js-data-table-wrap" id="kt_profile_details_view">

    <div class="card-header cursor-pointer">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Документы</h3>
        </div>

        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <input type="hidden" name="candidate_id" value="{{ request()->get('id') }}" class="js-filter">

            <a href="/templates/?candidate_id={{ request()->get('id') }}&find_tpl=1" class="btn btn-primary btn-sm">Сгенерировать документ</a>
        </div>
    </div>

    <div class="card-body p-9 pb-0">
        <div class="table-responsive">
            <table class="js-data-table table align-middle table-row-dashed fs-6 gy-3"
                data-route="{{route('candidates.documents.json')}}" data-tpl="CandidateDocuments"
                id="CandidateDocumentsTable">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                        <th class="max-w-55px sorting_disabled">Id</th>
                        <th class="max-w-85px sorting_disabled">Название</th>
                        <th class="max-w-85px sorting_disabled">Дата создания</th>
                        <th class="max-w-85px sorting_disabled text-center">Скачать</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">

                </tbody>
                <!--end::Table body-->
            </table>
        </div>
    </div>

</div>
