<hr>
<div class="card-header mb-5" style="padding: 0;">
    <div class="card-title m-0">
        @if (Auth::user()->isKoordinator())
        <h4 class="fw-bolder m-0">Настройки трудоустройство</h4>
        @else
        <h3 class="fw-bolder m-0">Трудоустройство</h3>
        @endif
    </div>
</div>

@if (Auth::user()->isKoordinator())
<div class="row">
    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Должность</label>
            <select id="client_position_id" name="client_position_id" class="form-select form-select-sm form-select-solid">
                @if($candidate->Client_position != null)
                <option value="{{ $candidate->Client_position->id }}">{{ $candidate->Client_position->title }}</option>
                @endif
            </select>
        </div>
    </div>
</div>

@else

<div class="row mb-5">
    @if(Auth::user()->group_id == 1)
    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Статус трудоустройства</label>
            <select id="real_status_work_id" class="form-select  form-select-sm form-select-solid"> </select>
        </div>
    </div>
    @endif

    @if(Auth::user()->group_id == 5)
    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Статус</label>
            {!! $select_active !!}
        </div>
    </div>
    @endif
</div>

<div class="row mb-5">
    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Клиент</label>
            <select id="client_id" name="client_id" class="form-select form-select-sm form-select-solid">
                @if($candidate->Client != null)
                <option value="{{ $candidate->Client->id }}">{{ $candidate->Client->name }}</option>
                @endif
            </select>
        </div>
    </div>
    
    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Должность</label>
            <select id="client_position_id" name="client_position_id" class="form-select form-select-sm form-select-solid">
                @if($candidate->Client_position != null)
                <option value="{{ $candidate->Client_position->id }}">{{ $candidate->Client_position->title }}</option>
                @endif
            </select>
        </div>
    </div>

    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">PESEL</label>
            <input id="pesel"
                @if($candidate != null) value="{{$candidate->pesel}}" @endif
                class="form-control form-control-sm form-control-solid"
                type="text" name="pesel">
        </div>
    </div>

    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Номер банковского счета</label>
            <input id="account_number"
                @if($candidate != null) value="{{$candidate->account_number}}" @endif
                class="form-control form-control-sm form-control-solid"
                type="text" name="account_number">
        </div>
    </div>

    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Имя матери</label>
            <input id="mothers_name"
                @if($candidate != null) value="{{$candidate->mothers_name}}" @endif
                class="form-control form-control-sm form-control-solid"
                type="text" name="mothers_name">
        </div>
    </div>

    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Имя отца</label>
            <input id="fathers_name"
                @if($candidate != null) value="{{$candidate->fathers_name}}" @endif
                class="form-control form-control-sm form-control-solid"
                type="text" name="fathers_name">
        </div>
    </div>

    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class=" fs-5 fw-bold mb-2">Адрес</label>
            <textarea id="address"
                    name="address"
                      class="form-control form-control-sm form-control-solid"
                      cols="20"
                      rows="6"> @if($candidate != null){{$candidate->address}}@endif</textarea>
        </div>
    </div>

    <div class="col-6">
        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Индекс</label>
            <input id="zip"
                @if($candidate != null) value="{{$candidate->zip}}" @endif
                class="form-control form-control-sm form-control-solid"
                type="text" name="zip">
        </div>

        <div class="d-flex flex-column mb-5 fv-row">
            <label class="fs-5 fw-bold mb-2">Город</label>
            <input id="city"
                @if($candidate != null) value="{{$candidate->city}}" @endif
                class="form-control form-control-sm form-control-solid"
                type="text" name="city">
        </div>
    </div>

</div>

@endif