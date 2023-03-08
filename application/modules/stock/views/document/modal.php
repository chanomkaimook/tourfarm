<div id="modal_from" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal was-validated" autocomplete="off" id="dataitem" action="">

                    <input type="hidden" id="hidden_id" name="hidden_id" value="1"> <!-- set 1 for prevent null-->
                    <input type="hidden" id="hidden_table_id" name="hidden_table_id" value="">
                    <input type="hidden" id="hidden_table_name" name="hidden_table_name" value="">

                    <div class="form-group row">
                        <div class="col-12">
                            <div id="data_item"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <input class="form-control form-control-lg" type="number" id="item_total" name="item_total" placeholder="จำนวน" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12 form-inline">
                            <div class="custom-control custom-radio pr-2">
                                <input type="radio" id="normal" name="customRadio" class="custom-control-input" checked>
                                <label class="custom-control-label" for="normal">ปกติ</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="hold" name="customRadio" class="custom-control-input">
                                <label class="custom-control-label" for="hold">ยืมเข้า/ให้ยืม</label>
                            </div>
                        </div>
                    </div>

                    <div class="input_suppiler form-group row">
                        <div class="col-12">
                            <label for="">ผู้ติดต่อ <span class="temp-text-node font-weight-bold"></span></label>
                            <select name="node_id" id="node_id" class="form-control" data-toggle="select2">
                                <option value="">ระบุ</option>
                                <?php
                                if ($node) {
                                    foreach ($node as $row) {
                                        echo "<option value=\"$row->ITEM_ID\">$row->ITEM_NAME</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <textarea class="form-control" rows="3" id="remark" name="remark"></textarea>
                        </div>
                    </div>

                    <div class="form-group row text-center mt-2">
                        <div class="col-12">
                            <button class="btn btn-md btn-block btn-primary btn_add_item" type="submit">เพิ่ม</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>

    </div>
</div>