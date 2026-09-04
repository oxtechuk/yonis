<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم استلام طلب حجزك - قيد المراجعة</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl; text-align: right; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Container Card -->
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f766e, #0d9488); padding: 30px; text-align: center; color: #ffffff;">
                            <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: bold; margin-bottom: 12px;">
                                عيادة د. يونس المرشد للاستشارات النفسية
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff;">
                                تم استلام طلب حجزك بنجاح
                            </h1>
                            <p style="margin: 8px 0 0; font-size: 14px; opacity: 0.95; color: #ccfbf1;">
                                رقم مرجع الحجز الخاص بك: <strong style="background: #ffffff; color: #0f766e; padding: 3px 10px; border-radius: 6px; font-family: monospace; font-size: 15px;">{{ $booking->booking_reference }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Status Alert -->
                            <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-right: 4px solid #f59e0b; padding: 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; color: #92400e; line-height: 1.7;">
                                <strong style="font-size: 15px;">⏳ حالة الحجز: قيد المراجعة والتدقيق</strong><br>
                                عزيزي/عزيزتي <strong>{{ $booking->patient?->name ?? ($booking->temp_user_data['name'] ?? 'المراجع الكريم') }}</strong>، تم استلام بيانات حجزك بنجاح. يقوم فريق العيادة حالياً بمراجعة إيصال وتفاصيل الدفع، وسيصلك <strong>إشعار التأكيد النهائي</strong> فور إتمام التحقق.
                            </div>

                            <!-- Summary Details -->
                            <h3 style="font-size: 16px; margin: 0 0 14px; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">
                                📋 ملخص الموعد المحجوز
                            </h3>
                            <table width="100%" cellspacing="0" cellpadding="10" style="font-size: 14px; margin-bottom: 24px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td width="35%" style="color: #64748b; font-weight: 600;">الخدمة:</td>
                                    <td style="color: #0f172a; font-weight: 700;">{{ $booking->service?->title ?? 'جلسة استشارة' }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">نوع الجلسة:</td>
                                    <td style="color: #0f172a;">
                                        {{ $booking->booking_type === 'clinic' ? 'كشف واستشارة في العيادة' : 'استشارة أونلاين عبر الإنترنت' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">الموعد والتوقيت:</td>
                                    <td style="color: #0d9488; font-weight: 700;">
                                        {{ $booking->date instanceof \DateTimeInterface ? $booking->date->format('Y-m-d') : substr((string)$booking->date, 0, 10) }}
                                        | الساعة {{ $booking->start_time }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">المبلغ:</td>
                                    <td style="color: #0f172a; font-weight: 700;">{{ number_format($booking->price ?? $booking->service?->price ?? 0, 2) }} $</td>
                                </tr>
                            </table>

                            <!-- WhatsApp Helper Box -->
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 18px; border-radius: 12px; text-align: center; margin-bottom: 20px;">
                                <p style="margin: 0 0 12px; font-size: 14px; color: #166534; font-weight: 600;">
                                    هل قمت بالتحويل وترغب بتسريع تأكيد حجزك؟
                                </p>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::get('doctor_whatsapp', '+9647700000000')) }}?text={{ urlencode('مرحباً دكتور، هذا رقم مرجع حجزي: ' . $booking->booking_reference . ' وأرغب بتأكيد الموعد.') }}" 
                                   target="_blank"
                                   style="display: inline-block; background: #22c55e; color: #ffffff; text-decoration: none; padding: 10px 24px; border-radius: 50px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(34,197,94,0.3);">
                                    📲 إرسال الإيصال عبر الواتساب مباشرة
                                </a>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
                            عيادة د. يونس المرشد للاستشارات النفسية والأسرية.<br>
                            شكراً لثقتكم بنا، ونتمنى لكم دوام الصحة والعافية.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
