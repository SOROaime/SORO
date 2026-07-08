<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Notifications\InstallmentPaidNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminController — Tableau de bord administrateur
 * 
 * Toutes les routes de ce contrôleur sont protégées par le middleware 'admin'.
 * Statistiques, gestion commandes, paiements.
 */
class AdminController extends Controller
{
    /** Tableau de bord avec statistiques */
    public function dashboard()
    {
        // Statistiques générales
        $stats = [
            'total_products'  => Product::count(),
            'active_products' => Product::active()->count(),
            'total_users'     => User::where('role', 'user')->count(),
            'total_orders'    => Order::count(),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'paid_orders'     => Order::where('status', 'paid')->count(),
            'total_revenue'   => Payment::where('status', 'success')->sum('amount'),
            'today_revenue'   => Payment::where('status', 'success')
                                        ->whereDate('paid_at', today())
                                        ->sum('amount'),
        ];

        // Dernières commandes (10)
        $recentOrders = Order::with('user', 'payment')
                             ->latest()
                             ->take(10)
                             ->get();

        // Produits en rupture de stock
        $lowStockProducts = Product::where('stock', '<=', 5)
                                   ->orderBy('stock')
                                   ->take(5)
                                   ->get();

        // Revenus des 7 derniers jours (pour un mini graphique)
        $weeklyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyRevenue[] = [
                'date'    => $date->format('d/m'),
                'revenue' => Payment::where('status', 'success')
                                    ->whereDate('paid_at', $date)
                                    ->sum('amount'),
            ];
        }

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts', 'weeklyRevenue'));
    }

    /** Liste de tous les produits (admin) */
    public function products(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /** Liste de toutes les commandes */
    public function orders(Request $request)
    {
        $query = Order::with('user', 'payment');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /** Détail d'une commande (admin) */
    public function showOrder(Order $order)
    {
        $order->load('items.product', 'payment', 'user', 'installments');
        return view('admin.orders.show', compact('order'));
    }

    /** Changer le statut d'une commande */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,paid,processing,shipped,delivered,cancelled,refunded'],
        ]);

        $newStatus = $request->status;
        $oldStatus = $order->status;

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $order->restoreStock();
                $order->load('payment');
                if ($order->payment && in_array($order->payment->status, ['pending'])) {
                    $order->payment->update([
                        'status'         => 'cancelled',
                        'failure_reason' => 'Commande annulée par l\'administrateur.',
                    ]);
                }
            }
            $order->update(['status' => $newStatus]);
        });

        // Envoyer l'email de notification au client
        try {
            $order->load('user', 'items', 'payment');
            $order->user->notify(new OrderStatusNotification($order));
        } catch (\Exception $e) {
            report($e); // log sans bloquer
        }

        return back()->with('success', "Statut de la commande #{$order->order_number} mis à jour. Email envoyé au client.");
    }

    /** Liste de tous les paiements */
    public function payments(Request $request)
    {
        $query = Payment::with('order.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    /** Rapports de ventes & statistiques produits */
    public function reports(Request $request)
    {
        $period = (int) $request->get('period', 30);
        $startDate = now()->subDays($period)->startOfDay();

        // Revenus journaliers pour le graphique linéaire
        $dailyRevenue = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyRevenue[] = [
                'date'    => $date->format('d/m'),
                'revenue' => Payment::where('status', 'success')
                                    ->whereDate('paid_at', $date)->sum('amount'),
                'orders'  => Order::whereDate('created_at', $date)->count(),
            ];
        }

        // Commandes par statut
        $ordersByStatus = Order::where('created_at', '>=', $startDate)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->get();

        // Revenus par méthode de paiement
        $revenueByMethod = Payment::where('status', 'success')
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('payment_method, sum(amount) as total, count(*) as nb')
            ->groupBy('payment_method')->get();

        // Top 10 produits les plus vendus
        $topProducts = OrderItem::select(
                'product_name',
                DB::raw('sum(quantity) as total_qty'),
                DB::raw('sum(subtotal) as total_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', $startDate)
            ->whereNotIn('orders.status', ['cancelled'])
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(10)->get();

        // KPIs de la période
        $periodStats = [
            'revenue'       => Payment::where('status', 'success')->where('paid_at', '>=', $startDate)->sum('amount'),
            'orders'        => Order::where('created_at', '>=', $startDate)->count(),
            'avg_order'     => Order::where('created_at', '>=', $startDate)->avg('total_amount') ?? 0,
            'new_customers' => User::where('created_at', '>=', $startDate)->where('role', 'user')->count(),
        ];

        return view('admin.reports', compact('dailyRevenue', 'ordersByStatus', 'revenueByMethod', 'topProducts', 'periodStats', 'period'));
    }

    /** Export CSV du rapport de ventes */
    public function exportSales(Request $request)
    {
        $period = (int) $request->get('period', 30);
        $startDate = now()->subDays($period)->startOfDay();

        $orders = Order::with('user', 'payment', 'items')
            ->where('created_at', '>=', $startDate)
            ->latest()->get();

        $filename = 'rapport-ventes-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel

            fputcsv($file, ['N° Commande', 'Client', 'Date', 'Ville', 'Commune', 'Quartier', 'Montant (FCFA)', 'Statut commande', 'Statut paiement', 'Mode paiement'], ';');

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name ?? 'N/A',
                    $order->created_at->format('d/m/Y H:i'),
                    $order->shipping_city,
                    $order->shipping_commune,
                    $order->shipping_quartier,
                    number_format($order->total_amount, 0, ',', ' '),
                    $order->status_label,
                    $order->payment ? $order->payment->status_label : 'N/A',
                    $order->payment ? $order->payment->payment_method : 'N/A',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** Liste de tous les utilisateurs */
    public function users(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /** Liste de tous les avis clients */
    public function reviews(Request $request)
    {
        $query = Review::with('user', 'product')->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $query->whereHas('product', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            );
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => Review::count(),
            'avg'     => round(Review::avg('rating') ?? 0, 1),
            'five'    => Review::where('rating', 5)->count(),
            'one_two' => Review::whereIn('rating', [1, 2])->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /** Liste de toutes les tranches de paiement */
    public function installments(Request $request)
    {
        $query = Installment::with('order.user')
            ->orderByRaw("FIELD(status, 'overdue', 'pending', 'paid')")
            ->orderBy('due_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('order', fn($q) =>
                $q->where('order_number', 'like', '%' . $request->search . '%')
            );
        }

        $installments = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => Installment::where('status', 'pending')->count(),
            'paid'    => Installment::where('status', 'paid')->count(),
            'overdue' => Installment::where('status', 'pending')
                             ->whereDate('due_date', '<', today())->count(),
            'total_pending_amount' => Installment::where('status', 'pending')->sum('amount'),
        ];

        return view('admin.installments.index', compact('installments', 'stats'));
    }

    /** Marquer une tranche comme payée */
    public function markInstallmentPaid(Order $order, Installment $installment)
    {
        if ($installment->order_id !== $order->id) {
            abort(404);
        }

        if ($installment->status === 'paid') {
            return back()->with('error', 'Cette tranche est déjà marquée comme payée.');
        }

        $installment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        // Vérifier si toutes les tranches sont payées → mettre la commande en "paid"
        $order->load('installments');
        $allPaid = $order->installments->every(fn($i) => $i->status === 'paid');
        if ($allPaid) {
            $order->update(['status' => 'paid']);
            $order->payment->update(['status' => 'success', 'paid_at' => now()]);
        }

        // Email de confirmation au client
        try {
            $order->load('user', 'items', 'payment', 'installments');
            $order->user->notify(new InstallmentPaidNotification($order, $installment));
        } catch (\Exception $e) {
            report($e);
        }

        $msg = $allPaid
            ? "Toutes les tranches sont réglées. Commande #{$order->order_number} marquée comme payée."
            : "Tranche {$installment->installment_number}/{$order->installment_count} marquée comme payée.";

        return back()->with('success', $msg);
    }

    /** Supprimer un avis */
    public function destroyReview(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Avis supprimé.');
    }
}
