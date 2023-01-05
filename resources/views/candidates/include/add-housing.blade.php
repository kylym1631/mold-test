<hr>

<input type="hidden" name="is_housing_block" value="1">

@if ($view == 'tabs')
<div class="card-header mb-5" style="padding: 0;">
    <div class="card-title m-0">
        <h4 class="fw-bolder m-0">Настройки жилья</h4>
    </div>
</div>
@else
<div class="card-header mb-5" style="padding: 0;">
    <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Жилье</h3>
    </div>
</div>    
@endif

<div class="row mb-10">
    <div class="col">
        <div class="d-flex flex-column mb-0 fv-row form-check form-check-solid">
            <label class="fs-5 d-flex align-items-center gap-3" 
            style="line-height: 1">
                <input class="js-own-housing-checkbox form-check-input float-none" type="checkbox" name="own_housing" value="1" @checked($candidate && $candidate->own_housing)>
                Выселен
            </label>
        </div>
    </div>
</div>

<div class="js-housing-container"
@if ($candidate && $candidate->own_housing)
style="display: none"   
@endif>
    <div class="row mb-5">
        <div class="col">
            <div class="d-flex flex-column mb-0 fv-row">
                <label for="select-housing-77" class="@if(Request::is('*candidate/view*')) required @endif fs-5 fw-bold mb-2">Жилье</label>
                <select id="select-housing-77" class="js-status-input js-housing-id form-select form-select-sm form-select-solid" name="housing_id">
                    @if ($candidate && $candidate->housing_id)
                    <option value="{{ $candidate->housing_id }}" selected>{{ $candidate->Housing->title .' '. $candidate->Housing->address }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col">
            <div class="d-flex flex-column mb-0 fv-row">
                <label for="select-housing-88" class="@if(Request::is('*candidate/view*')) required @endif fs-5 fw-bold mb-2">Комната</label>
                <select id="select-housing-88" class="js-status-input js-housing-room-id form-select form-select-sm form-select-solid" name="housing_room_id">
                    @if ($candidate && $candidate->housing_room_id && $candidate->Housing_room)
                    <option value="{{ $candidate->housing_room_id }}" selected>{{ $candidate->Housing_room->number }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col">
            <div class="d-flex flex-column mb-0 fv-row">
                <label for="status-manage-input-10" class="@if(Request::is('*candidate/view*')) required @endif fs-5 fw-bold mb-2">Дата начала проживания</label>
                <input id="status-manage-input-10" class="js-status-input js-date-input form-control form-control-sm form-control-solid" type="text" name="residence_started_at" 
                @if ($candidate && $candidate->residence_started_at) 
                value="{{\Carbon\Carbon::parse($candidate->residence_started_at)->format('d.m.Y')}}"
                @endif>
            </div>
        </div>
    </div>
</div>