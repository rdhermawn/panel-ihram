<?php
// admin_panel.php

// --- Configuration and Environment Simulation ---
// You must configure these variables according to your environment.

// The URL of your API script that contains all the switch-case logic.
// Example: 'http://localhost/your-directory/api_endpoint.php'
$API_ENDPOINT_URL = 'http://72.61.213.173/fonts/pwmonitor_api/index.php'; 

// The security token configured in your API script.
$GLOBAL_API_TOKEN = 'tester'; 

// Variable to store the API result for display
$api_response = null;

// --- Function to Make the API Request ---
function callApiEndpoint($action, $data, $method = 'GET') {
    global $API_ENDPOINT_URL, $GLOBAL_API_TOKEN;

    // Adds the token and the action (function or action) to the data
    $data['token'] = $GLOBAL_API_TOKEN;
    if ($method === 'GET') {
        $data['function'] = $action; // Your script uses 'function' for GET
    } else { // POST
        $data['action'] = $action; // Your script uses 'action' for POST
    }

    $url = $API_ENDPOINT_URL;
    $options = [
        'http' => [
            'method'  => $method,
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data),
        ],
    ];

    // If the method is GET, append the data to the URL
    if ($method === 'GET') {
        $url .= '?' . http_build_query($data);
        unset($options['http']['content']);
    }

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return ['status' => 'error', 'error' => 'Failed to connect or API server error.'];
    }

    // Attempt to decode the JSON response
    $json_response = json_decode($result, true);
    if ($json_response === null && json_last_error() !== JSON_ERROR_NONE) {
        return ['status' => 'error', 'error' => 'API response is not valid JSON.', 'raw_response' => $result];
    }
    
    return $json_response;
}


