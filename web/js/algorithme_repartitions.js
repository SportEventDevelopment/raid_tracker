import SED from './utils.js';

var id_raid;
var token;

$(".Algorithme").click( function() {

    id_raid = this.getAttribute('id');
    token = $(".container").data('token');
    
    $.when(recuperer_prefpostes_raid(id_raid),recuperer_benevoles_raid(id_raid),recuperer_postes_raid(id_raid)).done(function(data_prefpostes,data_benevoles,data_postes){
        
        let all_postes_raid = [];
        let concordances_postes = [];
        let postes = [];
        let all_benevoles_raid = [];
        let concordances_benevoles = [];
        let liste_benevoles = [];
        let prefpostes = [];
        let all_preferences_raid = [];
        let pref_benevoles = [];
        let couple = [];
        let repartitions = [];

        [all_postes_raid,all_benevoles_raid,all_preferences_raid] = preparation_donnees(data_postes[0],data_benevoles[0],data_prefpostes[0]);

        [postes,concordances_postes] = creation_concordances_postes_algo(all_postes_raid);
        [liste_benevoles,concordances_benevoles] = creation_concordances_benevoles_algo(all_benevoles_raid);

        prefpostes = remplir_prefpostes_algo(concordances_benevoles);
        prefpostes = creation_preferences_postes_algo(all_preferences_raid, concordances_benevoles, concordances_postes, prefpostes);

        prefpostes = ajout_preferences_supplementaire_benevoles(liste_benevoles, postes, prefpostes);
        [pref_benevoles,couple] = creation_preferences_benevoles_pour_postes(liste_benevoles,postes);

        couple = attribution_automatique(couple, prefpostes, pref_benevoles, liste_benevoles);
        ecrire_repartitions(couple, concordances_benevoles, concordances_postes, repartitions);

    })
});

function recuperer_prefpostes_raid(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/prefpostes/raids/'+ id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log('Récupération des préférences de postes du raid réussie');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la récupération des préférences de postes du raid');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function recuperer_benevoles_raid(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/benevoles/raids/'+ id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log('Récupération des bénévoles du raid réussie');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors des bénévoles du raid');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function recuperer_postes_raid(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/postes/raids/'+ id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log('Récupération des postes du raid réussie');
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la récupération des postes du raid');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function preparation_donnees(data_postes,data_benevoles,data_prefpostes) {
    let all_postes_raid = [];
    let all_benevoles_raid = [];
    let all_preferences_raid = [];

    all_postes_raid = recuperation_bdd_postes_raid(data_postes);
    all_benevoles_raid = recuperation_bdd_benevoles_raid(data_benevoles);
    all_preferences_raid = recuperation_bdd_preferences_raid(data_prefpostes);
    
    return [all_postes_raid, all_benevoles_raid, all_preferences_raid];
}

function recuperation_bdd_postes_raid(data_postes) {
    let all_postes_raid = [];
    for(let i=0 ; i < data_postes.length ; i++){
        let tmp = [];
        tmp["idPoste"] = data_postes[i]["id"];
        tmp["nb_benevoles"] = data_postes[i]["nombre"];
        tmp["type"] = data_postes[i]["type"];
        tmp["heureDebut"] = data_postes[i]["heureDebut"];
        tmp["heureFin"] = data_postes[i]["heureFin"];
        all_postes_raid.push(tmp);
    }
    return all_postes_raid;
}

function recuperation_bdd_benevoles_raid(data_benevoles) {
    let all_benevoles_raid = []
    for(let i=0 ; i < data_benevoles.length ; i++){
        let tmp = [];
        tmp["idBenevole"] = data_benevoles[i]["id"];
        tmp["idRaid"] = data_benevoles[i]["idRaid"];
        tmp["idUser"] = data_benevoles[i]["idUser"];
        all_benevoles_raid.push(tmp);
    }
    return all_benevoles_raid;
}

function recuperation_bdd_preferences_raid(data_prefpostes) {
    let all_preferences_raid = [];
    for(let i=0 ; i < data_prefpostes.length ; i++){
        let tmp = [];
        tmp["idPreference"] = data_prefpostes[i]["id"];
        tmp["idPoste"] = data_prefpostes[i]["idPoste"]["id"];
        tmp["idBenevole"] = data_prefpostes[i]["idBenevole"]["id"];
        tmp["priority"] = data_prefpostes[i]["priority"];
        all_preferences_raid.push(tmp);
    }
    return all_preferences_raid;
}

function creation_concordances_postes_algo(all_postes_raid) {
    let boucle_y = 0;
    let postes = [];
    let concordances_postes = [];

    all_postes_raid.forEach(function(element) {
        let loop = element.nb_benevoles;
        let tmp = [];
        for(let i = 0 ; i < loop ; i++) {
            postes.push(boucle_y);
            tmp.push(boucle_y);
            boucle_y++;
        }
        concordances_postes[element.idPoste] = tmp;
    })
    return [postes, concordances_postes];
}

