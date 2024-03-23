<!DOCTYPE html>
<html>
<head>
    <title>Pagina di Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        .login-form {
            width: 340px;
            margin: 50px auto;
        }
        .login-form form {
            margin-bottom: 15px;
            background: #f7f7f7;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .login-form h2 {
            margin: 0 0 15px;
        }
        .form-control, .btn {
            min-height: 38px;
            border-radius: 2px;
        }
        .btn {
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <br>
	<br>
	<div align="center"> Hai inserito le credenziali sbagliate. Per favore reinseriscile. </div>
    <hr>
    <div class="login-form">
                <form action="login.php" method="POST">
            <h2 class="text-center">LOGIN Area Personale</h2>
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Nome utente" required="required">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required="required">
            </div>
            <div class="form-group">
                <select type="text" class="form-control" name="ruolo" placeholder="Ruolo" required="required">
                    <option value="segreteria">Segreteria</option>
                    <option value="docente">Docente</option>
                    <option value="studente">Studente</option><select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Accedi</button>
            </div>
        </form>
    </div>
	<div align="center"> Hai dimenticato l'username o la Password? <a href="mailto:simonealessandro.casciaro@studenti.unimi.it">Clicca qui per ricevere assistenza.</a></div>
</body>
</html>