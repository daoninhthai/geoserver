<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <style>
        
        .map, .righ-panel {
                height: 500px;
                width: 80%;
                float: left;
            }
            
        .map,
        .righ-panel {
            height: 98vh;
            width: 80vw;
            float: left;
        }

        .map {
            border: 1px solid #000;
        }

        .ol-popup {
            position: absolute;
            background-color: white;
            -webkit-filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
            filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #cccccc;
            bottom: 12px;
            left: -50px;
            min-width: 180px;
        }

        .ol-popup:after,
        .ol-popup:before {
            top: 100%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }

        .ol-popup:after {
            border-top-color: white;
            border-width: 10px;
            left: 48px;
            margin-left: -10px;
        }

        .ol-popup:before {
            border-top-color: #cccccc;
            border-width: 11px;
            left: 48px;
            margin-left: -11px;
        }

        .ol-popup-closer {
            text-decoration: none;
            position: absolute;
            top: 2px;
            right: 8px;
        }

        .ol-popup-closer:after {
            content: "✖";
        }
    </style>
</head>

<body onload="initialize_map();">

    <table>

        <tr>

            <td>
                <div id="map" class="map"></div>
                <div id="map" style="width: 50vw; height: 50vh;"></div>
                <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                    <div id="popup-content"></div>
                </div>
                <!--<div id="map" style="width: 80vw; height: 100vh;"></div>-->
            </td>
            <td>
                <input type="textinput" class="tags" id="ctiy"><br/>
                <button id="btnSeacher"> Tìm kiếm</button>
                <br />
                <br />
                <br />

                <input onclick="oncheckhydropower();" type="checkbox" id="hydropower" name="layer" value="hydropower"> Thủy điện<br />
                <input onclick="oncheckcang();" type="checkbox" id="cang" name="layer" value="cang"> Cảng <br />
                <input onclick="oncheckriver();" type="checkbox" id="river" name="layer" value="river"> Sông <br />
                <input onclick="oncheckgiaothong();" type="checkbox" id="giaothong" name="layer" value="giaothong"> Giao thông <br />
                <input onclick="onchecksanbay();" type="checkbox" id="sanbay" name="layer" value="sanbay"> Sân bay <br />
                <input onclick="oncheckstation();" type="checkbox" id="station" name="layer" value="station"> Ga tàu <br />
                <input onclick="oncheckduongray();" type="checkbox" id="duongray" name="layer" value="duongray"> Đường ray <br />
                <input onclick="oncheckvn()" type="checkbox" id="vn" name="layer" value="vn"> Việt Nam<br />
                <button id="btnRest"> Reset</button>

            </td>
        </tr>
    </table>
    <?php include 'CMR_pgsqlAPI.php' ?>

    <script>
        var format = 'image/png';
        var map;
        var minX = 102.107963562012;
        var minY = 8.30629825592041;
        var maxX = 109.505798339844;
        var maxY = 23.4677505493164;
        var cenX = (minX + maxX) / 2;
        var cenY = (minY + maxY) / 2;
        var mapLat = cenY;
        var mapLng = cenX;
        var mapDefaultZoom = 5;
        var layerCMR_adm1;
        var layer_river;
        var layer_hydropower;

        var layer_cang;
        var layer_giaothong;
        var layer_station;
        var layer_duongray;
        var layer_sanbay;


        var vectorLayer;
        var styleFunction;
        var styles;
        var container = document.getElementById('popup');
        var content = document.getElementById('popup-content');
        var closer = document.getElementById('popup-closer');
        var ctiy = document.getElementById("ctiy");
        var chkVN = document.getElementById("vn");
        var chkHydro = document.getElementById("hydropower");
        var chkRiver = document.getElementById("river");

        var chkCang = document.getElementById("cang");
        var chkGiaoThong = document.getElementById("giaothong");
        var chkSanBay = document.getElementById("sanbay");
        var chkStation = document.getElementById("station");
        var chkDuongRay = document.getElementById("duongray");

        var value ;
        /**
         * Create an overlay to anchor the popup to the map.
         */
        var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */ ({
            element: container,
            autoPan: true,
            autoPanAnimation: {
                duration: 250
            }
        }));
        closer.onclick = function() {
            overlay.setPosition(undefined);
            closer.blur();
            return false;
        };
        function handleOnCheck(id, layer) {
            if (document.getElementById(id).checked) {
                value = document.getElementById(id).value;
                // map.setLayerGroup(new ol.layer.Group())
                map.addLayer(layer)
                vectorLayer = new ol.layer.Vector({});
                map.addLayer(vectorLayer);
            } else {
                map.removeLayer(layer);
                map.removeLayer(vectorLayer);
            }
        }
        function myFunction() {
            var popup = document.getElementById("popup");
            popup.classList.toggle("show");
        }
        //dap
        function oncheckhydropower() {
            handleOnCheck('hydropower', layer_hydropower);

        }
        //river
        function oncheckriver() {
            handleOnCheck('river', layer_river);

        }
        //cang
        function oncheckcang() {
            handleOnCheck('cang', layer_cang);

        }
          //giao thong
          function oncheckgiaothong() {
            handleOnCheck('giaothong', layer_giaothong);

        }
          //san bay
          function onchecksanbay() {
            handleOnCheck('sanbay', layer_sanbay);

        }
          //station
          function oncheckstation() {
            handleOnCheck('station', layer_station);

        }
          //duongray
          function oncheckduongray() {
            handleOnCheck('duongray', layer_duongray);

        }
        //vn
        function oncheckvn() {
            handleOnCheck('vn', layerCMR_adm1);
        }

        function initialize_map() {

         
            layerBG = new ol.layer.Tile({
                source: new ol.source.OSM({})
            });

        
            layerCMR_adm1 = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'gadm36_vnm_1',
                    }
                })

            });

            layer_river = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'river',
                    }
                })

            });

            layer_hydropower = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'hydropower_dams',
                    }
                })

            });

            layer_cang = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'cang',
                    }
                })

            });

            layer_duongray = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'vnm_rlwl_2015_osm',
                    }
                })

            });

            layer_giaothong = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'giao_thong_wgs84',
                    }
                })

            });
            layer_sanbay = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'sanbay',
                    }
                })

            });

            layer_station = new ol.layer.Image({
                source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8080/geoserver/data1/wms?',
                    params: {
                        'FORMAT': format,
                        'VERSION': '1.1.1',
                        STYLES: '',
                        LAYERS: 'station',
                    }
                })

            });
           

            var viewMap = new ol.View({
                center: ol.proj.fromLonLat([mapLng, mapLat]),
                zoom: mapDefaultZoom
            });
            map = new ol.Map({
                target: "map",
                layers: [layerBG],
                view: viewMap,
                overlays: [overlay], //them khai bao overlays
            });
            styles = {
                'Point': new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'yellow',
                        width: 3
                    })
                }),
                'MultiLineString': new ol.style.Style({

                    stroke: new ol.style.Stroke({
                        color: 'red',
                        width: 3
                    })
                }),
                'Polygon': new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'red',
                        width: 3
                    })
                }),
                'MultiPolygon': new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'orange'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'yellow',
                        width: 2
                    })
                })
            };
            styleFunction = function(feature) {
                return styles[feature.getGeometry().getType()];
            };
            var stylePoint = new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 0.5],
                    anchorXUnits: "fraction",
                    anchorYUnits: "fraction",
                    src: "http://localhost:1131/WebGIS/WebGIS_First/BTL/BTL/Yellow_dot.svg"
                })
            });
            vectorLayer = new ol.layer.Vector({
                style: styleFunction
            });
            map.addLayer(vectorLayer);
            var buttonReset = document.getElementById("btnRest").addEventListener("click", () => {
                location.reload();
            })
            var button = document.getElementById("btnSeacher").addEventListener("click",
                () => {
                    vectorLayer.setStyle(styleFunction);
                    ctiy.value.length ?
                        $.ajax({
                            type: "POST",
                            url: "CMR_pgsqlAPI.php",
                            data: {
                                name: ctiy.value
                            },
                            success: function(result, status, erro) {

                                if (result == 'null')
                                    alert("không tìm thấy đối tượng");
                                else
                                    highLightObj(result);
                            },
                            error: function(req, status, error) {
                                alert(req + " " + status + " " + error);
                            }
                        }) : alert("Nhập dữ liệu tìm kiếm")
                });
            function createJsonObj(result) {
                var geojsonObject = '{' +
                    '"type": "FeatureCollection",' +
                    '"crs": {' +
                    '"type": "name",' +
                    '"properties": {' +
                    '"name": "EPSG:4326"' +
                    '}' +
                    '},' +
                    '"features": [{' +
                    '"type": "Feature",' +
                    '"geometry": ' + result +
                    '}]' +
                    '}';
                return geojsonObject;
            }
            function highLightGeoJsonObj(paObjJson) {
                var vectorSource = new ol.source.Vector({
                    features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    })
                });
                vectorLayer.setSource(vectorSource);
            }

            function highLightObj(result) {
                var strObjJson = createJsonObj(result);
                var objJson = JSON.parse(strObjJson);
                highLightGeoJsonObj(objJson);
            }

            function displayObjInfo(result, coordinate) {
                $("#popup-content").html(result);
                overlay.setPosition(coordinate);

            }
            function bla(result){
            
            }

            map.on('singleclick', function(evt) {
                var myPoint = 'POINT(12,5)';
                var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                var lon = lonlat[0];
                var lat = lonlat[1];
                var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                
                if (value == 'vn') {
                    vectorLayer.setStyle(styleFunction);

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoCMRToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getGeoCMRToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
                if (value == "river") {
                    //river
                    vectorLayer.setStyle(styleFunction);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoRiveroAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getRiverToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
                //duong ray
                if (value == "duongray") {
                    
                    vectorLayer.setStyle(styleFunction);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoDuongRayToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getDuongRayToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }
                //giao thong
                if (value == "giaothong") {
                    
                    vectorLayer.setStyle(styleFunction);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoGiaoThongToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getGiaoThongToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                }

                //dap
                if (value == "hydropower") {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoHyproPowerToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getGeoEagleToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    
                }
                //station
                if (value == "station") {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoStationToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getStationToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    
                }
                 //cang
                 if (value == "cang") {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoCangToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getCangToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    
                }

                 //san bay
                 if (value == "sanbay") {
                    vectorLayer.setStyle(stylePoint);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getInfoSanBayToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            displayObjInfo(result, evt.coordinate);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {
                            functionname: 'getSanBayToAjax',
                            paPoint: myPoint
                        },
                        success: function(result, status, erro) {
                            highLightObj(result);
                        },
                        error: function(req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                    
                }

            });
        };
        $( function() {
            availableTags=[];
           
             $( "#ctiy" ).autocomplete({
             source: availableTags
            });
        } );




    </script>
</body>

</html>