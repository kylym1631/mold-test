<div id="arrivals-add-popup" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Добавить приезд</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="arrival-manage-select-2" class="required fs-5 fw-bold mb-2">Вид транспорта</label>
                            <select id="arrival-manage-select-2" class="js-input js-transport-id form-select form-select-sm form-select-solid" name="transport_id" required></select>
                        </div>
                        <div class="js-transportations-field mt-5" style="display: none">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <label for="transportation-id-select" class="required form-label">Регулярные перевозки</label>
                                <select id="transportation-id-select" name="transportation_id" class="js-transportions-id js-input form-select form-select-sm form-select-solid" required></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex flex-column mb-0 fv-row">
                            <label for="arrival-manage-select-1" class="required fs-5 fw-bold mb-2">Место</label>
                            <select id="arrival-manage-select-1" class="js-input js-place-arrive-id form-select form-select-sm form-select-solid" name="place_arrive_id" required></select>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-6">
                        <label for="arrival-manage-input-1" class="required fs-5 fw-bold mb-2">Дата и время</label>
                        <input id="arrival-manage-input-1" class="js-input js-date-arrive form-control form-control-sm form-control-solid" type="text" name="date_arrive" required>
                    </div>
                    <div class="col-6">
                        <label for="arrival-manage-textarea-1" class="required fs-5 fw-bold mb-2">Комментарий</label>
                        <textarea id="arrival-manage-textarea-1" class="js-input form-control form-control-sm form-control-solid" cols="20" name="comment" required></textarea>
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

<script src="{{ mix('/js/arrivals-manage.js') }}"></script>