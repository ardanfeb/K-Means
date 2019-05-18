<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dataset extends CI_Controller {

	public function index() {
		$this->load->view('home');
	}

	public function wine() {
		$data = $this->db->get('wine');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="panel-title">Dataset <b>Wine</b></h3></div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>ID</th>
							<th>Alcohol</th>
							<th>Malic Acid</th>
							<th>Ash</th>
							<th>Alcalinity of Ash</th>
							<th>Magnesium</th>
							<th>Total Phenols</th>
							<th>Flavanoids</th>
							<th>Nonflavanoid Phenols</th>
							<th>Proanthocyanins</th>
							<th>Color Intensity</th>
							<th>Hue</th>
							<th>OD280/OD315 of Diluted Wines</th>
							<th>Proline</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->b; ?></td>
							<td><?php echo $rows->c; ?></td>
							<td><?php echo $rows->d; ?></td>
							<td><?php echo $rows->e; ?></td>
							<td><?php echo $rows->f; ?></td>
							<td><?php echo $rows->g; ?></td>
							<td><?php echo $rows->h; ?></td>
							<td><?php echo $rows->i; ?></td>
							<td><?php echo $rows->j; ?></td>
							<td><?php echo $rows->k; ?></td>
							<td><?php echo $rows->l; ?></td>
							<td><?php echo $rows->m; ?></td>
							<td><?php echo $rows->n; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}

	public function iris() {
		$data = $this->db->get('iris');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="panel-title">Dataset <b>Iris</b></h3></div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>ID</th>
							<th>Sepal Length</th>
							<th>Sepal Width</th>
							<th>Petal Length</th>
							<th>Petal Width</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->a; ?></td>
							<td><?php echo $rows->b; ?></td>
							<td><?php echo $rows->c; ?></td>
							<td><?php echo $rows->d; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>Dalam centimeter (cm)</p>
			</div>
		</div>

		<!-- Data tables -->

		<?php
	}

	public function glass() {
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="panel-title">Dataset <b>Glass</b></h3></div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>ID</th>
							<th>RI</th>
							<th>Na</th>
							<th>Mg</th>
							<th>Al</th>
							<th>Si</th>
							<th>K</th>
							<th>Ca</th>
							<th>Ba</th>
							<th>Fe</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>213</td>
							<td>1.51651</td>
							<td>14.38</td>
							<td>0.00</td>
							<td>1.94</td>
							<td>73.61</td>
							<td>0.00</td>
							<td>8.48</td>
							<td>1.57</td>
							<td>0.00</td>
						</tr>
						<tr>
							<td>214</td>
							<td>1.51711</td>
							<td>14.23</td>
							<td>0.00</td>
							<td>2.08</td>
							<td>73.36</td>
							<td>0.00</td>
							<td>8.62</td>
							<td>1.67</td>
							<td>0.00</td>
						</tr>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}

	public function ujicoba() {
		$data = $this->db->get('ujicoba');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Dataset <b>Ujicoba</b></h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>ID</th>
							<th>Var Satu</th>
							<th>Var Dua</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->a; ?></td>
							<td><?php echo $rows->b; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}

	public function datasetsatu() {
		$data = $this->db->get('dataset_satu');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Dataset <b>Testing Satu</b></h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>Id</th>
							<th>A</th>
							<th>B</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->a; ?></td>
							<td><?php echo $rows->b; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}

	public function datasetdua() {
		$data = $this->db->get('dataset_dua');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Dataset <b>Testing Dua</b></h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>Id</th>
							<th>A</th>
							<th>B</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->a; ?></td>
							<td><?php echo $rows->b; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}

	public function datasettiga() {
		$data = $this->db->get('dataset_tiga');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Dataset <b>Testing Tiga</b></h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>Id</th>
							<th>A</th>
							<th>B</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->a; ?></td>
							<td><?php echo $rows->b; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}

	public function datasetempat() {
		$data = $this->db->get('dataset_empat');
		$row = $data->result();
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Dataset <b>Testing Empat</b></h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-responsive" id="dtables">
					<thead>
						<tr>
							<th>Id</th>
							<th>A</th>
							<th>B</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row as $rows): ?>
						<tr>
							<td><?php echo $rows->id; ?></td>
							<td><?php echo $rows->a; ?></td>
							<td><?php echo $rows->b; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table><br>
				<h4 class="pull-left" style="padding-right:20px;">Grafik</h4>
				<hr>
			</div>
			<script type="text/javascript">
				$(document).ready(function () {
					$('#dtables').DataTable({
						"aLengthMenu":[[5, 10, 15, -1], [5, 10, 15, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>
			<div class="panel-footer">
				<p>* Keterangan</p>
			</div>
		</div>
		<?php
	}
}
