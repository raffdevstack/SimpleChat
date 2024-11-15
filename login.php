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
        <div id="form_wrapper">
            <h2>Sign Up</h2>
            <p id="error" style="color: red; display: none"></p>
            <form id="signupForm">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" >

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" >

                <input type="submit" id="signup_button" value="Signup" />
            </form>
            <p id="message"></p>
        </div>
    </div>
    <script>
        const signup_button = document.getElementById("signup_button");
        signup_button.addEventListener("click", collectData)

        function collectData() {
            signup_button.disabled = true;
            signup_button.value = "Loading... Please wait...";
            const formData = {};  // Create an object to store input data
            const inputs = document.querySelectorAll("#signupForm input");  // Select all input elements
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
            sendDataToServer(formData, 'signup');
        }

        // Function to send data to the server using AJAX (XMLHttpRequest)
        function sendDataToServer(data, type) {

            const xhr = new XMLHttpRequest();

            xhr.onload = function () {
                if (xhr.status === 200 || xhr.readyState === 4) {
                    handle_result(xhr.responseText);
                    signup_button.disabled = false;
                    signup_button.value = "Signup";
                }
            };

            data.type = type; // the type is sent to the data object
            let data_string = JSON.stringify(data);

            xhr.open("POST","api.php",true);
            xhr.send(data_string);
        }

        function handle_result(result) {
            let data = JSON.parse(result);
            if(data.data_type === "info") {
                window.location = "index.php";
            } else {
                const error_element = document.getElementById("error");
                error_element.innerHTML = data.message;
                error_element.style.display = "block";
            }
        }
    </script>
</body>
</html>
