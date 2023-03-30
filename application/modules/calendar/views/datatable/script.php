<script>
    $(document).ready(function() {

        let datatable = $('#datatable')
        let last_columntable = datatable.find('th').length - 1
        let last_defaultSort = last_columntable - 1
        let frm = $('#dataform')
        let method = frm.find('input#method')
        let btn_submit = frm.find('.btn_submit')
        let modal_name = $("#modal_from")
        let modal_body = modal_name.find('.modal-body')

        let prefix_url = 'calendar/ctl_manage/'

        //  fetch data
        //
        let urlname = new URL(prefix_url + '/get_data', domain);

        datatable.DataTable({
            ajax: {
                url: urlname,
                type: 'get',
                dataType: 'json',
                data: function(d) {
                    d.hidden_payment = $('#hidden_payment').val(),
                    d.hidden_datestart = $('#hidden_datestart').val(),
                    d.hidden_dateend = $('#hidden_dateend').val()
                },
            },
            order: [
                [last_defaultSort, 'desc']
            ],



            columns: [{
                    "data": "NAME",
                    "width": "150px",
                    "orderable": false
                },
                {
                    "data": "NAME"
                },
                {
                    "data": {
                        _: 'BOOKING_DATE.display',
                        sort: 'BOOKING_DATE.timestamp',
                    },
                },
                {
                    "data": "ROUND_NAME"
                },
                {
                    "data": "PAYMENT_ALIAS"
                },
                {
                    "data": "AGENT_NAME"
                },
                {
                    "data": "AGENT_CONTACT"
                },
                {
                    "data": "TOTALS"
                },
                {
                    "data": "CREATER"
                },
                {
                    "data": {
                        _: 'DATE_STARTS.display',
                        sort: 'DATE_STARTS.timestamp',
                    },
                    "width": "90px",
                    "searchable": false,
                },
                {
                    "data": "REMARK"
                }
            ],
            "createdRow": function(row, data, index) {

                let booking_dateShow = null
                if (data.BOOKING_DATE.data && data.BOOKING_DATE.data != null) {
                    let dsplit = data.BOOKING_DATE.data.split("-")
                    booking_dateShow = dsplit[2] + "/" + dsplit[1] + "/" + dsplit[0]
                }

                let table_action =
                    `
                <button type="button" class="btn btn-primary btn_edit_item" 
                data-id="${data['ID']}" 
                data-booking_dateShow="${booking_dateShow}" 
                data-remark="${data['REMARK']}" 
                data-agent_name="${data['AGENT_NAME']}" 
                data-agent_contact="${data['AGENT_CONTACT']}" 
                data-title="${data['NAME']}" 
                data-totals="${data['TOTALS']}" 
                data-payment_id="${data['PAYMENT_ID']}" 
                data-round_id="${data['ROUND_ID']}" 
                data-toggle="modal" data-target="#add-category">แก้ไข</button>
                <button type="button" class="btn btn-danger btn_delete_item" data-id="${data['ID']}">ลบ</button>
                `
                $('td', row).eq(0).html(table_action)
            },
            "rowCallback": function(row, data, index) {

                let textClass = ''
                switch (data.PAYMENT_ALIAS) {
                    case 'รอโอน':
                        textClass = 'text-warning'
                        break;
                    case 'โอนแล้ว':
                        textClass = 'text-success'
                        break;
                    default:
                        break;
                }

                $('td', row).eq(4).addClass(textClass);
            },


            dom: datatable_dom,
            buttons: datatable_button,
        })

        let frmadd = document.getElementById('form_add')
        frmadd.addEventListener('submit', function(e) {
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

        //
        // button add
        $(document).on('click', '.btn_add_item', function() {
            method.val('insert')
            btn_submit.text('เพิ่มรายการ')
        })

        //
        // button edit
        $(document).on('click', '.btn_edit_item', function() {

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

            $('#form_add')
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

        //
        // reset form
        $('.modal').on('hidden.bs.modal', function(e) {
            e.preventDefault()

            resetForm()
            modalLoading_clear()
        })


        //===================================================================

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

            let dateselect = ''
            if ($('#booking_date').val()) {
                let datesplit = $('#booking_date').val().split('/')
                dateselect = datesplit[2] + "-" + datesplit[1] + "-" + datesplit[0]
                data.append('booking_date', dateselect)
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

                            dataReload()

                        })
                    }
                });

        }

        async function async_insert_data(data = []) {
            let url = new URL(prefix_url + '/insert_data', domain);

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

            let dateselect = ''
            if ($('#booking_date').val()) {
                let datesplit = $('#booking_date').val().split('/')
                dateselect = datesplit[2] + "-" + datesplit[1] + "-" + datesplit[0]
                data.append('booking_date', dateselect)
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

                            dataReload()

                        })
                    }
                });
        }

        async function async_update_data(data = []) {
            let url = new URL(prefix_url + '/update_data', domain)

            let method = {
                'method': 'post',
                'body': data
            }

            let response = await fetch(url, method)
            let result = await response.json()

            return result
        }

        //
        //  Delete data 
        //
        $(document).on('click', '.btn_delete_item', function() {
            $("#item_id[type=hidden]").val($(this).attr('data-id'))
            let hidden_id = $("#item_id[type=hidden]").val()

            let table_tr = $('.btn_delete_item[data-id=' + hidden_id + ']').parents('tr')
            let dataname = table_tr.children('td').eq(1).text()

            Swal.fire(swal_setConfirm('ยืนยันการลบ', 'คุณต้องการลบข้อมูลนี้ ' + dataname + '')).then((result) => {
                if (result.value) {
                    confirm_delete(hidden_id)
                }
            })
        })

        async function async_get_deleteEvent(item_id = null) {
            if (item_id) {

                let url = new URL(prefix_url + 'delete_data', domain)

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

        //===================================================================

        //
        // Function
        //

        //  reset form
        function resetForm() {
            let formadd = $('#form_add')
            formadd.trigger('reset')

            $(".selectpicker").selectpicker("refresh");

            var frm_method = $("#form_add").find("#method");
            frm_method.val("");

            modalLoading_clear()
        }

        //  datatable reload
        function dataReload() {
            datatable.DataTable().ajax.reload(null, false);

            $(".modal").modal('hide');

            // updateSystem()
        }

        function confirm_delete(id = null) {
            modalLoading()

            if (id) {
                async_get_deleteEvent(id)
                    .then((resp) => {
                        if (resp.error == 0) {

                            dataReload()

                            swalalert('success')

                            modalLoading_clear()
                        } else {
                            swalalert('error', resp.txt, {
                                auto: false
                            })

                            modalLoading_clear()
                        }

                    });
            }

        }


        //===================================================================

        //
        //  HTML
        // 

        //
        // status_ff

        //===================================================================

        //
        // Modal
        //

        // 
        //  Modal loading
        function modalLoading() {
            if (modal_body.length) {
                modal_body.append(loading)
                modal_name.find('.modal-body form').addClass('d-none')
            }
        }

        // 
        //  Modal loading close
        function modalLoading_clear() {
            if (modal_body.length) {
                modal_name.find('.loading').remove()
                modal_name.find('.modal-body form').removeClass('d-none')
            }
        }
    })
</script>