// --- Form Processing ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_action'])) {
    $action = $_POST['api_action'];
    $data = $_POST;
    unset($data['api_action']); // Remove the control field
    
    // Define the method (most are GET in your original script, 
    // but sendmail is POST and some others are mixed)
    $method = 'GET';
    if (in_array($action, ['sendmail', 'banrole'])) {
        $method = 'POST';
    }

    // Make the call and store the result
    $api_response = callApiEndpoint($action, $data, $method);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1, h2 { color: #333; border-bottom: 2px solid #ccc; padding-bottom: 5px; }
        .tab-content { border: 1px solid #ddd; padding: 15px; margin-top: 10px; border-radius: 4px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #000000; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .response { margin-top: 20px; padding: 10px; border: 1px solid #007bff; background-color: #e9f5ff; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; }
        .error { border-color: #dc3545; background-color: #f8d7da; color: #721c24; }
        .tabs { overflow: hidden; border: 1px solid #ccc; border-bottom: none; border-radius: 5px 5px 0 0; }
        .tab-button { background-color: #000000; float: left; border: none; outline: none; cursor: pointer; padding: 14px 16px; transition: 0.3s; font-size: 17px; }
        .tab-button:hover { background-color: #ddd; }
        .tab-button.active { background-color: #ccc; }
    </style>
</head>
<body>

<div class="container">
    <h1>API Management Panel</h1>
    
    <div class="tabs">
        <button class="tab-button active" onclick="openTab(event, 'General')">General</button>
        <button class="tab-button" onclick="openTab(event, 'Chat')">Chat</button>
        <button class="tab-button" onclick="openTab(event, 'Admin')">Admin</button>
        <button class="tab-button" onclick="openTab(event, 'Mail')">Mail</button>
    </div>

    <div id="General" class="tab-content">
        <h2>Player Online</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="playeron">
            <button type="submit">List Online Players</button>
        </form>
        <hr>

        <h2>User Roles</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="userroles">
            <div class="form-group">
                <label for="userroles_id">User ID (id):</label>
                <input type="number" id="userroles_id" name="id" value="1024" required>
            </div>
            <button type="submit">Get Roles</button>
        </form>
        <hr>

        <h2>Name by Role ID</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="getnamebyid">
            <div class="form-group">
                <label for="getnamebyid_roleid">Role ID (roleid):</label>
                <input type="number" id="getnamebyid_roleid" name="roleid" value="1024" required>
            </div>
            <button type="submit">Get Name</button>
        </form>
        <hr>

        <h2>Role Information</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="roleinfo">
            <div class="form-group">
                <label for="roleinfo_roleid">Role ID (roleid):</label>
                <input type="number" id="roleinfo_roleid" name="roleid" value="1024" required>
            </div>
            <button type="submit">Get Role Info</button>
        </form>
    </div>

    <div id="Chat" class="tab-content" style="display:none">
        <h2>World Chat (Global)</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="worldchat">
            <div class="form-group">
                <label for="worldchat_roleid">Role ID (roleid - sender):</label>
                <input type="number" id="worldchat_roleid" name="roleid" value="1" required>
            </div>
            <div class="form-group">
                <label for="worldchat_channel">Channel ID (channel - e.g., 9 for global):</label>
                <input type="number" id="worldchat_channel" name="channel" value="9" required>
            </div>
            <div class="form-group">
                <label for="worldchat_msg">Message (msg):</label>
                <textarea id="worldchat_msg" name="msg" required>Test.</textarea>
            </div>
            <button type="submit">Send Global Message</button>
        </form>
        <hr>

        <h2>Whisper (Private Message)</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="whisper">
            <div class="form-group">
                <label for="whisper_roleid">Sender Role ID (roleid):</label>
                <input type="number" id="whisper_roleid" name="roleid" value="1" required>
            </div>
            <div class="form-group">
                <label for="whisper_targetid">Target Role ID (targetid):</label>
                <input type="number" id="whisper_targetid" name="targetid" value="1000" required>
            </div>
            <div class="form-group">
                <label for="whisper_srcname">Sender Name (srcname - optional):</label>
                <input type="text" id="whisper_srcname" name="srcname" value="API Admin">
            </div>
            <div class="form-group">
                <label for="whisper_dstname">Target Name (dstname - optional):</label>
                <input type="text" id="whisper_dstname" name="dstname" value="">
            </div>
            <div class="form-group">
                <label for="whisper_msg">Message (msg):</label>
                <textarea id="whisper_msg" name="msg" required>Private test message.</textarea>
            </div>
            <button type="submit">Send Whisper</button>
        </form>
    </div>

    <div id="Admin" class="tab-content" style="display:none">
        <h2>Ban Role</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="banrole">
            <div class="form-group">
                <label for="banrole_roleid">Role ID (roleid):</label>
                <input type="number" id="banrole_roleid" name="roleid" value="1000" required>
            </div>
            <div class="form-group">
                <label for="banrole_time">Ban Time (time - minutes, e.g., 60):</label>
                <input type="number" id="banrole_time" name="time" value="60" required>
            </div>
            <div class="form-group">
                <label for="banrole_reason">Reason (reason):</label>
                <input type="text" id="banrole_reason" name="reason" value="Reason">
            </div>
            <button type="submit">Execute Ban</button>
        </form>
        <hr>

        <h2>Teleport</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="teleport">
            <div class="form-group">
                <label for="teleport_userid">Role ID:</label>
                <input type="number" id="teleport_userid" name="userid" value="1000" required>
            </div>
            <div class="form-group">
                <label for="teleport_posx">Position X (posx):</label>
                <input type="text" id="teleport_posx" name="posx" value="1270.98" required>
            </div>
            <div class="form-group">
                <label for="teleport_posy">Position Y (posy):</label>
                <input type="text" id="teleport_posy" name="posy" value="219.75" required>
            </div>
            <div class="form-group">
                <label for="teleport_posz">Position Z (posz):</label>
                <input type="text" id="teleport_posz" name="posz" value="1034.33" required>
            </div>
            <div class="form-group">
                <label for="teleport_worldtag">World Tag (worldtag):</label>
                <input type="number" id="teleport_worldtag" name="worldtag" value="1" required>
            </div>
            <button type="submit">Teleport Player</button>
        </form>
    </div>
    
    <div id="Mail" class="tab-content" style="display:none">
        <h2>Send Mail with Item/Currency</h2>
        <form method="POST">
            <input type="hidden" name="api_action" value="sendmail">
            <h3>Recipient and Message</h3>
            <div class="form-group">
                <label for="mail_receiver">Role ID:</label>
                <input type="number" id="mail_receiver" name="receiver" value="1000" required>
            </div>
            <div class="form-group">
                <label for="mail_title">Title (title):</label>
                <input type="text" id="mail_title" name="title" value="Gift from Admin" required>
            </div>
            <div class="form-group">
                <label for="mail_msg">Message (msg):</label>
                <textarea id="mail_msg" name="msg" required>Thank you for playing!</textarea>
            </div>
            <div class="form-group">
                <label for="mail_money">Currency (money):</label>
                <input type="number" id="mail_money" name="money" value="10000000">
            </div>

            <h3>Attached Item (Optional)</h3>
            <div class="form-group">
                <label for="mail_iditem">Item ID (iditem - 0 for no item):</label>
                <input type="number" id="mail_iditem" name="iditem" value="0">
            </div>
            <div class="form-group">
                <label for="mail_count">Count (count):</label>
                <input type="number" id="mail_count" name="count" value="1">
            </div>
            <div class="form-group">
                <label for="mail_octet">octet):</label>
                <input type="text" id="mail_octet" name="octet" value="0" required>
            </div>			
            <div class="form-group">
                <label for="mail_proctype">proctype:</label>
                <input type="number" id="mail_proctype" name="proctype" value="0">
            </div>			
            <div class="form-group">
                <label for="mail_mask">Mask:</label>
                <input type="number" id="mail_mask" name="mask" value="0">
            </div>			
            <button type="submit">Send Mail</button>
        </form>
    </div>

    <?php if ($api_response !== null): ?>
        <h2>API Response for '<?php echo $action; ?>'</h2>
        <div class="response <?php echo (isset($api_response['status']) && ($api_response['status'] == -1 || $api_response['status'] == 'error' || $api_response['status'] == 0)) ? 'error' : ''; ?>">
            <?php 
                // Display the formatted response or an error message
                echo htmlspecialchars(print_r($api_response, true));
            ?>
        </div>
    <?php endif; ?>

</div>

<script>
// Function for tab control
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    
    // Remove the 'active' class from all buttons
    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    
    // Show the current tab and add the 'active' class to the button that opened it
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Open the 'General' tab by default on load
document.addEventListener('DOMContentLoaded', (event) => {
    // Check if a tab should be opened due to a POST request
    <?php 
    if (isset($action)) {
        $tabToOpen = 'General';
        if (in_array($action, ['worldchat', 'whisper'])) {
            $tabToOpen = 'Chat';
        } elseif (in_array($action, ['banrole', 'teleport'])) {
            $tabToOpen = 'Admin';
        } elseif ($action == 'sendmail') {
            $tabToOpen = 'Mail';
        }
        echo "openTab(new Event('load'), '$tabToOpen');";
    } else {
        echo "openTab(new Event('load'), 'General');";
    }
    ?>
});
</script>

</body>
</html>