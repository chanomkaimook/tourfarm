
<input type="hidden" id="hidden_doc_supplier" name="hidden_doc_supplier">
<div class="form-inline">
    <div class="form-group w-100">
        <select class="form-control form-control-sm" id="item_doc_supplier">
            <option value="" selected>ผู้ติดต่อ</option>
            <?php
            $this->load->library('Document');

            $data_node = $this->document->fetch_node();
            foreach($data_node as $key => $val){
                if($val->NODE_CAT_ID == 2){
                    echo '<option value="'.$val->ID.'">'.$val->NAME_TH.'</option>';
                }
            }
            ?>


        </select>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change','#item_doc_supplier',function(){
            $('#hidden_doc_supplier').val($(this).val())
        })
    })
</script>