import SED from './utils.js'

var token = $('.container').data('token');
var open = [];

$(".btn-primary").click( function() {
    var id_parcours = this.getAttribute('id');
    
    $.when(recuperer_points_parcours(id_parcours).done(function(data, textStatus, jqXHR){

        let tab_trace = []

        data.forEach((point) => {
            if(point.type != 3){
                tab_trace.push(point);
                if(point.type == 2){
                    telechargerGeoJSON(tab_trace, id_parcours);
                    tab_trace = [];
                }
            }
        });
    }));
});

$(".attribution-poste").click( function(e){
    let raid_id =$(this).data('idRaid');
    let poste_id =$(this).data('idPoste');
    let poste_name =$(this).data('namePoste');
    let user_id = $(this).data('idUser');

    open[user_id] = !open[user_id];
    
    if(open[user_id]){
        $(this)
            .find('option')
            .remove()
            .end()

        let self = $(this);

        $.when(charger_postes_disponibles(raid_id)
            .done(function(data, textStatus, jqXHR){

                let option = document.createElement('option');
                option.value = poste_id;
                option.innerHTML = "Poste actuel : " + poste_name;
                self.append(option);

                for(let i=0; i< data.length;i++){

                    if(poste_id != data[i].id){
                        
                        let option = document.createElement('option');
                        option.value = data[i].id;
                        option.innerHTML = data[i].type;
                        self.append(option);
                    }
                }
            })
        );
    }
});

$(".attribution-poste").change( function(e){

    let raid_id = $(this).data('idRaid');
    let user_id = $(this).data('idUser');

    $.when(recuperer_repartition(raid_id, user_id).done(function(data, textStatus, jqXHR){
        for(let i=0; i < data.length;i++){

            let repartition = {
                "idBenevole": data[i].idBenevole.id,
                "idPoste": e.target.value
            }
            maj_repartition(repartition, data[i].id);
        }
    }));
});

function telechargerGeoJSON(trace, id) {
    var geojson = {
        "name":"NewFeatureType",
        "type":"FeatureCollection",
        "features":[{
            "type":"Feature",
            "geometry":{
                "type":"LineString",
                "coordinates":[]
            },
            "properties":null
        }]
    };

    trace.forEach(function(point) {
        let lat = point.lat;
        let lon = point.lon;
        geojson.features[0].geometry.coordinates.push([lon, lat]);
    })

    var blob = new File([JSON.stringify(geojson)], {
        type: "text/plain;charset=utf-8"
    });

    saveAs(blob, "export_parcours_"+ id +".geojson");
}

function recuperer_points_parcours(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/points/parcours/'+id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        success: function(data){
            SED.log('Points du parcours ['+ id +'] récupérés avec succès!');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log(xhr.responseJSON.message);  
        }
    });
}

function charger_postes_disponibles(id_raid){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/postes/raids/'+ id_raid +'/available',  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log('Récupération des postes disponibles effectuée');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la récupération des postes disponibles');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function recuperer_repartition(id_raid, id_user){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/repartitions/raids/'+ id_raid +'/users/'+ id_user,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log('Récupération de la répartition utilisateur réussie');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la récupération de la répartition utilisateur');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function maj_repartition(repartition, id){
    
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/repartitions/'+ id,  
        type: 'POST',
        dataType: 'json',  
        headers: {"X-Auth-Token": token}, 
        data: { ...repartition },
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log(data);
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la MAJ de la répartition');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}