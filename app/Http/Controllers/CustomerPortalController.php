<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Models\AcrMember;
use App\Models\AcrPointTransaction;
use App\Models\Customer;
use App\Models\Ticket;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPortalController extends Controller
{
    // ── Halaman Login ────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_arm'   => 'required|string',
            'whatsapp' => 'required|string',
        ], [
            'id_arm.required'   => 'ID Pelanggan wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $customer = Customer::where('id_arm', strtoupper(trim($request->id_arm)))
            ->where('whatsapp', 'like', '%' . preg_replace('/[^0-9]/', '', $request->whatsapp) . '%')
            ->first();

        if (!$customer) {
            return back()->withErrors(['id_arm' => 'ID Pelanggan atau No. WhatsApp tidak ditemukan.'])->withInput();
        }

        $member = AcrMember::firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'id_pelanggan' => $customer->id_arm,
                'nama'         => $customer->name,
                'whatsapp'     => $customer->whatsapp,
                'total_poin'   => 0,
            ]
        );

        $member->update(['id_pelanggan' => $customer->id_arm, 'nama' => $customer->name]);
        Auth::guard('member')->login($member, remember: true);

        return redirect()->route('portal.dashboard');
    }

    public function logout()
    {
        Auth::guard('member')->logout();
        return redirect()->route('portal.login')->with('success', 'Anda telah logout.');
    }

    // ── Dashboard Pelanggan ──────────────────────────────────────────────────

    public function dashboard()
    {
        $member   = Auth::guard('member')->user();
        $customer = $member->customer;

        if (!$customer) {
            return redirect()->route('portal.login')
                ->withErrors(['id_arm' => 'Data pelanggan tidak ditemukan, hubungi CS.']);
        }

        $unpaidInvoices = $customer->invoices()
            ->where('status', InvoiceStatus::BELUM->value)
            ->orderByDesc('period')->get();

        $invoiceHistory = $customer->invoices()
            ->orderByDesc('period')->take(12)->get();

        $pointHistory = AcrPointTransaction::where('member_id', $member->id)
            ->orderByDesc('created_at')->take(10)->get();

        $activeTickets = Ticket::where('customer_id', $customer->id)
            ->whereNotIn('status', [TicketStatus::RESOLVED->value, TicketStatus::CLOSED->value])
            ->orderByDesc('created_at')->get();

        return view('portal.dashboard', compact(
            'member', 'customer', 'unpaidInvoices',
            'invoiceHistory', 'pointHistory', 'activeTickets'
        ));
    }

    // ── Halaman Invoice ──────────────────────────────────────────────────────

    public function invoiceDetail($invoiceId)
    {
        $member   = Auth::guard('member')->user();
        $customer = $member->customer;
        $invoice  = $customer->invoices()->findOrFail($invoiceId);

        return view('portal.invoice', compact('invoice', 'customer', 'member'));
    }

    // ── Halaman Tiket Gangguan ───────────────────────────────────────────────

    public function ticketIndex()
    {
        $member   = Auth::guard('member')->user();
        $customer = $member->customer;
        $tickets  = Ticket::where('customer_id', $customer->id)->orderByDesc('created_at')->get();

        return view('portal.tickets', compact('tickets', 'customer', 'member'));
    }

    public function ticketCreate()
    {
        $member     = Auth::guard('member')->user();
        $customer   = $member->customer;
        $categories = TicketCategory::cases();

        return view('portal.ticket-create', compact('customer', 'member', 'categories'));
    }

    public function ticketStore(Request $request)
    {
        $request->validate([
            'category'    => 'required|string',
            'description' => 'required|string|min:10|max:1000',
        ], [
            'description.min' => 'Tolong jelaskan keluhan Anda minimal 10 karakter.',
        ]);

        $member   = Auth::guard('member')->user();
        $customer = $member->customer;

        Ticket::create([
            'customer_id' => $customer->id,
            'category'    => $request->category,
            'description' => $request->description,
            'status'      => TicketStatus::OPEN->value,
        ]);

        return redirect()->route('portal.tickets')
            ->with('success', 'Laporan gangguan berhasil dikirim! Tim teknisi kami akan segera menghubungi Anda.');
    }

    public function ticketDetail($id)
    {
        $member   = Auth::guard('member')->user();
        $customer = $member->customer;
        $ticket   = Ticket::where('customer_id', $customer->id)->findOrFail($id);

        return view('portal.ticket-detail', compact('ticket', 'customer', 'member'));
    }

    // ── Pembayaran Midtrans ──────────────────────────────────────────────────

    public function generatePayment($id, MidtransPaymentService $midtrans)
    {
        $member   = Auth::guard('member')->user();
        $customer = $member->customer;
        $invoice  = $customer->invoices()->findOrFail($id);

        $statusVal = $invoice->status instanceof InvoiceStatus
            ? $invoice->status->value
            : $invoice->status;

        if ($statusVal === InvoiceStatus::LUNAS->value) {
            return response()->json(['error' => 'Invoice sudah lunas.'], 422);
        }

        try {
            $data = $midtrans->generatePaymentToken($invoice);
            return response()->json([
                'snap_token'  => $data['token'],
                'payment_url' => $data['url'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat link pembayaran: ' . $e->getMessage()], 500);
        }
    }
}
