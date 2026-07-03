<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #333;">
    <h2>Félicitations {{ $artisan->user->name }} !</h2>
    <p>Votre candidature en tant qu'artisan ({{ $artisan->profession }}) a été validée par notre équipe.</p>
    <p>Vous pouvez dès maintenant vous connecter à votre tableau de bord :</p>
    <p><a href="{{ url('/login') }}">Se connecter</a></p>
    <p>À très bientôt sur Artilo !</p>
</body>
</html>