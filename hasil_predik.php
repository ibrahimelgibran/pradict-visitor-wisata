<?php
include('includes/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php"); ?>
<body>
<div class="container-scroller">
  <?php @include("includes/header.php"); ?>
  <div class="container-fluid page-body-wrapper">
    <?php @include("includes/sidebar.php"); ?>

    <?php
    function getMonthlyVisitors($dbh) {
      $stmt = $dbh->prepare("SELECT NamaWisata, MONTH(Tanggal) as Bulan, SUM(JumlahPengunjung) as TotalPengunjung 
                             FROM tourism_data 
                             GROUP BY NamaWisata, Bulan 
                             ORDER BY NamaWisata, Bulan");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $data = getMonthlyVisitors($dbh);

    // Format data ke bentuk tabel prediksi
    $rows = [];
    foreach ($data as $entry) {
        $rows[$entry['NamaWisata']][$entry['Bulan']] = $entry['TotalPengunjung'];
    }

    $months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
    ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <h3 class="mb-4 font-weight-bold">Hasil Prediksi Berdasarkan Data Bulanan</h3>
        <div class="card">
          <div class="card-body table-responsive">
            <table class="table table-bordered table-hover text-center">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Nama Wisata</th>
                  <?php foreach ($months as $month): ?>
                    <th><?= $month ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                foreach ($rows as $nama => $bulanData): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-left"><?= htmlentities($nama) ?></td>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                      <td><?= isset($bulanData[$i]) ? htmlentities($bulanData[$i]) : 0 ?></td>
                    <?php endfor; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php @include("includes/footer.php"); ?>
  </div>
</div>
<?php @include("includes/foot.php"); ?>
<script type="text/javascript">
$(document).ready(function(){
  $(document).on('click', '.edit_data5', function(){
    var edit_id5 = $(this).attr('id');
    $.ajax({
      url: "view_tourism_details.php",
      type: "post",
      data: { edit_id5: edit_id5 },
      success: function(data){
        $("#info_update5").html(data);
        $("#editData5").modal('show');
      }
    });
  });
});
</script>
</body>
</html>
