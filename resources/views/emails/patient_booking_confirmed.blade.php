<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم تأكيد موعدك بنجاح</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl; text-align: right; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Container Card -->
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #10b981, #059669); padding: 35px 30px; text-align: center; color: #ffffff;">
                            <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 6px 18px; border-radius: 50px; font-size: 13px; font-weight: bold; margin-bottom: 12px;">
                                عيادة د. يونس المرشد
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff;">
                                🎉 تم تأكيد موعدك بنجاح!
                            </h1>
                            <p style="margin: 10px 0 0; font-size: 14px; opacity: 0.95; color: #d1fae5;">
                                رقم تأكيد الحجز: <strong style="background: #ffffff; color: #059669; padding: 4px 12px; border-radius: 6px; font-family: monospace; font-size: 16px;">{{ $booking->booking_reference }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <p style="font-size: 15px; color: #334155; line-height: 1.7; margin-top: 0;">
                                مرحباً بك <strong>{{ $booking->patient?->name ?? 'عزيزي المراجع' }}</strong>،<br>
                                يسرنا إبلاغك بأنه تم التحقق من عملية الدفع وتأكيد موعدك مع <strong>د. يونس المرشد</strong> رسمياً.
                            </p>

                            <!-- Confirmed Details Card -->
                            <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 20px; margin: 24px 0;">
                                <h3 style="margin: 0 0 14px; font-size: 16px; color: #166534; font-weight: 700;">
                                    📌 تفاصيل الموعد المؤكد:
                                </h3>
                                <table width="100%" cellspacing="0" cellpadding="6" style="font-size: 14px;">
                                    <tr>
                                        <td width="35%" style="color: #4b5563;">الخدمة:</td>
                                        <td style="color: #0f172a; font-weight: 700;">{{ $booking->service?->title ?? 'جلسة استشارة' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #4b5563;">التاريخ:</td>
                                        <td style="color: #059669; font-weight: 700;">{{ $booking->date instanceof \DateTimeInterface ? $booking->date->format('Y-m-d') : substr((string)$booking->date, 0, 10) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #4b5563;">الوقت:</td>
                                        <td style="color: #059669; font-weight: 700;">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #4b5563;">المكان / الطريقة:</td>
                                        <td style="color: #0f172a; font-weight: 700;">
                                            {{ $booking->booking_type === 'clinic' ? '🏥 في مقر العيادة' : '💻 استشارة أونلاين' }}
                                        </td>
                                    </tr>
                                    @if($booking->booking_type === 'online')
                                    <tr>
                                        <td style="color: #4b5563;">رابط الجلسة:</td>
                                        <td style="color: #2563eb; font-weight: bold;">
                                            @if(!empty($booking->service?->payment_url))
                                                <a href="{{ $booking->service->payment_url }}" target="_blank" style="color: #2563eb;">اضغط هنا للدخول للجلسة</a>
                                            @else
                                                سيتم إرسال رابط Google Meet أو الاتصال بك عبر الواتساب عند بدء الموعد.
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Instructions Box -->
                            <div style="background-color: #f8fafc; border-radius: 10px; padding: 16px; border: 1px solid #e2e8f0; font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                                💡 <strong>ملاحظة هامة:</strong> يرجى التواجد أو التفرغ قبل الموعد بـ 5 دقائق. إذا احتجت لتعديل موعدك أو واجهت أي استفسار، يمكنك التواصل مع إدارة العيادة مباشرة.
                            </div>

                            <!-- CTA Button to Dashboard -->
                            <div style="text-align: center; margin: 25px 0 10px;">
                                <a href="{{ url('/dashboard') }}" 
                                   target="_blank"
                                   style="display: inline-block; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 50px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                                    استعراض مواعيدي في المنصة
                                </a>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
                            عيادة د. يونس المرشد للاستشارات النفسية والأسرية.<br>
                            العراق | للاستفسارات: {{ \App\Models\Setting::get('doctor_whatsapp', '+9647700000000') }}
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
