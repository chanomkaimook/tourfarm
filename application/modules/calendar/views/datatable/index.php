<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="">
            <div class="card-box table-responsive">

                <div class="row">
                    <div class="col-md-12">
                        <!-- Button trigger modal  -->
                        <button type="button" class="btn btn-primary btn_add_item" data-toggle="modal" data-target="#add-category">เพิ่มรายการ</button>
                        <div class="filter">
                            <?php require_once 'application/views/partials/e_filter_doc_order.php'; ?>
                        </div>
                    </div>
                </div>
                <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>action</th>
                            <th>ชื่อ</th>
                            <th>วันจอง</th>
                            <th>รอบ</th>
                            <th>โอนเงิน</th>
                            <th>ผู้ติดต่อ</th>
                            <th>เบอร์</th>
                            <th>จำนวน</th>
                            <th>ผู้สร้าง</th>
                            <th>วันที่ทำรายการ</th>
                            <th>หมายเหตุ</th>
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
        // inisialize datepicker
        $("#booking_date").datepicker({
            autoclose: !0,
            todayHighlight: !0,
            format: 'dd/mm/yy',
        })
        // var dateTypeVar = $('#datestart-autoclose').datepicker('getDate');
        // console.log($.datepicker.formatDate('Y-m-d', dateTypeVar));

        $(".touchspin").TouchSpin({
            min: 1,
            max: 100,
        })
    })
</script>

<!-- Modal -->
<?php include(APPPATH . '/modules/calendar/views/manage/modal.php') ?>
<!-- End Modal -->

<!-- Script -->
<?php require_once('script.php') ?>
<!-- End Script -->