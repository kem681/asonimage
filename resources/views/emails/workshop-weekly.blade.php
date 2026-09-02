<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>3x30 : ta semaine</title></head>
<body style="font-family: Georgia, serif; color: #2A2A25; background: #FAF8F3; padding: 24px;">
<div style="max-width: 560px; margin: 0 auto; background: #FFFDF7; border: 1px solid #E8E0D0; padding: 28px;">
    <p style="font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; color: #A89070; margin: 0 0 8px;">3x30 · À Son Image</p>
    <h1 style="font-weight: normal; font-size: 26px; margin: 0 0 16px;">Bonjour {{ $user->firstName() }}</h1>
    <p style="margin: 0 0 20px; color: #4A4A42;">Une semaine, un geste. Voici ce qui t'attend.</p>

    @foreach($reminders as $reminder)
        <div style="border-left: 3px solid #C4A35A; background: #F5F0E8; padding: 12px 16px; margin-bottom: 10px;">
            <div style="font-weight: bold;">{{ $reminder['title'] }}</div>
            <div style="color: #4A4A42; font-size: 14px;">{{ $reminder['body'] }}</div>
            <div style="margin-top: 6px;"><a href="{{ route($reminder['route'], $reminder['params']) }}" style="color: #3A4A3A;">Ouvrir</a></div>
        </div>
    @endforeach

    <p style="margin: 24px 0 0; font-size: 13px; color: #4A4A42;">Un jour raté n'est pas une rupture. On reprend.</p>
    <p style="margin: 16px 0 0; font-size: 12px; color: #A89070;"><a href="{{ route('workshop.index') }}" style="color: #8B7355;">Ouvrir 3x30</a></p>
</div>
</body>
</html>
