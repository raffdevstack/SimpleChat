const signup_button = document.getElementById("signup_button");
signup_button.addEventListener("click", collectData);

function raffyCustomConsole(description, data) {
    console.log(" ::::: " + description.toUpperCase() + " ::::: " + data);
}

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

            case "first_name":
                formData.first_name = value;
                break;
            case "last_name":
                formData.last_name = value;
                break;
            case "email":
                formData.email = value;
                break;
            case "password":
                formData.password = value;
                break;
            case "password_confirm":
                formData.password_confirm = value;
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

    data.data_type = type; // the type is sent to the data object
    let data_string = JSON.stringify(data);

    xhr.open("POST","api.php",true);
    xhr.send(data_string);
}

function handle_result(result) {

    raffyCustomConsole("from handle result",result);

    if (result.trim() !== "") {

        let obj_result = JSON.parse(result);

        switch (obj_result.data_type) { // goes through all data types
            case "info":
                alert(obj_result.message);
                window.location = "login.html";
                break;
            case "error":

                raffyCustomConsole("from error block", obj_result.message.first_name);

                displayErrors(obj_result.message);
        }
    }
}

function displayErrors(errors) {

    for (const [key, error] of Object.entries(errors)) {

        const error_element = document.getElementById(`${key}_error`);

        if (error_element) {

            error_element.textContent = error;
            error_element.style.display = "block";

        }
    }
}