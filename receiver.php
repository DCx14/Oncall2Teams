<?php 

// set UTC FR
setlocale(LC_TIME, 'fr_FR');
date_default_timezone_set('Europe/Paris');

// input request webhook
$entityBody = file_get_contents('php://input');
$headers = getallheaders();


// json encode/decode
$str_json = json_encode($entityBody,JSON_FORCE_OBJECT);
$my_array = json_decode($entityBody,true);

//verify error
if (json_last_error()) {
    $error = "JSON parsing error: " . json_last_error();
    print $error;
    // To-do Add some logging for errors
    return;
}

//get metric in webhook



$severity = $my_array['alert_payload']['alerts'][0]['labels']['severity'];
$alertname = $my_array['alert_payload']['alerts'][0]['labels']['alertname'];
$status = $my_array['alert_payload']['alerts'][0]['status']; 
$Description = $my_array['alert_payload']['alerts'][0]['annotations']['description'];
$UrlAlert = $my_array['alert_group']['permalinks']['web'];
$Annotations = $my_array['alert_payload']['alerts'][0]['annotations'];
$WebhookDest = $headers['Webhook-Url'] ; // Get URL destination


// function for tranform json

function arrayToTextWithNewlines($array) {
    $textArray = [];
    foreach ($array as $key => $value) {
        $textArray[] = "$key => $value";
    }
    return implode("\n", $textArray);
}

// Utilisation de la fonction
$AnnotationsBrut = arrayToTextWithNewlines($Annotations);

$AnnotationsBr = nl2br($AnnotationsBrut);

// condition for change color and images

if ($severity === "critical")
{
    $images = 'https://cdn0.iconfinder.com/data/icons/basic-11/97/9-512.png';
    $color = '#EA0601';

}elseif ($severity === "warning")
{
    $images = 'https://cdn4.iconfinder.com/data/icons/set-1/32/__23-256.png';
    $color = '#E06C10';
}elseif  ($severity === "info")
{
    $images = 'https://cdn0.iconfinder.com/data/icons/small-n-flat/24/678110-sign-info-256.png';
    $color = '#1D6BE0';

}else{

    $images = 'https://cdn1.iconfinder.com/data/icons/web-illustration-1/132/48-256.png';
    $color = '#000000';
}


$data = [
            '@type'      => 'MessageCard',
            '@context'   => 'https://schema.org/extensions',
            'summary'    => 'AlertManager',
            'themeColor' => $color,
            'title'      => 'Alert: ' . $alertname,
            'sections'   => [
                [
                    'activityTitle'    => $status,
                    'activitySubtitle' => date('Y-m-d H:i:s'),
                    'activityImage'    => $images,
                    'facts'            => [
                        [
                            'name'  => 'Description: ',
                            'value' => $Description
                        ],
                        [
                            'name'  => 'severity',
                            'value' => $severity
                        ],
                        [
                            'name'  => 'Annotations',
                            'value' => $AnnotationsBr
                        ],

                    ],
                ]
            ],
            'potentialAction' => [
                [
                    '@type' => 'OpenUri',
                    'name' => 'Accéder a l\'alerte',
                    'targets' => [
                        [
                            'os' => 'default',
                            'uri' => $UrlAlert
                        ],
                 ]
               ]
               
               
             ],
         ];
        
$json_data1 = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

// Send WebHook

$ch = curl_init($WebhookDest);
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data1);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
echo $response;
curl_close( $ch );

?>
