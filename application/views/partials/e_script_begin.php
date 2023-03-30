<script src="<?= base_url('') ?>asset/js/vendor.min.js"></script>

<script src="<?= base_url('') ?>asset/libs/sweetalert2/sweetalert2.min.js"></script>

<script src="<?= base_url('') ?>asset/plugins/bootstrap4-toggle/js/bootstrap4-toggle.min.js"></script>

<script src="<?= base_url('') ?>asset/libs/select2/select2.min.js"></script>

<script src="<?= base_url('') ?>asset/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>

<!-- Boostrap select  -->
<script src="<?= base_url('') ?>asset/libs/bootstrap-select/bootstrap-select.min.js"></script>

<!-- <script src="<?= base_url('') ?>asset/js/pages/form-advanced.init.js"></script> -->
<script>
    $(document).ready(function() {
        // inisialize datepicker
        $("#datestart-autoclose").datepicker({
            autoclose: !0,
            todayHighlight: !0,
            format: 'dd-mm-yyyy',
        })
        $("#dateend-autoclose").datepicker({
            autoclose: !0,
            todayHighlight: !0,
            format: 'dd-mm-yyyy'
        })
        /* var dateTypeVar = $('#datestart-autoclose').datepicker('getDate');
        $.datepicker.formatDate('Y-m-d', dateTypeVar); */
    })
</script>