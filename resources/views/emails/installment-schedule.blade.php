<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plan de paiement</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- HEADER --}}
        <tr>
          <td style="background:linear-gradient(135deg,#1d4ed8,#2563eb);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            <div style="font-size:2rem;margin-bottom:8px;">📅</div>
            <h1 style="margin:0;color:#fff;font-size:1.5rem;font-weight:900;">ShopCI</h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,.7);font-size:.85rem;">Votre plan de paiement par tranches</p>
          </td>
        </tr>

        {{-- CORPS --}}
        <tr>
          <td style="background:#fff;padding:40px;">

            <p style="margin:0 0 6px;font-size:.85rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
              Confirmation de commande
            </p>
            <h2 style="margin:0 0 24px;font-size:1.4rem;font-weight:900;color:#0f172a;">
              Bonjour, {{ $order->user->name }} !
            </h2>

            <p style="margin:0 0 24px;color:#475569;line-height:1.7;font-size:.95rem;">
              Votre commande <strong style="color:#1d4ed8;">{{ $order->order_number }}</strong>
              a été enregistrée. Vous avez choisi de payer en
              <strong style="color:#1d4ed8;">{{ $total }} fois</strong>.
              Voici le détail de votre plan de paiement :
            </p>

            {{-- Récapitulatif commande --}}
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:28px;">
              <tr>
                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">N° Commande</span><br>
                  <span style="font-size:.95rem;font-weight:800;color:#1d4ed8;">{{ $order->order_number }}</span>
                </td>
                <td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Total commande</span><br>
                  <span style="font-size:.95rem;font-weight:800;color:#0f172a;">{{ $order->formatted_total }}</span>
                </td>
              </tr>
              <tr>
                <td style="padding:14px 20px;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Montant / tranche</span><br>
                  <span style="font-size:.95rem;font-weight:800;color:#16a34a;">{{ $order->installments->first()->formatted_amount }}</span>
                </td>
                <td style="padding:14px 20px;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Nombre de tranches</span><br>
                  <span style="font-size:.95rem;font-weight:800;color:#0f172a;">{{ $total }} fois</span>
                </td>
              </tr>
            </table>

            {{-- Plan des tranches --}}
            <p style="margin:0 0 14px;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">
              Échéancier de paiement
            </p>

            @php
              $statusColors = [
                'paid'    => ['bg'=>'#dcfce7','border'=>'#bbf7d0','color'=>'#166534','icon'=>'✅'],
                'pending' => ['bg'=>'#eff6ff','border'=>'#bfdbfe','color'=>'#1e40af','icon'=>'⏳'],
              ];
            @endphp

            @foreach($order->installments as $inst)
            @php
              $c = $statusColors[$inst->status] ?? $statusColors['pending'];
            @endphp
            <div style="display:flex;justify-content:space-between;align-items:center;
                        padding:14px 18px;border-radius:12px;margin-bottom:10px;
                        background:{{ $c['bg'] }};border:2px solid {{ $c['border'] }};">
              <div>
                <div style="font-weight:800;font-size:.9rem;color:{{ $c['color'] }};">
                  {{ $c['icon'] }} Tranche {{ $inst->installment_number }}/{{ $total }}
                </div>
                <div style="font-size:.78rem;color:{{ $c['color'] }};opacity:.8;margin-top:3px;">
                  @if($inst->status === 'paid')
                    Payée le {{ $inst->paid_at->format('d/m/Y à H:i') }}
                  @else
                    À régler avant le <strong>{{ $inst->due_date->format('d/m/Y') }}</strong>
                  @endif
                </div>
              </div>
              <span style="font-weight:900;font-size:1.05rem;color:{{ $c['color'] }};">
                {{ $inst->formatted_amount }}
              </span>
            </div>
            @endforeach

            {{-- Message de rappel --}}
            @if($order->installments->where('status','pending')->count() > 0)
            <div style="margin-top:24px;padding:16px 20px;background:#fef9c3;border:1px solid #fde68a;border-radius:12px;">
              <p style="margin:0;font-size:.84rem;color:#854d0e;line-height:1.6;">
                <strong>⚠️ Important :</strong>
                Vous recevrez un email de rappel avant chaque échéance.
                En cas de question, contactez-nous par retour de ce mail.
              </p>
            </div>
            @endif

            {{-- CTA --}}
            <div style="text-align:center;margin-top:32px;">
              <a href="{{ route('orders.show', $order) }}"
                 style="display:inline-block;background:linear-gradient(135deg,#1d4ed8,#2563eb);
                        color:#fff;text-decoration:none;padding:14px 36px;border-radius:12px;
                        font-weight:800;font-size:.95rem;">
                Suivre ma commande
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
