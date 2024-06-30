<?php

$host = 'localhost'; 
$dbname = 'aktier'; 
$username = 'root'; 
$password = ''; 

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    error_log("Failed to connect to MySQL: " . $mysqli->connect_error);
    exit();
}

$table_realtime_discords = 'wp_realtime_discords';
$table_discords = 'wp_discords';

$initial_response = file_get_contents('https://discord.com/api/v9/invites/pjqVkTud6F?with_counts=true&with_expiration=true');
$headers = $http_response_header; 
$cookie_headers = array_filter($headers, function($header) {
    return strpos($header, 'Set-Cookie:') === 0;
});
$cookie = '';
foreach ($cookie_headers as $cookie_header) {
    $cookie .= explode(';', $cookie_header)[0] . '; ';
}

$request_counter = 0;

$result = $mysqli->query("SELECT * FROM $table_discords");
if (!$result) {
    error_log("Error in fetching discord invites: " . $mysqli->error);
    exit();
}

$count = 0;

while ($discord = $result->fetch_assoc()) {
    preg_match('/discord\.gg\/(\w+)/', $discord['url'], $matches);
    if (empty($matches)) {
        $id = '';
        $member_count = 0;
        $presence_count = 0;
    } else {
        $id = $matches[1];
        
        $response = makeApiRequest('https://discord.com/api/v9/invites/' . $id . '?with_counts=true&with_expiration=true');
        
        if ($response['status_code'] == 200) {
            $discord_data = json_decode($response['response']);
            $member_count = $discord_data->approximate_member_count;
            $presence_count = $discord_data->approximate_presence_count;
        } elseif ($response['status_code'] == 429) {
            $wait_time = 2 ** $request_counter;
            sleep($wait_time); 
            $request_counter++;
            
            continue; 
        } else {
            $error_message = "HTTP request error: Unexpected response code " . $response['status_code'];
            error_log($error_message);
            $member_count = 0;
            $presence_count = 0;
        }
        sleep(2); // To avoid hitting rate limits
    }
    // echo $discord['img_src'];
    $data = array(
        'discord_id' => $id,
        'name' => $discord['name'],
        'member_count' => $member_count,
        'presence_count' => $presence_count,
        'img_src' => $discord['img_src'],
        'url' => $discord['url'],
        'timestamp' => date('Y-m-d H:i:s')
    );

    if (!empty($id)) {
        $query = "SELECT * FROM $table_realtime_discords WHERE discord_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $existing_row = $stmt->get_result()->fetch_assoc();

        if ($existing_row) {
            $query = "UPDATE $table_realtime_discords SET name = ?, member_count = ?, presence_count = ?, img_src = ?, url = ?, timestamp = ? WHERE discord_id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("siissss", $data['name'], $data['member_count'], $data['presence_count'], $data['img_src'], $data['url'], $data['timestamp'], $id);
            $stmt->execute();
        } else {
            $query = "INSERT INTO $table_realtime_discords (discord_id, name, member_count, presence_count, img_src, url, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssiisss", $data['discord_id'], $data['name'], $data['member_count'], $data['presence_count'], $data['img_src'], $data['url'], $data['timestamp']);
            $stmt->execute();
        }
    }

    $count++;
    echo $count . ' ' . $id . "\n";
}


$mysqli->close();

function makeApiRequest($url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'ignore_errors' => true, 
            'header' => "User-Agent: PHP\r\n", 
        ]
    ]);
    $response = @file_get_contents($url, false, $context);
    
    $status_code = $http_response_header[0] ? substr($http_response_header[0], 9, 3) : null;
    
    return [
        'response' => $response,
        'status_code' => $status_code
    ];
}
?>
