<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('asset/images/favicon.ico') ?>">

    <link href="<?= base_url('') ?>asset/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="<?= base_url('asset/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="<?= base_url('asset/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('asset/css/app.min.css') ?>" rel="stylesheet" type="text/css" id="app-stylesheet" />

</head>
<style>
    body {
        margin: 0px;
        padding: 0px;
    }
</style>

<?php
$this->session->sess_destroy();
if ($this->session->has_userdata('user_code')) {

    print_r($this->session->userdata());
}

?>

<body>
    <div class="authentication-bg bg-primary authentication-bg-pattern d-flex align-items-center pb-0 vh-100">

        <div class="account-pages w-100 mt-5 mb-5">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mb-0">

                            <div class="card-body p-4">

                                <div class="account-box">
                                    <div class="account-logo-box">
                                        <div class="text-center">
                                            <!-- <a href="index.html">
                                                <img src="<?= base_url('asset/images/logo-dark.png') ?>" alt="" height="30">
                                            </a> -->
                                        </div>
                                        <div class="text-center">
                                            <h2 class="text-uppercase mb-1  text-center">STOCK ร้านรังสิต</h2>
                                            <div class="small text-center">ระบบ stock สินค้าร้าน steakhouse</div>
                                        </div>
                                    </div>

                                    <div class="account-content mt-4">
                                        <form class="form-horizontal" autocomplete="off" id="login">

                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <label for="">ชื่อผู้ใช้</label>
                                                    <input type="text" id="input_username" name="input_username" class="form-control" placeholder="ชื่อผู้ใช้" required>

                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <a class="text-muted float-right"><small>ลืมรหัสผ่าน?</small></a>
                                                    <label for="">รหัสผ่าน</label>
                                                    <input type="password" id="input_password" name="input_password" class="form-control" placeholder="รหัสผ่าน" required>

                                                </div>
                                            </div>

                                            <div class="form-group row text-center mt-2">
                                                <div class="col-12">
                                                    <button class="btn btn-md btn-block btn-primary waves-effect waves-light" id="btn_login" type="submit">เข้าสู่ระบบ</button>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <?php echo anchor('register/ctl_register', 'หากไม่มีรหัส ลงทะเบียน', 'title="ลงทะเบียน"'); ?>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->
    </div>
    <script src="<?= base_url('') ?>asset/js/jquery/jquery-3.5.1.min.js"></script>

    <!-- Sweet alert -->
    <script src="<?= base_url('') ?>asset/libs/sweetalert2/sweetalert2.min.js"></script>

    <script>
        // url
        // let domain = window.location.protocol + '//' + window.location.hostname + '/' + window.location.pathname.split('/')[1] + '/'
        let domain = window.location.origin
        $(document).ready(function() {

            $(document).on('submit', '#login', function() {
                login()

                return false;
            })

            function login() {
                let url_check_login = new URL('login/ctl_login/check_login', domain);

                let data = new FormData();
                data.append('user_name', $('#input_username').val());
                data.append('user_password', $('#input_password').val());

                fetch(url_check_login, {
                        method: 'POST',
                        body: data,
                    })
                    .then(res => res.json())
                    .then((resp) => {
                        if (resp.error != 0) {
                            swal.fire('ผิดพลาด', resp.text, 'warning')
                        } else {
                            window.location.reload();

                        }
                    })
            }


        })
    </script>

</body>

</html>