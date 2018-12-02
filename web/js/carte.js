window.onload = function init(){
    var mymap = L.map('mapid',{
        zoomControl:false
    });
    longitude = 2.43896484375;
    latitude = 46.52863469527167;
    echelle = 6;

    mymap.setView([latitude, longitude], echelle);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mymap);

    var drawnItems = new L.FeatureGroup();
    mymap.addLayer(drawnItems);

    //Toolbar Visible
    L.drawLocal.draw.toolbar.buttons.polyline = 'Créer un parcours';
    L.drawLocal.draw.toolbar.buttons.marker = 'Ajouter un point d\'intérêt';
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

    //Toolbar Zoom
    var zoomControl = new L.control.zoom({
        zoomInTitle: "Zoomer",
        zoomOutTitle: "Dézoomer"
    });

    L.Control.geocoder().addTo(mymap);

    mymap.addControl(zoomControl);

    var drawControl = new L.Control.Draw({
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

    mymap.addControl(drawControl);

    var editableLayers = new L.FeatureGroup();
    mymap.addLayer(editableLayers);

    mymap.on('draw:created', function(e) {
        var type = e.layerType,
            layer = e.layer;

        if (type === 'polyline') {
            drawnItems.addLayer(layer);

            let points = [];
            e.layer.editing.latlngs[0].forEach(function(point) {
                points.push(point);
            });
            
            let point = [];
            sizeTabPoints = points.length;
            console.log($('#mapid').data('trace'));
            for (let i = 0; i < sizeTabPoints; i++) {
                point["idTrace"] = $('#mapid').data('trace');
                point["ordre"] = i;
                point["lat"] = points[i].lat;
                point["lng"] = points[i].lng;

                if(i == 0){
                    point["type"] = 1;
                }
                else if(i == sizeTabPoints-1){
                    point["type"]= 2;
                }
                else{
                    point["type"] = 0;
                }

                console.log(point);
                var token = $('#mapid').data('token');
                $.ajax({  
                    url: 'https://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/points',  
                    type: 'POST',
                    headers: {"X-Auth-Token":token}, 
                    dataType: 'json',  
                    data: point,
                    success: function (data, textStatus, xhr) {  
                        console.log(data);  
                    },  
                    error: function (xhr, textStatus, errorThrown) {  
                        console.log('Error in Operation');  
                    }
                });
            }
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
        editableLayers.addLayer(layer);
    });
/*
    mymap.on('draw:edited', function(e) {
        var type = e.layerType,
            layer = e.layer;
            drawnItems.addLayer(layer);
            editableLayers.addLayer(layer);
    });
*/
    var positionUser = function() {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        }
        else {
            alert("La localisation n'est pas disponible avec votre navigateur !");
        }
    }

    var showPosition = function(position) {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;
        echelle = 18;

        mymap.setView([latitude, longitude], echelle);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);

        var latlng = [latitude, longitude];
        var marker = L.marker([latitude, longitude]).addTo(mymap);
        var popup = L.popup().setLatLng(latlng).setContent('<p>Vous êtes ici<br>Latitude: '+ latitude +'<br>Longitude: '+longitude+'</p>').openOn(mymap);
        marker.bindPopup(popup).openPopup();
        
    }
    positionUser();
}