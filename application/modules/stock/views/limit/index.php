<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="">
            <div class="card-box table-responsive">

                <div class="row">
                    <div class="col-md-12">
                        <!-- Button trigger modal  -->
                        <button type="button" class="btn btn-primary btn_add_item" data-toggle="modal" data-target="#modal_from">เพิ่มรายการ</button>
                        <div class="filter">
                            <?php require_once 'application/views/partials/e_filter_calendar.php'; ?>
                        </div>
                    </div>
                </div>
                <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>ชื่อ</th>
                            <th>ประเภท</th>
                            <th>ค่าต่ำสุด</th>
                            <th>ค่าสูงสุด</th>
                            <th>ผู้ทำ</th>
                            <th>วันที่ทำรายการ</th>
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
<?php require_once('modal.php') ?>
<!-- End Modal -->

<!-- Script -->
<?php require_once('script.php') ?>
<!-- End Script -->