function creation_concordances_benevoles_algo(all_benevoles_raid) {
    let boucle_z = 0;
    let liste_benevoles = [];
    let concordances_benevoles = [];

    all_benevoles_raid.forEach(function(element) {
        let tmp = [];
        tmp["idBenevole"] = element.idBenevole;
        tmp["id_benevole_algo"] = boucle_z;
        liste_benevoles.push(boucle_z)
        concordances_benevoles[boucle_z] = element.idBenevole;
        boucle_z++;
    })
    return [liste_benevoles, concordances_benevoles];
}

function remplir_prefpostes_algo(concordances_benevoles) {
        let new_prefpostes = [];
        let value = -1;
        let size  = concordances_benevoles.length;

        new_prefpostes = Array.apply(null,{length: size}).map(function() { return value; });

        return new_prefpostes;
    }

function creation_preferences_postes_algo(all_preferences_raid, concordances_benevoles, concordances_postes, prefpostes) {

    all_preferences_raid.forEach(function(element) {
        let idBenevole_algo = concordances_benevoles.indexOf(element.idBenevole);
        let idPoste_algo = concordances_postes[element.idPoste];
        let tmp = [];

        for(let i = 0 ; i < idPoste_algo.length ; i++) {
            tmp.push(idPoste_algo[i]);
        }
        if(prefpostes[idBenevole_algo] != -1) {
            prefpostes[idBenevole_algo] = prefpostes[idBenevole_algo].concat(tmp);
        }
        else {
            prefpostes[idBenevole_algo] = tmp;
        }
    })
    return prefpostes;
}

function ajout_preferences_supplementaire_benevoles(liste_benevoles, postes, prefpostes) {
    liste_benevoles.forEach(function(element_out) {
        postes.forEach(function(element_in) {
            if(prefpostes[element_out].length != undefined) {
                if(prefpostes[element_out].indexOf(element_in) == -1) {
                    prefpostes[element_out] = prefpostes[element_out].concat(element_in);
                }
            }
            else {
                prefpostes[element_out] = shuffle(postes);;
            }
        })
    })
    return prefpostes;
}

function creation_preferences_benevoles_pour_postes(liste_benevoles, postes) {
    let pref_benevoles = [];
    let couple = [];
    let nouveau_tirage = [];

    nouveau_tirage = liste_benevoles;
    for(let boucle_i=0 ; boucle_i < postes.length ; boucle_i++){
        nouveau_tirage = shuffle(nouveau_tirage);
        pref_benevoles.push(nouveau_tirage);
    };
    for(let boucle_j=0 ; boucle_j < liste_benevoles.length ; boucle_j++){
        couple.push(-1);
    };
    return [pref_benevoles, couple];
}

function shuffle(array) {
    let currentIndex = array.length, temporaryValue, randomIndex
    while (0 !== currentIndex) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }
    return array;
}

function attribution_automatique(couple, prefpostes, pref_benevoles, liste_benevoles) {
    let benevoles_non_attribue = liste_benevoles;
    let i = 0;
    let test;

    while(benevoles_non_attribue.length > 0) {
        if(couple[i] == -1) {
            test = couple.indexOf(prefpostes[i][0]);
            if(test == -1) {
                couple[i] = prefpostes[i][0];
                prefpostes[i].shift();
                benevoles_non_attribue.splice(benevoles_non_attribue.indexOf(i),1);
                i++;
                //i = i%benevoles_non_attribue.length;
            }
            else {
                if(pref_benevoles[prefpostes[i][0]].indexOf(i) < pref_benevoles[prefpostes[i][0]].indexOf(couple[test])) {
                    couple[test] = -1;
                    couple[i] = prefpostes[i][0];
                    prefpostes[i].shift();
                    benevoles_non_attribue.push(test);
                    benevoles_non_attribue.shift();
                    i++;
                    i = i%benevoles_non_attribue.length;
                }
                else {
                    prefpostes[i].shift();
                }
            }
        }
        else {
            i++;
            i = i%benevoles_non_attribue.length;
        }
    };
    return couple;
}

function ecrire_repartitions(couple, concordances_benevoles, concordances_postes, repartitions) {
    let boucle_finale_algo = 0;
    couple.forEach(function(element,key) {
        let true_Bene;
        let true_Poste;
        true_Bene = concordances_benevoles[boucle_finale_algo];
        concordances_postes.forEach(function(element_bene,key2) {
            if(element_bene.indexOf(element) != -1){
                true_Poste = key2;
            };
        })
        repartitions[true_Bene] = true_Poste;
        boucle_finale_algo++;
    })

    $.when(supprimerRepartitions(id_raid).done(function(data, textStatus, jqXHR){
        
        repartitions.forEach(function(element,key) {
            let repartition = {
                "idPoste":element,
                "idBenevole":key
            };
            envoi_repartitions_api(repartition); 
        })
    }))
}

function envoi_repartitions_api(repartition){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/repartitions',  
        type: 'POST',
        dataType: 'json',  
        headers: {"X-Auth-Token": token}, 
        data: { ...repartition },
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log("Nouvelle répartition créée");
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la création des répartitions');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function supprimerRepartitions(id) {
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/repartitions/raids/'+ id,  
        type: 'DELETE',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            SED.log('Toutes les répartitions du raid ont été suprimmées')
        },
        error: function (xhr, textStatus, errorThrown) {  
            SED.log('Erreur lors de la suppression des répartitions');  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}