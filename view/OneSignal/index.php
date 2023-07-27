<?php
include 'conn.php';

if (isset($_POST['send_notification'])) {
    // Device Token
    $audience = $_POST['audience'];
    $deviceTokens = array();

    if ($audience === 'all') {
        $data = mysqli_query($connection, "SELECT device_token FROM users");
        if ($data) {
            while ($row = mysqli_fetch_array($data)) {
                $deviceToken = $row['device_token'];

                if (!empty($deviceToken)) {
                    $deviceTokens[] = $deviceToken;
                    // var_dump($deviceTokens);
                }
            }
        }
    } else {
        $deviceTokens[] = $audience;
        // var_dump($deviceTokens);
    }

    $title = $_POST['title'];
    $message = $_POST['message'];
    $sendAfter = $_POST['send_after'];
    $validDeviceTokens = array_filter($deviceTokens);

    $response = sendMessage($validDeviceTokens, $title, $message, $sendAfter);
    $sendTime = date("Y-m-d H:i:s", $sendAfter);
    // var_dump($sendTime);
    $array = json_decode($response, true);


    if (isset($array['id'])) {
        $notificationID = $array['id'];
        $data = mysqli_query($connection, "INSERT INTO notifications (notification_id, title, message, time) VALUES ('$notificationID', '$title', '$message', '$sendTime')");
        if ($data) {
            echo "Notifikasi berhasil terkirim";
        } else {
            echo "Gagal mengirim notifikasi";
        }
    }
}

function sendMessage($deviceTokens, $title, $message, $send_after)
{
    $app_id = '3009747d-883a-4af7-8d15-511a20388b7a';
    $content = array(
        "en" => $message
    );

    $fields = array(
        'app_id' => $app_id,
        'include_player_ids' => $deviceTokens,
        'data' => array("foo" => "bar"),
        'headings' => array("en" => $title),
        'contents' => $content,
        'send_after' => $send_after
    );

    return pushMessage($fields);
}

function pushMessage($fields)
{
    $rest_api_key = 'MzZjMDNmNGQtYWRkNy00NWRjLTkyM2MtYTU4ZjE2YzVhZTZm';

    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $rest_api_key
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OneSignal Notification</title>
</head>

<body>
    <h1>Send Notification</h1>
    <form method="POST" action="../../config/send_notif.php" enctype="multipart/form-data">
        <label for="user">Select User:</label><br>
        <select name="audience" id="user">
            <option value="all">All User</option>
            <!-- Tambahkan query dari database jika ingin mengirim berdasarkan user tertentu -->
            <!-- $data = mysqli_query($connection, "SELECT * FROM users where device_token != ''");
            while ($row = mysqli_fetch_array($data)) {
                echo "<option value='" . $row['device_token'] . "'>" . $row['username'] . "</option>";
            } -->
        </select>


        <br><br>
        <label for="title">Title :</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="message">Message :</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <br><input type="submit" value="Kirim Notifikasi" name="send_notification">
    </form>
</body>

</html>