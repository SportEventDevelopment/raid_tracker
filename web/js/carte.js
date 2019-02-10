var mymap;
var token;
var idparcours;
var drawnItems;
var drawControlFull;
var drawControlCreatePoste;
var controlImport;
var type;
var layer;
var points;
var departIcon;
var passageIcon;
var arriveeIcon;
var posteIcon;
var markers;

window.onload = function init(){
    mymap = L.map('mapid',{
        zoomControl:false
    });
    longitude = -2.93;
    latitude = 48.2;
    echelle = 9;
    token = $('#mapid').data('token');
    idparcours = $('#mapid').data('idparcours');

    departIcon = L.icon({
        iconUrl: asset_images+'/depart.png',
        iconSize: [40, 60],
        iconAnchor: [0, 60],
    });
    passageIcon = L.icon({
        iconUrl: asset_images+'/passage.png',
        iconSize: [31, 47],
        iconAnchor: [16, 47],
    });
    arriveeIcon = L.icon({
        iconUrl: asset_images+'/arrivee.png',
        iconSize: [40, 60],
        iconAnchor: [0, 60],
    });
    posteIcon = L.icon({
        iconUrl: asset_images+'/poste.png',
        iconSize: [40, 60],
        iconAnchor: [20, 60],
    });

    
    mymap.on('load', function(e){
        $(".form-poste").hide();
        
        let zoomControl = addZoom();
        this.addControl(zoomControl);

        L.Control.geocoder().addTo(this);
        
        traduireToolbar();
        
        drawnItems = new L.FeatureGroup();
        this.addLayer(drawnItems);
        
        controlImport = importGPX();
        controlImport.addTo(this);

        drawControlCreatePoste = addDrawControlCreatePoste();
        
        drawControlFull = addDrawControlFull(drawnItems);
        this.addControl(drawControlFull);

        markers = L.layerGroup().addTo(this);

        $.when(dessinerParcours(idparcours).done(function(data, textStatus, jqXHR){
            
            for (let i = 0; i < data.length; i++) {

                let trace_id = data[i].id;

                $.when(recupererTrace(trace_id).done(function(data, textStatus, jqXHR){

                    let tab_points= [];

                    for (let i = 0; i < data.length; i++) {
                        let point = []
                        point.push(data[i]['lat']);
                        point.push(data[i]['lon']);

                        if(data[i]['type'] == 0){
                            tab_points.push(point);
                        }
                        else if(data[i]['type'] == 1){
                            let depart = L.marker([data[i]['lat'], data[i]['lon']], {icon: departIcon})
                                .addTo(markers)
                                .bindPopup('Point de départ');
                            depart.idtrace = data[i]['idTrace']  
                            tab_points.push(point);
                        } 
                        else if(data[i]['type'] == 2){
                            let arrivee = L.marker([data[i]['lat'], data[i]['lon']], {icon: arriveeIcon})
                                .addTo(markers)
                                .bindPopup('Point d\'arrivée');
                            arrivee.idtrace = data[i]['idTrace']
                            tab_points.push(point);
                        }
                        else if(data[i]['type'] == 3){
                            let poste = L.marker([data[i]['lat'], data[i]['lon']], {icon: posteIcon})
                                .addTo(markers)
                                .bindPopup('Poste');
                            poste.idtrace = data[i]['idTrace']
                        }
                    }
                    
                    let polylines = L.polyline(tab_points, {
                        color:'green',
                        weight: 10
                    });
                    polylines.idtrace = trace_id;
                    drawnItems.addLayer(polylines);
                }));
            }
        }));
    });

    mymap.on('draw:created', function(e) {
        type = e.layerType;
        layer = e.layer;
        points = [];

        if (type === 'polyline') {

            layer.getLatLngs().forEach(function(point) {
                points.push(point);
            });
            
            let trace = {"idParcours": idparcours}
            $.when(creerTrace(trace).done(function(data, textStatus, jqXHR){
                layer.idtrace = data.id;
                sauvegarderTrace(points, data.id);          
            }));

            drawnItems.addLayer(layer);
        }
        else if ( type === 'polygon') {
            drawnItems.addLayer(layer);
        }
        else if (type === 'marker') {
            
            afficherFormulairePoste(layer.getLatLng());
            
            drawnItems.addLayer(layer);
        }
        else if (type === 'circlemarker') {
            drawnItems.addLayer(layer);
        }
        else if (type === 'circle') {
            drawnItems.addLayer(layer);
        }
        else if (type === 'rectangle') {
            drawnItems.addLayer(layer);
        }
    });
        
    mymap.on('draw:edited', function(e){
        
        e.layers.eachLayer(function(layer){

            let markers_to_remove = [];

            for (let i = 0; i < markers.getLayers().length; i++) {       
                if( markers.getLayers()[i].idtrace == layer.idtrace 
                    || markers.getLayers()[i].idtrace.id == layer.idtrace){
                    markers_to_remove.push(markers.getLayers()[i])
                }
            }
            for (let j = 0; j < markers_to_remove.length; j++) {
                markers.removeLayer(markers_to_remove[j])
            }

            $.when(supprimerTrace(layer.idtrace).done(function(data, textStatus, jqXHR){
                points = [];
                layer.getLatLngs().forEach(function(point) {
                    points.push(point);
                });
                
                let trace = {"idParcours": idparcours}
                $.when(creerTrace(trace).done(function(data, textStatus, jqXHR){
                    layer.idtrace = data.id;
                    sauvegarderTrace(points, data.id);
                }));
            }));
        });
    });

    mymap.on('draw:deleted', function(e) {

        e.layers.eachLayer(function(layer){

            let markers_to_remove = [];

            for (let i = 0; i < markers.getLayers().length; i++) {       
                if( markers.getLayers()[i].idtrace == layer.idtrace 
                    || markers.getLayers()[i].idtrace.id == layer.idtrace){
                    markers_to_remove.push(markers.getLayers()[i])
                }
            }
            for (let j = 0; j < markers_to_remove.length; j++) {
                markers.removeLayer(markers_to_remove[j])
            }

            supprimerTrace(layer.idtrace);
        });
    });

    mymap.setView([latitude, longitude], echelle);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mymap);

    positionUser();
}

