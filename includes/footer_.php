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
    });
</script>
</body>
</html>
