<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #333;">
    <h2>Bonjour {{ $artisan->user->name }},</h2>
    <p>Nous vous remercions pour votre candidature en tant qu'artisan.</p>
    <p>Après étude de votre dossier, nous ne sommes malheureusement pas en mesure de le valider pour la raison suivante :</p>
    <p style="background: #f8f8f8; padding: 10px; border-left: 3px solid #e53e3e;">{{ $artisan->refusal_reason }}</p>
    <p>N'hésitez pas à nous contacter pour plus d'informations.</p>
</body>
</html>