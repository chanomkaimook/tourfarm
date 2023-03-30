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
                        <div class="col-sm-6">
                            <label for="">เวลาเริ่มรอบ <span class="font-weight-bold"></span></label>
                            <select name="time_start" id="time_start" class="form-control" required>
                                <option value="">ระบุ</option>
                                <?php
                                    if($time){
                                        foreach($time as $row){
                                            $timming = toTime($row->NAME,'H:i');
                                            echo "<option value=\"$row->NAME\" >$timming</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="">เวลาจบรอบ <span class="font-weight-bold"></span></label>
                            <select name="time_end" id="time_end" class="form-control" required>
                                <option value="">ระบุ</option>
                                <?php
                                    if($time){
                                        foreach($time as $row){
                                            $timming = toTime($row->NAME,'H:i');
                                            echo "<option value=\"$row->NAME\" >$timming</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="html_statusoff"></div>

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