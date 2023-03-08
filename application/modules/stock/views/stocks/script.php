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

        let barcode_search = $('#barcode_search')

        let select2 = $('[data-toggle="select2"]')
        // inisialize select2
        select2.select2()

        let hiden_zero = $('input#zero[type=hidden]');

        const queryString = decodeURIComponent(window.location.search);
        const params = new URLSearchParams(queryString);
        let currentDate = new Date().toJSON().slice(0, 10)
        if (params.get("date")) {
            currentDate = params.get("date")
        }


        //  fetch data
        //
        let urlname = new URL(path('get_data'), domain);
        // urlname.searchParams.append('date', currentDate);
        let column_total = 6;

        datatable.DataTable({
            ajax: {
                url: urlname,
                type: 'get',
                dataType: 'json',
                data: function(d) {
                    d.item_filter_catagory = $('#item_filter_catagory').val(),
                        d.item_filter_statusview = $('#item_filter_statusview').val(),
                        d.hidden_datestart = $('#hidden_datestart').val(),
                        d.hidden_dateend = $('#hidden_dateend').val(),
                        d.zero = hiden_zero.val()
                },
            },
            order: [
                [column_total, 'asc']
            ],
            paging: false,
            columns: [{
                    "data": "ITEM"
                },
                {
                    "data": "CATAGORY"
                },
                {
                    "data": "TOTAL",
                },
                {
                    "data": "ISSUE_TOTAL"
                },
                {
                    "data": "IMPORT_TOTAL"
                },
                {
                    "data": "BILL_TOTAL"
                },
                {
                    "data": "TEMP_TOTAL"
                },
                /* {
                    "data": "HOLD_IM_TOTAL"
                }, */
                {
                    "data": "HOLD_IS_TOTAL"
                },
                /* {
                    "data": "NET_TOTAL"
                }, */
            ],
            columnDefs: [{
                targets: column_total,
                render: function(data, type, row, meta) {
                    if (type === 'sort') {
                        return data === 0 ? Number.MAX_SAFE_INTEGER : data;
                    }
                    return data;
                }
            }],
            "rowCallback": function(row, data, index) {
                if (data.TEMP_TOTAL <= parseInt(data.STOCK_MIN) && parseInt(data.STOCK_MIN)) {
                    $('td', row).css('background-color', '#e5a0a0');
                }
                if (data.TOTAL >= parseInt(data.STOCK_MAX) && parseInt(data.STOCK_MAX)) {
                    $('td', row).css('background-color', '#ffdc72');
                }
            },
            "createdRow": function(row, data, index) {

                $('td', row).eq(2).addClass('h4')
                $('td', row).eq(column_total).addClass('h4')
                // $('td', row).eq(8).addClass('h6')

                $('td', row).eq(3).attr({'data-toggle':'modal','data-target':'#modal_stock','data-type':'doc_issue_item','data-item_id':data.ID})
                $('td', row).eq(4).attr({'data-toggle':'modal','data-target':'#modal_stock','data-type':'doc_import_item','data-item_id':data.ID})
                $('td', row).eq(5).attr({'data-toggle':'modal','data-target':'#modal_stock','data-type':'doc_bill_item','data-item_id':data.ID})
                $('td', row).eq(7).attr({'data-toggle':'modal','data-target':'#modal_stock','data-type':'doc_waite','data-item_id':data.ID})

                let totalresult = 0 + '<span style="display:none">999</span>';
                if (data['TEMP_TOTAL'] == 0) {
                    $('td', row).eq(column_total).html(totalresult);
                }
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

        //
        // show modal data detail
        $(document).on('click', '#datatable td[data-toggle=modal]', function() {
            let ele = $(this)

            let modalbody = $('#modal_stock .modal-body')
            modalbody.html(loading)

            table = ele.attr('data-type')
            id = ele.attr('data-item_id')

            fetch_itemDoc(id,table)
            .then(resp => {
                html_docdetail = ''

                if(resp.length){
                    html_docdetail += `<span class="h4">${resp[0].ITEM_NAME}</span><span class="lead ml-2">${resp[0].DATE_STARTS_TEXT}</span>`
                    html_docdetail += `<table class="table">`
                    html_docdetail += `<thead class="thead-light">` 
                    html_docdetail += `<tr>`                
                    html_docdetail += `<td>ประเภท</td>`                                        
                    html_docdetail += `<td>จำนวน</td>`                     
                    html_docdetail += `<td>ผู้ติดต่อ</td>`                     
                    html_docdetail += `<td>โดย</td>`                     
                    html_docdetail += `<td>หมายเหตุ</td>`   
                    html_docdetail += `</tr>`      
                    html_docdetail += `</thead>`                      
                    html_docdetail += `<tbody>`                      
                    resp.forEach(function(item,index){
                        html_docdetail += `<tr>`
                        html_docdetail += create_dom_documentdetail(item)
                        html_docdetail += `</tr>`
                    })
                    html_docdetail += `</tbody>`  
                    html_docdetail += `</table>`
                }else{
                    html_docdetail = 'ไม่พบ'
                }

                modalbody.html(html_docdetail)
            })
        })

        async function fetch_itemDoc(id,table) {
            let url = new URL('stock/ctl_document/fetch_itemdoc',domain)

            let data = new FormData()
            data.append('table',table)
            data.append('item_id',id)

            if(table != 'doc_waite'){
                data.append('hidden_datestart',$('#hidden_datestart').val())
                data.append('hidden_dateend',$('#hidden_dateend').val())
            }

            const response = await fetch(url,{
                method:'POST',
                body: data
            })
            const result = await response.json()
            return result;
        }

        //
        // button search
        var delayTimer;
        $(document).on('keyup', '#barcode_search', function() {
            clearTimeout(delayTimer);
            let textsearch = $(this).val()
            delayTimer = setTimeout(function() {
                // Do the ajax stuff
                if (textsearch.trim() && textsearch.length >= 5) {

                    datatable.DataTable().search(textsearch).draw();

                }
            }, 500);
        })
        //===================================================================

        //
        // Filter
        //
        let html = '<div class="btn_toolbar"><button class="btn btn-outline-secondary waves-effect waves-light btn_zero">ซ่อนเหลือ 0</button></div>'
        addTableToolbar(html)
        $(document).on('click', '.btn_zero', function() {
            $(this).toggleClass('active');

            //
            // hide data item total = 0
            let val = hiden_zero.val()
            if (val) {
                hiden_zero.val('')
            } else {
                hiden_zero.val(1)
            }

            dataReload()
        })

        datatable.DataTable().on('search.dt', function() {
            resetForm()
        });

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

            if (frm.find('[type=file]').length && frm.find('[type=file]')[0].files.length) {
                data.append("image[]", frm.find('[type=file]')[0].files[0]);
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

            // file
            if (frm.find('[type=file]').length && frm.find('[type=file]')[0].files.length) {
                data.append("image[]", frm.find('[type=file]')[0].files[0]);
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

            frm.find('.html_statusoff').empty()

            // select 2 reset
            select2.val('').trigger('change')

            // clear temp
            image_temp.innerHTML = null

            // clear barcode search
            barcode_search.val('')
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
            modal_name.find("#item_name").val(data.ITEM_NAME)

            modal_name.find("#image_temp").html(data.ITEM_PIC_TEMP)

            modal_name.find("#item_barcode").val(data.ITEM_BARCODE)

            // change select2
            select2.select2().val(data.ITEM_CATAGORY_ID).trigger('change')

            create_form_edit(data);
        }

        function create_form_edit(data = null) {
            let html = "";
            let dom_toggle = $('input[data-toggle=toggle')
            if (data) {

                html += create_dom_statusoff();
                modal_body.find('.html_statusoff').html(html)

                if (data.ITEM_STATUS_OFFVIEW) {
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

        function create_dom_documentdetail(data) {

            let result = `<td>${data.STATUS_ALIAS_NAME}</td>`
            result += `<td>${data.TOTAL}</td>`
            result += `<td>${data.NODE_NAME}</td>`
            result += `<td>${data.CREATER}</td>`
            result += `<td>${data.REMARK}</td>`

            return result
        }

        //
        // temp image
        imgFile.onchange = evt => {
            const [file] = imgFile.files
            if (file) {
                image_temp.innerHTML = '<img src="' + URL.createObjectURL(file) + '" height="300" class="mw-100" >'
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
    })
</script>