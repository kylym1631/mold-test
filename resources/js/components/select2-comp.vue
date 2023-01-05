<script>
export default {
    name: 'Select2Comp',
    props: ['placeholder', 'modelValue', 'ajaxEndpoint'],
    mounted() {
        const _this = this;
        const opt = {
            placeholder: this.placeholder,
        };

        if (this.ajaxEndpoint) {
            opt.ajax = this.getAjaxOptions();
        }

        $(this.$el)
            .select2(opt)
            .val(this.modelValue)
            .trigger("change")
            .on("change", function () {
                _this.selChanged($(this).val());
            });
    },
    watch: {
        modelValue(value) {
            $(this.$el)
                .val(value)
                .trigger('change.select2');

            this.$emit('change');
        },
        placeholder() {
            const opt = {
                placeholder: this.placeholder,
            };

            if (this.ajaxEndpoint) {
                opt.ajax = this.getAjaxOptions();
            }

            $(this.$el)
                .empty()
                .select2(opt);
        }
    },
    destroyed: function () {
        $(this.$el)
            .off()
            .select2("destroy");
    },
    methods: {
        selChanged(val) {
            this.$emit('update:modelValue', val);
        },
        getAjaxOptions() {
            return {
                url: this.ajaxEndpoint,
                dataType: 'json',
                data: function (params) {
                    return {
                        f_search: params.term,
                    };
                },
                processResults: (data) => {
                    var results = [];
                    $.each(data, function (index, item) {
                        results.push({
                            id: item.id,
                            text: item.value,
                        });
                    });
                    return {
                        results: results
                    };
                }
            };
        },
    },
}
</script>

<template>
    <select class="form-select form-select-sm form-select-solid">
        <slot></slot>
    </select>
</template>