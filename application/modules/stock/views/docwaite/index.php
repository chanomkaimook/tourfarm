<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="">
            <div class="card-box table-responsive">
                <div class="">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Button trigger modal  -->
                            <p>คลิกที่รายการในตารางข้อมูลเพื่อดูรายละเอียด</p>
                            <div class="filter">
                                <?php require_once 'application/views/partials/e_filter_doc_complete.php'; ?>
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
                                <th>สถานะ</th>
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
                <!-- end tab -->

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