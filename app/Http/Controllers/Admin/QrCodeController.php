<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeController extends Controller
{
    /**
     * Display a listing of QR codes with pagination.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Build query with optional filters
        $query = QrCode::with('creator');

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Order by latest first
        $qrCodes = $query->latest()->paginate(15)->withQueryString();

        return view('admin.qrcodes.index', compact('qrCodes'));
    }

    /**
     * Show the form for creating a new QR code.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.qrcodes.create');
    }

    /**
     * Store a newly created QR code in storage.
     * Validates input, generates unique code, and sets created_by to current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'code' => 'nullable|string|max:255|unique:qr_codes,code',
            'type' => 'required|string|in:check_in,check_out,both',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ], [
            'type.required' => 'Please select a QR code type.',
            'type.in' => 'Invalid QR code type selected.',
            'valid_until.after_or_equal' => 'Valid until date must be after or equal to valid from date.',
        ]);

        // Generate unique code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateUniqueCode();
        }

        // Set created_by to current authenticated user
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['scan_count'] = 0;

        // Create the QR code record
        $qrCode = QrCode::create($validated);

        return redirect()
            ->route('admin.qrcodes.show', $qrCode)
            ->with('success', 'QR code created successfully. You can now generate the QR code image.');
    }

    /**
     * Display the specified QR code.
     *
     * @param  \App\Models\QrCode  $qrCode
     * @return \Illuminate\View\View
     */
    public function show(QrCode $qrCode)
    {
        // Load relationships
        $qrCode->load(['creator', 'attendances' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.qrcodes.show', compact('qrCode'));
    }

    /**
     * Show the form for editing the specified QR code.
     *
     * @param  \App\Models\QrCode  $qrCode
     * @return \Illuminate\View\View
     */
    public function edit(QrCode $qrCode)
    {
        return view('admin.qrcodes.edit', compact('qrCode'));
    }

    /**
     * Update the specified QR code in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QrCode  $qrCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, QrCode $qrCode)
    {
        // Validate the request
        $validated = $request->validate([
            'code' => 'nullable|string|max:255|unique:qr_codes,code,' . $qrCode->id,
            'type' => 'required|string|in:check_in,check_out,both',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ], [
            'type.required' => 'Please select a QR code type.',
            'type.in' => 'Invalid QR code type selected.',
            'valid_until.after_or_equal' => 'Valid until date must be after or equal to valid from date.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Update the QR code
        $qrCode->update($validated);

        return redirect()
            ->route('admin.qrcodes.show', $qrCode)
            ->with('success', 'QR code updated successfully.');
    }

    /**
     * Remove the specified QR code from storage.
     *
     * @param  \App\Models\QrCode  $qrCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(QrCode $qrCode)
    {
        // Delete the QR code image if it exists
        if ($qrCode->qr_image_path && Storage::disk('public')->exists($qrCode->qr_image_path)) {
            Storage::disk('public')->delete($qrCode->qr_image_path);
        }

        // Delete the QR code record
        $qrCode->delete();

        return redirect()
            ->route('admin.qrcodes.index')
            ->with('success', 'QR code deleted successfully.');
    }

    /**
     * Generate QR code image and save to storage.
     * Updates the qr_image_path in the database.
     *
     * @param  \App\Models\QrCode  $qrCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate(QrCode $qrCode)
    {
        try {
            // Delete old QR code image if exists
            if ($qrCode->qr_image_path && Storage::disk('public')->exists($qrCode->qr_image_path)) {
                Storage::disk('public')->delete($qrCode->qr_image_path);
            }

            // Generate QR code image
            $qrCodeImage = QrCodeGenerator::format('png')
                ->size(400)
                ->errorCorrection('H')
                ->generate($qrCode->code);

            // Create directory if it doesn't exist
            $directory = 'qrcodes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Save the QR code image
            $fileName = 'qrcode_' . $qrCode->id . '_' . time() . '.png';
            $path = $directory . '/' . $fileName;
            Storage::disk('public')->put($path, $qrCodeImage);

            // Update the QR code record with image path
            $qrCode->update([
                'qr_image_path' => $path
            ]);

            return redirect()
                ->route('admin.qrcodes.show', $qrCode)
                ->with('success', 'QR code image generated successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.qrcodes.show', $qrCode)
                ->with('error', 'Failed to generate QR code image: ' . $e->getMessage());
        }
    }

    /**
     * Download the QR code image.
     * Generates the QR code on-the-fly if not already generated.
     *
     * @param  \App\Models\QrCode  $qrCode
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function download(QrCode $qrCode)
    {
        try {
            // Generate QR code image if it doesn't exist
            if (!$qrCode->qr_image_path || !Storage::disk('public')->exists($qrCode->qr_image_path)) {
                // Generate QR code image
                $qrCodeImage = QrCodeGenerator::format('png')
                    ->size(400)
                    ->errorCorrection('H')
                    ->generate($qrCode->code);

                // Create directory if it doesn't exist
                $directory = 'qrcodes';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Save the QR code image
                $fileName = 'qrcode_' . $qrCode->id . '_' . time() . '.png';
                $path = $directory . '/' . $fileName;
                Storage::disk('public')->put($path, $qrCodeImage);

                // Update the QR code record
                $qrCode->update(['qr_image_path' => $path]);
            }

            // Get the full path to the file
            $filePath = Storage::disk('public')->path($qrCode->qr_image_path);

            // Download the file
            $fileName = 'qrcode_' . Str::slug($qrCode->code) . '.png';
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.qrcodes.show', $qrCode)
                ->with('error', 'Failed to download QR code: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique QR code.
     *
     * @return string
     */
    private function generateUniqueCode()
    {
        do {
            // Generate a random string with prefix
            $code = 'QR-' . strtoupper(Str::random(10));
        } while (QrCode::where('code', $code)->exists());

        return $code;
    }
}
