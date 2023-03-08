<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="">
            <div class="card-box table-responsive">
                <table id="datatable_staff" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>สิทธิ์</th>
                            <th>ชื่อ</th>
                            <th>นามสกุล</th>
                            <th>ชื่อผู้ใช้</th>
                            <th>วันที่สมัคร</th>
                            <th>verify</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- end row -->

    </div> <!-- end container-fluid -->

</div> <!-- end content -->


<script>
    $(document).ready(function() {

        let url_register = new URL('admin/ctl_register/fetch_data', domain);
        $('#datatable_staff').DataTable({
            ajax: {
                url: url_register,
                type: "get",
                dataType: "json",

            },
            order: [[ 4, 'asc' ]],
            columns: [{
                    "data": "ROLE",
                },
                {
                    "data": "NAME",
                },
                {
                    "data": "LASTNAME"
                },
                {
                    "data": "USERNAME"
                },
                {
                    "data": "DATE_START_TEXT"
                },
                {
                    "data": "VERIFY"
                },
            ],
            "createdRow": function(row, data, index) {
                let table_btn_verify = `<button type="button" class="btn btn-primary btn_verify" data-id="${data['ID']}" data-name="${data['NAME']}" data-lastname="${data['LASTNAME']}">อนุญาต</button>`
                $('td', row).eq(5).html(table_btn_verify)

            },
            dom: datatable_dom,
            buttons: datatable_button,

        });

        /**
         * 
         * verify event
         * 
         */
        $(document).on('click', '.btn_verify', function() {
            event.preventDefault();
            
            var data = new FormData();
            data.append('id', $(this).attr('data-id'))
            let url = new URL('admin/ctl_register/update_verify', domain);
            let option = {
                method: 'POST',
                body: data,
            }
            fetch(url, option)
                .then(res => res.json())
                .then((resp) => {
                    if(resp.error){
                        swal.fire('ผิดพลาด',resp.message , 'warning')
                    }else{
                        swal.fire('สำเร็จ',resp.message , 'success')
                        $(this).parents('tr').remove()
                    }
                })
        })
    })
</script>