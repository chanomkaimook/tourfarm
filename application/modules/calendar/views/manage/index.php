<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="">
            <div class="card-box">
                <div class="row">
                    <div class="col-lg-3">
                        <a href="#" data-toggle="modal" data-target="#add-category" class="btn btn-lg btn-primary btn-block waves-effect mt-2 waves-light">
                            <i class="fa fa-plus"></i> เพิ่มจอง
                        </a>
                        <div class="mt-3">
                            <p class="text-muted">รายการที่ยังไม่ลงวันจอง
                                <br>
                                <span class="text-warning">
                                    <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>รอโอน
                                </span>
                                <span class="text-success">
                                    <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>โอนแล้ว
                                </span>
                            </p>
                        </div>
                        <div id="external-events" class="order_list">

                            <!-- <br>
                            <div class="external-event bg-soft-success text-success" data-class="bg-success">
                                <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>New Theme Release
                            </div>
                            <div class="external-event bg-soft-info text-info" data-class="bg-info">
                                <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>My Event
                            </div>
                            <div class="external-event bg-soft-warning text-warning" data-class="bg-warning">
                                <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>Meet manager
                            </div>
                            <div class="external-event bg-soft-purple text-purple" data-class="bg-purple">
                                <i class="mdi mdi-checkbox-blank-circle mr-1 vertical-middle"></i>Create New theme
                            </div> -->
                        </div>

                        <!-- checkbox -->
                        <div class="checkbox checkbox-primary mt-4 d-none">
                            <input type="checkbox" id="drop-remove" checked=checked>
                            <label for="drop-remove">
                                Remove after drop
                            </label>
                        </div>
                    </div> <!-- end col-->
                    <div class="col-lg-9">
                        <div id="calendar"></div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>


            <!-- Modal -->
            <?php require_once('modal.php') ?>
            <!-- End Modal -->
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
            dateFormat: 'dd/mm/yy',
        })
        // var dateTypeVar = $('#datestart-autoclose').datepicker('getDate');
        // console.log($.datepicker.formatDate('Y-m-d', dateTypeVar));

        $(".touchspin").TouchSpin({
            min: 1,
            max: 100,
        })
    })
</script>
<?php require_once('script_autocustomer.php') ?>
<?php require_once('script.php') ?>