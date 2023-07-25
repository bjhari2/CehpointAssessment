<?php
    session_start();
include("header.php");
    if(retIsAdmin($_SESSION['uname'])==0){
        header('Location: pages/error.php?error=noAccess');
    }
	// Dictionary on what to replace what with what
    $table="Choose an table";
    $page=1;
    $perPage=10;
    $totalPages=1;
    if(empty($_GET['table'])){
        $_GET['table']=NULL;
    }
    if(!(empty($_GET['db']))){
        $_POST['db']=$_GET['db'];
    }
    if(!(empty($_POST['db']))){
        if($_POST['db']==1)
            $db=$MainDB;
        else if($_POST['db']==2)
            $db=$formDB;
        else if($_POST['db']==3)
            $db=$mailerDB;
    }
    else{
        $_POST['db']=1;
        $db=$MainDB;
    }
    if(empty($_GET['search'])){
        $_GET['search']="";
    }
    if(empty($_GET['page'])){
        $page=1;
    }
    else{
        $page=$_GET['page'];
    }
    if(empty($_GET['perPage'])){
        $perPage=10;
    }
    else{
        $perPage=$_GET['perPage'];
    }
    try{
        $conn = new PDO('mysql:dbname='.$db.';host='.$servername.';charset=utf8', $username, $password);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        $message = $e->getMessage()  ;
        header('Location:pages/error.php?error='.$e->getMessage());
        die();
    }

    $sql = $conn->prepare("show TABLES from ".$db);
    $sql->execute();
    $tableNames = $sql->fetchAll(PDO::FETCH_ASSOC);

    if(!(empty($_GET['table']))){
        $table = $_GET['table'];
        $table_list=[];

        foreach ($tableNames as $row) {
            $table_list[]=$row["Tables_in_".$db];
        }

        if(in_array($table,$table_list)){
            $sql = $conn->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`=:db AND `TABLE_NAME`=:table");
            $sql->bindValue(":db",$db);
            $sql->bindValue(":table",$table);
            $sql->execute();
            $columns = $sql->fetchAll(PDO::FETCH_ASSOC); // COLUMN_NAME
            $i=0;
            foreach ($columns as $row) {
                $colArr[$i]=$row['COLUMN_NAME'];
                $i++;
            }
            $sql = $conn->prepare("select count(*) from ".$table.";");
            $sql->execute();
            $rowc = $sql->fetch();
            $countr = $rowc[0]; // Count total responses
            // calculate number of pages needed
            $totalPages = ceil($countr/$perPage);
            // Find the starting element for the current $page
            $startPage = $perPage*($page-1);
            $sql = $conn->prepare("select * from ".$table." order by id limit :startpage,:perpage;");
            $sql->bindValue(":startpage",$startPage);
            $sql->bindValue(":perpage",$perPage);
            $sql->execute();
            $registrants  = $sql->fetchAll(PDO::FETCH_ASSOC);
        }else{
            header('Location: pages/error.php?error=Bad%20Request');
        }

    }
?>
<html>

<head id="head_tag">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Database Management:<?php echo " ".$OrgName; ?></title>
    <link rel="icon" type="image/png" sizes="600x600" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body id="page-top">
    <div id="wrapper">
            <?php include("navigation.php"); ?>
            <div class="container-fluid">
                <div class="d-sm-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-dark mb-0">Manage Databases</h3></div>
                <div class="row">
                    <div class="col-md-6 col-xl-3 mb-4">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <select class="form-control border-1 small" style="width: 68%;max-width:15em;" onchange="this.form.submit()" name="db">
                                <option value="1" <?php if($_POST['db']==1){echo 'selected=""';} ?>>CMS database</option>
                                <option value="2" <?php if($_POST['db']==2){echo 'selected=""';} ?>>Forms database</option>
                                <option value="3" <?php if($_POST['db']==3){echo 'selected=""';} ?>>Mailer database</option>
                            </select>
                        </form>
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
                            <input type="hidden" name="db" value="<?php if($_POST['db']==2){echo '2';} else if($_POST['db']==3){echo '3';} else {echo '1';} ?>" ?>
                            <select onchange="this.form.submit()" class="form-control border-1 small" style="width: 68%;max-width:15em;" name ="table" required>
                                <option value = "">Select an table</option>
                                <?php
                                    $table_list=[];
                                    foreach ($tableNames as $row) {
                                        echo "<option value = ".$row["Tables_in_".$db].">".$row["Tables_in_".$db]."</option>";
                                        $table_list[]=$row["Tables_in_".$db];
                                    }
                                ?>
                            </select><br>
                        </form>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4"></div>
                    <div class="col-md-6 col-xl-3 mb-4"></div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card shadow border-left-success py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col mr-2">
                                        <div class="text-uppercase text-success font-weight-bold text-xs mb-1"><span>Total Rows</span></div>
                                        <div class="text-dark font-weight-bold h5 mb-0"><span><?php if(isset($countr)){echo $countr;}else{echo "No table chosen";}  ?></span></div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-database fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header py-3">
                        <p class="text-primary m-0 font-weight-bold"><?php echo 'Table Name: '.$table; ?></p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-nowrap">
                                <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable"><label>Show&nbsp;
                                    <!-- Form weirdly starts here, don't ask me why :3 !-->
                                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>"  method="GET">
                                        <input type="hidden" name="table" value="<?php echo $table; ?>"/>
                                        <input type="hidden" name="page"  value="1"/>
                                        <input type="hidden" name="db" value="<?php echo $_GET['db']; ?>"/>
                                        <select onchange="this.form.submit()" name="perPage" class="form-control form-control-sm custom-select custom-select-sm">
                                            <option value="10" <?php if($perPage==10){echo 'selected=""';} ?>>10</option>
                                            <option value="25" <?php if($perPage==25){echo 'selected=""';} ?>>25</option>
                                            <option value="50" <?php if($perPage==50){echo 'selected=""';} ?>>50</option>
                                            <option value="100" <?php if($perPage==100){echo 'selected=""';} ?>>100</option>
                                        </select>&nbsp;</label></div>
                                        </div>
                                        <div class="col-md-6">
                                            <!--
                                                <div class="text-md-right dataTables_filter" id="dataTable_filter"><label><input type="search" name="search" class="form-control form-control-sm" aria-controls="dataTable" placeholder="Search ID"></label></div>
                                            -->
                                            </form>
                                        </div>
                                    </div>
                        <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                            <table class="table dataTable table-sm my-0 table-striped" id="dataTable">
                                <thead>
                                    <tr>
                                        <?php
                                            if(!(empty($_GET['table']))){
                                                foreach ($colArr as $col){
                                                    echo "<th>".$col."&nbsp;&nbsp;</th>";
                                                }
                                                echo "<th></th>";
                                            }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                         if(!(empty($_GET['table']))){
                                         	echo '<tr><form action="db-ops/insert.php" method="POST"><input type="hidden" value="'.$table.'" name="table"><input type="hidden" value="'.$db.'" name="db">';
                                         		foreach ($colArr as $col){
                                         			if($col!='id' && $col!='timestamp'){
	                                         			echo '<td>';
	                                         			echo '<input type="text" placeholder="'.$col.'" name="'.$col.'" class="form-control">';
	                                         			echo '</td>';
                                         			}else{
                                         				echo '<td></td>';
                                         			}
                                         		}
                                         	echo '<td align="center">';
                                         	echo '<button type="submit" class="btn btn-success ml-2 btn-sm"><i class="fas fa-plus text-white"></button></td>';
                                         	echo '</form></tr>';
                                            foreach ($registrants as $row){
                                            	$row_id = $row["id"];
                                                echo "<tr>";
                                                foreach ($colArr as $col){
                                                    if($col!="cert_link"){
                                                        echo "<td>".$row[$col]."</td>";
                                                    }
                                                    else{
                                                        echo '<td><a href="'.$row[$col].'">Link</a></td>';
                                                    }
                                                }
                                                echo '<td align="center">
	                                                	<a href="db-ops/delete.php?table='.$table.'&id='.$row_id.'&db='.$db.'" class="btn btn-danger ml-2 btn-sm"><i class="fas fa-trash text-white"></i>
	                                                	</a>
	                                                	<div style="padding:10px;">
	                                                		<a href="db-ops/modify.php?tbname='.$table.'&id='.$row_id.'&db='.$db.'" class="btn btn-info ml-2 btn-sm"><i class="fas fa-edit text-white"></i></a>
	                                                	</div>
                                                	</td>';

                                                echo "</tr>";
                                            }
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <?php
                                            if(!(empty($_GET['table']))){
                                                foreach ($colArr as $col){
                                                    echo "<th>".$col."</th>";
                                                }
                                                echo "<th></th>";
                                            }
                                        ?>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6 align-self-center">
                                <p id="dataTable_info" class="dataTables_info" role="status" aria-live="polite"></p>
                            </div>
                            <form class="col-md-6">
                                <nav class="d-lg-flex justify-content-lg-end dataTables_paginate paging_simple_numbers">
                                    <ul class="pagination">
                                        <li class="page-item <?php if($page==1){echo 'disabled';} ?>"><button name="page" value="<?php echo ($page-1); ?>" class="page-link" aria-label="Previous"><span aria-hidden="true">«</span></button></li>
                                        <input type="hidden" name="db" value="<?php echo $_GET['db']; ?>"/>
                                        <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>"/>
                                        <input type="hidden" name="perPage" value="<?php echo $perPage; ?>"/>
                                        <?php
                                            // Generate buttons for choosing pages
                                            for ($x=1; $x<=$totalPages; $x++){
                                                if($x==$page){
                                                    echo '<li class="page-item active"><button name="page" value="'.$x.'" class="page-link">'.$x.'</button></li>';
                                                }
                                                else{
                                                    echo '<li class="page-item"><button name="page" value="'.$x.'" class="page-link">'.$x.'</button></li>';
                                                }
                                             }
                                        ?>

                                        <li class="page-item <?php if($page==$totalPages){echo 'disabled';} ?>"><button name="page" value="<?php echo ($page+1); ?>" class="page-link" aria-label="Next"><span aria-hidden="true">»</span></button></li>
                                    </ul>
                                </nav>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <br><br>
        </div>

    </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
	<script src="../assets/js/bs-init.js"></script>
	<script src="../assets/js/theme.js"></script>
</body>

</html>
