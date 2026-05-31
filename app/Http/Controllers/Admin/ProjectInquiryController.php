<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectInquiry;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectInquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectInquiry::with(['scopes', 'timelines']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email',   'like', '%' . $request->search . '%')
                    ->orWhere('company_name', 'like', '%' . $request->search . '%');
            });
        }

        $inquiries = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Map scopes & timelines ke array string agar mudah dikonsumsi Vue
        $items = $inquiries->getCollection()->map(fn($i) => [
            ...$i->toArray(),
            'scopes'    => $i->scopes->pluck('scope')->toArray(),
            'timelines' => $i->timelines->pluck('timeline')->toArray(),
        ]);

        return Inertia::render('ProjectInquiries/Index', [
            'inquiries' => $items,
            'meta'      => ['total' => $inquiries->total()],
            'filters'   => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('ProjectInquiries/Form', [
            'mode'     => 'create',
            'vouchers' => Voucher::where('is_active', true)->get(['id', 'code', 'discount_pct']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:150',
            'company_name'        => 'nullable|string|max:150',
            'email'               => 'required|email|max:254',
            'budget'              => 'nullable|string|max:100',
            'voucher_code'        => 'nullable|exists:vouchers,code',
            'project_description' => 'nullable|string',
            'status'              => 'required|in:new,in_review,accepted,rejected',
            'scopes'              => 'nullable|array',
            'scopes.*'            => 'in:design_service,illustration_service',
            'timelines'           => 'nullable|array',
            'timelines.*'         => 'in:asap,1_2_weeks,long_term,flexible',
        ]);

        $inquiry = ProjectInquiry::create($data);

        foreach ($data['scopes'] ?? [] as $scope) {
            $inquiry->scopes()->create(['scope' => $scope]);
        }
        foreach ($data['timelines'] ?? [] as $timeline) {
            $inquiry->timelines()->create(['timeline' => $timeline]);
        }

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry berhasil ditambahkan.');
    }

    public function show(ProjectInquiry $inquiry)
    {
        $inquiry->load(['scopes', 'timelines', 'voucher']);

        return Inertia::render('ProjectInquiries/Show', [
            'inquiry' => [
                ...$inquiry->toArray(),
                'scopes'    => $inquiry->scopes->pluck('scope')->toArray(),
                'timelines' => $inquiry->timelines->pluck('timeline')->toArray(),
            ],
        ]);
    }

    public function edit(ProjectInquiry $inquiry)
    {
        $inquiry->load(['scopes', 'timelines']);

        return Inertia::render('ProjectInquiries/Form', [
            'mode'     => 'edit',
            'inquiry'  => [
                ...$inquiry->toArray(),
                'scopes'    => $inquiry->scopes->pluck('scope')->toArray(),
                'timelines' => $inquiry->timelines->pluck('timeline')->toArray(),
            ],
            'vouchers' => Voucher::where('is_active', true)->get(['id', 'code', 'discount_pct']),
        ]);
    }

    public function update(Request $request, ProjectInquiry $inquiry)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:150',
            'company_name'        => 'nullable|string|max:150',
            'email'               => 'required|email|max:254',
            'budget'              => 'nullable|string|max:100',
            'voucher_code'        => 'nullable|exists:vouchers,code',
            'project_description' => 'nullable|string',
            'status'              => 'required|in:new,in_review,accepted,rejected',
            'scopes'              => 'nullable|array',
            'scopes.*'            => 'in:design_service,illustration_service',
            'timelines'           => 'nullable|array',
            'timelines.*'         => 'in:asap,1_2_weeks,long_term,flexible',
        ]);

        $inquiry->update($data);

        // Sync pivot — delete lama, insert baru
        $inquiry->scopes()->delete();
        foreach ($data['scopes'] ?? [] as $scope) {
            $inquiry->scopes()->create(['scope' => $scope]);
        }

        $inquiry->timelines()->delete();
        foreach ($data['timelines'] ?? [] as $timeline) {
            $inquiry->timelines()->create(['timeline' => $timeline]);
        }

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry berhasil diperbarui.');
    }

    public function destroy(ProjectInquiry $inquiry)
    {
        // scopes & timelines ter-cascade delete otomatis karena FK ON DELETE CASCADE
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry berhasil dihapus.');
    }
}
