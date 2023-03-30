<script>
    $(document).ready(function() {
        const d = document
        let frm = document.getElementById('form_add')
        let modal_add_name = $("#add-category")
        let modal_add_body = modal_add_name.find('.modal-body')

        const selectRound = d.querySelectorAll('select.selectpicker')

        //  =========================
        //  JQUERY
        //  =========================
        // reset form
        $('.modal').on('hidden.bs.modal', function(e) {
            e.preventDefault()

            resetForm()
        })

        $(document).on('click', '.order_list .external-event', function(e) {

            let item_id = $(this).attr('data-id')
            $('#form_add').find('#method').val('edit')
            $('#form_add').find('#item_id').val(item_id)

            let booking_dateShow = ''
            if ($(this).attr('data-booking_dateShow') != "null") {
                booking_dateShow = $(this).attr('data-booking_dateShow')
            }

            let remark = ''
            if ($(this).attr('data-remark') != "null") {
                remark = $(this).attr('data-remark')
            }

            var agent_name = "";
            if ($(this).attr('data-agent_name') != "null") {
                agent_name = $(this).attr('data-agent_name');
            }
            var agent_contact = "";
            if ($(this).attr('data-agent_contact') != "null") {
                agent_contact = $(this).attr('data-agent_contact');
            }

            let data = []
            data.title = $(this).attr('data-title')
            data.agent_name = agent_name
            data.agent_contact = agent_contact
            data.totals = $(this).attr('data-totals')
            data.remark = remark
            data.payment_id = $(this).attr('data-payment_id')
            data.round_id = $(this).attr('data-round_id')
            data.booking_dateShow = booking_dateShow

            modal_add_name.modal({
                backdrop: "static",
            });

            modal_add_name
                .find("[name=customer]")
                .val(data.title)
                .end()
                .find("[name=agent_name]")
                .val(data.agent_name)
                .end()
                .find("[name=agent_contact]")
                .val(data.agent_contact)
                .end()
                .find("[name=totals]")
                .val(data.totals)
                .end()
                .find("[name=remark]")
                .val(data.remark)
                .end()
                .find("#payment")
                .val(data.payment_id)
                .trigger("change")
                .end()
                .find("#round")
                .val(data.round_id)
                .trigger("change")
                .end()
                .find("#booking_date")
                .val(data.booking_dateShow)
                .end()
            // .find(".modal-body .block_btn")
            // .prepend(form)
            // .end();
        })

        $(document).on('click', '.btn-del', function(e) {

            Swal.fire(
                swal_setConfirm("ยืนยันการลบ", "รายการนี้จะถูกลบออก")
            ).then((result) => {
                if (result.value) {

                    let form = $('#form_add')
                    let item_id = form.find('#item_id').val()

                    async_get_deleteEvent(item_id)
                        .then((resp) => {
                            updateCalendar();
                        })
                }
            })
        })

        async function async_get_deleteEvent(item_id = null) {
            if (item_id) {

                let url = new URL(path('delete_data'), domain)

                var data = new FormData()
                data.append('item_id', item_id)

                let method = {
                    'method': 'post',
                    'body': data
                }

                let response = await fetch(url, method)
                let result = await response.json()

                return result
            }
        }

        $(document).on('click', '.btn-cancel', function(e) {

            Swal.fire(
                swal_setConfirm("ยกเลิกจอง", "รายการจะไปอยู่ในส่วนรอ")
            ).then((result) => {
                if (result.value) {

                    let form = $('#form_add')
                    let item_id = form.find('#item_id').val()

                    async_get_cancelEvent(item_id)
                        .then((resp) => {
                            updateCalendar();
                        })
                }
            })
        })

        async function async_get_cancelEvent(item_id = null) {
            if (item_id) {
                let url = new URL(path('cancel_event'), domain)

                var data = new FormData()
                data.append('item_id', item_id)

                let method = {
                    'method': 'post',
                    'body': data
                }

                let response = await fetch(url, method)
                let result = await response.json()

                return result
            }
        }

        // calendar.init
        let calendar = window.jQuery.CalendarApp;

        calendar.onMove = function(eventObj, revertFunc) {
            var d = new Date(eventObj.start);
            var dateString = d.toDateString();

            Swal.fire(
                swal_setConfirm("ยืนยันการจอง", "จองรายการในวันที่ " + dateString)
            ).then((result) => {
                if (result.value) {

                    var currentDate = d;

                    var dataDate = currentDate.toJSON().slice(0, 10);

                    eventSubmit(eventObj.id, dataDate)
                        .then((resp) => {
                            if (resp.error) {
                                swalalert('error', resp.txt, {
                                    auto: false
                                })
                                revertFunc()
                            } else {
                                updateCalendar();
                            }
                        })
                } else {
                    revertFunc()
                }
            })
        }
        calendar.onDrop = function(eventObj, date) {
            var d = new Date(date);
            var dateString = d.toDateString();

            Swal.fire(
                swal_setConfirm("ยืนยันการจอง", "จองรายการในวันที่ " + dateString)
            ).then((result) => {
                if (result.value) {

                    var currentDate = new Date(date);

                    var dataDate = currentDate.toJSON().slice(0, 10);

                    eventSubmit(eventObj.attr("data-id"), dataDate)
                        .then((resp) => {
                            if (resp.error) {
                                swalalert('error', resp.txt, {
                                    auto: false
                                })
                            } else {
                                updateCalendar();
                            }
                        })
                }
            })
        }

        async function eventSubmit(item_id, date) {
            let url = new URL(
                "calendar/ctl_manage/update_bill_booking",
                window.origin
            );

            let data = new FormData();
            data.append("item_id", item_id);
            data.append("booking_date", date);

            let method = {
                'method': 'post',
                'body': data
            }

            let response = await fetch(url, method)
            let result = await response.json()

            return result
        }

        //  =========================
        //  Get Started
        //  =========================
        get_bill_wait()

        // $('#calendar').html(loading)

        frm.addEventListener('submit', function(e) {
            e.preventDefault()

            let form = $('#form_add')
            let form_method = form.find('#method')
            let item_id = form.find('#item_id').val()

            if (form_method.val() == 'edit') {
                update_data(item_id);
            } else {
                insert_data();
            }
        })

        //  =========================
        //  CRUD
        //  =========================

        // 
        //  Get data 
        //

        // 
        //  Insert data 
        //
        function insert_data() {
            modalLoading()

            var dataArray = $('#form_add').serializeArray(),
                len = dataArray.length

            let data = new FormData();
            for (i = 0; i < len; i++) {
                data.append(dataArray[i].name, dataArray[i].value);
            }

            if ($('#booking_date').val()) {
                var dateTypeVar = $('#booking_date').datepicker('getDate');
                data.append('booking_date', $.datepicker.formatDate('yy-mm-dd', dateTypeVar))
            }

            async_insert_data(data)
                .then((resp) => {
                    if (resp.error == 1) {
                        swalalert('error', resp.txt, {
                            auto: false
                        })

                        modalLoading_clear()
                    } else {

                        Swal.fire({
                            type: 'success',
                            title: 'สำเร็จ',
                            text: resp.txt,
                            timer: swal_autoClose,
                        }).then((result) => {

                            updateCalendar()

                        })
                    }
                });

        }

        async function async_insert_data(data = []) {
            let url = new URL(path('insert_data'), domain)

            let method = {
                'method': 'post',
                'body': data
            }

            let response = await fetch(url, method)
            let result = await response.json()

            return result
        }


        // 
        //  Update data 
        //
        function update_data() {
            modalLoading()

            var dataArray = $('#form_add').serializeArray(),
                len = dataArray.length

            let data = new FormData();
            for (i = 0; i < len; i++) {
                data.append(dataArray[i].name, dataArray[i].value);
            }

            if ($('#booking_date').val()) {
                var dateTypeVar = $('#booking_date').datepicker('getDate');
                data.append('booking_date', $.datepicker.formatDate('yy-mm-dd', dateTypeVar))
            }

            async_update_data(data)
                .then((resp) => {
                    if (resp.error == 1) {
                        swalalert('error', resp.txt, {
                            auto: false
                        })

                        modalLoading_clear()
                    } else {

                        Swal.fire({
                            type: 'success',
                            title: 'สำเร็จ',
                            text: resp.txt,
                            timer: swal_autoClose,
                        }).then((result) => {

                            updateCalendar();

                        })
                    }
                });

        }

        async function async_update_data(data = []) {
            let url = new URL(path('update_data'), domain)

            let method = {
                'method': 'post',
                'body': data
            }

            let response = await fetch(url, method)
            let result = await response.json()

            return result
        }


        //  =========================
        //  Function
        //  =========================
        const inputInt = d.querySelectorAll('input.int_only')
        inputInt.forEach(function(item, index) {
            item.addEventListener("keyup", function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            })
        })

        function resetForm() {
            // document.form_add.reset
            frm.reset();

            $(".selectpicker").selectpicker("refresh");

            var frm_method = $("#form_add").find("#method");
            frm_method.val("");

            modalLoading_clear()
        }

        function updateCalendar() {
            let s = window.jQuery.CalendarApp;
            $('#calendar').fullCalendar('destroy')
            s.init()

            get_bill_wait()
        }

        /**
         * get order booking status waite
         *
         * @return void
         */
        function get_bill_wait() {
            fetch_bill()
                .then((resp) => {

                    show_order(resp)

                    modal_close()
                })
        }
        async function fetch_bill() {
            let url = new URL(path('fetch_bill'), domain)

            let data = new FormData()
            data.append('complete',1) // 1 = waite
            // url.searchParams.append('complete', 1) // 1 = waite

            let body = {
                method:'post',
                body:data
            }

            let response = await fetch(url,body)
            let result = await response.json()

            return result
        }

        //  =========================
        //  HTML
        //  =========================
        //
        //  create order
        function payment_class(id = null) {
            let typeing
            switch (id) {
                case "4":
                    typeing = 'warning'
                    break
                case "5":
                    typeing = 'success'
                    break
            }

            return typeing
        }

        function create_dom_order(data = null) {
            let html = ''

            if (data) {
                let typeing = payment_class(data.PAYMENT_ID)

                // convert date to show on form input booking
                let booking_dateShow = null
                if (data.BOOKING_DATE || data.BOOKING_DATE != null) {
                    let dsplit = data.BOOKING_DATE.split("-")
                    booking_dateShow = dsplit[2] + "/" + dsplit[1] + "/" + dsplit[0]
                }

                let title_value = data.CUSTOMER_NAME
                if (data.TIME_START) {
                    title_value = data.TIME_START.slice(0, 5) + " " + data.CUSTOMER_NAME
                }

                html += `<div class="external-event bg-soft-${typeing} text-${typeing}" 
                data-class="bg-${typeing}" 
                data-id="${data.ID}"
                data-title="${data.CUSTOMER_NAME}"
                data-agent_name="${data.AGENT_NAME}"
                data-agent_contact="${data.AGENT_CONTACT}"
                data-totals="${data.TOTALS}"
                data-payment_id="${data.PAYMENT_ID}"
                data-booking_date="${data.BOOKING_DATE}"
                data-booking_dateShow="${booking_dateShow}"
                data-remark="${data.REMARK}"
                data-round_id="${data.ROUND_ID}"
                data-time_start="${data.TIME_START}"  
                data-time_end="${data.TIME_END}" >
                        <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>${title_value}
                    </div>`
            }


            return html
        }

        //
        //  show order
        function show_order(data = null) {
            let html = ''
            if (data) {
                data.forEach(function(item, index) {
                    html += create_dom_order(item)

                })

                // d.getElementById('external-events').insertAdjacentHTML('beforeend', html);
                d.getElementsByClassName('order_list')[0].innerHTML = html

                window.jQuery.CalendarApp.enableDrag()
            }
        }

        //  =========================
        //  Modal
        //  =========================
        // 
        //  Modal loading
        function modalLoading() {
            if (modal_add_body.length) {
                modal_add_body.append(loading)
                modal_add_name.find('.modal-body form').addClass('d-none')
            }
        }

        // 
        //  Modal loading close
        function modalLoading_clear() {
            if (modal_add_body.length) {
                modal_add_name.find('.loading').remove()
                modal_add_name.find('.modal-body form').removeClass('d-none')
            }
        }

        // Modal force to close
        function modal_close() {
            $('.modal').modal('hide')
        }
    })
</script>