<div class="navbar">

        <div class="navbar-header">
           <!-- <a href="#" class="navbar-brand">Mon site name</a>-->
            <img src="images/logo.jpg" class="logo"/>
        </div>
        <div>
            <?php
            if($_SESSION['depot'] == "SEC"){
             ?>
                <ul class="nav navbar-nav">

                    <li><a href="facture_entrer.php"><i class="fa fa-cogs"></i> &nbsp;Entrée Facture</a>
                    </li>
                    <li><a href="facture_sortie.php"><i class="fa fa-cogs"></i> &nbsp;Sortie Facture</a>
                    </li>
                    <li><a href="facture_historique.php"><i class="fa fa-cube"></i> &nbsp;Historique</a>
                    </li>
                    <li><a href="facture.php?p=1"><i class="fa fa-cogs"></i> &nbsp;Préparation</a>
                    </li>
                    <li><a href="resteLivrer.php"><i class="fa fa-cube"></i> &nbsp;Reste livrer</a>
                    </li>

                </ul>
            <?php
            }else{
             ?>
                <ul class="nav navbar-nav">
                    <!--<li><a href="profile.php"><i class="fa fa-user"></i> &nbsp;Profil</a>-->
                    <li><a href="facture.php"><i class="fa fa-cogs"></i> &nbsp;Préparation</a>
                    </li>
                    
                    <?php
                        if($_SESSION['depot'] != "LIVRAISON"){
                            ?>
                        <li><a href="livraisonFini.php"><i class="fa fa-cogs"></i> &nbsp;Livraison Fini</a>
                    </li>
                            <?php
                        }
                    ?>
                    <!--<li><a href="gestion.php"><i class="fa fa-cogs"></i> &nbsp;Gestion consultation</a>-->
                    </li>
                   <!-- <li><a href="stock.php"><i class="fa fa-cube"></i> &nbsp;Etat de stock</a>
                    </li>-->

                   <?php
                        //if($_SESSION['depot'] == "LIVRAISON"){
                    ?>
                         <li><a href="AfficheRetour.php"><i class="fa fa-cube"></i> &nbsp;Retour</a>
                          </li>
                  <?php
                      //  }
                    ?>
                    <li><a href="resteLivrer.php"><i class="fa fa-cube"></i> &nbsp;Reste livrer</a>
                    <?php
                        if($_SESSION['depot'] != "LIVRAISON"){
                    ?>
                    </li><li><a href="transm.php"><i class="fa fa-cube"></i> &nbsp;Transmission</a>
                    </li>
                     <?php
                        }
                    ?>
                    <li><a href="AfficheHistorique.php"><i class="fa fa-cube"></i> &nbsp;Historique</a>
                    </li>
                    <?php
                        if($_SESSION['depot'] == "LOGISTIQUE" || $_SESSION['depot'] == "LIVRAISON"){
                            ?>
                        <li><a href="logistique.php"><i class="fa fa-cube"></i> &nbsp;Logistique</a>
                        </li>
                         <li><a href="annulation.php"><i class="fa fa-cube"></i> &nbsp;Annulation</a>
                        </li>
                            <?php
                        }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){
                    ?>
                     <li><a href="annulation.php"><i class="fa fa-cube"></i> &nbsp;Annulation</a>
                        </li>
                            <?php
                    }
                    ?>
                </ul>
            <?php
            }
            ?>
           <!-- <ul class="nav navbar-nav navbar-right">
                <li><a href=""><span class="glyphicon glyphicon-log-in"></span> login</a></li>
            </ul>-->
        </div>
        <div class="version">V2.0.0</div>
    </div>
