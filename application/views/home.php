<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!--

Bismillahirrahmaanirrahim

"Kehidupan dunia hanyalah permainan dan senda gurau"
(QS 6:32&70) | (QS 7:51) | (QS 47:36) | (QS 57:20)

"Maka sesungguhnya bersama kesulitan ada kemudahan,
sesungguhnya bersama kesulitan ada kemudahan"
(QS 94:5-6)

-->

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title></title>

   <link rel="stylesheet" href="<?=base_url();?>assets/bootstrap/css/bootstrap.min.css">
   <link rel="stylesheet" href="<?=base_url();?>assets/font-awesome/css/font-awesome.min.css">
   <link rel="stylesheet" href="<?=base_url();?>assets/datatables/css/dataTables.bootstrap.min.css">
   <link rel="stylesheet" href="<?=base_url();?>assets/datatables/css/responsive.dataTables.min.css">
   <link rel="stylesheet" href="<?=base_url();?>assets/style.css">

   <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
   <![endif]-->

</head>

<body>

   <!-- Preloader -->
   <div id="preloader">
      <div id="status"></div>
   </div>

   <header>
      <!-- Navigasi -->
      <nav class="navbar navbar-default navbar-static-top" role="navigation">
         <div class="container">
            <div class="navbar-header">
               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <a class="navbar-brand" href="#">Thesis Clustering</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbar">
               <ul class="nav navbar-nav navbar-right">
                  <li><a href="<?=site_url('Compare');?>">Perbandingan algoritma</a></li>
               </ul>
            </div><!-- /.navbar-collapse -->
         </div><!-- /.container-fluid -->
      </nav>
   </header>

   <div id="algoritma1" class="container">
      <div class="row">

         <!-- TAB 1 -->
         <div class="col-md-4">

            <!-- Memilih data yang akan digunakan -->
            <h4>Pilih Data</h4>
            <hr>
            <div class="row">
               <div class="col-md-9">
                  <div class="form-group">
                     <select class="form-control" name="data" id="data">
                        <option value="1">Ujicoba</option>
                        <option value="2">Testing 1</option>
                        <option value="3">Testing 2</option>
                        <option value="4">Testing 3</option>
                        <option value="5">Testing 4</option>
                        <option value="6">Cluster Dua</option>
                        <option value="7">Cluster Tiga</option>
                        <option value="8">Cluster Empat</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <button class="btn-success btn pull-right" type="button" name="button" onclick="tampilData()">Lihat Data</button>
               </div>
            </div><br>

            <!-- Memilih algoritma yang akan digunakan -->
            <h4>Pilih Metode</h4>
            <hr>
            <div class="form-group">
               <select class="form-control" name="metode" id="metode">
                  <option value="1">k-means</option>
                  <option value="2">enhanced k-means</option>
                  <option value="3">dynamic cluster</option>
                  <option value="4">enhanced k-means + dynamic cluster</option>
               </select>
            </div>
            <div class="form-group">
               <input type="text" class="form-control" id="jml_cluster" placeholder="Inputkan jumlah cluster">
               <p class="help-block">* khusus k-means dan enhanced k-means</p>
            </div>
            <button class="btn btn-success pull-right" type="button" name="button" onclick="mulaiCluster()">Mulai proses cluster</button>
            <div class="clearfix"></div><br>

            <!-- Memilih algoritma yang akan digunakan -->
            <h4>Waktu Proses</h4>
            <hr>

         </div>

         <!-- TAB 2 -->
         <div class="col-md-8">
            <h4>Hasil</h4>
            <hr>

            <!-- Tempat menampilkan data dan proses cluster -->
            <div id="hasilData"></div>
            <div id="hasilCluster"></div>

         </div>
      </div>
   </div>

   <footer></footer>

   <script src="<?=base_url();?>assets/jquery/jquery.min.js"></script>
   <script src="<?=base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
   <script src="<?=base_url();?>assets/datatables/js/jquery.dataTables.min.js"></script>
   <script src="<?=base_url();?>assets/datatables/js/dataTables.responsive.min.js"></script>
   <script src="<?=base_url();?>assets/datatables/js/dataTables.bootstrap.min.js"></script>

   <!-- Fungsi menampilkan dataset -->
   <script type="text/javascript">
      function tampilData() {
      var data = $("#data").val();
         if (data == "1"){ // ujicoba
            $.ajax({
               type: 'POST',
               url: '<?= site_url('Dataset/ujicoba');?>',
               data: {"data": data},
               success: function(result) {
                  $("#hasilData").html(result);
               }
            });
         } else if (data == "2"){ // dataset satu
            $.ajax({
               type: 'POST',
               url: '<?= site_url('Dataset/datasetsatu');?>',
               data: {"data": data},
               success: function(result) {
                  $("#hasilData").html(result);
               }
            });
         } else if (data == "3"){ // dataset dua
            $.ajax({
               type: 'POST',
               url: '<?= site_url('Dataset/datasetdua');?>',
               data: {"data": data},
               success: function(result) {
                  $("#hasilData").html(result);
               }
            });
         } else if (data == "4"){ // dataset tiga
            $.ajax({
               type: 'POST',
               url: '<?= site_url('Dataset/datasettiga');?>',
               data: {"data": data},
               success: function(result) {
                  $("#hasilData").html(result);
               }
            });
         } else if (data == "5"){ // dataset empat
            $.ajax({
               type: 'POST',
               url: '<?= site_url('Dataset/datasetempat');?>',
               data: {"data": data},
               success: function(result) {
                  $("#hasilData").html(result);
               }
            });
         }
      }
   </script>

   <!-- Fungsi proses dan menampilkan hasil clustering -->
   <script type="text/javascript">
      function mulaiCluster() {
         var jmlcluster = $("#jml_cluster").val();
         var metode = $("#metode").val();
         var data = $("#data").val();

         switch (metode) {
            // K-Means
            case "1":
               if (jmlcluster == "") {
                  alert("isi terlebih dahulu jumlah clusternya")
               } else {
                  if (data == "1") { // ujicoba
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_ujicoba');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else if (data == "2") { // testing satu
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_testingsatu');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else if (data == "3") { // testing dua
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_testingdua');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else if (data == "4") { //testing tiga
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_testingtiga');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else if (data == "5") { // testing empat
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_testingempat');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else if (data == "6") { // clusterdua
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_clusterdua');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else if (data == "7") { // clustertiga
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_clustertiga');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  } else { // clusterempat
                     $.ajax({
                        type: 'POST',
                        url: '<?= site_url('Kmeans/data_clusterempat');?>',
                        data: {"jmlcluster" : jmlcluster},
                        beforeSend: function() {
                           $('#status').show();
                           $('#preloader').show();
                        },
                        complete: function() {
                           $('#status').hide();
                           $('#preloader').hide();
                        },
                        success: function(result) {
                           $("#hasilCluster").html(result);
                        }
                     });
                  }
               }
               break;

            // Enhanced K-Means
            case "2":
               if (data == "1") { //Ujicoba
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_ujicoba');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else if (data == "2") { // testing satu
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_testingsatu');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else if (data == "3") { //testing dua
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_testingdua');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else if (data == "4") { //testing tiga
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_testingtiga');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else if (data == "5") { //testing empat
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_testingempat');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else if (data == "6") { //clusterdua
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_clusterdua');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else if (data == "7") { //clustertiga
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_clustertiga');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               } else { //clusterempat
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Enhanced/data_clusterempat');?>',
                     data: {"jmlcluster" : jmlcluster},
                     beforeSend: function() {
                        $('#status').show();
                        $('#preloader').show();
                     },
                     success: function(result) {
                        $('#status').hide();
                        $('#preloader').hide();
                        $("#hasilCluster").html(result);
                     }
                  });
               }
               break;

            // Dynamic Cluster
            case "3":
               if (data == "1") {
                  alert("belum");
               } else if (data == "2") {
                  alert("belum");
               } else if (data == "3") {
                  alert("belum");
               } else if (data == "4") {
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Dynamic/data_ujicoba');?>',
                     data: {"jmlcluster" : 2},
                     success: function(result) {
                        $("#hasilCluster").html(result);
                        $("#jml_cluster").val("2");
                        //console.log(result);
                     }
                  });
               } else {
                  $.ajax({
                     type: 'POST',
                     url: '<?= site_url('Dynamic/data_datasetsatu');?>',
                     data: {"jmlcluster" : 2},
                     success: function(result) {
                        $("#hasilCluster").html(result);
                        $("#jml_cluster").val("2");
                        //console.log(result);
                     }
                  });
               }
               break;

            // Enhanced K-Means + Dynamic Cluster
            case "4":
               if (data == "1") {
                  alert("belum");
               } else if (data == "2") {
                  alert("belum");
               } else if (data == "3") {
                  alert("belum");
               } else {
                  alert("belum");
               }
               break;

            default:
               break;
         }
      }
   </script>

   <!-- Preloader -->
   <script type="text/javascript">
      $(window).load(function() {
         //Preloader
         $('#status').delay(300).fadeOut();
         $('#preloader').delay(300).fadeOut('slow');
         $('body').delay(550).css({'overflow':'visible'});
      })
   </script>

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

</body>

</html>
