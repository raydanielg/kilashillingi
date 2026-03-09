<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Transaction;
use App\Mail\WeeklyReportEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends weekly financial summary reports to all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        foreach ($users as $user) {
            $income = Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('amount');

            $expense = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('amount');

            $summary = [
                'income' => $income,
                'expense' => $expense,
            ];

            try {
                Mail::to($user->email)->send(new WeeklyReportEmail($user, $summary));
                $this->info("Report sent to: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send report to {$user->email}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
