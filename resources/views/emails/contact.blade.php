<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nova Mensagem de Contacto - Corujinha</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ec4899; border-bottom: 2px solid #ec4899; padding-bottom: 10px;">
            Nova Mensagem de Contacto
        </h2>
        
        <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Nome:</strong> {{ $name }}</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            @if($phone)
                <p><strong>Telefone:</strong> {{ $phone }}</p>
            @endif
        </div>
        
        <div style="background-color: #fff; padding: 20px; border-left: 4px solid #ec4899; margin: 20px 0;">
            <p><strong>Mensagem:</strong></p>
            <p style="white-space: pre-wrap;">{{ $contactMessage ?? $message ?? '' }}</p>
        </div>
        
        <p style="color: #666; font-size: 12px; margin-top: 30px;">
            Esta mensagem foi enviada através do formulário de contacto do website Corujinha.
        </p>
    </div>
</body>
</html>
