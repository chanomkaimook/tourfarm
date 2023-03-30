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

        //  fetch data
        //
        let urlname = new URL(path('get_data'), domain);

        datatable.DataTable({
            ajax: {
                url: urlname,
                type: 'get',
                dataType: 'json'
            },
            order: [
                [last_defaultSort, 'desc']
            ],
            columns: [{
                    "data": "NAME",
                },
                {
                    "data": {
                        _: 'TIME_START.display',
                        sort: 'TIME_START.data',
                    },
                },
                {
                    "data": {
                        _: 'TIME_END.display',
                        sort: 'TIME_END.data',
                    },
                },
                {
                    "data": "STATUS_OFFVIEW"
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
                    "data": "NAME",
                    "width": "150px",
                    "orderable": false
                }
            ],
            "createdRow": function(row, data, index) {
                let table_action =
                    `
                <button type="button" class="btn btn-primary btn_edit_item" data-id="${data['ID']}" data-toggle="modal" data-target="#modal_from">แก้ไข</button>
                <button type="button" class="btn btn-danger btn_delete_item" data-id="${data['ID']}">ลบ</button>
                `
                $('td', row).eq(last_columntable).html(table_action)
            },


            dom: datatable_dom,
            buttons: datatable_button,
        })


        $(document).on('submit', '#dataform', function(e) {
            e.preventDefault

            if (method.val() == 'insert') {
                insert_data()
            } else {
                update_data()
            }

            return false;

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
            let url = new URL(path('get_dataItem'), domain);
            url.searchParams.append('id', $(this).attr('data-id'));
            // let url = new URL('admin/ctl_user/get_user?id=' + $(this).attr('data-id'), domain);
            fetch(url)
                .then(res => res.json())
                .then((resp) => {
                    modal_input_data(resp.data)

                    method.val('edit')
                    frm.find('.btn_submit').text('บันทึก')

                    $("#hidden_id").val($(this).attr('data-id'))
                });
        })

        //
        // reset form
        $('#modal_from').on('hidden.bs.modal', function(e) {
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

            var dataArray = frm.serializeArray(),
                len = dataArray.length

            let url = new URL(path('insert_data'), domain);

            let data = new FormData();
            for (i = 0; i < len; i++) {
                data.append(dataArray[i].name, dataArray[i].value);
            }

            fetch(url, {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
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

        //
        //  Update data 
        //
        function update_data() {
            modalLoading()

            var dataArray = frm.serializeArray(),
                len = dataArray.length

            let data_hidden_id = $("#hidden_id").val();

            let url = new URL(path('update_data'), domain);

            var data = new FormData()
            for (i = 0; i < len; i++) {
                data.append(dataArray[i].name, dataArray[i].value);
            }

            data.append('item_id', data_hidden_id)


            // statusoff
            if (frm.find('[data-toggle=toggle]').length) {
                data.append("item_statusoff", $('#item_statusoff').prop('checked'));
            }

            let option = {
                method: 'POST',
                body: data,
            }
            fetch(url, option)
                .then(res => res.json())
                .then((resp) => {

                    if (resp.error != 0) {
                        swalalert('error', resp.txt, {
                            auto: false
                        })

                        modalLoading_clear()
                    } else {
                        dataReload()
                    }

                })
        }

        //
        //  Delete data 
        //
        $(document).on('click', '.btn_delete_item', function() {
            $("#hidden_id").val($(this).attr('data-id'))
            let hidden_id = $("#hidden_id").val()

            let table_tr = $('.btn_delete_item[data-id=' + hidden_id + ']').parents('tr')
            let dataname = table_tr.children('td').eq(0).text()

            Swal.fire(swal_setConfirm('ยืนยันการลบ', 'คุณต้องการลบข้อมูลนี้ ' + dataname + '')).then((result) => {
                if (result.value) {
                    confirm_delete(hidden_id)
                }
            })
        })

        //===================================================================

        //
        // Function
        //

        //  reset form
        function resetForm() {
            frm.trigger('reset')
        }

        //  datatable reload
        function dataReload() {
            datatable.DataTable().ajax.reload(null, false);

            $(".modal").modal('hide');

            updateSystem()
        }


        function modal_input_data(data = []) {
            modal_name.find("#item_name").val(data.NAME)
            modal_name.find("#time_start").val(data.TIME_START)
            modal_name.find("#time_end").val(data.TIME_END)

            create_form_edit(data);
        }

        function create_form_edit(data = null) {
            let html = "";
            let dom_toggle = $('input[data-toggle=toggle')
            if (data) {

                html += create_dom_statusoff();
                modal_body.find('.html_statusoff').html(html)

                if (data.STATUS_OFFVIEW) {
                    $('input[data-toggle=toggle').bootstrapToggle('off')
                } else {
                    $('input[data-toggle=toggle').bootstrapToggle()
                }
            }
        }

        function confirm_delete(id = null) {
            modalLoading()

            if (id) {
                let url = new URL(path('delete_data'), domain);

                var delete_data = new FormData();
                delete_data.append('item_id', id);
                fetch(url, {
                        method: 'POST',
                        body: delete_data
                    })
                    .then(res => res.json())
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
        function create_dom_statusoff() {
            let result = `
                        <div class="form-group row">
                            <div class="col-12">
                                <input id="item_statusoff" type="checkbox" checked data-toggle="toggle" 
                                data-width="100"
                                data-on="แสดง" data-off="ซ่อน"
                                data-onstyle="success" data-offstyle="danger"
                                >
                            </div>
                        </div>`

            return result
        }

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