				<!-- <div class="export">
					<div class="entry">
						<a href="#" id="export_csv" class="btn-export" data-type="csv">Export as CSV</a>
					</div>
					<div class="entry">
						<a href="#" id="export_pdf" class="btn-export" data-type="pdf">Export as PDF</a>
					</div>

					<div class="hidden" id="exportable_div">
						<table id="exportable_table" class="table">
							<tbody></tbody>
						</table>
					</div>
				</div> -->
				<div class="export">
					<a id="export_csv" href="#" data-type="csv" class="btn btn-info btn-export">
						<i class="fa fa-file-excel-o"></i> Export as CSV
					</a>

					<a id="export_pdf" class="btn btn-warning btn-export" data-type="pdf">
						<i class="fa fa-file-pdf-o"></i> Export as PDF
					</a>

					<div class="hidden" id="exportable_div">
						<table id="exportable_table" class="table">
							<tbody></tbody>
						</table>
					</div>
				</div>

<?php  if (isset($_GIVEN_URL) && trim($_GIVEN_URL) != ""): ?>
	<script>
		$(function () {
			$("#wpspy_url").trigger("change");
		});
	</script>
<?php endif; ?>