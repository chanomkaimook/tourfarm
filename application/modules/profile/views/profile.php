<div class="content">
    <!-- Start Content-->
    <div class="container-fluid">
        <div class="card-box">
            <div class="row">
                <div class="col-md-12">
                    <!-- Button trigger modal  -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit_profile_modal">
                        แก้ไข
                    </button>
                </div>
            </div>

            <p class="header-title font-20 mb-3 text-muted">ข้อมูลผู้ใช้</p>

            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-12">

                        <div class="">
                            <b class="title">ชื่อ</b>
                            <h3><?php echo $data['NAME']; ?></h3>
                        </div>
                        <div class="">
                            <b class="title">นามสกุล</b>
                            <h3><?php echo $data['LASTNAME']; ?></h3>
                        </div>
                        <div class="">
                            <b class="title">ตำแหน่งงาน</b>
                            <h3><?php echo $data['POSITION']; ?></h3>
                        </div>

                    </div>
                    <div class="col-md-6 col-12">

                        <div class="">
                            <b class="title">ผู้อนุมัติ 1</b>
                            <h3><?php
                                echo $this->mdl_profile->get_employee_name($data['OWNER1']);
                                ?></h3>
                        </div>
                        <div class="">
                            <b class="title">ผู้อนุมัติ 2</b>
                            <h3><?php
                                echo $this->mdl_profile->get_employee_name($data['OWNER2']);
                                ?></h3>
                        </div>
                        <div class="">
                            <b class="title">ผู้อนุมัติ 3</b>
                            <h3><?php
                                echo $this->mdl_profile->get_employee_name($data['OWNER3']);
                                ?></h3>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<!-- Modal -->
<div class="modal fade" id="edit_profile_modal" tabindex="-1" role="dialog" aria-labelledby="edit_profile_modal_Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_profile_modal_Label">ระดับผู้อนุมัติ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card-box">
                            <h4 class="header-title">ชื่อผู้ใช้: <?php echo $this->session->userdata('user_name') ?></h4>
                            <form class="form-horizontal" autocomplete="off" id="update_data_owner">
                                <input type="hidden" id="user-id" value="<?php echo $this->session->userdata('user_code') ?>">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="">ผู้อนุมัติ1</label>
                                        <select name="owner1" id="owner1" class="form-control select_owner">
                                            <option value="" selected>เลือกผู้อนุมัติ</option>
                                            <?php
                                            if ($sql_owner) {
                                                foreach ($sql_owner as $row) {

                                                    $selected = "";
                                                    if($data['OWNER1'] == $row->ID){
                                                        $selected = "selected";
                                                    }
                                                    echo "<option value='$row->ID' $selected >$row->NAME</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="">ผู้อนุมัติ2</label>
                                        <select name="owner2" id="owner2" class="form-control select_owner">
                                            <option value="" selected>เลือกผู้อนุมัติ</option>
                                            <?php
                                            if ($sql_owner) {
                                                foreach ($sql_owner as $row) {
                                                    $selected = "";
                                                    if($data['OWNER2'] == $row->ID){
                                                        $selected = "selected";
                                                    }
                                                    echo "<option value='$row->ID' $selected>$row->NAME</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="">ผู้อนุมัติ3</label>
                                        <select name="owner3" id="owner3" class="form-control select_owner">
                                            <option value="" selected>เลือกผู้อนุมัติ</option>
                                            <?php
                                            if ($sql_owner) {
                                                foreach ($sql_owner as $row) {
                                                    $selected = "";
                                                    if($data['OWNER3'] == $row->ID){
                                                        $selected = "selected";
                                                    }
                                                    echo "<option value='$row->ID' $selected>$row->NAME</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        $(document).on('change', '#update_data_owner', function() {

            let url_update_profile = new URL('profile/ctl_profile/update_profile', domain);

            var data = new FormData();
            data.append('id', $("#user-id").val());
            data.append('owner1', $("#owner1").val());
            data.append('owner2', $("#owner2").val());
            data.append('owner3', $("#owner3").val());

            let option = {
                method: 'POST',
                body: data,
            }
            fetch(url_update_profile, option)
                .then(res => res.json())
                .then((resp) => {
                    console.log(resp);
                    return false;
                })
        })

    })
</script>