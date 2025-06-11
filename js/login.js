document.addEventListener("DOMContentLoaded", function () {
    // Signup form handling...
    const form = document.querySelector("form");

    if (form && form.id === "signupForm") {
        const fullnameInput = form.querySelector("input[name='fullname']");
        const emailInput = form.querySelector("input[name='email']");
        const passwordInput = form.querySelector("input[name='password']");
        const signupBtn = form.querySelector("button[type='submit']");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            signupBtn.disabled = true;
            signupBtn.textContent = "Signing up...";

            fetch("../php/signup.php", {
                method: "POST",
                body: formData,
            })
                .then(res => res.text())
                .then(response => {
                    const trimmedResponse = response.trim().toLowerCase();

                    if (trimmedResponse === "success") {
                        alert("Signup successful! Redirecting to login...");
                        window.location.href = "../html/login.html";
                    } else {
                        alert(trimmedResponse);

                        if (trimmedResponse.includes("already")) {
                            window.location.href = "../html/login.html";
                        }
                    }
                })
                .catch(error => {
                    alert("An error occurred: " + error.message);
                })
                .finally(() => {
                    signupBtn.disabled = false;
                    signupBtn.textContent = "Sign Up";
                });
        });
    }
});


document.getElementById("login-form").addEventListener("submit", function (e) {
    e.preventDefault();
  
    const formData = new FormData(this);
  
    fetch("php/login.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          // Store user name in localStorage
          localStorage.setItem("userName", data.user_name);
  
          // Redirect to homepage
          window.location.href = "index.html";
        } else {
          alert(data.message);
        }
      })
      .catch(err => console.error("Login failed:", err));
  });
  
