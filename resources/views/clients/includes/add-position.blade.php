<div class="mt-10">
    <h3 class="fw-bolder m-0">Должности</h3>

    <div id="client-positions-container" class="mt-5">
        @if($client && $client->Positions)
        @foreach($client->Positions as $item)
        <div class="client-position">
            <div class="row mb-5">
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="required fs-5 fw-bold mb-2">Заголовок</label>
                        <input value="{{$item->title}}" data-id="{{$item->id}}"
                            class="form-control form-control-sm form-control-solid" type="text" name="title[]" />
                    </div>
                </div>
            </div>

            <h5 class="fw-bolder m-0">Ставки</h5>

            <div class="mt-5 position-rates-container">
                @if($item->Rates)
                <div class="row mb-5">
                    <div class="col">
                        <label class="required form-label mb-0">Тип ставки</label>
                    </div>
                    <div class="col">
                        <label class="required form-label mb-0">Начало действия</label>
                    </div>
                    <div class="col">
                        <label class="required form-label mb-0">Ставка</label>
                    </div>
                </div>
                @foreach($item->Rates as $rate)
                <div class="position-rate">
                    <div class="row mb-5">
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <select name="type[]" class="form-select  form-select-sm form-select-solid" disabled>
                                    <option 
                                    @selected($rate->type == 'rate')
                                    value="rate">Ставка</option>
                                    <option 
                                    @selected($rate->type == 'rate_after')
                                    value="rate_after">Ставка после 3 месяца</option>
                                    <option 
                                    @selected($rate->type == 'personal_rate')
                                    value="personal_rate">Ставка от клиента, brutto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <input
                                    class="js-rate-date form-control form-control-sm form-control-solid"
                                    type="text" name="start_at[]" 
                                    value="{{\Carbon\Carbon::parse($rate->start_at)->format('d.m.Y')}}" readonly />
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                <input
                                    class="form-control form-control-sm form-control-solid"
                                    type="text" name="amount[]" value="{{$rate->amount}}" data-id="{{$rate->id}}" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            @if($item->Rates->isEmpty())
            <p class="main-color js-no-rate-msg">Ставки не установлены</p>
            @endif

            <div class="row mt-5">
                <div class="col">
                    <button type="button" class="js-add-rate btn btn-warning btn-sm btn-xs">Добавить ставки</button>
                </div>
            </div>

            <div class="row mb-5 mt-5">
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="fs-5 fw-bold mb-2">{{__('Мин, нетто')}}</label>
                        <input value="{{$options['min_rate_netto']}}"
                            class="form-control form-control-sm form-control-solid" type="text" readonly />
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="fs-5 fw-bold mb-2">{{__('Мин, брутто')}}</label>
                        <input value="{{$options['min_rate_brutto']}}"
                            class="form-control form-control-sm form-control-solid" type="text" readonly />
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-6">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="fs-5 fw-bold mb-2">Описание</label>
                        <textarea name="description[]" class="form-control form-control-sm form-control-solid" cols="20"
                            rows="6">{{$item->description}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    <div class="row mt-5">
        <div class="col">
            <button type="button" id="add-client-position" class="btn btn-warning btn-sm">Добавить должность</button>
        </div>
    </div>
</div>

<script type="text/template" id="position-template">
    <div class="client-position">
        <div class="row mb-5">
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <label
                        class="required fs-5 fw-bold mb-2">Заголовок</label>
                    <input
                        class="form-control form-control-sm form-control-solid"
                        type="text" name="title[]" />
                </div>
            </div>
        </div>

        <h5 class="fw-bolder m-0">Ставки</h5>

        <div class="mt-5 position-rates-container">
            <div class="row mb-5">
                <div class="col">
                    <label class="required form-label mb-0">Тип ставки</label>
                </div>
                <div class="col">
                    <label class="required form-label mb-0">Начало действия</label>
                </div>
                <div class="col">
                    <label class="required form-label mb-0">Ставка</label>
                </div>
            </div>
            <!-- rates -->
        </div>

        <div class="row mb-5 mt-5">    
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <label
                            class="fs-5 fw-bold mb-2">{{__('Мин, нетто')}}</label>
                    <input value="{{$options['min_rate_netto']}}"
                        class="form-control form-control-sm form-control-solid"
                        type="text" readonly />
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <label
                            class="fs-5 fw-bold mb-2">{{__('Мин, брутто')}}</label>
                        <input value="{{$options['min_rate_brutto']}}"
                            class="form-control form-control-sm form-control-solid"
                            type="text" readonly />
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <div class="d-flex flex-column mb-0 fv-row">
                    <label class="fs-5 fw-bold mb-2">Описание</label>
                    <textarea
                        name="description[]"
                        class="form-control form-control-sm form-control-solid"
                        cols="20"
                        rows="6"></textarea>
                </div>
            </div>
            <div class="col-6">
                <button style="margin-top: 28px;" type="button" class="js-delete-client-position btn btn-light  btn-sm delete_contact">Удалить</button>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="rate-template">
    <div class="position-rate">
        <div class="row mb-5">
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <select  name="type[]" class="form-select  form-select-sm form-select-solid" disabled>
                        <option></option>
                        <option value="rate">Ставка</option>
                        <option value="rate_after">Ставка после 3 месяца</option>
                        <option value="personal_rate">Ставка от клиента, brutto</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <input
                        class="js-rate-date form-control form-control-sm form-control-solid"
                        type="text" name="start_at[]" />
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column mb-0 fv-row">
                    <input
                        class="form-control form-control-sm form-control-solid"
                        type="text" name="amount[]" />
                </div>
            </div>
        </div>
    </div>
</script>
