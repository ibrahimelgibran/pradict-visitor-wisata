<?php
include('includes/checklogin.php');
check_login();

$nama_wisata = $_POST['nama_wisata'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

$chartData = [];

if (!empty($nama_wisata)) {
  $query = "SELECT MONTH(Tanggal) as Bulan, SUM(JumlahPengunjung) as Total 
            FROM tourism_data 
            WHERE NamaWisata = :nama_wisata";

  if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND Tanggal BETWEEN :from AND :to";
  }

  $query .= " GROUP BY Bulan ORDER BY Bulan";

  $stmtChart = $dbh->prepare($query);
  $stmtChart->bindParam(':nama_wisata', $nama_wisata);

  if (!empty($from_date) && !empty($to_date)) {
    $stmtChart->bindParam(':from', $from_date);
    $stmtChart->bindParam(':to', $to_date);
  }

  $stmtChart->execute();
  $chartResults = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

  foreach ($chartResults as $row) {
    $chartData[(int)$row['Bulan']] = (int)$row['Total'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php"); ?>

<body>
  <div class="container-scroller">
    <?php @include("includes/header.php"); ?>
    <div class="container-fluid page-body-wrapper">
      <?php @include("includes/sidebar.php"); ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <!-- Modal -->
                <div id="editData5" class="modal fade">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Detail Tempat Wisata</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body" id="info_update5">
                        <?php @include("view_tourism_details.php"); ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                      </div>
                    </div>
                  </div>
                </div>




                <div class="card mt-4">

                  <div class="card-body">
                    <!-- Form Filter Nama Wisata -->
                    <form method="post" class="form-inline mb-3">
                      <label class="mr-2">Nama Wisata:</label>
                      <select name="nama_wisata" class="form-control mr-2">
                        <option value="">Semua</option>
                        <?php
                        $stmt = $dbh->prepare("SELECT DISTINCT NamaWisata FROM tourism_data ORDER BY NamaWisata");
                        $stmt->execute();
                        $wisatas = $stmt->fetchAll(PDO::FETCH_OBJ);
                        foreach ($wisatas as $wisata) {
                          $selected = ($nama_wisata == $wisata->NamaWisata) ? 'selected' : '';
                          echo "<option value='" . htmlentities($wisata->NamaWisata) . "' $selected>" . htmlentities($wisata->NamaWisata) . "</option>";
                        }
                        ?>
                      </select>
                      <button type="submit" name="filter" class="btn btn-primary">Tampilkan</button>
                    </form>
                    <h4 class="card-title">Grafik Jumlah Pengunjung</h4>
                    <canvas id="chartVisitor" height="100"></canvas>
                    <div class="table-responsive p-3">
                      <div class="card-body">
                        <!-- Form Filter Rentang Tanggal -->
                        <form method="post" class="form-inline my-3">
                          <input type="hidden" name="nama_wisata" value="<?= htmlentities($nama_wisata) ?>">

                          <label class="mr-2">Rentang Tanggal:</label>
                          <input type="date" name="from_date" class="form-control mr-2" value="<?= htmlentities($from_date) ?>">
                          <input type="date" name="to_date" class="form-control mr-2" value="<?= htmlentities($to_date) ?>">

                          <button type="submit" name="filter" class="btn btn-primary">Filter</button>
                          <a href="manage_visitor.php" class="btn btn-secondary ml-2">Reset</a>
                        </form>

                      </div>
                      <!-- Export buttons -->
                      <div class="mb-3">
                        <button type="button" id="exportPDF" class="btn btn-danger mr-2"><i class="mdi mdi-file-pdf"></i> Export PDF</button>
                        <button type="button" id="exportExcel" class="btn btn-success"><i class="mdi mdi-file-excel"></i> Export Excel</button>
                      </div>
                      
                      <table class="table table-hover table-bordered" id="dataTableHover">
                        <thead>
                          <tr>
                            <th class="text-center">No</th>
                            <th>Nama Wisata</th>
                            <th>Jumlah Pengunjung</th>
                            <th>Pendapatan</th>
                            <th>Sewa Gedung</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $conditions = [];
                          $params = [];

                          if (!empty($nama_wisata)) {
                            $conditions[] = "NamaWisata = :nama_wisata";
                            $params[':nama_wisata'] = $nama_wisata;
                          }
                          if (!empty($from_date) && !empty($to_date)) {
                            $conditions[] = "Tanggal BETWEEN :from AND :to";
                            $params[':from'] = $from_date;
                            $params[':to'] = $to_date;
                          }

                          $where = '';
                          if (!empty($conditions)) {
                            $where = "WHERE " . implode(" AND ", $conditions);
                          }

                          $sql = "SELECT * FROM tourism_data $where ORDER BY ID DESC";
                          $query = $dbh->prepare($sql);
                          foreach ($params as $key => $val) {
                            $query->bindValue($key, $val);
                          }
                          $query->execute();
                          $results = $query->fetchAll(PDO::FETCH_OBJ);
                          $cnt = 1;
                          if ($query->rowCount() > 0) {
                            foreach ($results as $row) {
                          ?>
                              <tr>
                                <td class="text-center"><?php echo $cnt; ?></td>
                                <td><?php echo htmlentities($row->NamaWisata); ?></td>
                                <td class="text-center"><?php echo htmlentities($row->JumlahPengunjung); ?></td>
                                <td class="text-right">Rp <?php echo number_format($row->Pendapatan, 0, ',', '.'); ?></td>
                                <td class="text-right">Rp <?php echo number_format($row->SewaGedung, 0, ',', '.'); ?></td>
                                <td class="text-center"><?php echo htmlentities(date("d-m-Y", strtotime($row->Tanggal))); ?></td>
                                <td class="text-center">
                                  <a href="#" class="edit_data5" id="<?php echo $row->ID; ?>" title="Lihat Detail">
                                    <i class="mdi mdi-eye"></i>
                                  </a>
                                </td>
                              </tr>
                          <?php $cnt++;
                            }
                          } ?>
                        </tbody>
                      </table>

                      <form method="post" action="hasil_predik.php">
                        <button type="submit" name="prediksi" class="btn btn-success mt-3">Prediksi</button>
                      </form>

                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <?php @include("includes/footer.php"); ?>
        <?php @include("includes/foot.php"); ?>
      </div>


      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('chartVisitor').getContext('2d');

          const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          const dataFromPHP = <?= json_encode($chartData) ?>;
          const dataValues = monthLabels.map((_, i) => dataFromPHP[i + 1] ?? 0);

          new Chart(ctx, {
            type: 'line',
            data: {
              labels: monthLabels,
              datasets: [{
                label: 'Jumlah Pengunjung',
                data: dataValues,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
              }]
            },
            options: {
              responsive: true,
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });

          $(document).on('click', '.edit_data5', function() {
            var edit_id5 = $(this).attr('id');
            $.ajax({
              url: "view_tourism_details.php",
              type: "post",
              data: {
                edit_id5: edit_id5
              },
              success: function(data) {
                $("#info_update5").html(data);
                $("#editData5").modal('show');
              }
            });
          });
          
          // Helper function to get table data
          function getTableData() {
            const table = document.getElementById('dataTableHover');
            const headers = [];
            const tableData = [];
            
            // Generate title
            let title = 'Data Wisata';
            if ('<?= $nama_wisata ?>') {
              title += ' - <?= htmlentities($nama_wisata) ?>';
            }
            if ('<?= $from_date ?>' && '<?= $to_date ?>') {
              title += ' (<?= date("d-m-Y", strtotime($from_date)) ?> s/d <?= date("d-m-Y", strtotime($to_date)) ?>)';
            }
            
            // Headers
            const headerCells = table.querySelectorAll('thead th');
            headerCells.forEach((cell, index) => {
              // Skip the action column
              if (index < headerCells.length - 1) {
                headers.push(cell.textContent.trim());
              }
            });
            
            // Rows
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
              const rowData = [];
              const cells = row.querySelectorAll('td');
              cells.forEach((cell, index) => {
                // Skip the action column
                if (index < cells.length - 1) {
                  rowData.push(cell.textContent.trim());
                }
              });
              tableData.push(rowData);
            });
            
            return { title, headers, tableData };
          }
          
          // PDF Export Functionality
          document.getElementById('exportPDF').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const today = new Date();
            const dateStr = today.toLocaleDateString('id-ID');
            const { title, headers, tableData } = getTableData();
            
            // Add title
            doc.setFontSize(16);
            doc.text(title, 14, 15);
            
            // Add current date
            doc.setFontSize(10);
            doc.text('Tanggal cetak: ' + dateStr, 14, 22);
            
            // Create table
            doc.autoTable({
              head: [headers],
              body: tableData,
              startY: 25,
              theme: 'grid',
              styles: {
                fontSize: 8
              },
              headStyles: {
                fillColor: [66, 135, 245],
                textColor: [255, 255, 255],
                fontStyle: 'bold'
              },
              alternateRowStyles: {
                fillColor: [240, 240, 240]
              }
            });
            
            // Save PDF
            doc.save('data_wisata_' + dateStr + '.pdf');
          });
          
          // Excel Export Functionality
          document.getElementById('exportExcel').addEventListener('click', function() {
            const { title, headers, tableData } = getTableData();
            const today = new Date();
            const dateStr = today.toLocaleDateString('id-ID').replace(/\//g, '-');
            
            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            
            // Add title row and empty row
            const titleRow = [[title]];
            const dateRow = [['Tanggal cetak: ' + dateStr]];
            const emptyRow = [[]];
            
            // Combine headers and data
            const excelData = [
              ...titleRow,
              ...dateRow,
              ...emptyRow,
              headers,
              ...tableData
            ];
            
            const ws = XLSX.utils.aoa_to_sheet(excelData);
            
            // Style the title cell (merge cells)
            if(!ws['!merges']) ws['!merges'] = [];
            ws['!merges'].push({s:{r:0,c:0}, e:{r:0,c:headers.length-1}}); // Merge title cells
            
            // Add to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Data Wisata");
            
            // Generate Excel file and trigger download
            XLSX.writeFile(wb, 'data_wisata_' + dateStr + '.xlsx');
          });
        });
      </script>
</body>

</html>