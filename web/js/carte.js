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

    /*var osmGeocoder = new L.Control.OSMGeocoder({
        position:'topright',
        collapsed:false
    });
    mymap.addControl(osmGeocoder);*/

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
    //console.log(mymap);
    //console.log(L.drawLocal);
    console.log(drawControl);

    mymap.addControl(drawControl);

    var editableLayers = new L.FeatureGroup();
    mymap.addLayer(editableLayers);

    mymap.on('draw:created', function(e) {
        var type = e.layerType,
            layer = e.layer;
            //console.log(layer);

        if (type === 'polyline') {
            drawnItems.addLayer(layer);
            //console.log(e.layer.editing.latlngs[0]);
            //console.log(e.layer.editing.latlngs[0][0]);
            var tableauPointsParcours = [];
            e.layer.editing.latlngs[0].forEach(function(element) {
                //console.log(element.lat);
                tableauPointsParcours.push(element);
              });
            
            //console.log(tableauPointsParcours[0]);
            var points = new Object();
            var point = [];
            sizeTableauPointsParcours = tableauPointsParcours.length;
            for (var i = 0; i < sizeTableauPointsParcours; i++) {
                point["ordre"] = i;
                point["lat"] = tableauPointsParcours[i].lat;
                point["lng"] = tableauPointsParcours[i].lng;
                points.ordre = i;
                points.lat = tableauPointsParcours[i].lat;
                points.lng = tableauPointsParcours[i].lng;
                if(i = 0){
                    point["type"] = 1;
                    points.type = 1;
                }
                else if(i = sizeTableauPointsParcours-1){
                    point["type"]= 2;
                    points.type = 2;
                }
                else{
                    point["type"] = 0;
                    points.type = 0;
                }
                //points.push(point);

                var json = JSON.stringify(points); 
                $.ajax({  
                    url: 'https://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/points',  
                    type: 'POST',
                    headers: {"X-Auth-Token": Comment_Récupérer_Le_Token?xD}, 
                    dataType: 'json',  
                    data: json,  
                    success: function (data, textStatus, xhr) {  
                        console.log(data);  
                    },  
                    error: function (xhr, textStatus, errorThrown) {  
                        console.log('Error in Operation');  
                    }
                });
            }
              console.log(point);
/*
              
            person.name = $('#name').val();  
            person.surname = $('#surname').val();  
            $.ajax({  
                url: 'api/points',  
                type: 'POST',  
                dataType: 'json',  
                data: point,  
                success: function (data, textStatus, xhr) {  
                    console.log(data);  
                },  
                error: function (xhr, textStatus, errorThrown) {  
                    console.log('Error in Operation');  
                }
            });  */
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