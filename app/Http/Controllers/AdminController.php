<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

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
        $order->load('items.product', 'payment', 'user');
        return view('admin.orders.show', compact('order'));
    }

    /** Changer le statut d'une commande */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,paid,processing,shipped,delivered,cancelled,refunded'],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Statut de la commande #{$order->order_number} mis à jour.");
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
}
