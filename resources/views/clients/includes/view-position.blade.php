<div class="mt-10">
    <h3 class="fw-bolder m-0">Должность</h3>

    <div id="client-positions-container" class="mt-5">
        @if($client && $client->Positions)
        @foreach($client->Positions as $item)
        <div class="client-position">
            <div class="row mb-5">
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label
                            class="fs-5 fw-bold mb-2">Заголовок</label>
                            {{$item->title}}
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
                                {{\App\Models\ClientPositionRate::getTypeTitle($rate->type)}}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                {{\Carbon\Carbon::parse($rate->start_at)->format('d.m.Y')}}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column mb-0 fv-row">
                                {{$rate->amount}}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            <div class="row mb-5">    
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label
                            class="fs-5 fw-bold mb-2">{{__('Мин, нетто')}}</label>
                            {{$options['min_rate_netto']}}
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label
                            class="fs-5 fw-bold mb-2">{{__('Мин, брутто')}}</label>
                            {{$options['min_rate_brutto']}}
                    </div>
                </div>
            </div>

            @if ($item->description)
            <div class="row mb-5">
                <div class="col">
                    <div class="d-flex flex-column mb-0 fv-row">
                        <label class="fs-5 fw-bold mb-2">Описание</label>
                        {{$item->description}}
                    </div>
                </div>
            </div>    
            @endif
            
        </div>
        @endforeach
        @endif
    </div>
</div>