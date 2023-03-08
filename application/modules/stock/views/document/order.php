<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="card-box table-responsive">
                    <div class="row">
                        <div id="section_document" class="w-100">
                            <button type="button" class="close"><i class="fas fa-arrow-left"></i></button>
                            <div class="h4">ค้นหาสินค้า</div>
                            <div class="form-row">

                                <div class="form-group col-sm-6">
                                    <select name="item" id="item" class="form-control" data-toggle="select2" placeholder="" required>
                                        <option></option>
                                        <?php
                                        if ($item) {
                                            foreach ($item as $row) {
                                                echo "<option value=\"$row->ITEM_ID\">$row->ITEM_NAME</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="text" class="form-control" id="item_search" placeholder="ระบุรหัสบาร์โค๊ด">
                                </div>
                            </div>

                            <form class="" id="dataform" action="">
                                <input type="hidden" id="method" name="method" value="">
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div id="section_list" class="d-none mb-4 col-md-12">
                            <div class="header-title">
                                <p id="list_head"></p>
                            </div>
                            <div id="list_date" class="text-right">วันที่ : 22-12-2023</div>
                            <div id="list_user" class="text-right">เจ้าหน้าที่ : คลังสินค้า ร้านรังสิต</div>
                            <div class="data_temp_item">
                                <!-- data before item add  -->
                            </div>
                            <div class="btn_itemtemp mt-2">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger btn_clear">ยกเลิกทั้งหมด</button>
                                    <button type="button" class="btn btn-primary btn_submit">บันทึกรายการ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="filter">
                                <?php require_once 'application/views/partials/e_filter_doc_order.php'; ?>
                                <?php require_once 'application/views/partials/e_filter_calendar.php'; ?>
                            </div>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ประเภท</th>
                                <th>สินค้า</th>
                                <th>จำนวน</th>
                                <th>ผู้ติดต่อ</th>
                                <th>หมายเหตุ</th>
                                <th>ผู้สร้าง</th>
                                <th>วันที่ทำรายการ</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- end row -->

    </div> <!-- end container-fluid -->

</div> <!-- end content -->

<!-- Modal -->
<?php require_once('modal.php') ?>
<!-- End Modal -->

<!-- Script -->
<?php require_once('script.php') ?>
<?php require_once('script_document.php') ?>
<?php require_once('script_order.php') ?>
<!-- End Script -->