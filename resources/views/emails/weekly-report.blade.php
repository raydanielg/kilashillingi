<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 10px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #059669; }
        .summary-box { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #777; border-top: 1px solid #e1e1e1; padding-top: 20px; }
        .stat-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px dashed #ddd; }
        .income { color: #059669; font-weight: bold; }
        .expense { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #059669;">KilaShillingi - Ripoti ya Wiki</h1>
        </div>
        <div class="content">
            <p>Habari {{ $user->name }},</p>
            <p>Huu ni muhtasari wa miamala yako kwa wiki iliyoishia leo:</p>
            
            <div class="summary-box">
                <div class="stat-row">
                    <span>Jumla ya Mapato:</span>
                    <span class="income">TSh {{ number_format($summary['income'], 0) }}</span>
                </div>
                <div class="stat-row">
                    <span>Jumla ya Matumizi:</span>
                    <span class="expense">TSh {{ number_format($summary['expense'], 0) }}</span>
                </div>
                <div class="stat-row" style="border-bottom: none; margin-top: 10px; font-size: 18px;">
                    <strong>Baki:</strong>
                    <strong>TSh {{ number_format($summary['income'] - $summary['expense'], 0) }}</strong>
                </div>
            </div>

            <p>Endelea kurekodi kila shillingi ili kufanikisha malengo yako ya kifedha.</p>
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/dashboard') }}" style="display: inline-block; padding: 12px 24px; background-color: #059669; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">Tazama Dashibodi Kamili</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} KilaShillingi Platform. Haki zote zimehifadhiwa.</p>
            <p>Simamia Fedha, Imarisha Maisha.</p>
        </div>
    </div>
</body>
</html>
