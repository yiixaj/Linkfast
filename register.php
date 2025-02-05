<?php require_once 'includes/init.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #552FFA;
            --secondary-color: #A084D7;
            --accent-color: #D048BC;
            --highlight-color: #71BDF1;
            --background-color: #F2F2F2;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .register-container {
            max-width: 480px;
            margin: auto;
            padding: 20px;
        }
        
        .register-card {
            background: rgba(242, 242, 242, 0.95);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .brand-logo-image {
            width: 200px; /* Ajusta este valor según necesites */
            height: auto;
            margin: 0 auto 1.5rem;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e1e1e1;
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(85, 47, 250, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
        }
        
        .social-login {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1.5rem 0;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
            transition: all 0.3s ease;
            font-size: 1.2rem;
            text-decoration: none;
        }
        
        .social-btn:hover {
            background: var(--highlight-color);
            color: white;
            border-color: var(--highlight-color);
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--secondary-color);
        }
        
        .divider span {
            padding: 0 1rem;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-top: 0.5rem;
        }

        .password-requirements i {
            font-size: 0.75rem;
            margin-right: 0.25rem;
            color: var(--highlight-color);
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
        }

        a:hover {
            color: var(--accent-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert {
            border-radius: 8px;
            margin-top: 1rem;
        }

        .alert-danger {
            background-color: #ffe5e5;
            border-color: #ffcccc;
            color: #ff3333;
        }

        .alert-success {
            background-color: #e5ffe5;
            border-color: #ccffcc;
            color: #33cc33;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .register-container {
                padding: 10px;
            }
            .brand-logo-image {
                width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container register-container">
        <div class="register-card p-4 p-md-5">
            <!-- Logo con la ruta especificada -->
            <img src="templates/logos.png" alt="Logo" class="brand-logo-image">
            
            <h3 class="text-center mb-4" style="color: var(--primary-color)">Crear una cuenta</h3>
            
            <!-- Botones de redes sociales -->
            <div class="social-login">
                <a href="#" class="social-btn">
                    <i class="bi bi-google"></i>
                </a>
                <a href="#" class="social-btn">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="social-btn">
                    <i class="bi bi-apple"></i>
                </a>
            </div>
            
            <div class="divider">
                <span>O regístrate con tu correo</span>
            </div>
            
            <!-- Formulario de registro -->
            <form action="register_process.php" method="post">
                <div class="mb-3">
                    <label class="form-label" style="color: var(--secondary-color)">Nombre de usuario</label>
                    <input type="text" 
                           class="form-control" 
                           name="username" 
                           placeholder="Elige un nombre de usuario" 
                           required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="color: var(--secondary-color)">Correo electrónico</label>
                    <input type="email" 
                           class="form-control" 
                           name="email" 
                           placeholder="ejemplo@correo.com" 
                           required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label" style="color: var(--secondary-color)">Contraseña</label>
                    <input type="password" 
                           class="form-control" 
                           name="password" 
                           placeholder="••••••••" 
                           required>
                    <div class="password-requirements">
                        <div><i class="bi bi-check-circle-fill"></i> Mínimo 8 caracteres</div>
                        <div><i class="bi bi-check-circle-fill"></i> Al menos una mayúscula y una minúscula</div>
                        <div><i class="bi bi-check-circle-fill"></i> Al menos un número</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="color: var(--secondary-color)">Confirmar contraseña</label>
                    <input type="password" 
                           class="form-control" 
                           name="confirm_password" 
                           placeholder="••••••••" 
                           required>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="terms" 
                               name="terms" 
                               required>
                        <label class="form-check-label" style="color: var(--secondary-color)" for="terms">
                            Acepto los <a href="terms.php">términos y condiciones</a>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
            </form>
            
            <p class="text-center mt-4 mb-0" style="color: var(--secondary-color)">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
            </p>
        </div>

        <!-- Mensajes de error o éxito -->
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center mt-3" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success text-center mt-3" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.querySelector('form').addEventListener('submit', function(event) {
    const password = document.querySelector('input[name="password"]').value;
    const confirm_password = document.querySelector('input[name="confirm_password"]').value;

    // Validar que las contraseñas coincidan
    if (password !== confirm_password) {
        alert("Las contraseñas no coinciden.");
        event.preventDefault();
        return;
    }

    // Validar la longitud de la contraseña
    if (password.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        event.preventDefault();
        return;
    }

    // Validar que la contraseña contenga al menos una mayúscula
    if (!/[A-Z]/.test(password)) {
        alert("La contraseña debe contener al menos una letra mayúscula.");
        event.preventDefault();
        return;
    }

    // Validar que la contraseña contenga al menos una minúscula
    if (!/[a-z]/.test(password)) {
        alert("La contraseña debe contener al menos una letra minúscula.");
        event.preventDefault();
        return;
    }

    // Validar que la contraseña contenga al menos un número
    if (!/[0-9]/.test(password)) {
        alert("La contraseña debe contener al menos un número.");
        event.preventDefault();
        return;
    }
});
</script>
</body>
</html>