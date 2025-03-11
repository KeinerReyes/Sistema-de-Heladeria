<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">

    @section('title', ': Administrador-Kairos')
    <link rel="dns-prefetch" href="//fonts.bunny.net">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">

    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',])
</head>
<body>
<main class="py-8">

<div id="ConteBase">

<div id="ContenedorText">
    <h1 id="TituloText">Bienvenido a KAIROS</h1>
    <p id="TextoConta">"En KAIROS, tu tienda está en manos seguras. Nuestro sistema de administración te ofrece la protección y confianza que necesitas para gestionar tu negocio sin preocupaciones. Con una interfaz intuitiva y herramientas avanzadas, facilitamos la gestión de inventarios, atención al cliente y facturación.
    <br>
    Confía en nosotros para simplificar tus operaciones diarias y mejorar la eficiencia. Nuestro equipo de soporte está siempre disponible para asegurarte una experiencia fluida y sin estrés."</p>
</div>


<div class="container" id="container">
    <br>
	<div class="form-container sign-in-container" id="LoginCompleto">
    <form method="POST" action="{{ route('login') }}">
      @csrf
         <div id="TituloKairo"><h4>KAIRO</h4></div>

         <br>
         <br>
                     <div class="row mb-2 align-items-center" id="ContenedorEmail">
                        <div class="col-md-2 text-md-end">
                            <label for="email" class="col-form-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                            </label>
                        </div>
                        <div class="col-md-10">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                     
                    <br>

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-2 text-md-end">
                            <label for="password" class="col-form-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                                    <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                </svg>
                            </label>
                        </div>
                        <div class="col-md-10">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <br>
                    <div id="contenedorBontonLo">
                     <button id="BotonLogin">Acceder</button>
			        </div>
		</form>
	</div>
</div>
</div>
</main>

</body>
</html>
<style>

@import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 85vh;
    margin: 0;
    padding: 0;
    background-image: url('img/fondoLogin.jpg');
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat;
}

#container {
    height: 400px;
    width: 440px;
    margin-left: 340px;
    background-color: white;
    border-radius: 40px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 20px;
}

#LoginCompleto{

    display: flex;
    justify-content: center; 
    align-items: center; 
}

#TituloKairo{
	 font-size: 48px; 
    font-family: 'Roboto', sans-serif; 
    text-align: center; 
    font-weight: bold; 
    margin: 0; 
    color: #333; 
}

#email{
	width: 100%;
    border: none;
    background-color: #eee;
    padding: 10px; 
    border-radius: 5px; 

}
#password{
	width: 100%;
	border: none;
	background-color: #eee;
}
#iconoEmail{
	border: 4px solid red;
	width: 40px;
}

#ContenedorEmail{
	width: 400px;
}

#BotonLogin {
    background-color: #333333; 
    color: white; 
    font-family: 'Arial', sans-serif; 
    font-size: 16px; 
    padding: 10px 20px; 
    border: none; 
    border-radius: 14px; 
    cursor: pointer; 
    transition: background-color 0.3s ease;
}
#BotonLogin:hover {
    background-color: #4d4d4d; 
}

#contenedorBontonLo{
    margin-left: 40px;
    text-align: center;
}
#ConteBase{
    width: 1560px;
    height: 500px;
    margin-top: 88px;
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    padding: 0 50px; 
}
#ContenedorText{
    width: 700px;
    height: 300px;
    color: white;
    border-radius: 20px;
    padding: 20px;
}

#TituloText{
    font-size: 40px;
    font-weight: bold; 
    color: white; 
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin-bottom: 25px; 
}
#TextoConta{
    font-size: 16px;
    line-height: 1.5; 
    color: white; 
    margin-bottom: 20px; 
    font-family: 'Arial', sans-serif; 
}

</style>