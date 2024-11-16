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
                <p class="profile_name">Username</p>
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

        function get_data(find, type) { // something we are searching (an object), data type (string)

            let xhr = new XMLHttpRequest();

            xhr.onload = function () {
                if (xhr.status === 200 || xhr.readyState === 4) {
                    handle_result(xhr.responseText, type);
                }
            }

            let data = {} // empty object
            data.find = find;
            data.data_type = type;
            data = JSON.stringify(data);
            xhr.open("POST","api.php",true);
            xhr.send(data);
        }

        // called inside the get_data()
        function handle_result(result, type) {

            if (result.trim() !== "") { // if result is not empty
                let obj = JSON.parse(result); // converting to object the text json
                if (typeof(obj.logged_in) != "undefined" && !obj.logged_in ) { // if not logged in
                    window.location = "login.php";
                } else {

                }
            }
        }

        // data getter from the server
        get_data({},"user_info"); // calling the function above, empty object because we are not finding anything

    </script>
</body>
</html>
