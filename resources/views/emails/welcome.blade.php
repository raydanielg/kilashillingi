<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 10px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #059669; }
        .content { padding: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #777; border-top: 1px solid #e1e1e1; padding-top: 20px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #059669; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #059669;">KilaShillingi</h1>
        </div>
        <div class="content">
            <h2>Karibu sana, {{ $user->name }}!</h2>
            <p>Shukrani kwa kujiunga na <strong>KilaShillingi</strong>, mfumo wako bora wa usimamizi wa fedha nchini Tanzania.</p>
            <p>Tumejipanga kukusaidia kudhibiti mapato yako, kufuatilia matumizi, na kufikia malengo yako ya kifedha kwa urahisi zaidi.</p>
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/dashboard') }}" class="button">Ingia kwenye Dashibodi yako</a>
            </p>
            <p>Ikiwa una maswali yoyote, usisite kuwasiliana nasi kupitia sehemu ya msaada ndani ya mfumo.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} KilaShillingi Platform. Haki zote zimehifadhiwa.</p>
            <p>Simamia Fedha, Imarisha Maisha.</p>
        </div>
    </div>
</body>
</html>
