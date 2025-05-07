<?php
include('includes/checklogin.php');
check_login();
if(isset($_POST['save']))
{
  $nama_wisata = $_POST['nama_wisata'];
  $jumlah_pengunjung = $_POST['jumlah_pengunjung'];
  $pendapatan = $_POST['pendapatan'];
  $sewa_gedung = $_POST['sewa_gedung'];
  $rentang_waktu = $_POST['rentang_waktu'];
  $tanggal = $_POST['tanggal'];

  $sql = "INSERT INTO tourism_data(NamaWisata, JumlahPengunjung, Pendapatan, SewaGedung, RentangWaktu, Tanggal)
          VALUES (:nama_wisata, :jumlah_pengunjung, :pendapatan, :sewa_gedung, :rentang_waktu, :tanggal)";
  $query = $dbh->prepare($sql);
  $query->bindParam(':nama_wisata', $nama_wisata, PDO::PARAM_STR);
  $query->bindParam(':jumlah_pengunjung', $jumlah_pengunjung, PDO::PARAM_INT);
  $query->bindParam(':pendapatan', $pendapatan, PDO::PARAM_STR);
  $query->bindParam(':sewa_gedung', $sewa_gedung, PDO::PARAM_STR);
  $query->bindParam(':rentang_waktu', $rentang_waktu, PDO::PARAM_STR);
  $query->bindParam(':tanggal', $tanggal, PDO::PARAM_STR);
  $query->execute();

  if ($dbh->lastInsertId()) {
    echo '<script>alert("Data berhasil disimpan!")</script>';
    echo "<script>window.location.href ='new_visitor.php'</script>";
  } else {
    echo '<script>alert("Gagal menyimpan data.")</script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php");?>
<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <?php @include("includes/header.php");?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:../../partials/_sidebar.html -->
      <?php @include("includes/sidebar.php");?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
               <div class="modal-header">
                <h5 class="modal-title" style="float: left;">Input Data Tempat Wisata</h5>
              </div>
              <div class="col-md-12 mt-4">
              <form class="forms-sample" method="post">
  <div class="row">
    <div class="form-group col-md-6">
      <label>Nama Wisata</label>
      <input type="text" name="nama_wisata" class="form-control" required>
    </div>
    <div class="form-group col-md-6">
      <label>Jumlah Pengunjung</label>
      <input type="number" name="jumlah_pengunjung" class="form-control" required>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <label>Pendapatan (Rp)</label>
      <input type="number" name="pendapatan" class="form-control" required>
    </div>
    <div class="form-group col-md-6">
      <label>Sewa Gedung (Rp)</label>
      <input type="number" name="sewa_gedung" class="form-control" required>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <label>Rentang Waktu</label>
      <select name="rentang_waktu" class="form-control" required>
        <option value="Harian">Harian</option>
        <option value="Mingguan">Mingguan</option>
        <option value="Bulanan">Bulanan</option>
      </select>
    </div>
    <div class="form-group col-md-6">
      <label>Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
  </div>
  <button type="submit" name="save" class="btn btn-info mr-2 mb-4">Simpan</button>
</form>

              </div>
            </div>
          </div>
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">

            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->
      <?php @include("includes/footer.php");?>
      <!-- partial -->
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<?php @include("includes/foot.php");?>
<!-- End custom js for this page -->
</body>
</html>