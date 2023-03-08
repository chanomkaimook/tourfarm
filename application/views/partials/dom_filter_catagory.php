<?php
$filter_sql_cat = $this->db->select('ID,NAME_TH')
    ->from('item_catagory')
    ->where('status_offview is null', null, false)
    ->where('status', 1)
    ->get();
$filter_num_cat = $filter_sql_cat->num_rows();
?>
<input type="hidden" id="hidden_catagory" name="hidden_catagory">
<div class="form-inline">
    <div class="form-group w-100">
        <select class="form-control form-control-sm" id="item_filter_catagory">
            <option value="" selected>หมวดหมู่</option>
            <?php
            if ($filter_num_cat) :
                foreach ($filter_sql_cat->result() as $filter_row_cat) :
                    echo "<option value=\"$filter_row_cat->ID\"> $filter_row_cat->NAME_TH </option>";
                endforeach;
            endif;
            ?>

        </select>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('change','#item_filter_catagory',function(){
            $('#hidden_catagory').val($(this).val())
        })
    })
</script>