function addZoom(){
    //Toolbar Zoom
    let zoomControl = new L.control.zoom({
        zoomInTitle: "Zoomer",
        zoomOutTitle: "Dézoomer"
    });

    return zoomControl;
}

function traduireToolbar(){
    console.log('Début de la traduction barre d\'outils...');

    //Toolbar Visible
    L.drawLocal.draw.toolbar.buttons.polyline = 'Créer un parcours';
    L.drawLocal.draw.toolbar.buttons.marker = 'Créer un nouveau poste';
    L.drawLocal.edit.toolbar.buttons.editDisabled = "Aucun élément à éditer";
    L.drawLocal.edit.toolbar.buttons.removeDisabled = "Aucun élément à supprimer";
    
    //Toolbar Visible Element Placé
    L.drawLocal.edit.toolbar.buttons.edit = "Editer parcours/points";
    L.drawLocal.edit.toolbar.buttons.remove = "Supprimer parcours/points";

    //Toolbar Polyline
    L.drawLocal.draw.toolbar.actions.text = "Annuler";
    L.drawLocal.draw.toolbar.actions.title = "Annuler";
    L.drawLocal.draw.toolbar.finish.text = "Terminer";
    L.drawLocal.draw.toolbar.finish.title = "Terminer la création du parcours";
    L.drawLocal.draw.toolbar.undo.text = "Supprimer le dernier point";
    L.drawLocal.draw.toolbar.undo.title = "Supprimer le dernier point dessiné";

    //Toolbar Polyline Texte Carte
    L.drawLocal.draw.handlers.polyline.tooltip.start = 'Cliquer sur la carte pour positionner le point de départ';
    L.drawLocal.draw.handlers.polyline.tooltip.cont = 'Cliquer sur la carte pour continuer le tracé';
    L.drawLocal.draw.handlers.polyline.tooltip.end = 'Cliquer sur le dernier point pour finaliser le parcours';

    //Toolbar Marker Texte
    L.drawLocal.draw.handlers.marker.tooltip.start = 'Cliquer sur la carte pour placer un point d\'intérêt';

    //Toolbar Edition
    L.drawLocal.edit.toolbar.actions.cancel.text = "Annuler";
    L.drawLocal.edit.toolbar.actions.cancel.title = "Annuler les derniers changements";
    L.drawLocal.edit.toolbar.actions.save.text = "Sauvegarder";
    L.drawLocal.edit.toolbar.actions.save.title = "Sauvegarder les changements";
    
    //Toolbar Edition Texte
    L.drawLocal.edit.handlers.edit.tooltip.text = "Maintenir un élément pour le déplacer";
    L.drawLocal.edit.handlers.edit.tooltip.subtext = "Retirer un point de passage en double-cliquant dessus";
    
    //Toolbar Suppression
    L.drawLocal.edit.toolbar.actions.clearAll.text = "Tout supprimer";
    L.drawLocal.edit.toolbar.actions.clearAll.title = "Supprimer tous les éléments sur la carte";

    //Toolbar Suppression Texte
    L.drawLocal.edit.handlers.remove.tooltip.text = "Cliquez sur un élément pour le supprimer";
    
    console.log('Fin de traduction...');
}

