<div class="card">
    <div class="card-body">
        <div class="row py-5">
            <div class="col-xs-6">
                <div class="mb-10">
                    <label class="form-label">Ссылка-приглашение</label>
                    <div class="input-group">
                        <!--begin::Input-->
                        <input id="kt_clipboard_1" type="text" class="form-control" value="{{$invite_link}}">
                        <!--end::Input-->
                        <!--begin::Button-->
                        <button   class="btn btn-light-primary" data-clipboard-target="#kt_clipboard_1">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="currentColor"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="currentColor"></path>
                                        </svg>
                        </button>
                        <!--end::Button-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{url('/')}}/assets/plugins/custom/clipboard/clipboard.min.js"></script>

<script>
    // Select elements
    const target = document.getElementById('kt_clipboard_1');
    const button = target.nextElementSibling;

    // Init clipboard -- for more info, please read the offical documentation: https://clipboardjs.com/
    var clipboard = new ClipboardJS(button, {
        target: target,
        text: function() {
            return target.value;
        }
    });

    // Success action handler
    clipboard.on('success', function(e) {
        const currentLabel = button.innerHTML;

        // Exit label update when already in progress
        if(button.innerHTML === 'Copied!'){
            return;
        }

        // Update button label
        button.innerHTML = 'Copied!';

        // Revert button label after 3 seconds
        setTimeout(function(){
            button.innerHTML = currentLabel;
        }, 3000)
    });
</script>