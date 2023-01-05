<div id="position-edit-popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Редактировать должность</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col-6">
                        <label for="position-manage-input-1" class="required fs-5 fw-bold mb-2">Начало</label>
                        <input id="position-manage-input-1" class="js-input js-date-position form-control form-control-sm form-control-solid" type="text" name="start_at" required>
                    </div>
                    <div class="col-6">
                        <div class="at-end-wrap">
                            <label for="position-manage-input-2" class="required fs-5 fw-bold mb-2">Окончание</label>
                            <input id="position-manage-input-2" class="js-input js-date-position form-control form-control-sm form-control-solid" type="text" name="end_at" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="js-cancel-btn btn btn-light btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="js-save-btn btn btn-primary btn-sm">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ mix('/js/positions-manage.js') }}"></script>