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
                <label id="label_contacts" for="radio_contacts">Contacts</label>
                <label id="label_chats" for="radio_chat" style="display: none">Chat</label>
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

        let CURRENT_CHAT_USER = null;

        const logout_el = document.getElementById("logout");
        logout_el.addEventListener("click", logout_user);

        const label_contacts_el = document.getElementById("label_contacts");
        label_contacts_el.addEventListener("click", get_contacts);

        const label_chats_el = document.getElementById("label_chats");
        label_chats_el.addEventListener("click", getChats);

        const label_settings_el = document.getElementById("label_settings");
        label_settings_el.addEventListener("click", get_settings);

        const radio_contacts_el = document.getElementById("radio_contacts");

        const inner_right_wrapper_el = document.getElementById("inner_right_wrapper");

        function initializeLanding() {
            // data getter from the server
            getData({},"user_info"); // calling the function above, empty object because we are not finding anything
            getData({},"contacts");
            radio_contacts_el.checked = true;
        }

        function raffyCustomConsole(description, data) {
            console.log(" ::::: " + description.toUpperCase() + " ::::: " + data);
        }

        function getData(find, type) { // something we are searching (an object), data type (string)

            let xhr = new XMLHttpRequest();

            xhr.onload = function () {
                if (xhr.status === 200 || xhr.readyState === 4) {
                    handle_result(xhr.responseText, type);
                }
            }

            let data = {} // empty object
            if (typeof find === 'object' && find !== null) { // Merge the properties of find into data
                Object.assign(data, find);
            } else {
                data.find = find;
            }
            data.data_type = type;

            data = JSON.stringify(data);
            xhr.open("POST","api.php",true);
            xhr.send(data);
        }

        // called inside the getData()
        function handle_result(result, type) {
            raffyCustomConsole("from handle result", result)
            inner_right_wrapper_el.style.overflow = "visible";
            inner_right_wrapper_el.style.opacity = "1";

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
                            document.getElementById("inner_left_wrapper").innerHTML = obj_result.chat_contact;
                            document.getElementById("inner_right_wrapper").innerHTML = obj_result.messages;
                            const wrapper_el = document.getElementById("messages_wrapper");
                            setTimeout(function () {
                                wrapper_el.scrollTo(0,wrapper_el.scrollHeight);
                                const text_input_el = document.getElementById("message_text");
                                text_input_el.focus();
                            }, 500);
                            break;
                        case "contacts":
                            document.getElementById("inner_left_wrapper").innerHTML = obj_result.message;
                            inner_right_wrapper_el.style.overflow = "hidden";
                            inner_right_wrapper_el.style.opacity = "0";
                            break;
                        case "settings":
                            document.getElementById("inner_left_wrapper").innerHTML = obj_result.message;
                            inner_right_wrapper_el.style.overflow = "hidden";
                            inner_right_wrapper_el.style.opacity = "0";
                            break;
                        case "save_settings":
                            alert("Message:" + obj_result.message);
                            getData({},"user_info");
                            get_settings(true);
                            break;
                        case "send_message":
                            getData({userid: obj_result.receiver_id},"chats");
                            break;
                        case "error":
                            alert("Error:" + obj_result.message);
                            initializeLanding();
                            break;
                    }
                }
            }
        }

        function logout_user() {
            var answer = confirm("Are you sure you want to logout?")
            if (answer)
                getData({},"logout");
        }

        function get_contacts(e) {
            getData({},"contacts");
        }

        function getChats(e) {
            getData({},"chats");
        }

        function get_settings(e) {
            getData({},"settings");
        }

        function sendMessage(e) {
            // get data from the input
            let text = document.getElementById("message_text").value.trim();
            if (text === "") {
                alert("Please type something to send");
                return;
            }
            getData({
                text: text,
                receiver_userid: CURRENT_CHAT_USER
            },"send_message");
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
                        console.log(`Unknown input in settings collection: ${name}`);
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

        function startChat(e) {
            let userid = e.target.getAttribute("userid"); // it targets the element where the event happened
            if (e.target.id === "") {
                userid = e.target.parentNode.getAttribute("userid"); // when inner element catches the click
            }
            CURRENT_CHAT_USER = userid;
            let radio_chat_el = document.getElementById("radio_chat");
            radio_chat_el.checked = true;
            getData({userid:CURRENT_CHAT_USER}, 'chats')
        }

        function enter_pressed(e) {
            if (event.keyCode === 13) {
                sendMessage(e);
            }
        }

        initializeLanding();
    </script>
</body>
</html>
