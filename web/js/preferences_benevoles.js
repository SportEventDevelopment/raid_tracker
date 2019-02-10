import SED from './utils.js'

var token = $("#sortlist").data('token');
var id_raid = $("#sortlist").data('idRaid');
var id_user = $("#sortlist").data('idUser');

$("#sortlist").sortable( {
    accept : 'sortable_item',
    axis : 'vertically',
    opacity : 0.6,
    change: function(event, ui){
            
        let all_prefs = this.getElementsByTagName("li");
        $.when(supprimerPreferences(id_raid, id_user).done(function(data, textStatus, jqXHR){

            for(let i = 0; i < all_prefs.length; i++){
                let id_poste = $(all_prefs[i]).data('idPoste');
                let id_benevole = $(all_prefs[i]).data('idBenevole');
                let priority = i+1;
                
                let preference = {
                    "idPoste": id_poste,
                    "idBenevole": id_benevole,
                    "priority": priority,
                }
                
                creerPreference(preference);
            }
        }));
    }
});

function supprimerPreferences(id_raid, id_user){
    return $.ajax({
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/prefpostes/raids/' + id_raid + '/users/'+ id_user,  
        type: 'DELETE',
        dataType: 'json',
        headers: {"X-Auth-Token": token},
        success: function(data){
            SED.log('Preferences supprimées avec succès!');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log(xhr.responseJSON.message);  
        }
    });
}

function creerPreference(preference){
    return $.ajax({
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/prefpostes',  
        type: 'POST',
        dataType: 'json',
        headers: {"X-Auth-Token": token},
        data: { ...preference },
        success: function(data){
            SED.log('Preference ajoutée avec succès!');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log(xhr.responseJSON.message);  
        }
    });
}