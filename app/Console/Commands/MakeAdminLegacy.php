<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class MakeAdminLegacy extends Command
{
    protected $signature='panel:make-admin {user : Username atau email} {--dry-run : Hanya cek tanpa mengubah}';
    protected $description='Promosikan akun panel lama menjadi Administrator menggunakan kolom users.role yang sudah ada.';
    public function handle(): int
    {
        if(!Schema::hasTable('users')||!Schema::hasColumn('users','role')){$this->error('users.role tidak tersedia. Tidak ada perubahan.');return self::FAILURE;}
        $needle=(string)$this->argument('user');
        $q=DB::table('users')->where('name',$needle);
        if(Schema::hasColumn('users','email')) $q->orWhere('email',$needle);
        $u=$q->first(); if(!$u){$this->error('User tidak ditemukan.');return self::FAILURE;}
        $this->line('User: '.($u->name??$needle).' | role sekarang: '.($u->role??'-'));
        if($this->option('dry-run')){$this->info('Dry-run: tidak ada perubahan.');return self::SUCCESS;}
        DB::table('users')->where('ID',$u->ID)->update(['role'=>'Administrator']);
        $this->info('Role diubah menjadi Administrator.'); return self::SUCCESS;
    }
}
