<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dynamic extends CI_Controller {

   function index() {}

   function data_wine() {
      // TODO: proses algoritma data wine
   }

   function data_iris() {
      // TODO: proses algoritma data iris
   }

   function data_glass() {
      // TODO: proses algoritma data glass
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

      // Proses clustering Dynamic KMeans - Dataset Ujicoba
      $i = 1; //Inisial random centroid
      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-default">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Dynamic Cluster - Dataset Ujicoba</b></h3>
         </div>
         <div class="panel-body">
         <?php for ($loop=$jmlcluster-1;$loop<$jmlcluster;$loop++) {?>
            <h4 style="margin-bottom: -13px;width: 100%;max-width: 100%;"><span class="label label-warning">Proses <?php echo $jmlcluster; ?> Cluster</span></h4><br><br>
            <p>Memilih centroid secara acak berdasarkan jumlah cluster</p>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                        $data = $this->db->query('select * from ujicoba order by rand() limit 0,'.$jmlcluster.'');
                        foreach ($data->result() as $r) {
                           $centroid[$i][1]=$r->a;
                           $centroid[$i][2]=$r->b;
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo $i; ?> (C<?php echo $i; ?>)</td>
                        <td><?php echo $centroid[$i][1]." , ".$centroid[$i][2]; ?></td>
                     </tr>
                     <?php
                        $i++; }
                        //Menghitung jarak masing-masing data
                        $ambil_data = $this->db->query('select * from ujicoba order by id asc');
                        $i = 1;
                        // $intra = 0;
                     ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
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
                     <?php
                        }
                     ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;
                                 //echo  $hasil_intra_temp[$c][$index_intra];
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
                                 //echo ' X'.$c.' ';
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
                     } ?>
               </tbody>
            </table>
            <br>

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

            <!-- TODO : ini harus dirubah -->
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
                                 //echo  $hasil_intra_temp[$c][$index_intra];
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
                     } ?>
               </tbody>
            </table>
            <br>

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
                           $tambah = 0;
                           $cek[$f] = 0;
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
                              $kondisi = false;
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

            <?php
            $iterasi++;
               }

               if ($hslnewinter>$hsloldinter && $hslbaginewintra<$hslbagioldintra) {
                  $i = 1;
                  $jmlcluster++;
                  $iterasi = 2;
               } else {
               }

            }
            ?>
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
                     // for ($c=1;$c<=$jmlcluster;$c++) {
                     //    for ($d=1;$d<=$jmlcluster;$d++) {
                     //       echo "<br>Rasio ".$c.", ".$d."<br>";
                     //       echo "Inter ".$c.", ".$d." = ".$jarak[$c][$d]."<br>";
                     //       echo "Intra".$c." = ".$ambilintra[$c]."<br>";
                     //       echo "Intra".$d." = ".$ambilintra[$d]."<br>";
                     //       $rasio = ($ambilintra[$c]+$ambilintra[$d])/$jarak[$c][$d];
                     //       echo "Hasilnya adalah = ".$rasio."<br>";
                     //       if ($rmax[$c]<=$rasio) {
                     //          $rmax[$c] = $rasio;
                     //       } else {
                     //       }
                     //    }
                     // }
                     // for ($i=1;$i<=$jmlcluster;$i++) {
                     //    echo "<br>Rasio Max C".$i." = ".$rmax[$i];
                     // }
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

         <!-- Silhouete Coeficient -->
         <div class="col-md-12">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Silhouete Coeficient</h3>
               </div>
               <div class="panel-body">
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

      // Proses clustering Dynamic KMeans - Dataset Ujicoba
      $i = 1; //Inisial random centroid
      ?>
      <!-- Panel proses clustering -->
      <div class="panel panel-default">
         <div class="panel-heading">
            <h3 class="panel-title">Proses Clustering <b>Dynamic Cluster - Dataset Satu</b></h3>
         </div>
         <div class="panel-body">
         <?php for ($loop=$jmlcluster;$loop<=$jmlcluster;$loop++) {?>
            <h4 style="margin-bottom: -13px;width: 100%;max-width: 100%;"><span class="label label-warning">Proses <?php echo $jmlcluster; ?> Cluster</span></h4><br><br>
            <p>Memilih centroid secara acak berdasarkan jumlah cluster</p>
            <?php
               for ($c=1; $c<=$jmlcluster; $c++){
                  $hasil_intra[$c]['hasil'] = 0;
                  $hasil_intra[$c]['jumlah'] = 0;
               }
            ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered table-responsive">
                     <?php
                        $data = $this->db->query('select * from dataset_satu order by rand() limit 0,'.$jmlcluster.'');
                        foreach ($data->result() as $r) {
                           $centroid[$i][1]=$r->a;
                           $centroid[$i][2]=$r->b;
                     ?>
                     <tr>
                        <td class="active">Centroid <?php echo $i; ?> (C<?php echo $i; ?>)</td>
                        <td><?php echo $centroid[$i][1]." , ".$centroid[$i][2]; ?></td>
                     </tr>
                     <?php
                        $i++; }
                        //Menghitung jarak masing-masing data
                        $ambil_data = $this->db->query('select * from dataset_satu order by id asc');
                        $i = 1;
                        // $intra = 0;
                     ?>
                  </table>
               </div>
               <div class="col-md-6"></div>
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
                     <?php
                        }
                     ?>
                     <td>
                        <?php
                           for ($c=1; $c<=$jmlcluster; $c++){
                              if($jarak_min == $jarak_data[$c][$i]){
                                 echo "C".$c;
                                 //echo  $hasil_intra_temp[$c][$index_intra];
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
                                 //echo ' X'.$c.' ';
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
                     } ?>
               </tbody>
            </table>
            <br><br>

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

            <!-- TODO : ini harus dirubah -->
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
                                 //echo  $hasil_intra_temp[$c][$index_intra];
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
                     } ?>
               </tbody>
            </table>
            <br><br>

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
                        <?php for($f=1; $f<=$jmlcluster; $f++){ //echo $centroid[$f][1];?>
                        <tr>
                           <td class="active">C<?php echo $f; ?></td>
                           <td>
                              <?php
                                 $avg = $this->db->query("select AVG(a) as avg_a, AVG(b)as avg_b from dataset_satu where tmp_cluster=".$f."")->row_array();
                                 echo $avg['avg_a']." , ".$avg['avg_b'];
                                 if(($centroid[$f][1] == $avg['avg_a']) && ($centroid[$f][2] == $avg['avg_b'])){
                                    $kondisi = true;
                                    echo "<br><span class='text-danger'>(Centroid tidak berubah)</span>";
                                 } else {
                                    $kondisi = false;
                                    echo "<br><span class='text-success'>(Centroid berubah)</span>";
                                 }
                              ?>
                           </td>
                        </tr>
                        <?php } ?>
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
                        for ($c=1;$c<=$jmlcluster;$c++) { ?>
                        <tr>
                           <td class="active">Jarak data ke C<?php echo $c;?></td>
                           <td>
                              <?php
                                 $newintra = $hasil_intra[$c]['hasil']/$hasil_intra[$c]['jumlah'];
                                 echo $newintra;
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

            <?php
            $iterasi++;
               }

               if ($hslnewinter>$hsloldinter && $hslbaginewintra<$hslbagioldintra) {
                  $i = 1;
                  $jmlcluster++;
                  $iterasi = 2;
               } else {
               }

            }
            ?>
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
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">DBI</h3>
               </div>
               <div class="panel-body">
                  <p>Menghitung rasio :</p>
                  <?php echo "inter ".$hslnewinter; ?><br>
                  <?php echo "intra tambah ".$hslnewintra; ?>
               </div>
               <div class="panel-footer">
                  <p>catatan :</p>
                  <ul>
                     <li>Semakin mendekati nilai 0 (non negatif) semakin baik hasil clusternya</li>
                  </ul>
               </div>
            </div>
         </div>

         <!-- Silhouete Coeficient -->
         <div class="col-md-6">
            <div class="panel panel-info">
               <div class="panel-heading">
                  <h3 class="panel-title">Silhouete Coeficient</h3>
               </div>
               <div class="panel-body">
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
