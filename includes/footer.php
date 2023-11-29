</div>
</div>
</div>
</div>

<?php


?>
<!-- Loading Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/datepicker.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/datetime-moment.js"></script>
<script src="js/Chart.min.js"></script>
<script src="js/fileinput.js"></script>
<script src="js/chartData.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function() {
            $('.succWrap').slideUp("slow");
        }, 3000);

        var statut = $(".statut").val();

        $( ".statut" ).each(function( index ) {
            console.log( index + ": " + $( this ).val() );
            if($( this ).val() == 1){
                console.log($(".statut > .valide"));
                $(".statut > .valide").css("display","none");
            }
        });

        $('#datepicker_debut').datepicker({
            uiLibrary: 'bootstrap4',
            /* format: 'dd/mm/yyyy',*/
            format: 'yyyy-mm-dd'
        });
        $('#datepicker_fin').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });

        $('.gj-icon').html("<i class='fa fa-calendar'></i>");
        $('i .gp-icon').addClass('fa fa-calendar');

        $(".numFact").focus(function(){
            $(".numFact").css("border","solid 1px blue");
        });

/* DEBUT ajax Tout Valider */
        $('.submit').click(function() {
            var urlLocation = window.location.href;
            
            //var refarticle = $("[name='refarticle[]']").serializeArray();
            var refarticle = $('input[name="refarticle[]"]').map(function(){
                return this.value;
            }).get();
            
            var dlLigne = $('input[name="dlLigne[]"]').map(function(){
                return this.value;
            }).get();
          //  var numBont = $("[name='numBont[]']").serializeArray();
            var numBont = $('input[name="numBont[]"]').map(function(){
                return this.value;
            }).get();

            var dlqtte = $('input[name="dlqtte[]"]').map(function(){
                return this.value;
            }).get();

            var dlqtep = $('input[name="dlqtep[]"]').map(function(){
                return this.value;
            }).get();
            var url = "toutValider.php";

            $.post(url, {refarticle,dlLigne,numBont,dlqtte,dlqtep} ,function(data) {
                $('#toutVal').html(data).show();
            });

            location.href="facture.php";

        });

        /* Fin ajax Tout Valider */

         /* debut disable button tout_valide*/
        if($(".nonvalide").length != 0){
            console.log($(".nonvalide").length)
            $(".toutvalide").attr('disabled',true);
        }

        /* fin disable button tout_valide*/
        /* collapse automatisue */
        $('.numFact').keyup(function(){
         
             $('.collapse').collapse();
          });

      /*  $('td').filter(function(){
		    return /^[.,\d]+$/.test($(this).text());
		});*/

         $("#dateDebut").val($("#datepicker_debut").val());
         $("#dateFin").val($("#datepicker_fin").val());

        
        $('.exp').on('click', '.xlsx', function() {
            window.location.href = 'RAL.php?g=1&num='+$("#num").val()+'&date_debut='+$("input[name*='date_debut']").val()+'&date_fin='+$("input[name*='date_fin']").val();
        });

        $('.expFini').on('click', '.xlsxFini', function() {
            window.location.href = 'LIVF.php?g=1&num='+$("#num").val()+'&date_debut='+$("input[name*='date_debut']").val()+'&date_fin='+$("input[name*='date_fin']").val();
        });

        $('.expResa').on('click', '.xlsxResa', function() {
            window.location.href = 'Resa.php?g=1';
        });


    });

</script>
</body>
</html>
