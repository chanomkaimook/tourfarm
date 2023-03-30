<div class="content">

	<!-- Start Content-->
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">

				<div class="card-box">
					<div class="text-center">

						<div class="row">
							<div class="col-sm-4 col-xs-12">
								<div class="my-3">
									<h3 class="mb-2 text-success score order">0</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">จองวันนี้</p>
								</div>
							</div>
							<div class="col-sm-4 col-xs-12">
								<div class="my-3">
									<h3 class="mb-2 text-info score order_customer">0</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">จำนวนคน</p>
								</div>
							</div>
							<div class="col-sm-4 col-xs-12">
								<div class="my-3">
									<h3 class="mb-2 text-danger score order_waite">0</h3>
									<p class="text-uppercase mb-1 font-13 font-weight-medium">ยังไม่โอนเงิน</p>
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
								<?php require_once 'application/views/partials/e_filter_doc_order.php'; ?>
							</div>
						</div>
					</div>
					<table id="datatable" class="table dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr>
								<th>รอบ</th>
								<th>ชื่อ</th>
								<th>วันจอง</th>
								<th>ผู้ติดต่อ</th>
								<th>เบอร์</th>
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

<style>
	.sk-circle {
		margin: 0px auto;
		height: 26px;
	}
</style>
<!-- Script -->
<?php require_once('script.php') ?>
<!-- End Script -->