<div class="col-lg-5">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Recent Transactions</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th>Factory</th>
							<th>Date/Time</th>
							<th>Action</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						{details}
						<tr>
							<td>{factory}</td>
							<td>{timestamp}</td>
							<td>{action}</td>
							<td>{amount}</td>
						</tr>
						{/details}
					</tbody>
				</table>
			</div>
			<div class="text-right">
				<a href="#">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
	</div>
</div>
