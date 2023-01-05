<div id="room-add-popup" class="js-room-popup modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Добавить комнату</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col-6">
                        <label for="add-room-input-1" class="required fs-5 fw-bold mb-2">Номер комнаты</label>
                        <input id="add-room-input-1" class="js-input form-control form-control-sm form-control-solid" type="text" name="number" required>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-6">
                        <label for="add-room-input-2" class="required fs-5 fw-bold mb-2">Кол-во спальных мест</label>
                        <input id="add-room-input-2" class="js-input form-control form-control-sm form-control-solid" type="text" name="places_count" required>
                    </div>
                    <div class="col-6">
                        <label for="add-room-input-3" class="fs-5 fw-bold mb-2">Заселено</label>
                        <input id="add-room-input-3" class="js-input form-control form-control-sm form-control-solid" type="text" name="filled_count" value="0" readonly>
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