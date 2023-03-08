<input type="hidden" id="hidden_itemhide" name="hidden_itemhide">
<div class="form-inline">
    <div class="form-group w-100">
        <select class="form-control form-control-sm" id="item_filter_statusview">
            <option value="" selected>สถานะ</option>
            <option value="all">ปกติ</option>
            <option value="1">ซ่อน</option>

        </select>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change', '#item_filter_statusview', function() {
            $('#hidden_itemhide').val($(this).val())
        })
    })
</script>