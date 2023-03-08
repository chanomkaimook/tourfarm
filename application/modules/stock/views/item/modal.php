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

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">ชื่อ</label>
                            <input class="form-control" type="text" id="item_name" name="item_name" placeholder="ระบุ" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">หมวดหมู่ <span class="temp-text-catagory font-weight-bold"></span></label>
                            <select name="item_catagory" id="item_catagory" class="form-control" data-toggle="select2" required>
                                <option value="">ระบุ</option>
                                <?php
                                    if($catagory){
                                        foreach($catagory as $row){
                                            echo "<option value=\"$row->ID\">$row->NAME_TH</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">บาร์โค๊ด</label>
                            <input class="form-control" type="text" id="item_barcode" name="item_barcode" placeholder="ระบุ" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">รหัส mac5</label>
                            <input class="form-control" type="text" id="item_mac5" name="item_mac5" placeholder="ระบุ">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">ราคาทุน</label>
                            <input class="form-control" type="number" step="0.01" id="item_cost" name="item_cost" placeholder="ระบุ">
                        </div>
                    </div>

                    <div class="html_statusoff"></div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="">รูปภาพ</label>
                            <input type="file" class="form-control-file border" id="imgFile" >
                            <div id="image_temp" class="text-center"></div>
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