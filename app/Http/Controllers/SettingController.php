<?php

namespace App\Http\Controllers;

use App\Models\LeadStatus;
use App\Models\FraudType;
use App\Models\LossRange;
use App\Models\WalletType;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $statuses = LeadStatus::orderBy('sira')->get();
        $fraudTypes = FraudType::orderBy('sira')->get();
        $lossRanges = LossRange::orderBy('sira')->get();
        $walletTypes = WalletType::orderBy('sira')->get();

        return view('settings.index', compact('statuses', 'fraudTypes', 'lossRanges', 'walletTypes'));
    }

    public function updateStatuses(Request $request)
    {
        $request->validate([
            'statuses' => 'required|array',
            'statuses.*.isim' => 'required|string|max:255',
            'statuses.*.renk' => 'required|string|max:50',
            'statuses.*.sira' => 'required|integer',
        ]);

        foreach ($request->statuses as $id => $data) {
            if (str_starts_with($id, 'new')) {
                LeadStatus::create($data);
            } else {
                LeadStatus::where('id', $id)->update($data);
            }
        }

        if ($request->filled('delete_ids')) {
            LeadStatus::whereIn('id', $request->delete_ids)->delete();
        }

        AuditService::logStatic('Lead durumları güncellendi', 'LeadStatus');
        return redirect()->back()->with('success', 'Durumlar güncellendi.');
    }

    public function updateFraudTypes(Request $request)
    {
        $request->validate([
            'fraud_types' => 'required|array',
            'fraud_types.*.isim' => 'required|string|max:255',
            'fraud_types.*.sira' => 'required|integer',
        ]);

        foreach ($request->fraud_types as $id => $data) {
            if (str_starts_with($id, 'new')) {
                FraudType::create($data);
            } else {
                FraudType::where('id', $id)->update($data);
            }
        }

        if ($request->filled('delete_ids')) {
            FraudType::whereIn('id', $request->delete_ids)->delete();
        }

        AuditService::logStatic('Dolandırıcılık türleri güncellendi', 'FraudType');
        return redirect()->back()->with('success', 'Dolandırıcılık türleri güncellendi.');
    }

    public function updateLossRanges(Request $request)
    {
        $request->validate([
            'loss_ranges' => 'required|array',
            'loss_ranges.*.isim' => 'required|string|max:255',
            'loss_ranges.*.sira' => 'required|integer',
        ]);

        foreach ($request->loss_ranges as $id => $data) {
            if (str_starts_with($id, 'new')) {
                LossRange::create($data);
            } else {
                LossRange::where('id', $id)->update($data);
            }
        }

        if ($request->filled('delete_ids')) {
            LossRange::whereIn('id', $request->delete_ids)->delete();
        }

        AuditService::logStatic('Kayıp aralıkları güncellendi', 'LossRange');
        return redirect()->back()->with('success', 'Kayıp aralıkları güncellendi.');
    }

    public function updateWalletTypes(Request $request)
    {
        $request->validate([
            'wallet_types' => 'required|array',
            'wallet_types.*.isim' => 'required|string|max:255',
            'wallet_types.*.sira' => 'required|integer',
        ]);

        foreach ($request->wallet_types as $id => $data) {
            if (str_starts_with($id, 'new')) {
                WalletType::create($data);
            } else {
                WalletType::where('id', $id)->update($data);
            }
        }

        if ($request->filled('delete_ids')) {
            WalletType::whereIn('id', $request->delete_ids)->delete();
        }

        AuditService::logStatic('Cüzdan türleri güncellendi', 'WalletType');
        return redirect()->back()->with('success', 'Cüzdan türleri güncellendi.');
    }
}
