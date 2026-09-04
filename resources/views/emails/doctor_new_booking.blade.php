<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $eventTitle ?? 'إشعار حجز جديد' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; direction: rtl; text-align: right; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Container Card -->
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a, #3B52A4); padding: 30px; text-align: center; color: #ffffff;">
                            <div style="display: inline-block; background: rgba(255,255,255,0.15); padding: 8px 18px; border-radius: 50px; font-size: 13px; font-weight: bold; margin-bottom: 12px; letter-spacing: 0.5px;">
                                🏥 منصة عيادة د. يونس المرشد
                            </div>
                            <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff;">
                                {{ $eventTitle ?? '🔔 حجز جديد بانتظار المراجعة' }}
                            </h1>
                            <p style="margin: 8px 0 0; font-size: 14px; opacity: 0.9; color: #e0e7ff;">
                                رقم مرجع الحجز: <strong style="background: #ffffff; color: #1e3a8a; padding: 2px 8px; border-radius: 6px; font-family: monospace;">{{ $booking->booking_reference }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Notice Badge -->
                            <div style="background-color: #fffbeb; border-right: 4px solid #f59e0b; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; color: #92400e; line-height: 1.6;">
                                <strong>تنبيه الإدارة:</strong> تم تسجيل طلب حجز جديد بحالة 
                                <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 6px; font-weight: bold;">
                                    {{ $booking->status === 'PendingPaymentReview' ? 'بانتظار مراجعة الدفع' : ($booking->status === 'AwaitingPayment' ? 'بانتظار الدفع' : $booking->status) }}
                                </span>.
                                يرجى مراجعة إيصال الدفع وتأكيد الموعد للمريض.
                            </div>

                            <!-- Patient Info Table -->
                            <h3 style="font-size: 16px; margin: 0 0 14px; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">
                                👤 بيانات المريض
                            </h3>
                            <table width="100%" cellspacing="0" cellpadding="8" style="font-size: 14px; margin-bottom: 24px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td width="35%" style="color: #64748b; font-weight: 600;">اسم المريض:</td>
                                    <td style="color: #0f172a; font-weight: 700;">{{ $booking->patient?->name ?? ($booking->temp_user_data['name'] ?? 'زائر') }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">رقم الهاتف:</td>
                                    <td style="color: #0f172a; font-weight: 700; direction: ltr; text-align: right;">{{ $booking->patient?->phone ?? ($booking->temp_user_data['phone'] ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">البريد الإلكتروني:</td>
                                    <td style="color: #0f172a;">{{ $booking->patient?->email ?? ($booking->temp_user_data['email'] ?? 'غير متوفر') }}</td>
                                </tr>
                            </table>

                            <!-- Appointment Info Table -->
                            <h3 style="font-size: 16px; margin: 0 0 14px; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">
                                📅 تفاصيل الموعد والخدمة
                            </h3>
                            <table width="100%" cellspacing="0" cellpadding="8" style="font-size: 14px; margin-bottom: 24px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td width="35%" style="color: #64748b; font-weight: 600;">الخدمة المطلوبة:</td>
                                    <td style="color: #1e3a8a; font-weight: 700;">{{ $booking->service?->title ?? 'جلسة استشارة' }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">نوع الحجز:</td>
                                    <td style="color: #0f172a;">
                                        {{ $booking->booking_type === 'clinic' ? '🏥 كشف في العيادة' : '💻 استشارة أونلاين' }}
                                        ({{ $booking->consultation_type_label ?? $booking->consultation_type }})
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">تاريخ وتوقيت الجلسة:</td>
                                    <td style="color: #059669; font-weight: 700;">
                                        {{ $booking->date instanceof \DateTimeInterface ? $booking->date->format('Y-m-d') : substr((string)$booking->date, 0, 10) }}
                                        ({{ $booking->start_time }} - {{ $booking->end_time }})
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">طريقة الدفع:</td>
                                    <td style="color: #7c3aed; font-weight: 700;">{{ strtoupper($booking->payment_method ?? 'zaincash') }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">المبلغ الإجمالي:</td>
                                    <td style="color: #0f172a; font-size: 16px; font-weight: 800;">{{ number_format($booking->price ?? $booking->service?->price ?? 0, 2) }} $</td>
                                </tr>
                                @if(!empty($booking->notes))
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">ملاحظات الحجز / التحويل:</td>
                                    <td style="color: #334155; font-size: 13px;">{{ $booking->notes }}</td>
                                </tr>
                                @endif
                            </table>

                            <!-- CTA Button to Admin Panel -->
                            <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
                                <a href="{{ url('/admin/bookings?search=' . $booking->booking_reference) }}" 
                                   target="_blank"
                                   style="display: inline-block; background: linear-gradient(135deg, #1e3a8a, #3B52A4); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 50px; font-size: 15px; font-weight: 700; box-shadow: 0 6px 16px rgba(30, 58, 138, 0.25);">
                                    👉 فتح الحجز في لوحة التحكم والتأكيد
                                </a>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
                            تم إرسال هذا الإشعار الآلي من نظام الحجوزات الذكي لمنصة عيادة د. يونس المرشد.<br>
                            جميع الحقوق محفوظة &copy; {{ date('Y') }}
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
