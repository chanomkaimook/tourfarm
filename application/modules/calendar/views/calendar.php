<div class="content">
    <!-- Start Content-->
    <div class="container-fluid">

        <div class="card-box">
            <div class="row">
                <div class="col-md-12">
                    <!-- Button trigger modal  -->
                    <button id="btn-leave-modal" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#form_leave">
                        ฟอร์มใบลา
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex">
                        <div class="px-2"><i class="mdi mdi-checkbox-blank-circle text-primary"></i> ลาป่วย</div>
                        <div class="px-2"><i class="mdi mdi-checkbox-blank-circle text-warning"></i> ลากิจ</div>
                        <div class="px-2"><i class="mdi mdi-checkbox-blank-circle text-danger"></i> ลาพักร้อน</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div id="calendar"></div>
                </div>
            </div>

        </div>


        <!-- Modal -->
        <div class="modal fade" id="form_leave" tabindex="-1" aria-modal="true" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="form_leaveLabel">ฟอร์มลางาน</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form class="was-validated leave-validation" name="form_valid_leave" id="form_valid_leave" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="method_edit" value="">
                            <input type="hidden" id="user-id" value="">
                            <div class="mb-3">
                                <h3>ชื่อผู้ใช้: <?php echo $this->session->userdata('user_name') ?></h3>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" id="leave" required aria-label="select leave" name="leave">
                                    <option value="">ระบุการลา</option>
                                    <?php
                                    foreach ($leave as $data) {
                                        $selected = '';
                                        if ($data->ID == 3) {
                                            // $selected = 'selected';
                                        }

                                    ?>
                                        <option <?php echo $selected; ?> value="<?= $data->ID; ?>"><?= $data->LEAVE_NAME; ?></option>
                                    <?php
                                    }
                                    ?>


                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control is-invalid rounded-2 border-4" id="description" placeholder="โปรดระบุ เหตุผลการลา" name="description" required></textarea>
                            </div>
                            <div class="mb-3 d-flex flex-row">

                                <input type='text' class="me-3 form-control rounded-2 border-4" id='date_start' placeholder='วันที่เริ่มต้น' name="date_start" autocomplete="off" required>
                                <input type='text' class="form-control rounded-2 border-4" id='date_end' placeholder='วันที่สิ้นสุด' name="date_end" autocomplete="off" required>
                            </div>

                            <!-- <div class="mb-4">
                            <ul class="nav nav-tabs"  role="tablist">
                                <li class="nav-item employee-type">
                                    <a class="nav-link  active" data-bs-toggle="tab" id="office">พนักงานออฟฟิศ</a>
                                </li>
                                <li class="nav-item employee-type">
                                    <a class="nav-link " data-bs-toggle="tab" id="home">แม่บ้าน</a>
                                </li>

                            </ul>
                        </div> -->

                            <div class="mb-4">
                                <div class="dropdown">

                                    <select name="emp_name" id="emp_name" class="form-select employee_name" required>
                                        <option value="">ระบุตำแหน่ง</option>
                                        <?php
                                        foreach ($emp as $data) {
                                        ?>
                                            <option class="" value="<?= $data->ID; ?>"><?= $data->EMP_NAME; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class="mb-4 leave_day_type">
                                <div class="dropdown">

                                    <select name="leave_type_name" id="leave_type_name" class="form-select leave_type_name" required>
                                        <option value="">ระบุช่วงเวลา ลา</option>
                                        <?php
                                        foreach ($leave_type as $data) {

                                            $selected = '';
                                            if ($data->ID == 1) {
                                                $selected = 'selected';
                                            }
                                        ?>
                                            <option value="<?= $data->ID; ?>"><?= $data->LEAVE_TYPE_NAME; ?></option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>


                            <div class="list_day d-none" data-type="4">
                                <div class="mb-3 d-flex flex-row">
                                    <select class="form-select me-3 time_start_hour" aria-label="Default select example" id="time_start_hour" name="time_start_hour" required>

                                        <?php

                                        foreach ($time_s_e as $data) {
                                            if ($data->START) {
                                        ?>
                                                <option value="<?= $data->ID; ?>" data-time="<?= $data->START; ?>"><?= date('H:i', strtotime($data->START)); ?></option>

                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select class="form-select time_end_hour" aria-label="Default select example" id="time_end_hour" name="time_end_hour" required disabled=disabled>

                                        <?php

                                        foreach ($time_s_e as $data) {
                                            if ($data->END) {
                                        ?>
                                                <option value="<?= $data->ID; ?>" data-time="<?= $data->END; ?>"><?= date('H:i', strtotime($data->END)); ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <input type="file" id='files' name="files[]" class="filestyle" multiple required>


                                <div id="file_img" class="d-flex flex-column justify-content-center border-2"></div>



                                <div id="file_img_data" class="d-flex flex-column justify-content-center border-2"></div>


                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="save-event">บันทึกวันลา</button>
                                <button type="button" class="btn btn-secondary" id="delete-event" data-bs-dismiss="modal">ลบข้อมูลวันลา</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- end container-fluid -->
</div> <!-- end content -->

<script>
    $(document).ready(function() {


        $('#date_start , #date_end').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
        });

        $(document).on('change', '#date_end', function() {
            let date_start = $('#date_start').val()
            let date_end = $(this).val()

            if (date_end < date_start) {
                $(this).datepicker('setDate', new Date(date_start));
            }
        })
        $(document).on('change', '#date_start', function() {
            let date_start = $(this).val()
            let date_end = $('#date_end').val()

            if (date_end < date_start) {
                $('#date_end').datepicker('setDate', new Date(date_start));
            }
        })


        $("#leave").change(function() {

            let val = $(this).val();

            $(".leave_type_name").removeAttr('disabled', 'disabled')
            // console.log(val)
            let array_leave_type = ['4', '3', '5', '6', '7']
            if (array_leave_type.indexOf(val) != -1) {
                setdefault_fulltime();
            }

        });

    })

    /**
     * 
     * * set default
     * 
     */


    function setdefault_fulltime() {

        $(".leave_type_name").val('1').triggerHandler('change')
        $(".leave_type_name").attr('disabled', 'disabled')

    }

    /* select emp change to time_s and time_e */
    $(".employee_name").change(function() {
        $(".leave_type_name").triggerHandler('change')
    });

    $(".leave_type_name").change(function() {
        let val = $(this).val();
        $(".list_day").removeClass('d-none')

        $('.employee_name').val();

        // console.log($('.employee_name').val())

        switch (val) {
            case '4':
                switch ($('.employee_name').val()) {

                    case '1':
                        $("#time_start_hour").val('3')
                        $("#time_end_hour").val('4')

                        break;

                    case '2':
                        $("#time_start_hour").val('1')
                        $("#time_end_hour").val('2')

                        break;

                    case 'default':
                        $("#time_start_hour").val('3')
                        $("#time_end_hour").val('21')
                        break;
                }
        }

        switch (val) {
            case '1':

                switch ($('.employee_name').val()) {

                    case '1':
                        $("#time_start_hour").val('3')
                        $("#time_end_hour").val('21')
                        break;

                    case '2':
                        $("#time_start_hour").val('1')
                        $("#time_end_hour").val('20')
                        break;

                    case 'default':
                        $("#time_start_hour").val('3')
                        $("#time_end_hour").val('21')
                        break;
                }

                break;

            case '2':

                switch ($('.employee_name').val()) {

                    case '1':
                        $("#time_start_hour").val('3')
                        $("#time_end_hour").val('11')
                        break;

                    case '2':
                        $("#time_start_hour").val('1')
                        $("#time_end_hour").val('11')
                        break;

                    case 'default':
                        $("#time_start_hour").val('3')
                        $("#time_end_hour").val('11')
                        break;
                }
                break;

            case '3':

                switch ($('.employee_name').val()) {

                    case '1':
                        $("#time_start_hour").val('12')
                        $("#time_end_hour").val('21')
                        break;

                    case '2':
                        $("#time_start_hour").val('12')
                        $("#time_end_hour").val('20')
                        break;

                    case 'default':
                        $("#time_start_hour").val('12')
                        $("#time_end_hour").val('21')
                        break;
                }
                break;
        }


        // ปิดตัวเลือกเวลา  ***4=รายชั่วโมง
        // $("#time_start_hour,#time_end_hour").attr('disabled', 'disabled')
        if (val != '4') {
            $("#time_start_hour,#time_end_hour").attr('disabled', 'disabled')
        } else {
            $("#time_start_hour").removeAttr('disabled', 'disabled')
        }


    })

    /* set select ลาแบบ รายชั่วโมง=4 */
    $("#time_start_hour").change(function() {
        $("#time_end_hour").removeAttr('disabled', 'disabled')
        $("#time_end_hour").find('option').removeClass('d-none');

        let val = $(this).val()

        let next = parseInt(val) + 1;
        console.log(next)
        for (var i = 1; i <= val; i++) {

            $("#time_end_hour").find('option[value=' + i + ']').addClass('d-none');
        }

        // $("#time_end_hour").find('option[value=' + val + ']').removeClass('d-none');

        $("#time_end_hour").val(next)
    })

    // * domain = e_navbar.php
    async function get_data() {
        let url_calendar = new URL('calendar/event_calendars/get_data', domain);
        const response = await fetch(url_calendar, {})

        return response.json()
    }

    get_data()
        .then((data) => {
                // JSON data parsed by `data.json()` call

                var set_event = [];
                $.each(data.result, function(key, value) {
                    let class_name = '';
                        if (value.LEAVE_ID == 1) {
                          class_name = 'bg-primary';
                        } else if (value.LEAVE_ID == 2) {
                          class_name = 'bg-warning';
                        } else {
                          class_name = 'bg-danger';
                        }

                        set_event.push({
                            id: value.ID,
                            emp: value.EMP_ID,
                            leave: value.LEAVE_ID,
                            title: value.DESCRIPTION,
                            start: value.DATE_START,
                            end: value.DATE_END,
                            l_n: value.LEAVE_TYPE_ID,
                            t_s: value.TIME_S_ID,
                            t_e: value.TIME_E_ID,
                            end_less: value.DATE_END_LESS,
                            image: value.IMAGE,
                            c_id: value.CALENDAR_ID,
                            className: class_name
                            // color: '#0dcaf0',
                        })
                 

                })
            //console.log(set_event[0].title)
            // console.log(set_event)

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: set_event,

                eventClick: function(event) {

                    if (event.id) {

                        let html_img = ''
                        event.image.map(function(item, key) {
                            html_img += `<div class='card '>
                                <button type='button' class='btn-close btn-delete-item align-self-end' data-id='${item.id}'></button>
                                <img class='img-thumbnail' src='${item.name}' >
                                </div>`
                            // $('#form_leave').find('#file_img').append(html_img);
                        })


                        $('#form_leave').modal('show');

                        $('#form_leave').find('#user-id').val(event.id);
                        $('#form_leave').find('#leave').val(event.leave);
                        $('#form_leave').find('#description').val(event.title);
                        $('#form_leave').find('#date_start').datepicker('setDate', event.start._i);
                        $('#form_leave').find('#date_end').datepicker('setDate', event.end_less);
                        $('#form_leave').find('.employee_name').val(event.emp).triggerHandler('change');
                        $('#form_leave').find('#leave_type_name').val(event.l_n);

                        $('#time_start_hour option[data-time="' + event.t_s + '"]').prop('selected', true);
                        $('#form_leave').find('#time_end_hour option[data-time="' + event.t_e + '"]').prop('selected', true);

                        $('#form_leave').find('#file_img_data').html(html_img);


                        // $('#form_leave').find('#time_end_hour').val(event.t_e);

                    }
                }

            });
        });

    $("#delete-event").click(function() {
        // let item2 = 'item';
        // swal_delete(iem2)
        swal_delete('item')
        return false;
        // alert('คุณต้องการลบข้อมูลนี้ใช่ไหม !')
    })
    $(document).on('click', '.btn-delete-item', function() {

        swal_delete('image', $(this).attr('data-id'))
    })

    function swal_delete(item, id = false) {

        Swal.fire({
            title: 'โปรดยืนยัน?',
            text: "คุณต้องการลบข้อมูลนี้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64c5b1',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ใช่ ต้องการลบ'
        }).then((result) => {
            if (result.value) {

                // callback
                if (item == 'item') {
                    item_delete()
                }

                if (item == 'image') {
                    image_delete(id)
                }

            }

        })
    }

    function image_delete(id) {

        let delete_data = new FormData();

        delete_data.append("id", id);



        fetch('event_calendars/delete_image_data', {
                method: 'POST',
                body: delete_data
            })
            .then(res => res.json())
            .then((resp) => {
                if (resp.status) {
                    swal.fire(
                        "ลบเสร็จแล้ว",
                        "",
                        "success"
                    )

                    $('.btn-delete-item[data-id=' + id + ']').parent('div').remove()
                }

            });
    }

    function item_delete() {
        let delete_data = new FormData();

        delete_data.append("user_id_up", $("#user-id").val());
        delete_data.append("leave_up", $("#leave").val());
        delete_data.append("description_up", $("#description").val());
        delete_data.append("date_start_up", $("#date_start").val());
        delete_data.append("date_end_up", $("#date_end").val());
        delete_data.append("emp_name_up", $("#emp_name").val());
        delete_data.append("leave_type_name_up", $("#leave_type_name").val());
        delete_data.append("time_start_hour_up", $("#time_start_hour").val());
        delete_data.append("time_end_hour_up", $("#time_end_hour").val());

        fetch('event_calendars/delete_data', {
                method: 'POST',
                body: delete_data
            })
            .then(res => res.json())
            .then((resp) => {


                window.location.reload()
            });
    }


    $("#btn-leave-modal").click(function() {
        $("#method_edit").val('')
        // $("#form_valid_leave").trigger("reset");
    });

    $('#form_leave').on('hidden.bs.modal', function(e) {

        $('#file_img').html('')
        $("#form_valid_leave").trigger("reset");
    })




    $("#save-event").click(function() {

        if ($("#method_edit").val() == "edit") {
            let update_data = new FormData();

            update_data.append("user_id_up", $("#user-id").val());
            update_data.append("leave_up", $("#leave").val());
            update_data.append("description_up", $("#description").val());
            update_data.append("date_start_up", $("#date_start").val());
            update_data.append("date_end_up", $("#date_end").val());
            update_data.append("emp_name_up", $("#emp_name").val());
            update_data.append("leave_type_name_up", $("#leave_type_name").val());
            update_data.append("time_start_hour_up", $("#time_start_hour").val());
            update_data.append("time_end_hour_up", $("#time_end_hour").val());
            var totalfiles = document.getElementById('files').files.length;
            for (var index = 0; index < totalfiles; index++) {
                update_data.append("files[]", $("#files")[0].files[index]);
            }



            fetch('event_calendars/update_data', {
                    method: 'POST',
                    body: update_data
                })
                .then(res => res.json())
                .then((resp) => {

                    var events = [];

                    $.each(resp.query, function(key, value) {
                        //  console.log(key + '=====' + value.DESCRIPTION);

                        events = [{
                            id: value.ID,
                            emp: value.EMP_ID,
                            leave: value.LEAVE_ID,
                            title: value.DESCRIPTION,
                            start: value.DATE_START,
                            end: value.DATE_END,
                            l_n: value.LEAVE_TYPE_ID,
                            t_s: value.TIME_S_ID,
                            t_e: value.TIME_E_ID,
                            end_less: value.DATE_END_LESS,
                            image: value.IMAGE,
                            c_id: value.CALENDAR_ID,
                            // start: value.start + 'T09:00:00',
                            // end: `${value.end}T00:00:00`
                        }]
                        console.log(events)
                        // $('#calendar').fullCalendar('updateEvent', events);
                        $('#calendar').fullCalendar('removeEvents', value.ID);
                        $('#calendar').fullCalendar('addEventSource', events);

                        $('.modal').modal('hide')
                        // $('#calendar').fullCalendar('refetchEvents');
                    });

                    //window.location.reload()
                });

        } else {


            emp_name = $("#emp_name").val(),
                leave = $("#leave").val(),
                description = $("#description").val(),
                date_start = $("#date_start").val(),
                date_end = $("#date_end").val(),
                leave_type_name = $("#leave_type_name").val(),
                time_start_hour = $("#time_start_hour").val(),
                time_end_hour = $("#time_end_hour").val()

            if (emp_name == '' || leave == '' || description == '' ||
                date_start == '' || date_end == '' || leave_type_name == '' ||
                time_start_hour == '' || time_end_hour == '') {
                alert('กรุณากรอกข้อมูลให้ครบ')

            } else {


                let data = new FormData();
                data.append("emp_name", $("#emp_name").val());
                data.append("leave", $("#leave").val());
                data.append("description", $("#description").val());
                data.append("date_start", $("#date_start").val());
                data.append("date_end", $("#date_end").val());
                data.append("leave_type_name", $("#leave_type_name").val());
                data.append("time_start_hour", $("#time_start_hour").val());
                data.append("time_end_hour", $("#time_end_hour").val());


                var totalfiles = document.getElementById('files').files.length;
                for (var index = 0; index < totalfiles; index++) {
                    data.append("files[]", $("#files")[0].files[index]);
                }

                // console.log(totalfiles)
                // console.log($("#files"))
                // data.append("emp_name", 2);
                // data.append("leave", 1);
                // data.append("description", "lll11111");
                // data.append("date_start", "2022-12-16");
                // data.append("date_end", "2022-12-16");
                // data.append("leave_type_name", 4);
                // data.append("time_start_hour", 3);
                // data.append("time_end_hour", 5);

                fetch('event_calendars/insert_data', { //insert_data ชื่อ controller
                        method: 'POST',
                        body: data //ส่งข้อมูลที่ชื่อ data ไปยัง controller ที่ชื่อ Eleave ในชื่อ function insert_data
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        // console.log(resp);
                        // console.log(resp.status);
                        // alert(resp.status + resp.id); //เข้าถึงข้อมูลแบบ json  jsonผลลัพธ์จะเป็นสตริง ไม่สามารถเข้าถึงข้อมูลแบบ arrayได้
                        // window.location.reload()
                        var events = [];

                        $.each(resp.query, function(key, value) {
                            //  console.log(key + '=====' + value.DESCRIPTION);

                            events = [{
                                id: value.ID,
                                emp: value.EMP_ID,
                                leave: value.LEAVE_ID,
                                title: value.DESCRIPTION,
                                start: value.DATE_START,
                                end: value.DATE_END,
                                l_n: value.LEAVE_TYPE_ID,
                                t_s: value.TIME_S_ID,
                                t_e: value.TIME_E_ID,
                                end_less: value.DATE_END_LESS,
                                image: value.IMAGE,
                                c_id: value.CALENDAR_ID,
                                // start: value.start + 'T09:00:00',
                                // end: `${value.end}T00:00:00`
                            }]
                            $('#calendar').fullCalendar('addEventSource', events);
                        });
                        $('.modal').modal('hide')
                        window.location.reload()
                    })
            }

        }

    })


    //  image 
    $(document).on('change', '#files', function(event) {
        let image_file = $(this);
        var length = (image_file[0].files.length - 1);
        var html = "";
        for (var i = 0; i <= length; i++) {

            if (image_file[0].files[i]) {

                //  check extension
                let fileName = image_file[0].files[i].name,
                    idxDot = fileName.lastIndexOf(".") + 1,
                    extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
                if (extFile == "jpg" || extFile == "jpeg" || extFile == "png") {
                    //TO DO
                    html += '<img id="img-' + i + '" src="' + window.URL.createObjectURL(image_file[0].files[i]) + '" class="img">';
                    $('#file_img').html(html);
                } else {
                    //  error
                    swal.fire('ข้อมูลไม่ถูกต้อง', 'บันทึกเฉพาะไฟล์รูปภาพ', 'warning');
                    clearImage(); // Reset the input so no files are uploaded

                    return false;
                }
            }
        }
    });



    function formatDate(input) { // แปลงค่า format carlendar  dd/mm/yyyy  เป็น  yyyy/mm/dd
        var datePart = input.match(/\d+/g),
            day = datePart[0],
            month = datePart[1],
            year = datePart[2];
        hour = datePart[3];
        minute = datePart[4];
        //   console.log(datePart)

        return year + '/' + month + '/' + day + ' ' + hour + ':' + minute;
    }

    function formatDate_database(input) { // แปลงค่า format carlendar  dd/mm/yyyy  เป็น  yyyy/mm/dd
        var datePart = input.match(/\d+/g),
            day = datePart[0],
            month = datePart[1],
            year = datePart[2];
        hour = datePart[3];
        minute = datePart[4];

        return year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00';
    }
</script>