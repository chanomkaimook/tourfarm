<input type="hidden" id="hidden_datestart" name="hidden_datestart">
<input type="hidden" id="hidden_dateend" name="hidden_dateend">
<div class="">
    <div class="form-inline">
        <div class="form-group">

            <label class="d-none d-sm-block">วัน </label>
            <input type="text" class="form-control form-control-sm" placeholder="วันที่" data-date-format='yy-mm-dd' id="datestart-autoclose" name="datestart-autoclose">
        </div>
    </div>
</div>
<div class="">
    <div class="form-inline">
        <div class="form-group">
            <label class="d-none d-sm-block">ถึงวัน</label>
            <input type="text" class="form-control form-control-sm" placeholder="วันที่สิ้นสุด" id="dateend-autoclose" name="dateend-autoclose">

        </div>

    </div>
</div>

<style>
    .filter input,
    .filter select {
        width: 6rem !important;
    }


    .filter .form-group {
        margin: 0
    }
</style>
<script>
    //
    // Filter
    if ($('.filter').find('input').length) {
        let ele = $('.filter')

        // create html width tool button
        ele.parents('.col-md-12').addClass('d-flex')
        ele.addClass('d-flex')

        ele.addClass('ml-auto')
        ele.addClass('bg-light')
        ele.addClass('px-2')
        ele.addClass('p-1')
    }

    $(document).ready(function() {
        $(document).on('change', '#datestart-autoclose', function() {
            var date_start_obj = $(this).datepicker('getDate');
            let t = new Date(date_start_obj);
            let item_month = (t.getMonth() + 1).toString().padStart(2, "0");
            let item_day = t.getDate().toString().padStart(2, "0")

            var date_start = t.getFullYear() + "-" + item_month + "-" + item_day

            $('#hidden_datestart').val(date_start)
        })

        $(document).on('change', '#dateend-autoclose', function() {
            var date_end_obj = $(this).datepicker('getDate');
            let t = new Date(date_end_obj);
            let item_month = (t.getMonth() + 1).toString().padStart(2, "0");
            let item_day = t.getDate().toString().padStart(2, "0")

            var date_end = t.getFullYear() + "-" + item_month + "-" + item_day

            $('#hidden_dateend').val(date_end)
        })

        $(document).on('click', '.button_search', function() {
            $('#datatable').DataTable().ajax.reload(null, false);
        })
    })
</script>