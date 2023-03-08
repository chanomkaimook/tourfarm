<style>
    .barcodeimg img {
        height: 35px;
        width: auto;
    }
</style>
<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">
        <div class="">
            <div class="card-box table-responsive">

                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            <input type="text" id="barcode_search" name="barcode_search" class="form-control bg-light" placeholder="สแกน barcode">
                        </div>

                        <div class="filter">
                            <?php require_once 'application/views/partials/e_filter_catagory.php'; ?>
                            <?php require_once 'application/views/partials/e_filter_calendar.php'; ?>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="zero" name="zero" val="">
                <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th class="select">หมวดหมู่</th>
                            <th>คงคลัง</th>
                            <th>เบิกออก</th>
                            <th>รับเข้า</th>
                            <th>ขาย</th>
                            <th>เหลือ</th>
                            <!-- <th>รอตัด</th> -->
                            <th>รอรับ</th>
                            <!-- <th>เหลือจริง</th> -->
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
<?php require_once('modal_stock.php') ?>
<!-- End Modal -->

<!-- Script -->
<?php require_once('script.php') ?>
<!-- End Script -->