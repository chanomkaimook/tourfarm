<?php
$this->load->library('Status');
$status_payment = $this->status->get_status_payment();

?>
<!-- Modal Add Event -->
<div class="modal fade none-border" id="event-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add New Eventas</h4>
            </div>
            <div class="modal-body pb-0"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success save-event waves-effect waves-light">Create event</button>
                <button type="button" class="btn btn-danger delete-event waves-effect waves-light" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Task -->

<div class="modal fade none-border" id="add-category" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body pb-0">
                <form class="form was-validated" autocomplete="off" id="form_add" action="">
                    <input type="hidden" id="method" name="method" value="">
                    <input type="hidden" id="item_id" name="item_id" value="">

                    <div class="form-group">
                        <label class="control-label">ชื่อทัวร์</label>
                        <!-- <input class="form-control" placeholder="ระบุ" type="text" name="category-name" required /> -->
                        <input type="text" id="tags" name="customer" class="form-control" required>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">ผู้ติดต่อ</label>
                                <input type="text" id="agent_name" name="agent_name" class="form-control">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">เบอร์ติดต่อ</label>
                                <input type="text" id="agent_contact" name="agent_contact" class="form-control">

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="control-label">โอนเงิน</label>
                                <select id="payment" name="payment" class="selectpicker show-tick" data-style="btn-outline-secondary">
                                    <?php
                                    foreach ($status_payment as $key => $row) {
                                        $selected = '';
                                        if ($key == 0) {
                                            $selected = 'selected';
                                        }

                                        echo '<option value="' . $row->ID . '" data-name="' . $row->NAME . '" ' . $selected . '>' . $row->NAME . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="control-label">รอบ</label>

                                <select id="round" name="round" class="selectpicker show-tick" data-style="btn-outline-secondary">
                                    <?php
                                    foreach ($round as $key => $row) {
                                        $selected = '';
                                        if ($key == 0) {
                                            $selected = 'selected';
                                        }

                                        echo '<option value="' . $row->ID . '" data-name="' . $row->NAME . '" ' . $selected . '>' . $row->NAME . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="control-label">จำนวนคน</label>
                                <input id="totals" name="totals" type="text" class="touchspin int_only" value="1">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="control-label">วันจอง</label>
                                <input type="text" class="form-control" placeholder="วันที่" id="booking_date">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="control-label">หมายเหตุ</label>
                                <textarea class="form-control" rows="3" id="remark" name="remark"></textarea>
                            </div>
                        </div>
                    </div>

                    <style>
                        .block_btn {
                            column-gap: 1rem;
                        }
                    </style>
                    <div class="row">
                        <div class="col-12">

                            <div class="d-flex flex-column flex-sm-row justify-content-end block_btn">

                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-success btn-block waves-effect waves-light">บันทึก</button>
                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
