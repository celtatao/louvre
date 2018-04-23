
////////////fORMAT DATE + GESTION BILLETJOUR//////////////////////////////
$(function()
{
    $('#datepicker').on('changeDate', function() {
        var maintenant = new Date();
        //On formate la date du jour pour être conforme au retour de datepicker

        //On ajoute un 0 au jour si le chiffre du jour est inférieur à 10
        var day;
        if(maintenant.getDate()>9){day= maintenant.getDate()}else{day = '0'+maintenant.getDate()}

        //De même pour le mois
        var month;
        if(maintenant.getMonth()>9){month = (maintenant.getMonth()+1)}else{month = '0'+(maintenant.getMonth()+1)}

        var year = maintenant.getFullYear();

        //On concatène les trois éléments de la date
        var formatedDate = day+'/'+month+'/'+year;

        //Lors d'un changement de date à la date du jour courant et si l'heure a dépassé 14h
        if($('#datepicker').datepicker('getFormattedDate') === formatedDate && maintenant.getHours()>=14 )
        {
            //On désactive l'option Journée et on sélectionne l'option demie-journée (pour éviter que l'option journée déjà sélectionnée s'applique).
            $("select option:contains('Journée')").attr("disabled","disabled");
            $("select option:contains('Journée')").attr("selected",false);

            $("select option:contains('Demi-journée')").attr('selected',true);
        }else{
            //Sinon on réactive l'option Journée si cette dernière n'est pas activé
            $("select option:contains('Journée')").prop("disabled", false);
        }
    });
});


$(function()
{
    // On récupère la balise <div>
    var $container = $('div#ml_billetteriebundle_commande_billets');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $('#ml_billetteriebundle_commande_billets').children().length;

    // On ajoute un premier champ automatiquement si aucun n'est défini
    
    if (index == 0) {
        addBillet($container);
    }

     // AJOUT DES BILLETS EN FONCTION DE QTEBILLET 
    
    var index = $container.find(':input').length;
    var $qteBillet = $("#ml_billetteriebundle_commande_qteBillet");
    
    // Si changement qteBillet
    
    $qteBillet.on('change', function(e){
        e.preventDefault();
        var placeMax = $("#qtebillet").text();
        if (parseInt($qteBillet.val()) > parseInt(placeMax)) {
            $('#myAlert').modal('show');
        } else {
            var indice = $("#ml_billetteriebundle_commande_qteBillet").val()-1;
            $container.empty();
            index = 0
            for (i = 0; i <= indice; i++ ) {
                addBillet($container);
                
            }
        }

    });
    
});
