const loginForm = document.getElementById('login-form');
const otpForm = document.getElementById('otp-form');

// Show or hide password
const passwordInput = document.getElementById('password');
const togglePasswordButton = document.getElementById('toggle-password');
const passwordEyeIcon = document.getElementById('password-eye-icon');

togglePasswordButton.addEventListener('click', () => {
    const isPasswordHidden = passwordInput.type === 'password';

    passwordInput.type = isPasswordHidden ? 'text' : 'password';

    passwordEyeIcon.classList.toggle('fa-eye', !isPasswordHidden);
    passwordEyeIcon.classList.toggle('fa-eye-slash', isPasswordHidden);

    togglePasswordButton.setAttribute(
        'aria-label',
        isPasswordHidden ? 'Hide password' : 'Show password'
    );

    togglePasswordButton.setAttribute(
        'aria-pressed',
        String(isPasswordHidden)
    );
});

// Login stage
loginForm.onsubmit = async (e) => {
    e.preventDefault();
    clearErrors();
    const emailValue = document.getElementById('email').value.trim();
    const passwordValue = document.getElementById('password').value;
    // validation
    let isValid = true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailValue) {
        showFieldError(
            'email-error',
            'Email is required.'
        );
        isValid = false;
    } else if (!emailRegex.test(emailValue)) {
        showFieldError(
            'email-error',
            'Invalid email address.'
        );
        isValid = false;
    }
    if (!passwordValue) {
        showFieldError(
            'password-error',
            'Password is required.'
        );
        isValid = false;
    }
    if (!isValid) return;
    // show spinner as the user waits for the code
    const loginButton = loginForm.querySelector('button[type="submit"]');
    loginButton.disabled = true;
    loginButton.innerHTML = `
        <i class="fas fa-spinner fa-spin"></i>
        Sending code...
    `;
    try {
        const response = await fetch('../api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: emailValue,
                password: passwordValue
            })
        });
        const data = await response.json();
        if (data.status === "success") {
            const otpResponse = await fetch('../api/send_otp.php', {
                method: 'POST'
            });
            const otpData = await otpResponse.json();
            if (otpData.status === "success") {
                loginForm.classList.add('hidden');
                otpForm.classList.remove('hidden');

                const authMessage = document.getElementById('auth-message');
                authMessage.textContent = 'A verification code was sent to your email.';
                authMessage.classList.remove('hidden');

                showToast(
                    "Verification code sent",
                    "success"
                );
                // re-enable the login button
                loginButton.disabled = false;
                loginButton.innerHTML = 'Login';
            } else {
                showToast(
                    decodeHTML(otpData.message),
                    "error"
                );
                // re-enable the login button
                loginButton.disabled = false;
                loginButton.innerHTML = 'Login';
            }
        } else {
            showToast(
                decodeHTML(data.message),
                "error"
            );
            // re-enable the login button
            loginButton.disabled = false;
            loginButton.innerHTML = 'Login';
        }
    } catch (error) {
        console.error(error);
        showToast(
            "Something went wrong",
            "error"
        );
        // re-enable the login button
        loginButton.disabled = false;
        loginButton.innerHTML = 'Login';
    }
};

// OTP verification stage
otpForm.onsubmit = async (e) => {
    e.preventDefault();
    const otp = document.getElementById('otp').value.trim();
    if (!otp) {
        showToast(
            "Enter verification code",
            "error"
        );
        return;
    }
    // show spinner as the user submits the code
    const otpButton = otpForm.querySelector('button[type="submit"]');
    otpButton.disabled = true;
    otpButton.innerHTML = `
        <i class="fas fa-spinner fa-spin"></i>
        Verifying...
    `;
    try {
        const response = await fetch('../api/verify_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                otp: otp
            })
        });
        const data = await response.json();
        if (data.status === "success") {
            showToast(
                "Login successful",
                "success"
            );
            setTimeout(() => {
                window.location.href = "../index.php";
            }, 1000);
        } else {
            showToast(
                decodeHTML(data.message),
                "error"
            );
            // re-enable the login button
            otpButton.disabled = false;
            otpButton.innerHTML = 'Verify';
        }
    } catch (error) {
        console.error(error);
        showToast(
            "Server connection error",
            "error"
        );
        // re-enable the login button
        otpButton.disabled = false;
        otpButton.innerHTML = 'Verify';
    }
};

function showFieldError(id, message) {
    const element = document.getElementById(id);
    element.textContent = message;
    element.classList.remove('hidden');
}
function clearErrors() {
    document.querySelectorAll('[id$="-error"]')
        .forEach(element => {
            element.textContent = "";
            element.classList.add('hidden');
        });
}

// helper functions for escaping and decoding html
// --- escape html ---

function escapeHTML(str) {
  if (str === null || str === undefined) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

// --- decode html ---
function decodeHTML(str) {
  if (!str) return "";
  const txt = document.createElement("textarea");
  let decoded = str;
  let prev;
  do {
    prev = decoded;
    txt.innerHTML = decoded;
    decoded = txt.value;
  } while (decoded !== prev);
  return decoded;
}