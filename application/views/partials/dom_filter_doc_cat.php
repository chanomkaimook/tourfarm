
<input type="hidden" id="hidden_doc_cat" name="hidden_doc_cat">
<div class="form-inline">
    <div class="form-group w-100">
        <select class="form-control form-control-sm" id="item_doc_cat">
            <option value="" selected>ประเภท</option>
            <?php
            $this->load->library('Document');

            $data_doc_cat = $this->document->fetch_doc_cat();
            foreach($data_doc_cat as $key => $val){
                echo '<option value="'.$key.'">'.$val['text_cat'].'</option>';
            }
            ?>


        </select>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change','#item_doc_cat',function(){
            $('#hidden_doc_cat').val($(this).val())
        })
    })
</script>