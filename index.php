<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Chat</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="wrapper">
        <!-- Left Sidebar -->
        <div id="left_wrapper">
            <!-- Profile Section -->
            <div id="profile_wrapper">
                <img id="profile_image" src="images/profile.png" alt="Profile Image">
                <p class="profile_name">Juan Dela Cruz</p>
                <p class="profile_email">email@example.com</p>
            </div>
            <!-- Navigation Buttons -->
            <div id="buttons_wrapper">
                <label id="label_chat" for="radio_chat">Chat</label>
                <label id="label_contacts" for="radio_contacts">Contacts</label>
                <label id="label_settings" for="radio_settings">Settings</label>
            </div>
        </div>
        <!-- Right Content Area -->
        <div id="right_wrapper">
            <!-- Header Section -->
            <div id="header">
                <h4 id="text_logo">Simple Chat</h4>
            </div>
            <!-- Chat Container -->
            <div id="container">
                <div id="inner_left_wrapper">

                </div>

                <input type="radio" id="radio_chat" name="my_radio" style="display: none">
                <input type="radio" id="radio_contacts" name="my_radio" style="display: none">
                <input type="radio" id="radio_settings" name="my_radio" style="display: none">

                <div id="inner_right_wrapper">
                    <!-- Placeholder for Chat Messages -->
                </div>
            </div>
        </div>

    </div>
    <script>
        document.getElementById("label_settings").addEventListener("click", function() {
            // Create an XMLHttpRequest object
            const xhr = new XMLHttpRequest();

            // Define what happens when the request is successful
            xhr.onload = function() {
                if (xhr.status === 200 || xhr.readyState === 4) {
                    // Display the response text in the result div
                    document.getElementById("text_logo").innerHTML = xhr.responseText;
                }
            };

            // Handle errors
            xhr.onerror = function() {
                console.error("Request failed.");
            };

            // Initialize the request
            xhr.open("POST", "data.php", true);
            // Send the request
            xhr.send();
        });

    </script>
</body>
</html>
