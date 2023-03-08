<?php
// path
$path_navbar = 'application/views/partials/e_navbar.php';
$path_topbar = 'application/views/partials/e_topbar.php';
$path_sidebar = 'application/views/partials/e_sidebar_menu.php';
$path_footer = 'application/views/partials/e_footer.php';
$path_head_link = 'application/views/partials/e_head_link.php';
$path_head_title = 'application/views/partials/e_head_title.php';
$path_script_begin = 'application/views/partials/e_script_begin.php';
$path_script_end = 'application/views/partials/e_script_end.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head title -->
    <?php include($path_head_title); ?>

    <!-- Link main -->
    <?php include($path_head_link); ?>
</head>

<body>

    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include($path_navbar); ?>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php include($path_sidebar); ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <?php include($path_topbar); ?>
            <?php echo $template['body']; ?>

            <!-- Footer Start -->
            <?php include($path_footer); ?>
            <!-- end Footer -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Script Begin -->
    <?php include($path_script_begin); ?>

    <!-- Script End -->
    <?php include($path_script_end); ?>

</body>

</html>