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

        // select complete
        $('#item_doc_complete').val(1)
        $('#hidden_doc_complete').val(1)

        //  fetch data
        //
        let urlname = new URL(path('get_data'), domain);

        let t = datatable.DataTable({
            ajax: {
                url: urlname,
                type: 'get',
                dataType: 'json',
                data: function(d) {
                    d.item_filter_complete = $('#hidden_doc_complete').val(),
                        d.hidden_datestart = $('#hidden_datestart').val(),
                        d.hidden_dateend = $('#hidden_dateend').val()
                }
            },
            order: [
                [last_defaultSort, 'desc']
            ],
            columns: [{
                    "data": "COMPLETE_ALIAS",
                },
                {
                    "data": "ITEM"
                },
                {
                    "data": "TOTAL"
                },
                {
                    "data": "DOC_TYPE_TEXT"
                },
                {
                    "data": "NODE_NAME"
                },
                {
                    "data": "REMARK"
                },
                {
                    "data": "CREATER"
                },
                {
                    "data": {
                        _: 'DATE_STARTS.display',
                        sort: 'DATE_STARTS.timestamp',
                    },
                    "searchable": false,
                },
                {
                    "data": "CODE",
                    "width": "90px",
                    "orderable": false
                }
            ],
            "createdRow": function(row, data, index) {
                let itemDate = new Date(data.DATE_STARTS.date_starts).toJSON().slice(0, 10)

                if (itemDate >= data['DATECUT']) {

                    let table_action_comp = ''
                    let table_action_del = ''
                    let table_action = ''
                    if (data['COMPLETE_ID'] == 2) {
                        table_action = 'ครบรายการ'
                    } else if (data['COMPLETE_ID'] == 3) {

                        let table_action_restore = ''

                        if (data['TOTAL_RECEIVED_ONLY'] < data['TOTAL_ONLY']) {
                            table_action_restore = `
                                <button type="button" class="btn btn-secondary btn_restore_item" data-id="${data['ID']}">ย้อน</button>
                                `
                        }

                        table_action = 'ตัดออก' + table_action_restore
                    } else {
                        table_action_comp = `
                        <button type="button" class="btn btn-primary btn_edit_item" data-id="${data['ID']}" data-toggle="modal" data-target="#modal_from">ยืนยัน</button>
                        `
                        let table_action_del = `
                        <button type="button" class="btn btn-danger btn_delete_item" data-id="${data['ID']}">ตัด</button>
                        `
                        table_action = table_action_comp + table_action_del
                    }

                    $('td', row).eq(last_columntable).html(table_action)
                } else {
                    table_action = ''
                    if (data['COMPLETE_ID'] != 3) {
                        
                        let table_action =
                        `
                        <button type="button" class="btn btn-danger btn_delete_item" data-id="${data['ID']}">ตัด</button>
                        `
                    }
                    
                    $('td', row).eq(last_columntable).html(table_action)

                }
            },
            "rowCallback": function(row, data, index) {

                let textClass = ''
                switch (data.COMPLETE_ALIAS) {
                    case 'รับเข้า':
                        textClass = 'text-primary'
                        break;
                    case 'ขาย':
                        textClass = 'text-success'
                        break;
                    case 'สโตร์':
                        textClass = 'text-warning'
                        break;
                    case 'สูญเสีย':
                        textClass = 'text-danger'
                        break;
                    default:
                        break;
                }

                $('td', row).eq(0).addClass(textClass);
            },


            dom: datatable_dom,
            buttons: datatable_button,
        })


        // #
        // add row datatable
        function format(d) {
            // `d` is the original data object for the row
            let html = ''

            let table_td = ''

            if (d) {
                d.forEach(function(item, index) {
                    let btn_del = `<button type="button" class="btn btn-danger btn-sm btn_delete_item_list" data-id="${item.ID}">ลบ</button>`

                    table_td += '<tr>' +
                        '<td> ' + item.TOTAL + ' </td>' +
                        '<td> ' + item.USERNAME + ' </td>' +
                        '<td> ' + item.DATE_STARTS_TEXT + ' </td>' +
                        '<td> ' + item.REMARK + ' </td>' +
                        '<td> ' + btn_del + ' </td>' +
                        '</tr>'

                })
            }

            html += '<table class="table-bordered table-detail bg-white" border="0" >' +
                '<thead>' +
                '<tr>' +
                '<th> จำนวน </th>' +
                '<th> โดย </th>' +
                '<th> วันที่ </th>' +
                '<th> หมายเหตุ </th>' +
                '<th> action </th>' +
                '</tr>' +
                '</thead>' +

                '<tbody>' + table_td + '</tbody>' +
                '</table>'

            return html
        }

        let tableDetail_id = 0;
        // Automatically add a first row of data
        $('#datatable tbody').on('click', 'tr[role=row] td:not(td:last-child)', function() {

            var tr = $(this).closest('tr');
            var row = t.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }else{
                let data_detail = fetchDetail(row.data().ID)
                    .then(res => res.json())
                    .then((resp) => {
                        row.child(format(resp.data)).show()
                        tr.addClass('shown')

                        row.child().addClass('bg-light')
                    })
            }
            /* if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            } */
        });

        /** 
         * id = table id
         * 
         */
        async function fetchDetail(id = null) {
            let url = new URL(path('get_datadetail'), domain)
            url.searchParams.append('id', id)

            let result = await fetch(url);

            return result;
        }
        // ##
        // #

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
        // button restore
        $(document).on('click', '.btn_restore_item', function() {
            let url = new URL(path('restore_dataItem'), domain);
            url.searchParams.append('id', $(this).attr('data-id'));

            fetch(url)
                .then(res => res.json())
                .then((resp) => {
                    dataReload()
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

        //
        //  Update data 
        //
        function update_data() {
            modalLoading()

            let data_hidden_id = $("#hidden_id").val();

            let url = new URL(path('update_data'), domain);

            var data = new FormData()
            data.append('item_id', data_hidden_id)
            data.append('item_total', $("#item_total").val())
            data.append('remark', $("#remark").val())

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
                        Swal.fire({
                            type: 'success',
                            title: 'สำเร็จ',
                            text: resp.txt,
                            timer: swal_autoClose,
                        }).then((result) => {

                            dataReload()

                        })
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
            let dataname = table_tr.children('td').eq(1).text()

            Swal.fire(swal_setConfirm('ยืนยันการตัด', 'ข้อมูลนี้จะถูกตัดยอดออก ' + dataname + '')).then((result) => {
                if (result.value) {
                    confirm_delete(hidden_id)
                }
            })
        })

        $(document).on('click', '.btn_delete_item_list', function() {

            Swal.fire(swal_setConfirm('ยืนยันการลบ', 'ข้อมูลนี้จะถูกลบ ')).then((result) => {
                if (result.value) {
                    confirm_delete_list($(this).attr('data-id'))
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

        // return path
        function path(name = null) {
            let pathname = window.location.pathname;
            if (name) {
                pathname = pathname + '/' + name
            }

            return pathname
        }

        //  datatable reload
        function dataReload() {
            datatable.DataTable().ajax.reload(null, false);

            $(".modal").modal('hide');

            updateSystem()
        }


        function modal_input_data(data = []) {
            modal_name.find("#item_total").val(data.TOTAL)
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

        function confirm_delete_list(id = null) {
            modalLoading()

            if (id) {
                let url = new URL(path('delete_data_list'), domain);

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