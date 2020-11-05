<?php
$paPDO = initDB();
$paSRID = '4326';
if (isset($_POST['functionname'])) {
    $paPoint = $_POST['paPoint'];

    $functionname = $_POST['functionname'];

    $aResult = "null";
    if ($functionname == 'getGeoCMRToAjax')
        $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoCMRToAjax')
        $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoRiveroAjax')
        $aResult = getInfoRiveroAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoHyproPowerToAjax')
        $aResult = getInfoHyproPowerToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getGeoEagleToAjax')
        $aResult = getGeoEagleToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getRiverToAjax')
        $aResult = getRiverToAjax($paPDO, $paSRID, $paPoint);
    
    else if ($functionname == 'getInfoGiaoThongToAjax')
        $aResult = getInfoGiaoThongToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoSanBayToAjax')
        $aResult = getInfoSanBayToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoCangToAjax')
        $aResult = getInfoCangToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoStationToAjax')
        $aResult = getInfoStationToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getInfoDuongRayToAjax')
        $aResult = getInfoDuongRayToAjax($paPDO, $paSRID, $paPoint);

    else if ($functionname == 'getGiaoThongToAjax')
        $aResult = getGiaoThongToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getSanBayToAjax')
        $aResult = getSanBayToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getCangToAjax')
        $aResult = getCangToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getStationToAjax')
        $aResult = getStationToAjax($paPDO, $paSRID, $paPoint);
    else if ($functionname == 'getDuongRayToAjax')
        $aResult = getDuongRayToAjax($paPDO, $paSRID, $paPoint);

    echo $aResult;

    closeDB($paPDO);
}
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $aResult = seacherCity($paPDO, $paSRID, $name);
    echo $aResult;
}

function initDB()
{
    // Kết nối CSDL
    $paPDO = new PDO('pgsql:host=localhost;dbname=data1;port=5432', 'postgres', 'postgres');
    return $paPDO;
}
function query($paPDO, $paSQLStr)
{
    try {
        // Khai báo exception
        $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sử đụng Prepare 
        $stmt = $paPDO->prepare($paSQLStr);
        // Thực thi câu truy vấn
        $stmt->execute();

        // Khai báo fetch kiểu mảng kết hợp
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        // Lấy danh sách kết quả
        $paResult = $stmt->fetchAll();
        return $paResult;
    } catch (PDOException $e) {
        echo "Thất bại, Lỗi: " . $e->getMessage();
        return null;
    }
}
function closeDB($paPDO)
{
    // Ngắt kết nối
    $paPDO = null;
}

// hightlight VN
function getGeoCMRToAjax($paPDO, $paSRID, $paPoint)
{
    
    $paPoint = str_replace(',', ' ', $paPoint);
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_vnm_1\" where ST_Within('SRID=4326;" . $paPoint . "'::geometry,geom)";
    $result = query($paPDO, $mySQLStr);
    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
// hightlight Thuy dien
function getGeoEagleToAjax($paPDO, $paSRID, $paPoint)
{
    
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hydropower_dams";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from hydropower_dams where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight san bay
function getGeoSanBayToAjax($paPDO, $paSRID, $paPoint)
{
    
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from sanbay";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from sanbay where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight station
function getGeoStationToAjax($paPDO, $paSRID, $paPoint)
{
    
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from station";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from station where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight cang
function getGeoCangToAjax($paPDO, $paSRID, $paPoint)
{
    
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from cang";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from cang where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight Song
function getRiverToAjax($paPDO, $paSRID, $paPoint)
{
   
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from river";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from river where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight giao thong
function getGiaoThongToAjax($paPDO, $paSRID, $paPoint)
{
   
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from giao_thong_wgs84";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from giao_thong_wgs84 where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// hightlight duong ray
function getDuongRayToAjax($paPDO, $paSRID, $paPoint)
{
   
    $paPoint = str_replace(',', ' ', $paPoint);
    
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from vnm_rlwl_2015_osm";
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from vnm_rlwl_2015_osm where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}

// Truy van thong tin VN
function getInfoCMRToAjax($paPDO, $paSRID, $paPoint)
{
   
    $paPoint = str_replace(',', ' ', $paPoint);
    $mySQLStr = "SELECT gid, name_1, ST_Area(geom) dt, ST_Perimeter(geom) as cv from \"gadm36_vnm_1\" where ST_Within('SRID=4326;" . $paPoint . "'::geometry,geom)";
    
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Mã Vùng: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên Tỉnh: ' . $item['name_1'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Diện Tích: ' . $item['dt'] . ' km2 ' .'</td></tr>';
            $resFin = $resFin . '<tr><td>Chu vi: ' . $item['cv'] . ' km '.'</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

//Truy van thong tin Song 
function getInfoRiveroAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from river";
    $mySQLStr = "SELECT *  from river where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên Sông: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Chiều dài: ' . $item['length'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

//Truy van thong tin duong ray
function getInfoDuongRayAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from vnm_rlwl_2015_osm";
    $mySQLStr = "SELECT *  from vnm_rlwl_2015_osm where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên đường ray: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Chiều dài: ' . $item['length'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

//Truy van thong tin giao thong
function getInfoGiaoThongAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from giao_thong_wgs84";
    $mySQLStr = "SELECT *  from giao_thong_wgs84 where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên đường: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Chiều dài: ' . $item['length'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

// truy van thong tin thuy dien
function getInfoHyproPowerToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from hydropower_dams";
    $mySQLStr = "SELECT * from hydropower_dams where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";

    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Kinh độ: ' . $item['long'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Vĩ độ: ' . $item['lat'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

// truy van thong tin station
function getInfoStationToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from station";
    $mySQLStr = "SELECT * from station where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";

    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Kinh độ: ' . $item['long'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Vĩ độ: ' . $item['lat'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

// truy van thong tin san bay
function getInfoSanBayToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from sanbay";
    $mySQLStr = "SELECT * from sanbay where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";

    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Kinh độ: ' . $item['long'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Vĩ độ: ' . $item['lat'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}

// truy van thong tin cang
function getInfoCangToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from cang";
    $mySQLStr = "SELECT * from cang where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.05";

    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>Tên: ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Kinh độ: ' . $item['long'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Vĩ độ: ' . $item['lat'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}



//tim kiem
function seacherCity($paPDO, $paSRID, $name)
{
    
    $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from gadm36_vnm_1 where name_1 like '$name' ";
    $result = query($paPDO, $mySQLStr);
    
    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
}
function fun($paPDO, $paSRID, $name)
{
    
    $mySQLStr = "select name_1 from gadm36_vnm_1";
    $result = query($paPDO, $mySQLStr);
    
    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['name_1'];
        }
    } else
        return "null";
}
