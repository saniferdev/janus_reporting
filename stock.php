<?php
session_start();
error_reporting(0);
include('includes/config.php');
$DE_No = array(27 => 'SANIFER 3', 28 => "DEPOT BRICO SANIFER 1" , 29 => 'SANIFER 2' , 31 => 'SANIFER 1');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if (!$dbh) {
        echo "La connexion avec la base-de-données n'a pu être établie.<br />";

    }else{
        //echo "conx reussi";
       // $tsql = "SELECT * FROM stock WHERE DE_No = 31 AND AR_Ref LIKE '%' + ? + '%'";
        $tsql = "SELECT
                article.AR_Ref_New,
                article.DL,
                stock.DE_No,
                ISNULL(stock.AS_QteSto,0) AS AS_QteSto,
                article.AR_Design,
                article.FA_CodeFamille

                FROM
                article
                LEFT JOIN stock ON article.AR_Ref_New = stock.AR_Ref AND stock.DE_No IN (" . implode(',', $_SESSION['DE_No']) . ")  
                WHERE
                 article.AR_Ref_New = ?";

        // $params = array($_POST['query']);
        if(isset($_POST['submit'])){

            $ar_ref = $_POST['ar_ref'];
            $params = array($_POST['ar_ref']);

            $getStock = $dbh->prepare($tsql);
            echo $tsql;
            $getStock->execute($params);
            $stock = $getStock->fetchAll(PDO::FETCH_OBJ);
            //var_dump($stock);
        }



    }
}
?>

<?php include('includes/entete.php'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5>Gestion Stock</h5>
                                </div>


                                <div class="panel-body">
                                    <form method="post">

                                        <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Article Réference</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="ar_ref" class="form-control" value="<?php if(isset($ar_ref)){echo $ar_ref;} ?>">

                                            </div>

                                        </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="submit" value="Rechercher" name="submit" class="btn btn-primary" >
                                        </div>

                                    </form>

                                    <table id="listeStock" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top: 70px;">
                                        <thead>
                                        <tr style="background: #0d72d8;color: #fff;">
                                            <th>Famille</th>
                                            <th>Référence Article</th>
                                            <th>Description</th>
                                            <th>Qte</th>                                             
                                            <th>Site</th>
                                          </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach($stock as $st) : ?>
                                        <tr>
                                            <td><?php echo $st->FA_CodeFamille; ?></td>
                                            <td><?php echo $st->AR_Ref_New; ?></td>
                                            <td><?php echo $st->AR_Design; ?></td>
                                            <td><?php echo number_format($st->AS_QteSto,2,","," "); ?></td>
                                            <td><?php echo $DE_No[$st->DE_No]; ?></td>
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
<?php include('includes/footer.php'); ?>
