<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tranche reçue</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- HEADER --}}
        <tr>
          <td style="background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            <div style="font-size:2rem;margin-bottom:8px;">✅</div>
            <h1 style="margin:0;color:#fff;font-size:1.5rem;font-weight:900;">ShopCI</h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,.7);font-size:.85rem;">Paiement par tranche</p>
          </td>
        </tr>

        {{-- CORPS --}}
        <tr>
          <td style="background:#fff;padding:40px;">
            <h2 style="margin:0 0 16px;font-size:1.4rem;font-weight:900;color:#0f172a;">
              Bonjour, {{ $order->user->name }} !
            </h2>
            <p style="margin:0 0 24px;color:#475569;line-height:1.7;">
              Nous avons bien reçu votre <strong style="color:#16a34a;">tranche {{ $installment->installment_number }} sur {{ $total }}</strong>
              pour la commande <strong style="color:#1d4ed8;">{{ $order->order_number }}</strong>.
            </p>

            {{-- Tranche payée --}}
            <div style="background:#dcfce7;border:2px solid #bbf7d0;border-radius:12px;padding:20px 24px;text-align:center;margin-bottom:28px;">
              <div style="font-size:1rem;font-weight:900;color:#166534;">
                ✅ Tranche {{ $installment->installment_number }}/{{ $total }} — {{ $installment->formatted_amount }}
              </div>
              <p style="margin:8px 0 0;font-size:.85rem;color:#166534;opacity:.85;">
                Reçue le {{ $installment->paid_at->format('d/m/Y à H:i') }}
              </p>
            </div>

            {{-- Progression --}}
            <p style="margin:0 0 12px;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">
              Progression du paiement
            </p>
            <div style="background:#f1f5f9;border-radius:8px;overflow:hidden;height:10px;margin-bottom:8px;">
              <div style="background:linear-gradient(90deg,#16a34a,#22c55e);width:{{ round(($paidCount/$total)*100) }}%;height:100%;border-radius:8px;"></div>
            </div>
            <p style="margin:0 0 28px;font-size:.82rem;color:#64748b;text-align:right;">
              {{ $paidCount }}/{{ $total }} tranche(s) payée(s)
            </p>

            @if($remaining > 0)
            @php $nextInst = $order->installments->where('status','pending')->sortBy('installment_number')->first(); @endphp

            {{-- Prochaine échéance mise en avant --}}
            @if($nextInst)
            <div style="background:#fff7ed;border:2px solid #fed7aa;border-radius:12px;padding:18px 22px;margin-bottom:16px;">
              <div style="font-size:.72rem;font-weight:700;color:#9a3412;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">
                🔔 Prochaine échéance
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                  <div style="font-weight:900;font-size:1rem;color:#c2410c;">
                    Tranche {{ $nextInst->installment_number }}/{{ $total }}
                  </div>
                  <div style="font-size:.82rem;color:#9a3412;margin-top:4px;">
                    À régler avant le <strong>{{ $nextInst->due_date->format('d/m/Y') }}</strong>
                    (dans {{ now()->diffInDays($nextInst->due_date) }} jours)
                  </div>
                </div>
                <span style="font-size:1.3rem;font-weight:900;color:#c2410c;">{{ $nextInst->formatted_amount }}</span>
              </div>
            </div>
            @endif

            {{-- Autres tranches restantes --}}
            @if($remaining > 1)
            <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:12px;padding:14px 18px;margin-bottom:28px;">
              <div style="font-weight:700;color:#854d0e;font-size:.82rem;margin-bottom:8px;">
                ⏳ Tranches suivantes
              </div>
              @foreach($order->installments->where('status','pending')->sortBy('installment_number')->skip(1) as $inst)
              <div style="display:flex;justify-content:space-between;font-size:.8rem;color:#92400e;padding:4px 0;border-bottom:1px solid rgba(0,0,0,.05);">
                <span>Tranche {{ $inst->installment_number }}/{{ $total }}</span>
                <span><strong>{{ $inst->formatted_amount }}</strong> — {{ $inst->due_date->format('d/m/Y') }}</span>
              </div>
              @endforeach
            </div>
            @else
            <div style="margin-bottom:28px;"></div>
            @endif
            @else
            <div style="background:#dcfce7;border:2px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:28px;text-align:center;">
              <div style="font-weight:900;color:#166534;font-size:1rem;">🎉 Félicitations ! Toutes les tranches ont été réglées.</div>
            </div>
            @endif

            {{-- CTA --}}
            <div style="text-align:center;margin-top:8px;">
              <a href="{{ route('orders.show', $order) }}"
                 style="display:inline-block;background:linear-gradient(135deg,#1d4ed8,#2563eb);
                        color:#fff;text-decoration:none;padding:14px 36px;border-radius:12px;
                        font-weight:800;font-size:.95rem;">
                Voir ma commande
              </a>
            </div>
          </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
          <td style="background:#f8fafc;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
            <p style="margin:0;font-size:.78rem;color:#94a3b8;">
              Email automatique de <strong style="color:#64748b;">ShopCI</strong> — Merci de votre confiance ♥
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
