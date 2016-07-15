<?php

// 连接数据库
$server = 'mobilesensor.cfjjvgdzpcab.ap-northeast-1.rds.amazonaws.com';
$username = 'mobilesensor';
$password = 'Sensor1!';
$database = 'mobilesensor';

$connection = mssql_pconnect($server, $username, $password);
if ($connection != FALSE) {
    echo "Connected to the database server successfully<br>";
} else {
    echo '连接数据库出错...';
    exit;
}

// 创建表
$createTable = "CREATE TABLE sensorlog ("
    . " device_id VARCHAR(255)  NOT NULL"
    . ",sensor_name VARCHAR(255) NOT NULL"
    . ",timestamp VARCHAR(255) NOT NULL"
    . ", value float NOT NULL"
    . ")";
$result = mssql_query($createTable, $connection);
if (!$result) {
    print("Table creation failed with error:<br>");
    print(mssql_get_last_message() . "<br>");
} else {
    print("Table  created.<br>");
}

// 上传文件
if (!is_dir("upload")) {
    mkdir("upload");
}

if (move_uploaded_file($_FILES['file']['tmp_name'], "upload/" . $_FILES["file"]["name"])) {
    echo 'upload successfully<br>';
} else {
    echo 'upload failed<br>';
}


// 文件写入数据库
$array = file("upload/demo.txt");
foreach ($array as $item) {
    $item = iconv("gb2312", "utf-8", $item) . "<br>";

    $deviceId = substr($item, strpos($item, '&') + 1, strpos($item, '@') - strpos($item, '&') - 1);
    $sensorName = substr($item, strpos($item, '@') + 1, strpos($item, '#') - strpos($item, '@') - 1);
    $timeStamp = substr($item, strpos($item, '#') + 1, strpos($item, '$') - strpos($item, '#') - 1);
    $value = floatval(substr($item, (strpos($item, '$') + 1)));

    // 插入数据
    $insert = "INSERT INTO sensorlog  (device_id, sensor_name, timestamp, value) VALUES   ('$deviceId','$sensorName','$timeStamp','$value')";
    $result = mssql_query($insert, $connection);
    if ($result) {
        print(" insert successfully.<br>");
    } else {
        print("SQL statement failed with error:<br>");
        print(mssql_get_last_message() . "<br>");
    }
}




/*
// 删除数据
$delete = "DELETE FROM sensorlog";
$result = mssql_query($delete, $connection);
if ($result) {
    print("delete successfully.<br>");
} else {
    print("SQL statement failed with error:<br>");
    print(mssql_get_last_message() . "<br>");
}
*/



/*
// 删除表
$delete = "DROP TABLE sensorlog";
$res = mssql_query($delete, $connection);
if (!$res) {
    print("Table creation failed with error:<br>");
    print(mssql_get_last_message() . "<br>");
} else {
    print("Table  delete.<br>");
}
*/



