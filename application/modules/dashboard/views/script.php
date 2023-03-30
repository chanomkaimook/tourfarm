<script>
    $(document).ready(function() {

        let datatable = $('#datatable')
        let last_columntable = datatable.find('th').length - 1
        let last_defaultSort = last_columntable - 1

        let currentDate = new Date().toJSON().slice(0, 10)

        let urlname = new URL('calendar/ctl_manage/get_data?dashboard=1', domain);

        let t = datatable.DataTable({
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
                [0, 'asc']
            ],



            columns: [{
                    "data": {
                        _:"ROUND_NAME",
                        sort:"TIME_START.data"
                    }
                },
                {
                    "data": "DETAIL_NAME"
                },
                {
                    "data": {
                        _: 'BOOKING_DATE.display',
                        sort: 'BOOKING_DATE.timestamp',
                    },
                },
                {
                    "data": "AGENT_NAME"
                },
                {
                    "data": "AGENT_CONTACT"
                },
            ],
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

                $('td', row).eq(0).addClass('h4');
            },


            dom: datatable_dom,
            buttons: datatable_button,
        })

        datatable.on("draw.dt", function() {
            updateScore()

            // updateSystem()
        })

        function updateScore() {
            $('.score').html(loading)

            dom_create('doc_bill')

            /* dom_create('doc_import_item')
            dom_create('doc_issue_item')
            dom_create('doc_bill_item')
            dom_create('doc_lost_item')
            dom_create('doc_order_item')
            dom_create('doc_node_item') */
        }

        function dom_create(table = null) {
            fetch_order(table)
                .then((resp) => {
                    $('.order').html(resp.total_order)
                    $('.order_customer').html(resp.total_customer)
                    $('.order_waite').html(resp.total_waite)
                })
        }

        async function fetch_order(table = null) {

            let url = new URL(path('fetch_order'), domain)



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

    })
</script>