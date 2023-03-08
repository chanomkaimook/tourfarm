    <!-- ========== Left Sidebar Start ========== -->
    <div class="left-side-menu">

        <div class="slimscroll-menu">

            <!--- Sidemenu -->
            <div id="sidebar-menu">

                <ul class="metismenu" id="side-menu">

                    <li class="menu-title">steakhouse</li>

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
                            <i class="far fa-calendar-alt"></i>
                            <span> คลังสินค้า </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= site_url('stock/ctl_stock') ?>">คลังสินค้า</a></li>
                            <li><a href="<?= site_url('stock/ctl_document') ?>">จัดการสินค้า</a></li>
                            <li><a href="<?= site_url('stock/ctl_document/order') ?>">สั่งสินค้า</a></li>
                            <li><a href="<?= site_url('stock/ctl_document/documentall') ?>">เอกสาร</a></li>
                            <li><a href="<?= site_url('stock/ctl_docwaite') ?>">เอกสารที่รอ <span class="badge badge-pink noti-icon-badge total_doc_waite d-none"></span></a></li>
                            <li><a href="<?= site_url('stock/ctl_item') ?>">สินค้า</a></li>
                            <li><a href="<?= site_url('stock/ctl_catagory') ?>">ประเภทสินค้า</a></li>
                            <li><a href="<?= site_url('stock/ctl_node') ?>">ผู้ติดต่อ</a></li>
                            <li><a href="<?= site_url('stock/ctl_setting/limit') ?>">ตั้งค่า</a></li>
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
                                <li><a href="<?= site_url('admin/ctl_nodecatagory') ?>">ประเภทผู้ติดต่อ</a></li>
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
