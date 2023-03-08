<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-4">
                <div class="card-box">


                    <style>
                        #section_button .card-body {
                            cursor: pointer;
                            opacity: 0.5;
                            background-color:rgba(255, 255, 255, 0.5);
                        }

                        #section_button .card-body:hover {
                            opacity:1.0;
                            background-color:unset;
                        }
                    </style>
                    <div id="section_button">
                        <div class="row text-center">
                            <div id="btn_add" data-value="add" class="col-sm-6 mb-3 button">
                                <div class="card text-white bg-primary h-100">
                                    <div class="card-body">
                                        <div class="display-4">รับ</div>

                                    </div>
                                </div>
                            </div>
                            <div id="btn_cut" data-value="cut" class="col-sm-6 mb-3 button">
                                <div class="card text-white bg-warning h-100">
                                    <div class="card-body">
                                        <div class="display-4">เบิก</div>

                                    </div>
                                </div>
                            </div>
                            <div id="btn_sale" data-value="sale" class="col-sm-6 mb-3 button">
                                <div class="card text-white bg-success h-100">
                                    <div class="card-body">
                                        <div class="display-4">ขาย</div>

                                    </div>
                                </div>
                            </div>
                            <div id="btn_lost" data-value="lost" class="col-sm-6 mb-3 button">
                                <div class="card text-white bg-danger h-100">
                                    <div class="card-body">
                                        <div class="display-4">เสีย</div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="section_document" class="d-none">
                        <button type="button" class="close"><i class="fas fa-arrow-left"></i></button>
                        <div class="h4">ค้นหาสินค้า</div>
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <select name="item" id="item" class="form-control" data-toggle="select2" placeholder="asdasd" required>
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

                    <div id="section_list" class="d-none mt-4">
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
            </div>
            <div class="col-lg-8">
                <div class="card-box table-responsive">
                    <div class="row">
                        <div class="col-md-12">
     
                            <div class="filter">
                            <?php require_once 'application/views/partials/e_filter_doc_cat.php'; ?>
                            <?php require_once 'application/views/partials/e_filter_calendar.php'; ?>
                        </div>
                        </div>
                    </div>
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
<!-- End Script -->