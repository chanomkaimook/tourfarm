<script>
    $(document).ready(function() {

        let split = window.location.pathname.split("/")
        let pathname = window.location.origin + "/" + split[1] + "/" + split[2];

        let datatable = $('#datatable')
        let last_columntable = datatable.find('th').length - 1

        if (typeof split[3] == 'undefined') {
            last_columntable = 5
        }

        let last_defaultSort = last_columntable - 1
        let frm = $('#dataform')
        let frm_item = $('#dataitem')
        let method = frm.find('input#method')
        let btn_submit = $('.btn_submit')
        let modal_name = $("#modal_from")
        let modal_body = modal_name.find('.modal-body')

        let select2 = $('[data-toggle="select2"]')
        let section_btn_name = '#section_button'
        let section_doc_name = '#section_document'
        let section_doc_list = '#section_list'
        let section_btn = $(section_btn_name)
        let section_doc = $(section_doc_name)
        let section_list = $(section_doc_list)
        let frm_item_data = frm_item.find('.data_item')

        // inisialize select2
        select2.select2({
            placeholder: "รายชื่อ",
            allowClear: true
        })

        //
        //  fetch data temp
        fetch_datatemp()

        //  fetch data
        //
        let urlname = new URL(path('get_data'), domain);

        if (split[3] == "order") {
            urlname = new URL('stock/ctl_order/get_data', domain);
        }

        let t = datatable.DataTable({
            ajax: {
                url: urlname,
                type: 'get',
                dataType: 'json',
                data: function(d) {
                    d.item_filter_complete = $('#hidden_doc_complete').val(),
                        d.item_filter_node = $('#hidden_doc_supplier').val(),
                        d.item_filter_catagory = $('#item_doc_cat').val(),
                        d.hidden_datestart = $('#hidden_datestart').val(),
                        d.hidden_dateend = $('#hidden_dateend').val()
                },
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

                    let table_action_comp = `
                    <button type="button" class="btn btn-primary btn_edit_item" data-id="${data['ID']}" data-item_id="${data['ITEM_ID']}" data-table="${data['TABLE']}">แก้ไข</button>
                        `
                    let table_action_del = `
                    <button type="button" class="btn btn-danger btn_delete_item" data-id="${data['ID']}" data-item_id="${data['ITEM_ID']}" data-table="${data['TABLE']}">ลบ</button>
                        `
                    table_action = table_action_comp + table_action_del

                    if (data['TOTAL_RECEIVED_ONLY'] >= data['TOTAL_ONLY']) {
                        table_action = 'ครบรายการ'
                    }


                    $('td', row).eq(last_columntable).html(table_action)
                } else {
                    $('td', row).eq(last_columntable).html('')
                }

            },
            "rowCallback": function(row, data, index) {

                let textClass = textDocColor(data.COMPLETE_ALIAS)

                $('td', row).eq(0).addClass(textClass);
            },

            dom: datatable_dom,
            buttons: datatable_button,
            columnDefs: [{
                visible: false,
                targets: [3, 4]
            }, ],
        })

        //
        // text color
        function textDocColor(text) {
            switch (text) {
                case 'รับเข้า':
                    result = 'text-primary'
                    break;
                case 'ขาย':
                    result = 'text-success'
                    break;
                case 'เบิก':
                    result = 'text-warning'
                    break;
                case 'สูญเสีย':
                    result = 'text-danger'
                    break;
                case 'สั่งซื้อ':
                    result = 'text-purple'
                    break;
                default:
                    result = ''
                    break;
            }

            return result
        }

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

        // Automatically add a first row of data
        $('#datatable tbody').on('click', 'tr[role=row] td:not(td:last-child)', function() {

            var tr = $(this).closest('tr');
            var row = t.row(tr);

            // removeTrDetail()

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                let data_detail = fetchDetail(row.data().DOC_NODE_ID)
                    .then(res => res.json())
                    .then((resp) => {
                        row.child(format(resp.data)).show()
                        tr.addClass('shown')

                        row.child().addClass('bg-light')
                    })
            }

        });

        /* datatable
            .on('order.dt', function() {
                removeTrDetail()
            })
            .on('search.dt', function() {
                removeTrDetail()
            })
            .on('page.dt', function() {
                removeTrDetail()
                console.log('page')
            })
            .dataTable(); */

        function removeTrDetail() {
            if ($('tr').find('.table-detail').length) {
                $('tr .table-detail').parents('tr').remove()
                $('tr[role=row]').removeClass('shown');
            }
        }

        /** 
         * id = table id
         * 
         */
        async function fetchDetail(id = null) {
            let url = new URL('stock/ctl_docwaite/get_datadetail', domain)
            url.searchParams.append('id', id)

            let result = await fetch(url);

            return result;
        }
        // ##
        // #


        $(document).on('submit', '#dataitem', function() {
            // e.preventDefault

            let ele = $(this)

            // หากมีรายการที่รออยู่ต้องทำรายการนั้นให้จบก่อน
            if (!check_listTemp()) {

                return false
            }

            if (ele.find('[type=submit]').attr('data-edit') == 1) {

                update_data()

            } else {

                if (split[3] == "order") {
                    // insert_data_order()
                    insert_item('order')
                    return false
                }

                switch (method.val()) {
                    case 'add':
                        insert_item('import')
                        break;
                    case 'sale':
                        insert_item('bill')
                        break;
                    case 'cut':
                        insert_item('issue')
                        break;
                    case 'lost':
                        insert_item('lost')
                        break;

                    default:
                        break;
                }
            }


            return false;

        })

        //
        // update doc
        $(document).on('click', '#btn_update', function(e) {
            e.preventDefault

            console.log($(this))

            return false;

        })

        //
        // section show
        $('.button', section_btn_name).click(function(e) {
            e.preventDefault()

            // หากมีรายการที่รออยู่ต้องทำรายการนั้นให้จบก่อน
            if (!check_listTemp()) {

                return false
            }

            let ele = $(this)

            method.val(ele.attr('data-value'))

            if (ele.attr('data-value') != 'cut') {
                modal_name.find('#node_id').removeAttr('required')

                $('input#hold').attr('disabled', 'disabled')
            } else {
                modal_name.find('#node_id').attr('required', 'required')

                $('input#hold').removeAttr('disabled', 'disabled')
            }

            if (ele.attr('data-value') == 'lost') {
                modal_name.find('#remark').attr('required', 'required')
            } else {
                modal_name.find('#remark').removeAttr('required', 'required')
            }

            showSectionDoc()
        })
        $('button.close', section_doc_name).click(function(e) {
            e.preventDefault()
            // หากมีรายการที่รออยู่ต้องทำรายการนั้นให้จบก่อน
            if (!check_listTemp()) {

                return false
            }

            hideSectionDoc()
        })

        $(document).on('change', section_doc_name + ' select#item', function() {
            let ele = $(this)
            modal_show_item(ele.val())
        })

        $(document).on('click', '#dataitem input[name=customRadio]', function() {
            if ($('input#hold').prop('checked') == true) {
                modal_name.find('#node_id').attr('required', 'required')
            }
        })

        var delayTimer;
        $(document).on('keyup', section_doc_name + ' #item_search', function() {
            clearTimeout(delayTimer);
            let textsearch = $(this).val()
            delayTimer = setTimeout(function() {
                // Do the ajax stuff
                if (textsearch.trim() && textsearch.length >= 5) {

                    modal_show_itemFromBarcode(textsearch.trim())

                }
            }, 500);
        })

        $(document).on('click', '.btn_del_temp', function() {
            let data_id = $(this).attr('data-id')
            Swal.fire(swal_setConfirm('ยืนยันการลบ', 'ยกเลิกรายการนี้')).then((result) => {
                if (result.value) {
                    confirm_delete_itemTemp(data_id)
                }
            })
        })

        $(document).on('click', '.btn_clear', function() {
            Swal.fire(swal_setConfirm('ยืนยันการลบ', 'ยกเลิกรายการทั้งหมด')).then((result) => {
                if (result.value) {
                    confirm_clear()
                }
            })
        })

        //
        // button edit
        $(document).on('click', '.btn_edit_item', function() {
            let url = new URL(path('get_dataItem'), domain);
            url.searchParams.append('id', $(this).attr('data-id'));
            url.searchParams.append('item_id', $(this).attr('data-item_id'));
            url.searchParams.append('table', $(this).attr('data-table'));
            // let url = new URL('admin/ctl_user/get_user?id=' + $(this).attr('data-id'), domain);
            fetch(url)
                .then(res => res.json())
                .then((resp) => {

                    modal_show_list(resp.data.ITEM_ID, resp.data)

                    // set hidden value
                    $('#hidden_table_id').val($(this).attr('data-id'))
                    $('#hidden_table_name').val($(this).attr('data-table'))

                    buttonUpdate_show()

                    $("#hidden_id").val($(this).attr('data-item_id'))
                });
        })

        btn_submit.click(function() {
            Swal.fire(swal_setConfirm('ยืนยันการทำรายการ', 'รายการทั้งหมดจะเข้าคลังสินค้า')).then((result) => {
                if (result.value) {
                    insert_data()
                }
            })
        })


        //
        //  Delete data 
        //
        $(document).on('click', '.btn_delete_item', function() {
            // set hidden value
            $('#hidden_table_id').val($(this).attr('data-id'))
            $('#hidden_table_name').val($(this).attr('data-table'))
            $("#hidden_id").val($(this).attr('data-item_id'))

            Swal.fire(swal_setConfirm('ยืนยันการลบ', 'คุณต้องการลบข้อมูลนี้')).then((result) => {
                if (result.value) {
                    confirm_delete()
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

        //
        // reset form
        modal_name.on('hidden.bs.modal', function(e) {
            e.preventDefault()

            resetFormSearch()
            modalLoading_clear()
        })
        modal_name.on('shown.bs.modal', function(e) {
            e.preventDefault()
            let ele = $(this)
            ele.find('input#item_total').focus()
        })

        //===================================================================

        // 
        //  Get data 
        //
        function fetch_datatemp() {

            tempLoading()

            asyn_fetch_datatemp()
                .then((resp) => {
                    if (resp.length) {

                        showSectionList()

                        $('.data_temp_item').html(create_dom_itemtemp(resp))
                    }
                })
        }

        async function asyn_fetch_datatemp() {
            tempLoading()

            let url = new URL(path('fetch_datatemp'), domain)

            const response = await fetch(url);
            const result = await response.json();
            return result;
        }

        function get_docTemp() {
            let url = new URL(path('get_docTemp'), domain);

            fetch(url)
                .then(res => res.json())
                .then((resp) => {
                    create_dom_temp(resp)
                });
        }

        // 
        //  Insert data 
        //
        function insert_item(alias = null) {
            modalLoading()

            var dataArray = frm_item.serializeArray(),
                len = dataArray.length

            let url = new URL(path('insert_item'), domain);

            let data = new FormData();
            for (i = 0; i < len; i++) {
                data.append(dataArray[i].name, dataArray[i].value);
            }

            // check hold item
            if (frm_item.find('#hold[type=radio]').prop('checked')) {
                data.append('hold', 1);
            }

            if (alias) {
                data.append('alias', alias);
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

                        fetch_datatemp()

                        modal_hide_item()
                    }
                });

        }

        function insert_data() {
            tempLoading()

            let url = new URL(path('insert_data'), domain);

            fetch(url)
                .then(res => res.json())
                .then((resp) => {
                    if (resp.error != 0) {
                        swalalert('error', resp.txt, {
                            auto: false
                        })
                    } else {
                        swalalert('success')

                        if (split[3] == "order") {
                            hideSectionList()
                            dataReload()
                        } else {
                            hideSectionDoc()
                            hideSectionList()
                        }
                    }
                    tempLoading_clear()
                });

        }

        //
        //  Update data 
        //
        function update_data() {
            modalLoading()

            let table_id = $('#hidden_table_id').val()
            let item_id = $('#hidden_id').val()
            let table_name = $('#hidden_table_name').val()

            let url = new URL(path('update_data'), domain);

            var data = new FormData()
            data.append('table_id', table_id)
            data.append('item_id', item_id)
            data.append('table_name', table_name)
            data.append('total', modal_name.find('#item_total').val())
            data.append('remark', modal_name.find('#remark').val())

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
        function confirm_clear() {
            tempLoading()

            let url = new URL(path('clear_data'), domain);

            fetch(url)
                .then(res => res.json())
                .then((resp) => {
                    if (resp.error != 0) {
                        swalalert('error', resp.txt, {
                            auto: false
                        })
                    } else {
                        if (split[3] == 'order') {
                            hideSectionList()
                            dataReload()
                        } else {
                            hideSectionDoc()
                            hideSectionList()
                        }

                    }
                    tempLoading_clear()
                });

        }

        function confirm_delete_itemTemp(data_id = null) {

            if (data_id) {
                let url = new URL(path('delete_dataTemp'), domain);

                let data = new FormData()
                data.append('item_id', data_id)
                fetch(url, {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        if (resp.error != 0) {
                            swalalert('error', resp.txt, {
                                auto: false
                            })
                        } else {
                            asyn_fetch_datatemp()
                                .then((resp) => {
                                    if (resp.length) {

                                        $('.data_temp_item').html(create_dom_itemtemp(resp))

                                    } else {
                                        if (split[3] == 'order') {
                                            hideSectionList()
                                            dataReload()
                                        } else {
                                            hideSectionDoc()
                                            hideSectionList()
                                        }


                                    }
                                })

                        }

                    });
            }
        }

        function confirm_delete() {
            modalLoading()

            let id = $('#hidden_id').val()
            let table_id = $('#hidden_table_id').val()
            let table_name = $('#hidden_table_name').val()

            if (id) {
                let url = new URL(path('delete_data'), domain);

                var delete_data = new FormData();
                delete_data.append('item_id', id);
                delete_data.append('table_id', table_id);
                delete_data.append('table_name', table_name);

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
                let url = new URL('stock/ctl_docwaite/delete_data_list', domain);

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

        async function fetch_dataItem(id = null) {

            let url = new URL(path('get_dataItemPure'), domain);

            var data = new FormData();
            data.append('item_id', id);

            const response = await fetch(url, {
                method: 'POST',
                body: data
            });
            const result = await response.json();
            return result;
        }

        async function fetch_dataItemFromBarcode(barcode = null) {

            let url = new URL(path('get_dataItemPure'), domain);

            var data = new FormData();
            data.append('item_barcode', barcode);

            const response = await fetch(url, {
                method: 'POST',
                body: data
            });
            const result = await response.json();
            return result;
        }

        //===================================================================

        //
        // Function
        //

        //  reset form search
        function resetFormSearch() {
            frm_item.trigger('reset')

            // select 2 reset
            select2.val('').trigger('change')

            section_doc.find('#item_search').val('')
            modal_name.find('#normal[name=customRadio]').prop('checked', true)
            modal_name.find(".temp-text-node").html('')
        }


        function modal_input_data(data = []) {

            let item = create_dom_item(data)

            modal_name.find("#data_item").html(item)
            modal_name.find("#hidden_id").val(data.ITEM_ID)

            modal_name.find('.btn_add_item').text('เพิ่ม')

            if (split[3] == "order") {
                modal_name.find('#node_id').attr('required', 'required')
            }
        }

        function modal_input_data_edit(data = []) {
            // console.log(data)
            modal_input_data(data)

            modal_name.find(".temp-text-node").html(data.NODE_NAME)
            modal_name.find("#item_total").val(data.TOTAL)
            modal_name.find("#remark").val(data.REMARK)

            if (split[3] != "order") {
                if (data.TEMP) {
                    modal_name.find('#hold').prop('checked', true)
                }

                modal_name.find('#node_id').removeAttr('required', 'required')
            } else {
                modal_name.find('#node_id').removeAttr('required', 'required')
            }

        }

        // return path
        function path(name = null) {
            let split = window.location.pathname.split("/")
            let pathname = window.location.origin + "/" + split[1] + "/" + split[2];
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

        function check_listTemp() {
            let ele = section_list.find('ul.list-group')
            if (ele.length) {
                swalalert('error', 'บันทึกหรือล้างข้อมูลรายการที่มีอยู่ก่อน', {
                    auto: false
                })

                return false
            } else {

                return true
            }

        }

        function showSectionDoc() {
            section_btn.addClass('d-none')
            section_doc.removeClass('d-none')

            section_doc.find('#item_search').focus()
        }

        function hideSectionDoc() {
            dataReload()

            section_btn.removeClass('d-none')
            section_doc.addClass('d-none')

            resetFormSearch()
        }

        function hideSectionList() {
            $('#list_head').empty()
            $('#list_date').empty()
            $('#list_user').empty()

            section_list.addClass('d-none')

            resetFormSearch()
        }

        function showSectionList() {
            // check temp
            if (!section_list.find('#list_head').html()) {
                get_docTemp()
            }
            section_list.removeClass('d-none')

            resetFormSearch()
        }

        //===================================================================

        //
        //  HTML
        // 

        //
        // item
        function create_dom_item(data = []) {
            let result = ""
            let img_barcode = ""

            if (data) {

                if (data.ITEM_BARCODE_IMG) {
                    img_barcode = data.ITEM_BARCODE_IMG
                }
                result = `
                        <div class="">
                            <div class="">
                            <h2 class="">${data.ITEM_NAME}</h2>
                            <p>หมวดหมู่ :${data.ITEM_CATAGORY_NAME}</p>
                            ${img_barcode}
                            </div>
                        </div>`
            }


            return result
        }

        //
        // item temp
        function create_dom_itemtemp(data = []) {
            let result = ""
            let html = ""
            let html_item = ""

            if (data) {
                html += `
                        <ul class="list-group list-group-flush">
                        `
                html += `<div class="d-flex justify-content-between header-title">
                    <span>ลำดับ</span>
                    <span>สินค้า</span>
                    <span>ประเภท</span>
                    <span>จำนวน</span>
                    </div>`
                data.forEach((item, index) => {
                    let order = index + 1
                    html_item = `<div class="d-flex justify-content-between">
                    <span><button class="btn btn-danger btn-sm btn_del_temp" data-id="${item.ID}"><i class="fas fa-trash"></i></button> ${order}</span>
                    <span>${item.ITEM_NAME}</span>
                    <span>${item.STATUS_ALIAS_NAME}</span>
                    <span>${item.TOTAL}</span>
                    </div>`
                    html += `
                        <li class="list-group-item px-0 py-1">${html_item}</li>
                        `
                })

                html += `
                        </ul>
                        `


                result = html
            }

            return result
        }

        //
        // temp detail
        function create_dom_temp(data) {

            if (data) {
                let doc_alias = ""
                let date = new Date(data.DATE_STARTS).toJSON().slice(0, 10)

                // เอกสาร รับสินค้าเข้าคลัง
                if (data.DOC_ALIAS == 'import') {
                    doc_alias = "เอกสารรับสินค้าเข้าคลัง"
                }
                if (data.DOC_ALIAS == 'import') {
                    doc_alias = "เอกสารรับสินค้าเข้าคลัง"
                }

                switch (data.DOC_ALIAS) {
                    case 'import':
                        doc_alias = "เอกสารรับสินค้าเข้าคลัง"
                        break;
                    case 'bill':
                        doc_alias = "เอกสารขายสินค้า"
                        break;
                    case 'issue':
                        doc_alias = "เอกสารเบิกสินค้าเข้าคลัง"
                        break;
                    case 'node':
                        doc_alias = "เอกสารรอสินค้า"
                        break;
                    case 'lost':
                        doc_alias = "เอกสารเบิกเสีย"
                        break;
                    case 'order':
                        doc_alias = "เอกสารสั่งซื้อ"
                        break;
                    default:
                        doc_alias = ""
                        break;
                }

                section_list.find('#list_head').html(doc_alias)
                section_list.find('#list_date').html('วันที่ : ' + date)
                section_list.find('#list_user').html('โดย : ' + data.USERNAME)
            }
        }

        // 
        // temp loading
        function tempLoading() {
            domLoading('.data_temp_item')
        }

        function tempLoading_clear() {
            $('.data_temp_item').find('.loading').remove()
        }

        // 
        //  dom loading
        function domLoading(domname = null) {
            if ($(domname).length) {
                $(domname).html(loading)
            }
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

        //
        // Modal show after filter data
        function modal_show_item(id = null) {
            if (id) {
                modalLoading()

                modal_name.modal('show')

                fetch_dataItem(id)
                    .then(resp => {
                        buttonUpdate_hide()

                        modal_input_data(resp.data)

                        modalLoading_clear()
                    })

            }
        }

        //
        // Modal show after filter data
        function modal_show_list(id = null, data = null) {
            if (id) {
                modal_name.modal('show')

                modal_input_data_edit(data)
            }
        }

        //
        // Modal show after filter data
        function modal_show_itemFromBarcode(barcode = null) {
            if (barcode) {

                fetch_dataItemFromBarcode(barcode)
                    .then(resp => {

                        if (resp.data) {
                            modalLoading()

                            buttonUpdate_hide()

                            modal_name.modal('show')

                            modal_input_data(resp.data)

                            modalLoading_clear()
                        }

                    })

            }
        }

        //
        // Modal hide after add data
        function modal_hide_item() {

            modal_name.modal('hide')
        }

        //
        // show button update document
        function buttonUpdate_show() {
            modal_name.find('button.btn_add_item').attr('data-edit', 1)

            if (split[3] != "order") {
                modal_name.find('#node_id').attr('disabled', 'disabled')
                modal_name.find('[name=customRadio]').attr('disabled', 'disabled')
            }
        }

        function buttonUpdate_hide() {
            modal_name.find('button.btn_add_item').attr('data-edit', '')

            modal_name.find('#node_id').removeAttr('disabled', 'disabled')
            modal_name.find('[name=customRadio]').removeAttr('disabled', 'disabled')

            if (split[3] == "order") {
                // modal_name.find('#node_id').removeAttr('disabled', 'disabled')
                $('input#hold').attr('disabled', 'disabled')
            }

            switch (method.val()) {
                case 'sale':
                    $('input#hold').attr('disabled', 'disabled')
                    modal_name.find('#node_id').attr('disabled', 'disabled')
                    break;
                case 'lost':
                    $('input#hold').attr('disabled', 'disabled')
                    modal_name.find('#node_id').attr('disabled', 'disabled')
                    break;
                default:
                    break;
            }
        }
    })
</script>