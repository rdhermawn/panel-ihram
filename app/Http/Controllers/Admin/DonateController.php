<?php



/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpaymuLog;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonateController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function showPaymentwall()
    {
        return view('admin.donate.paymentwall');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function postPaymentwall(Request $request): RedirectResponse
    {
        if ($request->has('status')) {
            Config::write('pw-config.payment.paymentwall.status', true);
        } else {
            Config::write('pw-config.payment.paymentwall.status', false);
        }

        if (config('pw-config.payment.paymentwall.status') === true) {
            $configs = $request->validate([
                'widget_code' => 'string',
                'widget_width' => 'string',
                'widget_high' => 'string',
                'project_key' => 'string',
                'secret_key' => 'string'
            ]);
            foreach ($configs as $config => $value) {
                if (!$value) {
                    Config::write('pw-config.payment.paymentwall.' . $config, '');
                }
                Config::write('pw-config.payment.paymentwall.' . $config, $value);
            }
        }
        return redirect()->back()->with('success', __('admin.configSaved'));
    }

    /**
     * @return Application|Factory|View
     */
    public function showBank()
    {
        return view('admin.donate.banktransfer');
    }

    /**
     * @return Application|Factory|View
     */
    public function showConfirm()
    {
        $logs = BankLog::paginate();
        $user = new User();
        return view('admin.donate.bankconfirm', [
            'logs' => $logs,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified transfer history.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateBankLog(Request $request, int $id): RedirectResponse
    {
        $input = $this->validate($request, [
            'status' => 'required|string',
            'reason' => 'required|string',
        ]);


        $BankLog = BankLog::whereId($id);
        $data = $BankLog->get(['amount', 'gameID'])->firstOrFail();
        $user = User::whereId($data->gameID)->firstOrFail();
        $amount = $data->amount;
        if (config('pw-config.payment.bank_transfer.double')) {
            $amount *= 2;
        }

        $BankLog->update([
            'status' => $input['status'],
            'reason' => $input['reason'],
        ]);

        if ($input['status'] === 'accept') {
            $user->update([
                'money' => $user->money += $amount,
            ]);
        }
        return redirect()->back()->with('success', __('donate.history.success'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function postBank(Request $request): RedirectResponse
    {
        if ($request->has('status')) {
            Config::write('pw-config.payment.bank_transfer.status', true);
        } else {
            Config::write('pw-config.payment.bank_transfer.status', false);
        }

        if ($request->has('double')) {
            Config::write('pw-config.payment.bank_transfer.double', true);
        } else {
            Config::write('pw-config.payment.bank_transfer.double', false);
        }

        if (config('pw-config.payment.bank_transfer.status') === true) {

            $configs = $request->validate([
                'accountOwner' => 'required|string',
                'bankName1' => 'string|nullable',
                'bankName2' => 'string|nullable',
                'bankName3' => 'string|nullable',
                'bankAccountNo1' => 'numeric|nullable',
                'bankAccountNo2' => 'numeric|nullable',
                'bankAccountNo3' => 'numeric|nullable',
                'multiply' => 'required|numeric',
                'limit' => 'required|numeric',
                'CurrencySign' => 'required|string',
                'payment_price' => 'required|numeric'
            ]);

            foreach ($configs as $config => $value) {
                if (!$value) {
                    Config::write('pw-config.payment.bank_transfer.' . $config, '');
                } else {
                    Config::write('pw-config.payment.bank_transfer.' . $config, $value);
                }
            }
        }
        return redirect()->back()->with('success', __('admin.configSaved'));
    }

    public function showPaypal()
    {
        return view('admin.donate.paypal');
    }

    public function postPaypal(Request $request)
    {
        if ($request->has('status')) {
            Config::write('pw-config.payment.paypal.status', true);
        } else {
            Config::write('pw-config.payment.paypal.status', false);
        }

        if ($request->has('sandbox')) {
            Config::write('pw-config.payment.paypal.sandbox', true);
        } else {
            Config::write('pw-config.payment.paypal.sandbox', false);
        }

        if ($request->has('double')) {
            Config::write('pw-config.payment.paypal.double', true);
        } else {
            Config::write('pw-config.payment.paypal.double', false);
        }

        if (config('pw-config.payment.paypal.status') === true) {
            $configs = $request->validate([
                'client_id' => 'required|string',
                'secret' => 'required|string',
                'currency' => 'required|string',
                'currency_per' => 'required|numeric',
                'minimum' => 'required|numeric',
                'maximum' => 'required|numeric',
                'mingetbonus' => 'required|numeric',
                'bonusess' => 'required|numeric'
            ]);

            foreach ($configs as $config => $value) {
                if (!$value) {
                    Config::write('pw-config.payment.paypal.' . $config, '');
                } else {
                    Config::write('pw-config.payment.paypal.' . $config, $value);
                }
            }
        }
        return redirect()->back()->with('success', __('admin.configSaved'));
    }

    public function showIpaymu()
    {
        return view('admin.donate.ipaymu');
    }

    public function postIpaymu(Request $request)
    {
        if ($request->has('status')) {
            Config::write('ipaymu.status', true);
        } else {
            Config::write('ipaymu.status', false);
        }

        if ($request->has('sandbox')) {
            Config::write('ipaymu.sandbox', true);
        } else {
            Config::write('ipaymu.sandbox', false);
        }

        if ($request->has('double')) {
            Config::write('ipaymu.double', true);
        } else {
            Config::write('ipaymu.double', false);
        }

        if (config('ipaymu.status') === true) {
            $configs = $request->validate([
                'va' => 'required|string',
                'key' => 'required|string',
                'currency_per' => 'required|numeric',
                'minimum' => 'required|numeric',
                'maximum' => 'required|numeric',
                'refid' => 'required|string',
                'bonusess' => 'required|numeric|max:100',
                'mingetbonus' => 'required|numeric',
                'route' => 'required|string|min:6|max:30',
                'promo_code' => 'nullable|string|max:255',     
                'promo_bonus_percent' => 'nullable|numeric|min:0|max:100',                          
            ]);
            if ($configs['maximum'] < $configs['minimum']) {
                return redirect()->back()->with('error', __('donate.ipaymu.error.smalmax'));
            } else {
                foreach ($configs as $config => $value) {
                    if (!$value) {
                        Config::write('ipaymu.' . $config, '');
                    } else {
                        Config::write('ipaymu.' . $config, $value);
                    }
                }
            }
        }

        return redirect()->back()->with('success', __('admin.configSaved'));
    }

    public function getHistory(Request $request)
{
    $month = $request->get('month', date('m'));
    $year = $request->get('year', date('Y'));
    $filter = $request->get('filter', 'monthly');

    $ipaymuQuery = IpaymuLog::where('status', 'berhasil')
        ->where('status_code', '1');

    if ($filter === 'monthly') {
        $ipaymuQuery
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
    } elseif ($filter === 'yearly') {
        $ipaymuQuery->whereYear('created_at', $year);
    }

    $ipaymuLogs = (clone $ipaymuQuery)
        ->with('user')
        ->orderByDesc('created_at')
        ->paginate(10, ['*'], 'ipaymu_page');

    $ipaymuTotal = (clone $ipaymuQuery)->sum('money');
    $ipaymuCount = (clone $ipaymuQuery)->count();

    $bankTotal = 0;
    $bankCount = 0;
    $banks = collect(); // Collection kosong untuk banks
        
        // Data untuk chart monthly (12 bulan terakhir) - hanya ipaymu
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
                
            $ipaymuAmount = IpaymuLog::where('status', 'berhasil')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('money');
                
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'bank' => 0, // Set ke 0 karena tidak menggunakan bank_logs
                'ipaymu' => $ipaymuAmount,
                'total' => $ipaymuAmount
            ];
        }
        
        // Get top donors based on filter (only from pwp_ipaymu)
        $topDonors = [];
        if ($filter == 'monthly') {
            // Top donors for specific month
            $topDonors = DB::table('users')
                ->select(
                    'users.name',
                    'users.ID',
                    DB::raw('COALESCE(SUM(pwp_ipaymu.money), 0) as total_donation'),
                    DB::raw('COUNT(pwp_ipaymu.id) as total_transactions')
                )
                ->leftJoin('pwp_ipaymu', function($join) use ($year, $month) {
                    $join->on('users.ID', '=', 'pwp_ipaymu.user_id')
                         ->where('pwp_ipaymu.status', '=', 'berhasil')
                         ->whereYear('pwp_ipaymu.created_at', '=', $year)
                         ->whereMonth('pwp_ipaymu.created_at', '=', $month);
                })
                ->groupBy('users.ID', 'users.name')
                ->having('total_donation', '>', 0)
                ->orderBy('total_donation', 'desc')
                ->limit(10)
                ->get();
        } elseif ($filter == 'yearly') {
            // Top donors for specific year
            $topDonors = DB::table('users')
                ->select(
                    'users.name',
                    'users.ID',
                    DB::raw('COALESCE(SUM(pwp_ipaymu.money), 0) as total_donation'),
                    DB::raw('COUNT(pwp_ipaymu.id) as total_transactions')
                )
                ->leftJoin('pwp_ipaymu', function($join) use ($year) {
                    $join->on('users.ID', '=', 'pwp_ipaymu.user_id')
                         ->where('pwp_ipaymu.status', '=', 'berhasil')
                         ->whereYear('pwp_ipaymu.created_at', '=', $year);
                })
                ->groupBy('users.ID', 'users.name')
                ->having('total_donation', '>', 0)
                ->orderBy('total_donation', 'desc')
                ->limit(10)
                ->get();
        } else {
            // Top donors all time
            $topDonors = DB::table('users')
                ->select(
                    'users.name',
                    'users.ID',
                    DB::raw('COALESCE(SUM(pwp_ipaymu.money), 0) as total_donation'),
                    DB::raw('COUNT(pwp_ipaymu.id) as total_transactions')
                )
                ->leftJoin('pwp_ipaymu', function($join) {
                    $join->on('users.ID', '=', 'pwp_ipaymu.user_id')
                         ->where('pwp_ipaymu.status', '=', 'berhasil');
                })
                ->groupBy('users.ID', 'users.name')
                ->having('total_donation', '>', 0)
                ->orderBy('total_donation', 'desc')
                ->limit(10)
                ->get();
        }
        
        return view('admin.donate.history', [
            'banks' => $banks,
            'ipaymuLogs' => $ipaymuLogs,
            'bankTotal' => $bankTotal,
            'ipaymuTotal' => $ipaymuTotal,
            'bankCount' => $bankCount,
            'ipaymuCount' => $ipaymuCount,
            'monthlyData' => $monthlyData,
            'currentMonth' => $month,
            'currentYear' => $year,
            'currentFilter' => $filter,
            'topDonors' => $topDonors
        ]);
    }
}
