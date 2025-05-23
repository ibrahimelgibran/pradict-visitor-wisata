<?php
include('includes/checklogin.php');
check_login();

if (isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);
    $sql = "UPDATE tbladmin SET Status='0' WHERE ID = :rid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rid', $rid, PDO::PARAM_INT);
    if ($query->execute()) {
        echo "<script>alert('User blocked');</script>"; 
        echo "<script>window.location.href = 'userregister.php'</script>";
    } else {
        echo '<script>alert("Update failed! Try again later")</script>';
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
                                <h5 class="modal-title" style="float: left;">Management Pengelola Wisata</h5>    
                                <div class="card-tools" style="float: right;">
                                    <!-- <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#delete" ></i> Blocked users
                                    </button> -->
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#registeruser" ><i class="fas fa-plus" ></i> Register User
                                    </button>
                                </div>      
                            </div>
                            <!-- /.card-header -->
                            <div class="modal fade" id="registeruser">
                                <div class="modal-dialog ">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Management Pengelola Wisata</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- <p>One fine body&hellip;</p> -->
                                            <?php @include("newuser_form.php");?>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                            <div class="modal fade" id="delete">
                                <div class="modal-dialog modal-xl ">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Deleted user</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- <p>One fine body&hellip;</p> -->
                                            <?php @include("deleted_users.php");?>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                            <!--  start  modal -->
                            <div id="editData" class="modal fade">
                                <div class="modal-dialog ">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit user info</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="info_update">
                                            <?php @include("update_user.php");?>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
                            </div>
                            <!--   end modal -->
                            <div class="card-body table-responsive p-3">

                                                        <?php
                            $countSql = "SELECT COUNT(*) AS total FROM tbladmin WHERE Status='1'";
                            $countQuery = $dbh->prepare($countSql);
                            $countQuery->execute();
                            $countResult = $countQuery->fetch(PDO::FETCH_ASSOC);
                            $totalUsers = $countResult['total'];
                            ?>
                            <div class="mb-3">
                            <h5>Jumlah Pengelola Wisata: <?php echo $totalUsers; ?></h5>
                            </div>


                                <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="">Nama</th>
                                            <th class="text-center">Nama Wisata</th>
                                            <th class="">Username</th>
                                            <th class=" text-center">Password</th>
                                            <th class="text-center" style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql="SELECT * from tbladmin where Status='1'";
                                        
                                        $query = $dbh -> prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0)
                                        {
                                            foreach($results as $row)
                                            {    
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo htmlentities($cnt);?></td>
                                                    <td><?php  echo htmlentities($row->UserName);?></td>

                                                    <td class="text-center"><?php  echo htmlentities($row->MobileNumber);?></td>
                                                    <td><?php  echo htmlentities($row->Email);?></td>
                                                    <td class="text-center">
                                                    <?php echo str_repeat("•", 5); ?></td>
                                                    <td class=" text-center">
                                                        <a href="#"  class=" edit_data" id="<?php echo  ($row->ID); ?>" title="click for edit"><i class="mdi mdi-pencil-box-outline" aria-hidden="true"></i></a>
                                                        <a href="userregister.php?delid=<?php echo ($row->ID);?>" onclick="return confirm('Do you really want to Delete ?');" title="Delete this User"><i class="mdi mdi-delete fa-delete"style="color: #f05050"  aria-hidden="true"></i></a> </td>
                                                    </tr>
                                                    <?php $cnt=$cnt+1;
                                                }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
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
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click','.edit_data',function()
            {
                var edit_id=$(this).attr('id');
                $.ajax(
                {
                    url:"update_user.php",
                    type:"post",
                    data:{edit_id:edit_id},

                    success:function(data)
                    {
                        $("#info_update").html(data);
                        $("#editData").modal('show');
                    }

                });
            });
        });
        
    </script>
    
</body>
</html>
