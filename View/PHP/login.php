<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | IMPERIAL HOMES CORPORATION</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/century-gothic" rel="stylesheet">

<style>
:root{
    --orange:#f57c1f;
    --orange-hover:#e06a10;
    --ink:#1a1a1a;
    --muted:#6b6b6b;
    --border:#f3c397;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html, body{
    height:100%;
}

body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
    overflow-y:auto;
    background:
        radial-gradient(900px 440px at 50% 0%, rgba(245,124,31,.16) 0%, transparent 62%),
        radial-gradient(620px 360px at 100% 100%, rgba(245,124,31,.10) 0%, transparent 65%),
        linear-gradient(180deg, #fffaf5 0%, #ffffff 48%, #fff7ef 100%);
}

.login-card{
    width:100%;
    max-width:520px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:24px;
    padding:32px 36px;
    box-shadow:
        0 24px 54px rgba(98,54,16,.12),
        0 2px 9px rgba(245,124,31,.08);
    animation: fadeIn .25s ease-in-out;
}

.brand{
    display:flex;
    justify-content:center;
    margin-bottom:18px;
}

.brand-logo{
    width:82px;
    height:82px;
    object-fit:contain;
    padding:10px;
    background:#fffaf5;
    border:1px solid var(--border);
    border-radius:18px;
}

.login-head{
    text-align:center;
    margin-bottom:22px;
}

.login-head h1{
    font-size:20px;
    font-weight:900;
    font-family:'Century Gothic', sans-serif;
    display:inline-block;
    background:linear-gradient(90deg, #ff6f00 0%, #ff9800 45%, #ff6f00 100%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
    -webkit-text-fill-color:transparent;
    -webkit-text-stroke:0.8px;
    text-shadow:
        0 1px 0 rgba(255,255,255,.4),
        0 2px 6px rgba(255,165,0,.35);
}

.login-head p{
    color:var(--muted);
    font-size:14px;
    margin-top:12px;
}

.alert{
    background:#fff1f0;
    border:1px solid #ffd6d3;
    color:#c0392b;
    padding:12px;
    border-radius:10px;
    text-align:center;
    margin-bottom:18px;
    font-size:14px;
}

.login-form{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.field{
    display:flex;
    flex-direction:column;
}

.field span{
    margin-bottom:8px;
    font-size:14px;
    font-weight:600;
}

.field input{
    width:100%;
    height:50px;
    padding:0 15px;
    border:1px solid var(--border);
    border-radius:12px;
    font-size:14px;
    transition:.2s;
}

.field input:focus{
    outline:none;
    border-color:var(--orange);
    box-shadow:0 0 0 3px rgba(245,124,31,.15);
}

.password-wrapper{
    position:relative;
}

.password-wrapper input{
    padding-right:50px;
}

.toggle-password{
    position:absolute;
    right:16px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#888;
}

.row{
    display:flex;
    align-items:center;
}

.remember{
    display:flex;
    align-items:center;
    gap:8px;
    color:var(--muted);
    font-size:14px;
}

.btn-primary{
    margin-top:6px;
    height:50px;
    border:none;
    border-radius:12px;
    background:var(--orange);
    color:#fff;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}

.btn-primary:hover{
    background:var(--orange-hover);
}

.footer{
    text-align:center;
    margin-top:18px;
    color:var(--muted);
    font-size:12px;
}

@keyframes fadeIn{
    from{ opacity:0; transform:translateY(10px); }
    to{ opacity:1; transform:translateY(0); }
}

@media(max-width:480px){
    .login-card{
        padding:24px;
        border-radius:18px;
    }
    .brand-logo{
        width:72px;
        height:72px;
    }
    .login-head h1{
        font-size:22px;
    }
}

@media(max-height:700px){
    .login-card{
        padding:22px 24px;
    }
    .field input,
    .btn-primary{
        height:46px;
    }
}
</style>
</head>

<body>

<div class="login-card">

    <div class="brand">
        <img
            src="../assets/img/logo/imperialhouse_logo.png?v=20260618"
            alt="Imperial Homes"
            class="brand-logo">
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert">
            Invalid username or password.
        </div>
    <?php endif; ?>

    <div class="login-head">
        <h1>IMPERIAL HOMES CORPORATION</h1>
        <p>Sign in to your account</p>
    </div>

    <form action="login_process.php" method="POST" class="login-form">

        <label class="field">
            <span>Username</span>
            <input type="text" name="username" placeholder="Enter your username" required>
        </label>

        <label class="field">
            <span>Password</span>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>
        </label>

        <div class="row">
            <label class="remember">
                <input type="checkbox" name="remember">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn-primary">
            Login
        </button>

    </form>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> Imperial Homes Corporation
    </div>

</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const password = document.getElementById('password');

togglePassword.addEventListener('click', () => {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    togglePassword.classList.toggle('fa-eye');
    togglePassword.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>