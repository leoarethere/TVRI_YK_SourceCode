<style>
    body {
        font-family: "Google Sans Flex", sans-serif;
    }
    
    /* Fix CSS Conflicts */
    .login-container {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr;
    }
    
    @media (min-width: 1024px) {
        .login-container {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    .login-banner {
        display: none;
    }
    
    @media (min-width: 1024px) {
        .login-banner {
            display: block;
        }
    }
    
    .login-form-section {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    @media (min-width: 640px) {
        .login-form-section {
            padding: 3rem;
        }
    }
    
    @media (min-width: 1024px) {
        .login-form-section {
            padding: 4rem;
        }
    }
    
    .login-form-container {
        width: 100%;
        max-width: 28rem;
        margin: 0 auto;
    }
    
    .login-logo {
        height: 2.25rem;
        width: auto;
    }
    
    @media (min-width: 768px) {
        .login-logo {
            height: 3rem;
        }
    }
    
    .login-input {
        background-color: rgb(249 250 251);
        border: 1px solid rgb(209 213 219);
        color: rgb(17 24 39);
        font-size: 0.875rem;
        border-radius: 0.5rem;
        padding: 0.625rem;
        width: 100%;
    }
    
    .login-input:focus {
        outline: 2px solid rgb(37 99 235);
        outline-offset: 2px;
        border-color: rgb(37 99 235);
    }
    
    .dark .login-input {
        background-color: rgb(55 65 81);
        border-color: rgb(75 85 99);
        color: rgb(255 255 255);
    }
    
    .dark .login-input:focus {
        outline: 2px solid rgb(59 130 246);
        outline-offset: 2px;
        border-color: rgb(59 130 246);
    }
    
    .login-input-error {
        border-color: rgb(239 68 68);
    }
    
    .login-button {
        width: 100%;
        color: rgb(255 255 255);
        background-color: rgb(37 99 235);
        font-weight: 500;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        padding: 0.625rem 1.25rem;
        text-align: center;
    }
    
    .login-button:hover {
        background-color: rgb(29 78 216);
    }
    
    .dark .login-button {
        background-color: rgb(37 99 235);
    }
    
    .dark .login-button:hover {
        background-color: rgb(29 78 216);
    }
    
    .alert-container {
        padding: 1rem;
        font-size: 0.875rem;
        border-radius: 0.5rem;
        border: 1px solid;
    }
    
    .alert-error {
        color: rgb(153 27 27);
        background-color: rgb(254 242 242);
        border-color: rgb(254 202 202);
    }
    
    .dark .alert-error {
        color: rgb(254 202 202);
        background-color: rgb(127 29 29);
        border-color: rgb(153 27 27);
    }
    
    .alert-success {
        color: rgb(21 128 61);
        background-color: rgb(240 253 244);
        border-color: rgb(187 247 208);
    }
    
    .dark .alert-success {
        color: rgb(187 247 208);
        background-color: rgb(22 101 52);
        border-color: rgb(21 128 61);
    }
    
    .alert-info {
        color: rgb(30 64 175);
        background-color: rgb(239 246 255);
        border-color: rgb(191 219 254);
    }
    
    .dark .alert-info {
        color: rgb(191 219 254);
        background-color: rgb(30 58 138);
        border-color: rgb(30 64 175);
    }
</style>
