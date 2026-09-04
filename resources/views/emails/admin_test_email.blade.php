<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار اتصال البريد</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl; text-align: right; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 550px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 30px; text-align: center; color: #ffffff;">
                            <div style="font-size: 40px; margin-bottom: 10px;">✉️</div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800;">تم اختبار اتصال البريد بنجاح!</h1>
                            <p style="margin: 6px 0 0; font-size: 14px; opacity: 0.9; color: #bfdbfe;">
                                عيادة {{ $doctorName }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px;">
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px; font-size: 14px; color: #166534; line-height: 1.6; margin-bottom: 20px;">
                                <strong>تهانينا!</strong> إعدادات Google Gmail SMTP على خادمك تعمل بشكل مثالي.<br>
                                ستصلك من الآن إشعارات فورية بكل حجز جديد وكل عملية تأكيد دفع مباشرة على هذا البريد.
                            </div>

                            <table width="100%" cellspacing="0" cellpadding="8" style="font-size: 13px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td width="40%" style="color: #64748b;">وقت إرسال الاختبار:</td>
                                    <td style="color: #0f172a; font-weight: 700;">{{ $testTime }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b;">حالة النظام:</td>
                                    <td style="color: #16a34a; font-weight: 700;">جاهز ومفعل (Active)</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; padding: 15px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
                            منصة إدارة عيادة د. يونس المرشد &copy; {{ date('Y') }}
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
