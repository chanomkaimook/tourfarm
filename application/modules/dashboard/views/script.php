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

        let currentDate = new Date().toJSON().slice(0, 10)

        // inisialize select2
        select2.select2({
            placeholder: "รายชื่อ",
            allowClear: true
        })


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
                        d.hidden_datestart = $('#hidden_datestart').val() ? $('#hidden_datestart').val() : currentDate,
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

        datatable.on("draw.dt",function(){
            updateScore()

            updateSystem()
        })

        function updateScore() {
            $('.score').html(loading)

            dom_create('doc_import_item')
            dom_create('doc_issue_item')
            dom_create('doc_bill_item')
            dom_create('doc_lost_item')
            dom_create('doc_order_item')
            dom_create('doc_node_item')
        }

        function dom_create(table = null) {
            fetch_doc(table)
                .then((resp) => {
                    $('.' + table).html(resp)
                })
        }

        async function fetch_doc(table = null) {
            let url = new URL(path('fetch_doc'), domain)

            let data = new FormData()
            data.append('table', table)
            data.append('hidden_datestart', $('#hidden_datestart').val() ? $('#hidden_datestart').val() : currentDate)
            data.append('hidden_dateend', $('#hidden_dateend').val())

            let response = await fetch(url, {
                method: 'post',
                body: data
            })
            let result = await response.json()

            return result
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
    })
</script>