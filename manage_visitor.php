<?php
include('includes/checklogin.php');
check_login();

$nama_wisata = $_POST['nama_wisata'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

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

// Build conditions for pagination
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

// Count total records for pagination
$countSql = "SELECT COUNT(*) as total FROM tourism_data $where";
$countQuery = $dbh->prepare($countSql);
foreach ($params as $key => $val) {
  $countQuery->bindValue($key, $val);
}
$countQuery->execute();
$totalRecords = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $records_per_page);
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
                    <form method="post" class="form-inline mb-3" action="?page=1">
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
                        <form method="post" class="form-inline my-3" action="?page=1">
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
                      
                      <!-- Data Info -->
                      <div class="mb-3">
                        <p class="text-muted">
                          Menampilkan <?= min($offset + 1, $totalRecords) ?> - <?= min($offset + $records_per_page, $totalRecords) ?> dari <?= $totalRecords ?> data
                        </p>
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
                          // Query with LIMIT for pagination
                          $sql = "SELECT * FROM tourism_data $where ORDER BY ID DESC LIMIT :limit OFFSET :offset";
                          $query = $dbh->prepare($sql);
                          
                          foreach ($params as $key => $val) {
                            $query->bindValue($key, $val);
                          }
                          $query->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
                          $query->bindValue(':offset', $offset, PDO::PARAM_INT);
                          
                          $query->execute();
                          $results = $query->fetchAll(PDO::FETCH_OBJ);
                          $cnt = $offset + 1; // Start numbering from current offset
                          
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
                          <?php 
                              $cnt++;
                            }
                          } else {
                          ?>
                            <tr>
                              <td colspan="7" class="text-center">Tidak ada data yang ditemukan</td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>

                      <br><br>
                      <!-- Pagination -->
                      <?php if ($totalPages > 1): ?>
                      <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                          <!-- Previous button -->
                          <?php if ($page > 1): ?>
                            <li class="page-item">
                              <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($nama_wisata) ? '&nama_wisata=' . urlencode($nama_wisata) : '' ?><?= !empty($from_date) ? '&from_date=' . $from_date : '' ?><?= !empty($to_date) ? '&to_date=' . $to_date : '' ?>">
                                &laquo; Sebelumnya
                              </a>
                            </li>
                          <?php endif; ?>
                          
                          <!-- Page numbers -->
                          <?php
                          $start_page = max(1, $page - 2);
                          $end_page = min($totalPages, $page + 2);
                          
                          if ($start_page > 1): ?>
                            <li class="page-item">
                              <a class="page-link" href="?page=1<?= !empty($nama_wisata) ? '&nama_wisata=' . urlencode($nama_wisata) : '' ?><?= !empty($from_date) ? '&from_date=' . $from_date : '' ?><?= !empty($to_date) ? '&to_date=' . $to_date : '' ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                              <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                          <?php endif; ?>
                          
                          <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                              <a class="page-link" href="?page=<?= $i ?><?= !empty($nama_wisata) ? '&nama_wisata=' . urlencode($nama_wisata) : '' ?><?= !empty($from_date) ? '&from_date=' . $from_date : '' ?><?= !empty($to_date) ? '&to_date=' . $to_date : '' ?>"><?= $i ?></a>
                            </li>
                          <?php endfor; ?>
                          
                          <?php if ($end_page < $totalPages): ?>
                            <?php if ($end_page < $totalPages - 1): ?>
                              <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                              <a class="page-link" href="?page=<?= $totalPages ?><?= !empty($nama_wisata) ? '&nama_wisata=' . urlencode($nama_wisata) : '' ?><?= !empty($from_date) ? '&from_date=' . $from_date : '' ?><?= !empty($to_date) ? '&to_date=' . $to_date : '' ?>"><?= $totalPages ?></a>
                            </li>
                          <?php endif; ?>
                          
                          <!-- Next button -->
                          <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                              <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($nama_wisata) ? '&nama_wisata=' . urlencode($nama_wisata) : '' ?><?= !empty($from_date) ? '&from_date=' . $from_date : '' ?><?= !empty($to_date) ? '&to_date=' . $to_date : '' ?>">
                                Selanjutnya &raquo;
                              </a>
                            </li>
                          <?php endif; ?>
                        </ul>
                      </nav>
                      <?php endif; ?>

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
          
          // Helper function to get ALL table data (for export - not just current page)
          function getAllTableData() {
            // For export, we need to get all data, not just current page
            return new Promise((resolve, reject) => {
              $.ajax({
                url: 'get_all_data.php', // You'll need to create this file
                type: 'POST',
                data: {
                  nama_wisata: '<?= $nama_wisata ?>',
                  from_date: '<?= $from_date ?>',
                  to_date: '<?= $to_date ?>'
                },
                dataType: 'json',
                success: function(response) {
                  resolve(response);
                },
                error: function() {
                  // Fallback to current page data
                  resolve(getCurrentPageData());
                }
              });
            });
          }
          
          // Helper function to get current page table data
          function getCurrentPageData() {
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
              if (cells.length > 1) { // Skip empty state row
                cells.forEach((cell, index) => {
                  // Skip the action column
                  if (index < cells.length - 1) {
                    rowData.push(cell.textContent.trim());
                  }
                });
                if (rowData.length > 0) {
                  tableData.push(rowData);
                }
              }
            });
            
            return { title, headers, tableData };
          }
          
          // PDF Export Functionality
          document.getElementById('exportPDF').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const today = new Date();
            const dateStr = today.toLocaleDateString('id-ID');
            const { title, headers, tableData } = getCurrentPageData();
            
            // Add title
            doc.setFontSize(16);
            doc.text(title, 14, 15);
            
            // Add current date
            doc.setFontSize(10);
            doc.text('Tanggal cetak: ' + dateStr, 14, 22);
            doc.text('Halaman: <?= $page ?> dari <?= $totalPages ?>', 14, 28);
            
            // Create table
            doc.autoTable({
              head: [headers],
              body: tableData,
              startY: 32,
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
            doc.save('data_wisata_halaman_<?= $page ?>_' + dateStr + '.pdf');
          });
          
          // Excel Export Functionality
          document.getElementById('exportExcel').addEventListener('click', function() {
            const { title, headers, tableData } = getCurrentPageData();
            const today = new Date();
            const dateStr = today.toLocaleDateString('id-ID').replace(/\//g, '-');
            
            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            
            // Add title row and info rows
            const titleRow = [[title]];
            const dateRow = [['Tanggal cetak: ' + dateStr]];
            const pageRow = [['Halaman: <?= $page ?> dari <?= $totalPages ?>']];
            const emptyRow = [[]];
            
            // Combine headers and data
            const excelData = [
              ...titleRow,
              ...dateRow,
              ...pageRow,
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
            XLSX.writeFile(wb, 'data_wisata_halaman_<?= $page ?>_' + dateStr + '.xlsx');
          });
        });
      </script>
</body>
</html>