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
                <p id="username" class="profile_name">Username</p>
                <p id="email" class="profile_email">email@example.com</p>
            </div>
            <!-- Navigation Buttons -->
            <div id="buttons_wrapper">
                <label id="label_chats" for="radio_chat">Chat</label>
                <label id="label_contacts" for="radio_contacts">Contacts</label>
                <label id="label_settings" for="radio_settings">Settings</label>
            </div>
            <input type="button" id="logout" value="Logout">
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

<!--            these are hidden so it's okay    -->
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

        const logout_el = document.getElementById("logout");
        logout_el.addEventListener("click", logout_user);

        const label_contacts_el = document.getElementById("label_contacts");
        label_contacts_el.addEventListener("click", get_contacts);
        const label_chats_el = document.getElementById("label_chats");
        label_chats_el.addEventListener("click", get_chats);
        const label_settings_el = document.getElementById("label_settings");
        label_settings_el.addEventListener("click", get_settings);


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
            // alert(result);
            if (result.trim() !== "") { // if result is not empty
                let obj_result = JSON.parse(result); // converting to object the text json
                if (typeof(obj_result.logged_in) != "undefined" && !obj_result.logged_in ) { // if not logged in
                    window.location = "login.php";
                } else {
                    switch (obj_result.data_type) { // goes through all data types
                        case "user_info":
                            document.getElementById("username").innerHTML = obj_result.username;
                            break;
                        case "chats":
                            document.getElementById("inner_left_wrapper").innerHTML = obj_result.message;
                            break;
                        case "contacts":
                            document.getElementById("inner_left_wrapper").innerHTML = obj_result.message;
                            break;
                        case "settings":
                            document.getElementById("inner_left_wrapper").innerHTML = obj_result.message;
                            break;
                        case "save_settings":
                            alert(obj_result.message)
                            get_data({},"user_info");
                            get_settings(true);
                            break;
                        case "error":
                            alert(obj_result.message)
                            get_settings(true);
                            break;
                    }
                }
            }
        }

        function logout_user() {
            var answer = confirm("Are you sure you want to logout?")
            if (answer)
                get_data({},"logout");
        }

        function get_contacts(e) {
            get_data({},"contacts");
        }

        function get_chats(e) {
            get_data({},"chats");
        }

        function get_settings(e) {
            get_data({},"settings");
        }

        function collectUpdatedSettings() {
            const save_settings_button_el = document.getElementById("save_settings_button");
            save_settings_button_el.disabled = true;
            save_settings_button_el.value = "Loading... Please wait...";
            const formData = {};  // Create an object to store input data
            const inputs = document.querySelectorAll("#settings_form input");  // Select all input elements
            // Iterate over each input element in the form
            for (let i = 0; i < inputs.length; i++) {
                const input = inputs[i];
                const name = input.name;
                const value = input.value;

                // Use a switch case to handle each input based on its name
                switch (name) {
                    case "username":
                        formData.username = value;
                        break;
                    case "password":
                        formData.password = value;
                        break;
                    default:
                        console.log(`Unknown input: ${name}`);
                }
            }
            sendSettingsUpdateToServer(formData, 'save_settings');
        }

        // Function to send data to the server using AJAX (XMLHttpRequest)
        function sendSettingsUpdateToServer(data, type) {
            const xhr = new XMLHttpRequest();

            xhr.onload = function () {
                if (xhr.status === 200 || xhr.readyState === 4) {
                    handle_result(xhr.responseText);
                    const save_settings_button_el = document.getElementById("save_settings_button");
                    save_settings_button_el.disabled = false;
                    save_settings_button_el.value = "Save Settings";
                }
            };

            data.data_type = type; // the type is sent to the data object
            let data_string = JSON.stringify(data);

            xhr.open("POST","api.php",true);
            xhr.send(data_string);
        }

        // data getter from the server
        get_data({},"user_info"); // calling the function above, empty object because we are not finding anything

        function startChat(e) {
            alert('start chat');
        }
    </script>
</body>
</html>