function afficherFormulairePoste(coords){

    $(".form-poste").show();
    controlMapInteractions(false);

    $(".form-poste").validate({
        rules : {
            'choix-trace' : {
                required : true
            },
            'name-poste' : {
                required: true
            },
            'nb-benevole' : {
                required: true
            }
        },
        messages : {
            "choix-trace" : "Veuillez sélectionner un tracé",
            "name-poste" : "Veuillez entrer l'intitulé du poste",
            "nb-benevole": "Veuillez renseigner le nombre de bénévoles pour ce poste (1 à 150)"
        },
        submitHandler: function(form, event) {
            event.preventDefault();
            let form_data = $(form).serializeArray();
            
            let new_point = {
                "idTrace": form_data[0].value,
                "lat": coords.lat,
                "lon": coords.lng,
                "ordre": 0,
                "type": 3
            };

            $.when(creerPoint(new_point).done(function(data, textStatus, jqXHR){

                let date1 = form_data[3].value.split(/-|T|:/);
                let date2 = form_data[4].value.split(/-|T|:/);

                hd_reformat = date1[2] +"/"+date1[1]+"/"+ date1[0]+" "+date1[3]+":"+date1[4];
                hf_reformat = date2[2] +"/"+date2[1]+"/"+ date2[0]+" "+date2[3]+":"+date2[4];

                let new_poste = {
                    "idPoint": data.id,
                    "type": form_data[1].value,
                    "nombre": form_data[2].value,
                    "heureDebut": hf_reformat,
                    "heureFin": hd_reformat
                };

                $.when(creerPoste(new_poste).done(function(data, textStatus, jqXHR){
                    $('.form-poste').hide();
                    controlMapInteractions(true);
                }));
            }));
        }
    });

    $(".annuler-poste").click(function(e){
        e.preventDefault();

        $(".form-poste").hide();
        controlMapInteractions(true);
    });    
    
    $('#choix-trace')
        .find('option')
        .remove()
        .end()

    $.when(recupererTraces(idparcours).done(function(data, textStatus, jqXHR){

        let count = 1;

        data.forEach((trace) => {
            let opt = document.createElement('option');
            opt.value = trace.id;
            opt.innerHTML = "Tracé n°"+count;
            $('#choix-trace').append(opt);
            count++;
        });
    }));
}

function sauvegarderTrace(points, trace_id) {
    let point = [];
                
    for (let i = 0; i < points.length; i++) {
        point["idTrace"] = trace_id;
        point["ordre"] = i;
        point["lat"] = points[i].lat;
        point["lon"] = points[i].lng;

        if(i == 0){
            point["type"] = 1;
            let depart = L.marker([point["lat"], point["lon"]], {icon: departIcon})
                .addTo(markers)
                .bindPopup('Point de départ');
            depart.idtrace = trace_id;
        }
        else if(i == points.length-1){
            point["type"] = 2;
            let arrivee = L.marker([point["lat"], point["lon"]], {icon: arriveeIcon})
                .addTo(markers)
                .bindPopup('Point de d\'arrivée');
            arrivee.idtrace = trace_id;
        }
        else{
            point["type"] = 0;
        }

        creerPoint(point);
    }
}

function controlMapInteractions(enable){
    if(enable){
        mymap.dragging.enable();
        drawControlCreatePoste.remove();
        controlImport.addTo(mymap);
        drawControlFull.addTo(mymap);
    
    } else {
        mymap.dragging.disable();
        drawControlFull.remove();
        controlImport.remove();
        drawControlCreatePoste.addTo(mymap);
    }
}

