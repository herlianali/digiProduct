<?php

namespace App\Http\Controllers;

use App\Models\ProjectInquiry;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicInquiryController extends Controller
{
    public function index()
    {
        return Inertia::render('Landing/Gsap/GetInTuch/Index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:150',
            'company_name'        => 'nullable|string|max:150',
            'email'               => 'required|email|max:254',
            'budget'              => 'nullable|string|max:100',
            'voucher_code'        => 'nullable|string',
            'project_description' => 'nullable|string',
            'scopes'              => 'nullable|array',
            'scopes.*'            => 'in:design_service,illustration_service',
            'timelines'           => 'nullable|array',
            'timelines.*'         => 'in:asap,1_2_weeks,long_term,flexible',
        ]);

        // Validasi voucher jika diisi — cek exist & aktif
        if (!empty($data['voucher_code'])) {
            $voucher = Voucher::where('code', $data['voucher_code'])
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            if (!$voucher) {
                return back()->withErrors(['voucher_code' => 'Voucher tidak valid atau sudah kadaluarsa.'])->withInput();
            }
        }

        // Status default 'new' untuk submission dari publik
        $data['status'] = 'new';

        $inquiry = ProjectInquiry::create($data);

        foreach ($data['scopes'] ?? [] as $scope) {
            $inquiry->scopes()->create(['scope' => $scope]);
        }

        foreach ($data['timelines'] ?? [] as $timeline) {
            $inquiry->timelines()->create(['timeline' => $timeline]);
        }

        return back()->with('success', 'Pesan kamu berhasil terkirim! Kami akan segera menghubungi kamu.');
    }
}
