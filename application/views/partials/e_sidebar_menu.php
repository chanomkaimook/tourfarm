    <!-- ========== Left Sidebar Start ========== -->
    <div class="left-side-menu">

        <div class="slimscroll-menu">

            <!--- Sidemenu -->
            <div id="sidebar-menu">

                <ul class="metismenu" id="side-menu">

                    <li class="menu-title">farmchokchai</li>

                    <li class="<?= check_permit_menu('dashboard') ?>">
                        <a href="javascript: void(0);">
                            <i class="fe-airplay"></i>
                            <span> รายงาน </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= site_url('dashboard/ctl_dashboard') ?>">Dashboard</a></li>
                        </ul>
                    </li>

                    <li class="<?= check_permit_menu('calendar') ?>">
                        <a href="#">
                            <i class="fas fa-calendar-alt"></i>
                            <span> รอบจองเข้าชม </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= site_url('calendar/ctl_manage') ?>">จัดการรอบ</a></li>
                            <li><a href="<?= site_url('calendar/ctl_manage/datatable') ?>">ตารางจอง</a></li>
                            <li><a href="<?= site_url('calendar/ctl_customer') ?>">ลูกค้า</a></li>
                            <li><a href="<?= site_url('calendar/ctl_round') ?>">รอบเข้าชม</a></li>
                        </ul>
                    </li>

                        <li class="<?= check_permit_menu('admin') ?>">
                            <a href="#">
                                <i class="fas fa-tools"></i>
                                <span>ผู้ดูแล</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="<?= site_url('admin/ctl_register') ?>">ลงทะเบียน</a></li>
                                <li><a href="<?= site_url('admin/ctl_user') ?>">ผู้ใช้งาน</a></li>
                            </ul>
                        </li>

                </ul>

            </div>
            <!-- End Sidebar -->

            <div class="clearfix"></div>

        </div>
        <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->