function recupererTraces(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces/parcours/' + id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){            
            console.log("Parcours ["+ id +"] récupéré avec succès!")
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    }); 
}

function dessinerParcours(id){

    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces/parcours/' + id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){            
            console.log("Parcours ["+ id +"] dessiné avec succès!")
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function supprimerParcours(id) {
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces/parcours/'+ id,  
        type: 'DELETE',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            console.log('Parcours supprimé ['+ data.id +']')
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function creerTrace(trace){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces',  
        type: 'POST',
        dataType: 'json',
        headers: {
            "X-Auth-Token": token
        },
        data: trace,
        beforeSend: function(){
            $("#loader").show();
        },
        success: function (data, textStatus, xhr) {  
            console.log('Nouveau tracé créé ['+ data.id +']');
        },  
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function recupererTrace(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/points/traces/' + id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message+" ["+id+"]");
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function supprimerTrace(id){
    return $.ajax({
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces/' + id,  
        type: 'DELETE',
        dataType: 'json',
        headers: {
            "X-Auth-Token": token
        },
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            console.log('Tracé supprimé [' + id + '] avec succès!');
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function creerPoint(point){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/points',  
        type: 'POST',
        dataType: 'json',  
        headers: {"X-Auth-Token": token}, 
        data: { ...point },
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            console.log(data)
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function creerPoste(poste){

    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/postes',  
        type: 'POST',
        dataType: 'json',  
        headers: {"X-Auth-Token": token}, 
        data: poste,
        beforeSend: function(){
            $("#loader").show();
        },
        success: function(data){
            console.log('Nouveau poste créé ['+ data.id +']');
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log(xhr.responseJSON.message);  
        },
        complete:function(data){   
            $("#loader").hide();
        }
    });
}

function importGPX(){

    L.Control.FileLayerLoad.TITLE = 'Importer parcours (GPX, GeoJSON)';
    L.Control.FileLayerLoad.LABEL = '<img class="icon" src="/raid_tracker/web/images/folder.png">';

    return L.Control.fileLayerLoad({
        fitBounds: true,
        layerOptions: {
            style: {
                color: 'red',
                weight: 10,
                opacity: 1,
                clickable:true
            },
            pointToLayer: function (data, latlng){
                return L.circleMarker(latlng, {
                    style: {
                        color:'purple',
                        opacity: 0,
                        fillOpacity: 0.5,
                        weight: 1,
                        clickable: false
                    }
                });
            },
            onEachFeature: function (data, layer) {
                let tab_points_import_gpx = [];
                
                layer.getLatLngs().forEach(function(element) {
                    tab_points_import_gpx.push(element);
                });
                
                let trace = {"idParcours": idparcours}
                $.when(creerTrace(trace).done(function(data, textStatus, jqXHR){
                    layer.idtrace = data.id;
                    sauvegarderTrace(tab_points_import_gpx, data.id);
                }));
                
                drawnItems.addLayer(layer);
            }
        }
    })
}

function addDrawControlCreatePoste(){
    return new L.Control.Draw({
        draw: false,
        edit: false
    });
}

function addDrawControlFull(drawnItems){
    let drawControlFull = new L.Control.Draw({
        position:'topleft',
        edit: {
            featureGroup: drawnItems,
            remove:true
        },
        draw: {
            polyline: {
                shapeOptions: {
                    color: 'orange',
                    weight: 10,
                }
            },
            circle: false,
            polygon: false,
            marker: {
                icon: posteIcon,
                repeatMode: false
            },
            circlemarker: false,
            rectangle: false,
        },
    });

    return drawControlFull;
}

function positionUser() {
    if(navigator.geolocation) {        
        navigator.geolocation.getCurrentPosition(showPosition);                
    }
    else {
        alert("La localisation n'est pas disponible avec votre navigateur !");
    }
}

function showPosition(position) {
    latitude = position.coords.latitude;
    longitude = position.coords.longitude;
    echelle = 18;

    mymap.setView([latitude, longitude], echelle);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mymap);

    let latlng = [latitude, longitude];
    let marker = L.marker([latitude, longitude]).addTo(mymap);
    let popup = L.popup().setLatLng(latlng).setContent('<p>Vous êtes ici<br>Latitude: '+ latitude +'<br>Longitude: '+longitude+'</p>').openOn(mymap);
    marker.bindPopup(popup).openPopup();
}