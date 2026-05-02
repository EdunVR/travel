@extends('investor.layouts.auth')

@section('title', 'Login Investor')

@if(auth()->guard('investor')->check())
    <script>
        window.location.href = "{{ route('investor.dashboard') }}";
    </script>
@endif

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Dongle:wght@300;400;700&display=swap');
    
    body {
        font-family: 'Dongle', sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    
    .auth-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 20px;
    }
    
    .logo {
        height: 80px;
        margin-bottom: 20px;
    }
    
    .greeting {
        font-size: 2.5rem;
        font-weight: 400;
        color: #333;
        text-align: center;
        margin-bottom: 20px;
        line-height: 1;
    }
    
    .portal-title {
        font-size: 3rem;
        font-weight: 700;
        color: #333;
        text-align: center;
        line-height: 1;
        margin-bottom: 10px;
    }
    
    .login-container {
        width: 100%;
        max-width: 400px;
        background: white;
        border-radius: 44px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 30px;
        margin-bottom: 10px;
    }
    
    .subtitle {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 20px;
        text-align: center;
        line-height: 1;
    }
    
    .input-group {
        position: relative;
        margin-bottom: 15px;
    }
    
    .input-field {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 50px;
        font-size: 1.3rem;
        font-family: 'Dongle', sans-serif;
        transition: all 0.3s;
    }
    
    .input-field:focus {
        border-color: #2E7D32;
        outline: none;
        box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.2);
    }
    
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
    }
    
    .remember-me {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        font-size: 1.3rem;
    }
    
    .remember-checkbox {
        margin-right: 8px;
        accent-color: #2E7D32;
        width: 16px;
        height: 16px;
    }
    
    .forgot-link {
        color: #2E7D32;
        font-size: 1.3rem;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 400;
    }
    
    .login-btn-container {
        width: 50%;
        max-width: 200px;
        position: relative;
        margin: 0 auto;
    }
    
    .login-btn {
        width: 100%;
        padding: 8px;
        background-color: #2E7D32;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1.8rem;
        font-family: 'Dongle', sans-serif;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
        position: relative;
        z-index: 2;
    }
    
    .login-btn:hover {
        background-color: #1B5E20;
        transform: translateY(-2px);
    }
    
    .register-text {
        color: #666;
        font-size: 1.3rem;
        text-align: center;
        margin-top: 20px;
        line-height: 1;
    }
    
    .register-link {
        color: #2E7D32;
        font-weight: 700;
        text-decoration: none;
    }
    
    @media (max-width: 480px) {
        .auth-wrapper {
            padding: 15px;
        }
        
        .login-container {
            padding: 25px 20px;
        }
        
        .greeting {
            font-size: 2.2rem;
        }
        
        .portal-title {
            font-size: 2.5rem;
        }
    }
</style>

<div class="auth-wrapper">
    <img class="logo" src="{{ asset('img/logo_2.png') }}" alt="Logo Investor">
    <h1 class="greeting">Assalamualaikum!</h1>
    
    <div class="login-container">
        <div class="portal-title">Portal Investor</div>
        <p class="subtitle">Gunakan email dan nomor telepon</p>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700" style="font-family: 'Dongle', sans-serif; font-size: 1.3rem;">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('investor.login') }}" method="POST" id="login-form">
            @csrf
            <input type="hidden" name="remember" value="true">
            
            <div class="input-group">
                <input id="email" name="email" type="email" autocomplete="email" required
                    class="input-field"
                    placeholder="Email@contoh.com"
                    value="{{ old('email') }}">
            </div>
            
            <div class="input-group">
                <input id="phone" name="phone" type="password" autocomplete="tel" required
                    class="input-field"
                    placeholder="Nomor telepon"
                    value="{{ old('phone') }}">
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" fill="#666666"/>
                        <path d="M0 10C0 10 4 3 10 3C16 3 20 10 20 10C20 10 16 17 10 17C4 17 0 10 0 10ZM10 14.5C11.923 14.5 13.6307 13.6307 14.8033 12.3033C15.5359 11.5 15.5359 8.5 14.8033 7.6967C13.6307 6.36929 11.923 5.5 10 5.5C8.077 5.5 6.36929 6.36929 5.1967 7.6967C4.46408 8.5 4.46408 11.5 5.1967 12.3033C6.36929 13.6307 8.077 14.5 10 14.5Z" fill="#666666"/>
                    </svg>
                </span>
            </div>
                
            <div class="remember-me">
                <input id="remember-me" name="remember-me" type="checkbox" class="remember-checkbox">
                <label for="remember-me">Ingat saya</label>
            </div>
            
            <a href="{{ route('investor.password.request') }}" class="forgot-link">Lupa nomor telepon?</a>
            
            <div class="login-btn-container">
                <button type="submit" class="login-btn">Masuk</button>
            </div>
        </form>
    </div>

    <p class="register-text">
        Belum memiliki akun? <a href="{{ route('investor.register') }}" class="register-link">Daftar sebagai investor</a>
    </p>
</div>

<script>
    function togglePasswordVisibility() {
        const phoneInput = document.getElementById('phone');
        const toggleIcon = document.querySelector('.toggle-password svg');
        
        if (phoneInput.type === 'password') {
            phoneInput.type = 'text';
            toggleIcon.innerHTML = `
                <path d="M0 10C0 10 4 3 10 3C16 3 20 10 20 10C20 10 16 17 10 17C4 17 0 10 0 10ZM10 14.5C11.923 14.5 13.6307 13.6307 14.8033 12.3033C15.5359 11.5 15.5359 8.5 14.8033 7.6967C13.6307 6.36929 11.923 5.5 10 5.5C8.077 5.5 6.36929 6.36929 5.1967 7.6967C4.46408 8.5 4.46408 11.5 5.1967 12.3033C6.36929 13.6307 8.077 14.5 10 14.5Z" fill="#666666"/>
                <path d="M2.5 2.5L17.5 17.5" stroke="#666666" stroke-width="2" stroke-linecap="round"/>
            `;
        } else {
            phoneInput.type = 'password';
            toggleIcon.innerHTML = `
                <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" fill="#666666"/>
                <path d="M0 10C0 10 4 3 10 3C16 3 20 10 20 10C20 10 16 17 10 17C4 17 0 10 0 10ZM10 14.5C11.923 14.5 13.6307 13.6307 14.8033 12.3033C15.5359 11.5 15.5359 8.5 14.8033 7.6967C13.6307 6.36929 11.923 5.5 10 5.5C8.077 5.5 6.36929 6.36929 5.1967 7.6967C4.46408 8.5 4.46408 11.5 5.1967 12.3033C6.36929 13.6307 8.077 14.5 10 14.5Z" fill="#666666"/>
            `;
        }
    }
</script>
@endsection
