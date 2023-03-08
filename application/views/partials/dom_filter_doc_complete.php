
<input type="hidden" id="hidden_doc_complete" name="hidden_doc_complete">
<div class="form-inline">
    <div class="form-group w-100">
        <select class="form-control form-control-sm" id="item_doc_complete">
            <option value="" selected>สถานะ</option>

            <option value="1" >รอ</option>
            <option value="2" >สำเร็จ</option>
            <option value="3" >ตัด</option>
        </select>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change','#item_doc_complete',function(){
            $('#hidden_doc_complete').val($(this).val())
        })
    })
</script>