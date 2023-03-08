<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="">
            <div class="card-box table-responsive">

                <div class="row">
                    <div class="col-md-12">
                        <!-- Button trigger modal  -->
                        <button type="button" id="register" class="btn btn-primary" data-id="" data-toggle="modal" data-target="#btn_register_user_modal">เพิ่ม user</button>

                    </div>
                </div>
                <table id="datatable_users" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>ตำแหน่ง</th>
                            <th>ชื่อ</th>
                            <th>นามสกุล</th>
                            <th>ชื่อผู้ใช้</th>
                            <th>วันที่สมัคร</th>
                            <th>action</th>
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

<!-- Modal -->
<div id="btn_register_user_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal was-validated" autocomplete="off" id="dataform" action="" class="was-validated">
                    <input type="hidden" id="method" name="method" value="">
                    <input type="hidden" id="hidden_id" name="hidden_id" value="1"> <!-- set 1 for default value check -->

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">สิทธิ์</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">ระบุสิทธิ์</option>
                                <option value="admin">admin</option>
                                <option value="approve">approve</option>
                                <option value="user">user</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">ชื่อ</label>
                            <input class="form-control" type="text" id="name" name="name" placeholder="ชื่อภาษาไทย" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">นามสกุล</label>
                            <input class="form-control" type="text" id="lastname" name="lastname" placeholder="นามสกุลภาษาไทย" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">ชื่อผู้ใช้</label>
                            <input type="text" id="input_username" name="input_username" class="form-control" placeholder="ชื่อผู้ใช้" required>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">รหัสผ่าน</label>
                            <input type="password" id="input_password" name="input_password" class="form-control" placeholder="รหัสผ่าน" required>

                        </div>
                    </div>

                    <div class="form-group row text-center mt-2">
                        <div class="col-12">
                            <button class="btn btn-md btn-block btn-primary waves-effect waves-light" id="btn_register" type="submit">ลงทะเบียน</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>

    </div>
</div>


<script>
    $(document).ready(function() {
        let frm = $('#dataform')
        let method = $('#dataform input#method')

        //  fetch data
        //
        let url_user = new URL('admin/ctl_user/fetch_data', domain);

        $('#datatable_users').DataTable({
            ajax: {
                url: url_user,
                type: 'get',
                dataType: 'json'
            },
            order: [
                [4, 'desc']
            ],
            "createdRow": function(row, data, index) {
                let table_btn_edit_user =
                    `
                <button type="button" class="btn btn-primary btn_edit_user" data-id="${data['ID']}" data-toggle="modal" data-target="#btn_register_user_modal">แก้ไข</button>
                <button type="button" class="btn btn-danger btn_delete_user" data-id="${data['ID']}">ลบ</button>
                `
                $('td', row).eq(5).html(table_btn_edit_user)
            },


            dom: datatable_dom,
            buttons: datatable_button,
        })


        $(document).on('submit', '#dataform', function() {

            if (method.val() == 'insert') {
                register()
            } else {
                update_userdata()
            }

            return false;

        })

        //
        // button add
        $(document).on('click', '#register', function() {

            method.val('insert')
            frm.find('#btn_register').text('ลงทะเบียน')
        })

        //
        // button edit
        $(document).on('click', '.btn_edit_user', function() {

            let url_get_user = new URL('admin/ctl_user/get_user?id=' + $(this).attr('data-id'), domain);
            fetch(url_get_user)
                .then(res => res.json())
                .then((resp) => {
                    modal_input_data(resp.data)

                    method.val('edit')
                    frm.find('#btn_register').text('บันทึก')

                    $("#hidden_id").val($(this).attr('data-id'))
                });
        })

        //
        // reset form
        $('#btn_register_user_modal').on('hidden.bs.modal', function(e) {
            // do something...
            frm.trigger('reset')
            $(this).find("#input_username").removeAttr('disabled')
            $(this).find("#input_password").removeAttr('disabled')
        })


        function register() {
            //serializeArray() สามารถส่งข้อมูล fromไปพร้อมกัน โดยไม่ต้องมาใส่ value ใน append ที่ละตัว
            var dataArray = $("#dataform").serializeArray(),
                len = dataArray.length,
                dataObj = {};
            //length ให้นับข้อมูลใน dataArray
            // console.log(dataArray);return false;

            let url = new URL('register/ctl_register/insert_data_staff', domain);

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
                        Swal.fire('ผิดพลาด', resp.txt, 'warning')
                    } else {

                        Swal.fire({
                            title: 'สำเร็จ',
                            html: 'รหัสพร้อมใช้งาน',
                            timer: 2000,
                            timerProgressBar: true,
                        }).then((result) => {
                            update_verify(resp.data.ID)
                            window.location.reload();
                        })
                    }

                });

        }

        function update_verify(id = null) {

            if (id) {
                let data_vf = new FormData()
                data_vf.append('id', id)

                let url_verify = new URL('admin/ctl_register/update_verify', domain);
                fetch(url_verify, {
                        method: 'POST',
                        body: data_vf,
                    })
                    .then(res => res.json())
                    .then((resp) => {

                    });

            }

        }

        function modal_input_data(data = []) {
            let modal_name = $("#btn_register_user_modal")

            modal_name.find("#role").val(data.ROLE)
            modal_name.find("#name").val(data.NAME)
            modal_name.find("#lastname").val(data.LASTNAME)
            modal_name.find("#input_username").attr('disabled', 'disabled')
            modal_name.find("#input_password").attr('disabled', 'disabled')

        }

        function update_userdata() {
            
            let data_hidden_id = $("#hidden_id").val();

            let url_update_user = new URL('admin/ctl_user/update_user', domain)

            var data = new FormData()
            data.append('id', data_hidden_id)
            data.append('role', $("#role").val())
            data.append('name', $("#name").val())
            data.append('lastname', $("#lastname").val())

            let option = {
                method: 'POST',
                body: data,
            }

            fetch(url_update_user, option)
                .then(res => res.json())
                .then((resp) => {

                    $('#datatable_users').DataTable().ajax.reload();

                    $('#btn_register_user_modal').modal('hide')
                    
                })
        }


        $(document).on('click', '.btn_delete_user', function() {
            $("#hidden_id").val($(this).attr('data-id'))
            let hidden_id = $("#hidden_id").val();

            let table_tr = $('.btn_edit_user[data-id=' + hidden_id + ']').parents('tr');
            let user_name = table_tr.children('td').eq(1).text() + ' ' + table_tr.children('td').eq(2).text()

            Swal.fire({
                title: 'ยืนยันการลบ',
                text: "คุณต้องการลบข้อมูลนี้ " + user_name,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#64c5b1',
                cancelButtonColor: '#f96a74',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.value) {
                    confirm_delete(hidden_id)
                }
            })
        })

        function confirm_delete(id = null) {

            if (id) {
                let url_delete_user = new URL('admin/ctl_user/delete_user', domain);

                var delete_data = new FormData();
                delete_data.append('id', id);
                fetch(url_delete_user, {
                        method: 'POST',
                        body: delete_data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        if (resp.data.error == 0) {
                            $('#datatable_users').DataTable().ajax.reload(null,false);

                            Swal.fire(
                                'สำเร็จ',
                                resp.data.text,
                                'success'
                            )
                        } else {
                            Swal.fire(
                                'ผิดพลาด',
                                resp.data.text,
                                'warning'
                            )
                        }

                        //window.location.reload()
                    });
            }

        }


    })
</script>