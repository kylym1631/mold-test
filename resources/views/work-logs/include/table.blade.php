<div id="vue-app">
    @php
    $days = json_encode(['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], JSON_UNESCAPED_UNICODE);
    $client_ids = json_encode(request()->has('id') 
        ? [request()->get('id')] 
        : (isset($client_ids) ? $client_ids : []));
    @endphp

    <worklog-table 
        client-ids="{{$client_ids}}" 
        days-of-week="{{$days}}"
        user-role="{{Auth::user()->group_id}}"
    ></worklog-table>
</div>