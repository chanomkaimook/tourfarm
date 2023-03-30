
<input type="hidden" id="hidden_payment" name="hidden_payment">
<div class="form-inline">
    <div class="form-group w-100">
        <select class="form-control form-control-sm" id="item_payment">
            <option value="" selected>ชำระ</option>

            <option value="4" >รอโอน</option>
            <option value="5" >โอนแล้ว</option>
        </select>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change','#item_payment',function(){
            $('#hidden_payment').val($(this).val())
        })
    })
</script>