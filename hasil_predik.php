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
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="font-weight-bold">Hasil Prediksi</h3>
          <div>
            <button type="button" id="exportPDF" class="btn btn-danger mr-2"><i class="mdi mdi-file-pdf"></i> Export PDF</button>
            <button type="button" id="exportExcel" class="btn btn-success"><i class="mdi mdi-file-excel"></i> Export Excel</button>
          </div>
        </div>
        <div class="card">
          <div class="card-body table-responsive">
            <table class="table table-bordered table-hover text-center" id="prediksiTable">
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

    
  </div>
</div>
<?php @include("includes/footer.php"); ?>
<?php @include("includes/foot.php"); ?>

<!-- Add necessary scripts for PDF and Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
  
  // PDF Export Functionality
  document.getElementById('exportPDF').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape orientation for wide table
    const today = new Date();
    const dateStr = today.toLocaleDateString('id-ID');
    
    // Add title
    const title = 'Hasil Prediksi Data Wisata';
    doc.setFontSize(16);
    doc.text(title, 14, 15);
    
    // Add current date
    doc.setFontSize(10);
    doc.text('Tanggal cetak: ' + dateStr, 14, 22);
    
    // Extract table data
    const table = document.getElementById('prediksiTable');
    
    // Headers
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(cell => {
      headers.push(cell.textContent.trim());
    });
    
    // Rows
    const tableData = [];
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const rowData = [];
      const cells = row.querySelectorAll('td');
      cells.forEach(cell => {
        rowData.push(cell.textContent.trim());
      });
      tableData.push(rowData);
    });
    
    // Create table
    doc.autoTable({
      head: [headers],
      body: tableData,
      startY: 25,
      theme: 'grid',
      styles: {
        fontSize: 7, // Smaller font to fit wide table
        cellPadding: 1
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
    doc.save('prediksi_wisata_' + dateStr + '.pdf');
  });
  
  // Excel Export Functionality
  document.getElementById('exportExcel').addEventListener('click', function() {
    const table = document.getElementById('prediksiTable');
    const today = new Date();
    const dateStr = today.toLocaleDateString('id-ID').replace(/\//g, '-');
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    
    // Add title row and empty row
    const titleRow = [['Hasil Prediksi Data Wisata']];
    const dateRow = [['Tanggal cetak: ' + dateStr]];
    const emptyRow = [[]];
    
    // Headers
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(cell => {
      headers.push(cell.textContent.trim());
    });
    
    // Rows
    const tableData = [];
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const rowData = [];
      const cells = row.querySelectorAll('td');
      cells.forEach(cell => {
        rowData.push(cell.textContent.trim());
      });
      tableData.push(rowData);
    });
    
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
    XLSX.utils.book_append_sheet(wb, ws, "Prediksi Data");
    
    // Generate Excel file and trigger download
    XLSX.writeFile(wb, 'prediksi_wisata_' + dateStr + '.xlsx');
  });
});
</script>
</body>
</html>