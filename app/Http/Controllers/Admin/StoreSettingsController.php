<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStoreSettingsRequest;
use App\Services\StoreSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreSettingsController extends Controller
{
    public function edit(StoreSettings $storeSettings): View
    {
        return view('admin.settings.edit', ['settings' => $storeSettings->get()]);
    }

    public function update(UpdateStoreSettingsRequest $request, StoreSettings $storeSettings): RedirectResponse
    {
        $storeSettings->update($request->validated());

        return to_route('admin.settings.edit')->with('status', 'Mağaza ayarları güncellendi.');
    }
}
