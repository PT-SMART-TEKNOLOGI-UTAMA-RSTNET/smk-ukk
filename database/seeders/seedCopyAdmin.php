<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class seedCopyAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultUsers = DB::connection(config('database.erapor'))->table('users')->whereIn('level',['admin','guru','siswa'])->orderBy('level','asc')->get();
        if ($defaultUsers->count() > 0){
            $this->command->getOutput()->progressStart($defaultUsers->count());
            foreach ($defaultUsers as $user){
                $checkUser = User::where('email', $user->email)->limit(1)->get();
                if ($checkUser->count() === 0){
                    $newUser = new User();
                    $newUser->id = Uuid::uuid4()->toString();
                    $newUser->user_type = $user->level;
                } else {
                    $newUser = $checkUser->first();
                }
                if ($user->level == 'guru') {
                    $newUser->penguji_type = 'internal';
                }
                $newUser->siswa = null;
                $newUser->nis = null;
                $newUser->nisn = null;
                $newUser->rombel = null;
                $newUser->jurusan = null;
                $newUser->tingkat = null;
                if ($user->level == 'siswa'){
                    $dataSiswa = DB::connection(config('database.erapor'))->table('siswas')->where('user', $user->id)->limit(1)->get();
                    if ($dataSiswa->count() > 0){
                        $dataSiswa = $dataSiswa->first();
                        $dataTapel = DB::connection(config('database.erapor'))->table('tapels')->where('active',true)->get();
                        if ($dataTapel->count() > 0) {
                            $dataTapel = $dataTapel->first();
                            $dataRombelMember = DB::connection(config('database.erapor'))->table('rombel_members')->where('tapel', $dataTapel->id)->where('siswa',$dataSiswa->id)->get();
                            if ($dataRombelMember->count() > 0) {
                                $dataRombelMember = $dataRombelMember->first();
                                $dataRombel = DB::connection(config('database.erapor'))->table('rombels')->where('id', $dataRombelMember->rombel)->get();
                                if ($dataRombel->count() > 0) {
                                    $dataRombel = $dataRombel->first();
                                    $newUser->tingkat = $dataRombel->tingkat;
                                    $newUser->jurusan = $dataRombel->jurusan;
                                    $newUser->rombel = $dataRombel->name;
                                    $newUser->siswa = $dataSiswa->id;
                                    $newUser->nis = $dataSiswa->nis;
                                    $newUser->nisn = $dataSiswa->nisn;
                                }
                            }
                        }
                    }
                }
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->password = $user->password;
                $newUser->saveOrFail();
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
        }
    }
}
