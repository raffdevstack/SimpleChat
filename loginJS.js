const login_button = document.getElementById("login_button");
login_button.addEventListener("click", collectData);

function collectData(event) {
    event.preventDefault();
    login_button.disabled = true;
    login_button.value = "Loading... Please wait...";

    const formData = {};
    const inputs = document.querySelectorAll("#loginForm input");
    
    inputs.forEach(input => {
        const name = input.name;
        const value = input.value;
        if (name) formData[name] = value;
    });

    sendDataToServer(formData, 'login');
}

function sendDataToServer(data, type) {
    const xhr = new XMLHttpRequest();

    xhr.onload = function () {
        if (xhr.status === 200) {
            handle_result(xhr.responseText);
            login_button.disabled = false;
            login_button.value = "Login";
        }
    };

    data.data_type = type;
    const data_string = JSON.stringify(data);

    xhr.open("POST", "api.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(data_string);
}

function handle_result(result) {
    console.log(result);

    if (result.trim() !== "") {
        const obj_result = JSON.parse(result);

        switch (obj_result.data_type) {
            case "info":
                alert(obj_result.message);
                window.location = "index.html";
                break;
            case "error":
                document.getElementById("message").textContent = obj_result.message;
                break;
        }
    }
}