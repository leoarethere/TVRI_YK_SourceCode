<?php

namespace App\Http\Controllers;

use App\Models\ContactInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardContactInfoController extends Controller
{
    public function index(): View
    {
        $contactInfo = ContactInfo::firstOrCreate([], [
            'admin_phone' => '-',
            'partnership_phone' => '-',
            'hotline_phone' => '-',
            'address' => '-',
            'email' => null,
            'is_active' => false,
        ]);

        return view(
            'backend.contact-info.index',
            compact('contactInfo')
        );
    }


    public function edit(ContactInfo $contactInfo): View
    {
        return view(
            'backend.contact-info.edit',
            compact('contactInfo')
        );
    }


    public function update(
        Request $request,
        ContactInfo $contactInfo
    ): RedirectResponse {

        $request->validate([
            'admin_phone' => 'required|string|max:40',
            'partnership_phone' => 'required|string|max:40',
            'hotline_phone' => 'required|string|max:40',
            'address' => 'required|string',
            'email' => 'nullable|email',
            'google_maps_embed' => 'nullable|string|max:2000',
        ]);


        $googleMapsEmbed = $request->google_maps_embed;


        if ($googleMapsEmbed) {

            if (str_contains($googleMapsEmbed, '<iframe')) {

                preg_match(
                    '/src="([^"]+)"/',
                    $googleMapsEmbed,
                    $matches
                );

                if (isset($matches[1])) {
                    $googleMapsEmbed = $matches[1];
                }
            }


            if (
                ! preg_match(
                    '#^https://www\.google\.com/maps/embed#',
                    $googleMapsEmbed
                )
            ) {

                return back()
                    ->withErrors([
                        'google_maps_embed' =>
                            'Link yang Anda masukkan bukan link Google Maps Embed yang valid. Pastikan URL diawali dengan https://www.google.com/maps/embed',
                    ])
                    ->withInput();
            }
        }


        $contactInfo->update([

            'admin_phone' =>
                $request->admin_phone,

            'partnership_phone' =>
                $request->partnership_phone,

            'hotline_phone' =>
                $request->hotline_phone,

            'address' =>
                $request->address,

            'email' =>
                $request->email,

            'google_maps_embed' =>
                $googleMapsEmbed,

            'is_active' =>
                $request->has('is_active'),
        ]);


        return redirect()
            ->route('dashboard.contact-info.index')
            ->with(
                'success',
                'Informasi kontak berhasil diperbarui!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AKTIF / NONAKTIF INFORMASI KONTAK
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(
        ContactInfo $contactInfo
    ): RedirectResponse {

        $contactInfo->update([
            'is_active' => ! $contactInfo->is_active,
        ]);


        $status = $contactInfo->is_active
            ? 'ditampilkan'
            : 'disembunyikan';


        return redirect()
            ->route('dashboard.contact-info.index')
            ->with(
                'success',
                "Informasi kontak berhasil {$status}."
            );
    }
}