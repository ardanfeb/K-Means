<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enhanced extends CI_Controller {

   function index() {}

   function data_wine() {
      # code...
   }

   function data_iris() {
      # code...
   }

   function data_glass() {
      # code...
   }

   function data_ujicoba() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Ujicoba
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Ujicoba</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <div class="row">
               <div class="col-md-12">
                  <table class="dtables table table-bordered table-responsive">
                     <?php
                        $data = $this->db->get('ujicoba');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <thead>
                        <tr class="active">
                           <td>#</td>
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <td><?php echo $i; ?></td>
                           <?php } ?>
                        </tr>
                     </thead>

                     <tbody>
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <td>
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 echo $jarak_data[$i][$j];
                              ?>
                           </td>
                           <?php } ?>
                        </tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $this->db->query("truncate table urutan_ujicoba");
                     $id = 1;
                     for ($i=1;$i<=$count;$i++) {
                     for ($j=$i+1;$j<=$count;$j++) { ?>
                     <tr>
                        <td class="active"><?php echo $i; ?></td>
                        <td class="active"><?php echo $j; ?></td>
                        <td><?php echo $jarak_data[$i][$j]; ?></td>
                     </tr>
                     <?php
                        // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                        $this->db->query("insert into urutan_ujicoba (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                        $id++;
                        }
                     } ?>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $data_xy = array();
                     $urut = 1;
                     $sql = "select * from urutan_ujicoba order by hasil asc";
                     $datas = $this->db->query($sql);
                     $data_raw = $this->db->query('select * from ujicoba order by id asc');
                     $row_dataraw = $data_raw->result();
                     $row = $datas->result();
                     $no = 0;
                     foreach ($row as $rows) { ?>
                     <tr>
                        <td class="active"><?php echo $rows->data_x; ?></td>
                        <td class="active"><?php echo $rows->data_y; ?></td>
                        <td><?php echo $rows->hasil; ?></td>
                     </tr>
                     <?php
                        if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                        } else {
                           $data_xy[] = $rows->data_x;
                           $data_xy[] = $rows->data_y;
                        }
                     }
                     ?>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <?php
                  ?>
                  <table class="table table-bordered table-responsive">
                     <tr class="active">
                        <td>id</td>
                        <td>a</td>
                        <td>b</td>
                     </tr>
                     <?php
                        $jum_data_xy = count($data_xy);
                        $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                        // echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                        for ($i=0; $i<$jum_data_xy; $i++) {
                           $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                           $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                           ?>
                           <tr>
                              <td><?php echo $data_xy[$i]; ?></td>
                              <td><?php echo $data_urut_a[$i]; ?></td>
                              <td><?php echo $data_urut_b[$i]; ?></td>
                           </tr>
                           <?php
                        }
                     ?>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from ujicoba order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update ujicoba set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("ujicoba", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from ujicoba where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from ujicoba where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from ujicoba where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update ujicoba set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("ujicoba",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from ujicoba where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from ujicoba where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
			</div>

         <!-- Datatable -->
         <script type="text/javascript">
				$(document).ready(function () {
					$('.dtables').DataTable({
                  responsive: true,
						"aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
						"iDisplayLength": 5,
						responsive: true
					});
				});
			</script>

			<div class="panel-footer">
				<p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
			</div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_testingsatu() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Testing Satu
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Testing Satu</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <div class="row">
               <div class="col-md-12">
                  <table class="dtables table table-bordered table-responsive">
                     <?php
                        $data = $this->db->get('dataset_satu');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <thead>
                        <tr class="active">
                           <td>#</td>
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <td><?php //echo $i; ?></td>
                           <?php } ?>
                        </tr>
                     </thead>

                     <tbody>
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <tr>
                           <td class="active"><?php //echo $i; ?></td>
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <td>
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 //echo $jarak_data[$i][$j];
                              ?>
                           </td>
                           <?php } ?>
                        </tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="dtables table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_datasetsatu");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_datasetsatu (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="dtables table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_datasetsatu order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from dataset_satu order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <?php
                  ?>
                  <table class="dtables table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from dataset_satu order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update dataset_satu set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_satu", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_satu where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_satu where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from dataset_satu where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update dataset_satu set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_satu",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from dataset_satu where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_satu where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
         </div>

         <!-- Datatable -->
         <script type="text/javascript">
            $(document).ready(function () {
               $('.dtables').DataTable({
                  responsive: true,
                  "aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
                  "iDisplayLength": 5,
                  responsive: true
               });
            });
         </script>

         <div class="panel-footer">
            <p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
         </div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_testingdua() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Testing Dua
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Testing Dua</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <!-- <div class="row"> -->
               <!-- <div class="col-md-12"> -->
                  <!-- <table class="dtables table table-bordered table-responsive"> -->
                     <?php
                        $data = $this->db->get('dataset_dua');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <!-- <thead> -->
                        <!-- <tr class="active"> -->
                           <!-- <td>#</td> -->
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <!-- <td><?php // echo $i; ?></td> -->
                           <?php } ?>
                        <!-- </tr> -->
                     <!-- </thead> -->

                     <!-- <tbody> -->
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <!-- <tr> -->
                           <!-- <td class="active"><?php // echo $i; ?></td> -->
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <!-- <td> -->
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 // echo $jarak_data[$i][$j];
                              ?>
                           <!-- </td> -->
                           <?php } ?>
                        <!-- </tr> -->
                        <?php } ?>
                     <!-- </tbody> -->
                  <!-- </table> -->
               <!-- </div> -->
            <!-- </div> -->

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_datasetdua");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_datasetdua (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_datasetdua order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from dataset_dua order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from dataset_dua order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update dataset_dua set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_dua", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_dua where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_dua where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from dataset_dua where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update dataset_dua set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_dua",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from dataset_dua where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_dua where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
			</div>

         <!-- Datatable -->
         <script type="text/javascript">
				$(document).ready(function () {
					$('.dtables').DataTable({
                  responsive: true,
						"aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
						"iDisplayLength": 5
					});
				});
			</script>

			<div class="panel-footer">
				<p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
			</div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_testingtiga() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Testing Tiga
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Testing Tiga</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <!-- <div class="row"> -->
               <!-- <div class="col-md-12"> -->
                  <!-- <table class="dtables table table-bordered table-responsive"> -->
                     <?php
                        $data = $this->db->get('dataset_tiga');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <!-- <thead> -->
                        <!-- <tr class="active"> -->
                           <!-- <td>#</td> -->
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <!-- <td><?php // echo $i; ?></td> -->
                           <?php } ?>
                        <!-- </tr> -->
                     <!-- </thead> -->

                     <!-- <tbody> -->
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <!-- <tr> -->
                           <!-- <td class="active"><?php // echo $i; ?></td> -->
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <!-- <td> -->
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 // echo $jarak_data[$i][$j];
                              ?>
                           <!-- </td> -->
                           <?php } ?>
                        <!-- </tr> -->
                        <?php } ?>
                     <!-- </tbody> -->
                  <!-- </table> -->
               <!-- </div> -->
            <!-- </div> -->

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_datasettiga");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_datasettiga (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_datasettiga order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from dataset_tiga order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from dataset_tiga order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update dataset_tiga set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_tiga", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_tiga where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_tiga where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from dataset_tiga where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update dataset_tiga set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_tiga",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from dataset_tiga where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_tiga where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
         </div>

         <!-- Datatable -->
         <script type="text/javascript">
            $(document).ready(function () {
               $('.dtables').DataTable({
                  responsive: true,
                  "aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
                  "iDisplayLength": 5
               });
            });
         </script>

         <div class="panel-footer">
            <p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
         </div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_testingempat() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Testing Empat
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Testing Empat</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <!-- <div class="row"> -->
               <!-- <div class="col-md-12"> -->
                  <!-- <table class="dtables table table-bordered table-responsive"> -->
                     <?php
                        $data = $this->db->get('dataset_empat');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <!-- <thead> -->
                        <!-- <tr class="active"> -->
                           <!-- <td>#</td> -->
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <!-- <td><?php // echo $i; ?></td> -->
                           <?php } ?>
                        <!-- </tr> -->
                     <!-- </thead> -->

                     <!-- <tbody> -->
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <!-- <tr> -->
                           <!-- <td class="active"><?php // echo $i; ?></td> -->
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <!-- <td> -->
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 // echo $jarak_data[$i][$j];
                              ?>
                           <!-- </td> -->
                           <?php } ?>
                        <!-- </tr> -->
                        <?php } ?>
                     <!-- </tbody> -->
                  <!-- </table> -->
               <!-- </div> -->
            <!-- </div> -->

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_datasetempat");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_datasetempat (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_datasetempat order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from dataset_empat order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from dataset_empat order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update dataset_empat set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_empat", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_empat where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_empat where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from dataset_empat where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update dataset_empat set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("dataset_empat",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from dataset_empat where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from dataset_empat where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
			</div>

         <!-- Datatable -->
         <script type="text/javascript">
				$(document).ready(function () {
					$('.dtables').DataTable({
                  responsive: true,
						"aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
						"iDisplayLength": 5
					});
				});
			</script>

			<div class="panel-footer">
				<p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
			</div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_clusterdua() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Cluster Dua
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Cluster Dua</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <!-- <div class="row"> -->
               <!-- <div class="col-md-12"> -->
                  <!-- <table class="dtables table table-bordered table-responsive"> -->
                     <?php
                        $data = $this->db->get('clusterdua');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <!-- <thead> -->
                        <!-- <tr class="active"> -->
                           <!-- <td>#</td> -->
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <!-- <td><?php // echo $i; ?></td> -->
                           <?php } ?>
                        <!-- </tr> -->
                     <!-- </thead> -->

                     <!-- <tbody> -->
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <!-- <tr> -->
                           <!-- <td class="active"><?php // echo $i; ?></td> -->
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <!-- <td> -->
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 // echo $jarak_data[$i][$j];
                              ?>
                           <!-- </td> -->
                           <?php } ?>
                        <!-- </tr> -->
                        <?php } ?>
                     <!-- </tbody> -->
                  <!-- </table> -->
               <!-- </div> -->
            <!-- </div> -->

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_clusterdua");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_clusterdua (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_clusterdua order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from clusterdua order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from clusterdua order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update clusterdua set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("clusterdua", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clusterdua where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clusterdua where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from clusterdua where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update clusterdua set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("clusterdua",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from clusterdua where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clusterdua where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
         </div>

         <!-- Datatable -->
         <script type="text/javascript">
            $(document).ready(function () {
               $('.dtables').DataTable({
                  responsive: true,
                  "aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
                  "iDisplayLength": 5
               });
            });
         </script>

         <div class="panel-footer">
            <p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
         </div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_clustertiga() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Testing Empat
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Testing Empat</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <!-- <div class="row"> -->
               <!-- <div class="col-md-12"> -->
                  <!-- <table class="dtables table table-bordered table-responsive"> -->
                     <?php
                        $data = $this->db->get('clustertiga');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <!-- <thead> -->
                        <!-- <tr class="active"> -->
                           <!-- <td>#</td> -->
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <!-- <td><?php // echo $i; ?></td> -->
                           <?php } ?>
                        <!-- </tr> -->
                     <!-- </thead> -->

                     <!-- <tbody> -->
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <!-- <tr> -->
                           <!-- <td class="active"><?php // echo $i; ?></td> -->
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <!-- <td> -->
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 // echo $jarak_data[$i][$j];
                              ?>
                           <!-- </td> -->
                           <?php } ?>
                        <!-- </tr> -->
                        <?php } ?>
                     <!-- </tbody> -->
                  <!-- </table> -->
               <!-- </div> -->
            <!-- </div> -->

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_clustertiga");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_clustertiga (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_clustertiga order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from clustertiga order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from clustertiga order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update clustertiga set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("clustertiga", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clustertiga where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clustertiga where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from clustertiga where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update clustertiga set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("clustertiga",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from clustertiga where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clustertiga where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
         </div>

         <!-- Datatable -->
         <script type="text/javascript">
            $(document).ready(function () {
               $('.dtables').DataTable({
                  responsive: true,
                  "aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
                  "iDisplayLength": 5
               });
            });
         </script>

         <div class="panel-footer">
            <p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
         </div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

   function data_clusterempat() {
      error_reporting(0);
      $row = array(
         'jmlcluster' => $this->input->post("jmlcluster"),
         'metode' => $this->input->post("metode"),
         'data' => $this->input->post("data")
      );

      $jmlcluster = $row['jmlcluster'];
      $metode = $row['metode'];
      $data = $row['data'];
      $iterasi = 2;

      // Proses clustering Enhanced KMeans - Dataset Testing Empat
      $i = 1; //Inisial random centroid

      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-success">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Enhanced K-Means - Dataset Testing Empat</b></h3>
         </div>
         <div class="panel-body">
            <p>Memilih centroid menggunakan metode enhanced k-means sebanyak jumlah cluster yang telah ditentukan</p>
            <!-- <div class="row"> -->
               <!-- <div class="col-md-12"> -->
                  <!-- <table class="dtables table table-bordered table-responsive"> -->
                     <?php
                        $data = $this->db->get('clusterempat');
                        $row = $data->result();
                        $i = 1;
                        foreach ($row as $rows) {
                           $data_a[$i] = $rows->a;
                           $data_b[$i] = $rows->b;
                           $i++;
                        }
                        $count = count($row);
                     ?>

                     <!-- <thead> -->
                        <!-- <tr class="active"> -->
                           <!-- <td>#</td> -->
                           <?php for ($i=1;$i<=$count;$i++) { ?>
                              <!-- <td><?php // echo $i; ?></td> -->
                           <?php } ?>
                        <!-- </tr> -->
                     <!-- </thead> -->

                     <!-- <tbody> -->
                        <?php for ($i=1;$i<=$count;$i++) { ?>
                        <!-- <tr> -->
                           <!-- <td class="active"><?php // echo $i; ?></td> -->
                           <?php for ($j=1;$j<=$count;$j++) { ?>
                           <!-- <td> -->
                              <?php
                                 $jarak_data[$i][$j] = pow(pow(($data_a[$i]-$data_a[$j]),2)+pow(($data_b[$i]-$data_b[$j]),2),0.5);
                                 // echo $jarak_data[$i][$j];
                              ?>
                           <!-- </td> -->
                           <?php } ?>
                        <!-- </tr> -->
                        <?php } ?>
                     <!-- </tbody> -->
                  <!-- </table> -->
               <!-- </div> -->
            <!-- </div> -->

            <p>Mengurutkan hasil dari nilai terkecil ke nilai terbesar</p>
            <div class="row">
               <!-- Mengurutkan hasil -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Sebelum Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $this->db->query("truncate table urutan_clusterempat");
                        $id = 1;
                        for ($i=1;$i<=$count;$i++) {
                        for ($j=$i+1;$j<=$count;$j++) { ?>
                        <tr>
                           <td class="active"><?php echo $i; ?></td>
                           <td class="active"><?php echo $j; ?></td>
                           <td><?php echo $jarak_data[$i][$j]; ?></td>
                        </tr>
                        <?php
                           // $this->db->query("update urutan set data_x=".$i.", data_y=".$j.", hasil=".$jarak_data[$i][$j]." where id=".$id."");
                           $this->db->query("insert into urutan_clusterempat (id, data_x, data_y, hasil) values (".$id.", ".$i.", ".$j.", ".$jarak_data[$i][$j].")");
                           $id++;
                           }
                        } ?>
                     </tbody>
                  </table>
               </div>

               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr>
                           <td colspan="3">Setelah Diurut</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $data_xy = array();
                        $urut = 1;
                        $sql = "select * from urutan_clusterempat order by hasil asc";
                        $datas = $this->db->query($sql);
                        $data_raw = $this->db->query('select * from clusterempat order by id asc');
                        $row_dataraw = $data_raw->result();
                        $row = $datas->result();
                        $no = 0;
                        foreach ($row as $rows) { ?>
                        <tr>
                           <td class="active"><?php echo $rows->data_x; ?></td>
                           <td class="active"><?php echo $rows->data_y; ?></td>
                           <td><?php echo $rows->hasil; ?></td>
                        </tr>
                        <?php
                           if (in_array($rows->data_x, $data_xy) || in_array($rows->data_y, $data_xy)) {
                           } else {
                              $data_xy[] = $rows->data_x;
                              $data_xy[] = $rows->data_y;
                           }
                        }
                        ?>
                     </tbody>
                  </table>
               </div>
            </div>

            <!-- Pengurutan terakhir dan pemilihan centroid -->
            <div class="row">
               <!-- Diurut lagi -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <thead>
                        <tr class="active">
                           <td>id</td>
                           <td>a</td>
                           <td>b</td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           $jum_data_xy = count($data_xy);
                           $jum_data_cluster = floor($jum_data_xy / $jmlcluster);

                           echo "Jumlah Data per Cluster : ".$jum_data_cluster;
                           for ($i=0; $i<$jum_data_xy; $i++) {
                              $data_urut_a[$i] = $row_dataraw[$data_xy[$i] - 1]->a;
                              $data_urut_b[$i] = $row_dataraw[$data_xy[$i] - 1]->b;
                              ?>
                              <tr>
                                 <td><?php echo $data_xy[$i]; ?></td>
                                 <td><?php echo $data_urut_a[$i]; ?></td>
                                 <td><?php echo $data_urut_b[$i]; ?></td>
                              </tr>
                              <?php
                           }
                        ?>
                     </tbody>
                  </table>
               </div>

               <?php
                  for ($i=0;$i<$jmlcluster;$i++) {
                     $med_a[$i] = array_slice($data_urut_a,($jum_data_cluster*$i), $jum_data_cluster);
                     $med_b[$i] = array_slice($data_urut_b,($jum_data_cluster*$i), $jum_data_cluster);
                  }

                  // print_r($med_b);

                  function calculate_median($arr) {
                     rsort($arr);
                     $count = count($arr); //total numbers in array
                     $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
                     if ($count % 2) { // odd number, middle is the median
                        $median = $arr[$middleval];
                     } else { // even number, calculate avg of 2 medians
                        $low = $arr[$middleval];
                        $high = $arr[$middleval + 1];
                        $median = (($low + $high) / 2);
                     }
                     return $median;
                  }
               ?>

               <!-- Pemilihan centroid -->
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                     $z = 1;
                     for ($i = 0; $i < $jmlcluster; $i++) {
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo 'C' . ($i + 1); ?></td>
                        <td>
                           <?php
                              echo calculate_median($med_a[$i]);
                              $centroid[$z][1] = calculate_median($med_a[$i]);
                           ?>
                        </td>
                        <td>
                           <?php
                              echo calculate_median($med_b[$i]);
                              $centroid[$z][2] = calculate_median($med_b[$i]);;
                           ?>
                        </td>
                     </tr>
                     <?php
                     $z++;
                     }
                     $ambil_data = $this->db->query('select * from clusterempat order by id asc');
                     ?>
                  </table>
               </div>
            </div>

            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke 1</b></h4>
            <hr>

            <!-- Menghitung jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                        <td>Jarak ke C<?php echo $c; ?></td>
                     <?php } ?>
                     <td>Status Anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $hasil_intra_temp;
                  $hasil_intra;
                  $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i]=$r->a;
                        $data_b[$i]=$r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                     $index_intra++;
                        for ($c=1; $c<=$jmlcluster; $c++){
                           $jarak_data[$c][$i] = pow(pow(($centroid[$c][1]-$data_a[$i]),2)+pow(($centroid[$c][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$c] = $jarak_data[$c][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                        <td>
                        <?php
                           $hasils = $jarak_data[$c][$i];
                           $hasil_intra_temp[$c][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                        </td>
                     <?php } ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;

                                 if (isset($hasil_intra[$c])) {
                                    $hasil_intra[$c]['hasil'] += $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$c]['hasil'] = $hasil_intra_temp[$c][$index_intra];
                                    $hasil_intra[$c]['jumlah']=1;
                                 }

                                 $tmp_cluster[$c] = $c;
                                 $this->db->query("update clusterempat set tmp_cluster=".$c." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                           //echo "min temp :".$min_temp_cluster;
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil Untuk Anggota Cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++) { ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("clusterempat", array('tmp_cluster' => $c));
                              foreach ($cek_anggota->result() as $anggota ) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster; $c++){ ?>
                     <tr>
                        <td class="active">C<?php echo $c; ?></td>
                        <td>
                           <?php
                              $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clusterempat where tmp_cluster=".$c."");
                              foreach ($avg->result() as $avg) {
                                  echo $avg->avg_a." , ".$avg->avg_b;
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 1 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $oldinter = 0;
                     $hitung = 0;
                     $hsloldinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1; $d<=$jmlcluster; $d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clusterempat where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $oldinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $oldinter/($hitung);
                     $hsloldinter = $oldinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $pembagi = 0;
                        $oldintra = 0;
                        $hsloldintra = 0;
                        $hslbagioldintra = 0;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $oldintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $oldintra;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hsloldintra += $oldintra;
                        }
                        $pembagi = $c-1;
                        $hslbagioldintra = $hsloldintra/$pembagi;
                        // echo $hsloldintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <!-- Perulangan untuk perhitungan kembali jarak dan keanggotaan -->
            <?php
               $kondisi = false;
               while ($kondisi == false) {
            ?>
            <h4 class="pull-left" style="padding-right:20px;"><b>Iterasi ke <?php echo $iterasi;?></b></h4>
            <hr>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php for($c=1; $c<=$jmlcluster ; $c++) {
                        $avg = $this->db->query("select AVG(a) as avg_a, AVG(b) as avg_b from clusterempat where tmp_cluster=".$c."");
                        foreach ($avg->result() as $avg ) {
                           //sesuaikan dengan tabel yang diuji
                           $centroid[$c][1] = $avg->avg_a;
                           $centroid[$c][2] = $avg->avg_b;
                        }?>
                     <tr>
                        <td class="active"><?php echo "C".$c; ?></td>
                        <td><?php echo $centroid[$c][1]." , ".$centroid[$c][2]; ?></td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
            </div>

            <!-- Menghitung Jarak -->
            <table class="dtables table table-bordered table-striped table-responsive">
               <thead>
                  <tr>
                     <td>Data ke</td>
                     <td>a</td>
                     <td>b</td>
                     <?php for($a=1; $a<=$jmlcluster; $a++){ ?>
                        <td>Jarak ke C<?php echo $a; ?></td>
                     <?php } ?>
                     <td>Status anggota</td>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $i=1;
                     $hasil_intra_temp;
                     $hasil_intra;
                     $index_intra= 0;
                     foreach ($ambil_data->result() as $r) {
                        $data_a[$i] = $r->a;
                        $data_b[$i] = $r->b;
                  ?>
                  <tr>
                     <td><?php echo $r->id; ?></td>
                     <td><?php echo $r->a; ?></td>
                     <td><?php echo $r->b; ?></td>
                     <?php
                        $index_intra++;
                        for($b=1; $b<=$jmlcluster; $b++){
                           $jarak_data[$b][$i] = pow(pow(($centroid[$b][1]-$data_a[$i]),2)+pow(($centroid[$b][2]-$data_b[$i]),2),0.5);
                           $hasil_jarak[$b] = $jarak_data[$b][$i];
                           $jarak_min = min($hasil_jarak);
                     ?>
                     <td>
                        <?php
                           // echo $jarak_data[$b][$i];
                           $hasils = $jarak_data[$b][$i];
                           $hasil_intra_temp[$b][$index_intra] = $hasils;
                           echo $hasils;
                        ?>
                     </td>
                     <?php } ?>
                     <td>
                        <?php
                           for($d=1; $d<=$jmlcluster; $d++){
                              if($jarak_min == $jarak_data[$d][$i]){
                                 echo 'C'.$d;

                                 if (isset($hasil_intra[$d])) {
                                    $hasil_intra[$d]['hasil'] += $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']++;
                                 }
                                 else {
                                    $hasil_intra[$d]['hasil'] = $hasil_intra_temp[$d][$index_intra];
                                    $hasil_intra[$d]['jumlah']=1;
                                 }
                                 $tmp_cluster[$d] = $d;

                                 $this->db->query("update clusterempat set tmp_cluster=".$d." where id=".$r->id."");
                              }else{
                              }
                           }
                           $min_tmp_cluster = min($tmp_cluster);
                        ?>
                     </td>
                  </tr>
                  <?php
                        $intra += $jarak_data[1][$i];
                        $i++;
                     }
                  ?>
               </tbody>
            </table>

            <!-- Menghitung titik centroid baru -->
            <div class="row">
               <div class="col-md-6">
                  <p>Hasil untuk anggota cluster</p>
                  <table class="table table-bordered table-responsive">
                     <?php for($e=1; $e<=$jmlcluster; $e++){ ?>
                     <tr>
                        <td class="active">Anggota pada C<?php echo $e; ?></td>
                        <td>
                           <?php
                              $cek_anggota = $this->db->get_where("clusterempat",array('tmp_cluster' => $e));
                              foreach ($cek_anggota->result() as $anggota) {
                                 echo $anggota->id.", ";
                              }
                           ?>
                        </td>
                     </tr>
                     <?php } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <p>Centroid Baru</p>
                     <table class="table table-bordered table-responsive">
                        <?php
                        $cek[$f] = 0;
                        $tambah = 0;
                        for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from clusterempat where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $cek[$f] = 0;
                                    // $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $cek[$f] = 1;
                                    // $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                                 $tambah += $cek[$f];
                              ?>
                           </td>
                        </tr>
                        <?php
                           }
                           if ($tambah == 0) {
                              $kondisi = true;
                           } else {
                              $kondsi = false;
                           }
                        ?>
                     </table>
               </div>
            </div>

            <!-- Hitung jarak inter dan intra -->
            <!-- TODO: Inter dan intra 2 -->
            <div class="row">
               <!-- Hitung Inter -->
               <div class="col-md-6">
                  <p>Inter</p>
                  <table class="table table-bordered">
                     <?php
                     $newinter = 0;
                     $hitung = 0;
                     $hslnewinter = 0;
                     for($c=1; $c<=$jmlcluster; $c++){
                        for ($d=1;$d<=$jmlcluster;$d++) { ?>
                           <tr>
                              <td class="active">C<?php echo $c; ?> ke C<?php echo $d; ?></td>
                              <td>
                                 <?php
                                    $avg = $this->db->query("select AVG(a) as avg_a,AVG(b) as avg_b from clusterempat where tmp_cluster=".$d."");
                                    foreach ($avg->result() as $avg) {
                                       $x[$d] = $avg->avg_a;
                                       $y[$d] = $avg->avg_b;
                                       $jarak[$c][$d] = pow(pow($x[$c]-$x[$d],2)+pow($y[$c]-$y[$d],2),0.5);
                                       $hasil[$c] = $jarak[$c][$d];
                                       echo $hasil[$c];
                                    }
                                 ?>
                              </td>
                           </tr>
                     <?php
                        $newinter += $hasil[$c];
                        $hitung++;
                        }
                     }
                     // echo $newinter/($hitung);
                     $hslnewinter = $newinter/$hitung;
                     ?>
                  </table>
               </div>

               <!-- Hitung Intra -->
               <div class="col-md-6">
                  <p>Intra</p>
                  <table class="table table-bordered">
                     <?php
                        $newintra = 0;
                        $hslnewintra = 0;
                        $hslbaginewintra = 0;
                        $pembagi = 0;
                        $o = 1;
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
                                 $ambilintra[$o] = $newintra;
                                 $o++;
                              ?>
                           </td>
                        </tr>
                     <?php
                           $hslnewintra += $newintra;
                        }
                        $pembagi = $c-1;
                        $hslbaginewintra = $hslnewintra/$pembagi;
                        // echo $hslnewintra;
                     ?>
                  </table>
               </div>
            </div>
            <br>

            <?php $iterasi++; } ?>
         </div>

         <!-- Datatable -->
         <script type="text/javascript">
            $(document).ready(function () {
               $('.dtables').DataTable({
                  responsive: true,
                  "aLengthMenu":[[10, 20, 30, -1], [10, 20, 30, "All"]],
                  "iDisplayLength": 5
               });
            });
         </script>

         <div class="panel-footer">
            <p>Keterangan</p>
            <ul>
               <li>C = Cluster</li>
            </ul>
         </div>
      </div>

      <!-- Panel inter, intra -->
      <div class="row">
         <!-- Intra -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Intra</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Intra merupakan jarak antar titik data didalam cluster</li>
                     <li>Semakin kecil nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Inter -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Inter</h3>
               </div>
               <div class="panel-body">
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Inter merupakan jarak antar cluster</li>
                     <li>Semakin besar nilainya, semakin baik</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>

      <!-- Penilaian DBI -->
      <div class="row">
         <!-- DBI -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <?php
                     $rasio = 0;
                     $rmax[$c] = 0;
                     $dbiraw = 0;
                     $dbi = 0;
                  ?>
                  <h5 class="pull-left" style="padding-right:20px;">Menghitung Nilai Rasio</h5>
                  <hr>
                  <?php
                  for ($i=1;$i<=$jmlcluster;$i++) {
                     ?>
                     <table class="table table-bordered text-center">
                        <tr>
                           <td class="active">#</td>
                           <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php } ?>
                           <td class="active">Rasio Max</td>
                        </tr>

                        <?php for ($i=1;$i<=$jmlcluster;$i++) { ?>
                        <tr>
                           <td class="active">C<?php echo $i; ?></td>
                           <?php for ($j=1;$j<=$jmlcluster;$j++) { ?>
                           <td>
                              <?php
                                 $rasio = ($ambilintra[$i]+$ambilintra[$j])/$jarak[$i][$j];
                                 if (is_nan($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 if (is_infinite($rasio) == 'TRUE') {
                                    $rasio = 0;
                                 }
                                 echo $rasio;
                                 if ($rmax[$i]<=$rasio) {
                                    $rmax[$i] = $rasio;
                                 } else {
                                 }
                              ?>
                           </td>
                           <?php } ?>
                           <td><?php echo $rmax[$i]; ?></td>
                        </tr>
                        <?php
                              $dbiraw += $rmax[$i];
                           }
                           // echo $dbiraw;
                           $dbi = $dbiraw/$jmlcluster;
                        ?>
                     </table>
                     <div class="row text-center">
                        <div class="col-md-4">
                           <table class="table table-bordered">
                              <tr>
                                 <td class="active">Nilai DBI</td>
                              </tr>
                              <tr>
                                 <td><?php echo $dbi; ?></td>
                              </tr>
                           </table>
                        </div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <?php
                     }
                  ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <?php
   }

}
