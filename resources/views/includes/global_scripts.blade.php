<script>
    var hostUrl = "{{url('/')}}/assets/";
    var routes = {
        ping: '{{route("ping")}}',
        tasksJson: '{{route("tasks.json")}}',
    };
    var userId = '{{Auth::user()->id}}';
    var group = '{{Auth::user()->group_id}}';
    var permissions = '@json(Auth::user()->getPermissions())';
</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{url('/')}}/assets/plugins/global/plugins.bundle.js"></script>
<script src="{{url('/')}}/assets/js/scripts.bundle.js"></script>
<script src="{{ mix('/js/modules.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>
<!--end::Global Javascript Bundle-->