<div id="status-manage-popup-3" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Отказ</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-3" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-3" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-5" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Архив</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-5" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-5" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-7" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Выберите жилье</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label class="fs-5 fw-bold mb-2 align-middle d-flex gap-3" 
                            style="line-height: 1">
                                <input class="js-status-input js-own-housing-checkbox" type="checkbox" name="own_housing" value="1">
                                <span>Свое жилье</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="js-housing-container">
                    <div class="row mb-5">
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label for="select-housing-7" class="required fs-5 fw-bold mb-2">Жилье</label>
                                <select id="select-housing-7" class="js-status-input js-housing-id form-select form-select-sm form-select-solid" name="housing_id" required></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label for="select-housing-8" class="required fs-5 fw-bold mb-2">Комната</label>
                                <select id="select-housing-8" class="js-status-input js-housing-room-id form-select form-select-sm form-select-solid" name="housing_room_id" required></select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label for="status-manage-input-7" class="required fs-5 fw-bold mb-2">Дата начала проживания</label>
                                <input id="status-manage-input-7" class="js-status-input js-date-input form-control form-control-sm form-control-solid" type="text" name="residence_started_at" required>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-9" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Первый рабочий день</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="select-position-9" class="required fs-5 fw-bold mb-2">Должность</label>
                            <select id="select-position-9" class="js-status-input js-client-position-id-select form-select form-select-sm form-select-solid" name="client_position_id" required></select>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-9" class="required fs-5 fw-bold mb-2">Дата</label>
                            <input id="status-manage-input-9" class="js-status-input form-control form-control-sm form-control-solid" type="text" name="date" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-10" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Загрузить документ</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="fv-row">
                            <!--begin::Dropzone-->
                            <div class="js-status-dropzone dropzone">
                                <!--begin::Message-->
                                <div class="dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Загрузить документ</h3>
                                        <span class="fs-7 fw-bold text-gray-400">Перетащите документ сюда</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-10" class="required fs-5 fw-bold mb-2">Дата срока важности документа</label>
                            <input id="status-manage-input-10" class="js-status-input js-date-input form-control form-control-sm form-control-solid" type="text" name="date_of_documents_importance" tabindex="-1" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="select-doc-10" class="required fs-5 fw-bold mb-2">Тип документа</label>
                            <select id="select-doc-10" class="js-status-input js-doc-type-id form-select form-select-sm form-select-solid" name="doc_type_id" required></select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-11" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Выселение</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-11" class="required fs-5 fw-bold mb-2">Дата выселения</label>
                            <input id="status-manage-input-11" class="js-status-input js-date-input form-control form-control-sm form-control-solid" type="text" name="residence_stopped_at" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-13" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Архив отказ</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-13" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-13" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-14" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Перезвонить</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-14" class="required fs-5 fw-bold mb-2">Дата и время перезвона</label>
                            <input id="status-manage-input-14" class="js-status-input js-status-input-callback form-control form-control-sm form-control-solid" type="text" name="date" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-14" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-14" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-15" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Недозвон</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-16" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Оформление</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-16" class="required fs-5 fw-bold mb-2">Дата и время перезвона</label>
                            <input id="status-manage-input-16" class="js-status-input form-control form-control-sm form-control-solid" type="text" name="date" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-16" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-16" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-21" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Перезвонить</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-21" class="required fs-5 fw-bold mb-2">Дата и время перезвона</label>
                            <input id="status-manage-input-21" class="js-status-input js-status-input-callback form-control form-control-sm form-control-solid" type="text" name="date" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-21" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-21" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-22" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Не рекрутируем</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-22" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-22" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-lead-2" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Причины неликвида</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body js-status-details-wrap">
                <div class="row mb-5">
                    <div class="col">
                        <select class="js-status-input js-status-details-select form-select form-select-sm form-select-solid" name="details" required>
                            <option></option>
                            <option>Брак номера</option>
                            <option>Заявку не подавал</option>
                            <option>Не рекрутируем</option>
                            <option value="other">Прочее</option>
                        </select>
                    </div>
                </div>

                <div class="js-status-details-other js-hidden" style="display: none">
                    <div class="row mb-5">
                        <div class="col">
                            <label for="status-manage-textarea-lead-2" class="required fs-5 fw-bold mb-2">Комментарий</label>
                            <textarea id="status-manage-textarea-lead-2" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div id="status-manage-popup-lead-4" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Перезвонить</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="status-manage-input-lead-4" class="required fs-5 fw-bold mb-2">Дата и время перезвона</label>
                            <input id="status-manage-input-lead-4" class="js-status-input js-status-input-callback form-control form-control-sm form-control-solid" type="text" name="date" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col">
                        <label for="status-manage-textarea-lead-4" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="status-manage-textarea-lead-4" class="js-status-input form-control form-control-sm form-control-solid" cols="30" name="comment" required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-status-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-status-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ mix('/js/status-manage.js') }}"></script>