<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre commande</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- ── HEADER ── --}}
        <tr>
          <td style="background:linear-gradient(135deg,#1d4ed8,#2563eb);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            <div style="font-size:2rem;margin-bottom:8px;">{{ $icon }}</div>
            <h1 style="margin:0;color:#fff;font-size:1.5rem;font-weight:900;letter-spacing:-.02em;">
              ShopCI
            </h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,.7);font-size:.85rem;">
              Votre boutique en ligne de confiance
            </p>
          </td>
        </tr>

        {{-- ── CORPS ── --}}
        <tr>
          <td style="background:#fff;padding:40px;">

            <p style="margin:0 0 6px;font-size:.85rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
              Mise à jour de commande
            </p>
            <h2 style="margin:0 0 24px;font-size:1.5rem;font-weight:900;color:#0f172a;letter-spacing:-.03em;">
              Bonjour, {{ $order->user->name }} !
            </h2>

            <p style="margin:0 0 24px;color:#475569;line-height:1.7;font-size:.95rem;">
              Votre commande <strong style="color:#1d4ed8;">{{ $order->order_number }}</strong>
              vient de changer de statut.
            </p>

            {{-- Statut --}}
            @php
              $colors = [
                'pending'    => ['bg'=>'#fef9c3','color'=>'#854d0e','border'=>'#fde68a'],
                'paid'       => ['bg'=>'#dcfce7','color'=>'#166534','border'=>'#bbf7d0'],
                'processing' => ['bg'=>'#dbeafe','color'=>'#1e40af','border'=>'#bfdbfe'],
                'shipped'    => ['bg'=>'#ede9fe','color'=>'#5b21b6','border'=>'#ddd6fe'],
                'delivered'  => ['bg'=>'#dcfce7','color'=>'#166534','border'=>'#bbf7d0'],
                'cancelled'  => ['bg'=>'#fee2e2','color'=>'#991b1b','border'=>'#fecaca'],
                'refunded'   => ['bg'=>'#f1f5f9','color'=>'#475569','border'=>'#e2e8f0'],
              ];
              $c = $colors[$order->status] ?? $colors['pending'];
            @endphp

            <div style="background:{{ $c['bg'] }};border:2px solid {{ $c['border'] }};border-radius:12px;padding:20px 24px;text-align:center;margin-bottom:32px;">
              <div style="font-size:1.1rem;font-weight:900;color:{{ $c['color'] }};letter-spacing:-.01em;">
                {{ $icon }} {{ $order->status_label }}
              </div>
              @php
                $messages = [
                  'pending'    => 'Votre commande est en attente de traitement.',
                  'paid'       => 'Votre paiement a été confirmé. Nous préparons votre commande.',
                  'processing' => 'Votre commande est en cours de préparation.',
                  'shipped'    => 'Votre commande a été expédiée et est en route !',
                  'delivered'  => 'Votre commande a été livrée. Nous espérons que vous êtes satisfait !',
                  'cancelled'  => 'Votre commande a été annulée. Le stock a été restauré.',
                  'refunded'   => 'Votre remboursement a été initié.',
                ];
              @endphp
              <p style="margin:8px 0 0;font-size:.88rem;color:{{ $c['color'] }};opacity:.85;">
                {{ $messages[$order->status] ?? '' }}
              </p>
            </div>

            {{-- Détails commande --}}
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:32px;">
              <tr>
                <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">N° Commande</span><br>
                  <span style="font-size:.95rem;font-weight:800;color:#1d4ed8;">{{ $order->order_number }}</span>
                </td>
                <td style="padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Montant total</span><br>
                  <span style="font-size:.95rem;font-weight:800;color:#16a34a;">{{ $order->formatted_total }}</span>
                </td>
              </tr>
              <tr>
                <td style="padding:16px 20px;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Adresse de livraison</span><br>
                  <span style="font-size:.85rem;color:#334155;">
                    {{ $order->shipping_quartier ? $order->shipping_quartier.', ' : '' }}{{ $order->shipping_commune ? $order->shipping_commune.' — ' : '' }}{{ $order->shipping_city }}
                  </span>
                </td>
                <td style="padding:16px 20px;">
                  <span style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Date</span><br>
                  <span style="font-size:.85rem;color:#334155;">{{ $order->updated_at->format('d/m/Y à H:i') }}</span>
                </td>
              </tr>
            </table>

            {{-- Articles --}}
            <p style="margin:0 0 12px;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">
              Articles commandés
            </p>
            @foreach($order->items as $item)
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:.88rem;">
              <span style="color:#334155;font-weight:600;">{{ $item->product_name }} <span style="color:#94a3b8;font-weight:400;">× {{ $item->quantity }}</span></span>
              <span style="font-weight:800;color:#1d4ed8;">{{ $item->formatted_subtotal }}</span>
            </div>
            @endforeach

            {{-- Bouton CTA --}}
            <div style="text-align:center;margin-top:32px;">
              <a href="{{ route('orders.show', $order) }}"
                 style="display:inline-block;background:linear-gradient(135deg,#1d4ed8,#2563eb);
                        color:#fff;text-decoration:none;padding:14px 36px;border-radius:12px;
                        font-weight:800;font-size:.95rem;letter-spacing:-.01em;">
                Voir ma commande
              </a>
            </div>

          </td>
        </tr>

        {{-- ── FOOTER ── --}}
        <tr>
          <td style="background:#f8fafc;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;
                     border-top:1px solid #e2e8f0;">
            <p style="margin:0 0 4px;font-size:.78rem;color:#94a3b8;">
              Cet email a été envoyé automatiquement par <strong style="color:#64748b;">ShopCI</strong>.
            </p>
            <p style="margin:0;font-size:.75rem;color:#cbd5e1;">
              Merci de votre confiance &hearts;
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
