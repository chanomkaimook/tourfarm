<div class="content">

	<!-- Start Content-->
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">

				<div class="card-box">
					<div class="text-center">

						<div class="row">
							<div class="col-lg-2 col-sm-6">
								<div class="my-3">
									<h3 class="mb-2 text-primary score doc_import_item">25563</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ใบรับ</p>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="my-3">
									<h3 class="mb-2 text-warning score doc_issue_item">6952</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ใบเบิก</p>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="my-3">
									<h3 class="mb-2 text-success score doc_bill_item">18361</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ใบขาย</p>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="my-3">
									<h3 class="mb-2 text-danger score doc_lost_item">250</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ใบเสีย</p>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="my-3">
									<h3 class="mb-2 text-purple score doc_order_item">18361</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ใบสั่ง</p>
								</div>
							</div>
							<div class="col-lg-2 col-sm-6">
								<div class="my-3">
									<h3 class="mb-2 text-secondary score doc_node_item">250</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ใบรอ</p>
								</div>
							</div>
						</div>

					</div>
				</div>





			</div>
		</div>
		<!-- end row -->

		<div class="row">
			<div class="col-12">
				<div class="card-box table-responsive">
					<div class="row">
						<div class="col-md-12">

							<div class="filter">
								<?php require_once 'application/views/partials/e_filter_doc_cat.php'; ?>
								<?php require_once 'application/views/partials/e_filter_calendar.php'; ?>
							</div>
						</div>
					</div>
					<table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr>
								<th>ประเภท</th>
								<th>สินค้า</th>
								<th>จำนวน</th>
								<th>ผู้ติดต่อ</th>
								<th>หมายเหตุ</th>
								<th>ผู้สร้าง</th>
								<th>วันที่ทำรายการ</th>
								<th>action</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- end row -->

	</div> <!-- end container-fluid -->

</div> <!-- end content -->

<!-- Script -->
<?php require_once('script.php') ?>
<!-- End Script -->