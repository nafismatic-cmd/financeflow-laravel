
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FinanceFlow</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-logo">F</div>

        <h1>FinanceFlow</h1>
        <p>Kelola keuanganmu dengan lebih mudah.</p>

        <form id="loginForm">
            <label>Email</label>
            <input type="email" id="email"
                placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" id="password"
                placeholder="Masukkan password" required>

            <button type="submit" class="login-button">
                Login
            </button>
        </form>

        <p id="loginMessage"></p>
    </div>

    <script>
        const API = "http://127.0.0.1:8000/api";

        document.getElementById('loginForm')
        .addEventListener('submit', async function(e) {
            e.preventDefault();

            const message = document.getElementById('loginMessage');

            try {
                const response = await fetch(`${API}/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Login gagal');
                }

                localStorage.setItem('token', result.token);
                window.location.href = '/dashboard';

            } catch (error) {
                message.textContent = error.message;
                message.style.color = 'red';
            }
        });
    </script>

</body>
</html>
