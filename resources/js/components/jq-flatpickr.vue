<script>
export default {
    name: 'JqFlatpickr',
    props: ['type', 'date'],
    mounted() {
        let conf = {};

        if (this.type == 'datetime') {
            conf = {
                enableTime: true,
                time_24hr: true,
                altInput: true,
                altFormat: "d.m.Y H:i",
                dateFormat: "Y-m-d H:i",
                locale: {
                    firstDayOfWeek: 1
                },
            };
        } else if (this.type == 'time') {
            conf = {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: 8,
            };
        } else {
            conf = {
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                locale: {
                    firstDayOfWeek: 1
                },
            };
        }

        conf.onChange = () => {
            this.$emit('change-date', this.$refs.input.value);
        }

        this.fp = flatpickr(this.$refs.input, conf);
    }
}
</script>

<template>
    <input
        tabindex="-1"
        class="form-control form-control-sm form-control-solid"
        ref="input"
        type="text"
        :value="date"
    >
</template>