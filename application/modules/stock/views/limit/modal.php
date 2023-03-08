<div id="modal_from" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal was-validated" autocomplete="off" id="dataform" action="" class="was-validated">
                    <input type="hidden" id="method" name="method" value="">
                    <input type="hidden" id="hidden_id" name="hidden_id" value="1"> <!-- set 1 for prevent null-->

                    <div id="temp_item" class="form-group row">
                        <div class="col-12">
                            <label for="">สินค้า <span class="font-weight-bold"></span></label>
                            <select name="item_name" id="item_name" class="form-control" data-toggle="select2" required>
                                <option value="">ระบุ</option>
                                <?php
                                if ($item) {
                                    foreach ($item as $row) {
                                        echo "<option value=\"$row->ITEM_ID\">$row->ITEM_NAME</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="temp_item_name" class="form-group row d-none">
                        <div class="col-12">
                            <label class="h2"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">จำนวนต่ำสุดที่จะแจ้งเตือน</label>
                            <input class="form-control" type="number" id="item_min" name="item_min" placeholder="ระบุ">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">จำนวนสูงสุดที่กำหนด</label>
                            <input class="form-control" type="number" id="item_max" name="item_max" placeholder="ระบุ">
                        </div>
                    </div>

                    <div class="form-group row text-center mt-2">
                        <div class="col-12">
                            <button class="btn btn-md btn-block btn-primary btn_submit" type="submit">เพิ่ม</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>

    </div>
</div>