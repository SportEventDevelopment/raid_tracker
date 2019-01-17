var mymap;
var token;
var idparcours;
var drawnItems;
var drawControl;
var type;
var layer;
var points;
var departIcon;
var passageIcon;
var arriveeIcon;
var posteIcon;

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
        iconAnchor: [0, 60],
    });

    mymap.on('load', function(e){
        let zoomControl = addZoom();
        this.addControl(zoomControl);

        L.Control.geocoder().addTo(this);

        drawnItems = new L.FeatureGroup();
        this.addLayer(drawnItems);
    
        drawControl = addDrawControl(drawnItems);
        this.addControl(drawControl);

        traduireToolbar();
        
        $.when(dessinerParcours()
            .done(function(a1, a2, a3, a4){                
                console.log('Parcours dessiné avec succès');   
            })
        );
    });
 
    mymap.on('draw:created', function(e) {
        type = e.layerType;
        layer = e.layer;
        points = [];

        if (type === 'polyline') {
            drawnItems.addLayer(layer);

            e.layer.editing.latlngs[0].forEach(function(point) {
                points.push(point);
            });
            
            let trace = {"idParcours": idparcours}
            $.when(creerTrace(trace).done(function(a1, a2, a3, a4){
                console.log("Nouveau trajet sauvegardé avec succès");
            }));
        }
        else if ( type === 'polygon') {
            drawnItems.addLayer(layer);
        }
        else if (type === 'marker') {
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
            
            $.when(supprimerTrace(layer.idtrace).done(function(a1, a2, a3, a4){
                console.log('Tracé supprimé ['+ layer.idtrace +']');
            }));

            points = [];
            layer.editing.latlngs[0].forEach(function(point) {
                points.push(point);
            });
            
            let trace = {"idParcours": idparcours}
            $.when(creerTrace(trace).done(function(a1, a2, a3, a4){
                console.log('Nouveau tracé enregistré');
            }));
        });
    });

    mymap.on('draw:deleted', function(e) {
        e.layers.eachLayer(function(layer){
            $.when(supprimerTrace(layer.idtrace).done(function(a1, a2, a3, a4){
                console.log('Tracé supprimé avec succès ['+ layer.idtrace + ']');
            }));
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

function dessinerParcours(){

    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces/parcours/' + idparcours,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        success: function(data){
            for (let i = 0; i < data.length; i++) {
                recupererTrace(data[i].id)
            }
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log('Erreur lors de la récupération du parcours ['+ idparcours + ']');  
        }
    });
}

function supprimerParcours(id) {
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/traces/parcours/'+ id,  
        type: 'DELETE',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        success: function(data){
            console.log('Tracés supprimés')
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log('Erreur lors de l\'enregistrement du point');  
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
        success: function (data, textStatus, xhr) {  
            let point = [];
            let sizeTabPoints = points.length;
            
            for (let i = 0; i < sizeTabPoints; i++) {
                point["idTrace"] = data['id'];
                point["ordre"] = i;
                point["lat"] = points[i].lat;
                point["lon"] = points[i].lng;

                if(i == 0){
                    point["type"] = 1;
                    
                    L.marker([point['lat'], point['lon']], {icon: departIcon})
                        .addTo(mymap)
                        .bindPopup('Point de départ');
                    
                }
                else if(i == sizeTabPoints-1){
                    point["type"]= 2;
                    
                    L.marker([point['lat'], point['lon']], {icon: arriveeIcon})
                        .addTo(mymap)
                        .bindPopup('Point de d\'arrivée');
                }
                else{
                    point["type"] = 0;
                }

                $.when(creerPoint(point).done(function(a1, a2, a3, a4){
                    console.log(a1);
                }));
            }                    
        },  
        error: function (xhr, textStatus, errorThrown) {  
            console.log(textStatus);
        }
    });
}

function recupererTrace(id){
    return $.ajax({  
        url: 'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/points/traces/' + id,  
        type: 'GET',
        dataType: 'json',  
        headers: {"X-Auth-Token": token},
        success: function(data){
            let tab_points= [];

            for (let i = 0; i < data.length; i++) {
                let point = []
                point.push(data[i]['lat']);
                point.push(data[i]['lon']);

                if(data[i]['type'] == 1){
                    let depart = L.marker([data[i]['lat'], data[i]['lon']], {icon: departIcon})
                        .addTo(mymap)
                        .bindPopup('Point de départ');
                    depart.idtrace = data[i]['idTrace']
        
                }
                else if(data[i]['type'] == 2){
                    let arrivee = L.marker([data[i]['lat'], data[i]['lon']], {icon: arriveeIcon})
                        .addTo(mymap)
                        .bindPopup('Point d\'arrivée');
                    arrivee.idtrace = data[i]['idTrace']
                }
                tab_points.push(point);
            }
            
            let polylines = L.polyline(tab_points, {
                color:'green',
                weight: 10
            });
            polylines.idtrace = id;
            drawnItems.addLayer(polylines);
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log('Erreur lors de la récupération des points du tracé [' + id + ']');  
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
        success: function(data){
            console.log('Tracé supprimé');
        },
        error: function (xhr, textStatus, errorThrown) {  
            console.log('Erreur lors de la suppression du tracé ['+ id +']');  
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
        error: function (xhr, textStatus, errorThrown) {  
            console.log('Erreur lors de l\'enregistrement du point');  
        }
    });
}


function addDrawControl(drawnItems){
    let drawControl = new L.Control.Draw({
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
                repeatMode:true
            },
            circlemarker: false,
            rectangle: false,
        },
    });
    return drawControl;
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