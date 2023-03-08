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
                            <label for="">จำนวน</label>
                            <input class="form-control" type="text" id="item_total" name="item_total" placeholder="ระบุ" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <textarea class="form-control" rows="3" id="remark" name="remark"></textarea>
                        </div>
                    </div>

                    <!-- <div class="form-group row">
                        <div class="col-12">
                            <label for="">รูปภาพ</label>
                            <input type="file" class="form-control-file border" id="imgFile" >
                            <div id="image_temp" class="text-center"></div>
                        </div>
                    </div> -->

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